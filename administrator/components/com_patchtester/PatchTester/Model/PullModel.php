<?php
/**
 * Patch testing component for the Joomla! CMS
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace PatchTester\Model;

use Joomla\Registry\Registry;

use PatchTester\Helper;

/**
 * Methods supporting pull requests.
 *
 * @since  2.0
 */
class PullModel extends \JModelBase
{
	/**
	 * Array containing top level non-production folders
	 *
	 * @var    array
	 * @since  2.0
	 */
	protected $nonProductionFolders = array('build', 'docs', 'installation', 'tests');

	/**
	 * Method to parse a patch and extract the affected files
	 *
	 * @param   string  $patch  Patch file to parse
	 *
	 * @return  array  Array of files within a patch
	 *
	 * @since   2.0
	 */
	protected function parsePatch($patch)
	{
		$state = 0;
		$files = array();

		$lines = explode("\n", $patch);

		foreach ($lines AS $line)
		{
			switch ($state)
			{
				case 0:
					if (strpos($line, 'diff --git') === 0)
					{
						$state = 1;
					}

					$file         = new \stdClass;
					$file->action = 'modified';

					break;

				case 1:
					if (strpos($line, 'index') === 0)
					{
						$file->index = substr($line, 6);
					}

					if (strpos($line, '---') === 0)
					{
						$file->old = substr($line, 6);
					}

					if (strpos($line, '+++') === 0)
					{
						$file->new = substr($line, 6);
					}

					if (strpos($line, 'new file mode') === 0)
					{
						$file->action = 'added';
					}

					if (strpos($line, 'deleted file mode') === 0)
					{
						$file->action = 'deleted';
					}

					// Binary files are presently unsupported, use this to reset the parser in the meantime
					if (strpos($line, 'Binary files') === 0)
					{
						$state = 0;
					}

					if (strpos($line, '@@') === 0)
					{
						$state = 0;

						/*
						 * Check if the patch tester is running in a production environment
						 * If so, do not patch certain files as errors will be thrown
						 */
						if (!file_exists(JPATH_ROOT . '/installation/index.php'))
						{
							$filePath = explode('/', $file->new);

							if (in_array($filePath[0], $this->nonProductionFolders))
							{
								continue;
							}
						}

						$files[] = $file;
					}

					break;
			}
		}

		return $files;
	}

