<h2>Group Edit</h2>
<?php
require VPP_APP_PATH.'/Vpp_Form.php';

function DBoutss($string){
	$string = stripslashes(trim($string));
	return str_replace("&#039;","'",html_entity_decode($string,ENT_QUOTES,'UTF-8'));
}

$get_group = $wpdb->get_row(
    $wpdb->prepare(
        "select * from ".self::$table['video_groups']." where id=%d",
        sanitize_text_field($_GET['gid'])
    )
,ARRAY_A);

if(count($get_group)>0){
?>
	<form action="admin.php?page=<?php echo esc_attr(self::$name) ?>&action=group_save" method="POST">
	<?php
	Vpp_Form::startTable();
	?>
		<input type="hidden" name="id" value="<?php echo esc_attr($get_group['id']); ?>" />
		<tr>
			<th>Group Name</th>
			<td><input type="text" name="group_name" value="<?php echo esc_attr(DBoutss($get_group['name'])); ?>" /></td>
		</tr>
		
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" class="button-primary action" value="Save" />
			</td>
		</tr>
	</table>
	<?php wp_nonce_field( 'group_add_edit', 'group_save_nonce' ); ?>
	</form>
	<hr />
<?php
	}
