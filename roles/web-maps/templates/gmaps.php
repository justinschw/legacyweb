<html>
<head>
<?php
$base_url = explode("&routeIndex", $_SERVER["REQUEST_URI"])[0];
$main_url = explode("?", $base_url)[0];
/* Convert full state to abbreviation */
function convert_state($name, $to='name') {
  $states = array(
  array('name'=>'Alabama', 'abbrev'=>'AL'),
  array('name'=>'Alaska', 'abbrev'=>'AK'),
  array('name'=>'Arizona', 'abbrev'=>'AZ'),
  array('name'=>'Arkansas', 'abbrev'=>'AR'),
  array('name'=>'California', 'abbrev'=>'CA'),
  array('name'=>'Colorado', 'abbrev'=>'CO'),
  array('name'=>'Connecticut', 'abbrev'=>'CT'),
  array('name'=>'Delaware', 'abbrev'=>'DE'),
  array('name'=>'Florida', 'abbrev'=>'FL'),
  array('name'=>'Georgia', 'abbrev'=>'GA'),
  array('name'=>'Hawaii', 'abbrev'=>'HI'),
  array('name'=>'Idaho', 'abbrev'=>'ID'),
  array('name'=>'Illinois', 'abbrev'=>'IL'),
  array('name'=>'Indiana', 'abbrev'=>'IN'),
  array('name'=>'Iowa', 'abbrev'=>'IA'),
  array('name'=>'Kansas', 'abbrev'=>'KS'),
  array('name'=>'Kentucky', 'abbrev'=>'KY'),
  array('name'=>'Louisiana', 'abbrev'=>'LA'),
  array('name'=>'Maine', 'abbrev'=>'ME'),
  array('name'=>'Maryland', 'abbrev'=>'MD'),
  array('name'=>'Massachusetts', 'abbrev'=>'MA'),
  array('name'=>'Michigan', 'abbrev'=>'MI'),
  array('name'=>'Minnesota', 'abbrev'=>'MN'),
  array('name'=>'Mississippi', 'abbrev'=>'MS'),
  array('name'=>'Missouri', 'abbrev'=>'MO'),
  array('name'=>'Montana', 'abbrev'=>'MT'),
  array('name'=>'Nebraska', 'abbrev'=>'NE'),
  array('name'=>'Nevada', 'abbrev'=>'NV'),
  array('name'=>'New Hampshire', 'abbrev'=>'NH'),
  array('name'=>'New Jersey', 'abbrev'=>'NJ'),
  array('name'=>'New Mexico', 'abbrev'=>'NM'),
  array('name'=>'New York', 'abbrev'=>'NY'),
  array('name'=>'North Carolina', 'abbrev'=>'NC'),
  array('name'=>'North Dakota', 'abbrev'=>'ND'),
  array('name'=>'Ohio', 'abbrev'=>'OH'),
  array('name'=>'Oklahoma', 'abbrev'=>'OK'),
  array('name'=>'Oregon', 'abbrev'=>'OR'),
  array('name'=>'Pennsylvania', 'abbrev'=>'PA'),
  array('name'=>'Rhode Island', 'abbrev'=>'RI'),
  array('name'=>'South Carolina', 'abbrev'=>'SC'),
  array('name'=>'South Dakota', 'abbrev'=>'SD'),
  array('name'=>'Tennessee', 'abbrev'=>'TN'),
  array('name'=>'Texas', 'abbrev'=>'TX'),
  array('name'=>'Utah', 'abbrev'=>'UT'),
  array('name'=>'Vermont', 'abbrev'=>'VT'),
  array('name'=>'Virginia', 'abbrev'=>'VA'),
  array('name'=>'Washington', 'abbrev'=>'WA'),
  array('name'=>'West Virginia', 'abbrev'=>'WV'),
  array('name'=>'Wisconsin', 'abbrev'=>'WI'),
  array('name'=>'Wyoming', 'abbrev'=>'WY'),
  array('name'=>'Alberta', 'abbrev'=>'AB'),
  array('name'=>'British Columbia', 'abbrev'=>'BC'),
  array('name'=>'Manitoba', 'abbrev'=>'MB'),
  array('name'=>'New Brunswick', 'abbrev'=>'NB'),
  array('name'=>'Newfoundland', 'abbrev'=>'NL'),
  array('name'=>'Northwest Territories', 'abbrev'=>'NT'),
  array('name'=>'Nova Scotia', 'abbrev'=>'NS'),
  array('name'=>'Nunavut', 'abbrev'=>'NU'),
  array('name'=>'Ontario', 'abbrev'=>'ON'),
  array('name'=>'Prince Edward Island', 'abbrev'=>'PE'),
  array('name'=>'Quebec', 'abbrev'=>'QC'),
  array('name'=>'Saskatchewan', 'abbrev'=>'SK'),
  array('name'=>'Yukon Territory', 'abbrev'=>'YT')
  );

  $return = false;
  foreach ($states as $state) {
    foreach ($state as $title=>$value) {
      if (strtolower($value) == strtolower(trim($name))) {
        if ($to == 'name') {
          $return = $state['abbrev'];
        } else {
          $return = $state['name'];
        }
        break;
      }
    }
  }
  return $return;
}
?>
<SCRIPT LANGUAGE="JavaScript">
function loadHtml(form) {
	var baseurl="<?=$base_url?>";
	location = baseurl+"&routeIndex="+form.routes.selectedIndex.toString();
}
</SCRIPT>
</head>
<title>Retro Maps</title>
<body bgcolor="green">
<?php
// Fill in API key here
$api_key="{{ google_api_key }}";
// Fill in other parameters here
$zoom=13;
$map_length=550;
$map_height=400;
$radius=50000;

