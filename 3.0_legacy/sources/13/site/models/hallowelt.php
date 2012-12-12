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
    protected $item;

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return      void
     * @since       1.6
     */
    protected function populateState()
    {
        // Get the message id
        $id = JRequest::getInt('id');
        $this->setState('message.id', $id);

        // Load the parameters.
        $params = JFactory::getApplication()->getParams();
        $this->setState('params', $params);

        parent::populateState();
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param $type string The table type to instantiate.
     * @param $prefix string A prefix for the table class name.
     * @param $config array Configuration array for model.
     *
     * @return JTable A database object.
     */
    public function getTable($type = 'HalloWelt', $prefix = 'HalloWeltTable', $config = array(), $bla='x')
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Get the message.
     * Eine Nachricht aus der Datenbank beziehen.
     *
     * @return string The message to be displayed to the user
     * @return string Die Nachricht die dem Benutzer angezeigt wird.
     */
    public function getItem()
    {
        if ( ! isset($this->item))
        {
            $id = $this->getState('message.id');

            $this->_db->setQuery($this->_db->getQuery(true)
            ->from('#__hallowelt as h')
            ->select('h.hallo, h.params, c.title as category')
            ->leftJoin('#__categories as c ON h.catid=c.id')
            ->where('h.id='.(int)$id));

            if ( ! $this->item = $this->_db->loadObject())
            {
                $this->setError($this->_db->getError());
            }
            else
            {
                // Load the JSON string
                $params = new JRegistry($this->item->params);

                $this->item->params = $params;

                // Merge global params with item params
                $params = clone $this->getState('params');
                $params->merge($this->item->params);
                $this->item->params = $params;
            }
        }

        return $this->item;
    }
}
