<?php

/**
 * @package plugin AdminExile
 * @copyright (C) 2010-2017 Michael Richey
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
require_once(__DIR__ . '/classes/aehelper.class.php');

class plgSystemAdminExilePro extends JPlugin {

    private $_app;
    private $_ip;
    private $_key;
    /* pro feature start */
    private $_require;
    private $_db;
    /* pro feature end */
    private $_pass = false;
    private $_failed = false;

    public function __construct(&$subject, $config = array()) {
	$this->_app = JFactory::getApplication();
	$this->_ip = AdminExileHelper::getIP();
	/* pro feature start */
	$this->_require = preg_match_all('/:/', $this->_ip) ? 'SimpleCIDR6' : 'SimpleCIDR';
	/* pro feature end */
	if (
		($this->_app->isAdmin() && !JFactory::getUser()->guest) || // not logged into the backend
		$this->_app->isSite() // accessing frontend
	)
	{
	    $this->_pass = true;
	}
	$this->_db = JFactory::getDbo();
	parent::__construct($subject, $config);
    }

    public function onAfterInitialise() {
	if ($this->_app->isSite() && \JFactory::getUser()->guest && $this->params->get('frontrestrict', 0))
	{
	    return $this->_frontrestrict();
	}

	if (
		$this->_app->isSite() ||
		/* pro feature start */
		$this->_reentry() ||
		/* pro feature end */
		($this->_app->isAdmin() && !\JFactory::getUser()->guest))
	{
	    return;
	}

	$this->_key = $this->params->get('key', 'adminexile');
	
	if ($this->params->get('maillink', 1))
	{
	    $this->_maillink();
	}

	/* pro feature start */
	if ($this->_reentry())
	{
	    $this->_authorize();
	    return;
	}


	$this->_require = preg_match_all('/:/', $this->_ip) ? 'SimpleCIDR6' : 'SimpleCIDR';
	if (!class_exists($this->_require))
	{
	    require_once(__DIR__ . '/classes/' . strtolower($this->_require) . '.class.php');
	}
	if ($this->_whitelisted())
	{
	    $this->_pass = true;
	    $this->_authorize();
	}

	if ($this->_blacklisted() && !$this->_pass)
	{
	    $this->_fail();
	}
	
	$this->_bruteforce();
	/* pro feature end */

	if ($this->_pass || $this->_app->getUserState("plg_sys_adminexilepro.$this->_key", false) || $this->_keyauth())
	{
	    $this->_authorize();
	}
	else
	{
	    $this->_fail();
	}
    }

    /* pro feature start */

    public function onUserLogout($user) {
	// Initialise variables.
	if ($this->_app->isSite() && $this->params->get('allow_reentry', 0))
	{
	    return true;
	}
	$this->_dbLog((object) array('type' => 3, 'address' => $this->_ip, 'rule' => $this->_ip));
	return true;
    }

    /* pro feature end */

    private function _keyauth() {
	$keyvalue = filter_input(INPUT_GET, $this->_key);
	$key = !\is_null($keyvalue);
	return $this->params->get('twofactor', false) ? ($key && $this->params->get('keyvalue', false) === $keyvalue) : $key;
    }

    private function _authorize() {
	$this->_app->setUserState("plg_sys_adminexilepro.$this->_key", true);
    }

    private function _fail($action = true) {
	AdminExileHelper::stealth();
	$this->_faillog('(' . $this->_ip . ') failed to authenticate via AdminExile');
	if ($action)
	{
	    $this->_failAction();
	}
	$this->_failed = true;
	switch ($this->params->get('redirect', 'HOME'))
	{
	    case '{HOME}': // 2.x fallback
	    case 'HOME':
		header("Location: " . JURI::root());
		break;
	    case '{404}': // 2.x fallback
	    case '404':
		header(\filter_input(INPUT_SERVER, 'SERVER_PROTOCOL') . ' 404 Not Found');
		header("Status: 404 Not Found");
		$find = array('{url}', '{serversignature}');
		$replace = array(\filter_input(INPUT_SERVER, 'REQUEST_URI'), \filter_input(INPUT_SERVER, 'SERVER_SIGNATURE'));
		die(str_replace($find, $replace, $this->params->get('fourofour')));
		break;
	    default:
		$destination = $this->params->get('redirecturl', 'https://www.fbi.gov');
		if ($destination == 'https://www.richeyweb.com')
		{
		    $destination = 'https://www.fbi.gov';
		}
		header("Location: " . $destination);
		break;
	}
    }

    public function _frontrestrict() {
	$username = $this->_app->input->post->get('username', false);
	if (!$username)
	{
	    return;
	}
	$userid = AdminExileHelper::userIDFromUsername($username);
	if (!is_numeric($userid) || $userid === 0)
	{
	    return;
	}
	$user = JFactory::getUser($userid);
	if (!count(array_intersect($user->getAuthorisedGroups(), $this->params->get('restrictgroup', array()))))
	{
	    return;
	}
	$this->_faillog('AdminExile prevented user (' . $username . ') attempt to log into the frontend via ' . $this->_ip);
	JFactory::getSession()->close();
	return;
    }

    private function _maillink() {
	$username = filter_input(INPUT_GET, 'maillink');
	if ($username === null)
	{
	    return;
	}
	$this->loadLanguage('plg_system_adminexilepro');
	$userid = AdminExileHelper::userIDFromUsername($username);
	if (is_numeric($userid) && $userid != 0)
	{
	    $user = JFactory::getUser($userid);
	    if (!count(array_intersect($user->getAuthorisedGroups(), $this->params->get('maillinkgroup', array()))))
	    {
		$this->_fail();
	    }
	    $query = $this->params->get('twofactor', false) ? http_build_query(array($this->_key => urlencode($this->params->get('keyvalue', false)))) : $this->_key;
	    $secureurl = AdminExileHelper::buildURL($query);
	    $subject = JText::sprintf('PLG_SYS_ADMINEXILEPRO_EMAIL_SUBJECT', $this->_app->getCfg('sitename'));
	    $body = JText::sprintf('PLG_SYS_ADMINEXILEPRO_EMAIL_BODY', $secureurl, $user->email, $user->username, $this->_ip);
	    AdminExileHelper::sendMail($user->email, $subject, $body);
	}
	$this->_fail(false);
    }

    private function _faillog($log) {
	if ($this->params->get('faillog', 0))
	{
	    error_log($log);
	}
    }

    private function _failAction() {
	/* pro feature start */
	if ($this->params->get('enablebruteforce', false) && !$this->_blacklisted())
	{
	    $record = $this->_bruteforcerecord();
	    if ($record && $record->ts && !$this->_failed)
	    {
		$this->_updateBFRecord($record);
	    }
	    else
	    {
		$insert = (object) array('ts' => 'CURRENT_TIMESTAMP()', 'address' => $this->_ip, 'type' => 2, 'fail' => 1, 'expire' => 'CURRENT_TIMESTAMP()');
		$this->_db->insertObject('#__adminexilepro', $insert);
	    }

	    if (
		    (int) $this->params->get('bfnotify', 0) === 1 && $this->params->get('bfuser', false) &&
		    (
		    ((int) $this->params->get('bfnotifyonce', 0) === 0 && $record && $record->fail % (int) $this->params->get('bfthreshold', 5) === 0) ||
		    ((int) $this->params->get('bfnotifyonce', 1) === 1 && $record && $record->fail === (int) $this->params->get('bfthreshold', 5))
		    )
	    )
	    {
		$this->_bfemail($record);
	    }
	}
	/* pro feature end */
	return;
    }

    /* pro feature start */

    private function _updateBFRecord($record) {
	$record->fail++;
	$penalty = ($record->fail >= $this->params->get('bfthreshold', 5)) ? $this->_penalty($record->fail) : 0;
	$query = $this->_db->getQuery(true);
	$values = array(
	    'ts = CURRENT_TIMESTAMP()',
	    'fail = ' . $record->fail,
	    'expire = ADDTIME(CURRENT_TIMESTAMP(),sec_to_time(' . $penalty . '))'
	);
	$query->update('#__adminexilepro')->set($values)->where('ts = ' . $this->_db->q($record->ts) . ' AND address = ' . $this->_db->q($this->_ip) . ' AND type = 2');
	$this->_db->setQuery($query);
	$this->_db->execute();
    }

    private function _bfemail($record) {
	$this->loadLanguage('plg_system_adminexilepro');
	$config = JFactory::getConfig();
	$sitename = $config->get('sitename');
	$user = JFactory::getUser($this->params->get('bfuser', 0));
	$subject = JText::sprintf('PLG_SYS_ADMINEXILEPRO_BFEMAIL_SUBJECT', $sitename);
	$body = array(JText::sprintf('PLG_SYS_ADMINEXILEPRO_BFEMAIL_BODY1', $sitename, JURI::root()));
	$body[] = '';
	$body[] = JText::_('PLG_SYS_ADMINEXILEPRO_BFEMAIL_BODY2');
	$body[] = '';
	$body[] = JText::sprintf('PLG_SYS_ADMINEXILEPRO_BFEMAIL_BODY3', $this->_ip);
	$body[] = '';
	$body[] = JText::sprintf('PLG_SYS_ADMINEXILEPRO_BFEMAIL_BODY4', $record->expire);
	$body[] = '';
	$body[] = JText::sprintf('PLG_SYS_ADMINEXILEPRO_BFEMAIL_BODY5', $record->fail);
	if ((int) $this->params->get('bfnotifyonce', 0) === 1)
	{
	    $body[] = '';
	    $body[] = JText::_('PLG_SYS_ADMINEXILEPRO_BFEMAIL_BODY6');
	}
	AdminExileHelper::sendMail($user->email, $subject, implode("\n", $body));
    }

    private function _penalty($fail) {
	$count = ($fail >= $this->params->get('bfthreshold', 5)) ? $fail : $this->params->get('bfthreshold', 5);
	$failabove = $count - $this->params->get('bfthreshold', 5);
	$multiply = (($failabove? : 1)) * $this->params->get('bfmultiplier', 1);
	$penalty = ($this->params->get('bfpenalty', 5) * 60) * $multiply;
	return $penalty;
    }

    private function _bruteforcerecord() {
	$query = $this->_db->getQuery(true);
	$query->select('ts,fail,expire,UNIX_TIMESTAMP(ts) AS uts,UNIX_TIMESTAMP(expire) AS uexpire')->from('#__adminexilepro')->where('address = ' . $this->_db->q($this->_ip))->where('type = 2');
	$this->_db->setQuery($query);
	$record = $this->_db->loadObject();
	return $record;
    }

    private function _bruteforce() {
	if($this->_fail) return;
	$record = $this->_bruteforcerecord();
	if ($record)
	{
	    if ($record->uexpire < time())
	    {
		$this->_bruteforceclean();
		return;
	    }
	    if ($record->fail < $this->params->get('bfthreshold', 5))
	    {
		return;
	    }
	    $this->_fail();
	}
    }

    private function _bruteforceclean() {
	$query = $this->_db->getQuery(true);
	$query->delete('#__adminexilepro')->where('type=2 AND ((expire != ts AND expire < CURRENT_TIMESTAMP()) OR (expire = ts AND CURRENT_TIMESTAMP() >= (expire + INTERVAL ' . $this->params->get('bfpenalty', 5) . ' MINUTE)))');
	$this->_db->setQuery($query);
	$this->_db->execute();
    }

    private function _reentryrecord() {
	$this->_dbClean(array(3));
	$query = $this->_db->getQuery(true);
	$query->select('UNIX_TIMESTAMP(ts) AS uts')->from('#__adminexilepro')->where('address = ' . $this->_db->q($this->_ip))->where('type = 3');
	$this->_db->setQuery($query);
	$record = $this->_db->loadObject();
	return $record;
    }

    private function _reentry() {
	if (!$this->params->get('allow_reentry', 0))
	{
	    return false;
	}
	$record = $this->_reentryrecord();
	return ($record && $record->uts <= time() + $this->params->get('reentry_time', 60));
    }

    private function _whitelisted() {
	if ($match = $this->_listcontains('whitelist', $this->_require))
	{
	    $this->_dbLog((object) array('type' => 1, 'address' => $this->_ip, 'rule' => $match));
	    return true;
	}
	return false;
    }

    private function _blacklisted() {
	$match = $this->_listcontains('blacklist', $this->_require);
	if ($match)
	{
	    $this->_dbLog((object) array('type' => 0, 'address' => $this->_ip, 'rule' => $match));
	    $this->_faillog('(' . $this->_ip . ') blacklisted by AdminExile IP Security: ' . $match);
	    return true;
	}
	return false;
    }

    private function _listcontains($source) {
	if (!$this->params->get('enableip', false))
	{
	    return false;
	}
	foreach ((array) $this->params->get($source) as $item)
	{
	    $item->address = trim($item->address);
	    $item->address = strtolower($item->address);
	    $net46 = preg_match_all('/:/', $item->address) ? 'SimpleCIDR6' : 'SimpleCIDR';
	    // reasons to skip
	    if (
		    ($this->_require !== $net46) || // don't bother testing the wrong ip version
		    ($this->_require === 'SimpleCIDR' && $item->netmask > 32) // invalid netmask for IPv4
		) 
	    {
		break; 
	    }
	    $net = trim($item->address) . '/' . trim($item->netmask);
	    $test = $net46::getInstance($net);
	    $result = $test->contains($this->_ip);
	    if ($result !== false)
	    {
		return $item->address . '/' . $item->netmask;
	    }
	}
	return false;
    }

    private function _dbLog($values) {
	$query = 'INSERT INTO ' . $this->_db->qn('#__adminexilepro') .
		' (' . $this->_db->qn('type') . ',' . $this->_db->qn('address') . ',' . $this->_db->qn('rule') . ') ' .
		'VALUES (' . $this->_db->q($values->type) . ',' . $this->_db->q($values->address) . ',' . $this->_db->q($values->rule) . ') ' .
		'ON DUPLICATE KEY UPDATE ' . $this->_db->qn('ts') . '=CURRENT_TIMESTAMP()';
	$this->_db->setQuery($query);
	$this->_db->execute();
//	$this->_db->insertObject('#__adminexilepro',$values); /* re-apply when MySQL min version is at or above 5.6 */
	$this->_dbClean(in_array($values->type, array(0, 1)) ? array(0, 1) : array());
    }

    private function _dbClean($types = array()) {
	if (!count($types))
	{
	    return;
	}
	$query = $this->_db->getQuery(true);
	$query->delete('#__adminexilepro')->where($this->_db->qn('type') . ' IN (' . implode(',', $types) . ')')->where('TIMESTAMPDIFF(HOUR,`ts`,CURRENT_TIMESTAMP()) >= 24');
	$this->_db->setQuery($query);
	$this->_db->execute();
    }
    /* pro feature end */
}
