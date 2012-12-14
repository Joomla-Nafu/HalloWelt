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
	 * @param    object JDatabase connector object.
	 */
	function __construct(&$db)
	{
		parent::__construct('#__hallowelt', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @param       array           named array
	 *
	 * @see   JTable:bind
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string) $parameter;
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded load function
	 *
	 * @param int     $pk    primary key
	 * @param boolean $reset reset data
	 *
	 * @see JTable:load
	 * @return boolean
	 */
	public function load($pk = null, $reset = true)
	{
		if (parent::load($pk, $reset))
		{
			// Convert the params field to a registry.
			$params = new JRegistry;
			$params->loadJSON($this->params);
			$this->params = $params;

			return true;
		}
		else
		{
			return false;
		}
	}
}
