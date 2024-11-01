<?php
if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
    $scheme = 'https';
else
    $scheme = 'http';

require VPP_APP_PATH.'/Vpp_Form.php';
//echo get_option( 'rlm_license_key_s3_video_player_plus');
//echo self::$table['video'];
if (isset($_REQUEST['video_id']) && $_REQUEST['video_id'] && !self::$form_vars)
{       $video_id = sanitize_text_field($_REQUEST['video_id']);
	    self::$form_vars = $wpdb->get_row(
        $wpdb->prepare(
            'SELECT * FROM '.self::$table['video'].' WHERE video_id=%d',
            intval($video_id)
        )
    , ARRAY_A);

    if(@self::$form_vars['vid_url']==""){
          
            if(@self::$form_vars['youtube_url']!=""){
                $wpdb->query($wpdb->prepare("UPDATE ".self::$table['video']." SET vid_url='%s', vid_source='0' where video_id=%d",[$form_vars['youtube_url'],$form_vars['video_id']]));

            }else if(@self::$form_vars['mp4_url']!=""){
                $wpdb->query($wpdb->prepare("UPDATE ".self::$table['video']." SET vid_url='%s', vid_source='1' where video_id=%d",[$form_vars['mp4_url'],$form_vars['video_id']]));
            }else if(@self::$form_vars['webm_url']!=""){
                $wpdb->query($wpdb->prepare("UPDATE ".self::$table['video']." SET vid_url='%s', vid_source='2' where video_id=%d",[$form_vars['webm_url'],$form_vars['video_id']]));
            }else if(@self::$form_vars['ogg_url']!=""){
                $wpdb->query($wpdb->prepare("UPDATE ".self::$table['video']." SET vid_url='%s', vid_source='3' where video_id=%d",[$form_vars['ogg_url'],$form_vars['video_id']]));
            }else if(@self::$form_vars['mov_url']!=""){
                $wpdb->query($wpdb->prepare("UPDATE ".self::$table['video']." SET vid_url='%s', vid_source='4' where video_id=%d",[$form_vars['mov_url'],$form_vars['video_id']]));
            }else if(@self::$form_vars['vimeo_url']!=""){
                $wpdb->query($wpdb->prepare("UPDATE ".self::$table['video']." SET vid_url='%s', vid_source='5' where video_id=%d",[$form_vars['vimeo_url'],$form_vars['video_id']]));
            }
            
    }
  $video_id = sanitize_text_field($_REQUEST['video_id']);
  self::$form_vars = $wpdb->get_row(
    $wpdb->prepare(
        'SELECT * FROM '.self::$table['video'].' WHERE video_id=%d',
        intval($video_id)
    )
  , ARRAY_A);
	self::$form_vars['config'] = unserialize(@self::$form_vars['config']);
}
elseif (!self::$form_vars)
{
	self::$form_vars['name']			= '';
}
if (!isset($_REQUEST['video_id']) || !intval($_REQUEST['video_id']))
{
	$_REQUEST['video_id'] = 0;	
}

function DBoutss($string){
	$string = stripslashes(trim($string));
	return str_replace("&#039;","'",html_entity_decode($string,ENT_QUOTES,'UTF-8'));
}

$skins = array(0=>'Default',1=>'iMac',3=>"iPad");  
 

/******/ 

    if (isset(self::$form_vars['video_id']))
    {
        $get_res = $wpdb->get_results(
            $wpdb->prepare(
                "select * from ".self::$table['video_location']." where video_id=%d",
                sanitize_text_field(self::$form_vars['video_id'])
            )
        ,ARRAY_A);
        if(count($get_res)>0){
            foreach($get_res as $post_key=>$post_val){
                $cont_post = get_post_field('post_content', $post_val['post_id']);
                if($cont_post!=""){
                    $b = sanitize_text_field(self::$form_vars['handle']);
                    //if (!preg_match_all('/\[s3vpp id\=([0-9a-zA-Z]+)\]/is', $cont_post, $match_list)){
                        if (!preg_match_all('/\[s3vpp id\='.$b.'\]/is', $cont_post, $match_list)){
                        //$wpdb->query("delete from ".self::$table['video_location']." where video_id='".$post_val['video_id']."' and post_id='".$post_val['post_id']."'");
                    }
                }
            }
        }
    }
/*******/

