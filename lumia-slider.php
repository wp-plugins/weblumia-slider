<?php
/*
Plugin Name: Lumia Slider
Plugin URI: http://www.weblumia.com
Description: Fully loaded, responsive and video content slider
Version: 2.3
Author: Jinesh.P.V
Author URI: http://www.weblumia.com
*/
/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (Jinesh.P.V : jinuvijay5@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/********************************************************/
/*                        Actions                       */
/********************************************************/
add_action( 'init', 'do_output_buffer' );
add_action( 'admin_menu', 'lumia_add_menu' );
add_action( 'admin_init', 'lumia_reg_function' );
add_action( 'admin_init', 'lumia_scripts' );
add_action( 'admin_head', 'lumia_media_admin_scripts' );
add_action( 'admin_head', 'load_lumia_stylesheet' );
add_action( 'wp_head', 'load_lumia_stylesheet', 20 );
add_action( 'wp_enqueue_scripts', 'load_lumia_scripts' );
add_action( 'wp_footer', 'load_lumiaslider_scripts', 20 );
add_shortcode( 'lumiaslider', 'show_lumiaslider' );
add_action( 'widgets_init', create_function( '', 'register_widget( "lumia_slider_widget" );' ) );
// Activation hook for creating the initial DB table
register_activation_hook( __FILE__, 'lumia_activate' );
add_theme_support( 'post-thumbnails' );
function lumia_activate() {
	lumiaslider_create_db_table();
}
function do_output_buffer() {
        ob_start();
}
function lumia_add_menu() {
	add_menu_page( 'Lumia Slider', 'Lumia Slider', 'administrator', 'lumia_settings', 'lumia_menu_function' );
	add_submenu_page( 'lumia_settings', 'Lumia Slider Settings', 'Settings', 'manage_options', 'lumia_settings', 'lumia_add_menu' ); 
	add_submenu_page( 'lumia_settings', 'All Sliders', 'All Sliders', 'manage_options', 'lumia_sliders', 'lumia_all_sliders' ); 
}
/********************************************************/
/*           lumia sliders database table create          */
/********************************************************/
function lumiaslider_create_db_table() {
	// Get WPDB Object
	global $wpdb;
	
	// Executing the query
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	// Table name
	$table_name			=	$wpdb->prefix . "lumiaslider";
	$table_name_img		=	$wpdb->prefix . "lumiaslider_images";
	
	// Building the query
	$sql = "CREATE TABLE `$table_name` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) NOT NULL,
			  `date_c` int(10) NOT NULL,
			  `date_m` int(11) NOT NULL,
			  `flag_hidden` tinyint(1) NOT NULL DEFAULT '0',
			  `flag_deleted` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`)
			);";
	// Execute the query
	dbDelta( $sql );
	
	// Building the query
	$sql_img = "CREATE TABLE `$table_name_img` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `sid` int(100) NOT NULL,
			  `data` mediumtext NOT NULL,
			  `date_c` int(11) NOT NULL,
			  `date_m` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			);";
	// Execute the query
	dbDelta( $sql_img );
}
function lumia_reg_function() {
	
	register_setting( 'lumia-settings-group', 'lumia_mode' );
	register_setting( 'lumia-settings-group', 'lumia_infiniteLoop' );
	register_setting( 'lumia-settings-group', 'lumia_speed' );
	register_setting( 'lumia-settings-group', 'lumia_easing' );
	register_setting( 'lumia-settings-group', 'lumia_random_start' );
	register_setting( 'lumia-settings-group', 'lumia_start_slide' );
	register_setting( 'lumia-settings-group', 'lumia_video' );
	register_setting( 'lumia-settings-group', 'lumia_captions' );
	
	register_setting( 'lumia-settings-group', 'lumia_pager' );
	register_setting( 'lumia-settings-group', 'lumia_pagertype' );
	
	register_setting( 'lumia-settings-group', 'lumia_controls' );
	register_setting( 'lumia-settings-group', 'lumia_auto_controls' );
	
	register_setting( 'lumia-settings-group', 'lumia_auto' );
	register_setting( 'lumia-settings-group', 'lumia_pause' );
}
function load_lumia_stylesheet() {
	
	if( is_admin() && isset( $_REQUEST['page'] ) ){
		wp_enqueue_style( 'wl-admin', plugins_url( 'admin.css', __FILE__ ) );	
	}
	
	if( !is_admin() ){
		wp_enqueue_style( 'wlslider.css', plugins_url( 'wlslider.css', __FILE__ ) );
	}
}
/********************************************************/
/*               Enqueue Content Scripts                */
/********************************************************/
function load_lumia_scripts() {
	
	if( !is_admin() ){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-effects-core ', array( 'jquery' ), '1.3' );
		wp_enqueue_script( 'fitvids', plugins_url( '/js/jquery.fitvids.js', __FILE__ ), array( 'jquery' ), '1.0' );
		wp_enqueue_script( 'wlslider', plugins_url( '/js/jquery.wlslider.js', __FILE__ ), array( 'jquery' ), '1.1.0' );
	}
}
function lumia_scripts() {
	
	if( $_REQUEST['page'] == 'lumia_sliders' ){
		if( function_exists( 'wp_enqueue_media' ) ){
			wp_enqueue_media();
		}else{
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
		}
	}
}
function lumia_media_admin_scripts() {
	
	if( is_admin() && $_REQUEST['page'] == 'lumia_sliders' ){
	?>
<script type="text/javascript">
	jQuery( document ).ready(function() {
									  
		jQuery( '.imgbutton' ).click(function() {
			window.send_to_editor = function(html) {
				imgurl		=	jQuery( 'img', html ).attr( 'src' );
				jQuery( '#input_image' ).val( imgurl );
				jQuery( '.imagediv' ).css( 'display', 'table-row' );
				jQuery( '#image_src' ).css( 'display', 'block' );
				jQuery( '#image_src' ).attr( 'src', imgurl );
				//tb_remove();
			}
		});
		
		jQuery( '.thumbbutton' ).click(function() {
			window.send_to_editor = function(html) {
				imgurl		=	jQuery( 'img', html ).attr( 'src' );
				jQuery( '#input_image_thumb' ).val( imgurl );
				jQuery( '.imagediv' ).css( 'display', 'table-row' );
				jQuery( '#image_thumb_src' ).css( 'display', 'block' );
				jQuery( '#image_thumb_src' ).attr( 'src', imgurl );
				//tb_remove();
			}
		});
	});
	</script>
<?php
	}
}
function load_lumiaslider_scripts(){
	
	$mode					=	( get_option( 'lumia_mode' ) )				?	get_option( 'lumia_mode' )				:	'horizontal';
	$infiniteLoop			=	( get_option( 'lumia_infiniteLoop' ) )		?	get_option( 'lumia_infiniteLoop' )		:	'false';
	$speed					=	( get_option( 'lumia_speed' ) )				?	get_option( 'lumia_speed' )				:	'1000';
	$easing					=	( get_option( 'lumia_easing' ) )			?	get_option( 'lumia_easing' )			:	'linear';
	$startSlide				=	( get_option( 'lumia_start_slide' ) )		?	get_option( 'lumia_start_slide' )		:	'1';
	$randomStart			=	( get_option( 'lumia_random_start' ) )		?	get_option( 'lumia_random_start' )		:	'false';
	$video					=	( get_option( 'lumia_video' ) )				?	get_option( 'lumia_video' )				:	'false';
	$useCSS					=	( get_option( 'lumia_video' ) == 'true' )	?	'false'									:	'true';
	$captions				=	( get_option( 'lumia_captions' ) )			?	get_option( 'lumia_captions' )			:	'true';
	$pager					=	( get_option( 'lumia_pager' ) )				?	get_option( 'lumia_pager' )				:	'true';
	$pagerType				=	( get_option( 'lumia_pagertype' ) )			?	get_option( 'lumia_pagertype' )			:	'full';
	$controls				=	( get_option( 'lumia_controls' ) )			?	get_option( 'lumia_controls' )			:	'true';
	$autoControls			=	( get_option( 'lumia_auto_controls' ) )		?	get_option( 'lumia_auto_controls' )		:	'false';
	$auto					=	( get_option( 'lumia_auto' ) )				?	get_option( 'lumia_auto' )				:	'true';
	$pause					=	( get_option( 'lumia_pause' ) )				?	get_option( 'lumia_pause' )				:	'5000';
	
	$scripts				=	"<script type='text/javascript'>
								jQuery( '.wlSlider' ).wlSlider({
									mode			:	'" . $mode . "',
									infiniteLoop	:	" . $infiniteLoop . ",
									speed			:	" . $speed . ",
									easing			:	'" . $easing . "',
									startSlide		:	" . $startSlide . ",
									randomStart		:	" . $randomStart . ",
									video			:	" . $video . ",
									useCSS			:	" . $useCSS . ",
									captions		:	" . $captions . ",
									pager			:	" . $pager . ",
									pagerType		:	'" . $pagerType . "',
									controls		:	" . $controls . ",
									autoControls	:	" . $autoControls . ",
									auto			:	" . $auto . ",
									pause			:	" . $pause . "
								});
							</script>";
	
	echo $scripts;
}
function show_lumiaslider( $args ) {?>
<?php 
    global $wpdb;
	include( 'lumia-slider-functions.php' );
    if( !isset( $args['id'] ) || empty( $args['id'] ) ) {
        return '[lumiaslider] '.__('Invalid shortcode', 'lumiaslider').'';
    }
    $id						=	$args['id'];
    $slider					=	populate_slider_images( $id );
    if( $slider == null ) {
        return '[lumiaslider] ' . __( 'Slider not found', 'lumiaslider' );
    }
	
	$html					=	'<ul class="wlSlider">';
	
	if( $slider ){
		foreach( $slider as $sliderObj ){
			
			$slides			=	json_decode( $sliderObj['data'], true );
	
			if ( parse_youtube( stripslashes( $slides['custom_html'] ) ) ){
				
				$video_id	=	parse_youtube( stripslashes( $slides['custom_html'] ) ) ;
				$html		.=	'<li><iframe width="100%" height="100%" src="//www.youtube.com/embed/' . $video_id . '?loop=0&wmode=opaque" frameborder="0" allowfullscreen></iframe></li>';
			} elseif ( parse_vimeo( stripslashes( $slides['custom_html'] ) ) ){
				
				$video_id	=	parse_vimeo( stripslashes( $slides['custom_html'] ) ) ;
				$html		.=	'<li><iframe src="http://player.vimeo.com/video/' . $video_id . '?color=ff9933&amp;loop=1" width="100%" height="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></li>';
			} elseif ( $slides['image'] != '' ){
				
				$html		.=	'<li><img src="' . $slides['image'] . '" title="' . stripslashes( $slides['custom_html'] ) . '" /></li>';
			}else{
				$html		.=	'';
			}
		}
	}else{
		$html				.=	'<li>No data to display</li>';
	}
	
	$html					.=	'</ul>';
	
	echo $html;
} 
    
function lumia_all_sliders(){
	include( dirname(__FILE__) . '/' . 'all-sliders.php' );
}
function lumia_list_slider(){
	include( dirname(__FILE__) . '/' . 'list-images.php' );
}
function lumia_add_slider(){
	include( dirname(__FILE__) . '/' . 'add-slider.php' );
}
if( strstr( $_SERVER['REQUEST_URI'], 'lumia_settings' ) && $_REQUEST['action'] == 'add' ) {
	save_slider( $_REQUEST['slider_id'] );
}
function lumia_menu_function() {?>
<div class="wrap">
  <h2>
    <?php _e( 'Lumia Slider Settings', 'lumiaslider' ) ?>
  </h2>
  <?php if( $_REQUEST['settings-updated'] == 'true' ){?>
  <div class="updated below-h2" id="message">
    <p>Lumia Slider Settings Updated</p>
  </div>
  <?php }?>
  <form method="post" action="options.php">
    <div class="wl-pages" >
      <div class="wl-page wl-settings active">
        <div class="wl-box wl-settings">
          <h3 class="header">
            <?php _e( 'Global Settings', 'lumiaslider' ) ?>
          </h3>
          <?php settings_fields( 'lumia-settings-group' ); ?>
          <table>
            <thead>
              <tr>
                <td colspan="3"><h4>
                    <?php _e( 'General', 'lumiaslider' ) ?>
                  </h4></td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php _e( 'Slider Mode', 'lumiaslider' ) ?></td>
                <td><?php $lumia_mode = get_option( 'lumia_mode' ); ?>
                  <select name="lumia_mode" id="lumia_mode">
                    <option value="horizontal" <?php if( $lumia_mode == 'horizontal' ) echo 'selected = "selected"'; ?>>Horizontal</option>
                    <option value="vertical" <?php if( $lumia_mode == 'vertical' ) echo 'selected = "selected"'; ?> >Vertical</option>
                    <option value="slide" <?php if( $lumia_mode == 'slide' ) echo 'selected = "selected"'; ?> >Slide</option>
                  </select></td>
                <td class="desc"><?php _e( 'Type of transition between slides. Can be "horizontal", "vertical" or "slide".', 'lumiaslider' ) ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Infinite Loop', 'lumiaslider' ) ?></td>
                <td><?php $lumia_infiniteLoop = get_option( 'lumia_infiniteLoop' ); ?>
                  <select name="lumia_infiniteLoop" id="lumia_infiniteLoop">
                    <option value="true" <?php if( $lumia_infiniteLoop == 'true' ) echo 'selected = "selected"'; ?>>True</option>
                    <option value="false" <?php if( $lumia_infiniteLoop == 'false' ) echo 'selected = "selected"'; ?> >False</option>
                  </select></td>
                <td class="desc"><?php _e( 'If true, clicking "Next" while on the last slide will transition to the first slide and vice-versa.', 'lumiaslider' ) ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Slider Speed', 'lumiaslider' ) ?></td>
                <td><input type="text" name="lumia_speed" id="lumia_speed" size="7" value="<?php echo get_option( 'lumia_speed' ); ?>" class="input" /></td>
                <td class="desc">(ms)
                  <?php _e( ' The slider speed time in milli seconds. ', 'lumiaslider' ) ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Slide Transition', 'lumiaslider' ) ?></td>
                <td><?php $lumia_easing = get_option( 'lumia_easing' ); ?>
                  <select name="lumia_easing" id="lumia_easing">
                    <option value="linear" <?php if( $lumia_easing == 'linear' ) echo 'selected = "selected"'; ?>>linear</option>
                    <option value="swing" <?php if( $lumia_easing == 'swing' ) echo 'selected = "selected"'; ?>>swing</option>
                    <option value="easeInQuad" <?php if( $lumia_easing == 'easeInQuad' ) echo 'selected = "selected"'; ?>>easeInQuad</option>
                    <option value="easeOutQuad" <?php if( $lumia_easing == 'easeOutQuad' ) echo 'selected = "selected"'; ?>>easeOutQuad</option>
                    <option value="easeInOutQuad" <?php if( $lumia_easing == 'easeInOutQuad' ) echo 'selected = "selected"'; ?>>easeInOutQuad</option>
                    <option value="easeInCubic" <?php if( $lumia_easing == 'easeInCubic' ) echo 'selected = "selected"'; ?>>easeInCubic</option>
                    <option value="easeOutCubic" <?php if( $lumia_easing == 'easeOutCubic' ) echo 'selected = "selected"'; ?>>easeOutCubic</option>
                    <option value="easeInOutCubic" <?php if( $lumia_easing == 'easeInOutCubic' ) echo 'selected = "selected"'; ?>>easeInOutCubic</option>
                    <option value="easeInQuart" <?php if( $lumia_easing == 'easeInQuart' ) echo 'selected = "selected"'; ?>>easeInQuart</option>
                    <option value="easeOutQuart" <?php if( $lumia_easing == 'easeOutQuart' ) echo 'selected = "selected"'; ?>>easeOutQuart</option>
                    <option value="easeInOutQuart" <?php if( $lumia_easing == 'easeInOutQuart' ) echo 'selected = "selected"'; ?>>easeInOutQuart</option>
                    <option value="easeInQuint" <?php if( $lumia_easing == 'easeInQuint' ) echo 'selected = "selected"'; ?>>easeInQuint</option>
                    <option value="easeOutQuint" <?php if( $lumia_easing == 'easeOutQuint' ) echo 'selected = "selected"'; ?>>easeOutQuint</option>
                    <option value="easeInOutQuint" <?php if( $lumia_easing == 'easeInOutQuint' ) echo 'selected = "selected"'; ?>>easeInOutQuint</option>
                    <option value="easeInSine" <?php if( $lumia_easing == 'easeInSine' ) echo 'selected = "selected"'; ?>>easeInSine</option>
                    <option value="easeOutSine" <?php if( $lumia_easing == 'easeOutSine' ) echo 'selected = "selected"'; ?>>easeOutSine</option>
                    <option value="easeInOutSine" <?php if( $lumia_easing == 'easeInOutSine' ) echo 'selected = "selected"'; ?>>easeInOutSine</option>
                    <option value="easeInExpo" <?php if( $lumia_easing == 'easeInExpo' ) echo 'selected = "selected"'; ?>>easeInExpo</option>
                    <option value="easeOutExpo" <?php if( $lumia_easing == 'easeOutExpo' ) echo 'selected = "selected"'; ?>>easeOutExpo</option>
                    <option value="easeInOutExpo" <?php if( $lumia_easing == 'easeInOutExpo' ) echo 'selected = "selected"'; ?>>easeInOutExpo</option>
                    <option value="easeInCirc" <?php if( $lumia_easing == 'easeInCirc' ) echo 'selected = "selected"'; ?>>easeInCirc</option>
                    <option value="easeOutCirc" <?php if( $lumia_easing == 'easeOutCirc' ) echo 'selected = "selected"'; ?>>easeOutCirc</option>
                    <option value="easeInOutCirc" <?php if( $lumia_easing == 'easeInOutCirc' ) echo 'selected = "selected"'; ?>>easeInOutCirc</option>
                    <option value="easeInElastic" <?php if( $lumia_easing == 'easeInElastic' ) echo 'selected = "selected"'; ?>>easeInElastic</option>
                    <option value="easeOutElastic" <?php if( $lumia_easing == 'easeOutElastic' ) echo 'selected = "selected"'; ?>>easeOutElastic</option>
                    <option value="easeInOutElastic" <?php if( $lumia_easing == 'easeInOutElastic' ) echo 'selected = "selected"'; ?>>easeInOutElastic</option>
                    <option value="easeInBack" <?php if( $lumia_easing == 'easeInBack' ) echo 'selected = "selected"'; ?>>easeInBack</option>
                    <option value="easeOutBack" <?php if( $lumia_easing == 'easeOutBack' ) echo 'selected = "selected"'; ?>>easeOutBack</option>
                    <option value="easeInOutBack" <?php if( $lumia_easing == 'easeInOutBack' ) echo 'selected = "selected"'; ?>>easeInOutBack</option>
                    <option value="easeInBounce" <?php if( $lumia_easing == 'easeInBounce' ) echo 'selected = "selected"'; ?>>easeInBounce</option>
                    <option value="easeOutBounce" <?php if( $lumia_easing == 'easeOutBounce' ) echo 'selected = "selected"'; ?>>easeOutBounce</option>
                    <option value="easeInOutBounce" <?php if( $lumia_easing == 'easeInOutBounce' ) echo 'selected = "selected"'; ?>>easeInOutBounce</option>
                  </select></td>
                <td class="desc"><?php _e( 'The type of "easing" to use during transitions. If using CSS transitions, include a value for the transition timing function property.', 'lumiaslider' ) ?></td>
              </tr>
              <tr>
                <td><?php _e( 'First slide', 'lumiaslider' ) ?></td>
                <td><input type="text" name="lumia_start_slide" id="lumia_start_slide" size="7" value="<?php echo get_option( 'lumia_start_slide' ); ?>" class="input" /></td>
                <td class="desc"><?php _e( ' Lumia Slider will start with this slide.', 'lumiaslider' ) ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Random Start', 'lumiaslider' ) ?></td>
                <td><?php $lumia_random_start = get_option( 'lumia_random_start' ); ?>
                  <select name="lumia_random_start" id="lumia_random_start">
                    <option value="true" <?php if( $lumia_random_start == 'true' ) echo 'selected = "selected"'; ?>>True</option>
                    <option value="false" <?php if( $lumia_random_start == 'false' ) echo 'selected = "selected"'; ?> >False</option>
                  </select></td>
                <td class="desc"><?php _e( 'If true, the slider to start with a random slide.', 'lumiaslider' ) ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Enable Captions', 'lumiaslider' ) ?></td>
                <td><?php $lumia_captions = get_option( 'lumia_captions' ); ?>
                  <select name="lumia_captions" id="lumia_captions">
                    <option value="true" <?php if( $lumia_captions == 'true' ) echo 'selected = "selected"'; ?>>True</option>
                    <option value="false" <?php if( $lumia_captions == 'false' ) echo 'selected = "selected"'; ?> >False</option>
                  </select></td>
                <td class="desc"><?php _e( 'If true, caption will be visible.', 'lumiaslider') ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Enable Video', 'lumiaslider' ) ?></td>
                <td><?php $lumia_video = get_option( 'lumia_video' ); ?>
                  <select name="lumia_video" id="lumia_video">
                    <option value="true" <?php if( $lumia_video == 'true' ) echo 'selected = "selected"'; ?>>True</option>
                    <option value="false" <?php if( $lumia_video == 'false' ) echo 'selected = "selected"'; ?> >False</option>
                  </select></td>
                <td class="desc"><?php _e( 'If true, the slider will allow youtube and vimeo videos.', 'lumiaslider' ) ?></td>
              </tr>
              <tr class="heading">
                <td colspan="3"><h4>
                    <?php _e( 'Pager', 'lumiaslider' ) ?>
                  </h4></td>
              </tr>
              <tr>
                <td><?php _e( 'Enable Pager', 'lumiaslider' ) ?></td>
                <td><?php $lumia_video = get_option( 'lumia_pager '); ?>
                  <select name="lumia_pager" id="lumia_pager">
                    <option value="true" <?php if( $lumia_pager == 'true' ) echo 'selected = "selected"'; ?>>True</option>
                    <option value="false" <?php if( $lumia_pager == 'false' ) echo 'selected = "selected"'; ?> >False</option>
                  </select></td>
                <td class="desc"><?php _e( 'If true, slide buttons will be visible', 'lumiaslider' ) ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Pager Type', 'lumiaslider' ) ?></td>
                <td><?php $lumia_pagertype = get_option( 'lumia_pagertype '); ?>
                  <select name="lumia_pagertype" id="lumia_pagertype">
                    <option value="full" <?php if( $lumia_pagertype == 'full' ) echo 'selected = "selected"'; ?>>Full</option>
                    <option value="short" <?php if( $lumia_pagertype == 'short' ) echo 'selected = "selected"'; ?> >Short</option>
                  </select></td>
                <td class="desc"><?php _e( 'If true, slide buttons will be visible', 'lumiaslider' ) ?></td>
              </tr>
              <tr class="heading">
                <td colspan="3"><h4>
                    <?php _e( 'Controls', 'lumiaslider' ) ?>
                  </h4></td>
              </tr>
              <tr>
                <td><?php _e( 'Enable Controls', 'lumiaslider' ) ?></td>
                <td><?php $lumia_controls = get_option( 'lumia_controls '); ?>
                  <select name="lumia_controls" id="lumia_controls">
                    <option value="true" <?php if( $lumia_controls == 'true' ) echo 'selected = "selected"'; ?>>True</option>
                    <option value="false" <?php if( $lumia_controls == 'false' ) echo 'selected = "selected"'; ?> >False</option>
                  </select></td>
                <td class="desc"><?php _e( 'If true, Prev and Next buttons will be visible.', 'lumiaslider') ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Auto Controls', 'lumiaslider' ) ?></td>
                <td><?php $lumia_auto_controls = get_option( 'lumia_auto_controls '); ?>
                  <select name="lumia_auto_controls" id="lumia_auto_controls">
                    <option value="true" <?php if( $lumia_auto_controls == 'true' ) echo 'selected = "selected"'; ?>>True</option>
                    <option value="false" <?php if( $lumia_auto_controls == 'false' ) echo 'selected = "selected"'; ?> >False</option>
                  </select></td>
                <td class="desc"><?php _e( 'If true, "Start" / "Stop" controls will be visible', 'lumiaslider') ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Automatic', 'lumiaslider' ) ?></td>
                <td><?php $lumia_auto = get_option( 'lumia_auto' ); ?>
                  <select name="lumia_auto" id="lumia_auto">
                    <option value="true" <?php if( $lumia_auto == 'true' ) echo 'selected = "selected"'; ?>>True</option>
                    <option value="false" <?php if( $lumia_auto == 'false' ) echo 'selected = "selected"'; ?> >False</option>
                  </select></td>
                <td class="desc"><?php _e( 'If true, slideshow will automatically start after loading the page.', 'lumiaslider' ) ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Pause Time', 'lumiaslider' ) ?></td>
                <td><input type="text" name="lumia_pause" id="lumia_pause" size="7" value="<?php echo get_option( 'lumia_pause' ); ?>" class="input" /></td>
                <td class="desc">(ms)
                  <?php _e( ' The slider pause time in milli seconds. The slide will be delayed for the given milli seconds.', 'lumiaslider' ) ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="wl-box wl-publish">
      <h3 class="header">
        <?php _e('Publish', 'lumiasliders') ?>
      </h3>
      <div class="inner">
        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'lumiaslider' ); ?>" />
        <p class="wl-saving-warning"></p>
        <div class="clear"></div>
      </div>
    </div>
  </form>
</div>
<?php }
/********************************************************/
/*                   Widget settings                    */
/********************************************************/
class lumia_slider_widget extends WP_Widget {
	
	function __construct() {
		$widget_ops = array( 'classname' => 'lumiaslider_widget', 'description' => __('Insert a slider with Lumia Slider', 'lumiaslider') );
		$control_ops = array( 'id_base' => 'lumiaslider_widget' );
		parent::__construct( 'lumiaslider_widget', __( 'Lumia Slider', 'lumiaslider' ), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		// Call layerslider_init to show the slider
		echo do_shortcode('[layerslider id="'.$instance['id'].'"]');
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['id'] = strip_tags( $new_instance['id'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
	
	function form( $instance ) {
			// Defaults
			$defaults = array( 'title' => __('Lumia Slider', 'lumiaslider'));
			$instance = wp_parse_args( (array) $instance, $defaults );
			// Get WPDB Object
			global $wpdb;
			// Table name
			$table_name = $wpdb->prefix . "lumiaslider";
			// Get sliders
			$sliders = $wpdb->get_results( "SELECT * FROM $table_name
												WHERE flag_hidden = '0' AND flag_deleted = '0'
												ORDER BY date_c ASC LIMIT 100" );
			?>
            <p>
              <label for="<?php echo $this->get_field_id( 'id' ); ?>">
                <?php _e('Choose a slider:', 'lumiaslider') ?>
              </label>
              <br>
              <?php if($sliders != null && !empty($sliders)) { ?>
              <select id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>">
                <?php foreach($sliders as $item) : ?>
                <?php $name = empty($item->name) ? 'Unnamed' : $item->name; ?>
                <?php if(($item->id) == $instance['id']) { ?>
                <option value="<?php echo $item->id?>" selected="selected"><?php echo $name ?> | #<?php echo $item->id?></option>
                <?php } else { ?>
                <option value="<?php echo $item->id?>"><?php echo $name ?> | #<?php echo $item->id?></option>
                <?php } ?>
                <?php endforeach; ?>
              </select>
              <?php } else { ?>
              <?php _e("You didn't create any slider yet.", "Lumia Slider", "lumiaslider") ?>
              <?php } ?>
            </p>
            <p>
              <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                <?php _e('Title:', 'lumiaslider'); ?>
              </label>
              <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
            </p>
            <?php 
	}
}
function parse_youtube( $link ){
 	
	$matches = '';
	
	$regexstr = '~
		                                # Match Youtube link and embed code
		(?:                             # Group to match embed codes
			(?:<iframe [^>]*src=")?     # If iframe match up to first quote of src
			|(?:                        # Group to match if older embed
				(?:<object .*>)?        # Match opening Object tag
				(?:<param .*</param>)*  # Match all param tags
				(?:<embed [^>]*src=")?  # Match embed tag to the first quote of src
			)?                          # End older embed code group
		)?                              # End embed code groups
		(?:                             # Group youtube url
			https?:\/\/                 # Either http or https
			(?:[\w]+\.)*                # Optional subdomains
			(?:                         # Group host alternatives.
			youtu\.be/                  # Either youtu.be,
			| youtube\.com              # or youtube.com
			| youtube-nocookie\.com     # or youtube-nocookie.com
			)                           # End Host Group
			(?:\S*[^\w\-\s])?           # Extra stuff up to VIDEO_ID
			([\w\-]{11})                # $1: VIDEO_ID is numeric
			[^\s]*                      # Not a space
		)                               # End group
		"?                              # Match end quote if part of src
		(?:[^>]*>)?                     # Match any extra stuff up to close brace
		(?:                             # Group to match last embed code
			</iframe>                   # Match the end of the iframe
			|</embed></object>          # or Match the end of the older embed
		)?                              # End Group of last bit of embed code
		~ix';
		
	if ( ( is_array( $link ) ) ) {		
		preg_match( $regexstr, $link, $matches );
		if( !empty( $matches ) )
			return $matches[1];
		else
			return ;
	} else {
		return ;
	}
}
function parse_vimeo( $link ){
	
	$matches = '';
 
	$regexstr = '~
		                            # Match Vimeo link and embed code
		(?:<iframe [^>]*src=")?     # If iframe match up to first quote of src
		(?:                         # Group vimeo url
			https?:\/\/             # Either http or https
			(?:[\w]+\.)*            # Optional subdomains
			vimeo\.com              # Match vimeo.com
			(?:[\/\w]*\/videos?)?   # Optional video sub directory this handles groups links also
			\/                      # Slash before Id
			([0-9]+)                # $1: VIDEO_ID is numeric
			[^\s]*                  # Not a space
		)                           # End group
		"?                          # Match end quote if part of src
		(?:[^>]*></iframe>)?        # Match the end of the iframe
		(?:<p>.*</p>)?              # Match any title information stuff
		~ix';
	
	if ( ( is_array( $link ) ) ) {
		preg_match( $regexstr, $link, $matches );
		if( !empty( $matches ) )
			return $matches[1];
		else
			return ;
	} else {
		return ;
	}
}