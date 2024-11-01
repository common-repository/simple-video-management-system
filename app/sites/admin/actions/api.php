<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (@$_REQUEST['method'])
{
	$_REQUEST['method'] = preg_replace('/[^a-zA-Z\_]+/is', '', sanitize_text_field($_REQUEST['method']));

	switch ($_REQUEST['method'])
	{
		case 'getVideoConfig':
			Svms_getVideoConfig($_REQUEST);
			break;

		case 'getWatchMore':
			Svms_getWatchMore($_REQUEST);
			break;

		default:
			die('Invalid Request');
			break;
	}
}

function Svms_getVideoConfig($request)
{
	global $wpdb;
	$handle = sanitize_text_field($request['handle']);
	$video = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.Vpp_Base::$table['video'].' WHERE handle = %s ',[addslashes($handle)]), ARRAY_A);

	$watch_more_list = explode(',', $video['watch_more']);
	
	if ((int)$video['height'] && (int)$video['width'])
	{
		$aspect_ratio = round($video['height'] / $video['width'], 2);
	}
	else
	{
		$aspect_ratio = 9 / 16;
	}

	$config = array(
	'title'				=> $video['name'],
	'preload'			=> 'metadata',
	'width'				=> 'auto',
	'height'			=> 'auto',
	'aspect_ratio'		=> $aspect_ratio,
	'poster'			=> $video['splash_url'] ? $video['splash_url'] : null,
	'controls'			=> $video['hide_controls'] ? false : true,
	'autoplay'			=> $video['auto_play'] ? true : false,
	'loop'				=> $video['loop_video'] ? true : false,
	'has_watch_more'	=> $watch_more_list ? true : false,
	'google_analytics'	=> get_option('aws_google_events', 0) ? true : false,
	'redirect_url'		=> $video['redirect_url'],
	'show_html_seconds'	=> (int)$video['show_html_seconds'],
	);

	$source_list = array();

	if ($video['mp4_url'])
	{
		$source_list[] = array('src' => Vpp_Base::getS3TempLink($video['mp4_url']), 'type' => 'video/mp4');
	}
	
	if ($video['youtube_url'])
	{
		$source_list[] = array('src' => $video['youtube_url'], 'type' => 'video/youtube');
	}

	if ($video['webm_url'])
	{
		$source_list[] = array('src' => Vpp_Base::getS3TempLink($video['webm_url']), 'type' => 'video/webm');
	}

	if ($video['ogg_url'])
	{
		$source_list[] = array('src' => Vpp_Base::getS3TempLink($video['ogg_url']), 'type' => 'video/ogg');
	}

	$config['src'] = $source_list;

	echo json_encode($config);
	exit();
}

function Svms_getWatchMore($request)
{
	global $wpdb;

	$video = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.Vpp_Base::$table['video'].' WHERE handle = %s ',[addslashes($request['handle'])]), ARRAY_A);

	$watch_more_list = explode(',', $video['watch_more']);

	if ($watch_more_list)
	{
		$video_list = array();
		
		$t_video_list = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.Vpp_Base::$table['video'].' WHERE video_id != %d ORDER BY name', [$video['video_id']]), ARRAY_A);
		foreach ($t_video_list as $video)
		{
			$video_list[$video['video_id']] = $video;
		}

		$html = '<div id="vpp_watch_more_'.esc_attr($request['handle']).'" class="vpp_watch_more">';

		foreach ($watch_more_list as $video_id)
		{
			$t_video = $video_list[$video_id];
				
			echo '<div class="vpp_watch_more_item" onclick="svms_swap_video(\''.esc_attr($request['id']).'\', \''.esc_attr($t_video['handle']).'\')"><img src="'.esc_attr($t_video['splash_url']).'" /></div>';
		}

		$html = '</div>';
	}

	exit($html);
}
