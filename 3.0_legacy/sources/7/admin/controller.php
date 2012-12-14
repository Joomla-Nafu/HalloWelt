<?php

// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

/**
 * Allgemeiner Controller der HalloWelt Komponente
 */
class HalloWeltController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @param bool $cachable
	 *
	 * @return void
	 */
	function display($cachable = false)
	{
		// Der Standardview wird gesetzt
		JRequest::setVar('view', JRequest::getCmd('view', 'HalloWeltList'));

		// Die displaymethode der Elternklasse aufrufen
		parent::display($cachable);
	}
}
