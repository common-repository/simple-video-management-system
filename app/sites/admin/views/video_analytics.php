<?php
    global $wpdb;
     $agents = $wpdb->prefix."vpp_useragents";
    $ips = $wpdb->prefix."vpp_ips";
    $states = $wpdb->prefix."vpp_stats";
    $dropOf = $wpdb->prefix."vpp_dropsVideo";
    $video = $wpdb->base_prefix.'vpp_video';
    $played = 0;
    $total_videos=0;


function DBout($string)
{
    $string = @trim($string); 
    $string = stripslashes($string);
    return str_replace("&#039;","'",html_entity_decode($string,ENT_QUOTES,'UTF-8'));
}
function DBin($string)
{
 $a = html_entity_decode($string);
 return trim(htmlspecialchars($a,ENT_QUOTES));
}

$vid = sanitize_text_field($_REQUEST['analytics_video']);
    
$s = $wpdb->prepare("select sum(plays) as totalPlays, sum(completed_plays) as completedPlayes from $states where video_id=%d", [$vid]);
$row_nk = $wpdb->get_row($s,ARRAY_A);
if(count($row_nk)>0){
	$recc = $row_nk;
	$totalPlay = $recc["totalPlays"];
	$completedPlay = $recc["completedPlayes"];
	if($totalPlay=="")
		$totalPlay = '0';
	if($completedPlay=='')
		$completedPlay = '0';
}
$month = date("Y-m-d",strtotime("-10 days"));
$endDay= date("Y-m-d");
$arr = array();
for($i=0;$i<35;$i++){
	$day = date("Y-m-d",strtotime($month."+".$i." day"));
	$curDate = date("Y-m-d",strtotime($month."+".$i." day"));
    
	$ss = $wpdb->prepare("select sum(plays) as totalPlays from $states where date(play_date)='%s' and video_id=%d",[$curDate,$vid]);
    $row_nks = $wpdb->get_row($ss,ARRAY_A);
    if(count($row_nks)>0){
		$rec = $row_nks;
		$totalPlayes = $rec["totalPlays"];
		$completedPlays = $rec["completedPlayes"];
		if($totalPlayes=="")
			$totalPlayes = 0;
	}
	
	$sc = $wpdb->prepare("select sum(completed_plays) as completedPlayes from $states where date(play_date)='%s' and video_id=%d",[$curDate,$vid]);
    $row_nki = $wpdb->get_row($sc,ARRAY_A);
    if(count($row_nki)>0){
		$recc = $row_nki;
		$completedPlays = $recc["completedPlayes"];
		if($completedPlays=='')
			$completedPlays = 0;
	}
    settype($totalPlayes,'integer');
    settype($completedPlays,'integer');
    $arr[$i]['period'] = $day;
    $arr[$i]['licensed'] = $totalPlayes;
    $arr[$i]['sorned'] = $completedPlays;

	if($day == $endDay)
		break;
}

$data = json_encode($arr);
$sb = $wpdb->prepare("select sum(is_desktop) as desktop, sum(is_mobile) as mobile from $agents where video_id=%d",[$vid]);
$exe = $wpdb->get_row($sb,ARRAY_A);
if(count($exe)){
    $mobile = $exe['mobile'];
    $desktop = $exe['desktop'];
}else{
    $mobile = 0;
    $desktop = 0;
}
$n_arr = array();
$n_arrs = array(0=>array('Country','Total'));
$get_st = $wpdb->prepare("SELECT city,count(id) as total FROM `$ips` where video_id=%d group by city ",[$vid]);
$get_st = $wpdb->get_results($get_st,ARRAY_A);

$get_ct = $wpdb->prepare("SELECT c_name,count(id) as total FROM `$ips` where video_id=%d group by c_name ",[$vid]);
$get_ct = $wpdb->get_results($get_ct,ARRAY_A);
if(count($get_st)){
    $j=0;
    foreach($get_st as $d){
        $n_arr[$j]['label'] = $d['city'];
        $n_arr[$j]['value'] = $d['total'];//@round((@$d['total']/$totalPlay*100));
        $j++;
    }
}
if(count($get_ct)){
    $k=1;
    foreach($get_ct as $dv){
        settype($dv['total'],'integer');
       $n_arrs[$k]=array($dv['c_name'],$dv['total']);
        $k++;
    }
}

