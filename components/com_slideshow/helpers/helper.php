<?php 
/**
 * @package Slideshow
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 **/
defined('_JEXEC') or die('Restircted access');

class SlideshowsHelper {
    private function add_scripts() {
        $document = JFactory::getDocument();
        $document->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js");
    }

    private function get_data() {
        $db = JFactory::getDBO();
        $id = $this->slideshow_id;
        $query = $db->getQuery(true);
        $query->select('*,#__huge_itslideshow_images.name as imgname,#__huge_itslideshow_slideshows.description as pousetimeDescription');
        $query->from('#__huge_itslideshow_slideshows,#__huge_itslideshow_images');
        $query->where('#__huge_itslideshow_slideshows.id =' . $id)->where('#__huge_itslideshow_slideshows.id = #__huge_itslideshow_images.slideshow_id');
        $query->order('#__huge_itslideshow_images.ordering desc');
        $db->setQuery($query);
        $this->_data = $db->loadObjectList();
    }

    private function get_dataParams() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itslideshow_params');
        $db->setQuery($query);
        $this->options_params = $db->loadObjectList();
    }
    function get_youtube_id_from_url($url){
   if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
           return $match[1];
   }
}

    public function render_html() {
        
        ob_start();
        if ($this->type != 'plugin')
        $this->add_scripts();
        $this->get_data();
        $this->get_dataParams();

        $cis_options = array();
        $paramssld = array();
        foreach ($this->options_params as $rowpar) {
            $key = $rowpar->name;
            $value = $rowpar->value;
            $paramssld[$key] = $value;
        }
        for ($i = 0, $n = count($this->_data); $i < $n; $i++) {
            $cis_options[$this->_data[$i]->id][] = $this->_data[$i];
        }
        if (sizeof($cis_options) > 0) {
            reset($cis_options);
            $first_key = key($cis_options);
            $cis_options_value = $cis_options[$first_key][0];
            $images = $this->_data;            
            $slideshowID = $cis_options_value->id;
            $slideshowtitle = $cis_options_value->name;
            $slideshowheight = $cis_options_value->sl_height;
            $slideshowwidth = $cis_options_value->sl_width;
            $slideshoweffect = $cis_options_value->slideshow_list_effects_s;
            $slidepausetime = ($cis_options_value->pousetimeDescription + $cis_options_value->param);
            $slideshowpauseonhover = $cis_options_value->pause_on_hover;
            $slideshowposition = $cis_options_value->sl_position;
            $slidechangespeed = $cis_options_value->param;
            $slideshow_title_position = explode('-', trim($paramssld['slideshow_title_position']));
            $slideshow_description_position = explode('-', trim($paramssld['slideshow_description_position']));
            $hasyoutube = false;
            $hasvimeo = false;
            foreach ($images as $key => $image_row) {
                if (strpos($image_row->image_url, 'youtube') !== false) {
                    $hasyoutube = true;
                }
                if (strpos($image_row->image_url, 'vimeo') !== false) {
                    $hasvimeo = true;
                }
            }
            ?>
        <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>-->
            <?php if ($hasvimeo == true) { ?>
                <script src="<?php echo JURI::root() . 'media/com_slideshow/js/vimeo.lib.js' ?>"></script>
                <script>
                    jQuery(function () {
                        var vimeoPlayer = document.querySelector('iframe');

                        var volumes = [];
                        var colors = [];
                        var i = 0;
                <?php
                $i = 0;
                //$vimeoparams=array_reverse($images);
                foreach ($images as $key => $image_row) {
                    if ($image_row->sl_type == "video" and strpos($image_row->image_url, 'vimeo') !== false) {
                        ?>
                                volumes[<?php echo $i; ?>] = '<?php echo intval($image_row->description) / 100; ?>';
                                colors[<?php echo $i; ?>] = '<?php echo $image_row->name; ?>';
                        <?php $i++;
                    }
                } ?>
                        jQuery('iframe').each(function () {
                            Froogaloop(this).addEvent('ready', ready);
                        });
                        jQuery(".sidedock,.controls").remove();
                        function ready(player_id) {

                            froogaloop = $f(player_id);

                            function setupEventListeners() {
                                function setVideoVolume(player_id, value) {
                                    Froogaloop(player_id).api('setVolume', value);
                                }
                                function setVideoColor(player_id, value) {
                                    Froogaloop(player_id).api('setColor', value);
                                }
                                function onPlay() {
                                    froogaloop.addEvent('play',
                                            function () {
                                                video_is_playing_<?php echo $slideshowID; ?> = true;
                                            });
                                }
                                function onPause() {
                                    froogaloop.addEvent('pause',
                                            function () {
                                                video_is_playing_<?php echo $slideshowID; ?> = false;
                                            });
                                }
                                function stopVimeoVideo(player) {
                                    Froogaloop(player).api('pause');
                                }

                                setVideoVolume(player_id, volumes[i]);
                                setVideoColor(player_id, colors[i]);
                                i++;

                                onPlay();
                                onPause();
                                jQuery('#huge_it_slideshow_left_<?php echo $slideshowID; ?>, #huge_it_slideshow_right_<?php echo $slideshowID; ?>,.huge_it_slideshow_dots_<?php echo $slideshowID; ?>').click(function () {
                                    stopVimeoVideo(player_id);
                                });
                            }
                            setupEventListeners();
                        }
                    });
                </script>
            <?php } ?>
            <?php if ($hasyoutube == true) { ?>
                <script src="<?php echo JURI::root() . 'media/com_slideshow/js/youtube.lib.js' ?>"></script>
                <script>
                <?php
       
                $i = 0;
                foreach ($images as $key => $image_row) {
                    if ($image_row->sl_type == "video" and strpos($image_row->image_url, 'youtube') !== false) {
                        ?>
                            var player_<?php echo $image_row->id; ?>;
                        <?php } else if (strpos($image_row->image_url, 'vimeo') !== false) {
                        ?>

                        <?php
                    } else {
                        continue;
                    }
                    $i++;
                }
                ?>
                    video_is_playing_<?php echo $slideshowID; ?> = false;
                    function onYouTubeIframeAPIReady() {
                <?php foreach ($images as $key => $image_row) { ?>

                    <?php if ($image_row->sl_type == "video" and strpos($image_row->image_url, 'youtube') !== false) {
                        ?>
                                player_<?php echo $image_row->id; ?> = new YT.Player('video_id_<?php echo $slideshowID; ?>_<?php echo $key; ?>', {
                                    height: '<?php echo $slideshowheight; ?>',
                                    width: '<?php echo $slideshowwidth; ?>',
                                    videoId: '<?php echo $this->get_youtube_id_from_url($image_row->image_url); ?>',
                                    playerVars: {
                                        'controls': <?php if ($images[$key]->sl_url == "on") {
                            echo 1;
                        } else {
                            echo 0;
                        } ?>,
                                        'showinfo': <?php if ($images[$key]->link_target == "on") {
                            echo 1;
                        } else {
                            echo 0;
                        } ?>
                                    },
                                    events: {
                                        'onReady': onPlayerReady_<?php echo $image_row->id; ?>,
                                        'onStateChange': onPlayerStateChange_<?php echo $image_row->id; ?>,
                                        'loop': 1
                                    }
                                });
                        <?php
                    } else {
                        continue;
                    }
                }
                ?>
                    }
                <?php
                foreach ($images as $key => $image_row) {
                    if ($image_row->sl_type == "video" and strpos($image_row->image_url, 'youtube') !== false) {
                        ?>
                            function onPlayerReady_<?php echo $image_row->id; ?>(event) {
                                player_<?php echo $image_row->id; ?>.setVolume(<?php echo $images[$key]->description; ?>);
                            }

                            function onPlayerStateChange_<?php echo $image_row->id; ?>(event) {
                                //(event.data);
                                if (event.data == YT.PlayerState.PLAYING) {
                                    event.target.setPlaybackQuality('<?php echo $images[$key]->name; ?>');
                                    video_is_playing_<?php echo $slideshowID; ?> = true;
                                }
                                else {
                                    video_is_playing_<?php echo $slideshowID; ?> = false;
                                }
                            }
                        <?php
                    } else {
                        continue;
                    }
                }
                ?>
                    function stopYoutubeVideo() {
                <?php
                $i = 0;
                foreach ($images as $key => $image_row) {
                    if ($image_row->sl_type == "video" and strpos($image_row->image_url, 'youtube') !== false) {
                        ?>
                                player_<?php echo $image_row->id; ?>.pauseVideo();
                        <?php
                    } else {
                        continue;
                    }
                    $i++;
                }
                ?>
                    }

                </script>
            <?php } ?>
            <script>
                var data_<?php echo $slideshowID; ?> = [];
                var event_stack_<?php echo $slideshowID; ?> = [];
                video_is_playing_<?php echo $slideshowID; ?> = false;
            <?php
            //	$images=array_reverse($images);
//		$recent_posts = wp_get_recent_posts( $args, ARRAY_A );

            $i = 0;

            foreach ($images as $image) {
                $imagerowstype = $image->sl_type;
                if ($image->sl_type == '') {
                    $imagerowstype = 'image';
                }
                switch ($imagerowstype) {

                    case 'image':
                        echo 'data_' . $slideshowID . '["' . $i . '"]=[];';
                        echo 'data_' . $slideshowID . '["' . $i . '"]["id"]="' . $i . '";';
                        echo 'data_' . $slideshowID . '["' . $i . '"]["image_url"]="' . $image->image_url . '";';
                        $strdesription = str_replace('"', "'", $image->description);
                        $strdesription = preg_replace("/\r|\n/", " ", $strdesription);
                        echo 'data_' . $slideshowID . '["' . $i . '"]["description"]="' . $strdesription . '";';
                        $stralt = str_replace('"', "'", $image->name);
                        $stralt = preg_replace("/\r|\n/", " ", $stralt);
                        echo 'data_' . $slideshowID . '["' . $i . '"]["alt"]="' . $stralt . '";';
                        $i++;
                        break;
                    case 'video':
                        echo 'data_' . $slideshowID . '["' . $i . '"]=[];';
                        echo 'data_' . $slideshowID . '["' . $i . '"]["id"]="' . $i . '";';
                        echo 'data_' . $slideshowID . '["' . $i . '"]["image_url"]="' . $image->image_url . '";';
                        $strdesription = str_replace('"', "'", $image->description);
                        $strdesription = preg_replace("/\r|\n/", " ", $strdesription);
                        echo 'data_' . $slideshowID . '["' . $i . '"]["description"]="' . $strdesription . '";';
                        $stralt = str_replace('"', "'", $image->name);
                        $stralt = preg_replace("/\r|\n/", " ", $stralt);
                        echo 'data_' . $slideshowID . '["' . $i . '"]["alt"]="' . $stralt . '";';
                        $i++;
                        break;
                    case 'last_posts':
                        foreach ($recent_posts as $keyl => $recentimage) {
                            if (get_the_post_thumbnail($recentimage["ID"], 'thumbnail') != '') {
                                if ($keyl < $image->sl_url) {
                                    echo 'data_' . $slideshowID . '["' . $i . '"]=[];';
                                    echo 'data_' . $slideshowID . '["' . $i . '"]["id"]="' . $i . '";';
                                    echo 'data_' . $slideshowID . '["' . $i . '"]["image_url"]="' . $recentimage['guid'] . '";';
                                    $strdesription = str_replace('"', "'", $recentimage['post_content']);
                                    $strdesription = preg_replace("/\r|\n/", " ", $strdesription);
                                    $strdesription = substr_replace($strdesription, "", $image->description);
                                    echo 'data_' . $slideshowID . '["' . $i . '"]["description"]="' . $strdesription . '";';
                                    $stralt = str_replace('"', "'", $recentimage['post_title']);
                                    $stralt = preg_replace("/\r|\n/", " ", $stralt);
                                    echo 'data_' . $slideshowID . '["' . $i . '"]["alt"]="' . $stralt . '";';
                                    $i++;
                                }
                            }
                        }

                        break;
                }
            }
            ?>
                var huge_it_trans_in_progress_<?php echo $slideshowID; ?> = false;
                var huge_it_transition_duration_<?php echo $slideshowID; ?> = <?php echo $slidechangespeed; ?>;
                var huge_it_playInterval_<?php echo $slideshowID; ?>;
                // Stop autoplay.
                window.clearInterval(huge_it_playInterval_<?php echo $slideshowID; ?>);

                var huge_it_current_key_<?php echo $slideshowID; ?> = '<?php echo (isset($current_key) ? $current_key : ''); ?>';
                function huge_it_move_dots_<?php echo $slideshowID; ?>() {
                    var image_left = jQuery(".huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>").position().left;
                    var image_right = jQuery(".huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>").position().left + jQuery(".huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>").outerWidth(true);
                }
                function huge_it_testBrowser_cssTransitions_<?php echo $slideshowID; ?>() {
                    return huge_it_testDom_<?php echo $slideshowID; ?>('Transition');
                }
                function huge_it_testBrowser_cssTransforms3d_<?php echo $slideshowID; ?>() {
                    return huge_it_testDom_<?php echo $slideshowID; ?>('Perspective');
                }
                function huge_it_testDom_<?php echo $slideshowID; ?>(prop) {
                    // Browser vendor CSS prefixes.
                    var browserVendors = ['', '-webkit-', '-moz-', '-ms-', '-o-', '-khtml-'];
                    // Browser vendor DOM prefixes.
                    var domPrefixes = ['', 'Webkit', 'Moz', 'ms', 'O', 'Khtml'];
                    var i = domPrefixes.length;
                    while (i--) {
                        if (typeof document.body.style[domPrefixes[i] + prop] !== 'undefined') {
                            return true;
                        }
                    }
                    return false;
                }
                function huge_it_cube_<?php echo $slideshowID; ?>(tz, ntx, nty, nrx, nry, wrx, wry, current_image_class, next_image_class, direction) {
                    /* If browser does not support 3d transforms/CSS transitions.*/
                    if (!huge_it_testBrowser_cssTransitions_<?php echo $slideshowID; ?>()) {
                        jQuery(".huge_it_slideshow_dots_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>");
                        jQuery("#huge_it_dots_" + huge_it_current_key_<?php echo $slideshowID; ?> + "_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>");
                        return huge_it_fallback_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction);
                    }
                    if (!huge_it_testBrowser_cssTransforms3d_<?php echo $slideshowID; ?>()) {
                        return huge_it_fallback3d_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction);
                    }
                    huge_it_trans_in_progress_<?php echo $slideshowID; ?> = true;
                    /* Set active thumbnail.*/
                    jQuery(".huge_it_slideshow_dots_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>");
                    jQuery("#huge_it_dots_" + huge_it_current_key_<?php echo $slideshowID; ?> + "_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>");
                    jQuery(".huge_it_slide_bg_<?php echo $slideshowID; ?>").css('perspective', 1000);
                    jQuery(current_image_class).css({
                        transform: 'translateZ(' + tz + 'px)',
                        backfaceVisibility: 'hidden'
                    });

                    jQuery(".huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?>,.huge_it_slide_bg_<?php echo $slideshowID; ?>,.huge_it_slideshow_image_item_<?php echo $slideshowID; ?>,.huge_it_slideshow_image_second_item_<?php echo $slideshowID; ?> ").css('overflow', 'visible');

                    jQuery(next_image_class).css({
                        opacity: 1,
                        filter: 'Alpha(opacity=100)',
                        backfaceVisibility: 'hidden',
                        transform: 'translateY(' + nty + 'px) translateX(' + ntx + 'px) rotateY(' + nry + 'deg) rotateX(' + nrx + 'deg)'
                    });
                    jQuery(".huge_it_slideshow_<?php echo $slideshowID; ?>").css({
                        transform: 'translateZ(-' + tz + 'px)',
                        transformStyle: 'preserve-3d'
                    });
                    /* Execution steps.*/
                    setTimeout(function () {
                        jQuery(".huge_it_slideshow_<?php echo $slideshowID; ?>").css({
                            transition: 'all ' + huge_it_transition_duration_<?php echo $slideshowID; ?> + 'ms ease-in-out',
                            transform: 'translateZ(-' + tz + 'px) rotateX(' + wrx + 'deg) rotateY(' + wry + 'deg)'
                        });
                    }, 20);
                    /* After transition.*/
                    jQuery(".huge_it_slideshow_<?php echo $slideshowID; ?>").one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(huge_it_after_trans));
                    function huge_it_after_trans() {
                        /*if (huge_it_from_focus_<?php echo $slideshowID; ?>) {
                         huge_it_from_focus_<?php echo $slideshowID; ?> = false;
                         return;
                         }*/
                        jQuery(".huge_it_slide_bg_<?php echo $slideshowID; ?>,.huge_it_slideshow_image_item_<?php echo $slideshowID; ?>,.huge_it_slideshow_image_second_item_<?php echo $slideshowID; ?> ").css('overflow', 'hidden');
                        jQuery(".huge_it_slide_bg_<?php echo $slideshowID; ?>").removeAttr('style');
                        jQuery(current_image_class).removeAttr('style');
                        jQuery(next_image_class).removeAttr('style');
                        jQuery(".huge_it_slideshow_<?php echo $slideshowID; ?>").removeAttr('style');
                        jQuery(current_image_class).css({'opacity': 0, filter: 'Alpha(opacity=0)', 'z-index': 1});
                        jQuery(next_image_class).css({'opacity': 1, filter: 'Alpha(opacity=100)', 'z-index': 2});
                        // huge_it_change_watermark_container_<?php echo $slideshowID; ?>();
                        huge_it_trans_in_progress_<?php echo $slideshowID; ?> = false;
                        if (typeof event_stack_<?php echo $slideshowID; ?> !== 'undefined' && event_stack_<?php echo $slideshowID; ?>.length > 0) {
                            key = event_stack_<?php echo $slideshowID; ?>[0].split("-");
                            event_stack_<?php echo $slideshowID; ?>.shift();
                            huge_it_change_image_<?php echo $slideshowID; ?>(key[0], key[1], data_<?php echo $slideshowID; ?>, true, false);
                        }
                    }
                }
                function huge_it_cubeH_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    /* Set to half of image width.*/
                    var dimension = jQuery(current_image_class).width() / 2;
                    if (direction == 'right') {
                        huge_it_cube_<?php echo $slideshowID; ?>(dimension, dimension, 0, 0, 90, 0, -90, current_image_class, next_image_class, direction);
                    }
                    else if (direction == 'left') {
                        huge_it_cube_<?php echo $slideshowID; ?>(dimension, -dimension, 0, 0, -90, 0, 90, current_image_class, next_image_class, direction);
                    }
                }
                function huge_it_cubeV_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    /* Set to half of image height.*/
                    var dimension = jQuery(current_image_class).height() / 2;
                    /* If next slide.*/
                    if (direction == 'right') {
                        huge_it_cube_<?php echo $slideshowID; ?>(dimension, 0, -dimension, 90, 0, -90, 0, current_image_class, next_image_class, direction);
                    }
                    else if (direction == 'left') {
                        huge_it_cube_<?php echo $slideshowID; ?>(dimension, 0, dimension, -90, 0, 90, 0, current_image_class, next_image_class, direction);
                    }
                }
                /* For browsers that does not support transitions.*/
                function huge_it_fallback_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    huge_it_fade_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction);
                }
                /* For browsers that support transitions, but not 3d transforms (only used if primary transition makes use of 3d-transforms).*/
                function huge_it_fallback3d_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    huge_it_sliceV_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction);
                }
                function huge_it_none_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    jQuery(current_image_class).css({'opacity': 0, 'z-index': 1});
                    jQuery(next_image_class).css({'opacity': 1, 'z-index': 2});

                    /* Set active thumbnail.*/
                    jQuery(".huge_it_slideshow_dots_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>");
                    jQuery("#huge_it_dots_" + huge_it_current_key_<?php echo $slideshowID; ?> + "_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>");
                }
                function huge_it_fade_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    if (huge_it_testBrowser_cssTransitions_<?php echo $slideshowID; ?>()) {
                        jQuery(next_image_class).css('transition', 'opacity ' + huge_it_transition_duration_<?php echo $slideshowID; ?> + 'ms linear');
                        jQuery(current_image_class).css('transition', 'opacity ' + huge_it_transition_duration_<?php echo $slideshowID; ?> + 'ms linear');
                        jQuery(current_image_class).css({'opacity': 0, 'z-index': 1});
                        jQuery(next_image_class).css({'opacity': 1, 'z-index': 2});
                    }
                    else {
                        jQuery(current_image_class).animate({'opacity': 0, 'z-index': 1}, huge_it_transition_duration_<?php echo $slideshowID; ?>);
                        jQuery(next_image_class).animate({
                            'opacity': 1,
                            'z-index': 2
                        }, {
                            duration: huge_it_transition_duration_<?php echo $slideshowID; ?>,
                            complete: function () {
                                return false;
                            }
                        });
                        // For IE.
                        jQuery(current_image_class).fadeTo(huge_it_transition_duration_<?php echo $slideshowID; ?>, 0);
                        jQuery(next_image_class).fadeTo(huge_it_transition_duration_<?php echo $slideshowID; ?>, 1);
                    }

                    jQuery(".huge_it_slideshow_dots_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>");
                    jQuery("#huge_it_dots_" + huge_it_current_key_<?php echo $slideshowID; ?> + "_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>");
                }
                function huge_it_grid_<?php echo $slideshowID; ?>(cols, rows, ro, tx, ty, sc, op, current_image_class, next_image_class, direction) {
                    /* If browser does not support CSS transitions.*/
                    if (!huge_it_testBrowser_cssTransitions_<?php echo $slideshowID; ?>()) {
                        jQuery(".huge_it_slideshow_dots_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>");
                        jQuery("#huge_it_dots_" + huge_it_current_key_<?php echo $slideshowID; ?> + "_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>");
                        return huge_it_fallback_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction);

                    }
                    huge_it_trans_in_progress_<?php echo $slideshowID; ?> = true;
                    /* Set active thumbnail.*/
                    jQuery(".huge_it_slideshow_dots_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>");
                    jQuery("#huge_it_dots_" + huge_it_current_key_<?php echo $slideshowID; ?> + "_<?php echo $slideshowID; ?>").removeClass("huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?>").addClass("huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>");
                    /* The time (in ms) added to/subtracted from the delay total for each new gridlet.*/
                    var count = (huge_it_transition_duration_<?php echo $slideshowID; ?>) / (cols + rows);
                    /* Gridlet creator (divisions of the image grid, positioned with background-images to replicate the look of an entire slide image when assembled)*/
                    function huge_it_gridlet(width, height, top, img_top, left, img_left, src, imgWidth, imgHeight, c, r) {
                        var delay = (c + r) * count;
                        /* Return a gridlet elem with styles for specific transition.*/
                        return jQuery('<div class="huge_it_gridlet_<?php echo $slideshowID; ?>" />').css({
                            width: width,
                            height: height,
                            top: top,
                            left: left,
                            backgroundImage: 'url("' + src + '")',
                            backgroundColor: jQuery(".huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?>").css("background-color"),
                            /*backgroundColor: rgba(0, 0, 0, 0),*/
                            backgroundRepeat: 'no-repeat',
                            backgroundPosition: img_left + 'px ' + img_top + 'px',
                            backgroundSize: imgWidth + 'px ' + imgHeight + 'px',
                            transition: 'all ' + huge_it_transition_duration_<?php echo $slideshowID; ?> + 'ms ease-in-out ' + delay + 'ms',
                            transform: 'none'
                        });
                    }
                    /* Get the current slide's image.*/
                    var cur_img = jQuery(current_image_class).find('img');
                    /* Create a grid to hold the gridlets.*/
                    var grid = jQuery('<div />').addClass('huge_it_grid_<?php echo $slideshowID; ?>');
                    /* Prepend the grid to the next slide (i.e. so it's above the slide image).*/
                    jQuery(current_image_class).prepend(grid);
                    /* vars to calculate positioning/size of gridlets*/
                    var cont = jQuery(".huge_it_slide_bg_<?php echo $slideshowID; ?>");
                    var imgWidth = cur_img.width();
                    var imgHeight = cur_img.height();
                    var contWidth = cont.width(),
                            contHeight = cont.height(),
                            imgSrc = cur_img.attr('src'), /*.replace('/thumb', ''),*/
                            colWidth = Math.floor(contWidth / cols),
                            rowHeight = Math.floor(contHeight / rows),
                            colRemainder = contWidth - (cols * colWidth),
                            colAdd = Math.ceil(colRemainder / cols),
                            rowRemainder = contHeight - (rows * rowHeight),
                            rowAdd = Math.ceil(rowRemainder / rows),
                            leftDist = 0,
                            img_leftDist = (jQuery(".huge_it_slide_bg_<?php echo $slideshowID; ?>").width() - cur_img.width()) / 2;
                    /* tx/ty args can be passed as 'auto'/'min-auto' (meaning use slide width/height or negative slide width/height).*/
                    tx = tx === 'auto' ? contWidth : tx;
                    tx = tx === 'min-auto' ? -contWidth : tx;
                    ty = ty === 'auto' ? contHeight : ty;
                    ty = ty === 'min-auto' ? -contHeight : ty;
                    /* Loop through cols*/
                    for (var i = 0; i < cols; i++) {
                        var topDist = 0,
                                img_topDst = (jQuery(".huge_it_slide_bg_<?php echo $slideshowID; ?>").height() - cur_img.height()) / 2,
                                newColWidth = colWidth;
                        if (colRemainder > 0) {
                            var add = colRemainder >= colAdd ? colAdd : colRemainder;
                            newColWidth += add;
                            colRemainder -= add;
                        }
                        for (var j = 0; j < rows; j++) {
                            var newRowHeight = rowHeight,
                                    newRowRemainder = rowRemainder;
                            /* If contHeight (px) does not divide cleanly into the specified number of rows, adjust individual row heights to create correct total.*/
                            if (newRowRemainder > 0) {
                                add = newRowRemainder >= rowAdd ? rowAdd : rowRemainder;
                                newRowHeight += add;
                                newRowRemainder -= add;
                            }
                            grid.append(huge_it_gridlet(newColWidth, newRowHeight, topDist, img_topDst, leftDist, img_leftDist, imgSrc, imgWidth, imgHeight, i, j));
                            topDist += newRowHeight;
                            img_topDst -= newRowHeight;
                        }
                        img_leftDist -= newColWidth;
                        leftDist += newColWidth;
                    }
                    /* Set event listener on last gridlet to finish transitioning.*/
                    var last_gridlet = grid.children().last();
                    /* Show grid & hide the image it replaces.*/
                    grid.show();
                    cur_img.css('opacity', 0);
                    /* Add identifying classes to corner gridlets (useful if applying border radius).*/
                    grid.children().first().addClass('rs-top-left');
                    grid.children().last().addClass('rs-bottom-right');
                    grid.children().eq(rows - 1).addClass('rs-bottom-left');
                    grid.children().eq(-rows).addClass('rs-top-right');
                    /* Execution steps.*/
                    setTimeout(function () {
                        grid.children().css({
                            opacity: op,
                            transform: 'rotate(' + ro + 'deg) translateX(' + tx + 'px) translateY(' + ty + 'px) scale(' + sc + ')'
                        });
                    }, 1);
                    jQuery(next_image_class).css('opacity', 1);
                    /* After transition.*/
                    jQuery(last_gridlet).one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(huge_it_after_trans));
                    function huge_it_after_trans() {
                        jQuery(current_image_class).css({'opacity': 0, 'z-index': 1});
                        jQuery(next_image_class).css({'opacity': 1, 'z-index': 2});
                        cur_img.css('opacity', 1);
                        grid.remove();
                        huge_it_trans_in_progress_<?php echo $slideshowID; ?> = false;
                        if (typeof event_stack_<?php echo $slideshowID; ?> !== 'undefined' && event_stack_<?php echo $slideshowID; ?>.length > 0) {
                            key = event_stack_<?php echo $slideshowID; ?>[0].split("-");
                            event_stack_<?php echo $slideshowID; ?>.shift();
                            huge_it_change_image_<?php echo $slideshowID; ?>(key[0], key[1], data_<?php echo $slideshowID; ?>, true, false);
                        }
                    }
                }
                function huge_it_sliceH_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    if (direction == 'right') {
                        var translateX = 'min-auto';
                    }
                    else if (direction == 'left') {
                        var translateX = 'auto';
                    }
                    huge_it_grid_<?php echo $slideshowID; ?>(1, 8, 0, translateX, 0, 1, 0, current_image_class, next_image_class, direction);
                }
                function huge_it_sliceV_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    if (direction == 'right') {
                        var translateY = 'min-auto';
                    }
                    else if (direction == 'left') {
                        var translateY = 'auto';
                    }
                    huge_it_grid_<?php echo $slideshowID; ?>(10, 1, 0, 0, translateY, 1, 0, current_image_class, next_image_class, direction);
                }
                function huge_it_slideV_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    if (direction == 'right') {
                        var translateY = 'auto';
                    }
                    else if (direction == 'left') {
                        var translateY = 'min-auto';
                    }
                    huge_it_grid_<?php echo $slideshowID; ?>(1, 1, 0, 0, translateY, 1, 1, current_image_class, next_image_class, direction);
                }
                function huge_it_slideH_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    if (direction == 'right') {
                        var translateX = 'min-auto';
                    }
                    else if (direction == 'left') {
                        var translateX = 'auto';
                    }
                    huge_it_grid_<?php echo $slideshowID; ?>(1, 1, 0, translateX, 0, 1, 1, current_image_class, next_image_class, direction);
                }
                function huge_it_scaleOut_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    huge_it_grid_<?php echo $slideshowID; ?>(1, 1, 0, 0, 0, 1.5, 0, current_image_class, next_image_class, direction);
                }
                function huge_it_scaleIn_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    huge_it_grid_<?php echo $slideshowID; ?>(1, 1, 0, 0, 0, 0.5, 0, current_image_class, next_image_class, direction);
                }
                function huge_it_blockScale_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    huge_it_grid_<?php echo $slideshowID; ?>(8, 6, 0, 0, 0, .6, 0, current_image_class, next_image_class, direction);
                }
                function huge_it_kaleidoscope_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    huge_it_grid_<?php echo $slideshowID; ?>(10, 8, 0, 0, 0, 1, 0, current_image_class, next_image_class, direction);
                }
                function huge_it_fan_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    if (direction == 'right') {
                        var rotate = 45;
                        var translateX = 100;
                    }
                    else if (direction == 'left') {
                        var rotate = -45;
                        var translateX = -100;
                    }
                    huge_it_grid_<?php echo $slideshowID; ?>(1, 10, rotate, translateX, 0, 1, 0, current_image_class, next_image_class, direction);
                }
                function huge_it_blindV_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    huge_it_grid_<?php echo $slideshowID; ?>(1, 8, 0, 0, 0, .7, 0, current_image_class, next_image_class);
                }
                function huge_it_blindH_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    huge_it_grid_<?php echo $slideshowID; ?>(10, 1, 0, 0, 0, .7, 0, current_image_class, next_image_class);
                }
                function huge_it_random_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction) {
                    var anims = ['sliceH', 'sliceV', 'slideH', 'slideV', 'scaleOut', 'scaleIn', 'blockScale', 'kaleidoscope', 'fan', 'blindH', 'blindV'];
                    /* Pick a random transition from the anims array.*/
                    this["huge_it_" + anims[Math.floor(Math.random() * anims.length)] + "_<?php echo $slideshowID; ?>"](current_image_class, next_image_class, direction);
                }

                function iterator_<?php echo $slideshowID; ?>() {
                    var iterator = 1;

                    return iterator;
                }

                function huge_it_change_image_<?php echo $slideshowID; ?>(current_key, key, data_<?php echo $slideshowID; ?>, from_effect, clicked) {

                    if (data_<?php echo $slideshowID; ?>[key]) {

                        if (video_is_playing_<?php echo $slideshowID; ?> && !clicked) {
                            return false;
                        }
                        if (!from_effect) {
                            jQuery("#huge_it_current_image_key_<?php echo $slideshowID; ?>").val(key);
                            current_key = jQuery(".huge_it_slideshow_dots_active_<?php echo $slideshowID; ?>").attr("image_key");
                        }
                        if (huge_it_trans_in_progress_<?php echo $slideshowID; ?>) {
                            event_stack_<?php echo $slideshowID; ?>.push(current_key + '-' + key);
                            return;
                        }

                        var direction = 'right';
                        if (huge_it_current_key_<?php echo $slideshowID; ?> > key) {
                            var direction = 'left';
                        }
                        else if (huge_it_current_key_<?php echo $slideshowID; ?> == key) {
                            return false;
                        }

                        huge_it_current_key_<?php echo $slideshowID; ?> = key;
                        jQuery("#huge_it_slideshow_image_<?php echo $slideshowID; ?>").attr('image_id', data_<?php echo $slideshowID; ?>[key]["id"]);
                        jQuery(".huge_it_slideshow_title_text_<?php echo $slideshowID; ?>").html(data_<?php echo $slideshowID; ?>[key]["alt"]);
                        jQuery(".huge_it_slideshow_description_text_<?php echo $slideshowID; ?>").html(data_<?php echo $slideshowID; ?>[key]["description"]);
                        var current_image_class = "#image_id_<?php echo $slideshowID; ?>_" + data_<?php echo $slideshowID; ?>[current_key]["id"];
                        var next_image_class = "#image_id_<?php echo $slideshowID; ?>_" + data_<?php echo $slideshowID; ?>[key]["id"];
                        if (jQuery(current_image_class).find('.huge_it_video_frame_<?php echo $slideshowID; ?>').length > 0) {
                            var streffect = '<?php echo $slideshoweffect; ?>';
                            if (streffect == "cubeV" || streffect == "cubeH" || streffect == "none" || streffect == "fade") {
                                huge_it_<?php echo $slideshoweffect; ?>_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction);
                            } else {
                                huge_it_fade_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction);
                            }
                        } else {
                            huge_it_<?php echo $slideshoweffect; ?>_<?php echo $slideshowID; ?>(current_image_class, next_image_class, direction);
                        }
                        jQuery('.huge_it_slideshow_title_text_<?php echo $slideshowID; ?>').removeClass('none');
                        if (jQuery('.huge_it_slideshow_title_text_<?php echo $slideshowID; ?>').html() == "") {
                            jQuery('.huge_it_slideshow_title_text_<?php echo $slideshowID; ?>').addClass('none');
                        }

                        jQuery('.huge_it_slideshow_description_text_<?php echo $slideshowID; ?>').removeClass('none');
                        if (jQuery('.huge_it_slideshow_description_text_<?php echo $slideshowID; ?>').html() == "") {
                            jQuery('.huge_it_slideshow_description_text_<?php echo $slideshowID; ?>').addClass('none');
                        }
                        jQuery(current_image_class).find('.huge_it_slideshow_title_text_<?php echo $slideshowID; ?>').addClass('none');
                        jQuery(current_image_class).find('.huge_it_slideshow_description_text_<?php echo $slideshowID; ?>').addClass('none');

                        //errorlogjQuery(".huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?>").after("--cur-key="+current_key+" --cur-img-class="+current_image_class+" nxt-img-class="+next_image_class+"--");
                        huge_it_move_dots_<?php echo $slideshowID; ?>();
            //			stopYoutubeVideo();
                        window.clearInterval(huge_it_playInterval_<?php echo $slideshowID; ?>);
                        play_<?php echo $slideshowID; ?>();
                    }

                }

                function huge_it_popup_resize_<?php echo $slideshowID; ?>() {

                    var staticslideshowwidth =<?php echo $slideshowwidth; ?>;
                    var slideshowwidth =<?php echo $slideshowwidth; ?>;

                    var bodyWidth = jQuery(window).width();
                    var parentWidth = jQuery(".huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?>").parent().width();
                    //if responsive js late responsive.js @  take body size and not parent div
                    if (slideshowwidth > parentWidth) {
                        slideshowwidth = parentWidth;
                    }
                    if (slideshowwidth > bodyWidth) {
                        slideshowwidth = bodyWidth;
                    }

                    var str = (<?php echo $slideshowheight; ?> / staticslideshowwidth);

                    jQuery(".huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?>").css({width: (slideshowwidth)});
                    jQuery(".huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?>").css({height: ((slideshowwidth) * str)});
                    jQuery(".huge_it_slideshow_image_container_<?php echo $slideshowID; ?>").css({width: (slideshowwidth)});
                    jQuery(".huge_it_slideshow_image_container_<?php echo $slideshowID; ?>").css({height: ((slideshowwidth) * str)});

                    if ("<?php echo $slideshow_title_position[1]; ?>" == "middle") {
                        var titlemargintopminus = jQuery(".huge_it_slideshow_title_text_<?php echo $slideshowID; ?>").outerHeight() / 2;
                    }
                    if ("<?php echo $slideshow_title_position[0]; ?>" == "center") {
                        var titlemarginleftminus = jQuery(".huge_it_slideshow_title_text_<?php echo $slideshowID; ?>").outerWidth() / 2;
                    }
                    jQuery(".huge_it_slideshow_title_text_<?php echo $slideshowID; ?>").css({cssText: "margin-top:-" + titlemargintopminus + "px; margin-left:-" + titlemarginleftminus + "px;"});

                    if ("<?php echo $slideshow_description_position[1]; ?>" == "middle") {
                        var descriptionmargintopminus = jQuery(".huge_it_slideshow_description_text_<?php echo $slideshowID; ?>").outerHeight() / 2;
                    }
                    if ("<?php echo $slideshow_description_position[0]; ?>" == "center") {
                        var descriptionmarginleftminus = jQuery(".huge_it_slideshow_description_text_<?php echo $slideshowID; ?>").outerWidth() / 2;
                    }
                    jQuery(".huge_it_slideshow_description_text_<?php echo $slideshowID; ?>").css({cssText: "margin-top:-" + descriptionmargintopminus + "px; margin-left:-" + descriptionmarginleftminus + "px;"});


                    if ("<?php echo $paramssld['slideshow_crop_image']; ?>" == "resize") {
                        jQuery(".huge_it_slideshow_image_<?php echo $slideshowID; ?>, .huge_it_slideshow_image_item1_<?php echo $slideshowID; ?> img, .huge_it_slideshow_image_container_<?php echo $slideshowID; ?> img").css({
                            cssText: "width:" + slideshowwidth + "px; height:" + ((slideshowwidth) * str) + "px;"
                        });
                    } else {
                        jQuery(".huge_it_slideshow_image_<?php echo $slideshowID; ?>,.huge_it_slideshow_image_item1_<?php echo $slideshowID; ?>,.huge_it_slideshow_image_item2_<?php echo $slideshowID; ?>").css({
                            cssText: "max-width: " + slideshowwidth + "px !important; max-height: " + (slideshowwidth * str) + "px !important;"
                        });
                    }

                    jQuery('.huge_it_video_frame_<?php echo $slideshowID; ?>').each(function (e) {
                        jQuery(this).width(slideshowwidth);
                        jQuery(this).height(slideshowwidth * str);
                    });
                }

                jQuery(window).load(function () {
                    jQuery(window).resize(function () {
                        huge_it_popup_resize_<?php echo $slideshowID; ?>();
                    });

                    huge_it_popup_resize_<?php echo $slideshowID; ?>();
                    /* Disable right click.*/
                    jQuery('div[id^="huge_it_container"]').bind("contextmenu", function () {
                        return false;
                    });

                    /*HOVER SLIDESHOW*/
                    jQuery("#huge_it_slideshow_image_container_<?php echo $slideshowID; ?>, .huge_it_slideshow_image_container_<?php echo $slideshowID; ?>, .huge_it_slideshow_dots_container_<?php echo $slideshowID; ?>,#huge_it_slideshow_right_<?php echo $slideshowID; ?>,#huge_it_slideshow_left_<?php echo $slideshowID; ?>").hover(function () {
                        //errorlogjQuery(".huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?>").after(" -- hover -- <br /> ");
                        jQuery("#huge_it_slideshow_right_<?php echo $slideshowID; ?>").css({'display': 'inline'});
                        jQuery("#huge_it_slideshow_left_<?php echo $slideshowID; ?>").css({'display': 'inline'});
                    }, function () {
                        jQuery("#huge_it_slideshow_right_<?php echo $slideshowID; ?>").css({'display': 'none'});
                        jQuery("#huge_it_slideshow_left_<?php echo $slideshowID; ?>").css({'display': 'none'});
                    });
                    var pausehover = "<?php echo $slideshowpauseonhover; ?>";
                    if (pausehover == "on") {
                        jQuery("#huge_it_slideshow_image_container_<?php echo $slideshowID; ?>, .huge_it_slideshow_image_container_<?php echo $slideshowID; ?>, .huge_it_slideshow_dots_container_<?php echo $slideshowID; ?>,#huge_it_slideshow_right_<?php echo $slideshowID; ?>,#huge_it_slideshow_left_<?php echo $slideshowID; ?>").hover(function () {
                            window.clearInterval(huge_it_playInterval_<?php echo $slideshowID; ?>);
                        }, function () {
                            window.clearInterval(huge_it_playInterval_<?php echo $slideshowID; ?>);
                            play_<?php echo $slideshowID; ?>();
                        });
                    }
                    play_<?php echo $slideshowID; ?>();
                });

                function play_<?php echo $slideshowID; ?>() {
                    /* Play.*/
                    //errorlogjQuery(".huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?>").after(" -- paly  ---- ");
                    huge_it_playInterval_<?php echo $slideshowID; ?> = setInterval(function () {
                        //errorlogjQuery(".huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?>").after(" -- time left ---- ");
                        var iterator = 1;
                        huge_it_change_image_<?php echo $slideshowID; ?>(parseInt(jQuery('#huge_it_current_image_key_<?php echo $slideshowID; ?>').val()), (parseInt(jQuery('#huge_it_current_image_key_<?php echo $slideshowID; ?>').val()) + iterator) % data_<?php echo $slideshowID; ?>.length, data_<?php echo $slideshowID; ?>, false, false);
                    }, '<?php echo $slidepausetime; ?>');
                }

                jQuery(window).focus(function () {
                    /*event_stack_<?php echo $slideshowID; ?> = [];*/
                    var i_<?php echo $slideshowID; ?> = 0;
                    jQuery(".huge_it_slideshow_<?php echo $slideshowID; ?>").children("div").each(function () {
                        if (jQuery(this).css('opacity') == 1) {
                            jQuery("#huge_it_current_image_key_<?php echo $slideshowID; ?>").val(i_<?php echo $slideshowID; ?>);
                        }
                        i_<?php echo $slideshowID; ?>++;
                    });
                });
                jQuery(window).blur(function () {
                    event_stack_<?php echo $slideshowID; ?> = [];
                    window.clearInterval(huge_it_playInterval_<?php echo $slideshowID; ?>);
                });
            </script>
            <style>				
                .huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?> {
                    height:<?php echo $slideshowheight; ?>px;
                    width:<?php echo $slideshowwidth; ?>px;
                    position:relative;
                    display: block;
                    text-align: center;
                    /*HEIGHT FROM HEADER.PHP*/
                    clear:both;
            <?php if ($slideshowposition == "left") {
                $position = 'float:left;';
            } elseif ($slideshowposition == "right") {
                $position = 'float:right;';
            } else {
                $position = 'float:none; margin:0px auto;';
            } ?>
            <?php echo $position; ?>

                    border-style:solid;
                    border-left:0px !important;
                    border-right:0px !important;
                }


                .huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?> * {
                    box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    -webkit-box-sizing: border-box;
                }


                .huge_it_slideshow_image_<?php echo $slideshowID; ?> {
                    /*width:100%;*/
                }

                #huge_it_slideshow_left_<?php echo $slideshowID; ?>,
                #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                    cursor: pointer;
                    display:none;
                    display: block;

                    height: 100%;
                    outline: medium none;
                    position: absolute;

                    /*z-index: 10130;*/
                    z-index: 13;
                    bottom:25px;
                    top:50%;		
                }


                #huge_it_slideshow_left-ico_<?php echo $slideshowID; ?>,
                #huge_it_slideshow_right-ico_<?php echo $slideshowID; ?> {
                    z-index: 13;
                    -moz-box-sizing: content-box;
                    box-sizing: content-box;
                    cursor: pointer;
                    display: table;
                    left: -9999px;
                    line-height: 0;
                    margin-top: -15px;
                    position: absolute;
                    top: 50%;
                    /*z-index: 10135;*/
                }
                #huge_it_slideshow_left-ico_<?php echo $slideshowID; ?>:hover,
                #huge_it_slideshow_right-ico_<?php echo $slideshowID; ?>:hover {
                    cursor: pointer;
                }

                .huge_it_slideshow_image_container_<?php echo $slideshowID; ?> {
                    display: table;
                    position: relative;
                    top:0px;
                    left:0px;
                    text-align: center;
                    vertical-align: middle;
                    width:100%;
                }	  

                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> {
                    text-decoration: none;
                    position: absolute;
                    z-index: 11;
                    display: inline-block;
            <?php
            if ($paramssld['slideshow_title_has_margin'] == 'on') {
                $slideshow_title_width = ($paramssld['slideshow_title_width'] - 6);
                $slideshow_title_height = ($paramssld['slideshow_title_height'] - 6);
                $slideshow_title_margin = "3";
            } else {
                $slideshow_title_width = ($paramssld['slideshow_title_width']);
                $slideshow_title_height = ($paramssld['slideshow_title_height']);
                $slideshow_title_margin = "0";
            }
            ?>

                    width:<?php echo $slideshow_title_width; ?>%;
                    /*height:<?php echo $slideshow_title_height; ?>%;*/

            <?php
            if ($slideshow_title_position[0] == "left") {
                echo 'left:' . $slideshow_title_margin . '%;';
            } elseif ($slideshow_title_position[0] == "center") {
                echo 'left:50%;';
            } elseif ($slideshow_title_position[0] == "right") {
                echo 'right:' . $slideshow_title_margin . '%;';
            }

            if ($slideshow_title_position[1] == "top") {
                echo 'top:' . $slideshow_title_margin . '%;';
            } elseif ($slideshow_title_position[1] == "middle") {
                echo 'top:50%;';
            } elseif ($slideshow_title_position[1] == "bottom") {
                echo 'bottom:' . $slideshow_title_margin . '%;';
            }
            ?>
                    padding:2%;
                    text-align:<?php echo $paramssld['slideshow_title_text_align']; ?>;  
                    font-weight:bold;
                    color:#<?php echo $paramssld['slideshow_title_color']; ?>;

                    background:<?php
                    list($r, $g, $b) = array_map('hexdec', str_split($paramssld['slideshow_title_background_color'], 2));
                    $titleopacity = $paramssld["slideshow_title_background_transparency"] / 100;
                    echo 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $titleopacity . ')  !important';
                    ?>;
                    border-style:solid;
                    font-size:<?php echo $paramssld['slideshow_title_font_size']; ?>px;
                    border-width:<?php echo $paramssld['slideshow_title_border_size']; ?>px;
                    border-color:#<?php echo $paramssld['slideshow_title_border_color']; ?>;
                    border-radius:<?php echo $paramssld['slideshow_title_border_radius']; ?>px;
                }

                .huge_it_slideshow_description_text_<?php echo $slideshowID; ?> {
                    text-decoration: none;
                    position: absolute;
                    z-index: 11;
                    border-style:solid;
                    display: inline-block;
            <?php
            if ($paramssld['slideshow_description_has_margin'] == 'on') {
                $slideshow_description_width = ($paramssld['slideshow_description_width'] - 6);
                $slideshow_description_height = ($paramssld['slideshow_description_height'] - 6);
                $slideshow_description_margin = "3";
            } else {
                $slideshow_description_width = ($paramssld['slideshow_description_width']);
                $slideshow_descriptione_height = ($paramssld['slideshow_description_height']);
                $slideshow_description_margin = "0";
            }
            ?>

                    width:<?php echo $slideshow_description_width; ?>%;
                    /*height:<?php echo $slideshow_description_height; ?>%;*/
            <?php
            if ($slideshow_description_position[0] == "left") {
                echo 'left:' . $slideshow_description_margin . '%;';
            } elseif ($slideshow_description_position[0] == "center") {
                echo 'left:50%;';
            } elseif ($slideshow_description_position[0] == "right") {
                echo 'right:' . $slideshow_description_margin . '%;';
            }

            if ($slideshow_description_position[1] == "top") {
                echo 'top:' . $slideshow_description_margin . '%;';
            } elseif ($slideshow_description_position[1] == "middle") {
                echo 'top:50%;';
            } elseif ($slideshow_description_position[1] == "bottom") {
                echo 'bottom:' . $slideshow_description_margin . '%;';
            }
            ?>
                    padding:3%;
                    text-align:<?php echo $paramssld['slideshow_description_text_align']; ?>;  
                    color:#<?php echo $paramssld['slideshow_description_color']; ?>;

                    background:<?php
            list($r, $g, $b) = array_map('hexdec', str_split($paramssld['slideshow_description_background_color'], 2));
            $descriptionopacity = $paramssld["slideshow_description_background_transparency"] / 100;
            echo 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $descriptionopacity . ') !important';
            ?>;
                    border-style:solid;
                    font-size:<?php echo $paramssld['slideshow_description_font_size']; ?>px;
                    border-width:<?php echo $paramssld['slideshow_description_border_size']; ?>px;
                    border-color:#<?php echo $paramssld['slideshow_description_border_color']; ?>;
                    border-radius:<?php echo $paramssld['slideshow_description_border_radius']; ?>px;
                }

                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?>.none, .huge_it_slideshow_description_text_<?php echo $slideshowID; ?>.none,
                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?>.hidden, .huge_it_slideshow_description_text_<?php echo $slideshowID; ?>.hidden	   {display:none;}

                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> h1, .huge_it_slideshow_description_text_<?php echo $slideshowID; ?> h1,
                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> h2, .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> h2,
                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> h3, .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> h3,
                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> h4, .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> h4,
                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> p, .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> p,
                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> strong,  .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> strong,
                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> span, .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> span,
                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> ul, .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> ul,
                .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> li, .huge_it_slideshow_title_text_<?php echo $slideshowID; ?> li {
                    padding:2px;
                    margin:0px;
                }

                .huge_it_slide_container_<?php echo $slideshowID; ?> {
                    display: table-cell;
                    margin: 0 auto;
                    position: relative;
                    vertical-align: middle;
                    width:100%;
                    height:100%;
                    _width: inherit;
                    _height: inherit;
                }
                .huge_it_slide_bg_<?php echo $slideshowID; ?> {
                    margin: 0 auto;
                    width:100%;
                    height:100%;
                    _width: inherit;
                    _height: inherit;
                }
                .huge_it_slideshow_<?php echo $slideshowID; ?> {
                    width:100%;
                    height:100%;
                    display:table;
                    padding:0px;
                    margin:0px;

                }
                .huge_it_slideshow_image_item_<?php echo $slideshowID; ?> {
                    width:100%;
                    height:100%;
                    width: inherit;
                    height: inherit;
                    display: table-cell;
                    filter: Alpha(opacity=100);
                    opacity: 1;
                    position: absolute;
                    top:0px;
                    left:0px;
                    vertical-align: middle;
                    z-index: 2;
                    margin:0px !important;
                    padding:0px;
                    overflow:hidden;
                    border-radius: <?php echo $paramssld['slideshow_slideshow_border_radius']; ?>px !important;
                }
                .huge_it_slideshow_image_second_item_<?php echo $slideshowID; ?> {
                    width:100%;
                    height:100%;
                    _width: inherit;
                    _height: inherit;
                    display: table-cell;
                    filter: Alpha(opacity=0);
                    opacity: 0;
                    position: absolute;
                    top:0px;
                    left:0px;
                    vertical-align: middle;
                    z-index: 1;
                    overflow:hidden;
                    margin:0px !important;
                    padding:0px;
                    border-radius: <?php echo $paramssld['slideshow_slideshow_border_radius']; ?>px !important;
                }
                .huge_it_grid_<?php echo $slideshowID; ?> {
                    display: none;
                    height: 100%;
                    overflow: hidden;
                    position: absolute;
                    width: 100%;
                }
                .huge_it_gridlet_<?php echo $slideshowID; ?> {
                    opacity: 1;
                    filter: Alpha(opacity=100);
                    position: absolute;
                }


                .huge_it_slideshow_dots_container_<?php echo $slideshowID; ?> {
                    display: table;
                    position: absolute;
                    width:100% !important;
                    height:100% !important;
                }
                .huge_it_slideshow_dots_thumbnails_<?php echo $slideshowID; ?> {
                    margin: 0 auto;
                    overflow: hidden;
                    position: absolute;
                    width:100%;
                    height:30px;
                }

                .huge_it_slideshow_dots_<?php echo $slideshowID; ?> {
                    display: inline-block;
                    position: relative;
                    cursor: pointer;
                    box-shadow: 1px 1px 1px rgba(0,0,0,0.1) inset, 1px 1px 1px rgba(255,255,255,0.1);
                    width:10px;
                    height: 10px;
                    border-radius: 10px;
                    background: #00f;
                    margin: 10px;
                    overflow: hidden;
                    z-index: 17;
                }

                .huge_it_slideshow_dots_active_<?php echo $slideshowID; ?> {
                    opacity: 1;
                    background:#0f0;
                    filter: Alpha(opacity=100);
                }
                .huge_it_slideshow_dots_deactive_<?php echo $slideshowID; ?> {

                }

                .huge_it_slideshow_image_item1_<?php echo $slideshowID; ?> {
                    display: table; 
                    width: inherit; 
                    height: inherit;
                }
                .huge_it_slideshow_image_item2_<?php echo $slideshowID; ?> {
                    display: table-cell; 
                    vertical-align: middle; 
                    text-align: center;
                }

                .huge_it_slideshow_image_item2_<?php echo $slideshowID; ?> a {
                    display:block;
                    vertical-align:middle;
                    width:100%;
                    height:100%;
                }


                .huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?> {
                    background:#<?php echo $paramssld['slideshow_slideshow_background_color']; ?>;
                    border-width:<?php echo $paramssld['slideshow_slideshow_border_size']; ?>px;
                    border-color:#<?php echo $paramssld['slideshow_slideshow_border_color']; ?>;
                    border-radius:<?php echo $paramssld['slideshow_slideshow_border_radius']; ?>px;
                }

                .huge_it_slideshow_dots_thumbnails_<?php echo $slideshowID; ?> {
            <?php if ($paramssld['slideshow_dots_position'] == "bottom") { ?>
                        bottom:0px;
            <?php } else if ($paramssld['slideshow_dots_position'] == "none") { ?>
                        display:none;
                <?php } else {
                ?>
                        top:0px; <?php } ?>
                }

                .huge_it_slideshow_dots_<?php echo $slideshowID; ?> {
                    background:#<?php echo $paramssld['slideshow_dots_color']; ?>;
                }

                .huge_it_slideshow_dots_active_<?php echo $slideshowID; ?> {
                    background:#<?php echo $paramssld['slideshow_active_dot_color']; ?>;
                }

            <?php
            $arrowfolder = JURI::root() . 'media/com_slideshow/images/Front_images/arrows';
            switch ($paramssld['slideshow_navigation_type']) {
                case 1:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-21px;
                            height:43px;
                            width:29px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.simple.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-21px;
                            height:43px;
                            width:29px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.simple.png) right top no-repeat; 
                        }
                    <?php
                    break;
                case 2:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-25px;
                            height:50px;
                            width:50px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.circle.shadow.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-25px;
                            height:50px;
                            width:50px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.circle.shadow.png) right top no-repeat; 
                        }

                        #huge_it_slideshow_left_<?php echo $slideshowID; ?>:hover {
                            background-position:left -50px;
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?>:hover {
                            background-position:right -50px;
                        }
                    <?php
                    break;
                case 3:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-22px;
                            height:44px;
                            width:44px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.circle.simple.dark.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-22px;
                            height:44px;
                            width:44px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.circle.simple.dark.png) right top no-repeat; 
                        }

                        #huge_it_slideshow_left_<?php echo $slideshowID; ?>:hover {
                            background-position:left -44px;
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?>:hover {
                            background-position:right -44px;
                        }
                    <?php
                    break;
                case 4:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-33px;
                            height:65px;
                            width:59px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.cube.dark.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-33px;
                            height:65px;
                            width:59px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.cube.dark.png) right top no-repeat; 
                        }

                        #huge_it_slideshow_left_<?php echo $slideshowID; ?>:hover {
                            background-position:left -66px;
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?>:hover {
                            background-position:right -66px;
                        }
                    <?php
                    break;
                case 5:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-18px;
                            height:37px;
                            width:40px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.light.blue.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-18px;
                            height:37px;
                            width:40px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.light.blue.png) right top no-repeat; 
                        }

                    <?php
                    break;
                case 6:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-25px;
                            height:50px;
                            width:50px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.light.cube.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-25px;
                            height:50px;
                            width:50px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.light.cube.png) right top no-repeat; 
                        }

                        #huge_it_slideshow_left_<?php echo $slideshowID; ?>:hover {
                            background-position:left -50px;
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?>:hover {
                            background-position:right -50px;
                        }
                    <?php
                    break;
                case 7:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            right:0px;
                            margin-top:-19px;
                            height:38px;
                            width:38px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.light.transparent.circle.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-19px;
                            height:38px;
                            width:38px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.light.transparent.circle.png) right top no-repeat; 
                        }
                    <?php
                    break;
                case 8:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-22px;
                            height:45px;
                            width:45px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-22px;
                            height:45px;
                            width:45px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.png) right top no-repeat; 
                        }
                    <?php
                    break;
                case 9:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-22px;
                            height:45px;
                            width:45px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.circle.blue.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-22px;
                            height:45px;
                            width:45px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.circle.blue.png) right top no-repeat; 
                        }
                    <?php
                    break;
                case 10:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-24px;
                            height:48px;
                            width:48px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.circle.green.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-24px;
                            height:48px;
                            width:48px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.circle.green.png) right top no-repeat; 
                        }

                        #huge_it_slideshow_left_<?php echo $slideshowID; ?>:hover {
                            background-position:left -48px;
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?>:hover {
                            background-position:right -48px;
                        }
                    <?php
                    break;
                case 11:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-29px;
                            height:58px;
                            width:55px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.blue.retro.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-29px;
                            height:58px;
                            width:55px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.blue.retro.png) right top no-repeat; 
                        }
                    <?php
                    break;
                case 12:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-37px;
                            height:74px;
                            width:74px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.green.retro.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-37px;
                            height:74px;
                            width:74px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.green.retro.png) right top no-repeat; 
                        }
                    <?php
                    break;
                case 13:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-16px;
                            height:33px;
                            width:33px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.red.circle.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-16px;
                            height:33px;
                            width:33px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.red.circle.png) right top no-repeat; 
                        }
                    <?php
                    break;
                case 14:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-51px;
                            height:102px;
                            width:52px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.triangle.white.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-51px;
                            height:102px;
                            width:52px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.triangle.white.png) right top no-repeat; 
                        }
                    <?php
                    break;
                case 15:
                    ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:0px;
                            margin-top:-19px;
                            height:39px;
                            width:70px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.ancient.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:0px;
                            margin-top:-19px;
                            height:39px;
                            width:70px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.ancient.png) right top no-repeat; 
                        }
                        <?php
                        break;
                    case 16:
                        ?>
                        #huge_it_slideshow_left_<?php echo $slideshowID; ?> {	
                            left:-21px;
                            margin-top:-20px;
                            height:40px;
                            width:37px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.black.out.png) left  top no-repeat; 
                        }

                        #huge_it_slideshow_right_<?php echo $slideshowID; ?> {
                            right:-21px;
                            margin-top:-20px;
                            height:40px;
                            width:37px;
                            background:url(<?php echo $arrowfolder; ?>/arrows.black.out.png) right top no-repeat; 
                        }
                                <?php
                                break;
                        }
                        ?>
            </style>
                        <?php
                        $args = array(
                            'numberposts' => 10,
                            'offset' => 0,
                            'category' => 0,
                            'orderby' => 'post_date',
                            'order' => 'DESC',
                            'post_type' => 'post',
                            'post_status' => 'draft, publish, future, pending, private',
                            'suppress_filters' => true);
                        ?>
            <div class="huge_it_slideshow_image_wrap_<?php echo $slideshowID; ?>">
                        <?php
                        $current_pos = 0;
                        ?>
                <!-- ##########################DOTS######################### -->
                <div class="huge_it_slideshow_dots_container_<?php echo $slideshowID; ?>">
                    <div class="huge_it_slideshow_dots_thumbnails_<?php echo $slideshowID; ?>">
                        <?php
                        $current_image_id = 0;
                        $current_pos = 0;
                        $current_key = 0;
                        $stri = 0;
                        foreach ($images as $key => $image_row) {
                            $imagerowstype = $image_row->sl_type;
                            if ($image_row->sl_type == '') {
                                $imagerowstype = 'image';
                            }
                            switch ($imagerowstype) {

                                case 'image':

                                    if ($image_row->id == $current_image_id) {
                                        $current_pos = $stri;
                                        $current_key = $stri;
                                    }
                                    ?>
                                    <div id="huge_it_dots_<?php echo $stri; ?>_<?php echo $slideshowID; ?>" class="huge_it_slideshow_dots_<?php echo $slideshowID; ?> <?php echo (($key == $current_image_id) ? 'huge_it_slideshow_dots_active_' . $slideshowID : 'huge_it_slideshow_dots_deactive_' . $slideshowID); ?>" onclick="huge_it_change_image_<?php echo $slideshowID; ?>(parseInt(jQuery('#huge_it_current_image_key_<?php echo $slideshowID; ?>').val()), '<?php echo $stri; ?>', data_<?php echo $slideshowID; ?>, false, true);
                                                                                                return false;" image_id="<?php echo $image_row->id; ?>" image_key="<?php echo $stri; ?>"></div>
                                <?php
                                $stri++;
                                break;
                            case 'video':

                                if ($image_row->id == $current_image_id) {
                                    $current_pos = $stri;
                                    $current_key = $stri;
                                }
                                ?>
                                    <div id="huge_it_dots_<?php echo $stri; ?>_<?php echo $slideshowID; ?>" class="huge_it_slideshow_dots_<?php echo $slideshowID; ?> <?php echo (($key == $current_image_id) ? 'huge_it_slideshow_dots_active_' . $slideshowID : 'huge_it_slideshow_dots_deactive_' . $slideshowID); ?>" onclick="huge_it_change_image_<?php echo $slideshowID; ?>(parseInt(jQuery('#huge_it_current_image_key_<?php echo $slideshowID; ?>').val()), '<?php echo $stri; ?>', data_<?php echo $slideshowID; ?>, false, true);
                                                                                                return false;" image_id="<?php echo $image_row->id; ?>" image_key="<?php echo $stri; ?>"></div>
                        <?php
                        $stri++;
                        break;
                    case 'last_posts':

                        foreach ($recent_posts as $lkeys => $last_posts) {
                            if ($lkeys < $image_row->sl_url) {
                                if (get_the_post_thumbnail($last_posts["ID"], 'thumbnail') != '') {
                                    $imagethumb = wp_get_attachment_image_src(get_post_thumbnail_id($last_posts["ID"]), 'thumbnail-size', true);

                                    if ($image_row->id == $current_image_id) {
                                        $current_pos = $stri;
                                        $current_key = $stri;
                                    }
                                    ?>
                                                <div id="huge_it_dots_<?php echo $stri; ?>_<?php echo $slideshowID; ?>" class="huge_it_slideshow_dots_<?php echo $slideshowID; ?> <?php echo (($stri == $current_image_id) ? 'huge_it_slideshow_dots_active_' . $slideshowID : 'huge_it_slideshow_dots_deactive_' . $slideshowID); ?>" onclick="huge_it_change_image_<?php echo $slideshowID; ?>(parseInt(jQuery('#huge_it_current_image_key_<?php echo $slideshowID; ?>').val()), '<?php echo $stri; ?>', data_<?php echo $slideshowID; ?>, false, true);
                                                                                                            return false;" image_id="<?php echo $image_row->id; ?>" image_key="<?php echo $stri; ?>"></div>
                                                        <?php
                                                        $stri++;
                                                    }
                                                }
                                            }

                                            break;
                                    }
                                }
                                ?>
                    </div>

                                    <?php
                                    if ($paramssld['slideshow_show_arrows'] == "on") {
                                        ?>
                        <a id="huge_it_slideshow_left_<?php echo $slideshowID; ?>" href="#" onclick="huge_it_change_image_<?php echo $slideshowID; ?>(parseInt(jQuery('#huge_it_current_image_key_<?php echo $slideshowID; ?>').val()), (parseInt(jQuery('#huge_it_current_image_key_<?php echo $slideshowID; ?>').val()) - iterator_<?php echo $slideshowID; ?>()) >= 0 ? (parseInt(jQuery('#huge_it_current_image_key_<?php echo $slideshowID; ?>').val()) - iterator_<?php echo $slideshowID; ?>()) % data_<?php echo $slideshowID; ?>.length : data_<?php echo $slideshowID; ?>.length - 1, data_<?php echo $slideshowID; ?>, false, true);
                                                        return false;">
                            <div id="huge_it_slideshow_left-ico_<?php echo $slideshowID; ?>">
                                <div><i class="huge_it_slideshow_prev_btn_<?php echo $slideshowID; ?> fa"></i></div></div>
                        </a>

                        <a id="huge_it_slideshow_right_<?php echo $slideshowID; ?>" href="#" onclick="huge_it_change_image_<?php echo $slideshowID; ?>(parseInt(jQuery('#huge_it_current_image_key_<?php echo $slideshowID; ?>').val()), (parseInt(jQuery('#huge_it_current_image_key_<?php echo $slideshowID; ?>').val()) + iterator_<?php echo $slideshowID; ?>()) % data_<?php echo $slideshowID; ?>.length, data_<?php echo $slideshowID; ?>, false, true);
                                                        return false;">
                            <div id="huge_it_slideshow_right-ico_<?php echo $slideshowID; ?> , data_<?php echo $slideshowID; ?>">
                                <div><i class="huge_it_slideshow_next_btn_<?php echo $slideshowID; ?> fa"></i></div></div>
                        </a>
                                    <?php
                                }
                                ?>
                </div>
                <!-- ##########################IMAGES######################### -->
                <div id="huge_it_slideshow_image_container_<?php echo $slideshowID; ?>" class="huge_it_slideshow_image_container_<?php echo $slideshowID; ?>">        

                    <div class="huge_it_slide_container_<?php echo $slideshowID; ?>">
                        <div class="huge_it_slide_bg_<?php echo $slideshowID; ?>">
                            <ul class="huge_it_slideshow_<?php echo $slideshowID; ?>">
                                    <?php
                                    $i = 0;
                                    foreach ($images as $key => $image_row) {
                                        $imagerowstype = $image_row->sl_type;
                                        if ($image_row->sl_type == '') {
                                            $imagerowstype = 'image';
                                        }
                                        switch ($imagerowstype) {
                                            case 'image':
                                                $target = "";
                                                ?>
                                            <li class="huge_it_slideshow_image<?php if ($i != $current_image_id) {
                            $current_key = $key;
                            echo '_second';
                        } ?>_item_<?php echo $slideshowID; ?>" id="image_id_<?php echo $slideshowID . '_' . $i ?>">      
                                            <?php
                                            if ($image_row->sl_url != "") {
                                                if ($image_row->link_target == "on") {
                                                    $target = 'target="_blank' . $image_row->link_target . '"';
                                                }
                                                echo '<a href="' . $image_row->sl_url . '" ' . $target . '>';
                                            }
                                            ?>
                                                <img id="huge_it_slideshow_image_<?php echo $slideshowID; ?>" class="huge_it_slideshow_image_<?php echo $slideshowID; ?>" src="<?php echo $image_row->image_url; ?>" image_id="<?php echo $image_row->id; ?>" />
                        <?php if ($image_row->sl_url != "") {
                            echo '</a>';
                        } ?>		
                                                <div class="huge_it_slideshow_title_text_<?php echo $slideshowID; ?> <?php if (trim($image_row->name) == "") echo "none"; ?>">
                                                <?php echo $image_row->name; ?>
                                                </div>
                                                <div class="huge_it_slideshow_description_text_<?php echo $slideshowID; ?> <?php if (trim($image_row->description) == "") echo "none"; ?>">
                                                <?php echo $image_row->description; ?>
                                                </div>
                                            </li>
                                            <?php
                                            $i++;
                                            break;

                                        case 'last_posts':
                                            foreach ($recent_posts as $lkeys => $last_posts) {
                                                if ($lkeys < $image_row->sl_url) {
                                                    $imagethumb = wp_get_attachment_image_src(get_post_thumbnail_id($last_posts["ID"]), 'thumbnail-size', true);
                                                    if (get_the_post_thumbnail($last_posts["ID"], 'thumbnail') != '') {
                                                        $target = "";
                                                        ?>
                                                        <li class="huge_it_slideshow_image<?php if ($i != $current_image_id) {
                                                            $current_key = $key;
                                                            echo '_second';
                                                        } ?>_item_<?php echo $slideshowID; ?>" id="image_id_<?php echo $slideshowID . '_' . $i ?>">      
                                    <?php
                                    if ($image_row->sl_postlink == "1") {
                                        if ($image_row->link_target == "on") {
                                            $target = 'target="_blank' . $image_row->link_target . '"';
                                        }
                                        echo '<a href="' . $last_posts["guid"] . '" ' . $target . '>';
                                    }
                                    ?>
                                                            <img id="huge_it_slideshow_image_<?php echo $slideshowID; ?>" class="huge_it_slideshow_image_<?php echo $slideshowID; ?>" src="<?php echo $imagethumb[0]; ?>" image_id="<?php echo $image_row->id; ?>" />
                                    <?php if ($image_row->sl_postlink == "1") {
                                        echo '</a>';
                                    } ?>		
                                                            <div class="huge_it_slideshow_title_text_<?php echo $slideshowID; ?> <?php if (trim($last_posts["post_title"]) == "") echo "none";
                                    if ($image_row->sl_stitle != "1") echo " hidden"; ?>">
                                    <?php echo $last_posts["post_title"]; ?>
                                                            </div>
                                                            <div class="huge_it_slideshow_description_text_<?php echo $slideshowID; ?> <?php if (trim($last_posts["post_content"]) == "") echo "none";
                                    if ($image_row->sl_sdesc != "1") echo " hidden"; ?>">
                                    <?php echo substr_replace($last_posts["post_content"], "", $image_row->description); ?>
                                                            </div>
                                                        </li>
                                    <?php
                                    $i++;
                                }
                            }
                        }
                        break;
                    case 'video':
                        ?>
                                            <li  class="huge_it_slideshow_image<?php if ($i != $current_image_id) {
                            $current_key = $key;
                            echo '_second';
                        } ?>_item_<?php echo $slideshowID; ?>" id="image_id_<?php echo $slideshowID . '_' . $i ?>">      
                        <?php
                        if (strpos($image_row->image_url, 'youtube') !== false) {
                            $video_thumb_url = $this->get_youtube_id_from_url($image_row->image_url);
                            ?>

                                                    <div id="video_id_<?php echo $slideshowID; ?>_<?php echo $key; ?>" class="huge_it_video_frame_<?php echo $slideshowID; ?>"></div>
                        <?php
                        } else {
                            $vimeo = $image_row->image_url;
                            $vimeo_chng = explode("/", $vimeo);
                            $imgid = end($vimeo_chng);
                            ?>					
                                                    <iframe id="player_<?php echo $key; ?>"  class="huge_it_video_frame_<?php echo $slideshowID; ?>" src="//player.vimeo.com/video/<?php echo $imgid; ?>?api=1&player_id=player_<?php echo $key; ?>&showinfo=0&controls=0" frameborder="0" allowfullscreen></iframe>
                        <?php } ?>
                                            </li>
                        <?php
                        $i++;
                        break;
                }
            }
            ?>
                                <input  type="hidden" id="huge_it_current_image_key_<?php echo $slideshowID; ?>" value="0" />
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


            <?php
        }
        return $render_html1 = ob_get_clean();
    }

}
