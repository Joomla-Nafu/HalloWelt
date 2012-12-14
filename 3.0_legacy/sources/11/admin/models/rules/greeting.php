<?php

// Den direkten Aufruf verbieten
defined('_JEXEC') or die;

// Die Joomla! JFormRule Klasse importieren
jimport('joomla.form.formrule');

/**
 * Form Rule class.
 */
class JFormRuleGreeting extends JFormRule
{
	/**
	 * The regular expression.
	 *
	 * @var         string
	 */
	protected $regex = '^[^0-9]+$';
}
