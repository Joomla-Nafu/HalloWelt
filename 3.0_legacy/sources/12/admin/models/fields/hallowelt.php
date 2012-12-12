<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! Form Helper Bibliothek importieren
jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');

/**
 * HalloWelt     Form Field class for the HalloWelt     component
 */
class JFormFieldHalloWelt extends JFormFieldList
{
    /**
     * The field type.
     *
     * @var         string
     */
    protected $type = 'HalloWelt';

    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);

        $query->from('#__hallowelt AS h');
        $query->select('h.id AS id, h.hallo, h.catid, c.title AS category');
        $query->leftJoin('#__categories AS c on h.catid=c.id');

        $db->setQuery((string)$query);

        $messages = $db->loadObjectList();

        $options = array();

        if ($messages)
        {
            foreach($messages as $message)
            {
                $options[] = JHtml::_('select.option', $message->id
                , $message->hallo.($message->catid ? ' (' . $message->category . ')' : ''));
            }
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
