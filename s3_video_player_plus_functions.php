<?php
/**
 * If $key exists in $array, its value is returned. Otherwise, function will return $default.
 *
 * @param $array
 * @param $key
 * @param string $default
 *
 * @return mixed|string
 */
function getArrayItem( $array, $key, $default = '' ) {
	if ( array_key_exists( $key, $array ) ) {
		return $array[ $key ];
	}

	return $default;
}
add_action('admin_enqueue_scripts', 'load_svms_scripts');
function load_svms_scripts(){
	wp_enqueue_script('svms-scripts', plugins_url( '/includes/svms-scripts.js', __FILE__ ), [], '1.0', true);
}
/**
 * Enqueues scripts both for admin and client sides.
 */
function vpp_enqueueScripts() {
	// Admin only
	if ( is_admin() ) {
		include_once VPP_APP_PATH . '/Vpp_Base.php';
		$get_ver = get_option( 'rlm_version_' . Vpp_Base::$name, VPP_VERSION );
		if ( $get_ver != VPP_VERSION ) {
			update_option( 'rlm_version_' . Vpp_Base::$name, VPP_VERSION );
		}
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-draggable' );

		return;
	}

	// NOT admin
	// CSS
	wp_register_style( 'vpp_css', VPP_CSS_URL );
	wp_enqueue_style( 'vpp_css' );
	wp_register_style( 'svms-styles', SVMS_CSS );
	wp_enqueue_style( 'svms-styles' );


	// JS
	wp_enqueue_script( 'jquery' );
	wp_deregister_script( 'vpp_player' );
	wp_register_script( 'vpp_player', VPP_JS_URL );
	wp_enqueue_script( 'vpp_player' );

	// Vimeo plugin
	wp_deregister_script( 'vpp_vimeo_url' );
	wp_register_script( 'vpp_vimeo_url', VPP_VIMEO_URL );
	wp_enqueue_script( 'vpp_vimeo_url' );

	// YouTube plugin
	wp_deregister_script( 'vpp_player_youtube' );
	wp_register_script( 'vpp_player_youtube', VPP_YT_URL );
	wp_enqueue_script( 'vpp_player_youtube' );
	wp_register_script( 'vpp_video_ads', VPP_VADS_URL );
	wp_enqueue_script( 'vpp_video_ads' );
	wp_register_script( 'vpp_video_preroll', VPP_VPREROLL_URL );
	wp_enqueue_script( 'vpp_video_preroll' );
	wp_register_script( 'vpp_video_postroll', VPP_VPOSTROLL_URL );
	wp_enqueue_script( 'vpp_video_postroll' );
	wp_register_style( 'vpp_video_ads_css', VPP_VADS_CSS_URL );
	wp_enqueue_style( 'vpp_video_ads_css' );
	wp_register_style( 'vpp_video_preroll_css', VPP_VPREROLL_CSS_URL );
	wp_enqueue_style( 'vpp_video_preroll_css' );
}

