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
        JSubMenuHelper::addEntry(JText::_('COM_HALLOWELT_SUBMENU_MESSAGES')
        , 'index.php?option=com_hallowelt'
        , $submenu == 'messages');

        JSubMenuHelper::addEntry(JText::_('COM_HALLOWELT_SUBMENU_CATEGORIES')
        , 'index.php?option=com_categories&view=categories&extension=com_hallowelt'
        , $submenu == 'categories');

        if ($submenu == 'categories')
        {
            $document = JFactory::getDocument();

            $document->addStyleDeclaration(
        	'.icon-48-hallowelt-categories '
        	.'{background-image: url(../media/com_hallowelt/images/tux-48x48.png) !important;}'); //dirty ;(

        	$document->setTitle(JText::_('COM_HALLOWELT_ADMINISTRATION_CATEGORIES'));
        }
    }
}
