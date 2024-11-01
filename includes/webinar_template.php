<?php



$webinar['description'] = nl2br($webinar['description']);



$ical = array(

'id'			=> (int)$webinar['webinarKey'],

'dtstart'		=> str_replace(':', '', str_replace('-', '', $time_info['startTime'])),

'dtend'			=> str_replace(':', '', str_replace('-', '', $time_info['endTime'])),

'subject'		=> 'Webinar - '.$webinar['subject'],

'description'	=> strip_tags(str_replace("\n", ' ', $webinar['description'])).' '.$register_link

);



$ical_url = 'http://mynams.com/?app=mn_global&ical='.urlencode(serialize($ical));

$ical_link = ' <a href="'.$ical_url.'" target="_blank"><img src="'.esc_url(Mng_Base::$plugin_url).'/includes/icons/ical_feed.gif" width="36" height="14" alt="iCal" /></a>';



$join_link = '<a href="https://www2.gotomeeting.com/join/'.esc_attr((int)$webinar['webinarKey']).'" target="_blank">Join webinar now</a>';



$template =<<<END

<br />

<h2>Webinar: {$webinar['subject']} {$ical_link}</h2>



<p>{$webinar['description']}</p>



<h3>&gt;&gt; {$join_link} &lt;&lt;</h3>



<h4>Scheduled Times:</h4>



<ul>

END;



foreach ($webinar['times'] as $time_info)

{

	if (strtotime($time_info['startTime']) > time())

	{

		$start	= date('l, F j, Y -- g:ia', strtotime($time_info['startTime']));

		$end	= date('g:ia T', strtotime($time_info['endTime']));



		//https://www2.gotomeeting.com/join/804326154/106236601

		$register_link = 'https://www2.gotomeeting.com/register/'.(int)$webinar['webinarKey'];



		$template .= '<li>'.esc_attr($start).' to '.esc_attr($end).' <a href="'.esc_url($register_link).'" target="_blank">...click to register</a></li>';

	}

}



$template .=<<<END

</ul>

END;



//$template .=<<<END

//<h4>Other Upcoming Webinars</h4>

//<ul>

//END;

//

//foreach ($upcoming_webinars as $uc_webinar)

//{

//	if ((int)$webinar['webinarKey'] != (int)$uc_webinar['webinarKey'])

//	{

//		$template .= '<li>'.$uc_webinar['subject'].'<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

//

//		foreach ($uc_webinar['times'] as $time_info)

//		{

//			if (strtotime($time_info['startTime']) > time())

//			{

//				$start	= date('l, F j, Y -- g:ia', strtotime($time_info['startTime']));

//				$end	= date('g:ia T', strtotime($time_info['endTime']));

//

//				$register_link = 'https://www2.gotomeeting.com/register/'.(int)$webinar['webinarKey'];

//

//				$ical = array(

//				'id'			=> (int)$uc_webinar['webinarKey'],

//				'dtstart'		=> str_replace(':', '', str_replace('-', '', $time_info['startTime'])),

//				'dtend'			=> str_replace(':', '', str_replace('-', '', $time_info['endTime'])),

//				'subject'		=> 'Webinar - '.$uc_webinar['subject'],

//				'description'	=> strip_tags(str_replace("\n", ' ', $uc_webinar['description'])).' '.$register_link

//				);

//

//				$ical_url = 'http://mynams.com/?app=mn_global&ical='.urlencode(serialize($ical));

//

//				$ical_link = ' <a href="'.$ical_url.'" target="_blank"><img src="'.Mng_Base::$plugin_url.'/includes/icons/ical_feed.gif" width="36" height="14" alt="iCal" /></a>';

//

//				$template .= $start.' to '.$end.' '.$ical_link;

//				break;

//			}

//		}

//

//		$template .= '</li>';

//	}

//}

//

//$template .=<<<END

//</ul>

//END;



$template .=<<<END

<br /><br />

END;



?>