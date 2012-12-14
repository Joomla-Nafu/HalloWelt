<?php

// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

/**
 * HalloWelt component helper.
 */
abstract class HalloWeltHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_HALLOWELT_SUBMENU_MESSAGES'),
			'index.php?option=com_hallowelt',
			$submenu == 'messages');

		JSubMenuHelper::addEntry(
			JText::_('COM_HALLOWELT_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&view=categories&extension=com_hallowelt',
			$submenu == 'categories');

		if ($submenu == 'categories')
		{
			$document = JFactory::getDocument();

			$document->addStyleDeclaration(
				'.icon-48-hallowelt-categories '
					. '{background-image: url(../media/com_hallowelt/images/tux-48x48.png) !important;}');
			// Dirty ;(

			$document->setTitle(JText::_('COM_HALLOWELT_ADMINISTRATION_CATEGORIES'));
		}
	}

	/**
	 * Get the actions
	 */
	public static function getActions($messageId = 0)
	{
		$user = JFactory::getUser();
		$result = new JObject;

		if (empty($messageId))
		{
			$assetName = 'com_hallowelt';
		}
		else
		{
			$assetName = 'com_hallowelt.message.' . (int) $messageId;
		}

		$actions = array('core.admin', 'core.manage', 'core.create'
		, 'core.edit', 'core.delete');

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
