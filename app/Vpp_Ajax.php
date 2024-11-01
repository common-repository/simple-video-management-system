<?php
require_once 'Vpp_Base.php';
class Vpp_Ajax extends Vpp_Base
{
	public static $form_vars	= array();
	public static $form_errors	= array();
	public static function init()
	{
		self::initBase();
		if (!defined('VPP_SITE_PATH'))
		{
			define('VPP_SITE_PATH',	VPP_APP_PATH.'/sites/admin');
		}
	}
	public static function doAjax($action)
	{
		global $wpdb;
		if (isset($_POST['form_vars']))
		{
			$_POST['form_vars'] = self::array_stripslashes($_POST['form_vars']);
			self::$form_vars = sanitize_text_field($_POST['form_vars']);
		}
		$action = preg_replace('/[^a-z\_]+/is', '', $action);
		$action_path = VPP_APP_PATH.'/sites/admin/actions/default.php';
		if (is_file($action_path))
		{
			require $action_path;
		}
		$action_path = VPP_APP_PATH.'/sites/admin/actions/'.$action.'.php';
		if (is_file($action_path))
		{
			require $action_path;
		}
	}
}