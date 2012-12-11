<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! Viewbibliothek importieren
jimport('joomla.application.component.view');

/**
 * HTML View Klasse für die HalloWelt Komponente.
 */
class HalloWeltViewHalloWelt extends JView
{
    // Die JView display Methode wird überschrieben
    function display($tpl = null)
    {
        // Die Daten werden dem View zugewiesen
        $this->msg = 'Hallo Welt!';

        // Der View wird angezeigt
        parent::display($tpl);
    }
}
