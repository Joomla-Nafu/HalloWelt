<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;
?>

<tr>
    <th width="5">
        <?php echo JText::_('COM_HALLOWELT_HALLOWELT_HEADING_ID'); ?>
    </th>
    <th width="20">
        <input type="checkbox" name="toggle" value=""
        onclick="checkAll(<?php echo count($this->items); ?>);" />
    </th>
    <th>
        <?php echo JText::_('COM_HALLOWELT_HALLOWELT_HEADING_GREETING'); ?>
    </th>
</tr>
