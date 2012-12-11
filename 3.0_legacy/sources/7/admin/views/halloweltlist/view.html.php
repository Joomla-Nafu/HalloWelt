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
	 * HalloWeltList view display method
	 *
	 * @param null $tpl
	 *
	 * @return void
	 */
	function display($tpl = null)
	{
		JToolbarHelper::title('Hallo Welt !');

		// Die Daten werden vom Model bezogen
		$this->items = $this->get('Items');

		// Ein JPagination Objekt beziehen
		$this->pagination = $this->get('Pagination');

		// Das Template wird aufgerufen
		parent::display($tpl);
	}
}
