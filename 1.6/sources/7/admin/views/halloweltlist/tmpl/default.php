<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Das Tooltip Behavior wird geladen
JHtml::_('behavior.tooltip');
?>

<form action="<?php echo JRoute::_('index.php?option=com_hallowelt'); ?>" method="post" name="adminForm">
    <table class="adminlist">
        <thead><?php echo $this->loadTemplate('head');?></thead>
        <tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
        <tbody><?php echo $this->loadTemplate('body');?></tbody>
    </table>
</form>
