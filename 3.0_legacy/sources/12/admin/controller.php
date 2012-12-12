<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

/**
 * Allgemeiner Controller der HalloWelt Komponente.
 */
class HalloWeltController extends JControllerLegacy
{
	/**
	 * Display task.
	 *
	 * @param bool $cachable
	 * @param bool $urlparams
	 *
	 * @return void
	 */
    function display($cachable = false, $urlparams = false)
    {
        // Der Standardview wird gesetzt
        JRequest::setVar('view', JRequest::getCmd('view', 'HalloWeltList'));

        // Die displaymethode der Elternklasse aufrufen
        parent::display($cachable, $urlparams);

        // Das Submenü generieren
        HalloWeltHelper::addSubmenu('messages');
    }
}
