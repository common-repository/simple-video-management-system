<?php
require_once 'Vpp_Base.php';
class Vpp_Admin extends Vpp_Base
{
	public static $form_vars	= array();
	public static $form_errors	= array();
	public static $bulk_errors	= array();
	public static function init()
	{
		self::initBase();
        if (!defined('VPP_SITE_PATH'))
		{
		  define('VPP_SITE_PATH',	VPP_APP_PATH.'/sites/admin');
        }
		add_action('admin_init',	array('Vpp_Admin', 'doBeforeHeaders'), 1);
        add_action('admin_enqueue_scripts', ['Vpp_Admin','addToHead']);
        switch($_GET['action']) {
            case 'video_analytics':
                add_action('admin_enqueue_scripts', ['Vpp_Admin','add_video_analytics_files']);
                break;
            case 'video_edit':
                add_action('admin_enqueue_scripts', ['Vpp_Admin', 'add_video_edit_files']);
                break;
            case 'grid_shortcode':
                add_action('admin_enqueue_scripts', ['Vpp_Admin', 'add_grid_shortcode_js']);
                break;
            default :
                break;
        }
	}
	public static function doBeforeHeaders()
	{
		if ($_GET['page'] == self::$name)
		{
			global $wpdb;
			$config = unserialize(get_option('rlm_config_'.self::$name));
			if (isset($_POST['form_vars']))
			{
				$_POST['form_vars'] = self::array_stripslashes($_POST['form_vars']);
				self::$form_vars = $_POST['form_vars'];
			}
			if (isset($_GET['action']))
			{
				self::$action = preg_replace('/[^0-9a-zA-Z\_\-]+/is', '', strtolower($_GET['action']));
			}
			if (!self::$action)
			{
				self::$action = 'videos';
			}
			$file_path = VPP_SITE_PATH.'/actions/default.php';
			if (is_file($file_path))
			{
				require $file_path;
			}
			$file_path = VPP_SITE_PATH.'/actions/'.self::$action.'.php';
			if (is_file($file_path))
			{
				require $file_path;
			}
		}
	}
	public static function addToHead()
	{
        wp_register_style( 's3_admin_css', self::$plugin_url.'includes/style_admin.css', false, '1.0.0' );
        wp_register_style( 's3_svms_css', self::$plugin_url.'includes/svms.css', false, '1.0.0' );
        wp_enqueue_style( 's3_admin_css' );
        wp_enqueue_style( 's3_svms_css' );
    }

    /**
     * Add CSS Files to the video Analytics section
     * @return mix
     */
    public static function add_video_analytics_files() {
        wp_register_style( 's3_morris_css', VPP_PLUGIN_URL.'includes/morris.css', false, '1.0.0' );
        wp_register_style( 's3_chart_css', VPP_PLUGIN_URL.'includes/chart.css', false, '1.0.0' );
        wp_enqueue_style( 's3_morris_css' );
        wp_enqueue_style( 's3_chart_css' );
        wp_enqueue_script( 'google_jsapi',VPP_PLUGIN_URL .'includes/jsapi.js' );
        wp_localize_script('google_jsapi', 'routes', array(
            'plugin_url' => VPP_PLUGIN_URL,
        ));
        wp_enqueue_script( 'raphael-min',VPP_PLUGIN_URL.'includes/raphael-min.js' );
        wp_enqueue_script( 'morris',VPP_PLUGIN_URL.'includes/morris.min.js' );
        wp_enqueue_script( 'navgoco',VPP_PLUGIN_URL.'includes/jquery.navgoco.min.js' );
        wp_enqueue_script( 'bootstrap',VPP_PLUGIN_URL.'includes/bootstrap.min.js' );
        wp_enqueue_script( 'application',VPP_PLUGIN_URL.'includes/application.js' );
        wp_enqueue_script( 'jquery-count',VPP_PLUGIN_URL.'includes/jquery.countTo.js' );
    }

    /**
     * Add css and JS Files to the Video Edit section
     * @return mix
     */
    public static function add_video_edit_files(){
        wp_register_style( 's3_spectrum', VPP_INCLUDE. '/color/spectrum.css', false, '1.0.0' );
        wp_enqueue_script( 'svms_shortcodes', VPP_INCLUDE . '/svms-shortcodes.js');
        wp_enqueue_script( 's3_spectrum_js', VPP_INCLUDE . '/color/spectrum.js');
        wp_enqueue_style( 's3_spectrum' );
    }

