<?php
/**
 * @package    PatchTester
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

?>
<script type="text/javascript">
	var submitpatch = function (task, id) {
		document.id('pull_id').set('value', id);
		return Joomla.submitbutton(task);
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_patchtester&view=pulls'); ?>" method="post" name="adminForm" id="adminForm">

	<div>
		<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
		<button type="button" class="btn"
			onclick="document.id('filter_search').value='';document.id('filter_searchid').value='';this.form.submit();">
			<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
		</button>
	 </div>

	<table class="table table-striped table-hover table-condensed">
		<thead>
		<tr>
			<th width="8%">
				<?php echo JHtml::_('grid.sort', 'COM_PATCHTESTER_PULL_ID', 'number', $listDirn, $listOrder); ?>
				<br />
				<input type="text" name="filter_searchid" id="filter_searchid" class="span10" value="<?php echo $this->escape($this->state->get('filter.searchid')); ?>" />
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
				<br />
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
			</th>
			<th>I</th>
			<th class="title">
				<?php echo JText::_('COM_PATCHTESTER_JOOMLACODE_ISSUE'); ?>
			</th>
			<th width="20%">
				<?php echo JText::_('JSTATUS'); ?>
			</th>
			<th width="20%">
				<?php echo JText::_('COM_PATCHTESTER_TEST_THIS_PATCH'); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php echo $this->loadTemplate('items'); ?>
		</tbody>
	</table>
	<div class="well">

	<?php echo $this->pagination->getListFooter(); ?>
	</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="pull_id" id="pull_id" value=""/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
