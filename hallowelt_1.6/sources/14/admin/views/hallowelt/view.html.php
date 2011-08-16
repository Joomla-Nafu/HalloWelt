<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! View Bibliothek importieren
jimport('joomla.application.component.view');

/**
 * HalloWelt View
 */
class HalloWeltViewHalloWelt extends JView
{
    /**
     * Display method of HalloWelt view.
     *
     * @return void
     */
    public function display($tpl = null)
    {
        // Die Daten werden bezogen
        $this->item = $this->get('Item');

        // Das Formular
        $this->form = $this->get('Form');

        // JavaScript
        $this->script = $this->get('Script');

        // Auf Fehler prüfen
        $errors = $this->get('Errors');

        if (count($errors))
        {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

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
        JRequest::setVar('hidemainmenu', true);

        $userId = JFactory::getUser()->id;
        //        $userId = $user->id;
        $canDo = HalloWeltHelper::getActions($this->item->id);
        $isNew = $this->item->id == 0;

        JToolBarHelper::title($isNew
        ? JText::_('COM_HALLOWELT_MANAGER_HALLOWELT_NEW')
        : JText::_('COM_HALLOWELT_MANAGER_HALLOWELT_EDIT')
        , 'hallowelt');

        // Built the actions for new and existing records.
        if ($isNew)
        {

            // For new records, check the create permission.
            if ($canDo->get('core.create'))
            {
                JToolBarHelper::apply('hallowelt.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('hallowelt.save', 'JTOOLBAR_SAVE');
                JToolBarHelper::custom('hallowelt.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
            }

            JToolBarHelper::cancel('hallowelt.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            if ($canDo->get('core.edit'))
            {
                // We can save the new record
                JToolBarHelper::apply('hallowelt.apply', 'JTOOLBAR_APPLY');
                JToolBarHelper::save('hallowelt.save');

                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($canDo->get('core.create'))
                {
                    JToolBarHelper::custom('hallowelt.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
                }
            }

            if ($canDo->get('core.create'))
            {
                JToolBarHelper::custom('hallowelt.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            }

            JToolBarHelper::cancel('hallowelt.cancel', 'JTOOLBAR_CLOSE');
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
        $isNew = ($this->item->id < 1);

        $document = JFactory::getDocument();

        $document->setTitle($isNew
        ? JText::_('COM_HALLOWELT_HALLOWELT_CREATING')
        : JText::_('COM_HALLOWELT_HALLOWELT_EDITING'));

        $document->addScript(JURI::root(true).$this->script);

        $document->addScript(JURI::root(true)
        .'/administrator/components/com_hallowelt/views/hallowelt/submitbutton.js');

        JText::script('COM_HALLOWELT_HALLOWELT_ERROR_UNACCEPTABLE');
    }
}
