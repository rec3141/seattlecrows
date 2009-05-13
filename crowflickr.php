<?php

require_once("./phpFlickr/phpFlickr.php");

function quote_smart($value) {
	// Quote if not integer
	if (!is_numeric($value)) {
		$value = "'" . mysql_real_escape_string($value) . "'";
	}
	return $value;
}

function savePhoto($remoteImage, $isbn) {
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $remoteImage);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 2);
    $fileContents = curl_exec($ch);
    curl_close($ch);
    $newImg = imagecreatefromstring($fileContents);
    return imagejpeg($newImg, "./img/flickr/{$isbn}.jpg",100);
}


//connect to database
include("./details.txt");
$mysqllink = mysql_connect($addy, $user, $pass) or die("Could not connect: " . mysql_error());
mysql_select_db("crows", $mysqllink) or die ("Can\'t use crows : " . mysql_error());

$q="SELECT flickrid FROM icrows WHERE flickrid >0 ORDER BY flickrid DESC";
$doGet=mysql_query($q);

for($i = 0; $results[$i] = mysql_fetch_assoc($doGet); $i++);
//array_pop($results);

$allflickrids = array();
for($i=0;$allflickrids[] = $results[$i]['flickrid'];$i++);

print_r($allflickrids);

//get most recent in database to avoid duplicating
//$since_id = htmlentities(strip_tags($result['flickrid']));
//$since_id = $allflickrids[0];
//echo $since_id;

//get new phpflickr instance
$f = new phpFlickr($flickrkey);

$query = array(
//	"text"=>"crow",
//	"place_id"=> "xbp0.jWbBZWDASue", //seattle
//	"place_id"=> "8LwX6FSbApgD.7YC", //washington state
	"per_page"=> "500",
//	"license"=> "1,2,3,4,5,6,7",
	"sort"=> "date-posted-desc",
	"group_id"=>"1011166@N22",
	"has_geo"=>"true",
	);

$search = $f->photos_search($query);

echo "f:"; print_r($search);

foreach ($search['photo'] as $photo) {

$photoid_match = $photo['id'];
//echo($photoid_match);

//	if ($photo['id'] <= $since_id) continue;
	$idmatches = preg_grep("#$photoid_match#",$allflickrids);
//	print_r($idmatches);
	if ( sizeof($idmatches) > 0) continue;
	
    $owner = $f->people_getInfo($photo['owner']);
    $info = $f->photos_getInfo($photo['id']);
    $geo = $f->photos_geo_getLocation($photo['id']);
//	print_r($info);
	$obslat = $geo['location']['latitude'];
	$obslng = $geo['location']['longitude'];
	
	$flickrid = quote_smart(htmlentities(strip_tags($photo['id'])));
	$obsdatetime = quote_smart(htmlentities(strip_tags($info['dates']['taken'])));
	$obstype = quote_smart('flickr');
	$obsemail = quote_smart(htmlentities(strip_tags($owner['username'])));
	$obsnotes = quote_smart(htmlentities(strip_tags($photo['title'])));
	$link_a = "http://www.flickr.com/photos/" . $photo['owner'] . "/" . $photo['id'];
	$link = quote_smart($link_a);

	$photolink_d = 'http://farm' . $info['farm'] . '.static.flickr.com/' . $info['server'] . '/' . $info['id'] . '_' . $info['secret'] . '_m.jpg';
	savePhoto($photolink_d, $photo['id']);
	$photolink = quote_smart($photolink_d);

	$insert = sprintf("INSERT INTO icrows (id, flickrid, obsdatetime, obstype, flickrowner, obsnotes, obslat, obslng, link, photolink) VALUES ('',%s,%s,%s,%s,%s,%s,%s,%s,%s)", $flickrid, $obsdatetime,$obstype,$obsemail,$obsnotes, $obslat, $obslng, $link, $photolink);
//	echo("$insert" . "\n<br>");
	$result = mysql_query($insert, $mysqllink);
	echo("inserted $flickrid ? $result\n<br>");
	}

?>
