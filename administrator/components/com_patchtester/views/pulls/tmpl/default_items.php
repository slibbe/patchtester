<?php
/**
 * @package    PatchTester
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/** @type  PatchtesterViewPulls  $this */

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
		<a class="icon icon16-github hasTip" title="<?php echo JText::_('COM_PATCHTESTER_OPEN_IN_GITHUB'); ?>" href="<?php echo $item->pull_url; ?>" target="_blank">
			<?php echo $this->escape($item->title); ?>
		</a>
	</td>
	<td>
		<?php if ($item->description) :
			echo JHtml::_('tooltip', htmlspecialchars($item->description), 'Info');
		else :
			echo '&nbsp;';
		endif;
		?>
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
			echo '<a class="btn btn-small btn-success" href="javascript:submitpatch(\'pull.revert\', ' . (int) $item->applied . ');">' . JText::_('COM_PATCHTESTER_REVERT_PATCH') . '</a>';
		else :
			echo '<a class="btn btn-small btn-primary" href="javascript:submitpatch(\'pull.apply\', ' . (int) $item->pull_id . ');">' . JText::_('COM_PATCHTESTER_APPLY_PATCH') . '</a>';
		endif; ?>
	</td>
</tr>
<?php endforeach;
