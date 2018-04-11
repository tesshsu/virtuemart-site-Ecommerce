<?php
/*
copyright 2009 Fiona Coulter http://www.spiralscripts.co.uk

This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
// no direct access
defined('_JEXEC') or die('Restricted access');


class modVMModControllerProFieldsHelper
{
	public static $counter = 0;
	public static $categoryTree = array();

	public static function categoryListTree($cid = 0, $level = 0, $key = "value", $value = "text") {

		if(empty(self::$categoryTree)){
// 			vmTime('Start with categoryListTree');
			self::$categoryTree = self::categoryListTreeLoop($cid, $level, $key, $value);
			
// 			vmTime('end loop categoryListTree '.self::$counter);
		}

		return self::$categoryTree;
	}	
	
	public static function categoryListTreeLoop($cid = 0, $level = 0, $key = "value", $value = "text") {

		self::$counter++;

		static $categoryTree = array();

		$virtuemart_vendor_id = 1;

// 		vmSetStartTime('getCategories');
		$categoryModel = self::getModel('category');
		$level++;

		$categoryModel->_noLimit = true;
		$app = JFactory::getApplication();
		$records = $categoryModel->getCategories($app->isSite(), $cid);
// 		vmTime('getCategories','getCategories');
		$selected="";
		if(!empty($records)){
			foreach ($records as $id => $category) {

				$childId = $category->category_child_id;

				if ($childId != $cid) {
					$item = new stdClass();
					$item->$key = $childId;
					$item->$value = str_repeat(' - ', ($level-1) ).$category->category_name;
					$categoryTree[] = $item;
				}

				if($categoryModel->hasChildren($childId)){
					self::categoryListTreeLoop($childId, $level, $key, $value );
				}

			}
		}

		return $categoryTree;
	}	

	public static function getModel($name){

		$name = strtolower($name);
		$className = ucfirst($name);

		if( !class_exists('VirtueMartModel'.$className) ){

			$modelPath = JPATH_VM_ADMINISTRATOR."/models/".$name.".php";

			if( file_exists($modelPath) ){
				require( $modelPath );
			}
			else{
				JError::raiseWarning( 0, 'Model '. $name .' not found.' );
				echo 'Model '. $name .' not found.';die;
				return false;
			}
		}

		$className = 'VirtueMartModel'.$className;
// 		instancing the object
		$model = new $className();

		if(empty($model)){
			JError::raiseWarning( 0, 'Model '. $name .' not created.' );
			echo 'Model '. $name .' not created.';
		}else {
			return $model;
		}

	}

	
}