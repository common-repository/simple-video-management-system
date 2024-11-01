<?php
class Vpp_Base {
	public static $name = 's3_video_player_plus';
	public static $action = null;
	public static $table = array();
	public static $errors = array();
	public static $context = null;
	public static $plugin_url = null;


	public static function initBase() {
		if ( ! self::$plugin_url ) {
			global $wpdb;
			self::$table      = array(
				'video'            => $wpdb->prefix . 'vpp_video',
				'video_location'   => $wpdb->prefix . 'vpp_video_location',
				'video_grids'      => $wpdb->prefix . 'vpp_video_grids',
				'video_groups'     => $wpdb->prefix . 'vpp_groups',
				'video_ips'        => $wpdb->prefix . 'vpp_ips',
				'video_useragents' => $wpdb->prefix . 'vpp_useragents',
				'video_stats'      => $wpdb->prefix . 'vpp_stats',
				'video_drops'      => $wpdb->prefix . 'vpp_dropsVideo',
			);
			self::$plugin_url = VPP_PLUGIN_URL;
		}
	}

	public static function getUniqueHandle( $seed = null ) {
		global $wpdb;

		$seed     = md5( $seed . serialize( $_SERVER ) . mt_rand() );
		$video_id = $wpdb->get_var( $wpdb->prepare('SELECT * FROM ' . self::$table['video'] . ' WHERE handle = "%s"',[$seed]));
		if ( (int) $video_id ) {
			$seed = self::getUniqueHandle( $seed );
		}

		return $seed;
	}

	Public static function ApisCrmsCustomFields_VPP() {
		require_once( VPP_PATH . "/infusion/Infusionsoft/infusionsoft.php" );
		$appName = SVMS_INF_APP_KEY_NAME;
		$apiKey  = SVMS_INF_APP_KEY;
		$app     = new Infusionsoft_App( $appName, $apiKey );
		Infusionsoft_AppPool::addApp( $app );
		/*$customField = Infusionsoft_CustomFieldService::getCustomField(new Infusionsoft_Contact(), '_SomeCustomField');
        $fieldValues = $customField->getValues();
        */
		$contact      = new Infusionsoft_Contact();
		$customFields = @Infusionsoft_CustomFieldService::getCustomFields( new Infusionsoft_Contact() );
		/** @var Infusionsoft_DataFormField $customField */
		$customFieldsAsArray = array();
		$i                   = 0;
		foreach ( $customFields as $customField ) {
			$customFieldsAsArray[ $i ]['key']  = $customField->Name;
			$customFieldsAsArray[ $i ]['name'] = $customField->Label;
			$i ++;
		}

		return $customFieldsAsArray;
	}


