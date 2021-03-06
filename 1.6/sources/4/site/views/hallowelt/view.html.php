<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! Viewbibliothek importieren
jimport('joomla.application.component.view');

/**
 * HTML View class for the HalloWelt Component
 */
class HalloWeltViewHalloWelt extends JView
{
    // Die JView display Methode wird überschrieben
    function display($tpl = null)
    {
        // Die Daten werden dem View zugewiesen
        $this->msg = 'Hallo Welt!';

        $this->msg = $this->get('Msg');

        // Auf Fehler prüfen.
        $errors = $this->get('Errors');

        if (count($errors))
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        // Der View wird angezeigt
        parent::display($tpl);
    }
}
