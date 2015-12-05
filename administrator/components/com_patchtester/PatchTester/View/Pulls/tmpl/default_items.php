<?php
/**
 * Patch testing component for the Joomla! CMS
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

/** @var \PatchTester\View\DefaultHtmlView $this */

foreach ($this->items as $i => $item) :
	$status = '';

	if ($item->applied) :
		$status = ' class="success"';
	endif;
?>
<tr<?php echo $status; ?>>
	<td class="center">
		<?php echo $item->pull_id; ?>
	</td>
	<td>
		<span class="hasTooltip" title="<strong>Info</strong><br/><?php echo $this->escape($item->description); ?>"><?php echo $this->escape($item->title); ?></span>
	</td>
	<td>
		<?php echo substr($item->sha, 0, 10); ?>
	</td>
	<td class="center">
		<a class="btn btn-small btn-info" href="<?php echo $item->pull_url; ?>" target="_blank">
			<span class="octicon octicon-mark-github"></span> <?php echo \JText::_('COM_PATCHTESTER_GITHUB'); ?>
		</a>
	</td>
	<td class="center">
		<a class="btn btn-small btn-warning" href="https://issues.joomla.org/tracker/joomla-cms/<?php echo $item->pull_id; ?>" target="_blank">
			<i class="icon-joomla"></i> <?php echo \JText::_('COM_PATCHTESTER_JISSUE'); ?>
		</a>
	</td>
	<td class="center">
		<?php if ($item->applied) : ?>
			<span class="label label-success">
			<?php echo \JText::_('COM_PATCHTESTER_APPLIED'); ?>
			</span>
		<?php else : ?>
			<span class="label">
			<?php echo \JText::_('COM_PATCHTESTER_NOT_APPLIED'); ?>
			</span>
		<?php endif; ?>
	</td>
	<td class="center">
		<?php if ($item->applied) :
			echo '<a class="btn btn-small btn-success" href="javascript:PatchTester.submitpatch(\'revert\', ' . (int) $item->applied . ');">' . \JText::_('COM_PATCHTESTER_REVERT_PATCH') . '</a>';
		else :
			echo '<a class="btn btn-small btn-primary" href="javascript:PatchTester.submitpatch(\'apply\', ' . (int) $item->pull_id . ');">' . \JText::_('COM_PATCHTESTER_APPLY_PATCH') . '</a>';
		endif; ?>
	</td>
</tr>
<?php endforeach;
