<?php

// Den direkten Aufruf verbieten.
defined('_JEXEC') or die;

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
	 * HalloWeltList view display method.
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
		JToolBarHelper::title(JText::_('COM_HALLOWELT_MANAGER_HALLOWELTLIST'), 'hallowelt');

		JToolBarHelper::deleteList('', 'halloweltlist.delete');
		JToolBarHelper::editList('hallowelt.edit');
		JToolBarHelper::addNew('hallowelt.add');

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
		JFactory::getDocument()->setTitle(JText::_('COM_HALLOWELT_ADMINISTRATION'));
	}
}
