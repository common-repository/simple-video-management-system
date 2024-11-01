var TimeToFade = 2500.0;
function fade(eid)
{
	var element = document.getElementById(eid);
	if (element == null) return;
	element.FadeTimeLeft = TimeToFade;
	setTimeout("animateFade(" + new Date().getTime() + ",'" + eid + "')", 33);
}
function animateFade(lastTick, eid)
{
    var curTick = new Date().getTime();
    var elapsedTicks = curTick - lastTick;
    var element = document.getElementById(eid);
    if(element.FadeTimeLeft <= elapsedTicks) {
        element.style.opacity = element.FadeState == 1 ? '1' : '0';
        element.style.filter = 'alpha(opacity = ' + (element.FadeState == 1 ? '100' : '0') + ')';
        element.FadeState = element.FadeState == 1 ? 2 : -2;
        element.style.display = "none";
        return;
    }
    element.FadeTimeLeft -= elapsedTicks;
    var newOpVal = element.FadeTimeLeft/TimeToFade;
    if(element.FadeState == 1) {
        newOpVal = 1 - newOpVal;
    }
    element.style.opacity = newOpVal;
    element.style.filter = 'alpha(opacity = ' + (newOpVal*100) + ')';
    setTimeout("animateFade(" + curTick + ",'" + eid + "')", 33);
}

function verifyDelete(){
    var confirm = window.confirm("Are you sure to delete grid shortcode?");
    if(confirm){
        return true;
    }else{
        return false;
    }
}

function chk(){
    var w = window.confirm("Are you sure to delete this group?");
    if(w){
        return true;
    }else{
        return false;
    }
}

function change_fvl(vl, id) {

    var arr = vl.split("=");
    jQuery('.' + id).val(arr[0]);
    jQuery("#" + id).val(arr[1]);
}

function change_fvl_ac(vl, id) {
    var arr = vl.split("=");
    jQuery('.' + id).val(arr[2]);
    jQuery("#" + id).val(arr[1]);
    jQuery("#a" + id).val(arr[0]);
}

function export_svms_video() {
    var v = window.confirm("Are You Sure To Export Videos?");
    if (v) {

        return true;
    } else {
        return false;
    }
}

function changeTab(id) {
    var v = jQuery('#tab_' + id).val();

    if (v == '') {
        jQuery('#tab_' + id).val(1);
    }
    var v = jQuery('#tab_' + id).val();

    if (v == 1) {
        jQuery('#tab_' + id).val(2);
        jQuery("#tabclick_" + id).html("Click to hide");

        jQuery('#remm_section_' + id).show();


    } else if (v == 2) {
        jQuery('#tab_' + id).val(1);
        jQuery("#tabclick_" + id).html("Click to reveal");

        jQuery('#remm_section_' + id).hide();

    }
}

function downloadURI(uri, name) {
    var link = document.createElement("a");
    link.download = name;
    link.href = uri;
    link.click();
}

function clearval() {
    jQuery('#search').val("");
    jQuery('#clear_btn').hide();

}

function checksval() {
    var search = jQuery('#search').val();
    if (search != "") {
        jQuery('#clear_btn').show();
    } else {
        jQuery('#clear_btn').hide();
    }
}


function delete_svms_video() {
    var v = window.confirm("Are you sure to delete?");
    if (v) {
        return true;
    } else {
        return false;
    }
}

function deleteGroup(url)
{
    m = "Are you sure you want to delete this video?"
    if(window.confirm(m) == true)
    {
        window.location.href = url;
    }
}

function addGroup(){
    jQuery('#group_name').toggle();
}

/* Video Edit */

/*vitvis*/
jQuery("#intros_outros_chk").click(function(){
    if (jQuery('#intros_outros_chk').attr('checked')){
        jQuery("#intros_outros_chk").val('1');
    }else{
        jQuery("#intros_outros_chk").val('0');
    }
});
         