//if (!@self::$form_vars['width'])
//{
//	self::$form_vars['width'] = 565;
//}
?>

 <?php
    if(isset($_SESSION['vid_success_copy']) && $_SESSION['vid_success_copy']!=""){
        ?>
         <div class="updated" style="padding: 0.3%;">
             <p>Video Copied Successfully.</p>
         </div>
        <?php
        unset($_SESSION['vid_success_copy']);
    }
  if (isset(self::$form_vars['video_id']) && intval(self::$form_vars['video_id'])){
    ?>
    <input onclick="window.location.href='admin.php?page=<?php echo esc_attr(self::$name) ?>&action=video_analytics&analytics_video=<?php echo intval(esc_attr(self::$form_vars['video_id'])); ?>'" type="button" style="float: right; margin-right: 1%;"  class="button-primary action" value="Video Analytics" /> &nbsp;
    <input type="button" onclick="sbmt_form()" style="float: right; margin-right: 1%;"  class="button-primary action" name="submit_button" value="Save" />
    <?php
    }
    
    ?>
    
    <input type="button" style="float: right; margin-right: 1%;" class="button-primary action" title="Close" value="Close" onclick="window.location.href='admin.php?page=<?php echo esc_attr(self::$name) ?>&action=videos'" />
    
	<form id="Video_Form" action="admin.php?page=<?php echo esc_attr(self::$name) ?>&action=video_save&video_id=<?php echo esc_attr($_REQUEST['video_id']) ?>" method="POST" enctype="multipart/video-data" >
     <?php wp_nonce_field( 'video_add_edit', 'video_save_nonce' ); ?>
	<input type="hidden" name="form_vars[video_id]" value="<?php echo intval(self::$form_vars['video_id']) ?>" />
	<h2><?php if ( isset(self::$form_vars['video_id']) && intval(self::$form_vars['video_id'])){ ?>Edit<?php } else { ?>New<?php } ?> Video</h2>
<?php

if((int)@self::$form_vars['width']==''){
    $video_width = "1080";
}else{
    $video_width = (int)@self::$form_vars['width'];
}
if((int)@self::$form_vars['height']==''){
    $video_height = "607";
}else{
    $video_height = (int)@self::$form_vars['height'];
}


$size_list = array(
	'0'		=> '16:9',
	'1'	=> '4:3',
	'2'		=> 'Custom'
);
$group = array();
$get_groups = $wpdb->get_results("select * from  ".Vpp_Base::$table['video_groups']." ",ARRAY_A);

if(count($get_groups)>0){
    $group = $get_groups;
}

Vpp_Form::fadeSave();
Vpp_Form::startTable();
Vpp_Form::listErrors(self::$form_errors);
Vpp_Form::textField('Name/Title', 'form_vars[name]', DBoutss(@self::$form_vars['name']));
?>
  <?php
             //print_r($form_vars['intros_outros_chk']); //die;
 			if(@self::$form_vars['intros_outros_chk']==1){
			    $check = "checked='checked'";
			    $value = '1' ;

			}else{
			    $check = "";
			    $value = '0';
			}
          ?>

         <tr class="auto_resp_a">
	    	<th>Intros & Outros <br /> (<em>Check if this is an intro or outro</em>):</th>
		    <td><input type="checkbox" size="13"  value="<?php echo $value  ?>" <?php echo  $check   ?> name="form_vars[intros_outros_chk]" id="intros_outros_chk" />
            </td>
		</tr>
<?php
Vpp_Form::textField('Categories', 'form_vars[tags]', DBoutss(@self::$form_vars['tags']));

$vid_source_array = array(0=>"YouTube",1=>"MP4",2=>"WebM",3=>"OGG",4=>"MOV",5=>"Vimeo");

?>
    <tr>
        <th>Video Source:</th>
        <td>
            <select name="form_vars[vid_source]">
                <?php
                    foreach($vid_source_array as $k=>$v){
                        if($k==@self::$form_vars['vid_source']){
                            echo "<option value='$k' selected='selected'>$v</option>";
                        }else{
                            echo "<option value='$k' >$v</option>";
                        }
                    }
                ?>
            </select>
        </td>
    </tr>
