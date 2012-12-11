<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;
 
// Die Joomla! Modelitem Klasse importieren
jimport('joomla.application.component.modelitem');
 
// Diese Klasse erweitert die JModelitem Klasse
class HalloWeltModelHalloWelt extends JModelItem
{
    /**
     * @var string msg
     */
    protected $msg;
 
    /**
     * Get the message.
     * @return string The message to be displayed to the user
     */
    public function getMsg()
    {
        if ( ! isset($this->msg))
        {
            $id = JRequest::getInt('id');
 
            switch ($id)
            {
                case 1:
                    $this->msg = JText::_('COM_HALLOWELT_OPTION_1');
                    break;
 
                case 2:
                    $this->msg = JText::_('COM_HALLOWELT_OPTION_2');
                    break;
 
                default:
                    $this->msg = JText::_('COM_HALLOWELT_UNDEFINED_MESSAGE');
 
                    break;
            }
        }
 
        return $this->msg;
    }
}
