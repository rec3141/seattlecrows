<?php


include("details.txt");
$link = mysql_connect($addy, $user, $pass) or die("Could not connect: " . mysql_error());

mysql_select_db($database,$link) or die ("Can\'t use crows : " . mysql_error());

//reinitialize
// if (isset($_GET['timespan'])) {$timespan = $_GET['timespan'];}
// else {$timespan = 9999;}

$timespan=9999;
$crowdata = "./crowdata.js";
$fhdata = fopen($crowdata, 'w');
fclose($fhdata);

$fhdata = fopen($crowdata, 'a');
fwrite($fhdata, "var crowdata = [");

  $sql_llda = sprintf('SELECT DISTINCT obslat,obslng,UNIX_TIMESTAMP(obsdatetime) AS obsdatetime,obsdir,obsnum,obsnotes,obstype,bandur,bandlr,bandul,bandll,bandother,banddoing,link,flickrid,flickrowner,photolink FROM icrows WHERE obsdatetime >= DATE_SUB(CURDATE(), INTERVAL %u DAY) GROUP BY obsdatetime,obslat,obslng', $timespan);
  global $return_llda;
  $return_llda = mysql_query($sql_llda,$link);

  while ($row_llda = mysql_fetch_array($return_llda)) {

	//DON'T CHANGE THE ORDER OF THESE
	list($obslat, $obslng, $obsdatetime, $obsdir, $obsnum, $obsnotes, $obstype, $bandur, $bandlr, $bandul, $bandll, $bandother, $banddoing, $obslink, $flickrid, $flickrowner,$photolink) = $row_llda;	
	$obsnotes = mysql_real_escape_string($obsnotes);

	// skip if there is no geodata
	if (($obslat == 0) && ($obslng == 0)) {continue;}
	
	//make sure all necessary icons have been created
	$icondir = "./img/";
	if (!file_exists("$icondir"."icon_banded_$bandul-$bandur-$bandlr-$bandll.png")) {
		shell_exec("composite $icondir$bandul.png -rotate 270 $icondir$bandur.png $icondir$bandul-$bandur.png");
		shell_exec("composite $icondir$bandul-$bandur.png -rotate 270 $icondir$bandlr.png $icondir$bandul-$bandur-$bandlr.png");
		shell_exec("composite $icondir$bandul-$bandur-$bandlr.png -rotate 270 $icondir$bandll.png $icondir"."icon_banded_$bandul-$bandur-$bandlr-$bandll.png");
	}
	
	$obsdatetime_tran = strftime("%Y-%m-%d",$obsdatetime);

	$datastring = "\n{
	\"type\": \"$obstype\",
	\"position\": [\"$obslat\", \"$obslng\"],
	\"datetime\": \"$obsdatetime_tran\",
	\"direction\": \"$obsdir\",
	\"number\": \"$obsnum\",
	\"notes\": \"$obsnotes\",
	\"link\": \"$obslink\",
	\"photoid\": \"$flickrid\",
	\"flickrowner\": \"$flickrowner\",
	\"photolink\": \"$photolink\",
	\"bands\": [\"$bandul\", \"$bandur\", \"$bandlr\", \"$bandll\"],
	\"bandother\": \"$bandother\",
	\"activity\": \"$banddoing\"},\n";

	fwrite($fhdata, $datastring);
	unset($value); // break the reference with the last element
  }

fwrite($fhdata, "]\n");
fclose($fhdata);
//echo("crowdata ok\n");


////////////////////////////////////////
////////////////////////////////////////
////////////////////////////////////////

$crowhistory = "./crowhistory.rss";
$fhhistory = fopen($crowhistory, 'w');
fclose($fhhistory);

$nowtime = strftime("%a, %d %b %Y %T %Z", time());
$historytimespan = 9999;
$q=sprintf("SELECT id,obslat,obslng,obstype,obsnotes,twitterid,twitterplace,obsemail,link,flickrid,UNIX_TIMESTAMP(obsdatetime) as obsdatetime FROM icrows WHERE obsdatetime >= DATE_SUB(CURDATE(), INTERVAL %u DAY) ORDER BY obsdatetime DESC",$historytimespan);

$doGet=mysql_query($q);

$fhhistory = fopen($crowhistory, 'w');
fwrite($fhhistory, join(
	array(
	"<?xml version='1.0' encoding='UTF-8'?>\n",
	"<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom'>\n",
	"<channel>\n",
	"<atom:link href='http://depts.washington.edu/uwcrows/crowhistory.rss' rel='self' type='application/rss+xml' />\n",
	"<title>Seattle Crow Map -- updated $nowtime</title>\n",
	"<description>Seattle Crow Survey and Mapping</description>\n",
	"<link>http://depts.washington.edu/uwcrows/crowhistory.rss</link>\n")));

//mysql_data_seek( $doGet );
while($result = mysql_fetch_array($doGet)){
	
if ($result['obstype'] == 'twitter') {
fwrite($fhhistory, join(
    array(
    "<item>\n",
    "<title>",
    htmlentities(strip_tags($result['obstype'])),
    "</title>\n",
    "<description>",
    utf8_encode(htmlspecialchars($result['obsnotes'])),
    "</description>",
    "<link>",$result['link'],"</link>\n",   
    "<pubDate>",
    strftime("%a, %d %b %Y %T %Z", $result['obsdatetime']),
    "</pubDate>\n",
    "<author>",
    htmlentities(strip_tags($result['obsemail'])),
    " cawed from ",
    htmlentities(strip_tags($result['twitterplace'])),
    "</author>\n",
    "</item>\n")));
	}

else if ($result['obstype'] == 'flickr') {
fwrite($fhhistory, join(
    array(
    "<item>\n",
    "<title>",
    htmlentities(strip_tags($result['obstype'])),
    "</title>\n",
    "<description>",
    utf8_encode(htmlspecialchars($result['obsnotes'])),
    "</description>\n",
    "<link>",
    $result['link'],
    "</link>\n",   
    "<pubDate>",
    strftime("%a, %d %b %Y %T %Z", $result['obsdatetime']),
    "</pubDate>\n",
    "<author>",
    htmlentities(strip_tags($result['obslat'])),
    ",",
    htmlentities(strip_tags($result['obslng'])),
    "</author>\n",
    "</item>\n")));
    }
    
    	// skip if there is no geodata
else if (($result['obslat'] == 0) && ($result['obslng'] == 0)) {continue;}

else {


fwrite($fhhistory, join(
    array(
    "<item>\n",
    "<title>",
    htmlentities(strip_tags($result['obstype'])),
    "</title>\n",
    "<description>",
    utf8_encode(htmlspecialchars($result['obsnotes'])),
    "</description>\n",
    "<link>http://depts.washington.edu/uwcrows/</link>\n",   
    "<pubDate>",
    strftime("%a, %d %b %Y %T %Z", $result['obsdatetime']),
    "</pubDate>\n",
    "<author>",
    htmlentities(strip_tags($result['obslat'])),
    ",",
    htmlentities(strip_tags($result['obslng'])),
    "</author>\n",
    "</item>\n")));
    }
}

fwrite($fhhistory, join(
	array(
	"</channel>\n",
	"</rss>\n")));
fclose($fhhistory);
