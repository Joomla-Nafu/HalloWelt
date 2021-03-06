<?php

// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! JModelAdmin Klasse importieren
jimport('joomla.application.component.modeladmin');

/**
 * HalloWelt Model
 */
class HalloWeltModelHalloWelt extends JModelAdmin
{
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param    array     $data    An array of input data.
	 * @param    string    $key     The name of the key for the primary key.
	 *
	 * @return    boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()
			->authorise(
				'core.edit',
				'com_hallowelt.message.' . ((int) isset($data[$key]) ? $data[$key] : 0)
			)
			or parent::allowEdit($data, $key);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param       type    The table type to instantiate
	 * @param       string  A prefix for the table class name. Optional.
	 * @param       array   Configuration array for model. Optional.
	 *
	 * @return      JTable  A database object
	 */
	public function getTable($type = 'HalloWelt', $prefix = 'HalloWeltTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param array   $data     Data for the form.
	 * @param boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return mixed A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hallowelt.hallowelt', 'hallowelt',
			array('control' => 'jform', 'load_data' => $loadData)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string Script file
	 */
	public function getScript()
	{
		return '/administrator/components/com_hallowelt/models/forms/hallowelt.js';
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return      mixed   The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()
			->getUserState('com_hallowelt.edit.hallowelt.data');

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
}
