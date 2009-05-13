<?php
/* 
RSS to Twitter v0.1
by paul stamatiou 
of http://paulstamatiou.com
based on code from
http://morethanseven.net/posts/posting-to-twitter-using-php
*/
include('rsstwitparse.php');
include('details.txt');

$twitter_url = 'http://twitter.com/statuses/update.xml';
$feed = "http://depts.washington.edu/uwcrows/crowhistory.rss"; //the feed you want to micro-syndicate
$rss = new lastRSS;
if ($rs = $rss->get($feed)){ 
    $title = $rs[items][0][title]; //only get last submitted post to tweet
    $description = $rs[items][0][description];
	$url = $rs[items][0][link];
} else { die('Error: RSS file not found, dude.'); }
// $tiny_url =  file_get_contents("http://tinyurl.com/api-create.php?url=" . $url);
$status = $description;
$curl_handle = curl_init();
curl_setopt($curl_handle,CURLOPT_URL,"$twitter_url");
curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
curl_setopt($curl_handle,CURLOPT_POST,1);
curl_setopt($curl_handle,CURLOPT_POSTFIELDS,"status=$status");
curl_setopt($curl_handle,CURLOPT_USERPWD,"$tweetname:$tweetpass");
$buffer = curl_exec($curl_handle);
curl_close($curl_handle);
if (empty($buffer)){echo '<br/>message';}else{echo '<br/>success';}?>