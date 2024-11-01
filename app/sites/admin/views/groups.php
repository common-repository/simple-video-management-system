<?php
global $wpdb;
if(isset($_GET['del_gid']) && $_GET['del_gid']!=""){
    $c_id = get_current_user_id();
    $user_data_login = get_userdata($c_id);
    $user_role = $user_data_login->roles[0];
    if($user_role=="administrator"){
           $qrun =  $wpdb->query(
            $wpdb->prepare(
                "delete from ".self::$table['video_groups']." where id=%d",
                sanitize_text_field($_GET['del_gid'])
            )
           );
   }
    if($qrun){
        $_SESSION['group_del'] = 1;
    }
}

$page = 1;
$page_size = 25;
$prev_url = '';
$next_url = '';
if (isset($_GET['p']))
{
	$page = sanitize_text_field((int)$_GET['p']);
}
$total = $wpdb->get_var('SELECT COUNT(*) AS total FROM '.self::$table['video_groups']);
$max_page = ceil($total / $page_size);
if (!$page)
{
	$page = 1;
}
if ($page > $max_page)
{
	$page = $max_page;
}
$start = ($page - 1) * $page_size;
if ($start < 0)
{
	$start = 0;
}
$sql_search = '';
if ($_REQUEST['search'] && !empty($_REQUEST['search']))
{
	$search = sanitize_text_field($_REQUEST['search']);
	$sql_search = ' WHERE (
		name like "%'.addslashes($search).'%"
		OR handle like "%'.addslashes($search).'%"
		OR mp4_url like "%'.addslashes($search).'%"
		OR youtube_url like "%'.addslashes($search).'%"
		OR webm_url like "%'.addslashes($search).'%"
		OR ogg_url like "%'.addslashes($search).'%"
        OR mov_url like "%'.addslashes($search).'%"
        OR vimeo_url like "%'.addslashes($search).'%"
		OR handle like "%'.addslashes($search).'%"
	)';	
}
if (@$_REQUEST['sort_by'] == 'created')
{
	$sort_by = 'created DESC';
}else if(@$_REQUEST['sort_by']=="lb"){
    $sort_by = 'lightbox_enabled DESC';
}
else
{
	$sort_by = 'name';
}
$video_list = $wpdb->get_results('SELECT * FROM '.self::$table['video_groups'].$sql_search.' ORDER BY '.$sort_by, ARRAY_A);
if ($page > 1)
{
	$prev_url = 'admin.php?page='.esc_attr(self::$name).'&action=groups&search='.urlencode(trim(@$_REQUEST['search'])).'&sort_by='.@$_REQUEST['sort_by'].'&p='.($page - 1);
}
if ($page < $max_page)
{
	$next_url = 'admin.php?page='.esc_attr(self::$name).'&action=groups&search='.urlencode(trim(@$_REQUEST['search'])).'&sort_by='.@$_REQUEST['sort_by'].'&p='.($page + 1);
}
$end = $start + $page_size;
if ($end > $total)
{
	$end = $total;
}
if (!$total)
{
	$start = -1;
	$end = 0;
}
function DBouts($string){
	$string = stripslashes(trim($string));
	return str_replace("&#039;","'",html_entity_decode($string,ENT_QUOTES,'UTF-8'));
}
?>


<div style="clear: both"></div>
<?php
if(isset($_SESSION['group_del'])){
        ?>
            <div class="updated">
                <p>Group Deleted Successfully.</p>
            </div>
        <?php
    unset($_SESSION['group_del']);
}
?>

<table class="vpp_list_table" cellspacing="1" cellpadding="0">
	<tr>
        <th style="width: 50px;">SR#</th>
		<th width="50%">Name/Title</th>
        <th style="width: 150px;">Videos</th>
		<th width="1%">Action</th>
	</tr>
	<?php
	if ($video_list)
	{
		$i = 1;
		$total = count($video_list);
		foreach ($video_list as $video)
		{
			
			?>
	<tr>
        <td ><?php echo $i++; ?></td>
		<td><a href="<?php echo 'admin.php?page='.esc_attr(self::$name).'&action=group_edit&gid='.$video['id']; ?>"><?php echo esc_attr(DBouts(($video['name']))); ?></a></td>
        <td >
            <a href="<?php echo 'admin.php?page='.esc_attr(self::$name).'&action=videos&search&p&sort_by=name&group_id='.esc_attr($video['id']); ?>"><?php
            $get_count = $wpdb->get_row($wpdb->prepare("select COUNT(video_id) as total from ".Vpp_Base::$table['video']." where group_id='%d'",[$video['id']]), ARRAY_A);
            echo $get_count['total'];
            ?></a>
        </td>
        <td><a href="<?php echo 'admin.php?page='.esc_attr(self::$name).'&action=groups&del_gid='.$video['id']; ?>" title="Delete Group" onclick="return chk()">Delete</a></td>
	</tr>
	<?php
		}
	}
	else
	{
		?>
	<tr>
		<td class="vpp_empty" colspan="4" align="center"><br />
		Group is Empty<br />
		<br />
		</td>
	</tr>
	<?php
	}
	?>
</table>
