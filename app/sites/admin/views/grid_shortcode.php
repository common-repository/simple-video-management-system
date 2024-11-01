<?php

$video_grids = $wpdb->base_prefix.'vpp_video_grids';
if(!isset($_GET['view'])){
    
if(isset($_GET['vid_grid']) && $_GET['vid_grid']!='') {

    $vid_grid = sanitize_text_field($_GET['vid_grid']);
    
    $get_row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $video_grids where id = %d",
            $vid_grid
        )
    ,ARRAY_A);

    if(count($get_row)>0){
        $id = $get_row['id'];
        $code = $get_row['code'];
        $width = $get_row['width'];
        $cols = $get_row['cols'];
        $string = $get_row['title'];
    
        if($get_row['rand_key']==0){
            $get_row['rand_key'] = time();
            $wpdb->query($wpdb->prepare("UPDATE $video_grids SET rand_key=%s where id=%d", [$get_row['rand_key'], $id]));
            
        }

        $rand_key = $get_row['rand_key'];
        
       	$string = stripslashes(trim($string));
        $title =  str_replace("&#039;","'",html_entity_decode($string,ENT_QUOTES,'UTF-8'));
    }
    else {
        $code= "";
        $width = "";
        $cols = "";
        $id = "";
        $title = "";
        $rand_key = time();
    }
}else{
    $code= "";
    $width = "";
    $cols = "";
    $id = "";
    $title = "";
    $rand_key = time();
}
    if ($width != '') {
        $w = $width;
    } else {
        $w = 3;
    }
    if ($cols != '') {
        $c = $cols;
    } else {
        $c = 3;
    }



?>
<a href="admin.php?page=<?php echo esc_attr(self::$name) ?>&action=grid_shortcode&view=all" style="font-size: 14px;"><input type="button" style="float: right;" value="Show Grids" class="button-primary" /></a>
<div style="clear: both"></div>
<div class="updated" style="padding: 0.2%; display: none;" id="success_update">
    <p>Grid Saved Successfully.</p>
</div>
<h3>Your shortcode:</h3>
<input type="text" placeholder="Enter Grid Title" name="title" value="<?php echo esc_attr($title); ?>" id="grid_title" style="width: 100%;" required />
<textarea id="shortcode" style="height: 50px; width: 100%; margin-top: 1%;" onfocus="this.select();" onmouseup="return false;">[svms_grid]</textarea>
<form id="shortcode_builder">
<!--<p>The OTO Shortcode shows page or post content.  This can be between the start and end date/times and/or the display of that page or post can be set to only a limited number of visits and can be hidden a specific number of visits before the content first shows.</p>-->
<p>Copy shortcode and place in your page or post at the position you want.. <input id="cstm_btn" onclick="getsave()" type="button" style="float: right;" class="button-primary" value="Save Grid" /></p>
<p style="text-align: right; font-weight:  bold; color: green; display: none" id="saving">Saving...</p>
<table class="vpp_form_table" cellspacing='5px' width="100%">
<tr>
	<th># of Columns</th>
	<td>
		<input type="text" id="cols" name="cols" value="<?php echo esc_attr($c); ?>" style="width: 75px;" />
		<br />The cols of the page or post you wish to display. Required if the shortcode is not wrapping inline content.
	</td>
</tr>
<tr>
	<th>Gutter Width </th>
	<td>
		<input type="text" id="width" name="cols" value="<?php echo esc_attr($w); ?>" style="width: 75px;" />
	</td>
