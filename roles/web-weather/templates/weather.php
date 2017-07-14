<?php
/* Convert full state to abbreviation */
$api_key="{{ weather_api_key }}";
$gmaps_api_key="{{ google_api_key }}";
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

function save_icon($icon_id) {
  if(!file_exists('/var/www/html/images-tmp/'.$icon_id.'.jpg')) {
    $icon_url = 'http://openweathermap.org/img/w/'.$icon_id.'.png';
    $image = imagecreatefrompng($icon_url);
    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
    imagealphablending($bg, TRUE);
    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
    imagedestroy($image);
    $quality = 90;
    imagejpeg($bg, "/var/www/html/images-tmp/" . $icon_id . ".jpg", $quality);
    imagedestroy($bg);
  }
}

function k_to_f($temp_k) {
  return round(($temp_k * 9) / 5 - 459.67, 1);
}

function mps_to_mph($mps) {
  return round($mps / 0.44704, 1);
}

// Get Location Info
$ip = file_get_contents('http://ipv4bot.whatismyipaddress.com/');
$record=geoip_record_by_name($ip);
$lat=$record["latitude"];
$lon=$record["longitude"];
$city=$record["city"];
$state=convert_state($record["region"], "abbrev");
// In case of alternate zip
if(isset($_GET["alternateZip"]) && $_GET["alternateZip"] != "") {
  $current_loc_url="https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($_GET["alternateZip"]) . "&sensor=false&key=" . $gmaps_api_key;
  $current_loc_result=file_get_contents($current_loc_url);
  $current_loc_json=json_decode($current_loc_result);
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
}
if($city == "") {
  $city = "Unknown Region";
}
// TODO: get country from record
$country="us";

$appendzip1='';
$appendzip2='';
if(isset($_GET["alternateZip"]) && $_GET["alternateZip"] != "") {
  $appendzip1 = "&alternateZip=".$_GET["alternateZip"];
  $appendzip2 = "?alternateZip=".$_GET["alternateZip"];
}

?>
<html>
<head>
<title>LegacyWeb - Weather</title>
</head>
<body>
<table width=800px border=1>
<td colspan=2>
<h1>Weather</h1>
<br>
<a href=<?php print("weather.php".$appendzip2); ?>>Today's forecast</a>
<a href=<?php print('weather.php?reportType=5day'.$appendzip1); ?>>Five day Forecast</a>
</td>
<tr>
<td width=246px valign="top">
	<table width="200px" border=0 cellpadding=4>
	<td>
		<font size=2>Current location:</font>
	</td>
	<tr>
	<td bgcolor="green">
		<font color="white">
		<font size=4><b><?=$city?></b></font>
		<br>
		<font size=3><?=$state?></font>
	</td>
	<tr>
	<td>
	<small>Alternate Zip:</small>
	<br>
	<FORM NAME="altLoc">
	<INPUT TYPE="TEXT" NAME="alternateZip" SIZE="5">&nbsp
	<INPUT TYPE="SUBMIT" NAME="altZipButton" VALUE="Go">
	</FORM>
	</td>
	</table>
</td>
<td>
<?php
if(isset($_GET["reportType"]) && $_GET["reportType"] == "5day") {
  // 5-day forecast
  if(isset($_GET["alternateZip"]) && $_GET["alternateZip"] != "") {
    $altZip=$_GET["alternateZip"];
    $forecast_weather_url = "http://api.openweathermap.org/data/2.5/forecast?zip=".$altZip."&appid=".$api_key;
  } else {
    $forecast_weather_url = "http://api.openweathermap.org/data/2.5/forecast?q=".$city.",".$record["region"]."&appid=".$api_key;
  }
  $forecast_weather_result = file_get_contents($forecast_weather_url);
  $forecast_weather_json = json_decode($forecast_weather_result);
  print("<table>");
  print("<td><b>Date</b></td>");
  print("<td><b>12AM</b></td>");
  print("<td><b>3AM</b></td>");
  print("<td><b>6M</b></td>");
  print("<td><b>9AM</b></td>");
  print("<td><b>12PM</b></td>");
  print("<td><b>3PM</b></td>");
  print("<td><b>6PM</b></td>");
  print("<td><b>9PM</b></td>");
  //print("<tr>");
  $curr_date = '';
  $i = 0;
  foreach($forecast_weather_json->list as $listitem) {
    //print($listitem->dt_txt);
    $date_parts = explode(" ",$listitem->dt_txt);
    $date = $date_parts[0];
    $time = $date_parts[1];
    $hour = explode(":",$time)[0];
    if ($curr_date != $date) {
      $curr_date = $date;
      $i = 0;
      print("<tr>");
      print("<td><b>".$date."&nbsp;&nbsp;&nbsp;</b></td>");
    }
    if($i != $hour) {
      for( ; $i < $hour; $i += 3) {
        print("<td><center>-</center></td>");
      }
    }
    print("<td>");
    // Get icon
    $icon_id = $listitem->weather[0]->{"icon"};
    save_icon($icon_id);
    $low=k_to_f($listitem->{"main"}->{"temp_min"});
    $high=k_to_f($listitem->{"main"}->{"temp_max"});
    print("<img src='images-tmp/".$icon_id.".jpg'>");
    print("<br>");
    print("<small>L:".$low."&deg;F<br>H:".$high."&deg;F</small>");
    print("</td>");
    $i = $i + 3;
    ?>
    
    <?php
  }
  print("</table>");
} else {
  // Today's weather
  if(isset($_GET["alternateZip"]) && $_GET["alternateZip"] != "") {
    $altZip=$_GET["alternateZip"];
    $today_weather_url="http://api.openweathermap.org/data/2.5/weather?zip=".$altZip."&appid=".$api_key;
  } else {
    $today_weather_url="http://api.openweathermap.org/data/2.5/weather?q=".$city.",".$record["region"]."&appid=".$api_key;
  }
  $today_weather_result=file_get_contents($today_weather_url);
  $today_weather_result_json=json_decode($today_weather_result);
  //print($today_weather_result);
  $icon_id = $today_weather_result_json->weather[0]->{"icon"};

  save_icon($icon_id);
  // END IF
  $temp_k = $today_weather_result_json->{"main"}->{"temp"};
  $humidity = $today_weather_result_json->{"main"}->{"humidity"};
  $temp_min_k = $today_weather_result_json->{"main"}->{"temp_min"};
  $temp_max_k = $today_weather_result_json->{"main"}->{"temp_max"};
  $wind_speed_mps = $today_weather_result_json->{"wind"}->{"speed"};
  ?>
  <center>
  <table width="400px">
  <td colspan=4>
  <center>
  <img src="<?php print('images-tmp/'.$icon_id.'.jpg'); ?>" width=75 height=75>
  <br>
  <font size=6><?php print($today_weather_result_json->weather[0]->{"main"}); ?></font>
  <br>
  <small>
  Current temp: <?php print(k_to_f($temp_k)); ?>&deg;F
  </small>
  <br>
  <br>
  </center>
  </td>
  <tr>
  <td>
  <small><b>High:</b><?php print(k_to_f($temp_max_k)); ?>&deg;F</small>
  </td>
  <td>
  <small><b>Low:</b> <?php print(k_to_f($temp_min_k)); ?>&deg;F</small>
  </td>
  <td>
  <small><b>Humidity:</b> <?php print($humidity); ?></small>
  </td>
  <td>
  <small><b>Wind:</b> <?php print(mps_to_mph($wind_speed_mps)); ?>mph</small>
  </td>
  </table>
  <br>
  </center>
  <?php
}
?>
</td>
</table>
</body>
</html>