    /**
     * Add css and JS Files to the Video Shortcode Edit section
     * @return mix
     */

    public static function add_grid_shortcode_js  () {
        wp_enqueue_script( 'svms_scripts_footer', VPP_INCLUDE . '/svms-scripts.js', [], '1.0', true);
    }

	public static function displayView()
	{
		global $wpdb;
		$config = unserialize(get_option('rlm_config_'.self::$name));
		$layout_path = VPP_SITE_PATH.'/layouts/default.php';
		if (is_file($layout_path))
		{   
       
			$view_path = VPP_SITE_PATH.'/views/'.self::$action.'.php';
			if (!is_file($view_path))
			{
				exit('Invalid View: '.$view_path);
			}
			require($layout_path);
		}
		else
		{
			exit('Invalid Layout: '.$layout_path);
		}
	}
    public static  function instaLicenseValidate(){
       ?>
        <div style=" padding: 1%; width: 100%; max-width: 400px; margin: auto; border-radius: 5px; ">
        <div style="width: 100%; background-color: #565656; float: left; border-radius: 5px 5px 0px 0px;">
        </div>
        <div style="width: 100%;  float: left; border: solid 1px #ccc; box-shadow: 1px 1px 8px #ccc">
                <form method="post"  action="<?php echo esc_url(admin_url()); ?>admin.php?page=s3_video_player_plus">
                    <table cellspacing='10' width="100%">
                        <tr>
                            <td><b style="font-size: 22px;">License Key</b></td>
                        </tr>
                        <tr>
                            <td><input type="text" name="s3_license_key" style="width: 100%;"  required="" /><br />
                            <span style="float: left;"><em><i> Enter Valid License Key To Activate Plugin.</i></em></span></td>
                        </tr>

                        <tr>
                            <td>
                                <input type="hidden" name="cmd" value="s3_validate_license" />
                                <p style="text-align: center;"><input type="submit" value="Activate" style=" cursor: pointer; padding: 1% 5% 1% 5%;background-color: #565656; color: #fff; font-size: 20px; font-weight: bold; border: none; border-radius: 5px;"  /></p>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            </div>
        <?php
    }



