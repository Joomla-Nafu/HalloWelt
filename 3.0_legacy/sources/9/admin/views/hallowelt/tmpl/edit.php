<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Das Tooltip Behavior wird geladen
JHtml::_('bootstrap.tooltip', '.hasTip');

// Der Link für das Formular
$actionLink = JRoute::_('index.php?option=com_hallowelt&layout=edit&id=' . (int) $this->item->id);

?>
<form action="<?php echo $actionLink; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_HALLOWELT_HALLOWELT_DETAILS'); ?></legend>

		<?php foreach ($this->form->getFieldset() as $field): ?>
			<?php echo $field->label;
			echo $field->input; ?>
		<?php endforeach; ?>
	</fieldset>
	<div>
		<input type="hidden" name="task" value="hallowelt.edit"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
