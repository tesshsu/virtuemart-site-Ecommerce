<?php
/**
 * Help model for Spambotcheck
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 * @link         http://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2013 vi-solutions
 * @since        Joomla 1.6 
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
jimport( 'joomla.application.component.modellist' );

/**
 * Help model class for Spambotcheck
 *
 * @package      Joomla.Administrator
 * @subpackage   com_spambotcheck
 *
 * @since        Joomla 1.6 
 */
class SpambotcheckModelHelp extends JModelList
{
  public function _construct($config = array())
  {
	parent::_construct($config);
  }
  /* nothing to do */
}