</tr>
<tr>
	<td colspan="2">
		<br />

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td >
    <h3 style="margin: 0; padding: 0; background-color: #FFF; border: none;">Videos in Shortcode</h3>
    <table>
        <tr>
            	<td valign="top">
                    
            		<ul id="videos_used" class="connected_sortable">
                    <?php
                        if(isset($id) && $id!=''){
                            $vid_selected = $wpdb->get_results($wpdb->prepare('SELECT video_id, name, vimeo_url FROM '.self::$table['video'].' where video_id IN (%s) and vimeo_url="" ORDER BY name',[$code]), ARRAY_A);
                            foreach ($vid_selected as $v) {
                                
                                echo '<li class="ui-sortable-handle" style="">'.esc_attr($v['name']).'<input type="hidden" class="video_list" value="'.esc_attr($v['video_id']).'" /></li>';
                            }
                        }
                    ?>
            		</ul>
            		<img src="<?php echo esc_url(self::$plugin_url) ?>includes/images/s.gif" width="300" height="1" border="0" />
            	</td>
        </tr>
    </table>
    </td>
	<td >
    <h3 style="margin: 0; padding: 0; background-color: #FFF; border: none;">Available Videos</h3>
    <table>
        <tr>
    	<td valign="top">
    		<ul id="videos" class="connected_sortable">
    <?php
    if(isset($id) && $id!=''){
        $video_list = $wpdb->get_results($wpdb->prepare('SELECT video_id, vimeo_url, name FROM '.self::$table['video'].' where video_id NOT IN (%s) and vimeo_url="" ORDER BY name',[$code]), ARRAY_A);
    }else{
        $video_list = $wpdb->get_results('SELECT video_id, vimeo_url, name FROM '.self::$table['video'].' where vimeo_url="" ORDER BY name', ARRAY_A);
    }
    if ($video_list)
    {
    	foreach ($video_list as $video)
    	{
    		echo '<li>'.$video['name'].'<input type="hidden" class="video_list" value="'.$video['video_id'].'" /></li>';
    	}
    }
    ?>
    		</ul>
    		<img src="<?php echo esc_url(self::$plugin_url) ?>/includes/images/s.gif" width="300" height="1" border="0" />
    	</td>
        </tr>
    </table>
    </td>
</tr>
</table>
		<p>Drag Videos from the right column to the left column to be added to this shortcode.</p>
		<p>Drag Videos within the left column to sort them in the order you wish them to be seen.</p>
	</td>
</tr>
</table>
<?php wp_nonce_field( 'grid_add_edit', 'grid_save_nonce' ); ?>

