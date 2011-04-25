<?php
// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! Controllerbibliothek importieren
jimport('joomla.application.component.controller');

// Die Helperdatei registrieren
JLoader::register('HalloWeltHelper', JPATH_COMPONENT.'/helpers/hallowelt.php');

// Eine Instanz des Controllers mit dem Präfix 'HalloWelt' beziehen
$controller = JController::getInstance('HalloWelt');

// Den 'task' der im Request übergeben wurde ausführen
$controller->execute(JRequest::getCmd('task'));

// Einen Redirect durchführen wenn er im Controller gesetzt ist
$controller->redirect();
