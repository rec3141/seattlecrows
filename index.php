
<?php
include("header_doctype.txt");
Header('Cache-Control: no-cache');
Header('Pragma: no-cache');

//error_reporting(0);
?>

<html>
<head>

<title>Seattle Crows -- View and Submit Sightings</title>

<link rel="stylesheet" href="style.css" type="text/css" media="screen">


<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=YOUR_GMAP_KEY_HERE
      type="text/javascript"></script>
<script src="http://gmaps-utility-library.googlecode.com/svn/trunk/markermanager/release/src/markermanager.js" type="text/javascript"></script>

<!--<script src="./date.js">-->

<!-- initial loadup of data, remainder will be loaded by reloadData() -->

<script src="./crowdata.js"></script>


<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("YOUR_GOOGLE_ANALYTICS_KEY_HERE");
pageTracker._trackPageview();
} catch(err) {}</script>

<!--
######################################################
############################################################ IFRAME NONSENSE
#################################################
-->

<script type="text/javascript">
var IFrameObj; // our IFrame object
function getCrowData() {
  if (!document.createElement) {return true};
  var IFrameDoc;
  var URL = './crowdbase.php'
//   =' + document.viewform.timespan.value;
  if (!IFrameObj && document.createElement) {
    // create the IFrame and assign a reference to the
    // object to our global variable IFrameObj.
    // this will only happen the first time 
    // callToServer() is called
   try {
      var tempIFrame=document.createElement('iframe');
      tempIFrame.setAttribute('id','RSIFrame');
      tempIFrame.style.border='0px';
      tempIFrame.style.width='0px';
      tempIFrame.style.height='0px';
      IFrameObj = document.body.appendChild(tempIFrame);
      
      if (document.frames) {
        // this is for IE5 Mac, because it will only
        // allow access to the document object
        // of the IFrame if we access it through
        // the document.frames array
        IFrameObj = document.frames['RSIFrame'];
      }
    } catch(exception) {
      // This is for IE5 PC, which does not allow dynamic creation
      // and manipulation of an iframe object. Instead, we'll fake
      // it up by creating our own objects.
      iframeHTML='\<iframe id="RSIFrame" style="';
      iframeHTML+='border:0px;';
      iframeHTML+='width:0px;';
      iframeHTML+='height:0px;';
      iframeHTML+='"><\/iframe>';
      document.body.innerHTML+=iframeHTML;
      IFrameObj = new Object();
      IFrameObj.document = new Object();
      IFrameObj.document.location = new Object();
      IFrameObj.document.location.iframe = document.getElementById('RSIFrame');
      IFrameObj.document.location.replace = function(location) {
        this.iframe.src = location;
      }
    }
  }
  
  if (navigator.userAgent.indexOf('Gecko') !=-1 && !IFrameObj.contentDocument) {
    // we have to give NS6 a fraction of a second
    // to recognize the new IFrame
    setTimeout('getCrowData()',10);
    return false;
  }
  
  if (IFrameObj.contentDocument) {
    // For NS6
    IFrameDoc = IFrameObj.contentDocument; 
  } else if (IFrameObj.contentWindow) {
    // For IE5.5 and IE6
    IFrameDoc = IFrameObj.contentWindow.document;
  } else if (IFrameObj.document) {
    // For IE5
    IFrameDoc = IFrameObj.document;
  } else {
    return true;
  }
      
  IFrameDoc.location.replace(URL);
  return false;
}


</script>

<!--
######################################################
############################################################ LAYOUT JS
#################################################
-->

<script type="text/javascript">

function doClear(theText) {
    if (theText.value == theText.defaultValue) {
        theText.value = ""
    }
}

function gotoForm() {
    document.getElementById('formDiv').style.display = 'block';
    document.getElementById('textDiv').style.display = 'none';
    document.getElementById('aboutDiv').style.display = 'none';
    document.getElementById('subDiv').style.display = 'none';    
	
    //AddMarkerListener = 
    AddMarkerListener = GEvent.addListener(map, 'click', 
        function(overlay,point){
            if(overlay) {
            	if (typeof submarker != "undefined") {
            		map.removeOverlay(submarker);
            		document.dataform.obslat.value = "";
        			document.dataform.obslng.value = "";
            		};
        		}
            else if(point) {
                document.dataform.obslat.value = point.y;
                document.dataform.obslng.value = point.x;
                if (typeof submarker != "undefined") {
                	map.removeOverlay(submarker)
                };
                submarker = new GMarker(point);
                map.addOverlay(submarker);
            }
        });

}

function gotoView() {
    document.getElementById('formDiv').style.display = 'none';
    document.getElementById('textDiv').style.display = 'block';
    document.getElementById('aboutDiv').style.display = 'none';
    document.getElementById('subDiv').style.display = 'none';

    if (typeof AddMarkerListener != "undefined") {
        GEvent.removeListener(AddMarkerListener);
    };
}