<?php
Vpp_Form::textField('Video URL', 'form_vars[vid_url]', @self::$form_vars['vid_url'], 75);
/*
Vpp_Form::textField('MP4 URL', 'form_vars[mp4_url]', @self::$form_vars['mp4_url'], 75);
Vpp_Form::textField('Youtube URL', 'form_vars[youtube_url]', @self::$form_vars['youtube_url'], 75);
Vpp_Form::textField('WebM URL', 'form_vars[webm_url]', @self::$form_vars['webm_url'], 75);
Vpp_Form::textField('OGG URL', 'form_vars[ogg_url]', @self::$form_vars['ogg_url'], 75);
Vpp_Form::textField('MOV URL', 'form_vars[mov_url]', @self::$form_vars['mov_url'], 75);
Vpp_Form::textField('Vimeo URL', 'form_vars[vimeo_url]', @self::$form_vars['vimeo_url'], 75);
*/
Vpp_Form::textField('Splash Image URL', 'form_vars[splash_url]', @sanitize_text_field(self::$form_vars['splash_url']), 75,"splash_urls");
Vpp_Form::vml_MediaBtn("Add Media");
Vpp_Form::textField('End Image URL', 'form_vars[end_url]', @sanitize_text_field(self::$form_vars['end_url']), 75,"end_url");
Vpp_Form::vml_MediaBtn_end("Add Media");
Vpp_Form::textField('Pause overlay image', 'form_vars[pause_overlay_image]', @sanitize_text_field(self::$form_vars['pause_overlay_image']), 75,"pause_overlay_image");
Vpp_Form::vml_MediaBtn_pause("Add Media");
Vpp_Form::selectField_nn('Group', 'form_vars[group_id]', @sanitize_text_field(self::$form_vars['group_id']), $group);

/* **************"vitvish"************* *///name="form_vars[tags]"
?>

<?php
		//print_r($_REQUEST);
  	//echo  ; die;
      // echo $_REQUEST['video_id'] ; die;
    $video_id = sanitize_text_field($_REQUEST['video_id']);
    $query = $wpdb->prepare('SELECT video_id, name FROM `'.$wpdb->prefix.'vpp_video` WHERE intros_outros_chk = 1 AND video_id !=%d ORDER BY  name',[$video_id]);
      //echo $query ; die;
	$video_listAllData = $wpdb->get_results($query);
    	$prechecked1 = '';
	$prechecked2 = 'checked';
    
    $postchecked1 = '';
	$postchecked2 = 'checked';
    if($_REQUEST['video_id']){
		 $query =$wpdb->prepare('SELECT pre_roll_video_chk, pre_select_value, post_select_value, post_roll_video_ck, video_id FROM `'.$wpdb->prefix.'vpp_video` WHERE video_id=%d',[$video_id]);
		$video_list = $wpdb->get_row($query);
		if($video_list->pre_roll_video_chk != 0 AND $video_list->pre_roll_video_chk > 0 ){
			$prechecked1 = 'checked';
			$prechecked2 = '';
			$query2 = $wpdb->prepare('SELECT video_id, name FROM '.$wpdb->prefix.'vpp_video WHERE video_id= %d', [$video_list->pre_select_value]);
			$pre_select_video = $wpdb->get_row($query2);
			//$query3 ='SELECT * FROM `wp_vpp_video` WHERE video_id !='.$video_list->pre_select_value ;
			$x = '('.$video_list->pre_select_value.','.sanitize_text_field($_REQUEST['video_id']).')';
			$query3 = $wpdb->prepare( 'SELECT * FROM `'.$wpdb->prefix.'vpp_video` WHERE  intros_outros_chk = 1 AND  `video_id` NOT IN %s ORDER BY  name' ,[$x]);
			  //echo $query3 ; die;
			$pre_select_video_list = $wpdb->get_results($query3);
		}
		
		if($video_list->post_roll_video_ck) {
			$postchecked1 = 'checked';
			$postchecked2 = '';
			$query2 = $wpdb->prepare('SELECT video_id, name FROM `'.$wpdb->prefix.'vpp_video` WHERE video_id = %s',[$video_list->post_select_value]);
			$post_select_video = $wpdb->get_row($query2);
			//$query3 ='SELECT * FROM `wp_vpp_video` WHERE video_id !='.$video_list->post_select_value ;
			$x = '('.$video_list->post_select_value.','.sanitize_text_field($_REQUEST['video_id']).')';
			$query3 =$wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'vpp_video` WHERE intros_outros_chk = 1 AND  `video_id` NOT IN %s ORDER BY  name',[$x]);
			   //echo $query3 ; die;
			$post_select_video_list = $wpdb->get_results($query3);
		}
	}else{
			$prechecked2 = 'checked';
			$postchecked2 = 'checked';
		}
	//echo print_r($post_select_video_list);
