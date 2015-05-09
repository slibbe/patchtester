<?php
/**
 * Patch testing component for the Joomla! CMS
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

namespace PatchTester\Controller;

use PatchTester\Helper;
use PatchTester\Model\PullsModel;

/**
 * Controller class to start fetching remote data
 *
 * @since  2.0
 */
class StartfetchController extends DisplayController
{
	/**
	 * Execute the controller.
	 *
	 * @return  void  Redirects the application
	 *
	 * @since   2.0
	 */
	public function execute()
	{
		// We don't want this request to be cached.
		header('Pragma: no-cache');
		header('Cache-Control: no-cache');
		header('Expires: -1');

		// Check for a valid token. If invalid, send a 403 with the error message.
		if (!\JSession::checkToken('request'))
		{
			$response = new \JResponseJson(new \Exception(\JText::_('JINVALID_TOKEN'), 403));

			echo json_encode($response);

			$this->getApplication()->close(1);
		}

		// Make sure we can fetch the data from GitHub - throw an error on < 10 available requests
		$github = Helper::initializeGithub();

		try
		{
			$rate = $github->authorization->getRateLimit();
		}
		catch (\Exception $e)
		{
			$response = new \JResponseJson(
				new \Exception(
					\JText::sprintf('COM_PATCHTESTER_COULD_NOT_CONNECT_TO_GITHUB', $e->getMessage())
				)
			);

			echo json_encode($response);

			$this->getApplication()->close(1);
		}

		// If over the API limit, we can't build this list
		if ($rate->resources->core->remaining < 10)
		{
			$response = new \JResponseJson(
				new \Exception(
					\JText::sprintf('COM_PATCHTESTER_API_LIMIT_LIST', \JFactory::getDate($rate->resources->core->reset)),
					429
				)
			);

			echo json_encode($response);

			$this->getApplication()->close(1);
		}

		// TODO - Decouple the model and context?
		$model = new PullsModel('com_patchtester.fetch', null, \JFactory::getDbo());

		// Initialize the state for the model
		$model->setState($this->initializeState($model));

		try
		{
			// Sanity check, ensure there aren't any applied patches
			if (count($model->getAppliedPatches()) >= 1)
			{
				$response = new \JResponseJson(new \Exception(\JText::_('COM_PATCHTESTER_ERROR_APPLIED_PATCHES'), 500));

				echo json_encode($response);

				$this->getApplication()->close(1);
			}
		}
		catch (\Exception $e)
		{
			$response = new \JResponseJson($e);

			echo json_encode($response);

			$this->getApplication()->close(1);
		}

		// We're able to successfully pull data, prepare our environment
		\JFactory::getSession()->set('com_patchtester_fetcher_page', 1);

		$response = new \JResponseJson(
			array('complete' => false, 'header' => \JText::_('COM_PATCHTESTER_FETCH_PROCESSING', true)),
			\JText::sprintf('COM_PATCHTESTER_FETCH_PAGE_NUMBER', 1),
			false,
			true
		);

		echo json_encode($response);

		$this->getApplication()->close();
	}
}
