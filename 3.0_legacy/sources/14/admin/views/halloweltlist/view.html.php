<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! View Bibliothek importieren
jimport('joomla.application.component.view');

/**
 * HalloWeltList HTML View
 */
class HalloWeltViewHalloWeltList extends JViewLegacy
{
	/**
	 * @var array
	 */
	protected $items = array();

	/**
	 * @var JPagination
	 */
	protected $pagination;

	/**
	 * HalloWeltList view display method
	 *
	 * @param null $tpl
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

        // Das Template wird aufgerufen
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        $canDo = HalloWeltHelper::getActions();

        JToolBarHelper::title(JText::_('COM_HALLOWELT_MANAGER_HALLOWELTLIST')
        , 'hallowelt');

        if ($canDo->get('core.create'))
        {
            JToolBarHelper::addNew('hallowelt.add');
        }

        if ($canDo->get('core.edit'))
        {
            JToolBarHelper::editList('hallowelt.edit');
        }

        if ($canDo->get('core.delete'))
        {
            JToolBarHelper::deleteList('', 'halloweltlist.delete');
        }

        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_hallowelt');
        }

        // CSS Klasse für das 48x48 Icon der Toolbar
        JFactory::getDocument()->addStyleDeclaration(
        '.icon-48-hallowelt {background-image: url(../media/com_hallowelt/images/tux-48x48.png);}'
        );
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        JFactory::getDocument()
        ->setTitle(JText::_('COM_HALLOWELT_ADMINISTRATION'));
    }
}
