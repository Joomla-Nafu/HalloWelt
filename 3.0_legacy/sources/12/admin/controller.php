<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! Controllerbibliothek importieren
jimport('joomla.application.component.controllerx');

/**
 * Allgemeiner Controller der HalloWelt Komponente
 */
class HalloWeltController extends JController
{
    /**
     * display task
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
