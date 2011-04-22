<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! Viewbibliothek importieren
jimport('joomla.application.component.view');

/**
 * HalloWeltList HTML View
 */
class HalloWeltViewHalloWeltList extends JView
{
    /**
     * HalloWeltList view display method
     *
     * @return void
     */
    function display($tpl = null)
    {
        // Die Daten werden vom Model bezogen
        $this->items = $this->get('Items');

        // Ein JPagination Objekt beziehen
        $this->pagination = $this->get('Pagination');

        // Auf Fehler prüfen
        $errors = $this->get('Errors');

        if (count($errors))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

        // Das Template wird aufgerufen
        parent::display($tpl);
    }
}
