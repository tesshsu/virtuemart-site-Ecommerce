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

use JFactory;
use RegularLabs\Library\RegEx as RL_RegEx;

class Document
{
	public static function placeScriptsAndStyles(&$head, &$body)
	{
		if (
			strpos($head, '</head>') === false
			&& strpos($body, '<!-- CA HEAD START') === false
		)
		{
			return;
		}

		RL_RegEx::matchAll('<!-- CA HEAD START STYLES -->(.*?)<!-- CA HEAD END STYLES -->', $body, $matches);

		if (!empty($matches))
		{
			$styles = '';
			foreach ($matches as $match)
			{
				$styles .= $match['1'];

				$body = str_replace($match['0'], '', $body);
			}

			$add_before = '</head>';
			if (RL_RegEx::match('<link [^>]+templates/', $body, $add_before_match))
			{
				$add_before = $add_before_match['0'];
			}

			$head = str_replace($add_before, $styles . $add_before, $head);
		}

		RL_RegEx::matchAll('<!-- CA HEAD START SCRIPTS -->(.*?)<!-- CA HEAD END SCRIPTS -->', $body, $matches, null, PREG_SET_ORDER);

		if (!empty($matches))
		{
			$scripts = '';
			foreach ($matches as $match)
			{
				$scripts .= $match['1'];

				$body = str_replace($match['0'], '', $body);
			}

			$add_before = '</head>';
			if (RL_RegEx::match('<script [^>]+templates/', $body, $add_before_match))
			{
				$add_before = $add_before_match['0'];
			}

			$head = str_replace($add_before, $scripts . $add_before, $head);
		}

		self::removeDuplicatesFromHead($head, '#<link[^>]*>#');
		self::removeDuplicatesFromHead($head, '#<style.*?</style>#');
		self::removeDuplicatesFromHead($head, '#<script.*?</script>#');
	}

	public static function addScriptsAndStyles(&$data, $area = '')
	{
		// add set scripts and styles to current jdoc
		$doc = JFactory::getDocument();
		self::removeDuplicatesFromObject($data->styles, $doc->_styleSheets);
		self::removeDuplicatesFromObject($data->style, $doc->_style, 1);
		self::removeDuplicatesFromObject($data->scripts, $doc->_scripts);
		self::removeDuplicatesFromObject($data->script, $doc->_script, 1);

		if ($area == 'articles')
		{
			foreach ($data->styles as $style => $attr)
			{
				$doc->addStyleSheet($style, $attr->mime, $attr->media, $attr->attribs);
			}
			foreach ($data->style as $type => $content)
			{
				$doc->addStyleDeclaration($content, $type);
			}
			foreach ($data->scripts as $script => $attr)
			{
				$doc->addScript($script, $attr->mime, $attr->defer, $attr->async);
			}
			foreach ($data->script as $type => $content)
			{
				$doc->addScriptDeclaration($content, str_replace('javascript2', 'javascript', $type));
			}
			foreach ($data->custom as $content)
			{
				$doc->addCustomTag($content);
			}

			return;
		}

		$inline_head_styles  = [];
		$inline_head_scripts = [];

		// Generate stylesheet links
		foreach ($data->styles as $style => $attr)
		{
			$inline_head_styles[] = self::styleToString($style, $attr) . "\n";
		}

		// Generate stylesheet declarations
		foreach ($data->style as $type => $content)
		{
			$inline_head_styles[] = '<style type="' . $type . '">' . "\n"
				. $content . "\n"
				. $inline_head[] = '</style>' . "\n";
		}

		// Generate script file links
		foreach ($data->scripts as $script => $attr)
		{
			$inline_head_scripts[] = self::scriptToString($script, $attr) . "\n";
		}

		// Generate script declarations
		foreach ($data->script as $type => $content)
		{
			$inline_head_scripts[] = '<script type="' . str_replace('javascript2', 'javascript', $type) . '">' . "\n"
				. $content . "\n"
				. '</script>' . "\n";
		}

		$inline_head_scripts[] = is_array($data->custom)
			? implode("\n", $data->custom)
			: (string) $data->custom;

		if (!empty($inline_head_styles))
		{
			$data->html = '<!-- CA HEAD START STYLES -->' . implode('', $inline_head_styles) . '<!-- CA HEAD END STYLES -->' . $data->html;
		}

		if (!empty($inline_head_scripts))
		{
			$data->html = '<!-- CA HEAD START SCRIPTS -->' . implode('', $inline_head_scripts) . '<!-- CA HEAD END SCRIPTS -->' . $data->html;
		}
	}

	private static function styleToString($style, $attr)
	{
		$string = '<link rel="stylesheet" href="' . $style . '" type="' . $attr->mime . '"';

		$string .= !is_null($attr->media) ? ' media="' . $attr->media . '"' : '';
		$string = trim($string . ' ' . JArrayHelper::toString($attr->attribs));

		$string .= '>';

		return $string;
	}

	private static function scriptToString($script, $attr)
	{
		$string = '<script src="' . $script . '"';

		$string .= !is_null($attr->mime) ? ' type="' . $attr->mime . '"' : '';
		$string .= $attr->defer ? ' defer="defer"' : '';
		$string .= $attr->async ? ' async="async"' : '';

		$string .= '></script>';

		return $string;
	}

	private static function removeDuplicatesFromObject(&$obj, $doc, $match_value = 0)
	{
		foreach ($obj as $key => $val)
		{
			if (isset($doc[$key]) && (!$match_value || $doc[$key] == $val))
			{
				unset($obj->{$key});
			}
		}
	}

	private static function removeDuplicatesFromHead(&$head, $regex = '')
	{
		RL_RegEx::matchAll($regex, $head, $matches, null, PREG_PATTERN_ORDER);

		if (empty($matches))
		{
			return;
		}

		$tags = [];

		foreach ($matches['0'] as $tag)
		{
			if (!in_array($tag, $tags))
			{
				$tags[] = $tag;
				continue;
			}

			$tag  = RL_RegEx::quote($tag);
			$head = RL_RegEx::replace('(' . $tag . '.*?)\s*' . $tag, '\1', $head);
		}
	}
}