	public static function getVideoCodeGrid( $video_id, $sizes, $raw = false, $override_list = null ) {
		global $wpdb, $post;
		include_once VPP_APP_PATH . '/Vpp_Base.php';
		Vpp_Base::initBase();
		$code = "";
		if ( (string) $video_id == (string) intval( $video_id ) ) {
			$video = $wpdb->get_row( $wpdb->prepare('SELECT * FROM ' . Vpp_Base::$table['video'] . ' WHERE video_id = %s',[addslashes($video_id)]), ARRAY_A );
		} else {
			$video = $wpdb->get_row( $wpdb->prepare('SELECT * FROM ' . Vpp_Base::$table['video'] . ' WHERE handle = %s',[addslashes($video_id)]), ARRAY_A );
		}
		if ( $video['vid_url'] == "" ) {
			if ( $video['youtube_url'] != "" ) {
				$wpdb->query( $wpdb->prepare("update " . Vpp_Base::$table['video'] . " set vid_url='%s', vid_source='0' where video_id='%d'",[$video['youtube_url'],$video['video_id']] ));
			} else if ( $video['mp4_url'] != "" ) {
				$wpdb->query( $wpdb->prepare("update " . Vpp_Base::$table['video'] . " set vid_url='%s', vid_source='1' where video_id='%d'",[$video['mp4_url'],$video['video_id']]));
			} else if ( $video['webm_url'] != "" ) {
				$wpdb->query( $wpdb->prepare("update " . Vpp_Base::$table['video'] . " set vid_url='%s', vid_source='2' where video_id='%d'",[$video['webm_url'],$video['video_id']] ));
			} else if ( $video['ogg_url'] != "" ) {
				$wpdb->query( $wpdb->prepare("update " . Vpp_Base::$table['video'] . " set vid_url='%s', vid_source='3' where video_id='%d'",[$video['ogg_url'], $video['video_id']] ));
			} else if ( $video['mov_url'] != "" ) {
				$wpdb->query( $wpdb->prepare("update " . Vpp_Base::$table['video'] . " set vid_url='%s', vid_source='4' where video_id='%d'",[$video['mov_url'], $video['video_id']] ));
			} else if ( $video['vimeo_url'] != "" ) {
				$wpdb->query( $wpdb->prepare("update " . Vpp_Base::$table['video'] . " set vid_url='%s', vid_source='5' where video_id='%d'",[$video['vimeo_url'], $video['video_id']] ));
			}
		}
		if ( (string) $video_id == (string) intval( $video_id ) ) {
			$video = $wpdb->get_row( $wpdb->prepare('SELECT * FROM ' . Vpp_Base::$table['video'] . ' WHERE video_id = %s',[addslashes($video_id)]), ARRAY_A );
		} else {
			$video = $wpdb->get_row( $wpdb->prepare('SELECT * FROM ' . Vpp_Base::$table['video'] . ' WHERE handle = %s',[addslashes($video_id)]), ARRAY_A );
		}
		/*vitvis*/
		$preloadcheck     = $video['pre_roll_video_chk'];
		$preloadvideourl  = $video['pre_select_value'];
		$postloadcheck    = $video['post_roll_video_ck'];
		$postloadvideourl = $video['post_select_value'];
		$srcpreload = $srcpostload = '';

		if ( $preloadcheck == 1 && $preloadvideourl != 0 && $preloadvideourl != "" ) {
			$preLoadVidData    = $wpdb->get_row( $wpdb->prepare('SELECT vid_url, vid_source, video_id FROM ' . Vpp_Base::$table['video'] . ' WHERE video_id = %s',[$preloadvideourl]), ARRAY_A );
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
			$postLoadVidData    = $wpdb->get_row( $wpdb->prepare('SELECT vid_url, vid_source, video_id FROM ' . Vpp_Base::$table['video'] . ' WHERE video_id = %s',[$postloadvideourl]), ARRAY_A );
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
		if ($srcpreload != '' || $srcpostload !='') {
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
		if ( count( $video ) > 0 ) {
			if ( is_array( $override_list ) ) {
				foreach ( $override_list as $field => $value ) {
					$video[ $field ] = $value;
				}
			}
			$align = ' margin: 15px 15px 15px 0;';
			if ( $video['align'] == 'right' ) {
				$align = ' margin: 15px 0 15px 15px; float: right;';
			} elseif ( $video['align'] == 'center' ) {
				$align = ' margin: 15px;';
			}
			$source_list = array();
			if ( $video['mp4_url'] ) {
				$source_list[] = '<source src="' . self::getS3TempLink( $video['mp4_url'] ) . '" type="video/mp4" />';
			}
			if ( $video['mov_url'] ) {
				$source_list[] = '<source src="' . self::getS3TempLink( $video['mov_url'] ) . '" type="video/mp4" />';
			}
			if ( $video['webm_url'] ) {
				$source_list[] = '<source src="' . self::getS3TempLink( $video['webm_url'] ) . '" type="video/webm" />';
			}
			if ( $video['ogg_url'] ) {
				$source_list[] = '<source src="' . self::getS3TempLink( $video['ogg_url'] ) . '" type="video/ogg" />';
			}
			$source_list = implode( "\n\t\t", $source_list );
			$controls    = ' controls';
			if ( (int) @$video['hide_controls'] && ! VPP_FORCE_CONTROLS ) {
				$controls = '';
			}
			$autoplay = ' preload="metadata"';
			if ( (int) @$video['auto_play'] ) {
				//$autoplay = ' preload="metadata" autoplay';
				if ( self::isMobile_svms() ) {
					$is = "mobile";
				} else {
					// Do something for only desktop users
					$is = "desktop";
				}
				if ( $is == "desktop" ) {
					//$autoplay = ' preload="metadata" autoplay';
					$autoplay = ' preload="true" autoplay';
				} else {
					$controls = ' controls';
				}
			}
			$loop = '';
			if ( (int) @$video['loop_video'] ) {
				$loop = ' loop ';
			}
			$hw = array();
			if ( $video['width'] ) {
				//$hw[] = 'width="'.$video['width'].'"';
				$hw[] = 'width="565"';
				//$hw[] = 'width="'.$sizes['w'].'"';
			} else {
				$hw[] = 'width="565"';
				//$hw[] = 'width="'.$sizes['w'].'"';
			}
			if ( $video['height'] ) {
				//$hw[] = 'height="'.$video['height'].'"';
				$hw[] = 'height="318"';
				//$hw[] = 'height="'.$sizes['h'].'"';
			} else {
				//$hw[] = 'height="'.$sizes['h'].'"';
				$hw[] = 'height="423"';
			}
			$poster = '';
			if ( $video['splash_url'] ) {
				$poster = 'poster="' . $video['splash_url'] . '"';
			}
			$pause_overlay_image = '';
			if ( $video['pause_overlay_image'] ) {
				$pause_overlay_image = $video['pause_overlay_image'];
			}
			$hw                  = implode( ' ', $hw );
			$video['v_randCode'] = date( 'Ymdhis' ) . rand();
			$js_url              = admin_url( 'admin-ajax.php' ) . '?action=s3_video_player_plus&do=raw_js&video=' . $video['handle'];
			$raw_js              = self::getJs( $video ); // Vpp_Base::getVideoCodeGrid
			if ( (int) @$video['html_position'] == 1 ) {
				if ( (int) @$video['show_html_seconds'] >= 0 ) {
					$string    = stripslashes( trim( $video['show_html'] ) );
					$content_s = str_replace( "&#039;", "'", html_entity_decode( $string, ENT_QUOTES, 'UTF-8' ) );
					echo '<div id="vpp_html_' . esc_attr($video['video_id']) . '_' . esc_attr($video['v_randCode']) . '" style="display: none;">' . do_shortcode( $content_s ) . '</div>';
				}
			}
			if ( $video['youtube_url'] ) {
				$code = <<<END
<video id="vpp_video_{$video['video_id']}_{$video['v_randCode']}" {$controls}{$autoplay}{$loop} class="video-js vjs-default-skin" {$hw} {$poster}>
</video>
END;
			} else if ( $video['vimeo_url'] ) {
				$code = <<<END
<video id="vpp_video_{$video['video_id']}_{$video['v_randCode']}" {$controls}{$autoplay}{$loop} class="video-js vjs-default-skin" {$hw} {$poster}>
</video>
END;
			} else {
				$code = <<<END
<video id="vpp_video_{$video['video_id']}_{$video['v_randCode']}" {$controls}{$autoplay}{$loop} class="video-js vjs-default-skin" {$hw} {$poster}>
{$source_list}
</video>
END;
			}
			//if ((int)@$video['show_html_seconds'])
			if ( $video['show_html_seconds'] == '' ) {
				$video['show_html_seconds'] = 0;
			}
	
			if ( (int) @$video['static_html'] == 1 ) {
				$string     = stripslashes( trim( $video['static_html_code'] ) );
				$content_ss = str_replace( "&#039;", "'", html_entity_decode( $string, ENT_QUOTES, 'UTF-8' ) );
				if ( $content_ss != '' ) {
					$code .= '<div id="vpp_html_staic_' . esc_attr($video['video_id']) . '" style="clear: both;">' . do_shortcode( $content_ss ) . '</div>';
				}
			}
			if ( (int) @$video['html_position'] == 0 ) {
				if ( (int) @$video['show_html_seconds'] >= 0 ) {
					$string    = stripslashes( trim( $video['show_html'] ) );
					$content_s = str_replace( "&#039;", "'", html_entity_decode( $string, ENT_QUOTES, 'UTF-8' ) );
					if ( $content_s != '' ) {
						$code .= '<div id="vpp_html_' . esc_attr($video['video_id']) . '_' . esc_attr($video['v_randCode']) . '" style="display: none;">' . do_shortcode( $content_s ) . '</div>';
					}
				}
			}
			if ( $raw ) {
				wp_enqueue_script('svms_raw', $js_url, [], '1.0', True);
			} else {
				wp_add_inline_script('svms-scripts', $raw_js);
			}
			if ( $video['align'] == 'center' ) {
				$code = '<center>' . $code . '</center>';
			}
			if ( (int) @$video['video_id'] && (int) @$post->ID ) {
				$wpdb->query($wpdb->prepare('REPLACE INTO ' . Vpp_Base::$table['video_location'] . ' (video_id, post_id) VALUES (%d, %d)',[$video['video_id'], $post->ID]));
			}
		}

		return $code;
	}

	public static function getVideoFromDatabase( $video_id ) {
		global $wpdb;
		$column_name = 'handle';

		if ( (string) $video_id === (string) intval( $video_id ) ) {
			$column_name = 'video_id';
		}

		$table_name = self::$table['video'];
		$query = $wpdb->prepare( "SELECT * FROM $table_name WHERE $column_name = %s", $video_id );
		$video = $wpdb->get_row( $query, ARRAY_A );

		return $video;
	}

	public static function updateVideoUrl( $video ) {
		global $wpdb;
		$video_url = $video['vid_url'];
		$video_source = -1;

		if ( !$video_url ) {
			if ( $video['youtube_url'] ) {
				$video_url = $video['youtube_url'];
				$video_source = 0;
			} elseif ( $video['mp4_url'] ) {
				$video_url = $video['mp4_url'];
				$video_source = 1;
			} elseif ( $video['webm_url'] ) {
				$video_url = $video['webm_url'];
				$video_source = 2;
			} elseif ( $video['ogg_url'] ) {
				$video_url = $video['ogg_url'];
				$video_source = 3;
			} elseif ( $video['mov_url'] ) {
				$video_url = $video['mov_url'];
				$video_source = 4;
			} elseif ( $video['vimeo_url'] ) {
				$video_url = $video['vimeo_url'];
				$video_source = 5;
			}

			if ( $video_url ) {
				$table_name = self::$table['video'];
				$query = $wpdb->prepare(
					"UPDATE $table_name set vid_url = %s, vid_source = %d WHERE video_id = %d",
					$video_url,
					$video_source,
					$video['video_id']
				);
				$wpdb->query( $query );

				$video['vid_url'] = $video_url;
			}
		}

		return $video;
	}

	public static function getVideoCode( $video_id, $raw = false, $override_list = null, $plugin_saw = '' ) {
		global $wpdb, $post;
		include_once VPP_APP_PATH . '/Vpp_Base.php';
		Vpp_Base::initBase();

		$code = '';
		$video = self::getVideoFromDatabase( $video_id );

		// Don't to nothing else if video is not found.
		if ( !is_array( $video ) || count( $video ) === 0 ) {
			return compact('code', 'video');
		}

		$video = self::updateVideoUrl( $video );

		/*vitvis*/
		$preloadcheck     = $video['pre_roll_video_chk'];
		$preloadvideourl  = $video['pre_select_value'];
		$postloadcheck    = $video['post_roll_video_ck'];
		$postloadvideourl = $video['post_select_value'];
		$srcpreload       = '';
		$srcpostload      = '';

		if ( $preloadcheck == 1 && $preloadvideourl != 0 && $preloadvideourl != "" ) {
			$preLoadVidData    = $wpdb->get_row( $wpdb->prepare('SELECT vid_url, vid_source, video_id FROM ' . Vpp_Base::$table['video'] . ' WHERE video_id = %s',[$preloadvideourl]), ARRAY_A );
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
               }); 
               player.on("adend", function() {
                    player.load();
                    player.play();
            });
               ';


		}
		if ( $postloadcheck == 1 && $postloadvideourl != 0 && $postloadvideourl != "" ) {
			$postLoadVidData    = $wpdb->get_row( $wpdb->prepare('SELECT * FROM ' . Vpp_Base::$table['video'] . ' WHERE video_id = %s',[$preloadvideourl]), ARRAY_A );
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
		if ($srcpreload != '' || $srcpostload !='') {
			$video['prepostrolljs'] = 'var player = this; if (typeof player.ads === "function") { player.ads();  ' .
                                  $srcpreload . $srcpostload . '}';
		    wp_add_inline_script('svms-shortcodes', $video['prepostrolljs'],'before');
		}
		/*vitvis*/

		// TODO improve this video_source code block and reuse it in raw.php:getVideoCodeRaws
		$video_source = (int) getArrayItem( $video, 'vid_source', -1 );
		$video['youtube_url'] = $video['mp4_url'] = $video['webm_url'] = $video['ogg_url'] = $video['mov_url'] = $video['vimeo_url'] = '';

		if ( $video_source === 0 ) { // Youtube
			$video['youtube_url'] = $video['vid_url'];
		} elseif ( $video_source === 1 ) { // Mp4
			$video['mp4_url'] = $video['vid_url'];
		} elseif ( $video_source === 2 ) { // WebM
			$video['webm_url'] = $video['vid_url'];
		} elseif ( $video_source === 3 ) { // OGG
			$video['ogg_url'] = $video['vid_url'];
		} elseif ( $video_source === 4 ) { // MOV
			$video['mov_url'] = $video['vid_url'];
		} elseif ( $video_source === 5 ) { // Vimeo
			$video['vimeo_url'] = $video['vid_url'];
		}

		if ( count( $video ) > 0 ) {
			if ( is_array( $override_list ) ) {
				foreach ( $override_list as $field => $value ) {
					$video[ $field ] = $value;
				}
			}
			$align = ' margin: 15px 15px 15px 0;';
			$video_align = getArrayItem( $video, 'align' );
			if ( $video_align == 'right' ) {
				$align = ' margin: 15px 0 15px 15px; float: right;';
			} elseif ( $video_align == 'center' ) {
				$align = ' margin: 15px;';
			}
			$source_list      = array();
			$btn_url_download = "";
			$mp4_file         = 0;
			if ( $video['mp4_url'] ) {
				$source_list[]    = '<source src="' . esc_url(self::getS3TempLink( $video['mp4_url'] )) . '" type="video/mp4" />';
				$btn_url_download = $video['mp4_url'];
				$mp4_file         = 1;
			}
			if ( $video['webm_url'] ) {
				$source_list[]    = '<source src="' . esc_url(self::getS3TempLink( $video['webm_url'] )) . '" type="video/webm" />';
				$btn_url_download = $video['webm_url'];
			}
			if ( $video['ogg_url'] ) {
				$source_list[]    = '<source src="' . esc_url(self::getS3TempLink( $video['ogg_url'] )) . '" type="video/ogg" />';
				$btn_url_download = $video['ogg_url'];
			}
			if ( $video['mov_url'] ) {
				$source_list[]    = '<source src="' . esc_url(self::getS3TempLink( $video['mov_url'] )) . '" type="video/mp4" />';
				$btn_url_download = $video['mov_url'];
			}
			$source_list = implode( "\n\t\t", $source_list );
			$controls    = ' controls';
			if ( (int) @$video['hide_controls'] && ! VPP_FORCE_CONTROLS ) {
				$controls = '';
			}
			$autoplay = ' preload="metadata"';
			if ( (int) @$video['auto_play'] ) {
				if ( self::isMobile_svms() ) {
					$is = "mobile";
				} else {
					$is = "desktop";
				}
				if ( $is == "desktop" ) {
					$autoplay = ' preload="true" autoplay';
				} else {
					$controls = ' controls';
				}
			}
			$loop = '';
			if ( (int) @$video['loop_video'] ) {
				$loop = ' loop ';
			}
			$hw = array();
			if ( array_key_exists( 'width', $video ) ) {
				$hw[] = 'width="' . $video['width'] . '"';
			} else {
				//$hw[] = 'width="565"';
			}
			if ( array_key_exists( 'height', $video ) ) {
				$hw[] = 'height="' . $video['height'] . '"';
			} else {
				$hw[] = 'height="423"';
			}
			$poster = '';
			if ( array_key_exists( 'splash_url', $video ) ) {
				//if($preloadcheck== 0){
				$poster = 'poster="' . $video['splash_url'] . '"';
				//}

			}
			$pause_overlay_image = getArrayItem( $video, 'pause_overlay_image' );
			$hw                  = implode( ' ', $hw );
			$video['v_randCode'] = date( 'Ymdhis' ) . rand();
			$js_url              = admin_url( 'admin-ajax.php' ) . '?action=s3_video_player_plus&do=raw_js&video=' . getArrayItem( $video, 'handle' );

			if ( self::isMobile_svms() ) {
                $vpp_html = "max-width:" . $video['width'] . "px; width:100%; ";
			} else {
				$mp4_style = "";

				$plug         = function_exists( 'get_plugins' ) ? get_plugins() : [];
				$is_elementor = 0;
				if ( array_key_exists( "elementor/elementor.php", $plug ) ) {
					if ( is_plugin_active( 'elementor/elementor.php' ) ) {
						$is_elementor = 1;
					}
				}
				if ( $is_elementor == 1 ) {
					$mp4_style = "height: " . $video["height"] . "px;";

				}
				$mx_wd = $video["width"] - 8;

                $vpp_html .= $mp4_style;
				if ( $is_elementor == 1 ) {

					$mx_wd     = $video["width"] + 200;
                    $mx_height = $video["height"] - 80;
                    $vpp_html_extra_class = ' width_elementor';

				}
			}

            $vpp_video =  "max-width:" . $video["width"] . "px; ";

			if ( $video['layer_txt_color'] == "" ) {
				$video['layer_txt_color'] = "#ffffff";
			}
			if ( $video['layer_txt_size'] == "" ) {
				$video['layer_txt_size'] = 80;
			}
			if ( $video['layer_font'] == "" ) {
				$video['layer_font'] = "Arial";
			}
            $download_btn_style = "color:" . $video['dwn_text_color'] . "; background-color:" . $video["dwn_back_color"] . ";";
            $heading_play = " font-family:" . esc_attr($video['layer_font']) . "; font-size:" . esc_attr($video['layer_txt_size']) . "; color:" . esc_attr($video['layer_txt_color']) . ";";  

			if ( isset( $plugin_saw ) && $plugin_saw != "" ) {
				$video['plugin_saw'] = $plugin_saw;
			}
			$raw_js         = self::getJs( $video ); // Vpp_Base::getVideoCode
			$true_mobile    = self::isMobile_svms();
			$video_id       = $video['video_id'];
			$v_randCode     = $video['v_randCode'];
			$form_optin_URl = admin_url() . "admin-ajax.php?action=svms_optin_subscribe";
			if ( $video['enable_layer'] == "" ) {
				$video['enable_layer'] = 0;
            }
		    wp_add_inline_script('svms-shortcodes', "function svmsForm() {
                jQuery('.svms_btn_optin').val('Loading...');
                jQuery.post('{$form_optin_URl}',jQuery('.svms_optinForm').serialize(),function(data){
                    jQuery('.svms_btn_optin').val('Submit'); 
                    jQuery('.svms_optinForm')[0].reset();
                    jQuery('#svms_video_layer').fadeOut('slow');
                    var myPlayer = videojs('#vpp_video_{$video_id}_{$v_randCode}'); 
                    myPlayer.play();
                });
                return false;
            }
            if({$video['enable_layer']}==0 && {$video['auto_play']}==1){
        
                jQuery(document).ready(function(){
                var myPlayer = videojs('#vpp_video_{$video['video_id']}_{$video['v_randCode']}'); 
                myPlayer.muted(true);
                
                var OSName='Unknown OS';
                    if (navigator.appVersion.indexOf('Win')!=-1) OSName='Windows';
                    else if (navigator.appVersion.indexOf('Mac')!=-1) OSName='MacOS';
                    else if (navigator.appVersion.indexOf('X11')!=-1) OSName='UNIX';
                    else if (navigator.appVersion.indexOf('Linux')!=-1) OSName='Linux';
                    console.log(OSName);
                    if(OSName=='MacOS'){
                        if (navigator.userAgent.search('Chrome') >= 0) {
                            //jQuery('#svms_video_auto').show();
                            jQuery('.lbr_{$video['video_id']}_{$video['v_randCode']}').show();
                        }
                    } 
                    if(OSName=='Linux'){
                        //jQuery('#svms_video_auto').show();
                        jQuery('.lbr_{$video['video_id']}_{$video['v_randCode']}').show();
                    }
                    if(OSName=='MacOS'){
                        if({$true_mobile}){
                            //jQuery('#svms_video_auto').show();
                            jQuery('.lbr_{$video['video_id']}_{$video['v_randCode']}').show();
                        }						
                    }
                    if(OSName=='Windows'){
                            //jQuery('#svms_video_auto').show();
                            jQuery('.lbr_{$video['video_id']}_{$video['v_randCode']}').show();
                       } 
                });
                 
            }");
            
			$enject_id = '';
			$crm = $video['crm_id'];

			if ( $crm == 1 ) {
				$enject_id = $video['tag_id'];
			} else if ( $crm == 2 ) {
				$enject_id = $video['aw_list_id'];
			} else if ( $crm == 3 ) {
				if ( $video['getResponse_id'] != "" ) {
					$enject_id = $video['getResponse_id'];
				} else {
					$enject_id = 0;
				}
			} else if ( $crm == 4 ) {
				if ( $video['mailchimp_listid'] != "" ) {
					$enject_id = $video['mailchimp_listid'];
				} else {
					$enject_id = 0;
				}
			} else if ( $crm == 5 ) {
				$enject_id = $video['activecamp_id'];
			} else if ( $crm == 6 ) { // Convertkit
				$enject_id = $video['convertkit_tagid'];
			} else if ( $crm == 7 ) {
				if ( $video['markethero_tag'] != "" ) {
					$enject_id = $video['markethero_tag'];
				} else {
					$enject_id = 0;
				}
			} else if ( $crm == 8 ) { // DRIp APi
				if ( $video['drip_tag'] != "" ) {
					$enject_id = $video['drip_tag'];
				} else {
					$enject_id = 0;
				}
			} else if ( $crm == 9 ) { // Sandlane API
				$enject_id = $video['sendlane_tagid'];
			} else if ( $crm == 10 ) { // iContact
				$enject_id = $video['icontact_listid'];
			} else if ( $crm == 11 ) { // Ontraport APi
				if ( $video['ontraport_tag'] != "" ) {
					$enject_id = $video['ontraport_tag'];
				} else {
					$enject_id = 0;
				}
			} else if ( $crm == 12 ) { // Constant Contact
				if ( $video['c_contact_id'] != "" ) {
					$enject_id = $video['c_contact_id'];
				} else {
					$enject_id = 0;
				}
			} else if ( $crm == 13 ) { // Sendy
				$enject_id = $video['sendy_listid'];
			} else if ( $crm == 14 ) { // arpReach
				if ( $video['arpreach_list'] != "" ) {
					$enject_id = $video['arpreach_list'];
				} else {
					$enject_id = 0;
				}
			}
			/****** Optin Start *******/

			/****** Optin ENd*******/
			if ( (int) @$video['html_position'] == 1 ) {
				if ( (int) @$video['show_html_seconds'] >= 0 ) {
					$string    = stripslashes( trim( $video['show_html'] ) );
					$content_s = str_replace( "&#039;", "'", html_entity_decode( $string, ENT_QUOTES, 'UTF-8' ) );
					$code      .= '<div id="vpp_html_' . esc_attr($video['video_id']) . '_' . esc_attr($video['v_randCode']) . '" style="display: none; '.$vpp_html.'">' . do_shortcode( $content_s ) . '</div>';
				}
			}
			if ( $video['pl_background'] == "" ) {
				$pl_bak = "#000";
                $vpp_video .= "background-color: " . $pl_bak . " !important;" ;
			} else {
				$pl_bak = $video['pl_background'];
                $vpp_video .= "background-color: " . $pl_bak . " !important; ";
                
			}
			if ( $video['youtube_url'] ) {
				$v    = "{'customControlsOnMobile': true}";
                $fram = '<div class="vid_frame">
                    <video  oncontextmenu="return false;" style="padding-top: 0%; " id="vpp_video_' . $video['video_id'] . '_' . esc_attr($video['v_randCode']) . '" ' . $controls . ' ' . $autoplay . ' ' . $loop . ' AllowFullScreen=""  class="video-js vjs-default-skin" ' . $hw . ' ' . $poster . ' style="'.$vpp_video.'">
                    </video>
                    </div>';
			} else if ( $video['vimeo_url'] ) {
				$fram = '<div class="vid_frame"><video oncontextmenu="return false;" style="padding-top: 0%; " id="vpp_video_' . $video['video_id'] . '_' . esc_attr($video['v_randCode']) . '" ' . $controls . ' ' . $autoplay . ' ' . $loop . ' class="video-js vjs-default-skin" ' . $hw . ' ' . $poster . ' style2="'.$vpp_video.'"></video></div>';
			} else {
				$fram = '<div class="vid_frame"><video oncontextmenu="return false;" style="padding-top: 0%; " id="vpp_video_' . $video['video_id'] . '_' . esc_attr($video['v_randCode']) . '" ' . $controls . ' ' . $autoplay . ' ' . $loop . ' class="video-js vjs-default-skin" ' . $hw . ' ' . $poster . ' style="'.$vpp_video.'">' . $source_list . '</video></div>';
			}
			if ( $video['skin_type'] != 0 ) {
				$code          .= "<div class='cstom_outr'>";
				$videoLayerTxt = '';
				if ( $video['enable_layer'] == 0 && $video['auto_play'] == 1 ) {
					if ( $video['layer_img_url'] != '' ) {
						$videoLayerTxt = "<img src=" . $video['layer_img_url'] . ">";
					} else {
						$videoLayerTxt = $video['layer_content'];
					}
				}
				$overlay_image = '';
				if ( $video['pause_overlay_image'] != '' ) {
					$onclk         = 'onclick="ClosOverlay(' . $video['video_id'] . ')"';
					$overlay_image = "<div atk='{$video['video_id']}_{$video['v_randCode']}' class='obr_{$video['video_id']}' $onclk id='overlay_pause_" . $video["video_id"] . "_" . $video["v_randCode"] . "' style='margin-top:3%; display:none; cursor: pointer; position: absolute; z-index: +1;  width: 100%;  height: 100%; vertical-align: text-bottom !important; opacity: 1;'><div></div><img src='" . $video['pause_overlay_image'] . "' style='max-height: " . $video['height'] . "px; width: " . $video['width'] . "px;'></div>";
				}
				$onc_c = 'onclick="videoPlay(' . $video['video_id'] . ')"';
				$code  .= "<div class='cstom_outr_auto'>
					" . $overlay_image . "
                <div id='svms_video_auto' class='lbr_{$video['video_id']}_{$video['v_randCode']}' style=' position: absolute; z-index: +1; display: none; width: 100%; height: 100%; vertical-align: text-bottom !important; opacity: 0.9; padding-top: 30%; transform:translate(0%,-40%); '>
                    <h1 id='heading_play' atk='{$video['video_id']}_{$video['v_randCode']}' class='lay_{$video['video_id']}' $onc_c style=".$heading_play.">" . $videoLayerTxt . "</h1>
                </div>
              </div>";
				$code  .= "<div id='svms_video_layer' style='position: absolute; z-index: +1; display: none;  width: 100%; max-height: " . $video['height'] . "px; height: 100%; vertical-align: text-bottom !important; opacity: 0.8;'>
                <div id='vidolayer' style=' padding: 14px 0 14px 17px; margin-top: 20%; width: 100%;'>";
				$code  .= '<form method="post" onsubmit="return svmsForm()" class="svms_optinForm">
        <input type="hidden" name="crm" value="' . $crm . '" />
        <input type="hidden" name="tagging" value="' . $video['tagging_enable'] . '" />
        <input type="hidden" name="autresponder_tag_id" value="' . $enject_id . '" />
        <table border="0" style="width:100%; background-color: none; border: none;">
            <tr>
                <td style="width:35%;"><input type="text" class="svms_form_field" style="" required="required" name="name" placeholder="Name" /> &nbsp; &nbsp;</td>
                <td style="width:35%;"><input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" class="svms_form_field" name="email" required="required"  placeholder="Email" /> &nbsp;</td>
                <td style="width:26%;"><input type="submit" value="Submit" style="text-align: center; width: 86%;" class="svms_btn_optin" /></td>
            </tr>
        </table>
         </form></div></div>';
				$code  .= self::getSkinByType( $video['skin_type'], $fram, $video['width'], $video['height'] );
				$code  .= "</div>";
			} else {
				$code          .= "<div class='cstom_outr'>";
				$videoLayerTxt = '';
				if ( $video['enable_layer'] == 0 && $video['auto_play'] == 1 ) {
					if ( $video['layer_img_url'] != '' ) {
						$videoLayerTxt = "<img src=" . $video['layer_img_url'] . ">";
					} else {
						$videoLayerTxt = $video['layer_content'];
					}
				}
				$overlay_image = '';
				if ( $video['pause_overlay_image'] != '' ) {
					$onclk         = 'onclick="ClosOverlay(' . $video['video_id'] . ')"';
					$overlay_image = "<div atk='{$video['video_id']}_{$video['v_randCode']}' class='obr_{$video['video_id']}' $onclk id='overlay_pause_" . $video["video_id"] . "_" . $video["v_randCode"] . "' style='display:none; cursor: pointer; position: absolute; z-index: +1;  width: 100%;  height: 100%; vertical-align: text-bottom !important; opacity: 1;'><div></div><img src='" . $video['pause_overlay_image'] . "' style='max-height: " . $video['height'] . "px; width: " . $video['width'] . "px;'></div>";
				}
				$onc_c = 'onclick="videoPlay(' . $video['video_id'] . ')"';
				$code  .= "<div class='cstom_outr_auto'>
            		" . $overlay_image . "
                <div id='svms_video_auto' class='lbr_{$video['video_id']}_{$video['v_randCode']}' style=' position: absolute; z-index: +1; display: none;   width: 100%;  height: 100%; vertical-align: text-bottom !important; opacity: 0.9; padding-top: 30%; transform:translate(0%,-40%);'>
                    <h1 id='heading_play' atk='{$video['video_id']}_{$video['v_randCode']}' class='lay_{$video['video_id']}' $onc_c style= '".$heading_play."' >" . $videoLayerTxt . "</h2>
                </div>
              </div>";
				$code  .= "<div id='svms_video_layer' style='position: absolute; z-index: +1; display: none;  width: 100%; max-height: " . $video['height'] . "px; height: 100%; vertical-align: text-bottom !important; opacity: 0.8;'>
                <div id='vidolayer' style=' padding: 14px 0 14px 17px; margin-top: 20%; width: 100%;'>";
				$code  .= '<form method="post" onsubmit="return svmsForm()" class="svms_optinForm">
        <input type="hidden" name="crm" value="' . $crm . '" />
        <input type="hidden" name="tagging" value="' . $video['tagging_enable'] . '" />
        <input type="hidden" name="autresponder_tag_id" value="' . $enject_id . '" />
        <table border="0" style="width:100%; background-color: none; border: none;">
            <tr>
                <td style="width:35%;"><input type="text" class="svms_form_field" style="" required="required" name="name" placeholder="Name" /> &nbsp; &nbsp;</td>
                <td style="width:35%;"><input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" class="svms_form_field" name="email" required="required"  placeholder="Email" /> &nbsp;</td>
                <td style="width:26%;"><input type="submit" value="Submit" style="text-align: center; width: 86%;" class="svms_btn_optin" /></td>
            </tr>
        </table>
         </form></div></div>';
				$code  .= "$fram";
				$code  .= "</div>";
			}
			//if ((int)@$video['show_html_seconds'])
			if ( $video['show_html_seconds'] == '' ) {
				$video['show_html_seconds'] = 0;
			}
			if ( (int) @$video['static_html'] == 1 ) {
				$string     = stripslashes( trim( $video['static_html_code'] ) );
				$content_ss = str_replace( "&#039;", "'", html_entity_decode( $string, ENT_QUOTES, 'UTF-8' ) );
				if ( $content_ss != '' ) {
					$code .= '<div id="vpp_html_staic_' . $video['video_id'] . '" style="clear: both;">' . do_shortcode( $content_ss ) . '</div>';
				}
			}
			if ( (int) @$video['html_position'] == 0 ) {
				if ( (int) @$video['show_html_seconds'] >= 0 ) {
					$string    = stripslashes( trim( $video['show_html'] ) );
					$content_s = str_replace( "&#039;", "'", html_entity_decode( $string, ENT_QUOTES, 'UTF-8' ) );
					$v_sec     = (int) @$video['show_html_seconds'];
					$code      .= '<div class="vpp_html '.$vpp_html_extra_class.'" style="display: none; '.$vpp_html.'">' . do_shortcode( $content_s ) . '</div>';
				}
			}
			if ( (int) @$video['optin_gate'] ) {
				$fnp             = "close_start('" . $video['video_id'] . "_" . esc_attr($video['v_randCode']) . "')";
				$optin_gate_code = stripslashes( trim( $video['optin_gate_code'] ) );
				$optin_gate_code = str_replace( "&#039;", "'", html_entity_decode( $optin_gate_code, ENT_QUOTES, 'UTF-8' ) );
				$code            .= '
                <div id="optin_' . esc_attr($video['video_id']) . '_' . esc_attr($video['v_randCode']) . '" style="background: rgba(0,0,0,.8); top: 0px; left: 0px; position: fixed; width: 100%; height: 100%; z-index: 999999; display:none" >
                    <div id="dialogforms_' . $video['video_id'] . '_' . esc_attr($video['v_randCode']) . '" style="left: 0px;  max-width: 720px; width: 95%; margin: auto; margin-top: 10%; z-index:999999; display:none">
                            <img onclick="' . $fnp . '" src="' . esc_url(VPP_INCLUDE) . '/cross.png" id="crs_icon_' . $video['video_id'] . '_' . esc_attr($video['v_randCode']) . '" class="crsicon" style="display: none;" />
                            <div class="custom_vide" id="videoArea_' . $video['video_id'] . '_' . esc_attr($video['v_randCode']) . '">
                                ' . $optin_gate_code . '
                            </div>
                            <div id="lod_' . $video['video_id'] . '_' . esc_attr($video['v_randCode']) . '" style="z-index: 111111; position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; background: #000; opacity: .7;">
                            <img src="'. plugins_url('../assets/Loading.gif', __FILE__) .'" style="border-radius: 10px; position: fixed; left: 40%; top: 40% ! important;"/>
                        </div>
                    </div>
                </div>
                ';
			}
			if ( $raw ) {
		        wp_enqueue_script( 'svms-raw-js', $js_url );
                
			} else {
		        wp_add_inline_script('svms-shortcodes', $raw_js);
                
			}
			if ( $video_align == 'center' ) {
				$code = '<center>' . $code . '</center>';
			}
			if ( $video_align == 'right' ) {
				$code = '<div style="float:right; height: auto; max-width:' . $video['width'] . 'px; width: 100%; padding: 1%;">' . $code . '</div>';
			}
			if ( $video_align == 'left' ) {
				$code = '<div style="float:left; height: auto; max-width:' . $video['width'] . 'px; width: 100%; padding: 1%;">' . $code . '</div>';
			}
			if ( (int) @$video['video_id'] && (int) @$post->ID ) {
				$wpdb->query( 'REPLACE INTO ' . Vpp_Base::$table['video_location'] . ' (video_id, post_id) VALUES (' . (int) $video['video_id'] . ', ' . (int) $post->ID . ')' );
			}
			if ( $btn_url_download != "" && $video['is_download'] == 1 ) {
				$btn_url_download = esc_url($btn_url_download);
				if ( $video_align == 'center' ) {
					$code = "<center><a href='$btn_url_download' download ><input type='button' value='Download Video' style='color: {$video[dwn_text_color]}; background-color: {$video[dwn_back_color]}; border: 0px; border-radius: 3px; padding: 7px; '  /></a> </center> <br>" . $code;
				} elseif ( $video_align == 'right' ) {
					$code = "<div style='float:right; height: auto; width: auto; padding: 1%;'><a href='".esc_url($btn_url_download)."' download><input type='button' value='Download Video' style='color: {$video[dwn_text_color]}; background-color: {$video[dwn_back_color]}; border: 0px; border-radius: 3px; padding: 7px; '  /></a> </div> <div style='width: 100%; height: auto; clear: both;'></div> " . $code;
				} elseif ( $video_align == 'left' ) {
					$code = "<div style='float:left; height: auto; width: auto; padding: 1%;'><a href='".esc_url($btn_url_download)."' download><input type='button' value='Download Video' style='color: {$video[dwn_text_color]}; background-color: {$video[dwn_back_color]}; border: 0px; border-radius: 3px; padding: 7px; '  /></a> </div> <div style='width: 100%; height: auto; clear: both;'></div> " . $code;
				} else {
					$code = "<a href='".esc_url($btn_url_download)."' download><input type='button' value='Download Video' style='color: {$video[dwn_text_color]}; background-color: {$video[dwn_back_color]}; border: 0px; border-radius: 3px; padding: 7px; '  /></a> " . $code;
				}
			}
		}

		return compact('code', 'video');
	}


	public static function getBrowserSvms() {
		$u_agent  = $_SERVER['HTTP_USER_AGENT'];
		$bname    = 'Unknown';
		$platform = 'Unknown';
		$version  = "";
		//First get the platform?
		if ( preg_match( '/linux/i', $u_agent ) ) {
			$platform = 'linux';
		} elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
			$platform = 'mac';
		} elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
			$platform = 'windows';
		}
		// Next get the name of the useragent yes seperately and for good reason
		if ( preg_match( '/MSIE/i', $u_agent ) && ! preg_match( '/Opera/i', $u_agent ) ) {
			$bname = 'Internet Explorer';
			$ub    = "MSIE";
		} elseif ( preg_match( '/Firefox/i', $u_agent ) ) {
			$bname = 'Mozilla Firefox';
			$ub    = "Firefox";
		} elseif ( preg_match( '/Chrome/i', $u_agent ) ) {
			$bname = 'Google Chrome';
			$ub    = "Chrome";
		} elseif ( preg_match( '/Safari/i', $u_agent ) ) {
			$bname = 'Apple Safari';
			$ub    = "Safari";
		} elseif ( preg_match( '/Opera/i', $u_agent ) ) {
			$bname = 'Opera';
			$ub    = "Opera";
		} elseif ( preg_match( '/Netscape/i', $u_agent ) ) {
			$bname = 'Netscape';
			$ub    = "Netscape";
		}
		// finally get the correct version number
		$known   = array( 'Version', $ub, 'other' );
		$pattern = '#(?<browser>' . join( '|', $known ) .
		           ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if ( ! preg_match_all( $pattern, $u_agent, $matches ) ) {
			// we have no matching number just continue
		}
		// see how many we have
		$i = count( $matches['browser'] );
		if ( $i != 1 ) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if ( strripos( $u_agent, "Version" ) < strripos( $u_agent, $ub ) ) {
				$version = $matches['version'][0];
			} else {
				$version = $matches['version'][1];
			}
		} else {
			$version = $matches['version'][0];
		}
		// check if we have a number
		if ( $version == null || $version == "" ) {
			$version = "?";
		}

		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'   => $pattern
		);
	}

	public static function isMobile_svms() {
		return preg_match( "/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"] );
	}

	public static function curl_loc_svms( $url ) {
		set_time_limit( 0 );
		$request = wp_remote_get($url);
		$data = wp_remote_retrieve_body($request);
		return $data;
	}

	public static function getJs( $video ) {
		$js  = '';
		$broser_name = self::getBrowserSvms();
		if ( self::isMobile_svms() ) {
			$is = "mobile";
		} else {
			$is = "desktop";
		}
		$ip  = $_SERVER['REMOTE_ADDR'];
		$loc = unserialize( self::curl_loc_svms( "http://www.geoplugin.net/php.gp?ip=$ip" ) );
		if ( $video['show_html_seconds'] == '' ) {
			$video['show_html_seconds'] = 0;
		}
		$v_randCode = $video['v_randCode'];
		if ( (int) @$video['show_html_seconds'] >= 0 ) {
			$v_sec = $video['show_html_seconds'];
			if ( $video['show_html_seconds'] == '' ) {
				$v_sec = 0;
			}
			$js        .= <<<END
    this.on('timeupdate', function(e){
        if (this.currentTime() >= {$v_sec})
        {
            jQuery('#vpp_html_{$video['video_id']}_{$v_randCode}').show(1000);
        }
    }, false);
END;
			$string    = stripslashes( trim( $video['show_html'] ) );

			$content_s = str_replace( "&#039;", "'", html_entity_decode( $string, ENT_QUOTES, 'UTF-8' ) );
			$show_html = '<div id="vpp_html_' . esc_attr($video['video_id']) . '_' . $v_randCode . '" style="display: none;">' . do_shortcode( $content_s ) . '</div>';
		}
		if ( @$video['redirect_url'] ) {
			$js .= <<<END
    this.on('ended', function(e){
        window.location.href = '{$video['redirect_url']}';
    }, false);
END;
		}
		if ( get_option( 'aws_google_events', 1 ) ) {
			$title = addslashes( htmlspecialchars( $video['name'] ) );
			$js    .= <<<END
    if (typeof ga != 'undefined')
    {
        this.on('play', function(e){
            ga('send', 'event', 'Videos', 'Play', '{$title}');
        }, false);
        this.on('pause', function(e){
            ga('send', 'event', 'Videos', 'Pause', '{$title}');
        }, false);
        this.on('ended', function(e){
            ga('send', 'event', 'Videos', 'Ended', '{$title}');
        }, false);
    }
END;
		}
		$video_action_analytic_url = admin_url() . "admin-ajax.php?action=svms_track_video_analytics";
		$rand                      = time();
		$ip                        = trim( $loc['geoplugin_request'] );
		$city                      = trim( $loc['geoplugin_city'] );
		$c_code                    = trim( $loc['geoplugin_countryCode'] );
		$c_name                    = trim( $loc['geoplugin_countryName'] );
		if ( $video['post_roll_video_ck'] == "" ) {
			$video['post_roll_video_ck'] = 0;
		}

		$js .= <<<END
        function updateStatus_svms(id,cmd,videoplayer,randcode){
            jQuery('#overlay_pause_'+id+'_'+randcode).hide();
            var current_time =  Math.round(videoplayer.currentTime());
            var duration_time =  Math.round(videoplayer.duration());
            var rec = "cmd="+cmd+"&video_id="+id+"&user_agent={$broser_name['name']}&is_device=$is&ip={$ip}&city={$city}&c_code={$c_code}&c_name={$c_name}&drop_off="+current_time+"&video_time="+duration_time+"&rand={$rand}";
            jQuery.post('{$video_action_analytic_url}',rec,function(data){
            });
        }
        function onPause_svms(id,cmd,videoplayer,randcode){
        	if(videoplayer.currentTime() > 1) {
            	jQuery('#overlay_pause').show();
            }
            jQuery('#overlay_pause_'+id+'_'+randcode).show();
            
            var current_time =  Math.round(videoplayer.currentTime());
            var duration_time =  Math.round(videoplayer.duration());
            var rec = "cmd="+cmd+"&video_id="+id+"&drop_off="+current_time+"&video_time="+duration_time+"&rand={$rand}";
            jQuery.post('{$video_action_analytic_url}',rec,function(data){
            });
        }
        function overlayPauseClickHandler()
        {
            //if(player && typeof player.getCurrentTime === "function") {
                //videoplayer.play();
                //videojs('#vpp_video_{$video['video_id']}_{$v_randCode}').play();
            //}
        }
        
        jQuery('#overlay_pause').bind("click", function() {
            	videojs('#vpp_video_{$video['video_id']}_{$v_randCode}').play();
                jQuery('#overlay_pause').hide();
        });
            
        if({$video['scroll_options']}==1){ // Video Stop on Scroll Down
            jQuery(window).bind("scroll", function() {
                if(jQuery('#vpp_video_{$video['video_id']}_{$v_randCode}').is(':within-viewport')){
                   // if(jQuery('#vpp_video_{$video['video_id']}_{$v_randCode}').withinviewport()){
                  // videojs('#vpp_video_{$video['video_id']}_{$v_randCode}').play();
                }else{
                     videojs('#vpp_video_{$video['video_id']}_{$v_randCode}').pause();
                }
            }); 
        }else if({$video['scroll_options']}==2){ // Video float down the page
        }
        this.on('play', function(e){
            updateStatus_svms('{$video['video_id']}','on_play',this,'{$v_randCode}')
        }, false);
        this.on('pause', function(e){
            onPause_svms('{$video['video_id']}','videoDropOf',this,'{$v_randCode}')
        }, false);
        this.on('ended', function(e){
            if({$video['post_roll_video_ck']}==1){
                this.pause();
            }
            updateStatus_svms('{$video['video_id']}','on_completed',this,'{$v_randCode}');
            
        }, false);
END;

		$js            .= <<<END

      this.on('ended', function(e){
         this.posterImage.hide();
    }, false);
    this.on('play', function(e){
        this.posterImage.hide();
        this.bigPlayButton.hide();
    },false);
    var the_parent = jQuery('#vpp_video_{$video['video_id']}_{$v_randCode}').parent();
    var the_video = this;
    var video_w = the_video.width();
    var video_h = the_video.height();
    if (the_parent.innerWidth() < the_video.width())
    {
        var new_width = the_parent.innerWidth();
        var new_height = Math.round(new_width * video_h / video_w);
        the_video.width(new_width);
        the_video.height(new_height);
    }
    jQuery(window).resize(function(){
        var new_width = the_parent.innerWidth();
        var new_height = Math.round(new_width * video_h / video_w);
        if (the_parent.innerWidth() < the_video.width())
        {
            the_video.width(new_width);
            the_video.height(new_height);
        }
        else if (the_parent.innerWidth() != the_video.width())
        {
            if(the_video.width() < video_w){
                the_video.width(new_width);
                the_video.height(new_height);
            
            }
        } 
    });
END;
		$url_start_tag = admin_url() . "admin-ajax.php";

		$webinar_id     = sanitize_text_field($_SESSION['saw_webinar_id']);
		$session_name   = sanitize_text_field($_SESSION[ 'sas_user_name_' . $_SESSION['saw_webinar_id'] ]);
		$session_email  = sanitize_text_field($_SESSION[ 'sas_user_email_' . $_SESSION['saw_webinar_id'] ]);
		$saw_webinar_id = sanitize_text_field($_SESSION['saw_webinar_id']);

		if ( $video['is_wait'] == 0 ) {
			if ( isset( $video['plugin_saw'] ) && $video['plugin_saw'] == "saw" ) {
				global $wpdb;
				$saw_tags    = $wpdb->prefix . "sas_tags";
				$webinars_db = $wpdb->prefix . "sas_webinars";
				
				$tags_list   = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $saw_tags . " WHERE webinar_id = %d", $webinar_id), ARRAY_A );
				$is_tags     = json_encode( array(), true );
				$webdetail = $wpdb->get_row($wpdb->prepare("SELECT crm_id FROM ' . $webinars_db . ' WHERE id = %d", $webinar_id), ARRAY_A );
				$crm       = @$webdetail['crm_id'];
				
				if ( $crm == 1 ) {
					$is_tags        = $tags_list['is_tags'];
					$is_tags_remove = $tags_list['is_tags_remove'];
				} else if ( $crm == 2 ) { // Aweber
					$is_tags        = $tags_list['aw_tags'];
					$is_tags_remove = $tags_list['aw_tags_remove'];
				} else if ( $crm == 5 ) { // Active Campaign
					$is_tags        = $tags_list['ac_tags'];
					$is_tags_remove = $tags_list['ac_tags_remove'];
				} else if ( $crm == 3 ) { // GetResponse
					$is_tags        = $tags_list['gr_tags'];
					$is_tags_remove = $tags_list['gr_tags_remove'];
				} else if ( $crm == 4 ) { // mailchimp
					$is_tags        = $tags_list['mc_tags'];
					$is_tags_remove = $tags_list['mc_tags_remove'];
				} else if ( $crm == 6 ) { // ConvertKit
					$is_tags        = $tags_list['ck_tags'];
					$is_tags_remove = $tags_list['ck_tags_remove'];
				} else if ( $crm == 7 ) { // MarketeHero
					$is_tags        = $tags_list['mh_tags'];
					$is_tags_remove = $tags_list['mh_tags_remove'];
				} else if ( $crm == 8 ) { // DRIP
					$is_tags        = $tags_list['drp_tags'];
					$is_tags_remove = $tags_list['drp_tags_remove'];
				} else if ( $crm == 9 ) { // Sendlane
					$is_tags        = $tags_list['sl_tags'];
					$is_tags_remove = $tags_list['sl_tags_remove'];
				} else if ( $crm == 10 ) { // iContact
					$is_tags        = $tags_list['ic_tags'];
					$is_tags_remove = $tags_list['ic_tags_remove'];
				} else if ( $crm == 11 ) { // OntraPort
					$is_tags        = $tags_list['op_tags'];
					$is_tags_remove = $tags_list['op_tags_remove'];
				} else if ( $crm == 12 ) { // Conctant Contact
					$is_tags        = $tags_list['cc_tags'];
					$is_tags_remove = $tags_list['cc_tags_remove'];
				} else if ( $crm == 13 ) { // Sendy
					$is_tags        = $tags_list['sen_tags'];
					$is_tags_remove = $tags_list['sen_tags_remove'];
				} else if ( $crm == 14 ) { // ARP Reach
					$is_tags        = $tags_list['arp_tags'];
					$is_tags_remove = $tags_list['arp_tags_remove'];
				}
				$js .= <<<END
        function setCookie(c_name,value,exdays) {
            var exdate=new Date();
            exdate.setDate(exdate.getDate() + exdays);
            var c_value=escape(value) + ((exdays==null) ? "" : ";expires="+exdate.toUTCString());
            document.cookie=c_name + "=" + c_value;
        }
        function getCookie(c_name) {
            var i,x,y,ARRcookies=document.cookie.split(";");
            for (i=0;i<ARRcookies.length;i++) {
                x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
                y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
                x=x.replace(/^\s+|\s+$/g,"");
                if (x==c_name) {
                    return unescape(y);
                }
            }
        }
        this.on('play', function(e){
           var vidd = this;
           var cokie_replay = getCookie("saw_webinar_replay_{$webinar_id}");
           if(cokie_replay!="" && cokie_replay!=0){
                 var rec = "action=sas_start_tag_apply&s=replay&name={$session_name}&email={$session_email}&webinar_id={$saw_webinar_id}&crm={$crm}";
                jQuery.post("{$url_start_tag}",rec,function(data){
                    setCookie("saw_webinar_replay_{$webinar_id}",0,1*365);
                });
           }
            setInterval(function(){
                var vt = Math.round(vidd.currentTime());
                //alert(vt);
                setCookie("saw_webinar_video_ime_{$webinar_id}",vt,1*365);
            },4000);
            var rec = "action=sas_start_tag_apply&s=start&name={$session_name}&email={$session_email}&webinar_id={$saw_webinar_id}&crm={$crm}";
            jQuery.post("{$url_start_tag}",rec,function(data){
            });
        }, false);
        var vidds = this;
        var cokie_tme = getCookie("saw_webinar_video_ime_{$webinar_id}");
        if(cokie_tme!="" && cokie_tme!=0){
            vidds.currentTime(cokie_tme);
            //vidds.play();
        }
        setInterval(function(){
            var c_time = vidds.currentTime();
            //alert(c_time);
            var btn_objcts = '{$is_tags}';
            if(btn_objcts!='')
            {
            var objcts = JSON.parse(btn_objcts);
            var vb = c_time;
                //vb =  vb.split(".");
            jQuery.each(objcts,function(k,v){
                var vr = parseInt(vb);
                //alert(k+" = "+v.is_tag_time+" = "+v.is_tag_id+" - "+c_time);
                var is_ajax = 0;
                var t_1 = Number(vr)+Number(1);
                var t_2 = Number(vr)+Number(2);
                var t_3 = Number(vr)+Number(3);
                var t_4 = Number(vr)+Number(4);
                //alert(t_1+" * "+t_2+" * "+t_3+" * "+t_4+"  == "+v.is_tag_time);
                var t_5 = Number(vr)-Number(4);
                var t_6 = Number(vr)-Number(3);
                var t_7 = Number(vr)-Number(2);
                var t_8 = Number(vr)-Number(1);
                //alert(t_5+" * "+t_6+" * "+t_7+" * "+t_8+"  == "+v.is_tag_time);
                var hr_sec = Number(v.is_hr)*Number(60)*Number(60);
                var mint_sec = Number(v.is_mint)*Number(60)
                var  is_tag_time = Number(v.is_sec)+Number(mint_sec)+Number(hr_sec);
                //alert(t_1+" * "+t_2+" * "+t_3+" * "+t_4+"  == "+is_tag_time);
                //alert(t_5+" * "+t_6+" * "+t_7+" * "+t_8+"  == "+is_tag_time);
                if(Number(t_1)==Number(is_tag_time)){
                    is_ajax = 1;
                }else if(Number(t_2)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_3)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_4)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_5)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_6)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_7)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_8)==Number(is_tag_time)){
                 is_ajax = 1;
                }
                //alert(is_ajax+" & "+Number(is_tag_time));
                if(is_ajax==1){
                    var rec = "action=sas_add_webinar_opt_tag&video_time="+parseInt(c_time)+"&tag_apply_time="+is_tag_time+"&tag_id="+v.is_tag_id+"&name={$session_name}&email={$session_email}&webinar_id={$saw_webinar_id}&crm={$crm}";
                     jQuery.post("{$url_start_tag}",rec,function(data){
                      });
                }
            });
            }
        },6000);
        setInterval(function(){
            var c_time = vidds.currentTime();
            //alert(c_time);
            var btn_objcts = '{$is_tags_remove}';
             if(btn_objcts!='')
             {
            var objcts = JSON.parse(btn_objcts);
            var vb = c_time;
                //vb =  vb.split(".");
            jQuery.each(objcts,function(k,v){
                var vr = parseInt(vb);
                //alert(k+" = "+v.is_tag_time+" = "+v.is_tag_id+" - "+c_time);
                var is_ajax = 0;
                var t_1 = Number(vr)+Number(1);
                var t_2 = Number(vr)+Number(2);
                var t_3 = Number(vr)+Number(3);
                var t_4 = Number(vr)+Number(4);
                //alert(t_1+" * "+t_2+" * "+t_3+" * "+t_4+"  == "+v.is_tag_time);
                var t_5 = Number(vr)-Number(4);
                var t_6 = Number(vr)-Number(3);
                var t_7 = Number(vr)-Number(2);
                var t_8 = Number(vr)-Number(1);
                //alert(t_5+" * "+t_6+" * "+t_7+" * "+t_8+"  == "+v.is_tag_time);
                var hr_sec = Number(v.is_hr)*Number(60)*Number(60);
                var mint_sec = Number(v.is_mint)*Number(60)
                var  is_tag_time = Number(v.is_sec)+Number(mint_sec)+Number(hr_sec);
                //alert(t_1+" * "+t_2+" * "+t_3+" * "+t_4+"  == "+is_tag_time);
                //alert(t_5+" * "+t_6+" * "+t_7+" * "+t_8+"  == "+is_tag_time);
                if(Number(t_1)==Number(is_tag_time)){
                    is_ajax = 1;
                }else if(Number(t_2)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_3)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_4)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_5)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_6)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_7)==Number(is_tag_time)){
                 is_ajax = 1;
                }else if(Number(t_8)==Number(is_tag_time)){
                 is_ajax = 1;
                }
                //alert(is_ajax+" & "+Number(is_tag_time));
                if(is_ajax==1){
                    var rec = "action=sas_add_webinar_opt_tag&video_time="+parseInt(c_time)+"&tag_apply_time="+is_tag_time+"&tag_id="+v.is_tag_id+"&name={$session_name}&email={$session_email}&webinar_id={$saw_webinar_id}&crm={$crm}&opt=remove";
                     jQuery.post("{$url_start_tag}",rec,function(data){
                      });
                }
            });
            }
        },6000);
        vidds.on("ended", function(){
            setCookie("saw_webinar_video_ime",0,1*365);
             var rec = "action=sas_start_tag_apply&s=end&name={$session_name}&email={$session_email}&webinar_id={$saw_webinar_id}&crm={$crm}";
            jQuery.post("{$url_start_tag}",rec,function(data){
                setCookie("saw_webinar_replay_{$webinar_id}",1,1*365);
            });
        });