/*vitvis*/
function checklayeropt(id){
if(id==0){
    jQuery('.layer_html').show();
}else{
    jQuery('.layer_html').hide();
}
}
//validate_customTIme('video_start_time','video_end_time',0)
function validate_customTIme(val_in,val_out,id){
if(id==0){
    var video_start_time = jQuery('#'+val_in).val();
    video_start_time=parseInt(video_start_time);
    var video_end_time = jQuery('#'+val_out).val();
    video_end_time=parseInt(video_end_time);
    //alert(video_start_time +" = "+video_end_time);
    if(video_start_time > video_end_time){
        jQuery('#'+val_in).val(video_end_time-1);
        alert("Please Enter Start Time Less Than End Time");
    }
}else{
    var video_end_time = jQuery('#'+val_in).val();
    video_start_time=parseInt(video_start_time);
    var video_start_time = jQuery('#'+val_out).val();
    video_end_time=parseInt(video_end_time);
    if(video_end_time<video_start_time){
        jQuery('#'+val_in).val(Number(video_start_time)+Number(1));
        alert("Please Enter End Time Greater Than Start Time");
    }
}
}
function changeTab(id){
var v = jQuery('#tab_'+id).val();

if(v==''){
    jQuery('#tab_'+id).val(1);
}
var v = jQuery('#tab_'+id).val();

if(v==1){
    jQuery('#tab_'+id).val(2);
    jQuery("#tabclick_"+id).html("Click to hide");
    
        jQuery('#remm_section_'+id).show();    
    
    
}else if(v==2){
    jQuery('#tab_'+id).val(1);
    jQuery("#tabclick_"+id).html("Click to reveal");
    
        jQuery('#remm_section_'+id).hide();
    
}
}
function chek_dbtn(val){
    if(val==1){
        jQuery('.video_btns').show();
        
    }else{
        jQuery('.video_btns').hide();
    }
}

function sbmt_form() {
    var form = document.getElementById("Video_Form");
    form.submit();
}



function getcrm_list(id){
    jQuery('.auto_resp').hide();
    jQuery('#auto_resp_'+id).show();
}
function check_tagging(){
    if(jQuery('.check_tagging').is(':checked')){
        jQuery('.auto_resp_a').show();
    }else{
        jQuery('.auto_resp_a').hide();
    }
}
function svmsskin(id){
    console.log(id);
    if(id==3){
        jQuery('#video_type').val(2);
        //jQuery('#width_v').val("500");
        //jQuery('#height_v').val("620");
        jQuery('#width_v').val("730");
        jQuery('#height_v').val("370");
    }else if(id==4){
        jQuery('#video_type').val(2);
        jQuery('#width_v').val("490");
        jQuery('#height_v').val("550");
    }else if(id==5 || id==6 ){
        jQuery('#video_type').val(2);
        jQuery('#width_v').val("320");
        jQuery('#height_v').val("450");
    }else if(id==7 || id==8 ){
        jQuery('#video_type').val(2);
        jQuery('#width_v').val("380");
        jQuery('#height_v').val("550");
    }else{
        jQuery('#video_type').val(0);
        jQuery('#width_v').val("1080");
        jQuery('#height_v').val("607");
    }
}
var svm_thumbailimage = function(title,onInsert,isMultiple){
  if(isMultiple == undefined)
   isMultiple = false;
  // Media Library params
  var frame = wp.media({
   title   : title,
   multiple  : isMultiple,
   library  : { type : 'image'},
   button   : { text : 'Insert' }
  });
  // Runs on select
  frame.on('select',function(){
   var objSettings = frame.state().get('selection').first().toJSON();
   var selection = frame.state().get('selection');
   var arrImages = [];
   if(isMultiple == true){ //return image object when multiple
    selection.map( function( attachment ) {
     var objImage = attachment.toJSON();
     var obj = {};
     obj.url = objImage.url;
     obj.id  = objImage.id;
     arrImages.push(obj);
    });
    onInsert(arrImages);
   }else{
    //return image url and id - when single
    svm_thumbail_file(objSettings.url);
    // onInsert("sdsds");
    // console.log(objSettings);
   }
  });
  // Open ML
  frame.open();
 }
 function svm_thumbail_file(img){
    jQuery('#thumbnail_img').attr('src','');
    jQuery('#thumbnail_img').attr('src',img);
    jQuery('#thumbail_url').val('');
    jQuery('#thumbail_url').val(img);
    jQuery('#thumbnail_img').show();
 }
 
 var svm_AddMedia_endpage = function(title,onInsert,isMultiple){
  if(isMultiple == undefined)
   isMultiple = false;
  // Media Library params
  var frame = wp.media({
   title   : title,
   multiple  : isMultiple,
   library  : { type : 'image'},
   button   : { text : 'Insert' }
  });
  // Runs on select
  frame.on('select',function(){
   var objSettings = frame.state().get('selection').first().toJSON();
   var selection = frame.state().get('selection');
   var arrImages = [];
   if(isMultiple == true){ //return image object when multiple
    selection.map( function( attachment ) {
     var objImage = attachment.toJSON();
     var obj = {};
     obj.url = objImage.url;
     obj.id  = objImage.id;
     arrImages.push(obj);
    });
    onInsert(arrImages);
   }else{
    //return image url and id - when single
    svm_AddMedia_file_end(objSettings.url);

   }
  });
  // Open ML
  frame.open();
 }
 function svm_AddMedia_file_end(img){
    jQuery('#end_url').val();
    jQuery('#end_url').val(img);
 }
 
  var svm_AddMedia_pause = function(title,onInsert,isMultiple){
  if(isMultiple == undefined)
   isMultiple = false;
  var frame = wp.media({
   title   : title,
   multiple  : isMultiple,
   library  : { type : 'image'},
   button   : { text : 'Insert' }
  });
  frame.on('select',function(){
   var objSettings = frame.state().get('selection').first().toJSON();
   var selection = frame.state().get('selection');
   var arrImages = [];
   if(isMultiple == true){ //return image object when multiple
    selection.map( function( attachment ) {
     var objImage = attachment.toJSON();
     var obj = {};
     obj.url = objImage.url;
     obj.id  = objImage.id;
     arrImages.push(obj);
    });
    onInsert(arrImages);
   }else{
    svm_AddMedia_file_pause(objSettings.url);
   }
  });
  // Open ML
  frame.open();
 }
