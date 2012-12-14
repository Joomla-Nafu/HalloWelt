<?php

// Den direkten Aufruf verbieten
defined('_JEXEC') or die;
?>

<?php foreach ($this->items as $i => $item): ?>
	<tr>
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<?php echo $item->hallo; ?>
		</td>
	</tr>
<?php endforeach;
