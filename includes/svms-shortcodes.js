function videoPlay(vid) {
    var keys = jQuery('.lay_' + vid).attr('atk');
    var myPlayer = videojs('#vpp_video_' + keys);
    myPlayer.muted(false);
    jQuery('.lbr_' + keys).hide();
    myPlayer.play();
}

function ClosOverlay(vid){
    var keys = jQuery('.obr_'+vid).attr('atk');
    jQuery('#overlay_pause_'+keys).css('display','none');
    var myPlayer = videojs('#vpp_video_'+keys);  
      myPlayer.play();
    
 }