	public static function upgradePlugin($network_wide)
	{
		global $wpdb;
		if (file_exists(ABSPATH.'wp-admin/includes/upgrade.php'))
		{
			require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		}
		else
		{
			require_once(ABSPATH.'wp-admin/upgrade-functions.php');
		}
      if (is_multisite()) {
        if($network_wide){
    		  global $wpdb;

            foreach ($wpdb->get_results("SELECT blog_id FROM ".$wpdb->prefix."blogs",ARRAY_A) as $k=>$blog_id) {
                switch_to_blog($blog_id['blog_id']);


                $wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'vpp_video'.'` (
    			`video_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    			`name` varchar(255),
    			`handle` varchar(32),
    			`mp4_url` text,
    			`youtube_url` text,
    			`webm_url` text,
    			`ogg_url` text,
    			`splash_url` text,
    			`width` int(11) unsigned NOT NULL default 565,
    			`height` int(11) unsigned NOT NULL default 423,
    			`ratio` decimal(10,4) unsigned NOT NULL,
    			`align` varchar(32),
    			`auto_play` tinyint(1) unsigned NOT NULL,
    			`hide_controls` tinyint(1) unsigned NOT NULL,
    			`loop_video` tinyint(1) unsigned NOT NULL,
    			`show_html_seconds` int(11) unsigned NOT NULL,
    			`show_html` text,
    			`redirect_url` text,
    			`created` int(11) unsigned NOT NULL,
    			`modified` int(11) unsigned NOT NULL,
    			PRIMARY KEY (`video_id`),
    			KEY `idx_handle` (`handle`)
    		);');
    		$wpdb->query('CREATE TABLE IF NOT EXISTS  `'.$wpdb->prefix.'vpp_video_location'.'` (
    			`video_id` int(11) unsigned NOT NULL,
    			`post_id` int(11) unsigned NOT NULL,
    			UNIQUE KEY `idx_video_post` (`video_id`,`post_id`)
    		);');
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `size_type` INT NOT NULL AFTER `splash_url`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'."  ADD `mov_url` TEXT NOT NULL AFTER `ogg_url`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `lightbox_enabled` INT NOT NULL AFTER `redirect_url`, ADD `thumbnail_url` TEXT NOT NULL AFTER `lightbox_enabled`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `static_html` INT NOT NULL AFTER `loop_video`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `use_splash` INT NOT NULL AFTER `redirect_url`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `static_html_code` TEXT NOT NULL AFTER `loop_video`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `html_position` INT NOT NULL AFTER `show_html_seconds`;");

                restore_current_blog();
            }
            }
        }else{

            $wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'vpp_video'.'` (
    			`video_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    			`name` varchar(255),
    			`handle` varchar(32),
    			`mp4_url` text,
    			`youtube_url` text,
    			`webm_url` text,
    			`ogg_url` text,
    			`splash_url` text,
    			`width` int(11) unsigned NOT NULL default 565,
    			`height` int(11) unsigned NOT NULL default 423,
    			`ratio` decimal(10,4) unsigned NOT NULL,
    			`align` varchar(32),
    			`auto_play` tinyint(1) unsigned NOT NULL,
    			`hide_controls` tinyint(1) unsigned NOT NULL,
    			`loop_video` tinyint(1) unsigned NOT NULL,
    			`show_html_seconds` int(11) unsigned NOT NULL,
    			`show_html` text,
    			`redirect_url` text,
    			`created` int(11) unsigned NOT NULL,
    			`modified` int(11) unsigned NOT NULL,
    			PRIMARY KEY (`video_id`),
    			KEY `idx_handle` (`handle`)
    		);');
    		$wpdb->query('CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'vpp_video_location'.'` (
    			`video_id` int(11) unsigned NOT NULL,
    			`post_id` int(11) unsigned NOT NULL,
    			UNIQUE KEY `idx_video_post` (`video_id`,`post_id`)
    		);');
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `size_type` INT NOT NULL AFTER `splash_url`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'."  ADD `mov_url` TEXT NOT NULL AFTER `ogg_url`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `lightbox_enabled` INT NOT NULL AFTER `redirect_url`, ADD `thumbnail_url` TEXT NOT NULL AFTER `lightbox_enabled`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `static_html` INT NOT NULL AFTER `loop_video`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `use_splash` INT NOT NULL AFTER `redirect_url`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `static_html_code` TEXT NOT NULL AFTER `loop_video`;");
            $wpdb->query("ALTER TABLE ".$wpdb->prefix.'vpp_video'." ADD `html_position` INT NOT NULL AFTER `show_html_seconds`;");
        }
	}

    public static function upgrade_versions()
	{
	   global $wpdb;
        //if(VPP_VALID_UPDATE_I<=7.3){
        $wpdb->query("CREATE TABLE  IF NOT EXISTS `".Vpp_Base::$table['video_grids']."` ( `id` INT NOT NULL AUTO_INCREMENT , `type` INT NOT NULL , `width` INT NOT NULL , `cols` INT NOT NULL ,  `code` TEXT NOT NULL , `title` varchar(230),`created` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
        $wpdb->query("ALTER TABLE `".Vpp_Base::$table['video']."`  ADD `skin_type` INT NOT NULL AFTER `modified`;");
        $wpdb->query("ALTER TABLE `".Vpp_Base::$table['video']."` ADD `custom_play` INT NOT NULL AFTER `html_position` , ADD `video_start_time` INT NOT NULL AFTER `custom_play` , ADD `video_end_time` INT NOT NULL AFTER `video_start_time` ;");
        $wpdb->query("ALTER TABLE `".Vpp_Base::$table['video']."` ADD `optin_gate` INT NOT NULL AFTER `thumbnail_url` , ADD `optin_start_time` VARCHAR( 100 ) NOT NULL AFTER `optin_gate` , ADD `optin_gate_code` TEXT NOT NULL AFTER `optin_start_time` ;");
        $wpdb->query("ALTER TABLE `".Vpp_Base::$table['video']."` ADD `pl_background` VARCHAR( 50 ) NOT NULL AFTER `optin_gate_code` ;");
        $wpdb->query("ALTER TABLE `".Vpp_Base::$table['video']."` ADD `vimeo_url` TEXT NOT NULL AFTER `ogg_url` ;");

        $wpdb->query('ALTER TABLE `'.Vpp_Base::$table['video'].'` ADD `group_id` INT NOT NULL AFTER `splash_url` ;');
        $wpdb->query('CREATE TABLE IF NOT EXISTS `'.Vpp_Base::$table['video_groups'].'` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `name` varchar(254) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
        $wpdb->query('ALTER TABLE `'.Vpp_Base::$table['video_location'].'` ADD `vid_status` INT NOT NULL AFTER `post_id` ;');
        /*** Update 6.8 END ***/
        $wpdb->query("CREATE TABLE IF NOT EXISTS `".Vpp_Base::$table['video_ips']."` ( `id` INT NOT NULL AUTO_INCREMENT , `video_id` INT NOT NULL , `ip_address` VARCHAR(50) NOT NULL , `city` VARCHAR(150) NOT NULL , `c_code` VARCHAR(100) NOT NULL , `c_name` VARCHAR(200) NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;");
        $wpdb->query("CREATE TABLE IF NOT EXISTS `".Vpp_Base::$table['video_useragents']."` ( `id` INT NOT NULL AUTO_INCREMENT , `video_id` INT NOT NULL , `user_agent` VARCHAR(254) NOT NULL , `play_date` VARCHAR(50) NOT NULL , `plays` INT NOT NULL , `is_mobile` INT NOT NULL , `is_desktop` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;");
        $wpdb->query("CREATE TABLE IF NOT EXISTS  `".Vpp_Base::$table['video_stats']."` ( `id` INT NOT NULL AUTO_INCREMENT , `video_id` INT NOT NULL , `plays` INT NOT NULL , `completed_plays` INT NOT NULL , `play_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = MyISAM;");
        $wpdb->query("CREATE TABLE IF NOT EXISTS `".Vpp_Base::$table['video_drops']."` ( `id` INT NOT NULL AUTO_INCREMENT , `video_id` INT NOT NULL , `drop_off` VARCHAR(100) NOT NULL , `video_time` VARCHAR(100) NOT NULL , `rand` VARCHAR(60) NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;");
        /****7.4***/

        $wpdb->query("ALTER TABLE `".Vpp_Base::$table['video']."` ADD `tag_id` INT NOT NULL AFTER `pl_background`, ADD `aw_list_id` INT NOT NULL AFTER `tag_id`, ADD `getResponse_id` VARCHAR(50) NOT NULL AFTER `aw_list_id`, ADD `mailchimp_listid` VARCHAR(30) NOT NULL AFTER `getResponse_id`, ADD `activecamp_id` INT NOT NULL AFTER `mailchimp_listid`, ADD `convertkit_tagid` INT NOT NULL AFTER `activecamp_id`, ADD `markethero_tag` VARCHAR(150) NOT NULL AFTER `convertkit_tagid`, ADD `drip_tag` VARCHAR(200) NOT NULL AFTER `markethero_tag`, ADD `icontact_listid` INT NOT NULL AFTER `drip_tag`, ADD `ontraport_tag` VARCHAR(150) NOT NULL AFTER `icontact_listid`, ADD `c_contact_id` TEXT NOT NULL AFTER `ontraport_tag`, ADD `sendy_listid` INT NOT NULL AFTER `c_contact_id`, ADD `sendlane_tagid` INT NOT NULL AFTER `sendy_listid`;");
        $wpdb->query("ALTER TABLE `".Vpp_Base::$table['video']."` ADD `tagging_enable` INT NOT NULL AFTER `optin_gate_code`;");
        $wpdb->query("ALTER TABLE `".Vpp_Base::$table['video']."` ADD `optin_start` INT NOT NULL AFTER `sendlane_tagid`;");
        //}
        $wpdb->query("ALTER TABLE  `".Vpp_Base::$table['video']."` ADD `arpreach_list` VARCHAR(200) NOT NULL AFTER `sendy_listid`;");
    }
}
