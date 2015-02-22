<?php
/**
 * Patch testing component for the Joomla! CMS
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

/** @type  \PatchTester\View\Pulls\PullsHtmlView  $this */

\JHtml::_('behavior.tooltip');
\JHtml::_('behavior.modal');
\JHtml::_('formbehavior.chosen', 'select');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$sortFields = $this->getSortFields();

if (count($this->envErrors)) :
	$this->loadTemplate('errors');
else :
?>
<script type="text/javascript">
	var submitpatch = function (task, id) {
		document.id('pull_id').set('value', id);
		return Joomla.submitbutton(task);
	}

	Joomla.orderTable = function() {
		table = document.getElementById('sortTable');
		direction = document.getElementById('directionTable');
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}

		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form action="<?php echo \JRoute::_('index.php?option=com_patchtester&view=pulls'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible"><?php echo \JText::_('JGLOBAL_SORT_BY'); ?></label>
			<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo \JText::_('JGLOBAL_SORT_BY'); ?></option>
				<?php echo \JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
			</select>
		</div>
	</div>
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo \JText::_('COM_PATCHTESTER_FILTER_SEARCH_DESCRIPTION'); ?></label>
				<input type="text" name="filter_search" placeholder="<?php echo \JText::_('COM_PATCHTESTER_FILTER_SEARCH_DESCRIPTION'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo \JText::_('COM_PATCHTESTER_FILTER_SEARCH_DESCRIPTION'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn tip hasTooltip" type="submit" title="<?php echo \JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo \JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo \JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo \JText::_('JFIELD_ORDERING_DESC'); ?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo \JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo \JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo \JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
				</select>
			</div>
		</div>
		<div class="clearfix"> </div>

		<table class="table table-striped">
			<thead>
			<tr>
				<th width="5%" class="nowrap center">
					<?php echo \JText::_('COM_PATCHTESTER_PULL_ID'); ?>
				</th>
				<th class="nowrap center">
					<?php echo \JText::_('JGLOBAL_TITLE'); ?>
				</th>
				<th class="nowrap center">I</th>
				<th width="20%" class="nowrap center">
					<?php echo \JText::_('JSTATUS'); ?>
				</th>
				<th width="20%" class="nowrap center">
					<?php echo \JText::_('COM_PATCHTESTER_TEST_THIS_PATCH'); ?>
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
			<?php if (count($this->items)) :
				echo $this->loadTemplate('items');
			else : ?>
				<td align="center" colspan="6"><?php echo \JText::_('COM_PATCHTESTER_NO_ITEMS'); ?></td>
			<?php endif; ?>
			</tbody>
		</table>
		<?php echo $this->pagination->getListFooter(); ?>

		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="pull_id" id="pull_id" value=""/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo \JHtml::_('form.token'); ?>
	</div>
</form>
<?php endif;
