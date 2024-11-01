<?php
global $wpdb;
$c_id        = get_current_user_id();
$user        = new WP_User( $c_id );
$u_role      = $user->roles[0];
$role_detail = get_role( $u_role );
if ( isset( $_GET['video_id'] ) && $_GET['video_id'] != "" ) {
	$video_id = sanitize_text_field($_GET['video_id']);
	$query = $wpdb->prepare("SELECT * FROM " . self::$table['video'] . " WHERE video_id = %d", [$video_id]);
	$check_video = $wpdb->get_row($query, ARRAY_A );
	if ( count( $check_video ) > 0  && current_user_can('administrator')) {
		$wpdb->query( 
			$wpdb->prepare("DELETE FROM " . self::$table['video'] . " WHERE video_id = %d",[$video_id])
		);
	}
}
$page      = 1;
$page_size = 25;
$prev_url  = '';
$next_url  = '';
if ( isset( $_GET['p'] ) ) {
	$page = (int) $_GET['p'];
}
$sql_search_1 = "";
if ( isset( $_REQUEST['group_id'] ) ) {
	$sql_search_1 = ' WHERE group_id == "%' . sanitize_text_field($_REQUEST['group_id']) . '%" ';
}

if ( isset( $_REQUEST['search'] ) && $_REQUEST['search'] != "" ) {
	$search = sanitize_text_field($_REQUEST['search']);
	$sql_search = ' WHERE (
		name like "%' . addslashes( $search ) . '%"
		OR handle like "%' . addslashes( $search ) . '%"
		OR mp4_url like "%' . addslashes( $search ) . '%"
		OR youtube_url like "%' . addslashes( $search ) . '%"
		OR webm_url like "%' . addslashes( $search ) . '%"
		OR ogg_url like "%' . addslashes( $search ) . '%"
        OR mov_url like "%' . addslashes( $search ) . '%"
        OR vimeo_url like "%' . addslashes( $search ) . '%"
        OR tags like "%' . addslashes( $search ) . '%"
	)';

	$total = $wpdb->get_var( 'SELECT COUNT(*) AS total FROM ' . self::$table['video'] . " $sql_search" );
} else {
	$total = $wpdb->get_var( 'SELECT COUNT(*) AS total FROM ' . self::$table['video'] . " $sql_search_1" );
}
$max_page = ceil( $total / $page_size );
if ( ! $page ) {
	$page = 1;
}
if ( $page > $max_page ) {
	$page = $max_page;
}
$start = ( $page - 1 ) * $page_size;
if ( $start < 0 ) {
	$start = 0;
}
$sql_search = '';
if ( isset( $_REQUEST['search'] ) ) {
	$search = sanitize_text_field($_REQUEST['search']);

	$sql_search = ' WHERE (
		name like "%' . addslashes( $search ) . '%"
		OR handle like "%' . addslashes( $search ) . '%"
		OR mp4_url like "%' . addslashes( $search ) . '%"
		OR youtube_url like "%' . addslashes( $search ) . '%"
		OR webm_url like "%' . addslashes( $search ) . '%"
		OR ogg_url like "%' . addslashes( $search ) . '%"
        OR mov_url like "%' . addslashes( $search ) . '%"
        OR vimeo_url like "%' . addslashes( $search ) . '%"
        OR tags like "%' . addslashes( $search ) . '%"
	)';
}
if ( isset( $_REQUEST['group_id'] ) && $_REQUEST['group_id'] != 0 ) {
	$sql_search = ' where group_id == "%' . sanitize_text_field($_REQUEST['group_id']) . '%" ';
}
//echo $sql_search;
if ( isset( $_REQUEST['sort_by'] ) && $_REQUEST['sort_by'] == 'created' ) {
	$sort_by = 'created DESC';
} else if ( @$_REQUEST['sort_by'] == "lb" ) {
	$sort_by = 'lightbox_enabled DESC';
} else {
	$sort_by = 'name';
}
$video_list = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . self::$table['video'] . $sql_search . ' ORDER BY ' . $sort_by . ' LIMIT %d, %d',[$start, $page_size]), ARRAY_A );
if ( $page > 1 ) {
	$prev_url = 'admin.php?page=' . esc_attr(self::$name) . '&action=videos&search=' . urlencode( trim( @$_REQUEST['search'] ) ) . '&sort_by=' . @$_REQUEST['sort_by'] . '&p=' . ( $page - 1 );
}
if ( $page < $max_page ) {
	$next_url = 'admin.php?page=' . esc_attr(self::$name) . '&action=videos&search=' . urlencode( trim( @$_REQUEST['search'] ) ) . '&sort_by=' . @$_REQUEST['sort_by'] . '&p=' . ( $page + 1 );
}
$end = $start + $page_size;
if ( $end > $total ) {
	$end = $total;
}
if ( ! $total ) {
	$start = - 1;
	$end   = 0;
}
if ( count( $video_list ) > 0 || isset( $_REQUEST['search'] ) ) {
	$filename = VPP_DIR_PATH . "includes/exported_videos.txt";
	$myfile = fopen( $filename, "w" ) or die( "Unable to open file!" );
	$fieldArray = $wpdb->get_results( "select * from " . self::$table['video'] . " order by name", ARRAY_A );
	fwrite( $myfile, json_encode( $fieldArray ) );
	fclose( $myfile );
	$url = admin_url();
	$url = str_replace( "wp-admin", "wp-content", $url );
	$url = plugins_url('../../../../exported_videos.txt', __FILE__);
	function DBouts( $string ) {
		$string = stripslashes( trim( $string ) );

		return str_replace( "&#039;", "'", html_entity_decode( $string, ENT_QUOTES, 'UTF-8' ) );
	}

	?>
    <div class="vpp_button_bar" style="padding-bottom: 10px; width: 40%; float: right;">
		<?php if ( ! (int) @$config['max_videos'] || ( $total < (int) @$config['max_videos'] ) ) { ?>
			<?php
			$u_add = 1;
			if ( $u_role != "administrator" ) {
				if ( isset( $role_detail->capabilities['ntk_svms_add_videos'] ) && $role_detail->capabilities['ntk_svms_add_videos'] == 1 ) {
					$u_add = 1;
				} else if ( isset( $role_detail->capabilities['ntk_svms_add_videos'] ) && $role_detail->capabilities['ntk_svms_add_videos'] == "" ) {
					$u_add = 0;
				}
			}
			if ( $u_add == 1 ) {
				?>
                <input type="button" value="Add New"
                       onclick="window.location.href='admin.php?page=<?php echo esc_attr(self::$name) ?>&action=video_edit'"
                       class="button-primary action"/>
				<?php
			}
		} else { ?>
            <input type="button" value="Add New" alt="Max video limit reached" title="Maximum allowed videos reached"
                   disabled/>
		<?php } ?>
    </div>
	<?php
	$clr = "display:none;";
	if ( ! isset( $_REQUEST['search'] ) ) {
		$clr = "display:none;";
	} else {
		if ( $_REQUEST['search'] == "" ) {
			$clr = "display:none;";
		} else {
			$clr = "";
		}
	}
	?>
    <div style="width: 60%; float: left;">
        <form action="admin.php" method="get">
            <input type="hidden" name="page" value="s3_video_player_plus"/>
            <input type="text" name="search" onblur="checksval()" id="search"
                   value="<?php echo esc_attr($_REQUEST['search']) ?>" size="35"/>
            <input type="submit" value="Search" class="button-primary action"/>
            &nbsp;<input type="button"
                         onclick="window.location.href='admin.php?page=<?php echo esc_attr(self::$name) ?>&action=videos'"
                         value="Clear" id="clear_btn" class="button-primary" style="<?php echo esc_attr($clr); ?>"/>
            <br/>
            <nobr>(Search by Name/Title, Video URL, Categories, or Shortcode "id")</nobr>
        </form>
        <br/>
    </div>
    <div style="clear: both"></div>
	<?php
		$changeSortBy = "function changeSortBy(sort_by) {
            window.location.href = 'admin.php?page=".esc_attr(self::$name) . "&action=videos&search=". esc_url(urlencode( trim( @$_REQUEST['search'] ) )). "&p=" .urlencode( trim( @$_REQUEST['p'] ) ) . "&sort_by=' + sort_by + '&group_id=' + jQuery('#group_id').val();
		}";
		wp_add_inline_script('svms-scripts', $changeSortBy);
	?>
    <div class="vpp_pagination">
        <div class="displaying">
            Sort by:&nbsp;
			<select name="sort_by" id="sort_by" onchange="changeSortBy(this.value)">
				<?php
					$sort_by = sanitize_text_field($_REQUEST['sort_by']);
					$search = sanitize_text_field($_REQUEST['search']);
				?>
                <option value="name" <?php if ( $sort_by == 'name' ) {
					echo ' selected';
				} ?>>Name/Title
                </option>
                <option value="created" <?php if ( $sort_by == 'created' ) {
					echo ' selected';
				} ?>>Date Created
                </option>
                <option value="lb" <?php if ( $sort_by == 'lb' ) {
					echo ' selected';
				} ?>>Lightbox Enabled
                </option>
            </select>
            Displaying:&nbsp;<?php echo $start + 1 ?>-<?php echo $end ?>&nbsp;of&nbsp;<?php echo $total ?>
        </div>
        <div class="pages">
			<?php
			$page_list = array();
			for ( $i = 1; $i <= $max_page; $i ++ ) {
				$selected = '';
				if ( $i == $page ) {
					$selected = ' class="selected"';
				}
				$page_list[] = '<a href="admin.php?page=' . esc_attr(self::$name) . '&action=videos&search=' . urlencode( trim( $search ) ) . '&sort_by=' . $sort_by . '&p=' . $i . '"' . $selected . '>' . $i . '</a>';
			}
			if ( $page_list ) {

				echo implode( ', ', $page_list );
			}
			?>
        </div>
        <div class="next_prev"><?php
			$nav = array();
			if ( $prev_url ) {
				$nav[] = '<a href="' . $prev_url . '">Prev</a>';
			} else {
				$nav[] = '<span class="disabled">Prev</span>';
			}
			if ( $next_url ) {
				$nav[] = '<a href="' . $next_url . '">Next</a>';
			} else {
				$nav[] = '<span class="disabled">Next</span>';
			}
			if ( $nav ) {

				echo implode( '&nbsp;|&nbsp;', $nav );
			}
			?>
        </div>
    </div>
    <div style="clear: both;"></div>
    <div>
        Group by: <select name="group_id" id="group_id" onchange="searchbygroup(this.value)">
            <option value="0">All</option>
			<?php
			$groups = $wpdb->get_results( 'SELECT * FROM ' . self::$table['video_groups'], ARRAY_A );
			foreach ( $groups as $v ) {
				if ( $v['id'] == @$_REQUEST['group_id'] ) {
					echo "<option value='" . esc_attr($v['id']) . "' selected='selected'>" . DBouts( esc_attr($v['name']) ) . "</option>";
				} else {
					echo "<option value='" . esc_attr($v['id']) . "'>" . DBouts( esc_attr($v['name']) ) . "</option>";
				}
			}

		?>
        </select>
    </div>
    
    <table class="vpp_list_table" cellspacing="1" cellpadding="0">
        <tr>
            <th width="50%">Name/Title</th>
            <th nowrap="nowrap">Shortcode</th>
            <th nowrap="nowrap">Video Source</th>
            <th nowrap="nowrap">Created</th>
            <th width="2%">LB</th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
        </tr>
		<?php
		if ( $video_list ) {
			$i     = 0;
			$total = count( $video_list );
			foreach ( $video_list as $video ) {
				$u_add = 1;
				if ( $u_role != "administrator" ) {
					if ( isset( $role_detail->capabilities['ntk_svms_edit_videos'] ) && $role_detail->capabilities['ntk_svms_edit_videos'] == 1 ) {
						$u_add = 1;
					} else if ( isset( $role_detail->capabilities['ntk_svms_edit_videos'] ) && $role_detail->capabilities['ntk_svms_edit_videos'] == "" ) {
						$u_add = 0;
					}
				}
				if ( $u_add == 1 ) {
					$edit_url = 'admin.php?page=' . esc_attr(self::$name) . '&action=video_edit&video_id=' . esc_attr($video['video_id']);
				} else {
					$edit_url = "#";
				}
				$delete_url  = 'admin.php?page=' . esc_attr(self::$name) . '&video_id=' . esc_attr($video['video_id']);
				$mp4_snippet = '--';
				if ( $video['vid_url'] == "" ) {
					if ( $video['youtube_url'] != "" ) {
						//$video['youtube_url'], $video['video_id']
						$wpdb->prepare("UPDATE " . self::$table['video'] . " set vid_url=%s, vid_source='0' where video_id=%d", [$video['youtube_url'], $video['video_id']]);
						$wpdb->query( $wpdb->prepare("UPDATE " . self::$table['video'] . " set vid_url=%s, vid_source='0' where video_id=%d", [$video['youtube_url'], $video['video_id']]));
						$video['vid_url']    = $video['youtube_url'];
						$video['vid_source'] = 0;
					} else if ( @$video['mp4_url'] != "" ) {
						$wpdb->query($wpdb->prepare("UPDATE " . self::$table['video'] . " set vid_url=%s, vid_source='0' where video_id=%d", [$video['mp4_url'], $video['video_id']]));
						$video['vid_url']    = $video['mp4_url'];
						$video['vid_source'] = 1;
					} else if ( @$video['webm_url'] != "" ) {
						$wpdb->query( $wpdb->prepare("UPDATE " . self::$table['video'] . " set vid_url=%s, vid_source='0' where video_id=%d", [$video['webm_url'], $video['video_id']]));
						$video['vid_url']    = $video['webm_url'];
						$video['vid_source'] = 2;
					} else if ( @$video['ogg_url'] != "" ) {
						$wpdb->query( $wpdb->prepare("UPDATE " . self::$table['video'] . " set vid_url=%s, vid_source='0' where video_id=%d", [$video['ogg_url'], $video['video_id']]));
						$video['vid_url']    = $video['ogg_url'];
						$video['vid_source'] = 3;
					} else if ( @$video['mov_url'] != "" ) {
						$wpdb->query( $wpdb->prepare("UPDATE " . self::$table['video'] . " set vid_url=%s, vid_source='0' where video_id=%d", [$video['mov_url'], $video['video_id']]) );
						$video['vid_url']    = $video['mov_url'];
						$video['vid_source'] = 4;
					} else if ( @$video['vimeo_url'] != "" ) {
						$wpdb->query($wpdb->prepare("UPDATE " . self::$table['video'] . " set vid_url=%s, vid_source='0' where video_id=%d", [$video['vimeo_url'], $video['video_id']]));
						$video['vid_url']    = $video['vimeo_url'];
						$video['vid_source'] = 5;
					}
				}
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
				$videoSource = "";
				if ( $video['mp4_url'] != "" ) {
					$mp4_snippet = '<a href="' . esc_url($video['mp4_url']) . '" target="_blank" title="Link to MP4">MP4</a>';
					$videoSource = $mp4_snippet;
				} else if ( $video['youtube_url'] != "" ) {
					$yt_snippet  = '<a href="' . esc_url($video['youtube_url']) . '" target="_blank" title="Link to Youtube">YT</a>';
					$videoSource = $yt_snippet;
				} else if ( $video['webm_url'] != "" ) {
					$webm_snippet = '<a href="' . esc_url($video['webm_url']) . '" target="_blank" title="Link to WebM">WebM</a>';
					$videoSource  = $webm_snippet;
				} else if ( $video['ogg_url'] != "" ) {
					$ogg_snippet = '<a href="' . esc_url($video['ogg_url']) . '" target="_blank" title="Link to OGG">OGG</a>';
					$videoSource = $ogg_snippet;
				} else if ( $video['mov_url'] != "" ) {
					$mov_snippet = '<a href="' . esc_url($video['mov_url']) . '" target="_blank" title="Link to MOV">MOV</a>';
					$videoSource = $mov_snippet;
				} else if ( $video['vimeo_url'] != "" ) {
					$vim_snippet = '<a href="' . esc_url($video['vimeo_url']) . '" target="_blank" title="Link to Vimeo">Vimeo</a>';
					$videoSource = $vim_snippet;
				}
				if ( $video['created'] == 0 ) {
					$video['created'] = "1462340486";
				}
				?>
                <tr>
                    <td><a href="<?php echo $edit_url ?>"
                           title="Edit Product"><?php echo esc_attr(DBouts( ( $video['name'] ) )); ?></a></td>
                    <td nowrap="nowrap" align="center"><input type="text"
                                                              value="<?php echo htmlspecialchars( '[s3vpp id=' . esc_attr($video['handle']) . ']' ) ?>"
                                                              style="min-width: 200px; width: 99%;"
                                                              onfocus="this.select();" onmouseup="return false;"/></td>
                    <td nowrap="nowrap" align="center"><?php echo $videoSource ?></td>
                    <td nowrap="nowrap" align="center"><?php echo date( 'Y-m-d g:i A', $video['created'] ) ?></td>
                    <td nowrap="nowrap" align="center"><?php if ( $video['lightbox_enabled'] == 1 ) {
							echo "<span style='color:green'><b>Y</b></span>";
						} else {
							echo "<span style='color:red'>N</span>";
						} ?></td>
                    <td align="right"><a onclick="return delete_svms_video()" href="<?php echo $delete_url ?>"><img
                                    src="<?php echo esc_url(self::$plugin_url) ?>includes/images/del.png" width="16" height="16"
                                    alt="Delete" title="Delete" border="0"/></a></td>
                    <td align="right"><a href="<?php echo $edit_url ?>"><img
                                    src="<?php echo esc_url(self::$plugin_url) ?>includes/icons/pencil.png" width="16"
                                    height="16" alt="Edit" title="Edit" border="0"/></a></td>
                    <td align="right"><a href="JavaScript:void(0);" title="Copy Video"
                                         onclick="return CopyVideo('<?php echo intval( $video['video_id'] ); ?>')"><img
                                    src="<?php echo esc_url(self::$plugin_url) ?>includes/icons/Duplicate.png" width="16"
                                    height="16" alt="Copy Video" title="Copy Video" border="0"/></a></td>
                </tr>
				<?php
			}
		} else {
			?>
            <tr>
                <td class="vpp_empty" colspan="12" align="center"><br/>
                    List is Empty<br/>
                    <br/>
                </td>
            </tr>
			<?php
		}
		?>
    </table>
    <div class="vpp_pagination" style="margin-top: 10px;">
        <div class="displaying">
            &nbsp;
        </div>
        <div class="pages">
			<?php
			$page_list = array();
			for ( $i = 1; $i <= $max_page; $i ++ ) {
				$selected = '';
				if ( $i == $page ) {
					$selected = ' class="selected"';
				}
				$page_list[] = '<a href="admin.php?page=' . esc_attr(self::$name) . '&action=videos&search=' . urlencode( trim( @$_REQUEST['search'] ) ) . '&p=' . $i . '&sort_by=' . $sort_by . '"' . $selected . '>' . $i . '</a>';
			}
			if ( $page_list ) {
				/*if (!isset($_REQUEST['search'])){
				   echo implode(', ', $page_list);
				}else{
					if(isset($_REQUEST['search']) && $_REQUEST['search']==""){
						echo implode(', ', $page_list);
					}
				}*/
				echo implode( ', ', $page_list );
			}
			?>
        </div>
        <div class="next_prev">
			<?php
			$nav = array();
			if ( $prev_url ) {
				$nav[] = '<a href="' . esc_attr($prev_url) . '">Prev</a>';
			} else {
				$nav[] = '<span class="disabled">Prev</span>';
			}
			if ( $next_url ) {
				$nav[] = '<a href="' . esc_attr($next_url) . '">Next</a>';
			} else {
				$nav[] = '<span class="disabled">Next</span>';
			}
			if ( $nav ) {
				/*if (!isset($_REQUEST['search'])){
					echo implode('&nbsp;|&nbsp;', $nav);
				}else{
					if(isset($_REQUEST['search']) && $_REQUEST['search']==""){
						echo implode('&nbsp;|&nbsp;', $nav);
					}
				}*/
				echo implode( '&nbsp;|&nbsp;', $nav );
			}
			?>
        </div>
    </div>
	<?php
} else {
	?>
    <div class="vpp_button_bar" style="padding-bottom: 10px; width: 40%; float: right;">
        <input type="button" value="Add New"
               onclick="window.location.href='admin.php?page=<?php echo esc_attr(self::$name) ?>&action=video_edit'"
               class="button-primary action"/>
    </div>
    <div style="clear: both"></div>
	<?php
}

$copyVideo = "function CopyVideo(id) {
	jQuery.post('".esc_url(admin_url())."admin-ajax.php?action=svm_copy_video', video_id=+id, function (data) {
		if (data == 0) {
			alert('An error occured. Please Try Again!');
		} else {
			window.location = '". esc_url(admin_url()). "admin.php?page=s3_video_player_plus&action=video_edit&video_id=' + data;
		}
	});
}";

wp_add_inline_script('svms-scripts', $copyVideo);

$request_p = sanitize_text_field($_REQUEST['p']);
$search = sanitize_text_field($_REQUEST['search']);

$searchByGroup = "function searchbygroup(val) {
	window.location.href = 'admin.php?page=". esc_attr(self::$name) . "&action=videos&search=". urlencode( trim($search ) ) . "&p=". urlencode( trim( $request_p ) ) . "&sort_by=' + jQuery('#sort_by').val() + '&group_id=' + val;
}";
wp_add_inline_script('svms-scripts', $searchByGroup);