if ( is_admin() ) {
	// Do admin stuff
	function Vpp_loadPlugin() {
		if ( getArrayItem( $_REQUEST, 'page' ) === 's3_video_player_plus' ) {
			include_once VPP_APP_PATH . '/Vpp_Admin.php';
			Vpp_Admin::init();
			if ( version_compare( VPP_VERSION, get_option( 'rlm_current_version_' . Vpp_Base::$name ) ) == 1 ) {
				update_option( 'rlm_current_version_' . Vpp_Base::$name, VPP_VERSION );
			}
		}
	}

	function Vpp_displayView() {
		include_once VPP_APP_PATH . '/Vpp_Admin.php';
		Vpp_Admin::displayView();
		$VPP_valid_update = get_option( 'VPP_valid_update', '' );
		if ( $VPP_valid_update === '' || $VPP_valid_update < VPP_VALID_UPDATE_I ) {
			update_option( 'VPP_valid_update', VPP_VALID_UPDATE_I );
			$mnar    = array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14 );
			$element = get_option( 'svms_CRM_ARM_OPT' );
			if ( is_numeric( $element ) ) {
				if ( in_array( $element, $mnar ) ) {
					update_option( 'svms_CRM_ARM_OPT', '' );
				}
			}
			Vpp_valid_update();
		}

	}

	/**
	 * This function runs updates in DB if this plugin is flagged to be updated.
	 */
	function Vpp_valid_update() {
		include_once VPP_APP_PATH . '/Vpp_Base.php';
		global $wpdb;
		//if(VPP_VALID_UPDATE_I<=7.3){
		$wpdb->query( "CREATE TABLE IF NOT EXISTS   `" . Vpp_Base::$table['video_grids'] . "` ( `id` INT NOT NULL AUTO_INCREMENT , `type` INT NOT NULL , `width` INT NOT NULL , `cols` INT NOT NULL ,  `code` TEXT NOT NULL , `title` varchar(230),`created` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "`  ADD `skin_type` INT NOT NULL AFTER `modified`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `custom_play` INT NOT NULL AFTER `html_position` , ADD `video_start_time` INT NOT NULL AFTER `custom_play` , ADD `video_end_time` INT NOT NULL AFTER `video_start_time` ;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `optin_gate` INT NOT NULL AFTER `thumbnail_url` , ADD `optin_start_time` VARCHAR( 100 ) NOT NULL AFTER `optin_gate` , ADD `optin_gate_code` TEXT NOT NULL AFTER `optin_start_time` ;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `pl_background` VARCHAR( 50 ) NOT NULL AFTER `optin_gate_code` ;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `vimeo_url` TEXT NOT NULL AFTER `ogg_url` ;" );
		$wpdb->query( 'ALTER TABLE `' . Vpp_Base::$table['video'] . '` ADD `group_id` INT NOT NULL AFTER `splash_url` ;' );
		$wpdb->query( 'CREATE TABLE IF NOT EXISTS `' . Vpp_Base::$table['video_groups'] . '` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `name` varchar(254) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;' );
		$wpdb->query( 'ALTER TABLE `' . Vpp_Base::$table['video_location'] . '` ADD `vid_status` INT NOT NULL AFTER `post_id` ;' );
		/*** Update 6.8 END ***/
		$wpdb->query( "CREATE TABLE IF NOT EXISTS  `" . Vpp_Base::$table['video_ips'] . "` ( `id` INT NOT NULL AUTO_INCREMENT , `video_id` INT NOT NULL , `ip_address` VARCHAR(50) NOT NULL , `city` VARCHAR(150) NOT NULL , `c_code` VARCHAR(100) NOT NULL , `c_name` VARCHAR(200) NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;" );
		$wpdb->query( "CREATE TABLE `" . Vpp_Base::$table['video_useragents'] . "` ( `id` INT NOT NULL AUTO_INCREMENT , `video_id` INT NOT NULL , `user_agent` VARCHAR(254) NOT NULL , `play_date` VARCHAR(50) NOT NULL , `plays` INT NOT NULL , `is_mobile` INT NOT NULL , `is_desktop` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;" );
		$wpdb->query( "CREATE TABLE `" . Vpp_Base::$table['video_stats'] . "` ( `id` INT NOT NULL AUTO_INCREMENT , `video_id` INT NOT NULL , `plays` INT NOT NULL , `completed_plays` INT NOT NULL , `play_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = MyISAM;" );
		$wpdb->query( "CREATE TABLE `" . Vpp_Base::$table['video_drops'] . "` ( `id` INT NOT NULL AUTO_INCREMENT , `video_id` INT NOT NULL , `drop_off` VARCHAR(100) NOT NULL , `video_time` VARCHAR(100) NOT NULL , `rand` VARCHAR(60) NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;" );
		/****7.4***/
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `tag_id` INT NOT NULL AFTER `pl_background`, ADD `aw_list_id` INT NOT NULL AFTER `tag_id`, ADD `getResponse_id` VARCHAR(50) NOT NULL AFTER `aw_list_id`, ADD `mailchimp_listid` VARCHAR(30) NOT NULL AFTER `getResponse_id`, ADD `activecamp_id` INT NOT NULL AFTER `mailchimp_listid`, ADD `convertkit_tagid` INT NOT NULL AFTER `activecamp_id`, ADD `markethero_tag` VARCHAR(150) NOT NULL AFTER `convertkit_tagid`, ADD `drip_tag` VARCHAR(200) NOT NULL AFTER `markethero_tag`, ADD `icontact_listid` INT NOT NULL AFTER `drip_tag`, ADD `ontraport_tag` VARCHAR(150) NOT NULL AFTER `icontact_listid`, ADD `c_contact_id` TEXT NOT NULL AFTER `ontraport_tag`, ADD `sendy_listid` INT NOT NULL AFTER `c_contact_id`, ADD `sendlane_tagid` INT NOT NULL AFTER `sendy_listid`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `tagging_enable` INT NOT NULL AFTER `optin_gate_code`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `optin_start` INT NOT NULL AFTER `sendlane_tagid`;" );
		//}
		$wpdb->query( "ALTER TABLE  `" . Vpp_Base::$table['video'] . "` ADD `arpreach_list` VARCHAR(200) NOT NULL AFTER `sendy_listid`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `is_wait` INT NOT NULL AFTER `optin_start`;" );
		//////************///////
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `scroll_options` INT NOT NULL AFTER `skin_type`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `is_download` INT NOT NULL AFTER `scroll_options`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `dwn_text_color` VARCHAR(10) NOT NULL AFTER `is_download`, ADD `dwn_back_color` VARCHAR(10) NOT NULL AFTER `dwn_text_color`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `vid_source` INT NOT NULL AFTER `handle`, ADD `vid_url` TEXT NOT NULL AFTER `vid_source`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `end_url` TEXT NOT NULL AFTER `is_download`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `tags` TEXT NOT NULL AFTER `name`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `crm_id` INT NOT NULL AFTER `tagging_enable`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video_grids'] . "` ADD `rand_key` INT NOT NULL AFTER `title`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `enable_layer` INT NULL DEFAULT '0' AFTER `end_url`, ADD `layer_content` TEXT NULL DEFAULT NULL AFTER `enable_layer`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `layer_txt_color` VARCHAR(20) NOT NULL AFTER `layer_content`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `layer_txt_size` VARCHAR(15) NULL DEFAULT NULL AFTER `layer_txt_color`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `layer_font` VARCHAR(80) NULL DEFAULT NULL AFTER `layer_txt_size`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `layer_img_url` VARCHAR(200) NOT NULL AFTER `layer_font`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `bg_color_ck` VARCHAR(20) NOT NULL AFTER `dwn_back_color`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `pre_roll_video_chk` VARCHAR(20) NOT NULL AFTER `bg_color_ck`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `pre_select_value` VARCHAR(20) NOT NULL AFTER `pre_roll_video_chk`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `post_roll_video_ck` VARCHAR(20) NOT NULL AFTER `pre_select_value`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `post_select_value` VARCHAR(20) NOT NULL AFTER `post_roll_video_ck`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `intros_outros_chk` VARCHAR(20) NOT NULL DEFAULT '0' AFTER `post_select_value`;" );
		$wpdb->query( "ALTER TABLE `" . Vpp_Base::$table['video'] . "` ADD `pause_overlay_image` TEXT NULL DEFAULT NULL AFTER `intros_outros_chk`;" );
		//ALTER TABLE `wp_vpp_video` ADD `intros_outros_chk` INT(20) NOT NULL DEFAULT '0' AFTER `post_select_value`;
		//$wpdb->query("ALTER TABLE `".Vpp_Base::$table['video']."` ADD `bg_color_ck` VARCHAR(20) NOT NULL DEFAULT '0' AFTER `dwn_back_color`, ADD `pre_roll_video_chk` VARCHAR(20) NOT NULL DEFAULT '0' AFTER `bg_color_ck`, ADD `pre_select_value` VARCHAR(20) NOT NULL DEFAULT '0' AFTER `pre_roll_video_chk`, ADD `post_roll_video_ck` VARCHAR(20) NOT NULL DEFAULT '0' AFTER `pre_select_value`, ADD `post_select_value` VARCHAR(20) NOT NULL DEFAULT '0' AFTER `post_roll_video_ck`;");
	}

	function Vpp_addAdminMenu() {
		add_menu_page(
			'Simple Video',
			'Simple Video',
			'administrator',
			's3_video_player_plus',
			'Vpp_displayView',
			VPP_PLUGIN_URL . '/includes/icons/movie.png'
		);
	}

	function Vpp_activatePlugin( $network_wide ) {
		include_once VPP_APP_PATH . '/Vpp_Admin.php';
		Vpp_Admin::init();
		Vpp_Admin::upgradePlugin( $network_wide );
	}

	add_action( 'wp_loaded', 'Vpp_loadPlugin', 1 );
	add_action( 'admin_menu', 'Vpp_addAdminMenu' );
	register_activation_hook( __FILE__, 'Vpp_activatePlugin' );
	if ( ! defined( 'VPP_CURRENT_PAGE' ) ) {
		define( 'VPP_CURRENT_PAGE', basename( $_SERVER['PHP_SELF'] ) );
	}
	if ( function_exists( 'members_get_capabilities' ) ) {
		add_filter( 'members_get_capabilities', 'svms_extra_caps' );
	}
	function svms_extra_caps( $caps ) {
		$caps[] = 'ntk_svms_add_videos';
		$caps[] = 'ntk_svms_edit_videos';

		return $caps;
	}

	function Vpp_formButton() {

		$arr = array( "simple_auto_webinar" );
		if ( isset( $_GET['page'] ) ) {
			if ( in_array( $_GET['page'], $arr ) ) {
				return;
			}
		}
		echo '
		<a href="#TB_inline?width=500&height=650&inlineId=vpp_form_popup" class="thickbox button vpp_edit_link" id="add_gform">
			<span class="vpp_edit_icon"></span>
			Add Video
		</a>';
	}

	function Vpp_formPopup() {
		$load_ball_url = plugins_url( '/includes/icons/loader-ball.gif', __FILE__ );
		//$video_save_url = admin_url('admin-ajax.php').'?action=s3_video_player_plus&do=video_save_quick';
		$video_save_url = admin_url( 'admin-ajax.php' ) . '?action=s3_video_player_plus&do=video_save';
		require dirname( __FILE__ ) . '/includes/media_form.php';
	}

	add_action( 'media_buttons', 'Vpp_formButton', 25 );
	//if (in_array(VPP_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php')))
	//{
	add_action( 'admin_footer', 'Vpp_formPopup' );
	//}

	add_action( 'admin_init', 'vpp_enqueueScripts' );
} else {
	function Vpp_handleShortcodes( $atts ) {
		// ob_start();
		global $wpdb, $post;
		wp_enqueue_script( 'svms-shortcodes', plugins_url( '/includes/svms-shortcodes.js', __FILE__ ),[],'1.0',true );
		include_once VPP_APP_PATH . '/Vpp_Base.php';
		Vpp_Base::initBase();
		$code            = '';
		$video_details = Vpp_Base::getVideoCode( $atts['id'], '', '', getArrayItem( $atts, 'plugin' ) );
		$code .= $video_details['code'];
		$video = $video_details['video'];
		$replace_query = $wpdb->prepare(
			'REPLACE INTO ' . Vpp_Base::$table['video_location'] . ' (video_id, post_id) VALUES (%d, %d)',
			$video['video_id'], $post->ID
		);
		$wpdb->query( $replace_query );
		return $code;
	}

	function Vpp_handleGridShortcode( $atts ) {
	    global $wpdb, $post;
		include_once VPP_APP_PATH . '/Vpp_Base.php';
		Vpp_Base::initBase();

				$video_id_list = array();
				$videos        = explode( ',', $atts['videos'] );
				foreach ( $videos as $video_id ) {
					$video_id = (int) $video_id;
					if ( $video_id ) {
						$video_id_list[] = $video_id;
					}
				}

				$cols  = (int) @$atts['cols'];
				$width = (int) @$atts['width'];
				if ( $cols == 0 || $cols == '' ) {
					$cols = 1;
				}
				$v = @ceil( @round( 100 / $cols, 2 ) );
				$v = $v - 1.5;
				if ( $width == '' ) {
					$width = 3;
				}
				if ( $width >= 3 ) {
					$w = $width / 10;
					$v = $v - $w;
					$v = $v - 1.2;
				}
				if ( $width > 6 ) {
					$width = $width - 3;
				}
				$svm_grid_widget_v = "width: {$v}%; margin: {$width}px;";

				$col = 0;
				if ( $video_id_list ) {
					$code  = "<div style='width: 100%; height: auto; margin: auto; padding: 5px;'>";
					$sizes = array();
					if ( $cols == 2 ) {
						$sizes['w'] = '565';
						$sizes['h'] = '318';
					} else if ( $cols == 3 ) {
						$sizes['w'] = '400';
						$sizes['h'] = '225';
					} else if ( $cols == 4 ) {
						$sizes['w'] = '380';
						$sizes['h'] = '214';
					}
					//TODO: remove the * fields and get only the necessary fields
					foreach ( $video_id_list as $video_id ) {
						$video = $wpdb->get_row( $wpdb->prepare('SELECT video_id, handle FROM ' . Vpp_Base::$table['video'] . ' WHERE video_id = %d' ,[ (int) $video_id]), ARRAY_A );
						$code .= "<div class='svm_grid_widget_v' style='".esc_attr($svm_grid_widget_v)."'>" . Vpp_Base::getVideoCodeGrid( $video['handle'], $sizes ) . "</div>\n";
						$col ++;
						if ( $col == (int) $atts['cols'] ) {
							$col = 0;
						}
						$wpdb->query($wpdb->prepare('REPLACE INTO ' . Vpp_Base::$table['video_location'] . ' (video_id, post_id) VALUES (%d, %d)',[(int) $video['video_id'],(int) $post->ID,]));
					}
					$code .= "</div>";
					$code .= "<div style='clear: both;'></div>";
					if ( (int) @$atts['id'] && (int) @$post->ID ) {
						$id      = 2;
						$get_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . Vpp_Base::$table['video_location'] . " WHERE video_id='%d' and post_id='%d' and vid_status='%d'",[(int) $atts['id'],(int) $post->ID,$id]) , ARRAY_A );
						if ( count( $get_row ) == 0 ) {
							$wpdb->query($wpdb->prepare('REPLACE INTO ' . Vpp_Base::$table['video_location'] . ' (video_id,post_id,vid_status ) VALUES ( %d , %d, %d)',[(int) $atts['id'], (int) $post->ID, $id])  );
						}
					}
				}


		return $code;
	}


	function Vpp_addShortcodeLast() {
		add_shortcode( 's3vpp', 'Vpp_handleShortcodes' );
		add_shortcode( 's3_video_player_plus', 'Vpp_handleShortcodes' );
		add_shortcode( 'svms_grid', 'Vpp_handleGridShortcode' );
	}

	add_action( 'wp_loaded', 'Vpp_addShortcodeLast' );
	function Quiz_add_head() {
		wp_enqueue_script('vimeo-one', VPP_VIMEO_ONE);
		wp_enqueue_script('vimeo-two', VPP_VIMEO_TWO);
	}

	add_action( "wp_head", "Quiz_add_head" );

	add_action( 'wp_enqueue_scripts', 'vpp_enqueueScripts' );
	function vpp_wp_head() {
		$swf_url       = VPP_SWF_URL;
		$VPP_JS_URL    = VPP_JS_URL;
		$VPP_VIMEO_URL = VPP_VIMEO_URL;
		$VPP_YT_URL    = VPP_YT_URL;
		$VPP_CSS_URL   = VPP_CSS_URL;
		wp_add_inline_script('svms-scripts', " videojs.options.flash.swf = '{$swf_url}';");
	}

	
	add_action( 'wp_head', 'vpp_wp_head' );
}

