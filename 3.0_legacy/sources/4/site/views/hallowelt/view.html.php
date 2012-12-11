<?php
// Den direkten Aufruf verbieten.
defined('_JEXEC') or die;

/**
 * HTML View Klasse für die HalloWelt Komponente
 */
class HalloWeltViewHalloWelt extends JViewLegacy
{
	/**
	 * @var string
	 */
	protected $hallo = '';

	// Die JView display Methode wird überschrieben.
    public function display($tpl = null)
    {
        // Die Daten werden vom Model bezogen.
        $this->hallo = $this->get('Hallo');

        // Der View wird angezeigt.
        parent::display($tpl);
    }
}
