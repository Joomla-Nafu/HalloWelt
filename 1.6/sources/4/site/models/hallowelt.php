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
        if( ! isset($this->msg))
        {
            $this->msg = 'Hallo Welt !';
        }

        return $this->msg;
    }
}
