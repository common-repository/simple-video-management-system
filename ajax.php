<?php
header( "access-control-allow-origin: *" );
header( "X-Frame-Options: GOFORIT" );

	if ( $_REQUEST['action'] == "svms_track_video_analytics" ) {
		global $wpdb;
		$agents   = $wpdb->prefix . "vpp_useragents";
		$ips      = $wpdb->prefix . "vpp_ips";
		$states   = $wpdb->prefix . "vpp_stats";
		$dropOff  = $wpdb->prefix . "vpp_dropsVideo";
		$endDay   = date( "Y-m-d" );
		$cmd      = sanitize_text_field(getArrayItem( $_REQUEST, 'cmd' ));
		$video_id = sanitize_text_field(getArrayItem( $_REQUEST, 'video_id' ));

		if ( $cmd === 'on_play' ) {
			$user_agent = sanitize_text_field(trim( getArrayItem( $_REQUEST, 'user_agent' ) ));
			$isdevice   = sanitize_text_field(trim( getArrayItem( $_REQUEST, 'is_device' ) ));
			$ip         = sanitize_text_field(trim( getArrayItem( $_REQUEST, 'ip' ) ));
			$city       = sanitize_text_field(trim( getArrayItem( $_REQUEST, 'city' ) ));
			$c_code     = sanitize_text_field(trim( getArrayItem( $_REQUEST, 'c_code' ) ));
			$c_name     = sanitize_text_field(trim( getArrayItem( $_REQUEST, 'c_name' ) ));
			$play_date  = date( 'Y-m-d' );
			$device_col = $isdevice === 'mobile' ? 'is_mobile' : 'is_desktop';

			$ins = $wpdb->prepare(
				"INSERT INTO $ips (video_id, ip_address, city, c_code, c_name) values(%d, %s, %s, %s, %s)",
				$video_id, $ip, $city, $c_code, $c_name
			);
			$wpdb->query( $ins );

			$agents_query = $wpdb->prepare(
				"SELECT id FROM $agents WHERE video_id = %d AND user_agent = %s",
				$video_id,
				$user_agent
			);
			$rows = $wpdb->get_row( $agents_query, ARRAY_A );

			// Insert or update record into `useragents` table.
			if ( count( $rows ) === 0 ) {
				$in = $wpdb->prepare(
					"INSERT INTO $agents (video_id, plays, user_agent, $device_col, play_date) VALUES (%d, %d, %s, %d, %s)",
					$video_id, 1, $user_agent, 1, $play_date
				);
				$wpdb->query( $in );
			} else {
				$sql   = $wpdb->prepare(
					"UPDATE $agents SET plays = plays + 1, $device_col = $device_col + 1 WHERE video_id = %d AND user_agent = %s",
					$video_id, $user_agent
				);
				$query = $wpdb->query( $sql );
			}

			$stats_query = $wpdb->prepare( "SELECT id FROM $states WHERE video_id = %d AND play_date = %s", $video_id, $endDay );
			$row         = $wpdb->get_row( $stats_query, ARRAY_A );

			if ( count( $row ) === 0 ) {
				$sql   = $wpdb->prepare(
					"INSERT INTO $states (video_id, plays, play_date) VALUES (%d, %d, %s)",
					$video_id, 1, $endDay
				);
				$query = $wpdb->query( $sql );
			} else {
				$sql   = $wpdb->prepare(
					"UPDATE $states SET plays = plays + 1 WHERE video_id = %d AND play_date = %s",
					$video_id, $endDay
				);
				$query = $wpdb->query( $sql );
			}
		}

		if ( $cmd === 'on_completed' ) {
			$sql = $wpdb->prepare(
				"UPDATE $states SET completed_plays = completed_plays + 1 WHERE video_id = %d and play_date = %s",
				$video_id, $endDay
			);
			error_log('on_completed query: ' . $sql);
			$query = $wpdb->query( $sql );
		}

		if ( $cmd === 'on_completed' || $cmd === 'videoDropOf' ) {
			$rand       = sanitize_text_field(getArrayItem( $_REQUEST, 'rand' ));
			$drop_off   = sanitize_text_field(getArrayItem( $_REQUEST, 'drop_off' ));
			$video_time = sanitize_text_field(getArrayItem( $_REQUEST, 'video_time' ));

			$drop_off_query = $wpdb->prepare(
				"SELECT id FROM $dropOff WHERE video_id = %d AND rand = %s", $video_id, $rand
			);
			$rows = $wpdb->get_row( $drop_off_query, ARRAY_A );

			if ( count( $rows ) === 0 ) {
				$sql = $wpdb->prepare(
					"INSERT INTO $dropOff (video_id, drop_off, video_time, rand) VALUES (%d, %s, %s, %s)",
					$video_id, $drop_off, $video_time, $rand
				);
				$wpdb->query( $sql );
			} else {
				$sql = $wpdb->prepare(
					"UPDATE $dropOff SET drop_off = %s WHERE video_id = %d and rand = %s",
					$drop_off, $video_id, $rand
				);
				$wpdb->query( $sql );
			}
		}
	}
