<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

/**
 * HalloWelt View
 */
class HalloWeltViewHalloWelt extends JViewLegacy
{
	/**
	 * @var stdClass
	 */
	protected $item;

	/**
	 * @var JForm
	 */
	protected $form;

	/**
	 * Display method of HalloWelt view.
	 *
	 * @param null $tpl
	 *
	 * @return void
	 */
    public function display($tpl = null)
    {
        // Die Daten werden bezogen
        $this->item = $this->get('Item');

        // Das Formular
        $this->form = $this->get('Form');

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
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        JToolBarHelper::title($isNew
        ? JText::_('COM_HALLOWELT_MANAGER_HALLOWELT_NEW')
        : JText::_('COM_HALLOWELT_MANAGER_HALLOWELT_EDIT')
        , 'hallowelt');

        JToolBarHelper::save('hallowelt.save');

        JToolBarHelper::cancel('hallowelt.cancel'
        , $isNew
        ? 'JTOOLBAR_CANCEL'
        : 'JTOOLBAR_CLOSE');

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
        $isNew = ($this->item->id < 1);

        JFactory::getDocument()->setTitle($isNew
        ? JText::_('COM_HALLOWELT_HALLOWELT_CREATING')
        : JText::_('COM_HALLOWELT_HALLOWELT_EDITING'));
    }
}
