<?php

// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! JControllerAdmin Klasse importieren
jimport('joomla.application.component.controlleradmin');

/**
 * HalloWeltList Controller
 */
class HalloWeltControllerHalloWeltList extends JControllerAdmin
{

	/**
	 * Proxy for getModel.
	 */
	public function getModel($name = 'HalloWelt', $prefix = 'HalloWeltModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}