function gotoAbout() {
    document.getElementById('formDiv').style.display = 'none';
    document.getElementById('textDiv').style.display = 'none';
    document.getElementById('aboutDiv').style.display = 'block';
    document.getElementById('subDiv').style.display = 'none';
    
    if (typeof AddMarkerListener != "undefined") {
        GEvent.removeListener(AddMarkerListener);
    };
}

function gotoSubmitted() {
    document.getElementById('formDiv').style.display = 'none';
    document.getElementById('textDiv').style.display = 'none';
    document.getElementById('aboutDiv').style.display = 'none';
    document.getElementById('subDiv').style.display = 'block';
}

function checkOpt() {
    var optval = document.dataform.obstype.options[document.dataform.obstype.selectedIndex].value;

    if (optval == "flyover") {
        document.getElementById('migration').style.display = 'block';
        document.getElementById('banded').style.display = 'none';
    } else if (optval == "banded") {
        document.getElementById('banded').style.display = 'block';
        document.getElementById('migration').style.display = 'none';
    } else {
        document.getElementById('migration').style.display = 'none';
        document.getElementById('banded').style.display = 'none';
    }
}

// function checkTimeOpt() {
//     var optval = document.viewform.timespan.options[document.viewform.timespan.selectedIndex].value;
// //document.viewform.timetext.value=document.viewform.timespan.value;
// reloadData();LoadMap();
// reloadData();LoadMap();
// }

Array.find = function(ary, element) {
    for (var i = 0; i < ary.length; i++) {
        if (ary[i] == element) {
            return i;
        }
    }
    return - 1;
}

function checkAll(field) {
    var checkboxes = ['story', 'banded', 'roost', 'am_flyover', 'pm_flyover', 'nest'];
    for (var i = 0; i < checkboxes.length; i++) {
        document.viewform.elements[checkboxes[i]].checked++;
    }
}

function include(filename)
{
	var head = document.getElementsByTagName('head')[0];
	
	script = document.createElement('script');
	script.src = filename;
	script.type = 'text/javascript';
	
	head.appendChild(script)
}

function getHistory() {
document.getElementById('feedall').style.display = "";
document.getElementById('tweetall').style.display = "";
}


</script>



<!--
######################################################
############################################################ GOOGLE MAP JS
#################################################
-->


<script type="text/javascript">
//<![CDATA[

var map;
var mgr;
var allmarkers = [];
var boxes;
var submarker;

	
function LoadMap() {
    if (GBrowserIsCompatible()) {

        map = new GMap2(document.getElementById("map"));
        map.setCenter(new GLatLng(47.6437, -122.2915));
        map.setZoom(12);
        map.addControl(new GLargeMapControl());
        map.addControl(new GMapTypeControl());
        mgr = new MarkerManager(map);
        checkAll(document.viewform.typelist)
        boxes = ['story', 'banded', 'roost', 'am_flyover', 'pm_flyover', 'nest', 'flickr'];
        var markerList = setupMarkerArray();

        mgr.addMarkers(allmarkers, 0);
        mgr.refresh();
    }
} // end LoadMap function


function zoomMap(lat,lng,zoom){
        map.setCenter(new GLatLng(lat,lng));
        map.setZoom(zoom);
}

function ReloadMap() {
    mgr.clearMarkers();
    boxes.length = 0;
    var checkboxes = ['story', 'banded', 'roost', 'am_flyover', 'pm_flyover', 'nest', 'flickr'];
    for (var i = 0; i < checkboxes.length; i++) {
        if (document.viewform.elements[checkboxes[i]].checked == 1) {
            boxes.push(checkboxes[i])
        }
        else {}
    }
    mgr = new MarkerManager(map);
    var markerList = setupMarkerArray();
    mgr.addMarkers(allmarkers, 0);
    mgr.refresh();
}

// function to create a marker whose info window displays the given html
function createMarker(markerpoint, markerhtml, markericon) {
    var marker = new GMarker(markerpoint, markericon);
    GEvent.addListener(marker, 'click',
    function() {
        marker.openInfoWindowHtml(markerhtml);
    });
    return marker;
};

