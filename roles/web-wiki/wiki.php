<?php
   // load Zend classes
   require_once 'Zend/Loader.php';
   Zend_Loader::loadClass('Zend_Rest_Client');
   ?>

<html>
  <head>
    <title>Legacyweb - Wikipedia</title>
  </head> 
  <body>
    <center>
      <table width="75%" border=1>
	<tr>
	  <td>
	    <h1>Wikipedia</h1>
	    <br>
	    <FORM NAME="searchForm">
	      <INPUT TYPE=HIDDEN NAME="display" VALUE="searchList">
	      Search: &nbsp <INPUT TYPE="TEXT" NAME="q"> <INPUT TYPE="SUBMIT" NAME="searchButton" VALUE="Go">
	      <?php
		 if(isset($_GET["q"]) && $_GET["q"] != "") {
		   $query = $_GET["q"];
		   $query_url="https://en.wikipedia.org/w/api.php?action=query&list=search&srwhat=text&format=json&srsearch=" . $query;
	           // perform request
                   $query_result = file_get_contents($query_url);
                   $result_json = json_decode($query_result);
                 }
	      ?>
	    </FORM>
	  </td>
	<tr>
	  <td padding="8">
	    <FORM NAME="pageSel">
	      <table border=0>
	    <?php
	       if(isset($_GET["display"]) && $_GET["display"] == "searchList") {
               print("<h3>Search results:</h3>");
	       foreach($result_json->{"query"}->{"search"} as $item) {
	           $title=$item->{"title"};
	           print("<td><INPUT TYPE='RADIO' NAME='wikipage' VALUE='".$title."' ONCLICK='this.form.submit()'></td>");
	           print("<td><b>".$title."</b><br><small>".$item->{"snippet"}."</small><br>"."</td><tr>");
	         }
	       }
	    ?>
	    </table>
	    <INPUT TYPE=HIDDEN NAME="display" VALUE="showPage">
	    </FORM>
	    <?php
	       if(isset($_GET["display"]) && $_GET["display"] == "showPage") {
	         $wikipage = $_GET["wikipage"];
		 $query_url="https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&titles=".$wikipage."&redirects=";
                 $query_result = file_get_contents($query_url);
                 $result_json = json_decode($query_result);
                 foreach($result_json->{"query"}->{"pages"} as $page) {
		   print($page->{"extract"});
		 }
	       }
	       ?>
	  </td>
      </table>
    </center>
  </body>
</html>