function svm_AddMedia_file_pause(img){
    jQuery('#pause_overlay_image').val();
    jQuery('#pause_overlay_image').val(img);
 }
 
var svm_AddMedia = function(title,onInsert,isMultiple){
  if(isMultiple == undefined)
   isMultiple = false;
  // Media Library params
  var frame = wp.media({
   title   : title,
   multiple  : isMultiple,
   library  : { type : 'image'},
   button   : { text : 'Insert' }
  });
  // Runs on select
  frame.on('select',function(){
   var objSettings = frame.state().get('selection').first().toJSON();
   var selection = frame.state().get('selection');
   var arrImages = [];
   if(isMultiple == true){ //return image object when multiple
    selection.map( function( attachment ) {
     var objImage = attachment.toJSON();
     var obj = {};
     obj.url = objImage.url;
     obj.id  = objImage.id;
     arrImages.push(obj);
    });
    onInsert(arrImages);
   }else{
    //return image url and id - when single
    svm_AddMedia_file(objSettings.url);
    // onInsert("sdsds");
    // console.log(objSettings);
   }
  });
  // Open ML
  frame.open();
 }
 function svm_AddMedia_file(img){
    jQuery('#splash_urls').val();
    jQuery('#splash_urls').val(img);
 }
 
 /////////////////// code for layer image ////////////
 var svm_AddMedia_layer = function(title,onInsert,isMultiple){
  if(isMultiple == undefined)
   isMultiple = false;
  // Media Library params
  var frame = wp.media({
   title   : title,
   multiple  : isMultiple,
   library  : { type : 'image'},
   button   : { text : 'Insert' }
  });
  // Runs on select
  frame.on('select',function(){
   var objSettings = frame.state().get('selection').first().toJSON();
   var selection = frame.state().get('selection');
   var arrImages = [];
   if(isMultiple == true){ //return image object when multiple
    selection.map( function( attachment ) {
     var objImage = attachment.toJSON();
     var obj = {};
     obj.url = objImage.url;
     obj.id  = objImage.id;
     arrImages.push(obj);
    });
    onInsert(arrImages);
   }else{
    //return image url and id - when single
    svm_AddMedia_layer_file(objSettings.url);
    // onInsert("sdsds");
    // console.log(objSettings);
   }
  });
  // Open ML
  frame.open();
 }
 function svm_AddMedia_layer_file(img){
    jQuery('#layer_img_url').val();
    jQuery('#layer_img_url').val(img);
 }
 //////////// end code here ////////////////
 
 
 
 
 function check_custom(){
    if(jQuery('.custom_video').is(':checked')){
        jQuery('.custom_settings').show();
    }else{
        jQuery('.custom_settings').hide();
    }
 }
 function checkenabled(){
    if(jQuery('.is_lighbox').is(':checked')){
        jQuery('.thumnail_row').show();
    }else{
        jQuery('.thumnail_row').hide();
    }
 }
 function bgcolor_ck(){
 	 //alert();
	if(jQuery("#bg_color_ck").prop('checked') == true){
	//do something
	 	jQuery("#bg_color_ck").val('1');
	 	// alert();
	}else{
		jQuery("#bg_color_ck").val('0');
	}
 }
 function checksplash(){
    if(jQuery('.use_splash').is(':checked')){
        var img = jQuery('#splash_urls').val();
        if(img==''){
            jQuery('.use_splash').attr('checked',false);
            alert('Splash Page Image URL Is Empty.');
        }else{
            jQuery('#thumbnail_img').attr('src','');
            jQuery('#thumbnail_img').attr('src',img);
            jQuery('#thumbail_url').val('');
            jQuery('#thumbail_url').val(img);
            jQuery('#thumbnail_img').show();
        }
    }else{
         jQuery('#thumbnail_img').attr('src','');
        jQuery('#thumbail_url').val('');
        jQuery('#thumbnail_img').hide();
    }
 }
 function check_static(){
    if(jQuery('.check_static').is(':checked')){
        jQuery('.hide_html').show();
    }else{
        jQuery('.hide_html').hide();
    }
 }
  function check_optin(){
    if(jQuery('.check_optin').is(':checked')){
        jQuery('#opting_dis').show();
        jQuery('.optin_html').show();
    }else{
        jQuery('#opting_dis').hide();
        jQuery('.optin_html').hide();
    }
 }

    //if(jQuery('.checkOptions').is(':checked')){
    jQuery('#width_v').on('keyup',function(){
        var val_s = jQuery('#video_type').val();
        var width = parseInt( jQuery(this).val() );
        if(val_s==0){
            width = Math.round(width * 0.562);
            if(!jQuery.isNumeric(width))
            width = 0;
            jQuery('#height_v').val(width);
        }
        if(val_s==1){
            width = Math.round(width * 0.75);
            if(!jQuery.isNumeric(width))
            width = 0;
            jQuery('#height_v').val(width);
        }
    });
    jQuery('#height_v').on('keyup',function(){
        var val_s = jQuery('#video_type').val();
            var height = parseInt( jQuery(this).val());
            if(val_s==0){
                var d = Number(56.25);
                var v = Number((height*100))/d;
                height = Math.round(v);
                if(!jQuery.isNumeric(height))
                height = 0;
                jQuery('#width_v').val(height);
            }
            if(val_s==1){
                var d = Number(75);
                var v = Number((height*100))/d;
                height = Math.round(v);
                if(!jQuery.isNumeric(height))
                height = 0;
                jQuery('#width_v').val(height);
            }
    });