?>
<?php //print_r($video_list) ?>
<tr>
	<th>Enable Pre Roll Video:</th>
	<td> 
	 Yes <input type="radio" onclick="pre_roll1('show')" value="1"  <?php echo esc_attr($prechecked1)  ?> name="pre_roll_video_chk"  />
	  No <input type="radio" onclick="pre_roll1('hide')" value="0"  <?php echo esc_attr($prechecked2)  ?>  name="pre_roll_video_chk"  />
	</td>
</tr>
<tr>
	<th id="preLoadTxt"> </th>
	<td id="appendPreLoadVideo">
		<select id="selectpreloadvid" name="pre_select_value">
			<?php
			//print_r($video_list->pre_roll_video_chk) ;
				if(count($pre_select_video) > 0){
				    echo '<option value="'.$pre_select_video->video_id.'">'.$pre_select_video->name.'</option>';
					foreach($pre_select_video_list as $key =>$value){
						echo '<option value = "'.esc_attr($value->video_id).'">'.esc_attr($value->name).'</option>';
					}
				}else{
					   echo '<option value="">Select Video</option>';
					foreach($video_listAllData as $key =>$value){
						echo '<option value = "'.esc_attr($value->video_id).'">'.esc_attr($value->name).'</option>';
					}
				}
			?>
		</select>
	</td>
</tr>
<tr>
	<th>Enable Post Video:</th>
	<td> 
	 Yes <input type="radio" onclick="post_roll1('show')"  value="1" <?php echo $postchecked1  ?> name="post_roll_video_ck"  />
	  No <input type="radio" onclick="post_roll1('hide')" value="0"  <?php echo $postchecked2  ?>   name="post_roll_video_ck"  />
	</td>
</tr>
<tr>
	<th id="postLoadTxt"> </th>
	<td id="appendPostLoadVideo">
		<select id="selectpostloadvid" name="post_select_value">
			<?php
				if(count($post_select_video) > 0){
				    echo '<option value="'.$post_select_video->video_id.'">'.$post_select_video->name.'</option>';
					foreach($post_select_video_list as $key =>$value){
						echo '<option value = "'.esc_attr($value->video_id).'">'.esc_attr($value->name).'</option>';
					}
				}else{
					   echo '<option value="">Select Video</option>';
					foreach($video_listAllData as $key =>$value){
						echo '<option value = "'.esc_attr($value->video_id).'">'.esc_attr($value->name).'</option>';
					}
				}
			?>
		</select>
	</td>
</tr>

<?php
$preRoll = " jQuery(function(){";

    if($video_list->pre_roll_video_chk) {
        $preRoll .= "jQuery('#selectpreloadvid').show();  
        jQuery('#preLoadTxt').text('');
        jQuery('#preLoadTxt').append('Select Pre Roll Video');";
    } else { 

        $preRoll .= "jQuery('#preLoadTxt').text('');
		jQuery('#selectpreloadvid').hide();";
}
 if($video_list->post_roll_video_ck) { 
     $preRoll .= "jQuery('#postLoadTxt').text('');
     jQuery('#postLoadTxt').append('Select Post Roll Video');
        jQuery('#selectpostloadvid').show();";
 } else { 
    $preRoll .= "jQuery('#selectpostloadvid').hide();";
}
    $preRoll .= "});
    function pre_roll1(value){
        if(value == 'show'){
            jQuery('#selectpreloadvid').show();
            jQuery('#preLoadTxt').text('');
            jQuery('#preLoadTxt').append('Select Pre Roll Video');
        }else{
            jQuery('#selectpreloadvid').hide();
                    jQuery('#preLoadTxt').text('');
    
        }
    }
    
    function post_roll1(value){
        if(value == 'show'){
            jQuery('#postLoadTxt').text('');
            jQuery('#postLoadTxt').append('Select Post Roll Video');
            jQuery('#selectpostloadvid').show();
        }else{
            jQuery('#postLoadTxt').text('');
            jQuery('#selectpostloadvid').hide();
        }
    }
    "; 
    wp_add_inline_script('svms-scripts' , $preRoll);