function setupMarkerArray() {
    allmarkers.length = 0;
    for (var i in crowdata) {
        var layer = crowdata[i];
        var obstype = layer["type"];
        //find obstype in list we want
        if (Array.find(boxes, obstype) > -1) {
            var position = new GLatLng(layer["position"][0], layer["position"][1]);
            var direction = layer["direction"];
            var bands = layer["bands"];
	    var linkurl = layer["link"];
	    var photoid = layer["photoid"];
	    var photolink = layer["photolink"];
	    var flickrowner = layer["flickrowner"];
            var icon = getIcon(obstype, direction, bands);
			var datetime = layer["datetime"];

            if (obstype == "am_flyover" || obstype == "pm_flyover") {
                var notes = "date: " + datetime + "<br>notes:" + layer["notes"] + "<br>number: 10^" + layer["number"];
            }
            else if (obstype == "banded") {
                var notes = "date: " + datetime + "<br>notes: " + layer["notes"] + "<br>Activity: " + layer["activity"];
            }
	    else if (obstype == "flickr") {
		var notes = "<div style='width:250px;height:265px;vertical-align:middle;text-align:middle;'><a href='" + linkurl + "' target='_blank'><img src='" + photolink + "'><br>by " + flickrowner + "<\/a><\/div>"; //'./img/flickr/" + photoid + ".jpg'
	    }
            else {
                var notes = "date: " + datetime + "<br>notes: " + layer["notes"];
            }
            // Show this marker's index in the info window when it is clicked
            var html = "<div style='max-width:400px;max-height:280px; overflow: auto;padding-right:3px'>" + notes + "<\/div>";
            var marker = createMarker(position, html, icon);
            allmarkers.push(marker); // push into generic array
        }
        else {}
    }
}

// set up icons
function getIcon(ctype, cdir, cbands) {
    var bandlist;
    var icon = null;
    icon = new GIcon();
    icon.iconSize = new GSize(18, 18); //minimum 12pt
    icon.iconAnchor = new GPoint(8, 8);
    icon.infoWindowAnchor = new GPoint(16, 8);
    icon.shadow = "";
    icon.shadowSize = new GSize(0, 0);
    if (ctype == "am_flyover" || ctype == "pm_flyover") {
        bandlist = "";
        icontype = cdir;
    }
    else if (ctype == "banded") {
        bandlist = "_" + cbands[0] + "-" + cbands[1] + "-" + cbands[2] + "-" + cbands[3];
        icontype = ctype;
    }
    else {
        bandlist = "";
        icontype = ctype;
    }

    icon.image = './img/icon_' + icontype.toLowerCase() + bandlist + '.png';
//    document.write(icontype + " " + cdir + " " + cbands + " " + icon.image + "\n<br>");
    return icon;
}

//]]>
</script>


<!--
######################################################
############################################################ PHP JS
#################################################
-->


<script type="text/javascript">
function reloadData() {
	getCrowData();
	include("./crowdata.js"); //can't call until header loaded?
}

function onReload() {
<?php

$today = getdate();

//safety function
function quote_smart($value) {
	// Quote if not integer
	if (!is_numeric($value)) {
		$value = "'" . mysql_real_escape_string($value) . "'";
	}
	return $value;
}

if (isset($_POST['formsub'])) {
	echo "gotoSubmitted();\n";
	
	if (quote_smart($_POST['security']) == (quote_smart($_POST['rand1']) + quote_smart($_POST['rand2']))) {

		if (checkdate($_POST['obsmonth'], $_POST['obsday'], $_POST['obsyear'])) {

			echo "getCrowData();\n";

			$obstimestamp = mktime($today['hours'], sprintf('%02s', $today['minutes']), 0, quote_smart($_POST['obsmonth']), quote_smart($_POST['obsday']), quote_smart($_POST['obsyear']));
			$obsdatetime = gmdate('YmdHis', $obstimestamp);

			include("details.txt");
			$link = mysql_connect($addy, $user, $pass) or die("Could not connect: " . mysql_error());
			mysql_select_db($database, $link) or die ("Can\'t use crows : " . mysql_error());

//safety function quote_smart implemented in crowdata2.php

			if ($_POST['obstype'] == 'flyover') {
				if ($_POST['acctime'] == 'morning') {$temptype = 'am_flyover';}
				else {$temptype = 'pm_flyover';}
			}
			else {$temptype = $_POST['obstype'];}

// 			if (isset($_POST['acctime'])) {$acctime = 'badtime';} else {$acctime = 'oktime';}
			if (isset($_POST['accdate'])) {$accdate = 'baddate';} else {$accdate = 'okdate';}

			$sql = sprintf("INSERT INTO icrows (id, obsdatetime, obslat, obslng, obstype, obsdir, obsnum, obsnotes, obsemail, bandur, bandlr, bandul, bandll, bandbl, bandbr, bandother, banddoing, accdate, acctime) VALUES ('',%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", $obsdatetime, quote_smart($_POST['obslat']), quote_smart($_POST['obslng']), quote_smart($temptype), quote_smart($_POST['obsdir']), quote_smart($_POST['obsnum']), quote_smart($_POST['obsnotes']), quote_smart($_POST['obsemail']), quote_smart($_POST['bandur']), quote_smart($_POST['bandlr']), quote_smart($_POST['bandul']), quote_smart($_POST['bandll']), quote_smart($_POST['bandbl']), quote_smart($_POST['bandbr']), quote_smart($_POST['bandother']), quote_smart($_POST['banddoing']), quote_smart($accdate), quote_smart($_POST['acctime']));
//echo($sql);
			$result = mysql_query($sql, $link);
//echo("result:$result");			
			include("./crowdbase.php");  //update rss feeds
			include("./rsstwit.php");  //post to twitter
		}
		else {$error = "Incorrect or missing date."; $result = 'ERROR';}

	}
	else {$error = "Wrong answer to security question."; $result = 'ERROR';}
}
?>
}
</script>


