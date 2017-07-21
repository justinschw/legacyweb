<html>
<head>
<title>LegacyWeb - YouTube</title>
</head>
<body bgcolor="black">
<center>
<table width="80%" border=1 bgcolor="white">
<td colspan=3>
<h3>YouTube</h3>
<br>
<FORM>
Search &nbsp;<INPUT TYPE="TEXT" NAME="q"><INPUT TYPE="SUBMIT", NAME="searchButton">
</FORM>
</td>
<tr>
<?php
$API_KEY="{{ google_api_key }}";
if(isset($_GET["selectVid"]) && $_GET["selectVid"] != "") {
   // Play the video
   $username = '';
   $password = '{{ vlc_web_pass }}';
 
   $context = stream_context_create(array(
    'http' => array(
        'header'  => "Authorization: Basic " . base64_encode("$username:$password")
    )
   ));
   $vid_id = $_GET["selectVid"];
   file_get_contents("http://127.0.0.1:8080/requests/status.xml?command=in_play&input=https://www.youtube.com/watch%3Fv%3D".$vid_id,false,$context);
}
if(isset($_GET["q"]) && $_GET["q"] != "") {
  $query=$_GET["q"];
  $youtube_search_url="https://www.googleapis.com/youtube/v3/search?q=".urlencode($query)."&maxResults=50&safeSearch=strict&part=snippet&key=".$API_KEY;
  $vid_search_result=file_get_contents($youtube_search_url);
  $vid_search_result_json=json_decode($vid_search_result);
  $i = 0;
  print("<FORM>");
  print("<INPUT TYPE='HIDDEN' NAME='q' VALUE='".$_GET["q"]."'>");
  foreach($vid_search_result_json->items as $item) {
    if( isset( $item->{"id"}->{"videoId"} ) ) {
        print("<td>");
        print("<INPUT TYPE='RADIO' NAME='selectVid' VALUE='".$item->{"id"}->{"videoId"}."' ONCLICK='this.form.submit()'>");
        print("</td>");
        print("<td>");
	print("<img src='http://i.ytimg.com/vi/".$item->{"id"}->{"videoId"}."/default.jpg'>&nbsp;");
        print("</td>");
        print("<td>");
          print("<table>");
          print("<td>");
          print("<b>".$item->{"snippet"}->{"title"}."</b>");
          print("</td>");
          print("<tr>");
          print("<td>");
          print("<small>".$item->{"snippet"}->{"description"}."</small>");
          print("</td>");
          print("</table>");
        print("</td>");
    	//print($item->{"id"}->{"videoId"});
        //print("</td>");
        print("<tr>");
    }
  }
  print("</FORM>");
}
?>
</table>
</center>
</body>
</html>
