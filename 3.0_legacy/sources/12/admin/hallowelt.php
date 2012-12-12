<?php

// Den direkten Aufruf verbieten.
defined('_JEXEC') or die;

// Die Helperdatei registrieren.
JLoader::register('HalloWeltHelper', JPATH_COMPONENT.'/helpers/hallowelt.php');

// Eine Instanz des Controllers mit dem Präfix 'HalloWelt' beziehen.
$controller = JControllerLegacy::getInstance('HalloWelt');

// Den 'task' der im Request übergeben wurde ausführen.
$controller->execute(JFactory::getApplication()->input->getCmd('task'));

// Einen Redirect durchführen wenn er im Controller gesetzt wurde.
$controller->redirect();
