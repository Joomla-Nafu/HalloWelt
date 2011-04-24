<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die JTable Klasse importieren
jimport('joomla.database.table');

/**
 * Hello Table class.
 */
class HalloWeltTableHalloWelt extends JTable
{
    /**
     * Constructor.
     *
     * @param	string Name of the table to model.
     * @param	string Name of the primary key field in the table.
     * @param	object JDatabase connector object.
     */
    function __construct(&$db)
    {
        parent::__construct('#__hallowelt', 'id', $db);
    }
}