function Vpp_Ajax_Action() {
	require_once VPP_APP_PATH . '/Vpp_Ajax.php';
	Vpp_Ajax::init();
	if ( ! @$_REQUEST['do'] ) {
		$_REQUEST['do'] = 'remote';
	}
	Vpp_Ajax::doAjax( sanitize_text_field($_REQUEST['do']));
	exit();
}

function getUniqueHandle( $seed = null ) {
	global $wpdb;
	$table    = $wpdb->prefix . "vpp_video";
	$seed     = md5( $seed . serialize( $_SERVER ) . mt_rand() );
	$video_id = $wpdb->get_var( $wpdb->prepare('SELECT * FROM ' . $table . ' WHERE handle = "%s"' ,[$seed]));
	if ( (int) $video_id ) {
		$seed = getUniqueHandle( $seed );
	}

	return $seed;
}

function Vpp_Ajax_new() {
	include_once VPP_APP_PATH . '/Vpp_Base.php';

	if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'svms_lightbox' ) {
		include_once VPP_APP_PATH . '/Vpp_Base.php';
		Vpp_Base::initBase();
		$id      = sanitize_text_field($_REQUEST['id']);
		$rand_id = sanitize_text_field($_REQUEST['rand_id']);
		echo "<center>" . Vpp_Base::getVideoLightBoxCodeGrid( $id, array(), $rand_id ) . "</center>";
	}
	die();
}

