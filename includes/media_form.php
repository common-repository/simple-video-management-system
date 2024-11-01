<div id="vpp_form_popup" style="display: none; height: auto;">
	<div class="wrap" style="height: auto;">
		<div style="height: auto;">
			<div style="padding: 15px 15px 0 15px;">
				<h3 class="vpp_popup_h3">Search for a Video...</h3>
				<input type="text" id="vpp_search_string" name="vpp_search_string" size="35" />
				<input type="button" value="Search" onclick="vpp_searchVideo()" class="button" />
				<img id="vpp_loader_ball" src="<?php echo $load_ball_url ?>" width="32" height="32" style="float: right; display: none;" />
				<div id="vpp_search_results" style="margin-top: 3px; clear: both;"></div>
			</div>
			<div style="padding: 15px 15px 0 15px;">
				<h3 class="vpp_popup_h3">Add a Video...</h3>
				<form id="vpp_video_add">
					<table class="vpp_form_table" cellpadding="0" cellspacing="5">
						<tr>
							<th>Name/Title:</th>
							<td><input type="text" id="vpp_form_name" name="form_vars[name]" value="" size="50" /></td>
						</tr>
						<tr>
							<th>Tags:</th>
							<td><input type="text" id="tags" name="form_vars[tags]" value="" size="50" /></td>
						</tr>
                        <?php
                        $vid_source_array = array(0=>"YouTube",1=>"MP4",2=>"WebM",3=>"OGG",4=>"MOV",5=>"Vimeo");
                        
                        
                        ?>
                        <tr>
                            <th>Video Source:</th>
                            <td>
                                <select id="vid_source" name="form_vars[vid_source]">
                                    <?php
                                        foreach($vid_source_array as $k=>$v){
                                            
                                                echo "<option value='".esc_attr($k)."' >".esc_attr($v)."</option>";
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>
						<tr>
							<th>Video URL:</th>
							<td><input type="text" id="vid_url" name="form_vars[vid_url]" value="" size="50" /></td>
						</tr>
					
						<tr>
							<th>Splash Image URL:</th>
							<td><input type="text" id="vpp_form_splash_url" name="form_vars[splash_url]" value="" size="50" /></td>
						</tr>
                        <tr>
                            <th>Video Size Type:</th>
                            <td>
                            <select name="form_vars[size_type]" id="video_type">
                            <?php
                            $size_list = array(
                                	'0'		=> '16:9',
                                	'1'	=> '4:3',
                                	'2'		=> 'Custom'
                                );
                                
                                        foreach($size_list as $k=>$v){
                                            
                                                echo "<option value='$k' >$v</option>";
                                        }

                            ?></select>
                            </td>
                        </tr>
						<tr>
							<th>Video Width:</th>
							<td><input type="text" name="form_vars[width]" id="width_v" value="1080" size="10" /></td>
						</tr>
						<tr>
							<th>Video Height:</th>
							<td><input type="text"  name="form_vars[height]" id="height_v" value="607" size="10" /></td>
						</tr>
						<tr>
							<th>Align:</th>
							<td><select id="vpp_form_align" name="form_vars[align]">
								<option value="">-- None --</option>
								<option value="left">Left</option>
								<option value="center">Center</option>
								<option value="right">Right</option>
							</select></td>
						</tr>
					</table>
				</form>
			</div>
			<div style="padding: 15px;">
				<input type="button" class="button-primary" value="Submit &amp; Insert" onclick="vpp_submitVideo();" />&nbsp;&nbsp;&nbsp;&nbsp; <a class="button" href="#" onclick="tb_remove(); return false;">Cancel</a>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div style="clear: both;"></div>
	</div>
	<div style="clear: both;"></div>
</div>