</form>
<input type="hidden" id="vids" />
<?php
}else{
    

    ?>
    <a href="admin.php?page=<?php echo esc_attr(self::$name) ?>&action=grid_shortcode" style="font-size: 14px;">Go Back</a>
    <?php
    if(isset($_GET['act']) && $_GET['act']=="delete_grid"){
        $grid_id = sanitize_text_field($_GET['del_grid']);
        $c_id = get_current_user_id();
        $user_data_login = get_userdata($c_id);
        $user_role = $user_data_login->roles[0];
            if($user_role=="administrator"){
                $delete = $wpdb->query(
                    $wpdb->prepare("delete from $video_grids where id=%d",$grid_id)
                );
                if($delete){
                ?>
                <div class="updated" style="padding: 0.2%;" >
                    <p>Grid Deleted Successfully.</p>
                </div>
                <?php
            }
        }
    }
    ?>
        <table class="vpp_list_table" cellspacing="1" cellpadding="0">
            	<tr>
                    <th nowrap="nowrap">SR#</th>
                    <th width="30%">Title</th>
            		<th width="40%">Grid Shortcode</th>
                    <th nowrap="nowrap">Modified Date</th>
            		<th nowrap="nowrap">Action</th>
            	</tr>
        <?php
    
        $per_page=15;
    
    $pages_query=$wpdb->get_row("SELECT COUNT('id') as total FROM ".$video_grids." where type='0'",ARRAY_A);

        
    $pages = ceil($pages_query['total'] / $per_page);
	$page = (isset($_GET['gid'])) ? (int)$_GET['gid'] : 1;
	$start=($page - 1) * $per_page;

    $query=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$video_grids." WHERE type='0' order by id desc LIMIT %d, %d ",[$start, $per_page]),ARRAY_A);
    if(isset($_GET['gid']) && $_GET['gid']>1)
        {
            $counter = sanitize_text_field($_GET['gid']) * 15;
            $counter= $counter-14 ;
        }
        else
        {
            $counter = "1";    
        }
	if(count($query)>0)
	{
         $i=1;
            foreach($query as $val)
            {
                if($val['rand_key'] == 0){
                    $val['rand_key'] = $val["id"];
                 }
            ?>
                <tr>
            		<td><?php echo esc_attr($counter); ?></td>
                    <td >
                    <?php
                    $string = stripslashes(trim($val['title']));
                        $tit =  str_replace("&#039;","'",html_entity_decode($string,ENT_QUOTES,'UTF-8'));
                        echo "<a href='admin.php?page=".esc_attr(self::$name)."&action=grid_shortcode&vid_grid=$val[id]'>$tit</a>";
                    ?>
                    </td>
            		<?php
                    echo "<td>[svms_grid id='".esc_attr($val['rand_key'])."' cols=".esc_attr($val["cols"])." width=".esc_attr($val["width"])." videos='".esc_attr($val["code"])."']</td>";
                    echo "<td>". esc_attr($val['created'])."</td>";
                    echo "<td><a href='admin.php?page=".esc_attr(esc_attr(self::$name))."&action=grid_shortcode&vid_grid=$val[id]'><img src='".esc_url(self::$plugin_url)."includes/icons/pencil.png' width='16' height='16' alt='Edit' title='Edit' border='0'/></a>
                    &nbsp; <a onclick='return verifyDelete()' href='admin.php?page=".esc_attr(esc_attr(self::$name))."&action=grid_shortcode&view=all&act=delete_grid&del_grid=".$val['id']."'><img src='".esc_url(self::$plugin_url)."/includes/images/del.png' width='16' height='16' alt='Delete' title='Delete' border='0'/></a>
                    </td>";
                    ?>
            	</tr>
            <?php
                $counter++;
            }
              ?>
        
        <?php
        }
      
	else
	{
		echo '<tr><td colspan="4">No Record Found</td></tr>';
	}
    echo "</table>";
	echo "<p style='text-align: center;'>";
	if($pages > 1)
	{
		$previous = $page - 1;
		$next = $page + 1;
		$s = 1;
		
		if(!($page<=1))
		{
            echo '<a  href="'.esc_url(admin_url()).'admin.php?page='.esc_attr(self::$name).'&action=grid_shortcode&view=all&gid='.$s.'"><button type="button">First</button>  </a>';
            echo '<a  href="'.esc_url(admin_url()).'admin.php?page='.esc_attr(self::$name).'&action=grid_shortcode&view=all&gid='.$previous.'"><button type="button">Previous</button>  </a>';
			
		}
		for($a=1; $a<=$pages; $a++)
		{
			echo ($a == $page) ? '<a  href="'.esc_url(admin_url()).'admin.php?page='.esc_attr(self::$name).'&action=grid_shortcode&view=all"><button >'.$a.'</button></a>  ': '
			<a  href="'.esc_url(admin_url()).'admin.php?page='.esc_attr(self::$name).'&action=grid_shortcode&view=all&gid='.$a.'"><button type="button">'.$a.'</button>  </a>'; 
		} 
		
		if(!($page >= $pages))
		{
		  echo '<a  href="'.esc_url(admin_url()).'admin.php?page='.esc_attr(self::$name).'&action=grid_shortcode&view=all&gid='.$next.'"><button type="button">Next</button>  </a>';
            echo '<a  href="'.esc_url(admin_url()).'admin.php?page='.esc_attr(self::$name).'&action=grid_shortcode&view=all&gid='.$pages.'"><button type="button">Last</button>  </a>';
		}
	}
    echo '</p>';

    
}
$nonce = wp_create_nonce('grid_add_edit');
$getSave = "function getsave() {
    jQuery('#saving').show();
    jQuery('#cstm_btn').attr('disabled',true);
    var vids = jQuery('#vids').val();
    var cols = parseInt(jQuery('#cols').val());
    var width = parseInt(jQuery('#width').val());
    var grid_title = jQuery('#grid_title').val();";
    if(isset($id) && $id!='') {       
        $getSave .= " var rec ='width='+width+'&cols='+cols+'&code='+vids+'&type=0&id=$id&title='+grid_title+'&rand_key=$rand_key&_ajax_nonce=$nonce';";
    } else {
        $getSave .= " var rec = 'width='+width+'&cols='+cols+'&code='+vids+'&type=0&title='+grid_title+'&rand_key=$rand_key&_ajax_nonce=$nonce';";
    }
    $getSave .= " jQuery.post('". esc_url(admin_url())."admin-ajax.php?action=vpp_saveGrid',rec,function(data){
        jQuery('#cstm_btn').attr('disabled',false);
        jQuery('#saving').hide();
        if(data==1){
            jQuery('#success_update').show();
            setTimeout(function(){
                jQuery('#success_update').hide();    
            },4000);
        }else{
            alert('An error occurred. Please Try Again.')
        }
    });
}";
wp_add_inline_script('svms_scripts_footer' , $getSave);

