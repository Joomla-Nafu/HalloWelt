<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;
?>

<h1>
<?php
echo $this->item->greeting;

if($this->item->category && $this->item->params->get('show_category')) :
   echo ' ('.$this->item->category.')';
endif;
?>
</h1>
