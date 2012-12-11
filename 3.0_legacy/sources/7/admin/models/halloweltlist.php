<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! Modellist Klasse importieren
jimport('joomla.application.component.modellist');

/**
 * HalloWeltList Model
 */
class HalloWeltModelHalloWeltList extends JModelList
{
    /**
     * Method to build an SQL query to load the list data.
     * Funktion um einen SQL Query zu erstellen der die Daten für die Liste läd.
     *
     * @return string SQL query
     */
    protected function getListQuery()
    {
        // Ein Datenbankobjekt beziehen.
        $db = JFactory::getDBO();

        // Ein neues (leeres) Queryobjekt beziehen.
        $query = $db->getQuery(true);

        // Aus der Tabelle 'hallowelt'...
        $query->from('#__hallowelt');

        // ... ein paar Felder auswählen.
        $query->select('id, hallo');

        return $query;
    }
}
