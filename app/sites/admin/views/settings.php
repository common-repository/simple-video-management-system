<h2>Settings</h2>
<?php
require VPP_APP_PATH . '/Vpp_Form.php';

if ( isset( $_POST['cmd'] ) && $_POST['cmd'] == "crm_settings" ) {
	$crms     = get_option( "svms_CRM_ARM_OPT" );
	$arm_type = sanitize_text_field($_POST['arm_type']);
	if ( is_numeric( $crms ) ) {
		update_option( "svms_CRM_ARM_OPT", "" );
		$crms = "";
	}
	if ( $crms == "" ) {
		$narr_add = array( $arm_type => $arm_type );
		update_option( "svms_CRM_ARM_OPT", json_encode( $narr_add ) );
	} else {
		if ( isset( $_POST['api_yes'] ) && $_POST['api_yes'] == 1 ) {
			$mnar = json_decode( $crms, true );

			if ( ! in_array( $arm_type, $mnar ) ) {
				$mnar[ $arm_type ] = $arm_type;
				update_option( "svms_CRM_ARM_OPT", json_encode( $mnar ) );

			}
		} else {
			$mnar = json_decode( $crms, true );
			if ( in_array( $arm_type, $mnar ) ) {
				unset( $mnar[ $arm_type ] );
				update_option( "svms_CRM_ARM_OPT", json_encode( $mnar ) );
			}
		}
	}


	//update_option("svms_CRM_ARM_OPT",$_POST['arm_type']);

	if ( isset( $_POST['crm'] ) && $_POST['crm'] == "infusion" ) {
		if ( $_POST['key_inf'] != self::starApiKey( get_option( 'svms_infusion_soft' ) ) && $_POST['app_inf'] != self::starApiKey( get_option( 'svms_infusion_soft_app_name' ) ) ) {
			if ( isset( $_POST['api_yes'] ) && $_POST['api_yes'] == 1 ) {
				$key_inf = sanitize_text_field($_POST['key_inf']);
				$app_inf = sanitize_text_field($_POST['app_inf']);
				update_option( "svms_infusion_soft", trim( $key_inf ) );
				update_option( "svms_infusion_soft_app_name", trim( $app_inf ) );
			}
		}
	}
	if ( isset( $_POST['crm'] ) && $_POST['crm'] == "aweber" ) {

		if ( $_POST['svms_consumer_key_aweber'] != self::starApiKey( get_option( 'svms_consumer_key_aweber' ) ) && $_POST['svms_consumer_secret_aweber'] != self::starApiKey( get_option( 'svms_consumer_secret_aweber' ) ) ) {
			if ( isset( $_POST['api_yes'] ) && $_POST['api_yes'] == 1 ) {
				$svms_consumer_key_aweber = sanitize_text_field($_POST['svms_consumer_key_aweber']);
				$svms_consumer_secret_aweber = sanitize_text_field($_POST['svms_consumer_key_aweber']);
				update_option( "svms_consumer_key_aweber", trim( $svms_consumer_key_aweber ) );
				update_option( "svms_consumer_secret_aweber", trim( $svms_consumer_secret_aweber ) );
			}
		}
	}

	$_SESSION['setting_success'] = 1;
}


$element = json_decode( get_option( "svms_CRM_ARM_OPT" ), true );

$crm = get_option( "svms_CRM_ARM_OPT" );

$crms = array(
	0  => "Select CRM/AR",
	1  => "InfusionSoft",
	2  => "Aweber",
	3  => "GetResponse",
	4  => "MailChimp",
	5  => "ActiveCampaign",
	6  => "Convertkit",
	7  => "MarketHero",
	8  => "Drip",
	9  => "Sendlane",
	10 => "iContact",
	11 => "Ontraport",
	12 => "Constant Contact",
	13 => "Sendy",
	14 => "ArpReach"
);


?>

<hr/>
<?php
//include_once( SVMS_MICAHEL_FILE . 'Michelf/MarkdownExtra.inc.php' );
//include_once( 'file_read.php' );
if (is_file(SVMS_PATH.'/change.log'))
{
	echo '<br /><h2>Change Log</h2>';
	echo '<pre style="padding: 0 0 0 20px;">'.file_get_contents(SVMS_PATH.'/change.log').'</pre>';
}
?>