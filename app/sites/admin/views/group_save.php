<?php
if ( 
    ! isset( $_POST['group_save_nonce'] ) 
    || ! wp_verify_nonce( $_POST['group_save_nonce'], 'group_add_edit' ) 
) {
 
   print 'Sorry, your nonce did not verify.';
   exit;

}else if(!current_user_can('administrator')){
    print 'Sorry, this user does not have admin privileges';
    exit;
 
} else {
    function DBinss($string){
        $a = html_entity_decode($string);
        return trim(htmlspecialchars($a,ENT_QUOTES));
    }
    global $wpdb;
    $group_name = sanitize_text_field(DBinss($_POST['group_name']));
    $post_id = sanitize_text_field($_POST['id']);   
    $group =   $wpdb->base_prefix.'vpp_groups';
    $wpdb->query($wpdb->prepare("UPDATE $group SET name ='%s' where id='%d'", [$group_name, $post_id]));
?>

<?php
    wp_add_inline_script('svms-scripts', " window.location = 'admin.php?page=". esc_attr(self::$name) . "&action=groups'");
}