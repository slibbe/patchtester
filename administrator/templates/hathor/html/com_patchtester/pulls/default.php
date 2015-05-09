<?php
/**
 * Patch testing component for the Joomla! CMS
 *
 * @copyright  Copyright (C) 2011 - 2012 Ian MacLennan, Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

/** @type  \PatchTester\View\Pulls\PullsHtmlView  $this */

\JHtml::_('behavior.core');
\JHtml::_('bootstrap.tooltip');

\JHtml::_('stylesheet', 'com_patchtester/octicons.css', array(), true);

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$filterApplied = $this->escape($this->state->get('filter.applied'));
$sortFields    = $this->getSortFields();

\JFactory::getDocument()->addScriptDeclaration(
	"
	var submitpatch = function (task, id) {
		jQuery('#pull_id').val(id);
		return Joomla.submitbutton(task);
	}
	"
);

JFactory::getDocument()->addStyleDeclaration(
	'
	.icon-48-patchtester { background-image:url("/media/com_patchtester/images/icon-48-patchtester.png"); }
	'
);
echo JHtmlBootstrap::renderModal(
	'modal-refresh', array(
		'url' => JUri::root() . 'administrator/index.php?option=com_patchtester&view=fetch&tmpl=component',
		'title' => JText::_('COM_PATCHTESTER_TOOLBAR_FETCH_DATA'),
		'width' => '800px',
		'height' => '300px'
	)
);
?>
<form action="<?php echo JRoute::_('index.php?option=com_patchtester&view=pulls'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<fieldset id="filter-bar">
			<legend class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></legend>
			<div class="filter-search">
				<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_PATCHTESTER_FILTER_SEARCH_DESCRIPTION'); ?>" />
				<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="document.getElementById('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="filter-select">
				<label class="selectlabel" for="filter_applied"><?php echo JText::_('COM_PATCHTESTER_FILTER_APPLIED_PATCHES'); ?></label>
				<select name="filter_applied" id="filter_applied">
					<option value=""><?php echo \JText::_('COM_PATCHTESTER_FILTER_APPLIED_PATCHES'); ?></option>
					<option value="yes"<?php if ($filterApplied == 'yes') echo ' selected="selected"'; ?>><?php echo \JText::_('COM_PATCHTESTER_APPLIED'); ?></option>
					<option value="no"<?php if ($filterApplied == 'no') echo ' selected="selected"'; ?>><?php echo \JText::_('COM_PATCHTESTER_NOT_APPLIED'); ?></option>
				</select>
				<button type="submit" id="filter-go"><?php echo JText::_('JSUBMIT'); ?></button>
			</div>
		</fieldset>
		<div class="clr"> </div>

		<table class="adminlist">
			<thead>
			<tr>
				<th width="5%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'COM_PATCHTESTER_PULL_ID', 'a.pull_id', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="8%" class="nowrap center">
					<?php echo \JText::_('COM_PATCHTESTER_GITHUB'); ?>
				</th>
				<th width="8%" class="nowrap center">
					<?php echo \JText::_('COM_PATCHTESTER_JISSUES'); ?>
				</th>
				<th width="10%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'applied', $listDirn, $listOrder); ?>
				</th>
				<th width="15%" class="nowrap center">
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
			<?php echo $this->loadTemplate('items'); ?>
			</tbody>
		</table>
		<?php echo $this->pagination->getListFooter(); ?>

		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="pull_id" id="pull_id" value="" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
