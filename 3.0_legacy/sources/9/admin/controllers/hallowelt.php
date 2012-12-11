<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! JControllerForm Klasse importieren
jimport('joomla.application.component.controllerform');

/**
 * HalloWelt Controller
 */
class HalloWeltControllerHalloWelt extends JControllerForm
{
    /**
     * !!!
     * Da unser Namensschema nicht den Pluralisierungsregeln entspricht
     * müssen wir hier den Namen des List Views angeben.
     *
     * @var string
     */
    protected $view_list = 'HalloWeltList';
}
