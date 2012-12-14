<?php

// Den direkten Aufruf verbieten
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