$updateShortCode = "function updateShortcode()
{
	var shortcode = '[svms_grid id=" .$rand_key."';
	var cols = parseInt(jQuery('#cols').val());
    var width = parseInt(jQuery('#width').val());
	if (cols || cols > 0)
	{
		shortcode = shortcode + ' cols=' + cols;
	}
    if (width || width > 0)
	{
	   shortcode = shortcode + ' width=' + width;
	}
	var check_list = [];
	var i = 0;
	jQuery('#videos_used .video_list').each(function() {
		if (parseInt(jQuery(this).val()) > 0)
		{
			check_list[i] = parseInt(jQuery(this).val());
			i++;
		}
	});
	shortcode = shortcode + ' videos=' + String(check_list.join()) + ']';
    
        jQuery('#vids').val(String(check_list.join()));
    
	jQuery('#shortcode').val(shortcode);
}
jQuery(document).ready(function(){
	updateShortcode();
	jQuery('#shortcode_builder').find('input[type=stext]').change(updateShortcode);
	jQuery('#cols').keypress(function(e) {
	    var a = [];
	    var k = e.which;
	    for (i = 48; i < 58; i++)
	        a.push(i);
	    if (!(jQuery.inArray(k,a)>=0))
	        e.preventDefault();
	});
	jQuery('#videos_used, #videos').sortable({
		placeholder: 'highlight',
		connectWith: '.connected_sortable',
		stop: function( event, ui ){
			updateShortcode();
		}
	}).disableSelection();
});";
wp_add_inline_script('svms_scripts_footer' , $updateShortCode);

if(isset($_GET['vid_grid']) && $_GET['vid_grid']!=''){
?>
    <h3>Pages/Posts Used on...</h3>
<ul>
<?php
$sql = $wpdb->prepare('SELECT
p.*
FROM
'.Vpp_Base::$table['video_location'].' vl,
'.$wpdb->base_prefix.'posts p
WHERE vl.post_id = p.ID
AND
	vl.video_id > 0
AND
    vl.vid_status = 2
AND
vl.video_id = %s
ORDER BY
	p.post_title
',[(int)$rand_key]);

$post_list = $wpdb->get_results($sql, ARRAY_A);

foreach ($post_list as $post)
{
	echo '<li>- <a href="'.esc_attr($post['guid']).'" target="_blank">'.esc_attr($post['post_title']).'&nbsp;&nbsp;</a> &nbsp; &nbsp; <a href="'.esc_url(admin_url()).'post.php?post='.esc_attr($post['ID']).'&action=edit" target="_blank" title="Edit Page/Post"><img style="width: 18px;" src="'.esc_url(VPP_PLUGIN_URL).'includes/images/icon-pencil.png" /></a></li>';
}
?>
</ul>
<?php
}
?>