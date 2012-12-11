<?php

// Den direkten Aufruf verbieten.
defined('_JEXEC') or die;

// Die Joomla! Controllerbibliothek importieren.
jimport('joomla.application.component.controller');

// Eine Instanz des Controllers mit dem Präfix 'HalloWelt' beziehen.
$controller = JControllerLegacy::getInstance('HalloWelt');

$task = JFactory::getApplication()->input->getCmd('task');

// Den 'task' der im Request übergeben wurde ausführen.
$controller->execute($task);

// Einen Redirect durchführen wenn er im Controller gesetzt wurde.
$controller->redirect();
