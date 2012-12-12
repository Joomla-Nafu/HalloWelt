<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Access check.
if ( ! JFactory::getUser()->authorise('core.manage', 'com_hallowelt'))
{
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Die Helperdatei registrieren
JLoader::register('HalloWeltHelper', JPATH_COMPONENT.'/helpers/hallowelt.php');

// Eine Instanz des Controllers mit dem Präfix 'HalloWelt' beziehen
$controller = JControllerLegacy::getInstance('HalloWelt');

// Den 'task' der im Request übergeben wurde ausführen
$controller->execute(JRequest::getCmd('task'));

// Einen Redirect durchführen wenn er im Controller gesetzt ist
$controller->redirect();
