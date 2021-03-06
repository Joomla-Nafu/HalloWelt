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

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return    string
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_hallowelt.message.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return    string
	 */
	protected function _getAssetTitle()
	{
		return $this->greeting;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return    int
	 */
	protected function _getAssetParentId()
	{
		$asset = JTable::getInstance('Asset');

		$asset->loadByName('com_hallowelt');

		return $asset->id;
	}
}
