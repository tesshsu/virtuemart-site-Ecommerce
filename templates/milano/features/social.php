<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/
//no direct accees
defined ('_JEXEC') or die('resticted aceess');

class Helix3FeatureSocial {

	private $helix3;
	public $position;

	public function __construct( $helix3 ){
		$this->helix3 = $helix3;
		$this->position = $this->helix3->getParam('social_position');
	}

	public function renderFeature() {

		$facebook 	= $this->helix3->getParam('facebook');
		$twitter  	= $this->helix3->getParam('twitter');
		$googleplus = $this->helix3->getParam('googleplus');
		$pinterest 	= $this->helix3->getParam('pinterest');
		$youtube 	= $this->helix3->getParam('youtube');
		$linkedin 	= $this->helix3->getParam('linkedin');
		$dribbble 	= $this->helix3->getParam('dribbble');
		$behance 	= $this->helix3->getParam('behance');
		$skype 		= $this->helix3->getParam('skype');
		$flickr 	= $this->helix3->getParam('flickr');
		$vk 		= $this->helix3->getParam('vk');

		if( $this->helix3->getParam('show_social_icons') && ( $facebook || $twitter || $googleplus || $pinterest || $youtube || $linkedin || $dribbble || $behance || $skype || $flickr || $vk ) ) {
			$html  = '<ul class="social-icons">';

			if( $facebook ) {
				$html .= '<li><a class="icon-facebook" data-toggle="tooltip" title="Facebook" target="_blank" href="'. $facebook .'"><i class="fa fa-facebook fa-2x"></i></a></li>';
			}
			if( $twitter ) {
				$html .= '<li><a class="icon-twitter" data-toggle="tooltip" title="Twitter" target="_blank" href="'. $twitter .'"><i class="fa fa-twitter fa-2x"></i></a></li>';
			}
			if( $googleplus ) {
				$html .= '<li><a class="icon-google-plus" data-toggle="tooltip" title="Google plus" target="_blank" href="'. $googleplus .'"><i class="fa fa-google-plus fa-2x"></i></a></li>';
			}
			if( $pinterest ) {
				$html .= '<li><a class="icon-pinterest" data-toggle="tooltip" title="Pinterest" target="_blank" href="'. $pinterest .'"><i class="fa fa-pinterest fa-2x"></i></a></li>';
			}
			if( $youtube ) {
				$html .= '<li><a class="icon-linkedin" data-toggle="tooltip" title="Youtube" target="_blank" href="'. $youtube .'"><i class="fa fa-youtube fa-2x"></i></a></li>';
			}
			if( $linkedin ) {
				$html .= '<li><a class="icon-dribbble" data-toggle="tooltip" title="Dribbble" target="_blank" href="'. $linkedin .'"><i class="fa fa-linkedin fa-2x"></i></a></li>';
			}
			if( $dribbble ) {
				$html .= '<li><a class="icon-behance" data-toggle="tooltip" title="Behance" target="_blank" href="'. $dribbble .'"><i class="fa fa-dribbble fa-2x"></i></a></li>';
			}
			if( $behance ) {
				$html .= '<li><a class="icon-youtube" data-toggle="tooltip" title="Instagram" target="_blank" href="'. $behance .'"><i class="fa fa-instagram fa-2x"></i></a></li>';
			}
			if( $flickr ) {
				$html .= '<li><a class="icon-flickr" data-toggle="tooltip" title="Flickr" target="_blank" href="'. $flickr .'"><i class="fa fa-flickr fa-2x"></i></a></li>';
			}
			if( $vk ) {
				$html .= '<li><a class="icon-skype" data-toggle="tooltip" title="Skype" target="_blank" href="'. $vk .'"><i class="fa fa-vk fa-2x"></i></a></li>';
			}
			if( $skype ) {
				$html .= '<li><a class="icon-vk" data-toggle="tooltip" title="VK" href="skype:'. $skype .'?chat"><i class="fa fa-skype fa-2x"></i></a></li>';
			}

			$html .= '</ul>';

			return $html;
		}

	}
}