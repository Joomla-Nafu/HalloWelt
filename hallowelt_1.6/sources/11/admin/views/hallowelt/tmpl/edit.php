<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Das Tooltip Behavior wird geladen
JHtml::_('behavior.tooltip');

// Das Formvalidation Behavior wird geladen
JHtml::_('behavior.formvalidation');

// Der Link für das Formular
$actionLink = JRoute::_('index.php?option=com_hallowelt&layout=edit&id='.(int) $this->item->id);

?>
<form action="<?php echo $actionLink; ?>" method="post"
	name="adminForm" id="helloworld-form"
	class="form-validate">

	<fieldset class="adminform">
        <legend><?php echo JText::_('COM_HALLOWELT_HALLOWELT_DETAILS'); ?></legend>

        <?php foreach($this->form->getFieldset() as $field): ?>
            <?php if (!$field->hidden): ?>
                <?php echo $field->label; ?>
            <?php endif; ?>

            <?php echo $field->input; ?>
        <?php endforeach; ?>

    </fieldset>

    <div>
        <input type="hidden" name="task" value="hallowelt.edit" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
