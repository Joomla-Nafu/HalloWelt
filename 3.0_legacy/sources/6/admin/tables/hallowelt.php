<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die JTable Klasse importieren
jimport('joomla.database.table');

/**
 * HalloWelt Table class.
 */
class HalloWeltTableHalloWelt extends JTable
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabase  &$db  JDatabase connector object.
	 */
	function __construct(&$db)
	{
		parent::__construct('#__hallowelt', 'id', $db);
	}
}
