<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

// No direct access

defined('_JEXEC') or die;

// Register helper class
require_once dirname(__FILE__) . '/helpers/ztvirtuemarter.php';
// Require the base controller
$input = JFactory::getApplication()->input;
$view = $input->getCmd('view');
$task = $input->getCmd('task');
$controllerClass = 'ZtvirtuemarterController' . ucfirst($view);
$controllerPath = 'controllers/' . $view . '.php';
require_once($controllerPath);

if (class_exists($controllerClass)) {
    $controller = new $controllerClass();
    $controller->execute($task);
    $controller->redirect();
}