if(isset($_GET["currentLoc"]) && $_GET["currentLoc"] != "") {
	// Get latitude and longitude of current location
	$current_loc_url="https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($_GET["currentLoc"]) . "&key=" . $api_key;
	$current_loc_result=file_get_contents($current_loc_url);
	$current_loc_json=json_decode($current_loc_result);
	$lat=$current_loc_json->results[0]->geometry->location->lat;
	$lon=$current_loc_json->results[0]->geometry->location->lng;
	if(count($current_loc_json->results) == 0) {
		$city="Invalid Search";
		$state="";
	} else {
		$city = "Unknown Region";
		foreach($current_loc_json->results[0]->address_components as $component) {
			if($component->types[0] == "locality") {
				$city = $component->long_name;
			} else if($component->types[0] == "administrative_area_level_1") {
				$state = $component->long_name;
			}
		}
	}
} else {
	$ip = file_get_contents('http://ipv4bot.whatismyipaddress.com/');
	$record=geoip_record_by_name($ip);
	$lat=$record["latitude"];
	$lon=$record["longitude"];
	$city=$record["city"];
	$state=convert_state($record["region"], "abbrev");
	if($city == "") {
		$city = "Unknown Region";
	}
}

// This is the default map for the main page; shows your approximate location
$default_map="https://maps.googleapis.com/maps/api/staticmap?center=" . $lat . "," . $lon . "&zoom=" . $zoom . "&size=" . $map_length . "x" . $map_height . "&maptype=roadmap&format=jpg&key=" . $api_key;

$map_type = "default";
$display_map = $default_map;
if(isset($_GET['q']) && $_GET['q']!="") {
	// Is this a places search?
	$map_type="search";
	// map parameters
	$marker_str="&markers=color:blue%7Clabel:H%7C" . $lat . "," . $lon;
	$query_url="https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=" . $lat . "," . $lon . "&rankby=distance&keyword=" . urlencode($_GET["q"]) . "&key=" . $api_key;
	$query_result=file_get_contents($query_url);
	$result_json=json_decode($query_result);
	$place_index=1;
	foreach($result_json->{"results"} as $item) {
		$marker_lat= $item->{"geometry"}->{"location"}->{"lat"};
		$marker_lon= $item->{"geometry"}->{"location"}->{"lng"};
		// Generate marker string
		$marker_str = $marker_str . "&markers=color:red%7Clabel:" . $place_index . "%7C" . $marker_lat . "," . $marker_lon;
		$place_index = $place_index + 1;
	}
	$search_map = "https://maps.googleapis.com/maps/api/staticmap?maptype=roadmap&format=jpg" . $marker_str . "&size=" . $map_length . "x" . $map_height . "&key=" . $api_key;
} else if(isset($_GET['src']) && $_GET['src']!="" && isset($_GET['dst']) && $_GET['dst']!="") {
	// Is this a directions search?
	$map_type="directions";
	$selected=0;
	if(isset($_GET["routeIndex"]) && $_GET["routeIndex"] != "") {
		$selected=$_GET["routeIndex"];
	}
	$mode="driving";
	if(isset($_GET["mode"]) && $_GET["mode"] != "") {
		$mode=$_GET["mode"];
	}
	$directions_url="https://maps.googleapis.com/maps/api/directions/json?origin=" . urlencode($_GET["src"]) . "&destination=" . urlencode($_GET["dst"]) . "&mode=" . $mode . "&alternatives=true";
	if(isset($_GET["avoidTolls"]) && $_GET["avoidTolls"] == "true") {
		$directions_url = $directions_url . "&avoid=tolls";
	}
	$directions_url = $directions_url . "&key=" . $api_key;
	$directions_result=file_get_contents($directions_url);
	$directions_json=json_decode($directions_result);
	$path = $directions_json->routes[$selected]->overview_polyline->points;
	$directions_map = "https://maps.googleapis.com/maps/api/staticmap?maptype=roadmap&format=jpg&size=" . $map_length . "x" . $map_height . "&path=enc" . urlencode(":" . $path) . "&key=" . $api_key;
}
?>

<!-- Begin main table -->
<center>
<table width="800px" border=1 bgcolor="#FFFFE0">
<!-- Top frame -->
<td colspan=2>
  <!-- <img src="/images/legmap_logo.jpg"> -->
  <h1>LegacyWeb - Maps</h1>
