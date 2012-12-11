<?php

// Den direkten Aufruf verbieten.
defined('_JEXEC') or die;
?>

<tr>
	<th width="5">
		<?php echo JText::_('COM_HALLOWELT_HALLOWELT_HEADING_ID'); ?>
	</th>
	<th width="20">
		<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
		       onclick="Joomla.checkAll(this)"/>
	</th>
	<th>
		<?php echo JText::_('COM_HALLOWELT_HALLOWELT_HEADING_GREETING'); ?>
	</th>
</tr>