</head>

<body onload="LoadMap();onReload();" onunload="GUnload()" id="top">



<!--
######################################################
############################################################ BODY
#################################################
-->


<div id="header">
<ul style="margin:0;">
<li id="li_about" style="display: inline;padding-left: 3px;padding-right: 3px;border-right: 0px;">
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="gotoAbout();return false;">About</a></li>
<img src="./img/icon_e.png" alt="Directional arrow" height="16px">

<li style="display: inline;padding-left: 3px;padding-right: 3px;border-right: 0px;">
<a href="#rssref">Sightings List</a></li>
<img src="./img/icon_s.png" alt="Directional arrow" height="16px">

<li id="li_view" style="display: inline;padding-left: 3px;padding-right: 3px;border-right: 0px;" >
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="gotoView();return false;">Sightings Map</a></li>
<img src="./img/icon_s.png" alt="Directional arrow" height="16px">

 <li id="li_submit"style="display: inline;padding-left: 3px;padding-right: 3px;border-right: 0px;">
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="gotoForm();return false;">Submit New Sighting</a></li>
<img src="./img/icon_e.png" alt="Directional arrow" height="16px">

<li style="display: inline;padding-left: 3px;padding-right: 3px;border-right: 0px;">
<a href="./crowhelp.php" target="_blank">Instructions</a></li>
<img src="./img/icon_n.png" alt="Directional arrow" height="16px">

<li style="display: inline;padding-left: 3px;padding-right: 3px;border-right: 0px;">
<a href="http://twitter.com/seattlecrows" target="_blank">@seattlecrows</a></li>
<img src="./img/icon_n.png" alt="Directional arrow" height="16px">

<li style="display: inline;padding-left: 3px;padding-right: 3px;border-right: 0px;">
<a href="./crowhistory.rss">RSS <img src="./img/rss.png" height="16px" alt="RSS feed"></a></li>

</ul>
</div>

<div id="map"></div>




<!--
######################################################
############################################################ VIEW
#################################################
-->



<div id="textDiv" style="display:block">
<?php
if (isset($_POST['formsub'])) {
	if ($result == 'TRUE') {
		echo "<p class='bold'>Thank you for submitting your crow sighting!</p>";
		}
}
?>

<p>Vancouver BC Crow Cam courtesy of <a href="http://www.cajecreative.com/">http://www.cajecreative.com/</a>
<object id="utv_o_982836" height="200" width="250"  classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">
<param value="http://www.ustream.tv/flash/live/664666" name="movie" />
<param value="true" name="allowFullScreen" />
<param value="always" name="allowScriptAccess" />
<param value="transparent" name="wmode" />
<param value="viewcount=true&amp;autoplay=false&amp;brand=embed&amp;" name="flashvars" />
<embed name="utv_e_273070" id="utv_e_562590" flashvars="viewcount=true&amp;autoplay=false&amp;brand=embed&amp;" height="200" width="250" allowfullscreen="true" allowscriptaccess="always" wmode="transparent" src="http://www.ustream.tv/flash/live/664666" type="application/x-shockwave-flash" />
</object>

<form name="viewform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<p class="bold">Select the types of sightings to display:
<table>


<tr>
<td><img src="./img/icon_crow.png" alt="Crow icon indicates anecdotal reports or other data"></td>
<td><input type="checkbox" name="story" OnClick="ReloadMap();" CHECKED value="story">crow stories and anecdotes</td>
</tr>

<tr>
<td><img src="./img/icon_banded_orange-gray-green-yellow.png" alt="Ring icon indicates a banded crow; placement of colors identifies unique crows"></td>
<td><input type="checkbox" name="banded" OnClick="ReloadMap();" CHECKED value="banded">banded crows</td>
</tr>

<tr>
<td><img src="./img/icon_roost.png" alt="Tree icon indicates a nightly roost"></td>
<td><input type="checkbox" name="roost" OnClick="ReloadMap();" CHECKED value="roost">nighttime crow roosts</td>
</tr>

