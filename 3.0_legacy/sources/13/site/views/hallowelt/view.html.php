<?php

// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! Viewbibliothek importieren
jimport('joomla.application.component.view');

/**
 * HTML View class for the HalloWelt Component
 */
class HalloWeltViewHalloWelt extends JViewLegacy
{
	// Die JView display Methode wird überschrieben
	function display($tpl = null)
	{
		// Die Daten werden vom Model bezogen
		$this->item = $this->get('Item');

		// Der View wird angezeigt
		parent::display($tpl);
	}
}
