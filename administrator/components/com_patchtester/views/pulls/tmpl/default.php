<?php
/**
 * @package        PatchTester
 * @copyright      Copyright (C) 2011 Ian MacLennan, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

?>
<script type="text/javascript">
	var submitpatch = function (task, id) {
		document.id('pull_id').set('value', id);
		return Joomla.submitbutton(task);
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_patchtester&view=pulls'); ?>" method="post" name="adminForm"
      id="adminForm">
	<table class="adminlist">
		<thead>
		<tr>
			<th colspan="7" style="text-align: left;"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></th>
		</tr>
		<tr>
			<td>
				<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button"
				        onclick="document.id('filter_search').value='';document.id('filter_searchid').value='';this.form.submit();">
					<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</td>
			<td>
				<label class="filter-search-lbl"
				       for="filter_searchid"><?php echo JText::_('COM_PATCHTESTER_SEARCH_IN_PULL_ID'); ?></label>
				<input type="text" name="filter_searchid" id="filter_searchid" size="5"
				       value="<?php echo $this->escape($this->state->get('filter.searchid')); ?>"
				       title="<?php echo JText::_('COM_PATCHTESTER_SEARCH_IN_PULL_ID'); ?>"/>
			</td>
			<td>
				<label class="filter-search-lbl"
				       for="filter_search"><?php echo JText::_('COM_PATCHTESTER_SEARCH_IN_TITLE'); ?></label>
				<br/>
				<input type="text" name="filter_search" id="filter_search"
				       value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				       title="<?php echo JText::_('COM_PATCHTESTER_SEARCH_IN_TITLE'); ?>"/>
			</td>
			<td colspan="4">
			</td>
		</tr>
		<tr>
			<th colspan="7" style="text-align: left;"><?php echo JText::_('COM_PATCHTESTER_SORT'); ?></th>
		</tr>
		<tr>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value=""
				       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
			</th>
			<th width="5%">
				<?php echo JHtml::_('grid.sort', 'COM_PATCHTESTER_PULL_ID', 'number', $listDirn, $listOrder); ?>
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
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
			<td colspan="10">
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php echo $this->loadTemplate('items'); ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="pull_id" id="pull_id" value=""/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