function vpp_vide() {
	include_once( 'raw.php' );
	die();
}

add_action( 'upgrader_process_complete', 'p_c_fun' );
function p_c_fun() {
	// inspect $options
	$plug = get_plugins();
	if ( array_key_exists( "autoptimize/autoptimize.php", $plug ) ) {
		if ( is_plugin_active( 'autoptimize/autoptimize.php' ) ) {
			$autoptimize_js_exclude = get_option( "autoptimize_js_exclude" );
			$is_update_opt          = get_option( "autoptimize_is_update_opt_svms" );
			if ( $is_update_opt == "" ) {
				$autoptimize_js_exclude = $autoptimize_js_exclude . ",video.min.js,Youtube.js,Vimeo.js";
				update_option( "autoptimize_js_exclude", $autoptimize_js_exclude );
				update_option( "autoptimize_is_update_opt_svms", "Done" );
			}
		}
	}
}

add_action( 'wp_ajax_s3_video_player_plus', 'Vpp_Ajax_Action' );
add_action( 'wp_ajax_nopriv_s3_video_player_plus', 'Vpp_Ajax_Action' );
add_action( 'wp_ajax_svm_copy_video', 'Vpp_Ajax_new' );
add_action( 'wp_ajax_svms_lightbox', 'Vpp_Ajax_new' );
add_action( 'wp_ajax_nopriv_svms_lightbox', 'Vpp_Ajax_new' );
add_action( "wp_ajax_vpp_saveGrid", "vpp_ajax_grid" );
add_action( "wp_ajax_nopriv_vpp_saveGrid", "vpp_ajax_grid" );
add_action( "wp_ajax_get_playlistVideo", 'vpp_ajax_grid' );
add_action( "wp_ajax_nopriv_get_playlistVideo", "vpp_ajax_grid" );
add_action( "wp_ajax_svms_track_video_analytics", "vpp_analytic_ajax" );
add_action( "wp_ajax_nopriv_svms_track_video_analytics", "vpp_analytic_ajax" );
add_action( "wp_ajax_svms_crm_arm_opt", "vpp_ajax_grid" );
add_action( "wp_ajax_export_svms_vids", "vpp_ajax_grid" );
add_action( "wp_ajax_saveGroup_svms", "vpp_ajax_grid" );
add_action( "wp_ajax_svms_optin_subscribe", "vpp_analytic_ajax" );
add_action( "wp_ajax_nopriv_svms_optin_subscribe", "vpp_analytic_ajax" );