<br>
<br>
<FORM NAME="placeSearchForm">
Search for a nearby place: &nbsp <INPUT TYPE="TEXT" NAME="q"> <INPUT TYPE="SUBMIT" NAME="searchButton" VALUE="Go">
<?php
	if(isset($_GET["currentLoc"]) && $_GET["currentLoc"] != "") {
		echo "<INPUT TYPE=HIDDEN NAME=\"currentLoc\" VALUE=\"" . $_GET["currentLoc"] . "\">";
	}
?>
</FORM>

</td>
<tr>
<!-- Sidebar -->
<td width=246px valign=top>
	<table width="95%" border=0 cellpadding=4>
	<td>
		<font size=2>Current location:</font>
	</td>
	<tr>
	<td bgcolor="green">
		<font color="white">
		<font size=4><b><?=$city?></b></font>
		<br>
		<font size=3><?=$state?></font>
		</font>
	</td>
	</table>
	<br>
	<font size=2>Enter address for a different location:</font>

	<FORM NAME="changeLoc">
	<table border=0 valign=top>
	<tr>
	<td>
	<INPUT TYPE="TEXT" NAME="currentLoc">
	</td>
	<td>
	<INPUT TYPE="SUBMIT" NAME="changeLocButton" VALUE="Go">
	</td>
	</table>
	</FORM>
</td>

<!-- Content -->
<td>

<table width=550 border=0>
<tr>
<?php
if($map_type == "search") {
	file_put_contents("/var/www/html/images-tmp/mapimg.jpg", file_get_contents($search_map));
?>
<FORM name="directions">
<?php
	if(isset($_GET["currentLoc"]) && $_GET["currentLoc"] != "") {
		echo "<INPUT TYPE=HIDDEN NAME=\"currentLoc\" VALUE=\"" . $_GET["currentLoc"] . "\">";
	}
?>
<td colspan=2>
<img src="/images-tmp/mapimg.jpg?<?=time()?>">
<!-- <img src="<?=$search_map?>"> -->
</td>
<tr>
<td colspan=2>
Get directions from: &nbsp
<INPUT TYPE=TEXT NAME="src">
&nbsp
<INPUT TYPE=SUBMIT NAME="goButton" VALUE="Go">
<br>
<font size=2>(Remember to include your city and state)
<br>
<INPUT TYPE="checkbox" NAME="avoidTolls" VALUE="true" onClick=0>Avoid Tolls
<br>
<INPUT TYPE="radio" NAME="mode" VALUE="driving" onClick=0 checked=true>Drive
<INPUT TYPE="radio" NAME="mode" VALUE="walking" onClick=0>Walk
</font>
</td>
<?php
$i = 1;
foreach($result_json->results as $result) {
	echo "<tr>";
	echo "<td width=20>";
	echo "<INPUT TYPE=\"radio\" NAME=dst VALUE=\"" . $result->name . ", " . $result->vicinity . ", " . $state . "\" onClick=0>";
	echo "</td>";
	echo "<td>";
	echo $i . ".&nbsp<b>" . $result->name . "</b><br>";
	echo "<font size=2>";
	echo $result->vicinity . ", " . $state;
	echo "</font>";
	echo "<br>";
	echo "</td>";
	$i = $i + 1;
}
?>
</FORM>
<?php
} else if($map_type == "directions") {
?>
<?php
	if(count($directions_json->routes) == 0) {
?>
<td><center><h3>NO RESULT</h3></center></td>
<?php
	} else { // Results if-statement
	file_put_contents("/var/www/html/images-tmp/mapimg.jpg", file_get_contents($directions_map));
?>
<td>
<img src="/images-tmp/mapimg.jpg?<?=time()?>">
<!-- <img src="<?=$directions_map?>"> -->
</td>
<tr>
<td>
<FORM NAME="routeSel">
Choose route: &nbsp
<?php
		echo "<SELECT NAME=\"routes\">";
		foreach($directions_json->routes as $route) {
			echo "<OPTION>" . $route->summary . "</OPTION>";
		}
		echo "</SELECT><INPUT TYPE=\"BUTTON\" NAME=\"chooseRoute\" VALUE=\"Go\" onClick=\"loadHtml(this.form)\">";
?>
</FORM>
</td>
<tr>
<td>
<?php
		foreach($directions_json->routes[$selected]->legs as $leg) {
			echo "<b>Total distance: " . $leg->distance->text . ", est. travel time: " . $leg->duration->text . "</b>";
			echo "<font size=2>";
			echo "<ol>";
			foreach($leg->steps as $step) {
				echo "<li>(". $step->distance->text . ") " . $step->html_instructions . "</li>";
			}
			echo "</ol>";
			echo "</font>";
		}
?>
</td>
<?php
	} // Results if-statement
} else {
	file_put_contents("/var/www/html/images-tmp/mapimg.jpg", file_get_contents($default_map));
?>
<td>
<img src="/images-tmp/mapimg.jpg?<?=time()?>">
<!-- <img src="<?=$default_map?>"> -->
</td>
<?php
}
?>
</table>

<!-- End content -->
</td>
<!-- End main table -->
</table>
</center>

</body>
</html>