?>

<?php
/* *************************** */
echo '<tr style="background-color: #EEE; cursor: pointer;" onclick="changeTab(1)"><td colspan="2">
<div style="width: 60%; height: auto; float: left; padding-top:1%;">
<h2 style="background-color: #EEE;">Player Appearance</h2>
</div>
<div style="width: 30%; height: auto; float: right; padding-top:1%;">
<h2 style="cursor: pointer; background-color: #EEE; padding-right: 1%; text-align: right; color: #FF9933; font-weight: bold;" id="tabclick_1" >Click to reveal</h2>
<input type="hidden" id="tab_1" />
</div>
</td></tr>';
?>
<tr id="remm_section_1" style="display: none;">
    <td colspan="2">
<?php
Vpp_Form::startTable();
Vpp_Form::selectField_Mange('Video Skin', 'form_vars[skin_type]', @self::$form_vars['skin_type'], $skins);
Vpp_Form::selectFields('Video Size Type', 'form_vars[size_type]', @self::$form_vars['size_type'], $size_list);
Vpp_Form::textField('Video Width', 'form_vars[width]', $video_width, 10,'width_v');
Vpp_Form::textField('Video Height', 'form_vars[height]', $video_height, 10,'height_v');
//$ratio_list = array(
//	'0.5625'	=> '16:9',
//	'0.75'		=> '4:3'
//);
//
//Vpp_Form::selectField('Aspect Ratio', 'form_vars[ratio]', @self::$form_vars['ratio'], $ratio_list);
$align_list = array(
	//''			=> 'None',
    'center'	=> 'Center',
	'left'		=> 'Left',
	'right'		=> 'Right'
);

Vpp_Form::selectField('Align', 'form_vars[align]', @self::$form_vars['align'], $align_list);
?>
<tr>
        <th>Video Download Button:</th>
        <td> Yes <input type="radio" onclick="chek_dbtn(this.value)" name="is_download" <?php if(@self::$form_vars['is_download']==1){ echo "checked='checked'"; } ?> value="1"  />
          No <input type="radio" onclick="chek_dbtn(this.value)" name="is_download" <?php if(@self::$form_vars['is_download']==0){ echo "checked='checked'"; } ?> value="0"  />
            
        </td>
</tr>
<?php
if(@self::$form_vars['is_download']==1){
    $btn_disp = "";
}else{
    $btn_disp = "display:none;";
}
if(@self::$form_vars['dwn_text_color']==""){
    $pl_back_1 = "#fff";
}else{
    $pl_back_1 = sanitize_text_field(self::$form_vars['dwn_text_color']);
}
if(@self::$form_vars['dwn_back_color']==""){
    $pl_back_2 = "#008EC2";
}else{
    $pl_back_2 = sanitize_text_field(self::$form_vars['dwn_back_color']);
}

    if(@self::$form_vars['pl_background']==""){
    $pl_back = "#000";
}else{
    $pl_back = sanitize_text_field(self::$form_vars['pl_background']);
}
?>

    <tr class="video_btns" style="<?php echo $btn_disp; ?>">
        <th>Download Button Text Color:</th>
        <td><input type="text" value="<?php echo $pl_back_1; ?>" id="custom_cl_1" name="dwn_text_color" /></td>
    </tr>
    <tr class="video_btns" style="<?php echo $btn_disp; ?>">
        <th>Download Button Color:</th>
        <td><input type="text" id="custom_cl_2" value="<?php echo esc_attr($pl_back_2); ?>" name="dwn_back_color" /></td>
    </tr>
    
 
     <tr>
        <th>Player Background:</th>
        <td><input type="text" id="custom_cl" value="<?php echo esc_attr($pl_back); ?>" name="pl_background" /></td>
     </tr>
<?php

Vpp_Form::endTable();
?>
</td>
</tr>
<?php

echo '<tr style="background-color: #EEE; cursor: pointer;" onclick="changeTab(2)"><td colspan="2">
<div style="width: 60%; height: auto; float: left; padding-top:1%;">
<h2 style="background-color: #EEE;">Player Settings</h2>
</div>
<div style="width: 30%; height: auto; float: right; padding-top:1%;">
<h2 style="cursor: pointer; background-color: #EEE; padding-right: 1%; text-align: right; color: #FF9933; font-weight: bold;" id="tabclick_2" >Click to reveal</h2>
<input type="hidden" id="tab_2" />
</div>
</td></tr>';
?>
<tr id="remm_section_2" style="display: none;">
    <td colspan="2">
