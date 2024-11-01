<?php
ob_end_clean();
header( "Content-type: text/javascript" );
$video_value = sanitize_text_field($_REQUEST['video']);
$video   = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . Vpp_Base::$table['video'] . ' WHERE handle = %s ', [$video_value]), ARRAY_A );

$video['v_randCode'] = sanitize_text_field(@$_REQUEST['rid']);
//if($_REQUEST['auto']==1){
if ( isset( $_REQUEST['auto'] ) ) {
	$video['auto_play'] = sanitize_text_field($_REQUEST['auto']);
}
//}

/*vitvis*/

$preloadcheck     = $video['pre_roll_video_chk'];
$preloadvideourl  = $video['pre_select_value'];
$postloadcheck    = $video['post_roll_video_ck'];
$postloadvideourl = $video['post_select_value'];
if ( $preloadcheck == 1 && $preloadvideourl != 0 && $preloadvideourl != "" ) {
	$preLoadVidData    = $wpdb->get_row($wpdb->prepare( 'SELECT vid_url, vid_source, video_id FROM ' . Vpp_Base::$table['video'] . ' WHERE video_id = %s ', [$preloadvideourl]), ARRAY_A );
	$preloadVideoSrc   = $preLoadVidData['vid_url'];
	$preaLoadVideoType = $preLoadVidData['vid_source'];
	if ( $preaLoadVideoType == 0 ) {
		$videoType = 'youtube';
	} elseif ( $preaLoadVideoType == 1 ) {
		$videoType = 'mp4';
	} elseif ( $preaLoadVideoType == 2 ) {
		$videoType = 'webm';
	} elseif ( $preaLoadVideoType == 3 ) {
		$videoType = 'ogg';
	} elseif ( $preaLoadVideoType == 4 ) {
		$videoType = 'mov';
	} elseif ( $preaLoadVideoType == 5 ) {
		$videoType = 'vimeo';
	}
	$srcpreload = 'player.preroll({ 
            src:{src:"' . $preloadVideoSrc . '",type:"video/' . $videoType . '"},
               });';
}

if ( $postloadcheck == 1 && $postloadvideourl != 0 && $postloadvideourl != "" ) {
	$postLoadVidData    = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . Vpp_Base::$table['video'] . ' WHERE video_id = %d ', [$postloadvideourl]), ARRAY_A );
	$postloadVideoSrc   = $postLoadVidData['vid_url'];
	$postaLoadVideoType = $postLoadVidData['vid_source'];
	if ( $postaLoadVideoType == 0 ) {
		$videoType1 = 'youtube';
	} elseif ( $postaLoadVideoType == 1 ) {
		$videoType1 = 'mp4';
	} elseif ( $postaLoadVideoType == 2 ) {
		$videoType1 = 'webm';
	} elseif ( $postaLoadVideoType == 3 ) {
		$videoType1 = 'ogg';
	} elseif ( $postaLoadVideoType == 4 ) {
		$videoType1 = 'mov';
	} elseif ( $postaLoadVideoType == 5 ) {
		$videoType1 = 'vimeo';
	}
	$srcpostload = 'player.postroll({ 
            src:{src:"' . $postloadVideoSrc . '",type:"video/' . $videoType1 . '"},
               });';
}
	$video['prepostrolljs'] = '';
if($srcpreload != '' || $srcpostload != '') {
	$video['prepostrolljs'] = 'var player = this; if (typeof player.ads === "function") { player.ads(); }' .
                          $srcpreload . $srcpostload;
}

/*vitvis*/

if ( $video['vid_source'] == 0 ) { // Youtube
	$video['youtube_url'] = $video['vid_url'];
	$video['mp4_url']     = "";
	$video['webm_url']    = "";
	$video['ogg_url']     = "";
	$video['mov_url']     = "";
	$video['vimeo_url']   = "";
} else if ( $video['vid_source'] == 1 ) { // Mp4
	$video['mp4_url']     = $video['vid_url'];
	$video['youtube_url'] = "";
	$video['webm_url']    = "";
	$video['ogg_url']     = "";
	$video['mov_url']     = "";
	$video['vimeo_url']   = "";
} else if ( $video['vid_source'] == 2 ) { // WebM
	$video['webm_url']    = $video['vid_url'];
	$video['mp4_url']     = "";
	$video['youtube_url'] = "";
	$video['ogg_url']     = "";
	$video['mov_url']     = "";
	$video['vimeo_url']   = "";
} else if ( $video['vid_source'] == 3 ) { // OGG
	$video['ogg_url']     = $video['vid_url'];
	$video['mp4_url']     = "";
	$video['youtube_url'] = "";
	$video['webm_url']    = "";
	$video['mov_url']     = "";
	$video['vimeo_url']   = "";
} else if ( $video['vid_source'] == 4 ) { // MOV
	$video['mov_url']     = $video['vid_url'];
	$video['mp4_url']     = "";
	$video['youtube_url'] = "";
	$video['webm_url']    = "";
	$video['ogg_url']     = "";
	$video['vimeo_url']   = "";
} else if ( $video['vid_source'] == 5 ) { // Vimeo
	$video['vimeo_url']   = $video['vid_url'];
	$video['mp4_url']     = "";
	$video['youtube_url'] = "";
	$video['webm_url']    = "";
	$video['ogg_url']     = "";
	$video['mov_url']     = "";
}
if ( isset( $_REQUEST['plugin_saw'] ) && $_REQUEST['plugin_saw'] != "" ) {
	$video['plugin_saw'] = sanitize_text_field($_REQUEST['plugin_saw']);
	$video['webinar_id'] = sanitize_text_field($_REQUEST['webinar_id']);
}

exit( self::getJs( $video ) );