/** End Video Edit */
/** Media Form */
function vpp_searchVideo()
{
	jQuery('#vpp_loader_ball').show();
	jQuery.ajax({
		url: 'admin.php?page=s3_video_player_plus&action=video_search&search=' + jQuery('#vpp_search_string').val(),
		dataType: 'html',
		timeout: 30000,
		success: function(data){
			jQuery('#vpp_search_results').html(data);
			jQuery('#vpp_loader_ball').hide();
		}
	});
}
function vpp_insertVideo(shortcode)
{
	window.send_to_editor(shortcode);
	tb_remove();
}
function vpp_submitVideo()
{
	var errors = false;
	if (jQuery.trim(jQuery('#vpp_form_name').val()) == '')
	{
		alert('Please enter a Name/Title for your video');
	}
	if (jQuery.trim(jQuery('#vid_url').val()) == '' )
	{
		alert('Please enter the URL for at least one video file');
	}
	jQuery.ajax({
		url: '<?php echo esc_url($video_save_url); ?>',
		type: 'post',
		dataType: 'json',
		data: {
			'form_vars[name]': jQuery('#vpp_form_name').val(),
			'form_vars[vid_source]': jQuery('#vid_source').val(),
			'form_vars[vid_url]': jQuery('#vid_url').val(),
			'form_vars[splash_url]': jQuery('#vpp_form_splash_url').val(),
			'form_vars[width]': jQuery('#width_v').val(),
            'form_vars[height]': jQuery('#height_v').val(),
            'form_vars[size_type]': jQuery('#video_type').val(),
			'form_vars[align]': jQuery('#vpp_form_align').val(),
		},
		success: function(data){
			vpp_insertVideo('[s3vpp id=' + esc_attr(data['handle']) + ']');
		},
		error: function(data){
			alert('There was a problem submitting this form');
		}
	});
}
jQuery(document).ready(function(){
    jQuery('#width_v').on('keyup',function(){
        var val_s = jQuery('#video_type').val();
        var width = parseInt( jQuery(this).val() );
        if(val_s==0){
            width = Math.round(width * 0.562);
            if(!jQuery.isNumeric(width))
            width = 0;
            jQuery('#height_v').val(width);
        }
        if(val_s==1){
            width = Math.round(width * 0.75);
            if(!jQuery.isNumeric(width))
            width = 0;
            jQuery('#height_v').val(width);
        }
    });
    jQuery('#height_v').on('keyup',function(){
        var val_s = jQuery('#video_type').val();
            var height = parseInt( jQuery(this).val());
            if(val_s==0){
                var d = Number(56.25);
                var v = Number((height*100))/d;
                height = Math.round(v);
                if(!jQuery.isNumeric(height))
                height = 0;
                jQuery('#width_v').val(height);
            }
            if(val_s==1){
                var d = Number(75);
                var v = Number((height*100))/d;
                height = Math.round(v);
                if(!jQuery.isNumeric(height))
                height = 0;
                jQuery('#width_v').val(height);
            }
    });
});