<tr>
<td><img src="./img/icon_n.png" alt="Directional arrow indicates flying direction during daily migration"></td>
<td><input type="checkbox" name="am_flyover" OnClick="ReloadMap();" CHECKED value="am_flyover">morning crow migrations</td>
</tr>

<tr>
<td><img src="./img/icon_s.png" alt="Directional arrow indicates flying direction during daily migration"></td>
<td><input type="checkbox" name="pm_flyover" OnClick="ReloadMap();" CHECKED value="pm_flyover">evening crow migrations</td>
</tr>

<tr>
<td><img src="./img/icon_nest.png" alt="Nest icon indicates a nest"></td>
<td><input type="checkbox" name="nest" OnClick="ReloadMap();" CHECKED value="nest" >crow nests</td>
</tr>

<tr>
<td><img src="./img/icon_flickr.png" alt="flickr icon indicates photo" width=30px></td>
<td><input type="checkbox" name="flickr" OnClick="ReloadMap();" CHECKED value="flickr">flickr photos</td>
</tr>



<tr>
<td></td>
<td><input type="button" onclick="LoadMap()" value="reset map"></td>
</tr>
<!--
<tr>
<td></td>
<td>
<p class="bold">Time span to show: &nbsp;&nbsp;<SELECT name="timespan" onchange="checkTimeOpt();">
<input type=hidden name=timespan value=9999>
<OPTION value="9999" SELECTED>all times</option>
<OPTION value="1">past day</option>
<OPTION value="7">past week</option>
<OPTION value="28">past month</option>
<OPTION value="365">past year</option>
</SELECT>

</td>
</tr>
-->
<tr>
<td></td>
<td><input type="button" onclick="reloadData();LoadMap();" value="reload map data">
<!-- <br>(may need to click more than once) -->
</td>
</tr>



</table>
</form>
<p class="bold">Zoom in to see more geographic detail.  Click on the markers to see information about each sighting.
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="zoomMap(47.6543,-122.3078, 15);return false;">Zoom to UW</a>

<p>Seattle Crows is now on <a href="http://flickr.com" target="_blank">Flickr</a>!  To see your crow pictures here, add them to the <a href="http://www.flickr.com/groups/seattlecrowproject/" target="_blank">Seattle Crow Project Group Pool</a> and make sure to <a href="http://blog.flickr.net/2006/08/28/great-shot-whered-you-take-that/" target="_blank">geotag</a> them with the location of the sighting! They'll be automagically added to the site.

<p><a href="http://twitter.com/seattlecrows">@seattlecrows</a> is now on <a href="http://twitter.com" target="_blank">Twitter</a>!  Just include the tag #seattlecrows in any twitter post about a crow sighting and it will be automagically updated onto the site.  

<p style="font-size:0.7em">Site verified to work on Linux/Kubuntu 8.10 (Firefox 3.0, Konqueror 4.1); MacOSX 10.5.5 (Firefox 2.0, Opera 9.6, Safara 3.1) and Windows XP (Firefox 2.0, IE7).  Does it work on your browser?  Please let me know.

</div>





<!--
###################################################### 
############################################################ SUBMIT
#################################################
-->



<div id="formDiv">

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="dataform">

<p class="bold">Location of sighting (click map)</p>
latitude: <INPUT TYPE="TEXT" NAME="obslat" value="">
<br>longitude: <INPUT TYPE="TEXT" NAME="obslng" value="">



<p class="bold">What are you reporting?
<select name="obstype" onfocus="checkOpt();" onblur="checkOpt();" onchange="checkOpt();">
<option value="story" SELECTED>Story/Anecdote/Other Observation</option>
<option value="banded">Banded Crow</option>
<option value="flyover">Crow Daily Migration</option>
<option value="roost">Crow Nighttime Roost</option>
<option value="nest">Crow Nest</option>
</select>


<div id="migration" style="display:none;">

<hr>
<p class="bold">Which direction were the crows flying?

<table border=1>
<tr>
<td><input type="radio" name="obsdir" value="nw">northwest</td>
<td><input type="radio" name="obsdir" value="n">north</td>
<td><input type="radio" name="obsdir" value="ne">northeast</td>
</tr>

<tr>
<td><input type="radio" name="obsdir" value="w">west</td>
<td><input type="radio" name="obsdir" value="xx" checked="yes">?</td>
<td><input type="radio" name="obsdir" value="e">east</td>
</tr>

<tr>
<td><input type="radio" name="obsdir" value="sw">southwest</td>
<td><input type="radio" name="obsdir" value="s">south</td>
<td><input type="radio" name="obsdir" value="se">southeast</td>
</tr>

</table>

