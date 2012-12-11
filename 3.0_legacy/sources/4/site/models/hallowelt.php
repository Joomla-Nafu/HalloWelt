 <?php
// Den direkten Aufruf verbieten
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
        if('' == $this->hallo)
        {
            $this->hallo = 'Hallo Welt !';
        }

        return $this->hallo;
    }
}
