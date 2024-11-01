
<div id="vpp_admin">
	<h1 style="margin: 5px;">Simple Video Management System <span style="font-size: 12px;">v<?php echo VPP_VERSION ?></span>
    </h1>
    <div class="svms-video-limit">Check out our <a href="https://namstoolkit.com/wpSVMS" target="_blank">PREMIUM VERSION</a> for more features and funtionalities.
    </div>

	<table id="vpp_layout_table" style="width: 99%;">
	<tr>
		<td>
			<ul id="vpp_nav_tabs">
				<li<?php if (self::$action == 'videos' || self::$action == 'video_edit'){ echo ' class="selected"'; } ?>><a href="admin.php?page=<?php echo esc_attr(self::$name) ?>&action=videos">Videos</a></li>
                <?php
                if(isset($_GET['analytics_video']) && $_GET['analytics_video']!=""){
                ?>
                    <li<?php if (self::$action == 'videos' || self::$action == 'video_edit' || self::$action == 'video_analytics' ){ echo ' class="selected"'; } ?>><a href="admin.php?page=<?php echo esc_attr(self::$name) ?>&action=video_analytics&analytics_video=<?php echo $_GET['analytics_video'] ?>">Analytics</a></li>    
                <?php
                }
                ?>
				<li<?php if (self::$action == 'grid_shortcode'){ echo ' class="selected"'; } ?>><a href="admin.php?page=<?php echo esc_attr(self::$name) ?>&action=grid_shortcode">Grid</a></li>
                <li<?php if (self::$action == 'groups'){ echo ' class="selected"'; } ?>><a href="admin.php?page=<?php echo esc_attr(self::$name) ?>&action=groups">Groups</a></li>
               
				<li<?php if (self::$action == 'settings'){ echo ' class="selected"'; } ?>><a href="admin.php?page=<?php echo esc_attr(self::$name) ?>&action=settings">Settings</a></li>
	
			</ul>
			<div id="vpp_view_wrapper">
				<div id="vpp_view_box" style="background-color: #fff;">
					<div style="margin: 15px;">
					<?php require($view_path); ?>
					</div>
				</div>
				<div id="vpp_spacer"></div>
				<div style="clear: both;"></div>
			</div>
		</td>
		<td width="15" style="width: 15px;">&nbsp;</td>
		<td id="vpp_news" style="min-width: 200px; max-width: 300px; background-color:#FFF">
             <img style="width: 230px;" class="img-responsive" src="<?= plugins_url('../../../../assets/NTKLogo.png', __FILE__) ?>">
             <?php
				$response = wp_remote_get('https://namstoolkit.com/wp-admin/admin-ajax.php?action=ntks_getads&p=svms');
				$response_body = wp_remote_retrieve_body($response); 
				print_r($response_body);
			 ?>
		</td>
	</tr>
	</table>
</div>