<?php

Vpp_Form::startTable();
Vpp_Form::checkboxField('Auto Play', 'form_vars[auto_play]', sanitize_text_field(self::$form_vars['auto_play']));
Vpp_Form::checkboxField('Hide Controls', 'form_vars[hide_controls]', sanitize_text_field(self::$form_vars['hide_controls']));
Vpp_Form::checkboxField('Loop Video', 'form_vars[loop_video]', sanitize_text_field(self::$form_vars['loop_video']));

if(@self::$form_vars['custom_play']==1){
    $cs_area = "";
    $cs_checked = "checked='checked'";
}else{
    $cs_area = "display:none";
    $cs_checked = "";
}
?>
<tr>
        <th>Page Scroll Options:</th>
        <td> <input type="radio" name="scroll_options" <?php if(@self::$form_vars['scroll_options']==0){ echo "checked='checked'"; } ?> value="0"  /> The video continues to play <br />
        <input type="radio" name="scroll_options" value="1" <?php if(@self::$form_vars['scroll_options']==1){ echo "checked='checked'"; } ?>  /> The video stops on scroll down <br />
        <!-- <input type="radio" name="scroll_options" value="2" <?php if(@self::$form_vars['scroll_options']==2){ echo "checked='checked'"; } ?>  /> The video floats down the page -->
        </td>
    </tr>
<tr>
            <th>Custom Video Start Time:</th>
            <td><input type="checkbox" value="1" <?php echo esc_attr($cs_checked); ?> name="custom_play" class="custom_video" onchange="check_custom()" /></td>
</tr>
<tr class="custom_settings" style="<?php echo $cs_area; ?>">
    <th>Custom Times:</th>
    <td>
        <b>Video Start Time</b>
        <input type="number" name="video_start_time" id="video_start_time" onblur="validate_customTIme('video_start_time','video_end_time',0)" value="<?php echo @esc_attr(sanitize_text_field(self::$form_vars['video_start_time'])); ?>" size="10"  />
        <b>Video End Time</b>
        <input type="number" name="video_end_time" id="video_end_time" onblur="validate_customTIme('video_end_time','video_start_time',1)" value="<?php echo @esc_attr(sanitize_text_field(self::$form_vars['video_end_time'])); ?>" size="10" />
        <br />
        <span>Enter Time in Seconds like (5,10,60,120...)</span>
    </td>
</tr>
<?php
Vpp_Form::checkbox_static('Show Captions', 'form_vars[static_html]', @self::$form_vars['static_html'],"check_static");
if(@self::$form_vars['static_html'] == 1){
    $d = 0;
}else{
    $d = 1;  
}
$vdd = 0;
Vpp_Form::textareaField_editor('Create Captions', 'form_vars[static_html_code]', @sanitize_text_field(self::$form_vars['static_html_code']), 10, 100, 'This HTML may contain Wordpress shortcodes, but they will only work when use on this Wordpress website.',$d,"hide_html","static_html_code");
Vpp_Form::endTable();
?>
</td>
</tr>

<?php
    Vpp_Form::endTable();
?>
		<div style="padding: 10px 15px;">   
			<input type="submit" class="button-primary action" name="submit_button" value="Save" />
			<input type="submit" class="button-primary action" name="submit_button" value="Save &amp; Add Another" />
			<?php if (isset(self::$form_vars['video_id']) && intval(self::$form_vars['video_id'])){ ?>
			<input type="button" class="button-primary action" value="Delete" onclick="deleteGroup('admin.php?page=<?php echo self::$name ?>&action=video_delete&video_id=<?php echo esc_attr(intval(self::$form_vars['video_id'])) ?>');" />
            <?php } ?>    
			<input type="button" class="button-primary action" title="Close" value="Close" onclick="window.location.href='admin.php?page=<?php echo esc_attr(self::$name) ?>&action=videos'" />
		</div>
	</video>
</form>

