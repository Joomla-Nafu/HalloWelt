<?php

// Den direkten Aufruf verbieten.
defined('_JEXEC') or die;

// Das Tooltip Behavior wird geladen
JHtml::_('bootstrap.tooltip');

?>

<form action="<?php echo JRoute::_('index.php?option=com_hallowelt'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="table table-bordered table-striped table-hover">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>
</form>