<p class="bold">About how many crows were there?
<select name="obsnum">
<option value="-1" SELECTED>Don't Know</option>
<option value="0">About 1</option>
<option value="1">About 10</option>
<option value="2">About 100</option>
<option value="3">About 1000</option>
</select>
</div>


<div id="banded" style="display:none;">

<hr>
<p class="bold"><a href="./crowhelp.php#banded" target="_blank">
Special instructions for reporting a banded crow</a>

<p class="bold">What were the colors of the bands (from top to bottom) on each of the crow's legs?

<table>
<tr>
<td>Left</td>
<td>Right</td>
</tr>

<tr>
<td>
<SELECT NAME="bandul">
<OPTION value="vacant" SELECTED>none</option>
<OPTION class="band_red" value="red">red</option>
<OPTION class="band_orange" value="orange">orange</option>
<OPTION class="band_yellow" value="yellow">yellow</option>
<OPTION class="band_green" value="green">green</option>
<OPTION class="band_pblue" value="pblue">powder blue</option>
<OPTION class="band_lblue" value="lblue">light blue</option>
<OPTION class="band_dblue" value="dblue">dark blue</option>
<OPTION class="band_purple" value="purple">purple</option>
<OPTION class="band_black" value="black">black</option>
<OPTION class="band_white" value="white">white</option>
<OPTION class="band_gray" value="gray">gray</option>
<OPTION class="band_brown" value="brown">brown</option>
<OPTION value="unknown">unknown</option>
<OPTION class="band_metal" value="metal">metal/silver band</option>

</SELECT>
</td>

<td>
<SELECT NAME="bandur">
<OPTION value="vacant" SELECTED>none</option>
<OPTION class="band_red" value="red">red</option>
<OPTION class="band_orange" value="orange">orange</option>
<OPTION class="band_yellow" value="yellow">yellow</option>
<OPTION class="band_green" value="green">green</option>
<OPTION class="band_pblue" value="pblue">powder blue</option>
<OPTION class="band_lblue" value="lblue">light blue</option>
<OPTION class="band_dblue" value="dblue">dark blue</option>
<OPTION class="band_purple" value="purple">purple</option>
<OPTION class="band_black" value="black">black</option>
<OPTION class="band_white" value="white">white</option>
<OPTION class="band_gray" value="gray">gray</option>
<OPTION class="band_brown" value="brown">brown</option>
<OPTION value="unknown">unknown</option>
<OPTION class="band_metal" value="metal">metal/silver band</option>
</SELECT>
</td>
</tr>

<tr>
<td>
<SELECT NAME="bandll">
<OPTION value="vacant" SELECTED>none</option>
<OPTION class="band_red" value="red">red</option>
<OPTION class="band_orange" value="orange">orange</option>
<OPTION class="band_yellow" value="yellow">yellow</option>
<OPTION class="band_green" value="green">green</option>
<OPTION class="band_pblue" value="pblue">powder blue</option>
<OPTION class="band_lblue" value="lblue">light blue</option>
<OPTION class="band_dblue" value="dblue">dark blue</option>
<OPTION class="band_purple" value="purple">purple</option>
<OPTION class="band_black" value="black">black</option>
<OPTION class="band_white" value="white">white</option>
<OPTION class="band_gray" value="gray">gray</option>
<OPTION class="band_brown" value="brown">brown</option>
<OPTION value="unknown">unknown</option>
<OPTION class="band_metal" value="metal">metal/silver band</option>
</SELECT>
</td>

<td>
<SELECT NAME="bandlr">
<OPTION value="vacant" SELECTED>none</option>
<OPTION class="band_red" value="red">red</option>
<OPTION class="band_orange" value="orange">orange</option>
<OPTION class="band_yellow" value="yellow">yellow</option>
<OPTION class="band_green" value="green">green</option>
<OPTION class="band_pblue" value="pblue">powder blue</option>
<OPTION class="band_lblue" value="lblue">light blue</option>
<OPTION class="band_dblue" value="dblue">dark blue</option>
<OPTION class="band_purple" value="purple">purple</option>
<OPTION class="band_black" value="black">black</option>
<OPTION class="band_white" value="white">white</option>
<OPTION class="band_gray" value="gray">gray</option>
<OPTION class="band_brown" value="brown">brown</option>
<OPTION value="unknown">unknown</option>
<OPTION class="band_metal" value="metal">metal/silver band</option>
</SELECT>
</td>
</tr>

<tr>
<td>
<SELECT NAME="bandbl">
<OPTION value="vacant" SELECTED>none</option>
<OPTION value="unknown">unknown</option>
<OPTION class="band_metal" value="metal">metal/silver band</option>
</SELECT>
</td>
<td>
<SELECT NAME="bandbr">
<OPTION value="vacant" SELECTED>none</option>
<OPTION value="unknown">unknown</option>
<OPTION class="band_metal" value="metal">metal/silver band</option>
</SELECT>
</td>
</tr>
</table>

