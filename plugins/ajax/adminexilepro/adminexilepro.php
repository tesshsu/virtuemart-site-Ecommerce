<?php

/**
 * @package     WistiaControl
 * @copyright   Copyright (C) 2009 - 2014 Michael Richey. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
class plgAjaxAdminExilePro extends JPlugin {
    private $app;
    private $input;
    private $debug = false;
    private $_db;
    
    public function __construct(&$subject, $config = array()) {
        parent::__construct($subject, $config);
        $this->app = JFactory::getApplication();
        $this->input = $this->app->input;
        $this->_db = JFactory::getDbo();
    }
    
    public function onAjaxAdminExilePro() {
	if(!JFactory::getUser()->authorise('core.edit','com_plugins')) {
	    return $this->_error();
	}
	
	$method = $this->input->get('method', 'error');
	
	if(substr($method,0,1) === '_') {
	    return $this->_error(); // not allowed to access super secret methods
	}
        
	return $this->{'_' . $method}()? : $this->_error();
    }
    
    // exposed methods globally available are prefixed with a single underscore
    private function _error() {
        return false;
    }
    
    private function __clearExpired() {
	$plugin = JPluginHelper::getPlugin('system', 'adminexilepro');
	$params = new JRegistry(json_decode($plugin->params));
	$query = 'DELETE FROM '.$this->_db->qn('#__adminexilepro').' WHERE ('.$this->_db->qn('type').' IN (0,1) AND TIMESTAMPDIFF(HOUR,'.$this->_db->qn('ts').',CURRENT_TIMESTAMP()) >= 24) OR ('.$this->_db->qn('type').' = 2 AND ((expire != ts AND expire < CURRENT_TIMESTAMP()) OR (expire = ts AND CURRENT_TIMESTAMP() >= (expire + INTERVAL '.$params->get('bfpenalty',5).' MINUTE))))';
	$this->_db->setQuery($query);
	$this->_db->execute();
    }
    
    private function _stats() {
	
	$query = $this->_db->getQuery(true);
	$query->select('COUNT(rule) AS count,rule,type')->group('rule')->from('#__adminexilepro')->where('type IN (0,1)')->where('TIMESTAMPDIFF(HOUR,`ts`,CURRENT_TIMESTAMP()) <= 24');
	$this->_db->setQuery($query);
	$firewall= $this->_db->loadObjectList('rule');
	
	$query = $this->_db->getQuery(true);
	$query->select('UNIX_TIMESTAMP(ts) AS ts,UNIX_TIMESTAMP(expire) AS expire,address,fail')->from('#__adminexilepro')->where('type = 2');
	$this->_db->setQuery($query);
	$bruteforce= $this->_db->loadObjectList('address');
	
	$this->__clearExpired();
	
	return (object)array('firewall'=>$firewall,'bruteforce'=>$bruteforce);
    }
    
    private function _delete() {
	$query = $this->_db->getQuery(true);
	$query->delete('#__adminexilepro')->where($this->_db->qn('address').' = '.$this->_db->q($this->input->get('address','0.0.0.0','STRING')))->where($this->_db->qn('type').' = 2');
	$this->_db->setQuery($query);
	$this->_db->execute();
	return true;
    }
}