<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* GeoLocator is free software released under GNU/GPL  This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* This php file was create by www.rupostel.com team
*/
class geoHelper {

    static function getClientIP($ip=""){
        if (empty($ip)) {
            if (class_exists('ShopFunctions')){
                $t = new ShopFunctions();
                if(method_exists($t,'getClientIP')){
                    $ip = ShopFunctions::getClientIP();
                }
            }


            if(empty($ip)) {
                if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else if (empty($ip) && (!empty($_SERVER['REMOTE_ADDR']))){
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
            }
        }
        return $ip;
    }

	static function getCountry2Code($ip="") {

        $ip = self::getClientIP($ip);
        //mail('bruno@cameleons.com','client ip',$ip);

		if (!empty($ip)) {

            if(($ip=='127.0.0.1' or $ip=='::1') and class_exists('ShopFunctions')){
                $v = VmModel::getModel('vendor');
                $add = $v->getVendorAdressBT(1);
                if($add and !empty($add->virtuemart_country_id)){
                    if (!class_exists ('shopFunctions'))
                        require(VMPATH_SITE . DS . 'helpers' . DS . 'shopfunctions.php');
                    $country_2_code = shopFunctions::getCountryByID($add->virtuemart_country_id,'country_2_code');
                    return $country_2_code;
                }
            }

			$db = JFactory::getDBO();
			// ipnum = 16777216*w + 65536*x + 256*y + z
			// IP Address = w.x.y.z
			$arr = explode('.', $ip);
			if (count($arr)<4) return false;
			$ipl = (16777216*$arr[0])+(65536*$arr[1])+(256*$arr[2])+$arr[3];
			$ipl2 = ip2long($ip);
			//vmdebug('my ip',$ip,$ipl,$ipl2);
			$q = 'select country_2_code from #__geodata where longstart <= '.$ipl.' and longend >= '.$ipl.' limit 0,1';
			$db->setQuery($q);
			$res = $db->loadAssoc();
			vmdebug('getCountry2Code ',$q,$res);
			if (empty($res)) return false;
			return $res['country_2_code'];
		}
   		return false;
  }

  // returns EN name of the country

    static function getCountry($ip='') {

      $ip = self::getClientIP($ip);

        if (!empty($ip)){

            if(($ip=='127.0.0.1' or $ip=='::1') and class_exists('ShopFunctions')){
                $v = VmModel::getModel('vendor');
                $add = $v->getVendorAdressBT(1);
                if($add and !empty($add->virtuemart_country_id)){
                    if (!class_exists ('shopFunctions'))
                        require(VMPATH_SITE . DS . 'helpers' . DS . 'shopfunctions.php');
                    $country_2_code = shopFunctions::getCountryByID($add->virtuemart_country_id,'country_name');
                    return $country_2_code;
                }
            }
            $db = JFactory::getDBO();

            $arr = explode('.', $ip);
            if (count($arr)<4) return false;
            $ipl = (16777216*$arr[0])+(65536*$arr[1])+(256*$arr[2])+$arr[3];

            //echo $ipl;
            $q = 'select country_name from #__geodata where longstart <= '.$ipl.' and longend >= '.$ipl.' limit 0,1';
            //echo $q;
            $db->setQuery($q);
            $res = $db->loadAssoc();
            //$err = $db->getErrorMsg();
            //if (!empty($err)) echo $err;
            if (empty($res)) return false;

            return $res['country_name'];
        }

        return false;
    }
  
  // returns false on failture and array with geodata if found
  static function getGeoData($ip)
  {
      if (empty($ip))
    {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
    if (empty($ip) && (!empty($_SERVER['REMOTE_ADDR']))) 
    $ip = $_SERVER['REMOTE_ADDR'];
    }

    
    if (!empty($ip))
    {

     $db = JFactory::getDBO();
     $ipl = ip2long($ip); 
     //echo $ipl;
     $q = 'select * from #__geodata where longstart <= '.$ipl.' and longend >= '.$ipl.' limit 0,1'; 
     //echo $q;
     $db->setQuery($q); 
     $res = $db->loadAssoc(); 
     //$err = $db->getErrorMsg(); 
     //if (!empty($err)) echo $err;
     if (empty($res)) return false;

     return $res; 
    }
    
   return false;    
  }
}