<p class="bold">What was the crow doing at the time of the sighting?
<SELECT NAME="banddoing">
<OPTION value="unknown" SELECTED>not recorded</option>
<OPTION value="forage">foraging</option>
<OPTION value="vocal">vocalizing</option>
<OPTION value="perch">perching</option>
<OPTION value="preen">preening</option>
<OPTION value="fly">flying</option>
<OPTION value="dead">being dead</option>
<option value="other">other</option>
</select>

<!--
<p>Did any of the bands have letters or numbers on them?  If so please describe them here.</p>
<INPUT TYPE="TEXT" NAME="bandother" value="none">
-->
<input type="hidden" name="bandother" value="none">
</div>

<hr>
<p class="bold">Observation Notes:

<br>
<TEXTAREA NAME="obsnotes" COLS=30 ROWS=6 onFocus="doClear(this);">Please be as detailed as you can.... anecdotes, stories, behaviors, exact crow counts, location detail, time details, weather, bands with letters or numbers, etc...   Thank you!</TEXTAREA>


<hr>
<p class="bold">Observation Date: 
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td>Day</td>
<td>Month</td>
<td>Year</td>
</font>
</tr>

<tr>
<td><input type="text" name="obsday" size=2 maxlength=2 value="<?php echo $today['mday'];?>"></td>
<td><input type="text" name="obsmonth" size=2 maxlength=2 value="<?php echo $today['mon'];?>"></td>
<td><input type="text" name="obsyear" size=4 maxlength=4 value="<?php echo $today['year'];?>"></td>
</tr>
</table>
<br>If date is unknown, check here: <input type="checkbox" value="baddate" name="accdate"><br>

<p class="bold">Observation Time:
<SELECT NAME="acctime">
<OPTION value="unknown" SELECTED>unknown</option>
<OPTION value="morning">morning</option>
<OPTION value="afternoon">afternoon</option>
<OPTION value="evening">evening</option>
</SELECT>

<hr>

<p class="bold">Email/contact info:
<INPUT name="obsemail" type="text" id="userid" size=35 value="your email address or other contact info" onFocus="doClear(this)">

<p class="bold">Security: what is
<?php
	$rand1 = rand(0, 9);
	$rand2 = rand(10, 19);
	print("$rand1 + $rand2?");?>


<input type="text" name="security" size="10" maxlength="2" />
<input type="hidden" name="rand1" value="<?php echo $rand1;?>">
<input type="hidden" name="rand2" value="<?php echo $rand2;?>">

<p>
<input type="submit" name="form_done" value="Submit Form">
<input type="hidden" name="formsub" value="1">

</form>
</div>

<div id="aboutDiv">
<h2>Welcome to the Seattle Crow Area Mapping Project!</h2>
<p>
Have you noticed hundreds of crows streaming through the shadows at dusk, or witnessed a crow harass a bald eagle?  Crows are all around us and it seems that everyone has at least one story to tell. If you do too, here is your chance!  I've created an interactive website enabling citizen scientists to share their observations of daily migrations, nightly roosts, banded crows, and more, with scientists and each other.  The ultimate goals of the project are to involve more people in the process of scientific discovery and explore our cultural fascination with our corvid neighbors. During this process I hope we will build a useful database of crow happenings in the Puget Sound region and beyond.  Sightings can be submitted and accessed from a map on the website, via Twitter, or by posting photos to Flickr. Help us collect data on these fascinating birds!

