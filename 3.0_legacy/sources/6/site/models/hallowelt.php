<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! Modelitem Klasse importieren
jimport('joomla.application.component.modelitem');

/**
 * HalloWelt Frontend Model
 */
class HalloWeltModelHalloWelt extends JModelItem
{
    /**
     * @var string msg
     */
    protected $msg;

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param $type string The table type to instantiate.
     * @param $prefix string A prefix for the table class name.
     * @param $config array Configuration array for model.
     *
     * @return JTable A database object.
     */
    public function getTable($type = 'HalloWelt', $prefix = 'HalloWeltTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Get the message.
     *
     * @return string The message to be displayed to the user
     */
    public function getMsg()
    {
        if ( ! isset($this->msg))
        {
            $id = JRequest::getInt('id');

            // Eine Instanz der HalloWelt Tabelle beziehen
            $table = $this->getTable();

            // Den angeforderten Datensatz laden
            $table->load($id);

            // Die Nachricht weitergeben wenn ein Datensatz gefunden wurde
            if($table->greeting)
            {
                $this->msg = $table->greeting;
            }
            else
            {
                $this->msg = JText::_('COM_HALLOWELT_UNDEFINED_MESSAGE');
            }
        }

        return $this->msg;
    }
}
