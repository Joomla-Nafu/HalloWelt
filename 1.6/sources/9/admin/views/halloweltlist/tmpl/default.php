<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Das Tooltip Behavior wird geladen
JHtml::_('behavior.tooltip');

$actionLink =  JRoute::_('index.php?option=com_hallowelt');

?>
<form action="<?php echo $actionLink; ?>" method="post" name="adminForm">
    <table class="adminlist">
        <thead><?php echo $this->loadTemplate('head');?></thead>
        <tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
        <tbody><?php echo $this->loadTemplate('body');?></tbody>
    </table>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
