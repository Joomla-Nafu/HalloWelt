<?php

// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

/**
 * HTML View Klasse für die HalloWelt Komponente.
 */
class HalloWeltViewHalloWelt extends JViewLegacy
{
	/**
	 * @var string
	 */
	protected $message = '';

	// Die JViewLegacy::display() Methode wird überschrieben
	function display($tpl = null)
	{
		// Die Daten werden dem View zugewiesen
		$this->msg = 'Hallo Welt!';

		// Der View wird angezeigt
		parent::display($tpl);
	}
}
