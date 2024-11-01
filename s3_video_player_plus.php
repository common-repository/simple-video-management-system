<?php
/*
 Plugin Name: Simple Video Management System 
 Plugin URI: https://namstoolkit.com/wpSVMS
 Description: Simple video player that just works
 Version: 1.0.4
 Author: NAMS, Inc
 Author URI: https://mynams.com/
 License: GPLv2 or later
 */

if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'VPP_VERSION', '1.0.4' );
define( 'VPP_VALID_UPDATE_I', '8.9.9' );
define( 'VPP_PATH', dirname( __FILE__ ) );
define( 'SVMS_PATH', dirname( __FILE__ ) );
define( 'VPP_APP_PATH', VPP_PATH . '/app' );
define( 'VPP_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'VPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VPP_INCLUDE', plugins_url( 'includes', __FILE__ ) );
define( 'VPP_VADS_CSS_URL', plugins_url( 'includes/video-js/videojs.ads.css', __FILE__ ) );
define( 'VPP_VPREROLL_CSS_URL', plugins_url( 'includes/video-js/videojs-preroll.css', __FILE__ ) );
define( 'VPP_VADS_URL', plugins_url( 'includes/video-js/videojs.ads.js', __FILE__ ) );
define( 'VPP_VPREROLL_URL', plugins_url( 'includes/video-js/videojs-preroll.js', __FILE__ ) );
define( 'VPP_VPOSTROLL_URL', plugins_url( 'includes/video-js/videojs-postroll.js', __FILE__ ) );
define( 'VPP_SWF_URL', plugins_url( 'includes/video-js/video-js.swf', __FILE__ ) );
define( 'VPP_VIMEO_URL', plugins_url( 'includes/video-js/Vimeo.js', __FILE__ ) );
define( 'VPP_VIMEO_ONE', plugins_url( 'includes/withinviewport.js', __FILE__ ) );
define( 'VPP_VIMEO_TWO', plugins_url( 'includes/jquery.withinviewport.js', __FILE__ ) );
define( 'SVMS_CSS', plugins_url( 'includes/svms-styles.css', __FILE__ ) );

if ( WP_DEBUG === true ) {
	define( 'VPP_JS_URL', plugins_url( 'includes/video-js/video.js', __FILE__ ) );
	define( 'VPP_CSS_URL', plugins_url( 'includes/video-js/video-js.css', __FILE__ ) );
	define( 'VPP_YT_URL', plugins_url( 'includes/video-js/Youtube.js', __FILE__ ) );
} else {
	define( 'VPP_JS_URL', plugins_url( 'includes/video-js/video.min.js', __FILE__ ) );
	define( 'VPP_CSS_URL', plugins_url( 'includes/video-js/video-js.css', __FILE__ ) );
	define( 'VPP_YT_URL', plugins_url( 'includes/video-js/Youtube.js', __FILE__ ) );
}

$functions_dir = plugin_dir_path( __FILE__ ) . '/';
define( 'SVMS_MICAHEL_FILE', $functions_dir );

if ( ! array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) || stristr( $_SERVER['HTTP_USER_AGENT'], 'ipad' ) === false ) {
	define( 'VPP_FORCE_CONTROLS', 0 );
} else {
	define( 'VPP_FORCE_CONTROLS', 1 );
}


require_once 's3_video_player_plus_functions.php';
register_activation_hook( __FILE__, 'Vpp_activatePlugin' );