function vpp_analytic_ajax() {
	include_once( "ajax.php" );
	die();
}

function vpp_ajax_grid() {
	include_once VPP_APP_PATH . '/Vpp_Base.php';
	global $wpdb;
	if ( 
		! isset( $_POST['_ajax_nonce'] ) 
		|| ! wp_verify_nonce( $_POST['_ajax_nonce'], 'grid_add_edit' ) 
	) {
	 
	   print 'Sorry, your nonce did not verify.';
	   exit;
	
	}

	if(!current_user_can('administrator')){
		print 'Sorry, this user does not have admin privileges';
		exit;
	 
	}

	if ( isset( $_REQUEST['action'] ) ) {

		if ( $_REQUEST['action'] == "vpp_saveGrid" ) {
			$a     = html_entity_decode( $_REQUEST['title'] );
			$title = trim( htmlspecialchars( $a, ENT_QUOTES ) );
			unset( $_REQUEST['action'] );
			$db = $wpdb->base_prefix . 'vpp_video_grids';
			if ( ! isset( $_REQUEST['id'] ) ) {
				$code = sanitize_text_field($_REQUEST['code']);
				$cols = sanitize_text_field($_REQUEST['cols']);
				$width = sanitize_text_field($_REQUEST['width']);
				$type = sanitize_text_field($_REQUEST['type']);
				$rand_key = sanitize_text_field($_REQUEST['rand_key']);
			
				$insert = $wpdb->query($wpdb->prepare("INSERT into ". $db . "(code,cols,width,type,title,rand_key) values('%d','%d','%d','%d','%s','%d')",[$code, $cols, $width, $type, $title, $rand_key]));

				if ( $insert ) {
					echo 1;
				} else {
					echo 0;
				}
			} else {
				$gid = sanitize_text_field($_REQUEST['id']);
				unset( $_REQUEST['id'] );
				unset( $_REQUEST['title'] );
				$_REQUEST['title'] = $title;
				$code = sanitize_text_field($_REQUEST['code']);
				$type = sanitize_text_field($_REQUEST['type']);
				$title = sanitize_text_field($_REQUEST['title']);
				//$update = $wpdb->update($db,$_REQUEST,array('id'=>$gid));
				$update = $wpdb->query( $wpdb->prepare("UPDATE $db SET code= %s, type=%s, title=%s WHERE id=%d", [$code,$type,$title,$gid]));

				if ( false === $update ) {
					echo 0;
				} else {
					echo 1;
				}
			}
		}
		/***********/
		if ( getArrayItem( $_REQUEST, 'action' ) == 'get_playlistVideo' ) {
			Vpp_Base::initBase();
			$handle = $_REQUEST['handle'];
			$code   = Vpp_Base::video_playlists( $handle );
			echo $code;
		}
		if ( getArrayItem( $_REQUEST, 'action' ) == "svms_crm_arm_opt" ) {
			update_option( "svms_CRM_ARM_OPT", 0 );
			echo 1;
		}
		if ( getArrayItem( $_REQUEST, 'action' ) == "saveGroup_svms" ) {
			$group = $wpdb->base_prefix . 'vpp_groups';
			$name  = sanitize_text_field($_REQUEST['name']);
			$a     = html_entity_decode( $name );
			$name  = trim( htmlspecialchars( $a, ENT_QUOTES ) );
			$wpdb->insert( $group, array( "name" => $name ) );
			echo $wpdb->insert_id;
		}
	}
	die();
}

$domains_list = array( 'informyes.com', 'www.recoveryes.com', 'recoveryes.com' );
if ( in_array( $_SERVER['HTTP_HOST'], $domains_list ) ) {
	remove_filter( 'the_content', 'wpautop' );
	remove_filter( 'the_content', 'wptexturize' );
}
