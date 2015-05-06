<?php
/**
 * @package    PatchTester
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

JHtml::stylesheet("com_patchtester/octicons.css", false, true, false);
foreach ($this->items as $i => $item) :
	$status = '';

	if ($item->applied) :
		$status = ' success';
	endif;

?>
<tr class="row<?php echo $i % 2; ?><?php echo $status ?>">
	<td class="center">
		<?php echo $item->pull_id; ?>
	</td>
	<td>
		<p class="hasTooltip" title="<?php echo $this->escape(\JHtml::_('string.truncateComplex', $item->description, 100)); ?>">
			<?php echo $this->escape($item->title); ?>
		</p>
	</td>
	<td>
		<a class="btn btn-small btn-info" href="<?php echo $item->pull_url; ?>" target="_blank">
			<span class="octicon octicon-mark-github"></span> <?php echo \JText::_('COM_PATCHTESTER_GITHUB'); ?>
		</a>
	</td>
	<td>
		<a class="btn btn-small btn-warning" href="http://issues.joomla.org/tracker/joomla-cms/<?php echo $item->pull_id; ?>" target="_blank">
			<i class="icon-joomla"></i> <?php echo \JText::_('COM_PATCHTESTER_JISSUE'); ?>
		</a>
	</td>
	<td class="center">
		<?php if ($item->applied) : ?>
			<span class="label label-success">
			<?php echo JText::_('COM_PATCHTESTER_APPLIED'); ?>
			</span>
		<?php else : ?>
			<span class="label">
			<?php echo JText::_('COM_PATCHTESTER_NOT_APPLIED'); ?>
			</span>
		<?php endif; ?>
	</td>
	<td class="center">
		<?php if ($item->applied) :
			echo '<a class="btn btn-small btn-success" href="javascript:submitpatch(\'pull.revert\', ' . (int) $item->id . ');">' . JText::_('COM_PATCHTESTER_REVERT_PATCH') . '</a>';
		else :
			echo '<a class="btn btn-small btn-primary" href="javascript:submitpatch(\'pull.apply\', ' . (int) $item->id . ');">' . JText::_('COM_PATCHTESTER_APPLY_PATCH') . '</a>';
		endif; ?>
	</td>
</tr>
<?php endforeach;
