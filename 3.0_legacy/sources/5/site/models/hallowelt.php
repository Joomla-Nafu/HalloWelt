<?php
// Den direkten Aufruf verbieten.
defined('_JEXEC') or die;

// Diese Klasse erweitert die JModelitem Klasse.
class HalloWeltModelHalloWelt extends JModelItem
{
    /**
     * @var string
     */
    protected $hallo = '';

	/**
	 * Gibt ein "Hallo" zurück.
	 *
	 * @return string
	 */
    public function getHallo()
    {
        if ('' == $this->hallo)
        {
            $id = JFactory::getApplication()->input->getInt('id');

            switch ($id)
            {
                case 1:
                    $this->hallo = JText::_('COM_HALLOWELT_OPTION_1');
                    break;

                case 2:
                    $this->hallo = JText::_('COM_HALLOWELT_OPTION_2');
                    break;

                default:
                    $this->hallo = JText::_('COM_HALLOWELT_UNDEFINED_MESSAGE');

                    break;
            }
        }

        return $this->hallo;
    }
}