END;
			}
		}
		if ( (int) @$video['loop_video'] ) {
			$js .= <<<END
END;
		} else {
			$urrr = $video['end_url'];
			if ( $urrr == "" ) {
				$urrr = $video['splash_url'];
				if ( $urrr == "" ) {
					$urrr = "";
				}
			}
			$js .= <<<END
    var vid = this;
        vid.on("ended", function(){
    vid.posterImage.setSrc("{$urrr}");
        vid.posterImage.show();
        vid.bigPlayButton.show();
    });
END;
		}
		if ( (int) @$video['optin_gate'] ) {
			if ( ! isset( $video['plugin_saw'] ) ) {
				$js .= <<<END
    var vid_contr = this;
    var OptinendTime = $video[optin_start_time];
    var i_check = 1;
    setInterval(function(){
         var vid_contr_time = vid_contr.currentTime();
         if(vid_contr_time>OptinendTime-1){
            if(i_check==1){
            vid_contr.pause();
            jQuery("#optin_{$video['video_id']}_{$v_randCode}").fadeIn('fast');
            jQuery("#dialogforms_{$video['video_id']}_{$v_randCode}").fadeIn('slow');
            jQuery("#lod_{$video['video_id']}_{$v_randCode}").show();
            jQuery("#lod_{$video['video_id']}_{$v_randCode}").hide();
            jQuery('#crs_icon_{$video['video_id']}_{$v_randCode}').show();
            i_check++;
            }
         }
    },1000);
END;
			}
		}
		if ( $video['tagging_enable'] == 1 ) {
			if ( ! isset( $video['plugin_saw'] ) ) {
				$js .= <<<END
    var videoplayers = this;
    var starttimes = $video[optin_start];
    var ci=0;
    setInterval(function(){
        var vsd = Math.round(videoplayers.currentTime());
        if(ci==0){
            if(starttimes==vsd){
                //jQuery("#thumnailoverlay").fadeIn('fast');
                //jQuery('#lod').show();
                //jQuery('#lod').hide();
                var wid = jQuery('.cstom_outr').width();
                //jQuery("#dialog-forms").fadeIn('slow');
                jQuery('#svms_video_layer').css('max-width',wid+"px");
                jQuery('#svms_video_layer').fadeIn('slow');
                videoplayers.pause();
                videoplayers.posterImage.show();
                videoplayers.bigPlayButton.show();
                ci=1;
            }
        }
    },1000);
END;
			}
		}
		if ( $video['youtube_url'] ) {
			if ( $video['custom_play'] == 1 ) {
				$js .= <<<END
          var vid = this;
        var videoplayer = vid;
        var starttime = $video[video_start_time];
        var endTime = $video[video_end_time];
        var lop_video = $video[loop_video];
        var auto_play = $video[auto_play];
        videoplayer.currentTime(starttime); //not sure if player seeks to seconds or milliseconds
        if(auto_play==1){
            videoplayer.play();
        }
        setInterval(function(){
            var vd = videoplayer.currentTime();
            if(vd>endTime-1){
                videoplayer.currentTime(starttime);
                if(lop_video!=1){
                    videoplayer.pause();
                    videoplayer.posterImage.show();
                    videoplayer.bigPlayButton.show();
                }
            }
            if(vd<starttime){
                videoplayer.currentTime(starttime);
            }
        },1000);
END;
			}
			$js .= <<<END
END;
			$js = <<<END
        videojs('#vpp_video_{$video['video_id']}_{$v_randCode}', { "techOrder": ["youtube","html5","flash"], "sources": [{ "type": "video/youtube", "src": "{$video['youtube_url']}"}] }).ready(function(){
                  {$video['prepostrolljs']}
                  {$js}
            });
END;
		} else if ( $video['vimeo_url'] ) {
			if ( $video['custom_play'] == 1 ) {
				$js .= <<<END
          var vid = this;
        var videoplayer = vid;
        var starttime = $video[video_start_time];
        var endTime = $video[video_end_time];
        var lop_video = $video[loop_video];
        var auto_play = $video[auto_play];
        videoplayer.currentTime(starttime); //not sure if player seeks to seconds or milliseconds
        if(auto_play==1){
            videoplayer.play();
        }
        setInterval(function(){
            var vd = videoplayer.currentTime();
            if(vd>endTime-1){
                videoplayer.currentTime(starttime);
                if(lop_video!=1){
                    videoplayer.pause();
                    videoplayer.posterImage.show();
                    videoplayer.bigPlayButton.show();
                }
            }
            if(vd<starttime){
                videoplayer.currentTime(starttime);
            }
        },1000);
END;
			}
			if ( (int) @$video['auto_play'] ) {
				if ( self::isMobile_svms() ) {
					$is = "mobile";
				} else {
					// Do something for only desktop users
					$is = "desktop";
				}
				if ( $is == "desktop" ) {
					$js .= <<<END
    var vidss = this;
    var auto_play = $video[auto_play];
     if(auto_play==1){
            vidss.play();
        }
END;
				}
			}
			$js = <<<END
        videojs('#vpp_video_{$video['video_id']}_{$v_randCode}', { "techOrder": ["html5","flash","youtube","vimeo"], "sources": [{ "type": "video/vimeo", "src": "{$video['vimeo_url']}"}] }).ready(function(){
{$video['prepostrolljs']}
{$js}
});
END;
		} else {
			if ( $video['custom_play'] == 1 ) {
				$js .= <<<END
          var vid = this;
        var videoplayer = vid;
        var starttime = $video[video_start_time];
        var endTime = $video[video_end_time];
        var lop_video = $video[loop_video];
        videoplayer.currentTime(starttime); //not sure if player seeks to seconds or milliseconds
        setInterval(function(){
            var vd = videoplayer.currentTime();
            if(vd>endTime-1){
                videoplayer.currentTime(starttime);
                if(lop_video!=1){
                    videoplayer.pause();
                    videoplayer.posterImage.show();
                    videoplayer.bigPlayButton.show();
                }
            }
            if(vd<starttime){
                videoplayer.currentTime(starttime);
            }
        },1000);
END;
			}
			if ( $video['auto_play'] == 1 ) {
				$js .= <<<END
            var vidsr = this;
            vidsr.play();
END;
			}
			$js = <<<END
videojs('#vpp_video_{$video['video_id']}_{$v_randCode}', {"techOrder": ["html5","flash","youtube","vimeo"]}).ready(function(){
{$video['prepostrolljs']}
{$js}
});
END;
		}
		$js .= <<<END
            function close_start(id){
                jQuery("#optin_"+id).hide();
            jQuery("#dialogforms_"+id).hide();
                var myPlayer = videojs("#vpp_video_"+id);
                myPlayer.play();                
            }        
