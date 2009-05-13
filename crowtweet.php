<?php
include("./feed/rss_fetch.inc");

function quote_smart($value) {
	// Quote if not integer
	if (!is_numeric($value)) {
		$value = "'" . mysql_real_escape_string($value) . "'";
	}
	return $value;
}

// include('rsstwitparse.php');


$twitter_url = 'http://twitter.com/statuses/update.xml';
$befriend_url = 'http://twitter.com/friendships/create/';
$userinfo_url = 'http://twitter.com/users/show/';
$obsdatetime = gmdate('YmdHis', time());

include("./details.txt");
$mysqllink = mysql_connect($addy, $user, $pass) or die("Could not connect: " . mysql_error());
mysql_select_db($database, $mysqllink) or die ("Can\'t use crows : " . mysql_error());

$q="SELECT twitterid FROM icrows ORDER BY twitterid DESC LIMIT 1";
$doGet=mysql_query($q);

$result = mysql_fetch_array($doGet);
$since_id = htmlentities(strip_tags($result['twitterid']));


//$feed = "http://search.twitter.com/search.rss?q=%23seattlecrows&since_id=$since_id";
//$show_user = 'false';

$rss_url = "http://search.twitter.com/search.atom?q=seattlecrows&since_id=$since_id&rpp=100";
$rss = fetch_rss( $rss_url );

  	foreach ($rss->items as $item) {
		print_r($item); //echo("\n<br>");

  		$author = htmlentities(strip_tags($item['author_name']));
  		$title = htmlentities(strip_tags($item['title']));
		$link = htmlentities(strip_tags($item['link']));
		preg_match('/[0-9]{10,}/', $link, $thepost);
		preg_match('/^\w+\b/',$author,$postauthor);
		$theauthor = $postauthor[0];
		
		//if it's an auto-tweet from website then skip it; if it's a manual tweet or retweet then change the author to the original and follow them instead
		if ($theauthor == 'seattlecrows') {
			continue;
//		  if (preg_match('/\#seattlecrows/',$title)) {continue;}
//		  else if (preg_match('/^RT\s@(\w+)\b/',$title,$newauthor)) {continue;}
// 		      $theauthor = $newauthor[1];
//		  }
		}
		else if (preg_match('/\@seattlecrows/',$title)) {continue;}

		else {
		//retweet 
//		  preg_replace('/\#seattlecrows/','',$title);
		  $status = "RT @{$theauthor}: $title";
		  $curl_handle = curl_init();
		  curl_setopt($curl_handle,CURLOPT_URL,"$twitter_url");
		  curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
		  curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		  curl_setopt($curl_handle,CURLOPT_POST,1);
		  curl_setopt($curl_handle,CURLOPT_POSTFIELDS,"status=$status");
		  curl_setopt($curl_handle,CURLOPT_USERPWD,"$tweetuser:$tweetpass");
 		  $buffer = curl_exec($curl_handle);
// 		  print_r($curl_handle);
		  curl_close($curl_handle);		  
		}
		
		//follow user
		$follow_url = $befriend_url . $theauthor . '.xml';
		$showuser_url = $userinfo_url . $theauthor . '.xml';
		$curl_handle = curl_init();
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl_handle,CURLOPT_POST,true);
		curl_setopt($curl_handle,CURLOPT_USERPWD,"$tweetuser:$tweetpass");
		curl_setopt($curl_handle,CURLOPT_URL,"$follow_url");
		$buffer = curl_exec($curl_handle);
		curl_setopt($curl_handle,CURLOPT_URL,"$showuser_url");
		$userinfo = curl_exec($curl_handle);
		curl_close($curl_handle);
		
		//get user's location
		$location='';
		preg_match('$\<location\>(.*)\<\/location\>$',$userinfo,$location);
//		echo("userinfo: $userinfo\n<br>location: $location[1]\n<br>");
		
  		//insert into database
		$author_pic = htmlentities(strip_tags($item['link_image']));
		//echo "status: $status\n<br>title: $title\n<br>llink: $link\n<br>thepost: $thepost[0]\n<br>author: $author\n<br>theauthor: $theauthor[0]\n<br>";
		$notes = quote_smart($title);
		$obstype = quote_smart('twitter');
		$obsemail = quote_smart($theauthor);
		$twitterid = $thepost[0]; 
//		echo "thepost: $thepost[0]";
		$twitterplace = quote_smart($location[1]);
//		echo "$twitterplace";
//		$authoricon = quote_smart($author_pic);
		if ($postauthor[0] == 'seattlecrows') {$link = "http://twitter.com/" . $theauthor;}
		else {$link = "http://twitter.com/" . $theauthor . "/status/" . $twitterid;}
		$safelink = quote_smart($link);
		$sql = sprintf("INSERT INTO icrows (id, twitterid, obsdatetime, obstype, obsemail, obsnotes, twitterplace, link) VALUES ('',%s,%s,%s,%s,%s,%s,%s)", $twitterid, $obsdatetime,$obstype,$obsemail,$notes, $twitterplace, $safelink);
		echo("$sql" . "\n<br>");
 		$result = mysql_query($sql, $mysqllink);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
			}
		print_r($result);

}

		include("./crowdbase.php");  //update rss feeds

?>

