<?php

// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Das Tooltip Behavior wird geladen
JHtml::_('behavior.tooltip');

// Das Formvalidation Behavior wird geladen
JHtml::_('behavior.formvalidation');

// Der Link für das Formular
$actionLink = JRoute::_('index.php?option=com_hallowelt&layout=edit&id=' . (int) $this->item->id);

?>
<form action="<?php echo $actionLink; ?>" method="post"
      name="adminForm" id="adminForm"
      class="form-validate">

	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_HALLOWELT_HALLOWELT_DETAILS'); ?>
			</legend>

			<?php foreach ($this->form->getFieldset() as $field): ?>
				<?php if (!$field->hidden): ?>
					<?php echo $field->label; ?>
				<?php endif; ?>

				<?php echo $field->input; ?>
			<?php endforeach; ?>

		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start', 'hallowelt-slider'); ?>

		<?php foreach ($params as $name => $fieldset): ?>
			<?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name . '-params'); ?>
			<?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
				<p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
			<?php endif; ?>

			<fieldset class="panelform">
				<ul class="adminformlist">
					<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
		<?php endforeach; ?>

		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<div>
		<input type="hidden" name="task" value="hallowelt.edit"/>

		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
