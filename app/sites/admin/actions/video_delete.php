<?php
if(!is_user_logged_in() || !current_user_can('administrator') ) {
	header('location: admin.php?page='.esc_attr(self::$name).'&action=videos');
	exit();
}

if (isset($_GET['video_id']) && intval($_GET['video_id']))
{

    $c_id = get_current_user_id();
    $user_data_login = get_userdata($c_id);
    $user_role = $user_data_login->roles[0];
    if($user_role=="administrator"){
    	$wpdb->query(
            $wpdb->prepare(
                'DELETE FROM '.self::$table['video'].' WHERE video_id=%d',
                [sanitize_text_field($_GET['video_id'])]
            )
        );
    }

}
header('location: admin.php?page='.esc_attr(self::$name).'&action=videos');
exit();