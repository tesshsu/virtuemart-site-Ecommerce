<?php
/**
 * @package         CDN for Joomla!
 * @version         6.0.2PRO
 *
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2017 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\CDNforJoomla;

defined('_JEXEC') or die;

use RegularLabs\Library\Protect as RL_Protect;
use RegularLabs\Library\RegEx as RL_RegEx;

class Replace
{
	static $set = null;

	public static function replace(&$string)
	{
		if (is_array($string))
		{
			self::replaceInList($string);

			return;
		}

		if (!is_string($string) || $string == '')
		{
			return;
		}

		$sets = Params::getSets();

		if (empty($sets))
		{
			return;
		}

		if(!empty($_GET['x'])){
	echo "\n\n<pre>==========================\n";
	print_r($string);
	echo "\n==========================</pre>\n\n";
	exit;
}

		Protect::_($string);

		foreach ($sets as $set)
		{

			self::replaceBySet($string, $set);
		}

		RL_Protect::unprotect($string);
	}

	private static function replaceInList(&$array)
	{
		foreach ($array as &$val)
		{
			self::replace($val);
		}
	}

	private static function replaceBySet(&$string, $set)
	{
		self::$set = $set;

		self::replaceBySearchList($string, self::$set->searches);

		if (!empty(self::$set->enable_in_scripts) && strpos($string, '<script') !== false)
		{
			self::replaceInJavascript($string);
		}
	}

	private static function replaceInJavascript(&$string)
	{
		$regex = '<script(?:\s+(language|type)\s*=[^>]*)?>.*?</script>';

		RL_RegEx::matchAll($regex, $string, $parts);

		if (empty($parts))
		{
			return;
		}

		foreach ($parts as $part)
		{
			self::replaceInJavascriptStringPart($string, $part);
		}
	}

	private static function replaceInJavascriptStringPart(&$string, $part)
	{
		$new_string = $part['0'];

		if (!self::replaceBySearchList($new_string, self::$set->js_searches))
		{
			return;
		}

		$string = str_replace($part['0'], $new_string, $string);
	}

	private static function replaceBySearchList(&$string, &$searches)
	{
		$changed = 0;

		foreach ($searches as $word => $search)
		{
			if (!is_numeric($word) && strpos($string, $word) == false)
			{
				continue;
			}

			$changed = self::replaceBySearch($string, $search);
		}

		return $changed;
	}

	private static function replaceBySearch(&$string, &$search)
	{
		RL_RegEx::matchAll($search, $string, $matches);

		if (empty($matches))
		{
			return false;
		}

		$changed = false;

		foreach ($matches as $match)
		{
			list($file, $query) = self::getFileParts($match['3']);

			if (!$file || self::fileIsIgnored($file))
			{
				continue;
			}

			if (self::$set->enable_versioning
				&& self::includeVersioningFile($file)
				&& file_exists(JPATH_SITE . '/' . $file)
			)
			{
				$query[] = filemtime(JPATH_SITE . '/' . $file);
			}

			$file = self::getCdnUrl($file)
				. '/' . self::addQueryToFile($file, $query);

			$string = str_replace(
				$match['0'],
				$match['1'] . $file . $match['4'],
				$string
			);

			$changed = true;
		}

		return $changed;
	}

	private static function includeVersioningFile($file)
	{
		foreach (self::$set->versioning_filetypes as $filetype)
		{
			if (substr($file, -strlen($filetype)) == $filetype)
			{
				return true;
			}
		}

		return false;
	}

	private static function getFileParts($file)
	{
		$file = trim($file);

		if (!$file)
		{
			return [null, null];
		}

		if (strpos($file, '?') === false)
		{
			return [$file, null];
		}

		list($file, $query) = explode('?', $file, 2);
		$query = explode('&', $query);

		return [$file, $query];
	}

	private static function addQueryToFile($file, $query = [])
	{
		$file = trim($file);

		if (empty($query))
		{
			return $file;
		}

		return $file . '?' . implode('&', $query);
	}

	private static function fileIsIgnored($file)
	{
		foreach (self::$set->ignorefiles as $ignore)
		{
			if ($ignore && (strpos($file, $ignore) !== false || strpos(htmlentities($file), $ignore) !== false))
			{
				return true;
			}
		}

		return false;
	}

	private static function getCdnUrl($file)
	{
		$cdns = self::$set->cdns;

		if (count($cdns) > 1)
		{
			// Make sure a file is always served from the same cdn server to leverage browser caching
			$cdns = array($cdns[hexdec(substr(hash('md2', $file), -4)) % count($cdns)]);
		}

		return self::$set->protocol . $cdns['0'];
	}
}
