<?php
##*HEADER*##

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport('joomla.plugin.plugin');

/**
 * Example System Plugin
 *
 * @package    modein
 * @subpackage Plugin
 */
class plgSystemmodein extends JPlugin
{
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @access      protected
     * @param       object  $subject The object to observe
     * @param       array   $config  An array that holds the plugin configuration
     */
    function plgSystemmodein( &$subject, $config )
    {
        parent::__construct( $subject, $config );

    }//function

    /**
     * Do something onAfterInitialise
     */
    function onAfterInitialise()
    {
     $heute=date("Y-m-d");
         $mod_id = $this->params->get('modul_id');
      $db = JFactory::getDbo();
      $db->setQuery("SELECT * FROM #__termine WHERE datum >= '$heute' ORDER BY datum LIMIT 0,2");
      $termine = $db->loadObjectList();

      $active =($termine) ? 1 : 0;
      $db->setQuery("UPDATE #__modules SET published = 1 WHERE ID='".$mod_id."'"); 
     // $db->setQuery("UPDATE #__modules SET published = $active WHERE ID='66'");
      $db->query(); 
    }//function

    /**
     * Do something onAfterRoute
     */
    function onAfterRoute()
    {
    }//function

    /**
     * Do something onAfterDispatch
     */
    function onAfterDispatch()
    {
    }//function

    /**
     * Do something onAfterRender
     */
    function onAfterRender()
    {
    }//function

}//class