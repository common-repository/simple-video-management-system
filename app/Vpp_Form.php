<?php
class Vpp_Form
{
	public static function textField($label, $name, $value, $size = 50,$k="", $caption = '', $required = FALSE)
	{
		if (!intval($size)){ $size = 50; }
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		echo '<input type="text" name="'.$name.'" value="'.htmlspecialchars($value).'" id="'.$k.'" size="'.$size.'" onchange="vpp_form_changed = true;" />';
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
    public static function textField_setting($label, $name, $value, $size = 25,$k="", $caption = '', $required = FALSE)
	{
		if (!intval($size)){ $size = 50; }
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		echo '<input type="text" name="'.$name.'" value="'.htmlspecialchars($value).'" id="'.$k.'" size="'.$size.'" onchange="vpp_form_changed = true;" /> <input type="submit" class="button-primary action" value="Verify" />';
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
    public static function textField_hiderow($label, $name, $value, $size = 50,$k="", $display,$caption = '', $required = FALSE)
	{
		if (!intval($size)){ $size = 50; }
        if($display==1){
            $st = "display:none;"; 
        }else{
            $st = "";
        }
		echo '<tr style="'.$st.'" class="hide_html"><th>'.esc_attr($label).':</th><td>';
		echo '<input type="text" name="'.$name.'" value="'.htmlspecialchars($value).'" id="'.$k.'" size="'.$size.'" onchange="vpp_form_changed = true;" />';
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
	public static function hiddenField($name, $value)
	{
		echo '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars($value).'" />';
	}
	public static function checkboxField($label, $name, $value, $caption = '', $required = FALSE)
	{
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		$checked = '';
		if (intval($value))
		{
			$checked = ' checked';
		}
		echo '<input type="checkbox" name="'.$name.'" value="1"'.$checked.' onchange="vpp_form_changed = true;" />';
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
  public static function checkbox_in($label, $name, $value, $caption = '', $required = FALSE)
	{
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		$checked = '';
		if (intval($value))
		{
			$checked = ' checked';
		}
		echo '<input type="checkbox" name="'.$name.'" value="1"'.$checked.' class="is_lighbox" onchange="checkenabled()" />';
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
    public static function checkbox_static($label, $name, $value, $fun,$caption = '', $required = FALSE)
	{
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		$checked = '';
		if (intval($value))
		{
			$checked = ' checked';
		}
        $func = "$fun()";
		echo '<input type="checkbox" name="'.$name.'" value="1"'.$checked.' class="'.$fun.'" onchange="'.$func.'" />';
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
	public static function textareaField_editor($label, $name, $value, $rows = 3, $cols = 40, $caption = '',$display,$cls,$editor_name, $max_length = NULL, $required = FALSE)
	{
		if (!intval($rows)){ $row = 3; }
		if (!intval($cols)){ $cols = 40; }
		if (intval($max_length)){ $max_length = ' maxlength="'.$max_length.'"'; }
        if($display==1){
            $st = "display:none;"; 
        }else{
            $st = "";
        }
		echo '<tr style="'.$st.'" class="'.$cls.'"><th>'.esc_attr($label).':</th><td>';
        $string = stripslashes(trim($value));
    	$content =  str_replace("&#039;","'",html_entity_decode($string,ENT_QUOTES,'UTF-8'));
        $settings = array("textarea_rows"=>$rows);
        $editor_id = 'mycustomeditor'; 
        wp_editor( $content, $editor_name,$settings );
		if ((int)$required)
		{
			echo '<div class="required">Required</div>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
    public static function textareaField_editor1($label, $name, $value, $rows = 3, $cols = 40, $caption = '',$display,$cls, $max_length = NULL, $required = FALSE)
	{
		if (!intval($rows)){ $row = 3; }
		if (!intval($cols)){ $cols = 40; }
		if (intval($max_length)){ $max_length = ' maxlength="'.$max_length.'"'; }
        if($display==1){
            $st = "display:none;"; 
        }else{
            $st = "";
        }
		echo '<tr style="'.$st.'" class="'.$cls.'"><th>'.esc_attr($label).':</th><td>';
        $string = stripslashes(trim($value));
    	$content =  str_replace("&#039;","'",html_entity_decode($string,ENT_QUOTES,'UTF-8'));
        $settings = array("textarea_rows"=>$rows);
        $editor_id = 'mycustomeditor_html'; 
        wp_editor( $content, 'show_html',$settings );
		if ((int)$required)
		{
			echo '<div class="required">Required</div>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
    public static function textareaField($label, $name, $value, $rows = 3, $cols = 40, $caption = '', $max_length = NULL, $required = FALSE)
	{
		if (!intval($rows)){ $row = 3; }
		if (!intval($cols)){ $cols = 40; }
		if (intval($max_length)){ $max_length = ' maxlength="'.$max_length.'"'; }
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		echo '<textarea name="'.$name.'" rows="'.$rows.'" cols="'.$cols.'"'.$max_length.' onchange="vpp_form_changed = true;">'.htmlspecialchars($value).'</textarea>';
		if ((int)$required)
		{
			echo '<div class="required">Required</div>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
	public static function selectField($label, $name, $value, $list, $caption = '', $required = FALSE)
	{
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		echo '<select name="'.$name.'" onchange="vpp_form_changed = true;">';
		if (is_array($list))
		{
			foreach ($list as $key => $opt_value)
			{
				$selected = '';
				if (trim($key) == trim($value))
				{
					$selected = ' selected';
				}
				echo '<option value="'.$key.'"'.$selected.'>'.$opt_value.'</option>';
			}
		}
		echo '</select>';
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
    public static function selectField_Mange($label, $name, $value, $list, $caption = '', $required = FALSE)
	{
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		echo '<select name="'.$name.'" onchange="svmsskin(this.value)">';
		if (is_array($list))
		{
			foreach ($list as $key => $opt_value)
			{
				$selected = '';
				if (trim($key) == trim($value))
				{
					$selected = ' selected';
				}
				echo '<option value="'.$key.'"'.$selected.'>'.$opt_value.'</option>';
			}
            @do_action("svms_action",$value);
		}
		echo '</select>';
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
	
	public static function selectField_preloader()
	{
		//$video_list = $wpdb->get_results('SELECT * FROM `wp_vpp_video`');
		$stringData =  '<select><option value="">Select Video</option>';
		$stringData   .= '<option value = "">selct </option>';
		$stringData   .='</select>';
		echo $stringData ; 
		/* $video_list = $wpdb->get_results('SELECT * FROM `wp_vpp_video`');
		$stringData = '<select name="preLoadVideoSelct" class="group_class" >';
		$stringData   .= '<option value = "">selct Video</option>';
		$stringData   .= '</select>';
		echo $stringData ; */
		
		/* $stringData =  '<select name="'.$name.'" id="'.$name.'" class="group_class"><option value="0">None</option>';
		if (is_array($list))
		{
			foreach ($list as $key => $opt_value)
			{
				$selected = '';
				if (trim($opt_value['id']) == trim($value))
				{
					$selected = ' selected';
				}
                $string = stripslashes(trim($opt_value['name']));
            	$content =  str_replace("&#039;","'",html_entity_decode($string,ENT_QUOTES,'UTF-8'));
				$stringData .= '<option value="'.$opt_value['id'].'"'.$selected.'>'.$content.'</option>';
			}
		}
		
		echo $stringData ; */
	}
	
    public static function selectField_nn($label, $name, $value, $list, $caption = '', $required = FALSE)
	{
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		echo '<select name="'.$name.'" id="'.$name.'" class="group_class">';
            echo '<option value="0">None</option>';
		if (is_array($list))
		{
			foreach ($list as $key => $opt_value)
			{
				$selected = '';
				if (trim($opt_value['id']) == trim($value))
				{
					$selected = ' selected';
				}
                $string = stripslashes(trim($opt_value['name']));
            	$content =  str_replace("&#039;","'",html_entity_decode($string,ENT_QUOTES,'UTF-8'));
				echo '<option value="'.esc_attr($opt_value['id']).'"'.esc_attr($selected).'>'.esc_attr($content).'</option>';
			}
		}
		echo '</select>
           &nbsp;  &nbsp; <input type="button" value="Add New" onclick="addGroup()" class="button-primary"  />
           <br>
           <div id="group_name" style="display: none;">
           <input type="text" name="group_name" placeholder="Enter Group Name"   />
           &nbsp <input type="button" value="Save" onclick="saveGr()" class="button-primary" /> </div>
        ';
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.esc_attr($caption).'</div>';
		}
		echo '</td></tr>';
	}
    public static function selectFields($label, $name, $value, $list, $caption = '', $required = FALSE)
	{
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		echo '<select name="'.esc_attr($name).'" id="video_type">';
		if (is_array($list))
		{
			foreach ($list as $key => $opt_value)
			{
				$selected = '';
				if (trim($key) == trim($value))
				{
					$selected = ' selected';
				}
				echo '<option value="'.esc_attr($key).'"'.esc_attr($selected).'>'.esc_attr($opt_value).'</option>';
			}
		}
		echo '</select>';
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.esc_attr($caption).'</div>';
		}
		echo '</td></tr>';
	}
	public static function radioGroup($label, $name, $value, $list, $caption = '', $required = FALSE)
	{
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		if (is_array($list))
		{
			foreach ($list as $key => $opt_value)
			{
				$selected = '';
				if (trim($key) == trim($value))
				{
					$selected = ' selected';
				}
				echo '<input type="radio" name="'.esc_attr($name).'" value="'.esc_attr($key).'"'.esc_attr($selected).' />&nbsp;'.esc_attr($opt_value).'<br />';
			}
		}
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.$caption.'</div>';
		}
		echo '</td></tr>';
	}
	public static function checkboxGroup($label, $name, $value, $list, $caption = '', $required = FALSE)
	{
		echo '<tr><th>'.esc_attr($label).':</th><td>';
		if (is_array($list))
		{
			foreach ($list as $key => $opt_value)
			{
				$checked = '';
				if ((int)$value[$key])
				{
					$checked = ' checked';
				}
				echo '<input type="checkbox" name="'.esc_attr($name).'['.esc_attr($key).']" value="1"'.esc_attr($checked).' />&nbsp;'.esc_attr($opt_value).'<br />';
			}
		}
		if ((int)$required)
		{
			echo '<span class="required">Required</span>';
		}
		if (strlen(trim($caption)))
		{
			echo '<div class="caption">'.esc_attr($caption).'</div>';
		}
		echo '</td></tr>';
	}
	public static function startTable()
	{
		echo '<table class="vpp_form_table" cellpadding="0" cellspacing="5">';
	}
	public static function fadeSave()
	{
		if (isset($_GET['saved']) && $_GET['saved'])
		{
			echo '<div id="vpp_saved" style="padding: 10px;">Saved</div>';
		}
		wp_add_inline_script('svms-scripts', 'fade("vpp_saved")');
	}

    public static function vml_MediaBtn($label, $size = 50,$k="", $caption = '', $required = FALSE)
	{
		if (!intval($size)){ $size = 50; }
		echo '<tr><th>or Add Media File: </th><td>';
        echo '<a title="'.esc_attr($label).'" class="button " onclick="svm_AddMedia()"  href="#"><span class="wp-media-buttons-icon"></span>'.esc_attr($label).'</a>';
		echo '</td></tr>';
	}
    public static function vml_MediaBtn_end($label, $size = 50,$k="", $caption = '', $required = FALSE)
	{
		if (!intval($size)){ $size = 50; }
		echo '<tr><th>or Add Media File: </th><td>';
        echo '<a title="'.esc_attr($label).'" class="button " onclick="svm_AddMedia_endpage()"  href="#"><span class="wp-media-buttons-icon"></span>'.esc_attr($label).'</a>';
		echo '</td></tr>';
	}
	 //////////// button for Layer ////////////
	public static function vml_MediaBtn_layer($label, $size = 50,$k="", $caption = '', $required = FALSE)
	{
		if (!intval($size)){ $size = 50; }
		echo '<tr><th>or Add Media File: </th><td>';
        echo '<a title="'.esc_attr($label).'" class="button " onclick="svm_AddMedia_layer()"  href="#"><span class="wp-media-buttons-icon"></span>'.esc_attr($label).'</a>';
		echo '</td></tr>';
	}
	
	public static function listErrors($error_list)
	{
		if (is_array($error_list) && count($error_list))
		{
			echo '<tr><td colspan="2">';
			echo '<ul class="vpp_error_list">';
			foreach ($error_list as $msg)
			{
				echo '<li>'.esc_attr($msg).'</li>';
			}
			echo '</ul>';
			echo '</td></tr>';
		}
	}
	public static function labelField($label, $value)
	{
		echo '<tr><th>'.esc_attr($label).':</th><td style="vertical-align: middle;">'.$value.'</td></tr>';
	}
	public static function blankRow()
	{
		echo '<tr><th>&nbsp;</th><td>&nbsp;</td></tr>';
	}
	public static function clearRow($text = '')
	{
		if (!$text)
		{
			$text = '&nbsp;';
		}
		echo '<tr><td colspan="2">'.$text.'</td></tr>';
	}
	public static function section($title)
	{
		echo '<tr><td colspan="2" class="section"><h3>'.$title.'</h3></td></tr>';
	}
	public static function endTable()
	{
		echo '</table>';
	}
	
	public static function vml_MediaBtn_pause($label, $size = 50,$k="", $caption = '', $required = FALSE)
	{
		if (!intval($size)){ $size = 50; }
		echo '<tr><th>or Add Media File: </th><td>';
        echo '<a title="'.esc_attr($label).'" class="button " onclick="svm_AddMedia_pause()"  href="#"><span class="wp-media-buttons-icon"></span>'.esc_attr($label).'</a>';
		echo '</td></tr>';
	}
}