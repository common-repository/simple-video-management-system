<?php
if ( 
    ! isset( $_POST['video_save_nonce'] ) 
    || ! wp_verify_nonce( $_POST['video_save_nonce'], 'video_add_edit' ) 
) {
 
   print 'Sorry, your nonce did not verify.';
   exit;

} else if(!current_user_can('administrator')){
    print 'Sorry, this user does not have admin privileges';
    exit;
 
} else {

 
    $total = $wpdb->get_var('SELECT COUNT(*) AS total FROM '.self::$table['video']);
    function DBin($string){
    	$a = html_entity_decode($string);   
    	return trim(htmlspecialchars($a,ENT_QUOTES));
    }
    function is_url_valid($uri){
        if(preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$uri)){
          return $uri;
        }
        else{
            return false;
        }
    }
 
    	$form_vars['video_id']			= intval(@self::$form_vars['video_id']);
    	$form_vars['name']				= DBin(trim(@self::$form_vars['name']));
        $form_vars['tags']              = DBin(trim(@self::$form_vars['tags']));
        $form_vars['vid_source']	    = intval(@self::$form_vars['vid_source']);
        $form_vars['vid_url']			= trim(@self::$form_vars['vid_url']);
    	$form_vars['splash_url']		= trim(@self::$form_vars['splash_url']);
        $form_vars['end_url']		= trim(@self::$form_vars['end_url']);    
    	$form_vars['layer_img_url']		= trim(@self::$form_vars['layer_img_url']);	
    	$form_vars['width']				= intval(@self::$form_vars['width']);
    	$form_vars['height']			= intval(@self::$form_vars['height']);
    	$form_vars['align']				= trim(@self::$form_vars['align']);
    	$form_vars['auto_play']			= intval(@self::$form_vars['auto_play']);
    	$form_vars['hide_controls']		= intval(@self::$form_vars['hide_controls']);
    	$form_vars['loop_video']		= intval(@self::$form_vars['loop_video']);
    	$form_vars['show_html_seconds']	= intval(@self::$form_vars['show_html_seconds']);
    	$form_vars['show_html']			= DBin(sanitize_text_field($_REQUEST['show_html']));
    	$form_vars['redirect_url']		= trim(@self::$form_vars['redirect_url']);
        $form_vars['size_type']         = trim(@self::$form_vars['size_type']);
        $form_vars['lightbox_enabled']  = trim(@self::$form_vars['lightbox_enabled']);
        $form_vars['static_html']       = trim(@self::$form_vars['static_html']);
        $form_vars['thumbnail_url']     = trim(@self::$form_vars['thumbnail_url']);
        $form_vars['use_splash']        = trim(@self::$form_vars['use_splash']);
        $form_vars['skin_type']         = trim(@self::$form_vars['skin_type']);
        $form_vars['static_html_code']  = trim(@sanitize_text_field($_REQUEST['static_html_code']));
        $form_vars['html_position']     = trim(@sanitize_text_field($_REQUEST['html_position']));
        $form_vars['optin_gate']        = intval(@self::$form_vars['optin_gate']);
        $form_vars['tagging_enable']        = intval(@self::$form_vars['tagging_enable']);
        $form_vars['optin_start']       = intval(@sanitize_text_field($_REQUEST['optin_start']));
        $form_vars['optin_start_time']  = trim(@sanitize_text_field($_REQUEST['optin_start_time']));
        $form_vars['optin_gate_code']	= DBin(trim(@sanitize_text_field($_REQUEST['optin_gate_code'])));
        $form_vars['pl_background']     = trim(@sanitize_text_field($_REQUEST['pl_background']));
        $form_vars['group_name']        = trim(@sanitize_text_field($_REQUEST['group_name']));
        $form_vars['group_id']          = intval(@self::$form_vars['group_id']);
        
        $form_vars['scroll_options']          = intval(@sanitize_text_field($_REQUEST['scroll_options']));
        $form_vars['is_download'] = @sanitize_text_field($_REQUEST['is_download']);
        $form_vars['dwn_text_color'] = @sanitize_text_field($_REQUEST['dwn_text_color']);
        $form_vars['dwn_back_color'] = @sanitize_text_field($_REQUEST['dwn_back_color']);
        if(!isset($_REQUEST['enable_layer'])){
            $_REQUEST['enable_layer'] = 0;
        }
        $form_vars['enable_layer'] = sanitize_text_field($_REQUEST['enable_layer']);
        $form_vars['layer_content'] = DBin(sanitize_text_field($_REQUEST['layer_content']));
        $form_vars['layer_txt_color'] = @sanitize_text_field($_REQUEST['layer_txt_color']);
        $form_vars['layer_txt_size'] = @sanitize_text_field($_REQUEST['layer_txt_size']);
        $form_vars['layer_font'] = @sanitize_text_field($_REQUEST['layer_font']);
        $form_vars['crm_id']   = intval(@self::$form_vars['crm_id']);
    	
    	 $form_vars['pre_roll_video_chk'] = sanitize_text_field($_REQUEST['pre_roll_video_chk']);
    	 $form_vars['pre_select_value'] = sanitize_text_field($_REQUEST['pre_select_value']);
    	 $form_vars['post_roll_video_ck'] = sanitize_text_field($_REQUEST['post_roll_video_ck']);
    	 $form_vars['post_select_value'] = sanitize_text_field($_REQUEST['post_select_value']);
    
        $form_vars['bg_color_ck']        = trim(@self::$form_vars['bg_color_ck']);
        $form_vars['intros_outros_chk']        = trim(@self::$form_vars['intros_outros_chk']);
        $form_vars['pause_overlay_image']       = trim(@self::$form_vars['pause_overlay_image']); 
        
            if(empty($form_vars['bg_color_ck'])){
                $form_vars['bg_color_ck'] = 0 ;
            }
    
            if(empty($form_vars['intros_outros_chk'])){
                $form_vars['intros_outros_chk'] = 0 ;
            }
    
    	    if(empty($form_vars['pre_select_value'])){
    			$form_vars['pre_roll_video_chk'] = 0 ;
    			$form_vars['pre_select_value'] = 0 ;
    		}
    		if(empty($form_vars['post_select_value'])){
    			$form_vars['post_roll_video_ck'] = 0 ;
    			$form_vars['post_select_value'] = 0 ;
    		}

        
        if($form_vars['group_name']!=""){

            $wpdb->query(
                $wpdb->prepare("insert into ".Vpp_Base::$table['video_groups']." (name) values(%s)","$form_vars[group_name]")  
            );
            $form_vars['group_id'] = $wpdb->insert_id;
        }
        
        if(!isset($_POST['custom_play'])){
            $form_vars['custom_play']   = 0;
        }else{
            $form_vars['custom_play']   = 1;
        }
        
        if(!isset($_POST['is_wait'])){
            $form_vars['is_wait']   = 0;
        }else{
            $form_vars['is_wait']   = 1;
        }
        
        if($form_vars['tagging_enable']!=1){
            $form_vars['tagging_enable'] = 0;
        }
      
        $form_vars['video_start_time']  = sanitize_text_field($_POST['video_start_time']);
        $form_vars['video_end_time']    = sanitize_text_field($_POST['video_end_time']);
    	if (!$form_vars['name'])
    	{
    		self::$form_errors['name'] = 'Name is required';
    	}

        if(!$form_vars['vid_url']){
            self::$form_errors['mp4_url'] = 'A video URL is required (MP4, Youtube, WebM, OGG, Vimeo or MOV )';
        }
        if(!is_url_valid($form_vars['vid_url'])){
            self::$form_errors['vid_url'] = 'Video URL Is Not Valid. Please Enter The Correct URL With HTTP or HTTPS';
        }
    	if (!$form_vars['width'])
    	{
    		$form_vars['width'] = 1080;
    	}
    	if (!$form_vars['height'])
    	{
    		$form_vars['height'] = 720;
    	}
    	if (!self::$form_errors)
    	{
    		if ($form_vars['video_id'])
    		{
     
    			$form_vars['modified'] = time();
                
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set name=%s, vid_source=%d, vid_url=%s, splash_url=%s,
                        layer_img_url=%s,end_url=%s,size_type=%d,width=%d,height=%d,ratio=%f,align=%s,auto_play=%d,
                        hide_controls=%d,loop_video=%d,show_html_seconds=%d,show_html=%s,redirect_url=%s,modified=%d,
                        lightbox_enabled=%d,thumbnail_url=%s,static_html=%d,use_splash=%d,static_html_code=%s,html_position=%d,
                        skin_type=%d,custom_play=%d,video_start_time=%d,video_end_time=%d,optin_gate=%d,optin_start_time=%s,
                        optin_gate_code=%s,pl_background=%s,group_id=%d,tagging_enable=%d,optin_start=%d,is_wait=%d,
                        scroll_options=%d,is_download=%d,dwn_text_color=%s,dwn_back_color=%s,tags=%s,crm_id=%d,
                        enable_layer=%d,layer_content=%s,layer_txt_color=%s,layer_txt_size=%s,layer_font=%s,pre_roll_video_chk=%s,
                        pre_select_value=%s,post_roll_video_ck=%s,post_select_value=%s,bg_color_ck=%s,intros_outros_chk=%s,pause_overlay_image=%s where video_id=%d",
                        
                        $form_vars["name"],$form_vars["vid_source"],$form_vars["vid_url"],$form_vars["splash_url"],$form_vars["layer_img_url"],
                        $form_vars["end_url"],$form_vars["size_type"],$form_vars["width"],$form_vars["height"],$form_vars["ratio"],
                        $form_vars["align"],$form_vars["auto_play"],$form_vars["hide_controls"],$form_vars["loop_video"],$form_vars["show_html_seconds"],
                        $form_vars["show_html"],$form_vars["redirect_url"],$form_vars["modified"],$form_vars["lightbox_enabled"],
                        $form_vars["thumbnail_url"],$form_vars["static_html"],$form_vars["use_splash"],$form_vars["static_html_code"],$form_vars["html_position"],
                        $form_vars["skin_type"],$form_vars["custom_play"],$form_vars["video_start_time"],$form_vars["video_end_time"],$form_vars["optin_gate"],
                        $form_vars["optin_start_time"],$form_vars["optin_gate_code"],$form_vars["pl_background"],$form_vars["group_id"],$form_vars["tagging_enable"],$form_vars["optin_start"],
                        $form_vars["is_wait"],$form_vars["scroll_options"],$form_vars["is_download"],$form_vars["dwn_text_color"],$form_vars["dwn_back_color"],$form_vars["tags"],$form_vars["crm_id"],
                        $form_vars["enable_layer"],$form_vars["layer_content"],$form_vars["layer_txt_color"],$form_vars["layer_txt_size"],$form_vars["layer_font"],$form_vars["pre_roll_video_chk"],
                         $form_vars["pre_select_value"],$form_vars["post_roll_video_ck"],$form_vars["post_select_value"],$form_vars["bg_color_ck"],$form_vars["intros_outros_chk"],$form_vars["pause_overlay_image"],$form_vars["video_id"]                                       
                                        
                    )
                );
    
    		}
    		else
    		{
    			 
    			$created_time = time();
    			$form_vars['handle'] = self::getUniqueHandle(serialize($form_vars));
                $columns = "`name`, `handle`,`vid_source`,`vid_url`,`splash_url`,`end_url`,`size_type`, `width`, `height`, `ratio`, `align`, `auto_play`, `hide_controls`, `loop_video`, `show_html_seconds`, 
                                            `show_html`, `redirect_url`, `created`, `modified`,`lightbox_enabled`,`thumbnail_url`,`static_html`,`use_splash`,`static_html_code`,`html_position`,`skin_type`,`custom_play`,`video_start_time`,`video_end_time`,
                                                                                    `optin_gate`,`optin_start_time`,`optin_gate_code`,`pl_background`,`group_id`,`tagging_enable`,`optin_start`,`is_wait`,`scroll_options`,`is_download`,`dwn_text_color`,`dwn_back_color`,
                                                                                                                                        `tags`,`crm_id`,`enable_layer`,`layer_content`,`layer_txt_color`,`layer_txt_size`,`layer_font`,`layer_img_url`,`bg_color_ck`,`pre_roll_video_chk` ,`pre_select_value`,`post_roll_video_ck`,`post_select_value`,
                                                                                                                                                                                        `intros_outros_chk`,`pause_overlay_image`";   // 
                $insrt_qry = "insert into ".self::$table['video']." ($columns) values(%s,%s,%d,%s,%s,%s,%d,%d,%d,%f,%s,%d,%d,%d,%d,%s,%s,%d,%d,%d,%s,%d,%d,%s,%d,%d,%d,%d,%d,%d,%s,%s,%s,%d,%d,%d,%d,%d,%d,%s,%s,%d,%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)";
                  
                $wp_prepare = $wpdb->prepare($insrt_qry,$form_vars["name"],$form_vars["handle"],
                $form_vars["vid_source"],$form_vars["vid_url"],
                $form_vars["splash_url"],$form_vars["end_url"],$form_vars["size_type"],$form_vars["width"],
                $form_vars["height"],$form_vars["ratio"],$form_vars["align"],$form_vars["auto_play"],$form_vars["hide_controls"],$form_vars["loop_video"],$form_vars["show_html_seconds"],$form_vars["show_html"],$form_vars["redirect_url"],$created_time,
    			$form_vars["modified"],$form_vars["lightbox_enabled"],$form_vars["thumbnail_url"],
    			$form_vars["static_html"],$form_vars["use_splash"],$form_vars["static_html_code"],
    			$form_vars["html_position"],$form_vars["skin_type"],$form_vars["custom_play"],$form_vars["video_start_time"],
    			$form_vars["video_end_time"],$form_vars["optin_gate"],$form_vars["optin_start_time"],$form_vars["optin_gate_code"],
    			$form_vars["pl_background"],$form_vars["group_id"],$form_vars["tagging_enable"],$form_vars["optin_start"],
    			$form_vars["is_wait"],$form_vars["scroll_options"],$form_vars["is_download"],$form_vars["dwn_text_color"],
    			$form_vars["dwn_back_color"],$form_vars["tags"],$form_vars["crm_id"],$form_vars["enable_layer"],
    			$form_vars["layer_content"],$form_vars["layer_txt_color"],$form_vars["layer_txt_size"],$form_vars["layer_font"],
    			$form_vars["layer_img_url"], 
    			$form_vars["bg_color_ck"] ,$form_vars["pre_roll_video_chk"],$form_vars["pre_select_value"],$form_vars["post_roll_video_ck"],$form_vars["post_select_value"],$form_vars["intros_outros_chk"],$form_vars["pause_overlay_image"]);
                
 
                $wpdb->query($wp_prepare);
                 
                
    			$form_vars['video_id'] = $wpdb->insert_id;
    
    		}
            
            if(isset(self::$form_vars['tag_id'])){ // inufions soft Tag ID
                $form_vars['tag_id'] =  self::$form_vars['tag_id'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set tag_id=%d where video_id=%d",
                        $form_vars["tag_id"],$form_vars["video_id"]    
                    )
                );
                
            }
            if(isset(self::$form_vars['aw_list_id'])){ // Aweber List ID
                $form_vars['aw_list_id'] =  self::$form_vars['aw_list_id'];
                $wpdb->query(
                    $wpdb->prepare(
                    "update ".self::$table['video']." set aw_list_id=%d where video_id=%d",
                    $form_vars["aw_list_id"],$form_vars["video_id"]
                    )
                );
            }
            if(isset(self::$form_vars['getResponse_id'])){ // GetResponse Campaign Save
                $form_vars['getResponse_id'] = self::$form_vars['getResponse_id'];
                $wpdb->query(
                    $wpdb->prepare(
                    "update ".self::$table['video']." set getResponse_id=%s where video_id=%d",
                    $form_vars["getResponse_id"],$form_vars["video_id"]
                    )
                );
            }
            
            if(isset(self::$form_vars['mailchimp_listid'])){ // MailChimp List Save
                $form_vars['mailchimp_listid'] = self::$form_vars['mailchimp_listid'];
                $wpdb->query(
                    $wpdb->prepare(
                    "update ".self::$table['video']." set mailchimp_listid=%s where video_id=%d",
                    $form_vars["mailchimp_listid"],$form_vars["video_id"]
                    )
                );
            }
            
            if(isset(self::$form_vars['activecamp_id'])){ // ActiveCampaign List Save
                $form_vars['activecamp_id'] = self::$form_vars['activecamp_id'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set activecamp_id=%d where video_id=%d",
                        $form_vars["activecamp_id"],$form_vars["video_id"]
                    )
                );
            }
            
            if(isset(self::$form_vars['convertkit_tagid'])){ // Convertkit Tag Save
                $form_vars['convertkit_tagid'] = self::$form_vars['convertkit_tagid'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set convertkit_tagid=%d where video_id=%d",
                        $form_vars["convertkit_tagid"],$form_vars["video_id"]
                    )
                );
            }
            if(isset(self::$form_vars['markethero_tag'])){ // MarketHero Tag Save
                $form_vars['markethero_tag'] = self::$form_vars['markethero_tag'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set markethero_tag=%s where video_id=%d",
                        $form_vars["markethero_tag"],$form_vars["video_id"]
                    )
                );
            }
            
            if(isset(self::$form_vars['drip_tag'])){ // MarketHero Tag Save
                $form_vars['drip_tag'] = self::$form_vars['drip_tag'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set drip_tag=%s where video_id=%d",
                        $form_vars["drip_tag"],$form_vars["video_id"]
                    )
                );
            }
            
            if(isset(self::$form_vars['sendlane_tagid'])){ // Sandlane Tag Save
                $form_vars['sendlane_tagid'] = self::$form_vars['sendlane_tagid'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set sendlane_tagid=%d where video_id=%d",
                        $form_vars["sendlane_tagid"],$form_vars["video_id"]
                    )
                );
            }
            
            if(isset(self::$form_vars['icontact_listid'])){ // iContact List Save
                $form_vars['icontact_listid'] = self::$form_vars['icontact_listid'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set icontact_listid=%d where video_id=%d",
                        $form_vars["icontact_listid"],$form_vars["video_id"]
                    )  
                );
            }
            if(isset(self::$form_vars['ontraport_tag'])){ // Ontraport List Save
                $form_vars['ontraport_tag'] = self::$form_vars['ontraport_tag'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set ontraport_tag=%s where video_id=%d",
                        $form_vars["ontraport_tag"],$form_vars["video_id"]
                    )
                );
            }
            
            if(isset(self::$form_vars['c_contact_id'])){ // Constant Contact List Save
                $form_vars['c_contact_id'] = self::$form_vars['c_contact_id'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set c_contact_id=%s where video_id=%d",
                        $form_vars["c_contact_id"],$form_vars["video_id"]
                    )
                );
            }
            
            if(isset(self::$form_vars['sendy_listid'])){ // Sendy List ID
                $form_vars['sendy_listid'] = self::$form_vars['sendy_listid'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set sendy_listid=%d where video_id=%d",
                        $form_vars["sendy_listid"],$form_vars["video_id"]
                    )
                );
            }
            
            if(isset(self::$form_vars['arpreach_list'])){ // Sendy List ID
                $form_vars['arpreach_list'] = self::$form_vars['arpreach_list'];
                $wpdb->query(
                    $wpdb->prepare(
                        "update ".self::$table['video']." set arpreach_list=%s where video_id=%d",
                        $form_vars["arpreach_list"],$form_vars["video_id"]
                    )
                );
            }
            
    		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    		{
    			exit(json_encode(array('error' => false, 'handle' => $form_vars['handle'])));
    		}
    		else
    		{
    			if (@$_REQUEST['submit_button'] == 'Save & Add Another')
    			{
    				header('location: admin.php?page=' . self::$name . '&action=video_edit');
    			}
    			else
    			{
    				header('location: admin.php?page=' . self::$name . '&action=video_edit&video_id='.$form_vars['video_id']);
    			}
    			exit();
    		}
    	}
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    {
    	exit(json_encode(array('error' => true, 'error_messages' => self::$form_errors)));
    }

self::$action = 'video_edit';
}