	/**
	 * Patches the code with the supplied pull request
	 *
	 * @param   integer  $id  ID of the pull request to apply
	 *
	 * @return  boolean
	 *
	 * @since   2.0
	 * @throws  \RuntimeException
	 */
	public function apply($id)
	{
		// Get the Github object
		$github = Helper::initializeGithub();
		$rate   = $github->authorization->getRateLimit();

		// If over the API limit, we can't build this list
		if ($rate->resources->core->remaining == 0)
		{
			throw new \RuntimeException(
				\JText::sprintf('COM_PATCHTESTER_API_LIMIT_LIST', \JFactory::getDate($rate->resources->core->reset))
			);
		}

		$pull = $github->pulls->get($this->getState()->get('github_user'), $this->getState()->get('github_repo'), $id);

		if (is_null($pull->head->repo))
		{
			throw new \RuntimeException(\JText::_('COM_PATCHTESTER_REPO_IS_GONE'));
		}

		// Set up the JHttp object
		$options = new Registry;
		$options->set('userAgent', 'JPatchTester/2.0');
		$options->set('timeout', 120);

		// Make sure we can use the cURL driver
		$driver = \JHttpFactory::getAvailableDriver($options, 'curl');

		if (!($driver instanceof \JHttpTransportCurl))
		{
			throw new \RuntimeException('Cannot use the PHP cURL adapter in this environment, cannot use patchtester', 500);
		}

		$transport = new \JHttp($options, $driver);

		$patch = $transport->get($pull->diff_url)->body;

		$files = $this->parsePatch($patch);

		if (!$files)
		{
			return false;
		}

		foreach ($files as $file)
		{
			if ($file->action == 'deleted' && !file_exists(JPATH_ROOT . '/' . $file->old))
			{
				throw new \RuntimeException(sprintf(\JText::_('COM_PATCHTESTER_FILE_DELETED_DOES_NOT_EXIST_S'), $file->old));
			}

			if ($file->action == 'added' || $file->action == 'modified')
			{
				// If the backup file already exists, we can't apply the patch
				if (file_exists(JPATH_COMPONENT . '/backups/' . md5($file->new) . '.txt'))
				{
					throw new \RuntimeException(sprintf(\JText::_('COM_PATCHTESTER_CONFLICT_S'), $file->new));
				}

				if ($file->action == 'modified' && !file_exists(JPATH_ROOT . '/' . $file->old))
				{
					throw new \RuntimeException(sprintf(\JText::_('COM_PATCHTESTER_FILE_MODIFIED_DOES_NOT_EXIST_S'), $file->old));
				}

				$url = 'https://raw.github.com/' . urlencode($pull->head->user->login) . '/' . urlencode($pull->head->repo->name)
					. '/' . urlencode($pull->head->ref) . '/' . $file->new;

				$file->body = $transport->get($url)->body;
			}
		}

		jimport('joomla.filesystem.file');

		// At this point, we have ensured that we have all the new files and there are no conflicts
		foreach ($files as $file)
		{
			// We only create a backup if the file already exists
			if ($file->action == 'deleted' || (file_exists(JPATH_ROOT . '/' . $file->new) && $file->action == 'modified'))
			{
				if (!\JFile::copy(\JPath::clean(JPATH_ROOT . '/' . $file->old), JPATH_COMPONENT . '/backups/' . md5($file->old) . '.txt'))
				{
					throw new \RuntimeException(
						sprintf('Can not copy file %s to %s', JPATH_ROOT . '/' . $file->old, JPATH_COMPONENT . '/backups/' . md5($file->old) . '.txt')
					);
				}
			}

			switch ($file->action)
			{
				case 'modified':
				case 'added':
					if (!\JFile::write(\JPath::clean(JPATH_ROOT . '/' . $file->new), $file->body))
					{
						throw new \RuntimeException(sprintf('Can not write the file: %s', JPATH_ROOT . '/' . $file->new));
					}

					break;

				case 'deleted':
					if (!\JFile::delete(\JPath::clean(JPATH_ROOT . '/' . $file->old)))
					{
						throw new \RuntimeException(sprintf('Can not delete the file: %s', JPATH_ROOT . '/' . $file->old));
					}

					break;
			}
		}

		$table                  = \JTable::getInstance('TestsTable', '\\PatchTester\\Table\\');
		$table->pull_id         = $pull->number;
		$table->data            = json_encode($files);
		$table->patched_by      = \JFactory::getUser()->id;
		$table->applied         = 1;
		$table->applied_version = JVERSION;

		if (!$table->store())
		{
			throw new \RuntimeException($table->getError());
		}

		return true;
	}

	/**
	 * Reverts the specified pull request
	 *
	 * @param   integer  $id  ID of the pull request to Reverts
	 *
	 * @return  boolean
	 *
	 * @since   2.0
	 * @throws  \RuntimeException
	 */
	public function revert($id)
	{
		$table = \JTable::getInstance('TestsTable', '\\PatchTester\\Table\\');
		$table->load($id);

		// We don't want to restore files from an older version
		if ($table->applied_version != JVERSION)
		{
			$table->delete();

			return $this;
		}

		$files = json_decode($table->data);

		if (!$files)
		{
			throw new \RuntimeException(sprintf(\JText::_('%s - Error retrieving table data (%s)'), __METHOD__, htmlentities($table->data)));
		}

		jimport('joomla.filesystem.file');

		foreach ($files as $file)
		{
			switch ($file->action)
			{
				case 'deleted':
				case 'modified':
					if (!\JFile::copy(JPATH_COMPONENT . '/backups/' . md5($file->old) . '.txt', JPATH_ROOT . '/' . $file->old))
					{
						throw new \RuntimeException(
							sprintf(
								\JText::_('Can not copy file %s to %s'),
								JPATH_COMPONENT . '/backups/' . md5($file->old) . '.txt',
								JPATH_ROOT . '/' . $file->old
							)
						);
					}

					if (!\JFile::delete(JPATH_COMPONENT . '/backups/' . md5($file->old) . '.txt'))
					{
						throw new \RuntimeException(sprintf(\JText::_('Can not delete the file: %s'), JPATH_COMPONENT . '/backups/' . md5($file->old) . '.txt'));
					}

					break;

				case 'added':
					if (!\JFile::delete(\JPath::clean(JPATH_ROOT . '/' . $file->new)))
					{
						throw new \RuntimeException(sprintf(\JText::_('Can not delete the file: %s'), JPATH_ROOT . '/' . $file->new));
					}

					break;
			}
		}

		$table->delete();

		return true;
	}
}