<p><a href="http://www.seattleaudubon.org/uploadedFiles/News/Newsletter_Earthcare_NW/09-03-Earthcare_March_2009.pdf" target="_new">Published in Earthcare Northwest</a>, the official newsletter of <a href="http://seattleaudubon.org" target="_new">Seattle Audubon</a>, site by <a href="http://staff.washington.edu/rec3141/">Eric Collins (&#x72;&#x65;&#99;&#x33;&#x31;&#52;&#x31;&#x40;&#103;&#109;&#97;&#x69;&#108;&#x2e;&#x63;&#x6f;&#109;)</a>

<p>
This project is sponsored by <a href="http://www.cfr.washington.edu/CFRPublic/People/FacultyProfile.aspx?PID=10">Prof. John Marzluff</a> in the <a href="http://www.cfr.washington.edu">College of Forestry Resources</a> at the <a href="http://www.washington.edu">University of Washington</a>


<br>
</div>

<div id="subDiv">

<?php
if (isset($result)) {
	if ($result == 'FALSE') {
		 echo "There was an error: <br><b>" . mysql_error() . "</b><p>Your entry could not be loaded into the database.  Please alert the webmaster.";
	}
	elseif ($result == 'ERROR') {
		echo "There was an error: <br><b>" . $error . "</b><p>Your entry was not recorded. Please go back and try again or alert the site owner.";
	}
}


?>

</div>


<!--
######################################################
############################################################ FEED
#################################################
-->



<div id="rssfeed">
<a name="rssref"></a>
<table id="rsstable">

<tr>
<td style="width:50%;">
<table id="rsstable">
<tbody id="feedsmall">
    <thead>
    	<tr>
    	<th></th>
    	<th scope="col">Last sightings (<a onclick="getHistory();" style="font-size:0.8em">view all</a>)</th>
        </tr>
    </thead>

  <?php
  include("./feed/rss_fetch.inc");
  $rss_url = "http://depts.washington.edu/uwcrows/crowhistory.rss";
  	$rss = fetch_rss( $rss_url );
	$rsscount = 0;
	//print_r($rss);
  	foreach ($rss->items as $item) {
  		$title = $item['title'];
  		$desc = $item['description'];
		$gps = $item['author'];
		$link = $item['link'];
  		$pubdate_unix = $item['date_timestamp'];
  		$pubdate = strftime("%Y-%m-%d",$pubdate_unix);
		if ($title == 'twitter') {continue;}
		if ($rsscount > 9) {break;}
		if ($title == 'flickr') {
		    	echo "<tr><td>$pubdate<br><a href='#top' onclick='zoomMap($gps,17);'>view photo</a></td><td>$desc</td></tr>\n";
		    	$rsscount++;
		}
		else {
		    	echo "<tr><td>$pubdate<br><a href='#top' onclick='zoomMap($gps,17);'>view on map</a></td><td>$desc</td></tr>\n";
		    	$rsscount++;
		}
	}
 ?>
 
 </tbody>

<tbody id="feedall" style="display:none">
<?php
  	foreach ($rss->items as $item) {
  		$title = $item['title'];
		if ($title == 'twitter') {continue;}
		$rsscount--;
  		if ($rsscount >= 0) {continue;}
  		$desc = $item['description'];
		$gps = $item['author'];
		$link = $item['link'];
  		$pubdate_unix = $item['date_timestamp'];
  		$pubdate = strftime("%Y-%m-%d",$pubdate_unix);
		if ($title == 'flickr') {echo "<tr><td>$pubdate<br><a href='#top' onclick='zoomMap($gps,17);'>view photo</a></td><td>$desc</td></tr>\n";}
		else {echo "<tr><td>$pubdate<br><a href='#top' onclick='zoomMap($gps,17);'>view on map</a></td><td>$desc</td></tr>\n";}
	}

  	?>
  	
</tbody>
</table>

</td>
<td style="width:50%;">

<table id="rsstable">
<tbody id="tweetsmall">
    <thead>
    	<tr>
    	<th></th>
    	<th scope="col">Last Tweets (<a onclick="getHistory();" style="font-size:0.8em">view all</a>)</th>
        </tr>
    </thead>

  <?php
    $rss_url = "http://depts.washington.edu/uwcrows/crowhistory.rss";
  	$rss = fetch_rss( $rss_url );
	$rsscount = 0;
  	foreach ($rss->items as $item) {
  		$title = $item['title'];
  		$desc = $item['description'];
		$gps = $item['author'];
		$link = $item['link'];
  		$pubdate_unix = $item['date_timestamp'];
  		$pubdate = strftime("%Y-%m-%d",$pubdate_unix);
  		if ($rsscount > 10) {break;}
		if ($title == 'twitter') {
		  	echo "<tr><td>$pubdate<br><a href='$link' target='_blank'>$gps</a></td><td>$desc</td></tr>\n";
		  	$rsscount++;
		}
	}

 ?>
 
 </tbody>

<tbody id="tweetall" style="display:none">
<?php
  	foreach ($rss->items as $item) {
  		$title = $item['title'];
  		$desc = $item['description'];
		$gps = $item['author'];
		$link = $item['link'];
  		$pubdate_unix = $item['date_timestamp'];
  		$pubdate = strftime("%Y-%m-%d",$pubdate_unix);
		if ($title == 'twitter') {
			$rsscount--;
  			if ($rsscount >= 0) {continue;}
		  	echo "<tr><td>$pubdate<br><a href='$link' target='_blank'>$gps</a></td><td>$desc</td></tr>\n";
		}
  	}
  	?>
  	
</tbody>
</table>
</td></tr>
</table>

<div id="footer">
Sponsored by <a href="http://www.cfr.washington.edu/CFRPublic/People/FacultyProfile.aspx?PID=10">Prof. John Marzluff</a> in the <a href="http://www.cfr.washington.edu">College of Forestry Resources</a> at the <a href="http://www.washington.edu">University of Washington</a>
</div>

</div>

</body>
</html>
