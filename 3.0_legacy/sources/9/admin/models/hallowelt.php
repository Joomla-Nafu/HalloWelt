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
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  JTable
     */
    public function getTable($name = 'HalloWelt', $prefix = 'HalloWeltTable', $options = array())
    {
        return JTable::getInstance($name, $prefix, $options);
    }
    /**
     * Method to get the record form.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return mixed A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_hallowelt.hallowelt', 'hallowelt'
        , array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        return $form;
    }
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return      mixed   The data for the form.
     * @since       1.6
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