$data_donut = json_encode($n_arr);
$n_arrs = json_encode($n_arrs);
?>
<?php
    $av = $wpdb->prepare("SELECT AVG(drop_off) as drop_off FROM $dropOf WHERE video_id=%d",[$vid]);
    $getr = $wpdb->get_row($av,ARRAY_A);
    if(count($getr)>0){
        $drop_off = $getr['drop_off'];
    }else{
        $drop_off = '0';
    }
$avs = $wpdb->prepare("SELECT * FROM $dropOf WHERE video_id=%d ORDER BY id DESC LIMIT 1",[$vid]);
    $getrf = $wpdb->get_row($avs,ARRAY_A);
    if(count($getrf)>0){
        $drop_offs = $getrf['drop_off'];
    }else{
        $drop_offs = '0';
    }
$hours = floor($drop_off / 3600);
$minutes = floor(($drop_off / 60) % 60);
$seconds = $drop_off % 60;

$hours_s = floor($drop_offs / 3600);
$minutes_s = floor(($drop_offs / 60) % 60);
$seconds_s = $drop_offs % 60;


$cc = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$ips` where video_id=%d order by id desc limit 1",[$vid]),ARRAY_A);
$jsons = array();
$jsons[0] = "<p><strong>Most Recent</strong>: The most recent person to start watching this video was from ".@$cc['c_name']." !</p>";
$jsons[1] = "<p><strong>Droped Off</strong>: The most recent person Dropped off video at $hours_s:$minutes_s:$seconds_s </p>";

$json = json_encode($jsons);


?>

<?php
$videoJs = "jQuery(document).ready(function(){
    var obj = JSON.parse('". $json . "');";
if(count($cc) > 0) {
$videoJs .= "var i=0;            
    setInterval(function() {
        jQuery('#uopm_success').html('');
        jQuery('#uopm_success').html(obj[i]);
        i++;
        if(i==2){
            i=0;
        }
    },6000);";
}
$videoJs .= "});";
?>

<?php
wp_add_inline_script('svms-scripts', $videoJs);
$get_video = $wpdb->get_row($wpdb->prepare("SELECT name FROM $video WHERE video_id=%d",[$vid]),ARRAY_A);
?>
        <h1>Analytics Report of : <b><?php echo esc_attr($get_video['name']); ?></b></h1>
        <div style="clear: both"></div>

        <!--<button style="float: right;" type="button" class="btnBlue" onclick="window.location.href='admin.php?page=<?php echo esc_attr(self::$name) ?>&action=videos'" ><i class="fa fa-arrow-left"></i> Go Back</button> -->
        <button style="float: right;" type="button" class="btnBlue" onclick="window.location.href='admin.php?page=<?php echo esc_attr(self::$name) ?>&action=video_edit&video_id=<?php echo esc_attr(@$_GET['analytics_video']); ?>'" ><i class="fa fa-arrow-left"></i> Go Back</button>
        
        <div style="clear: both"></div>
        <table>
            <tr>
                <td>
                    <div class="vidphenom-dashboard-tile-d detail blueclr">
            
                            <div class="content_d">
                                <h1 data-speed="2500" data-to="<?php echo esc_attr($totalPlay) ?>" data-from="0" class="vid-phenom-text-left timer"><?php echo esc_attr($totalPlay)?></h1>
                                <div class="uop_line_undr"></div>
                                <span>Total Plays</span>
                            </div>
                            <div class="vid-phenom-icon">
                            <i class="fa fa-play-circle">
                            </i>
                            </div>
                        </div>
                </td>
                <td>
                 <div class="vidphenom-dashboard-tile-d detail  blueclr">
                            <div class="content_d">
                                <h1 data-speed="2500" data-to="<?php echo esc_attr($completedPlay) ?>" data-from="0" class="vid-phenom-text-left timer"><?php echo esc_attr($completedPlay) ?></h1>
                                <div class="uop_line_undr"></div>
                                <span>Completed Plays</span>
                            </div>
                            <div class="vid-phenom-icon"><i class="fa fa-area-chart"></i>
                            </div>
                        </div>
                 </td>
              
            </tr>
            <tr>
                  <td>
                <div class="vidphenom-dashboard-tile-d detail  blueclr">
                        <?php $vd =  @round((@$completedPlay/$totalPlay*100)); ?>
                            <div class="content_d">
                                <h1 data-speed="2500" data-to="<?php echo esc_attr($vd); ?>" data-from="0" class="vid-phenom-text-left timer"><?php echo esc_attr($vd); ?></h1>
                                <div class="uop_line_undr"></div>
                                <span>Completion %</span>
                            </div>
                            <div class="vid-phenom-icon"><i class="fa fa-line-chart"></i>
                            </div>
                        </div>
                   
                </td>
                <td>
                <div class="vidphenom-dashboard-tile-d detail blueclr">
                    <?php
                        $hours = floor($drop_off / 3600);
                        $minutes = floor(($drop_off / 60) % 60);
                        $seconds = $drop_off % 60;
                    ?>
                                         
                            <div class="content_d">
                                <h1 class="vid-phenom-text-left"><?php echo esc_attr("$hours:$minutes:$seconds"); ?></h1>
                                <div class="uop_line_undr"></div>
                                <span>Average Drop Off</span>
                            </div>
                            <div class="vid-phenom-icon"><i class="fa fa-chain-broken"></i>
                            </div>
                        </div>
                   
                </td>
            </tr>
            <?php
            
        if(count($cc)>0){
        ?>
            <tr>
                <td colspan="4">
                    <div id="uopm_success" class="succ_green">
                        <?php echo $jsons[0]; ?>
                    </div>
                </td>
            </tr>
            <?php
            }
            ?>
            <tr>
                <td colspan="4">
                    <div class="postbox " id="formatdiv">
                        <div title="Click to toggle" class="handlediv"><br></div>
                        <h3 style="background-color: #E9E9E9; margin-top: 0px; padding-top: 6px; color:#001847;" class="hndle ui-sortable-handle"><span>Most Recent Videos Report</span></h3>
                            <div class="inside">
                                <div id="post-formats-select">
                                <div id="graph" style="height: 250px;"></div>
                                </div>
                            </div>
                        </div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="postbox " id="formatdiv">
                        <div title="Click to toggle" class="handlediv"><br></div>
                        <h3 style="background-color: #E9E9E9; margin-top: 0px; padding-top: 6px; color:#001847;" class="hndle ui-sortable-handle"><span>Break Down Plays By Device</span></h3>
                            <div class="inside">
                                <div id="post-formats-select">
                                   <table width="100%">
                                        <tr>
                                            <td><b style="float: left; font-size:16px;"><?php echo  esc_attr(@round((@$mobile/$totalPlay*100))); ?>% Are Mobile Devices</b></td>
                                            <td><b style="float: right; font-size:16px;"><?php echo  esc_attr(@round((@$desktop/$totalPlay*100))); ?>% Are Desktop Devices</b></td>
                                        </tr>

                                   </table>
                                   <ul>
                                    <?php
                                        $getBr = $wpdb->prepare("select * from $agents where video_id='%d",[$vid]);
                                        $obj = $wpdb->get_results($getBr,ARRAY_A);
                                        if(count($obj)>0){
                                            foreach($obj as $rec){
                                                $ic = @round((@$rec['plays']/$totalPlay*100));
                                            ?>
                                            <li><b><?php echo esc_attr($rec['user_agent']); ?></b>&nbsp;&nbsp;&nbsp; <?php echo esc_attr($ic); ?> %</li>
                                            <?php
                                            }
                                        }
                                    ?>
                                   </ul>
                        	   </div>
                       	    </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                <div class="postbox " id="formatdiv">
                        <div title="Click to toggle" class="handlediv"><br></div>
                        <h3 style="background-color: #E9E9E9; margin-top: 0px; padding-top: 6px; color:#001847;" class="hndle ui-sortable-handle"><span>Break Down Completed Plays Video By Location</span></h3>
                            <div class="inside">
                                <div id="post-formats-select">
                                <div id="donut-example"></div>
                                </div>
                            </div>
                        </div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                <div class="postbox " id="formatdiv">
                        <div title="Click to toggle" class="handlediv"><br></div>
                        <h3 style="background-color: #E9E9E9; margin-top: 0px; padding-top: 6px; color:#001847;" class="hndle ui-sortable-handle"><span>Video Plays Coming From?</span></h3>
                            <div class="inside">
                                <div id="post-formats-select">
                                <div id="regions_div" style="height: 350px;">
                                </div>
                            </div>
                        </div>
                        </div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                     <div class="postbox " id="formatdiv">
                        <div title="Click to toggle" class="handlediv"><br></div>
                        <h3 style="background-color: #E9E9E9; margin-top: 0px; padding-top: 6px; color:#001847;" class="hndle ui-sortable-handle"><span>Video Dropped Off Analytics</span></h3>
                            <div class="inside">
                                <div id="post-formats-select">
                                    <table class="wp-list-table widefat fixed striped posts">
                                        <thead>
                                            <tr>
                                                <td><b>Number Of Persons</b></td>
                                                <td><b>Average </b></b></td>
                                                <td><b>Duration</b></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    <?php
                                        $get_dr = $wpdb->prepare("SELECT drop_off, count(id) as total,video_time FROM $dropOf  WHERE video_id=%d and video_time!='0' group by drop_off",[$vid]);
                                        $all_an = $wpdb->get_results($get_dr,ARRAY_A);
                                        if(count($all_an)>0){
                                            asort($all_an);
                                            $count = count($all_an);
                                            
                                            foreach($all_an as $row){
                                                $hours = floor($row['drop_off'] / 3600);
                                                $minutes = floor(($row['drop_off'] / 60) % 60);
                                                $seconds = $row['drop_off'] % 60;
                                                
                                                $t = round(($row['drop_off']/$row['video_time'])*100);
                                                //$t = $row['video_time'];
                                                ?>
                                                    <tr>
                                                      <td><b><?php echo esc_attr($row['total']);
                                                        
                                                         ?></b></td>
                                                        <td style="padding-right: 4%;">
                                                        	<div class="charts">
                                                            <div class="charts__chart chart--p<?php echo esc_attr($t); ?> chart--sm" data-percent></div>
                                                            </div>
                                                           
                                                        </td>
                                                      
                                                        <td><?php echo esc_attr("$hours:$minutes:$seconds"); ?></td>
                                                    </tr>
                                                <?php
                                            }
                                        }else{
                                            ?>
                                                <tr>
                                                    <th><center><b>No Record Found...</b></center></th>
                                                </tr>
                                            <?php
                                        }
                                        
                                    ?>
                                    </tbody>
                                    </table>
                                    
                                </div>
                            </div>
                        </div>
                        </div>
                </td>
            </tr>
        </table>
<?php
    $analyticsJS = "
    google.load('visualization', '1', {packages:['geochart']});
      google.setOnLoadCallback(drawRegionsMap);
      function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable(". $n_arrs . ");
        var options = {};
        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
        chart.draw(data, options);
      }

    jQuery(document).ready(function(){
        app.timer(); 
        Morris.Donut({
     element: 'donut-example',
     data: ".$data_donut . ",
     colors: ['#0b62a4','#D58665','#37619d','#A87D8E','#2D619C','#2D9C2F']
   });
   var day_data = ". $data. ";
   var tday_data = [
    {'period': '2012-10-01', 'licensed': 3407, 'sorned': 660},
    {'period': '2012-09-30', 'licensed': 3351, 'sorned': 629},
    {'period': '2012-09-29', 'licensed': 3269, 'sorned': 618},
    {'period': '2012-09-20', 'licensed': 3246, 'sorned': 661},
    {'period': '2012-09-19', 'licensed': 3257, 'sorned': 667},
    {'period': '2012-09-18', 'licensed': 3248, 'sorned': 627},
    {'period': '2012-09-17', 'licensed': 3171, 'sorned': 660},
    {'period': '2012-09-16', 'licensed': 3171, 'sorned': 676},
    {'period': '2012-09-15', 'licensed': 3201, 'sorned': 656},
    {'period': '2012-09-10', 'licensed': 3215, 'sorned': 622}
  ];
  Morris.Line({
    element: 'graph',
    data: day_data,
    xkey: 'period',
    ykeys: ['licensed', 'sorned'],
    labels: ['Total Plays', 'Complete Plays']
  });
  });
";
wp_add_inline_script('svms-scripts', $analyticsJS);
?>