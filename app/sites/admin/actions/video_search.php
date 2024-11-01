<?php
$search = sanitize_text_field($_GET['search']);
$sql = '
SELECT
	*
FROM
	'.self::$table['video'].'
WHERE
	name LIKE "%'.addslashes($search).'%"
OR
	handle LIKE "%'.addslashes($search).'%"
OR
	mp4_url LIKE "%'.addslashes($search).'%"
OR
	youtube_url LIKE "%'.addslashes($search).'%"
OR
	webm_url LIKE "%'.addslashes($search).'%"
OR
	ogg_url LIKE "%'.addslashes($search).'%"
OR
     tags like "%'.addslashes($search).'%"
ORDER BY
	name';

$video_list = $wpdb->get_results($sql, ARRAY_A);

if (is_array($video_list) && count($video_list))
{
	foreach ($video_list as $video)
	{
		echo '<div class="vpp_search_result" onclick="vpp_insertVideo(\'[s3vpp id='.esc_attr($video['handle']).']\')">'.esc_attr($video['name']).'</div>';
	}
}
else
{
	echo '<div class="vpp_search_result" style="text-align: center; border: 0; padding: 50px 0;"><strong>No videos found</strong></div>';
}

exit();