END;

		return $js;
	} // getJS end

	

	public static function getS3TempLink( $url ) {

		return $url;
	}


	public static function getSkinByType( $type, $frame, $width, $height ) {
		$code     = "";
		$w1       = $width + 40;
		$w2       = $width + 175 + 45;
		$w2_minus = $w2 / 10;
		$w2_plus  = $w2_minus;
		$w2_minus = "-" . $w2_minus;
        $w2_w     = esc_attr($width + 37);
        // $height = esc_attr($height);
        $height = 'auto';
        if ( $type == 1 ) {//Imac
            $pca_hold = "max-width:" . $w2_w . "px;";
            $pca_hold_pca_main = "height:" . $height . "px;";

            $code .= "
        <div class='pca-hold' style='$pca_hold'>
            <div class='pca-main' style='$pca_hold_pca_main'>
                <div class='pca-inner'>
                    $frame
                </div><!-- 'pca-inner' -->
            </div><!-- 'pca-main' -->
            <div class='pca-sub'>
                <div class='pca-top'></div>
                <div class='pca-mid'>
                <div class='pca-part'></div>
                </div><!-- 'pca-mid' -->
                <div class='pca-bot'></div>
            </div><!-- 'pca-bot' -->
        </div><!-- 'pca-hold' -->";
        } else if ( $type == 3 ) { //iPad
            $pca_hold = " max-width: " . $w2_w . "px;
            width: 100%;
            height: auto;
            border: 21px solid #1d2d2d;
            border-width: 30px 2px;
            border-color: #1d2d2d;
            border-radius: 25px;
            display: flex;";
			$code .= '<div class="pca-hold" style="'.$pca_hold.'">
                <div class="left">
                    <div class="circle">
                        <div class="inner"></div>
                    </div>
                </div>
                <div class="center">
                ' . $frame . '
                </div>
                <div class="right">
                    <div class="circle2"></div>
                </div>
            </div>';
		}
		if ( $type != 1 && $type != 3 ) {
			$plug = get_plugins();
			if ( array_key_exists( "svms_skins/svms_skins.php", $plug ) ) {
				if ( is_plugin_active( 'svms_skins/svms_skins.php' ) ) {
                    if ( $type == 2 ) { //Mac Book
                        $pca_hold_2 = "max-width: " . $w2_w . "px;
                        margin: 0 " . $w2_plus . "px;
                        margin-top: 1%;
                        margin-bottom: 1%;";
						
						$code .= "<div class='pca-hold-2' style='$pca_hold_2'>
  <div class='pca-main-2'>
    <div class='pca-inner-2'>
        $frame
    </div><!-- 'pca-inner-2' -->
  </div><!-- 'pca-main-2' -->
  <div class='pca-sub-2'>
    <div class='pca-top-2'></div>
    <div class='pca-mid'>
      <div class='pca-part'></div>
    </div><!-- 'pca-mid' -->
    <div class='pca-bot-2'></div>
  </div><!-- 'pca-bot-2' -->
</div><!-- 'pca-hold-2' -->";
                    } else if ( $type == 4 ) { //iPad4
                        $pca_hold_4 = " max-width: " . $w2_w . "px;";
                        $pca_hold_4_pca_main_4 = " max-height: ". $height . "px;";

						$code .= "<div class='pca-hold-4' style='$pca_hold_4'>
          <div class='pca-main-4' style='$pca_hold_4_pca_main_4'>
            <div class='pca-inner-4'>
            $frame
            </div><!-- 'pca-inner' -->
          </div><!-- 'pca-main' -->
          <div class='pca-sub-4'>
            <div class='pca-top'></div>
            <div class='pca-mid'>
              <div class='pca-part'></div>
            </div><!-- 'pca-mid' -->
            <div class='pca-bot-4'></div>
          </div><!-- 'pca-bot' -->
        </div><!-- 'pca-hold' -->";
					} else if ( $type == 5 ) {//iphone 5 Black
                        $w2_w = $w2_w - 11;
                        $pca_hold_5 = " max-width: " . $w2_w . "px;";
                        $pca_hold_5_pca_main_5 = "max-height: ". $height . "px;";
						
						$code .= "<div class='pca-hold-5'>
                        <div class='pca-main-5'>
                            <div class='pca-inner-5'>
                            $frame
                            </div><!-- 'pca-inner' -->
                        </div><!-- 'pca-main' -->
                        <div class='pca-sub-5'>
                            <div class='pca-top'></div>
                            <div class='pca-mid'>
                            <div class='pca-part'></div>
                            </div><!-- 'pca-mid' -->
                            <div class='pca-bot-5'></div>
                        </div><!-- 'pca-bot' -->
                        </div><!-- 'pca-hold' -->";
					} else if ( $type == 6 ) {//iphone 5 white
                        $w2_w = $w2_w - 10;
                        $pc_hold_6 = "  max-width: " . $w2_w . "px;";
                        $pca_hold_6_main_6 = "max-height:". $height."px;";

						$code .= "<div class='pca-hold-6' style='$pc_hold_6'>
  <div class='pca-main-6' style='$pca_hold_6_main_6'>
    <div class='pca-inner-6'>
    $frame
    </div><!-- 'pca-inner' -->
  </div><!-- 'pca-main' -->
  <div class='pca-sub-6'>
    <div class='pca-top'></div>
    <div class='pca-mid'>
      <div class='pca-part'></div>
    </div><!-- 'pca-mid' -->
    <div class='pca-bot-6'></div>
  </div><!-- 'pca-bot' -->
</div><!-- 'pca-hold' -->";
					} else if ( $type == 7 ) {//iphone 6 Black
                        $w2_w = $w2_w - 19;
                        $pca_hold_7 = "max-width: $w2_w px;";
                        $pca_hold_7_pca_main_7 = "max-height: $height px;";
						
						$code .= "<div class='pca-hold-7' style='$pca_hold_7'>
  <div class='pca-main-7' style='$pca_hold_7_pca_main_7'>
    <div class='pca-inner-7'>
        $frame
    </div><!-- 'pca-inner' -->
  </div><!-- 'pca-main' -->
  <div class='pca-sub-7'>
    <div class='pca-top'></div>
    <div class='pca-mid'>
      <div class='pca-part'></div>
    </div><!-- 'pca-mid' -->
    <div class='pca-bot-7'></div>
  </div><!-- 'pca-bot' -->
</div><!-- 'pca-hold' -->";
					} else if ( $type == 8 ) {//iphone 6 White
                        $w2_w = $w2_w - 19;
                        $pca_hold_8 = "max-width: $height px;";
                        $pca_hold_8_pca_main_8 = "max-height: $height px;";
						$code .= "<div class='pca-hold-8' style='$pca_hold_8'>
                        <div class='pca-main-8' style='$pca_hold_8_pca_main_8'>
                            <div class='pca-inner-8'>
                            $frame
                            </div><!-- 'pca-inner' -->
                        </div><!-- 'pca-main' -->
                        <div class='pca-sub-8'>
                            <div class='pca-top'></div>
                            <div class='pca-mid'>
                            <div class='pca-part'></div>
                            </div><!-- 'pca-mid' -->
                            <div class='pca-bot-8'></div>
                        </div><!-- 'pca-bot' -->
                        </div><!-- 'pca-hold' -->";
					}
				} else { // skins plugin deactivated
					$code .= $frame;
				}
			} else {//skin plugin not installed
				$code .= $frame;
			}
		}

		return $code;
	}


	public static function prepRequest( $method, $params = null, $id = null ) {
		$array = array( get_option( "svms_apikey_getresponse" ) );
		if ( ! is_null( $params ) ) {
			$array[1] = $params;
		}
		$request = json_encode( array( 'method' => $method, 'params' => $array, 'id' => $id ) );

		return $request;
	}


	public static function getTsags( $app ) {
		$fields       = array( 'Id', 'GroupName' );
		$query        = array( 'Id' => '%' );
		$need_request = true;
		$page         = 0;
		$all_lists    = array();
		while ( true == $need_request ) {
			$result    = $app->dsQuery( 'ContactGroup', 1000, $page, $query, $fields );
			$all_lists = array_merge( $all_lists, $result );
			if ( 1000 > count( $result ) ) {
				$need_request = false;
			} else {
				$page ++;
			}
		}

		return $all_lists;
	}



	public static function encode_base64( $s_data ) {
		$result = strtr( base64_encode( $s_data ), '+/', '-_' );

		return $result;
	}

	public static function decode_base64( $s_data ) {
		$result = base64_decode( strtr( $s_data, '-_', '+/' ) );

		return $result;
	}

	public static function array_stripslashes( $array ) {
		if ( is_array( $array ) ) {
			foreach ( $array as $field => $value ) {
				if ( is_array( $value ) ) {
					$array[ $field ] = self::array_stripslashes( $value );
				} else {
					$array[ $field ] = stripslashes( $value );
				}
			}
		}

		return $array;
	}

	public static function starApiKey( $api_key ) {
		//return $api_key;
		$stars = strlen( trim( $api_key ) ) - 6;

		return @str_repeat( '*', $stars ) . substr( $api_key, - 6 );
	}

	public static function starApiKey2( $api_key ) {
		$stars = strlen( trim( $api_key ) ) - 4;

		return @str_repeat( '*', $stars ) . substr( $api_key, - 4 );
	}
}
