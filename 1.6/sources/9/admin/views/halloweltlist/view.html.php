<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! Viewbibliothek importieren
jimport('joomla.application.component.view');

/**
 * HalloWeltList HTML View
 */
class HalloWeltViewHalloWeltList extends JView
{
    /**
     * HalloWeltList view display method
     *
     * @return void
     */
    function display($tpl = null)
    {
        // Die Daten werden vom Model bezogen
        $this->items = $this->get('Items');

        // Ein JPagination Objekt beziehen
        $this->pagination = $this->get('Pagination');

        // Die Toolbar hinzufügen
        $this->addToolBar();

        // Auf Fehler prüfen
        $errors = $this->get('Errors');

        if (count($errors))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

        // Das Template wird aufgerufen
        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('COM_HALLOWELT_MANAGER_HALLOWELTLIST'));

        JToolBarHelper::deleteList('', 'halloweltlist.delete');
        JToolBarHelper::editList('hallowelt.edit');
        JToolBarHelper::addNew('hallowelt.add');
    }
}