<?php
$saveGr = "function saveGr() {
    var group_name = jQuery(input[name='group_name']).val();
    jQuery.post('". esc_url(admin_url()) ."admin-ajax.php?action=saveGroup_svms',name=+group_name,function(data){
        jQuery('.group_class').append('<option value='+data+' selected=selected>'+group_name+'</option>');
        jQuery('input[name=group_name]').val();
        jQuery('#group_name').hide();
    });
}";
wp_add_inline_script('svms-scripts', $saveGr);
$customCl = "jQuery(document).ready(function(){
    
    jQuery('#custom_cl').spectrum({
        preferredFormat: 'hex',
        color: '". $pl_back ."'
    });
     jQuery('#custom_cl_1').spectrum({
        preferredFormat: 'hex',
        color: '".$pl_back_1 ."'
    });
     jQuery('#custom_cl_2').spectrum({
        preferredFormat: 'hex',
        color: '" . $pl_back_2. "'
    });
    jQuery('#custom_layer').spectrum({
        preferredFormat: 'hex',
        color: '" . $layer_txt_color ."'
    });

});";

$copyVideo = "function CopyVideo(id){
    jQuery.post('". admin_url() . "admin-ajax.php?action=svm_copy_video',video_id=+id,function(data){
        if(data==0){
            alert('An error occured. Please Try Again!');
        }else{
            window.location = '".admin_url()."admin.php?page=s3_video_player_plus&action=video_edit&video_id='+data;
        }
    });
}";

wp_add_inline_script('svms-scripts', $customCl);
wp_add_inline_script('svms-scripts', $copyVideo);

$extra = '';
if (@self::$form_vars['video_id'])
{

$idr = "var iframe = document.getElementById('myIframe');"; 
$opt_ifr = "iframe.style.height=iframe.contentWindow.document.body.scrollHeight+'px';";

if(@self::$form_vars['is_download']==1){
    $video_height = 30+$video_height;
}
$iframe_video_width = '100%';
if(self::$form_vars['size_type'] == '2') {
	$iframe_video_width = self::$form_vars['width'] . 'px; max-width: 100% !important';
}


$vhh = $video_height;
$video_height  = "##";
$video_height1 = "###";
?>


<h3>Wordpress Shortcode</h3>
<input type="text" size="100" value="<?php echo htmlspecialchars('[s3vpp id='.self::$form_vars['handle'].']') ?>" />
<?php

?>
<br />
<?php
    $videoHeight = "jQuery(document).ready(function(){
        var video_height = '". $vhh . "';
        var html_sw = jQuery('#html_sw').height();
        video_height = Number(video_height)+Number(html_sw);

        if(html_sw!=0){
            video_height = Number(video_height)+Number(60);
        }
    });";
    wp_add_inline_script('svms-scripts', $videoHeight);

    if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
        $htp = "on";
    else
        $htp = "off";

   if($htp=="off"){
    ?>
        <div style="margin-top: 1%;padding: 3px; box-sizing: border-box;" class="error"><p style="f-ont-weight: bold;">On SSL enabled sites (especially like Leadpages, ClickFunnels or other sites), the Iframe/Raw code https:// enabled is required.</p></div>
    <?php
   }
?>

<?php } ?>
<h3>Pages/Posts Used on...</h3>
<ul>
<?php
if(isset(self::$form_vars['video_id'])){


$sql = $wpdb->prepare('
SELECT
	p.*
FROM
	'.self::$table['video_location'].' vl,
	'.$wpdb->base_prefix.'posts p
WHERE
	vl.post_id = p.ID
AND
	vl.video_id > 0
AND
	vl.video_id = %d
ORDER BY
	p.post_title
',[(int)self::$form_vars['video_id']]);

$post_list = $wpdb->get_results($sql, ARRAY_A);
foreach ($post_list as $post)
{
	echo '<li>- <a href="'.esc_attr($post['guid']).'" target="_blank">'.esc_attr($post['post_title']).'&nbsp;&nbsp;</a> &nbsp; &nbsp; <a href="'.admin_url().'post.php?post='.esc_attr($post['ID']).'&action=edit" target="_blank" title="Edit Page/Post"><img style="width: 18px;" src="'.esc_url(VPP_PLUGIN_URL).'includes/images/icon-pencil.png" /></a></li>';
}
}
?>

</ul>
<div id="html_sw"  style="display: none;">
<?php
echo DBoutss(@self::$form_vars['show_html']);
?>
</div>
