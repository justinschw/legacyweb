<html>
  <head>
    <title>Legacyweb - Wikipedia</title>
  </head> 
  <body bgcolor="black">
    <center>
      <table width="75%" border=1 bgcolor="white">
	<tr>
	  <td bgcolor="#99a3a4">
	    <h1>Wikipedia</h1>
	    <FORM NAME="searchForm">
	      <INPUT TYPE=HIDDEN NAME="display" VALUE="searchList">
	      Search: &nbsp <INPUT TYPE="TEXT" NAME="q"> <INPUT TYPE="SUBMIT" NAME="searchButton" VALUE="Go">
	      <?php
		 if(isset($_GET["q"]) && $_GET["q"] != "") {
		   $query = $_GET["q"];
		   $query = urlencode($query);
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
		 $query_url="https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&titles=".urlencode($wikipage)."&redirects=";
		 $query_image_url="https://en.wikipedia.org/w/api.php?action=query&titles=".urlencode($wikipage)."&prop=pageimages&format=json&pithumbsize=150";
                 $query_result = file_get_contents($query_url);
		 $query_image_result=file_get_contents($query_image_url);
                 $result_json = json_decode($query_result);
		 $result_image_json = json_decode($query_image_result);
		 print("<table>");
		 print("<tr>");
		 print("<td bgcolor=#99a3a4>");
		 print("<h2>".$wikipage."</h2>");
		 print("</td>");
		 print("<td width=150>");
		 foreach($result_image_json->{"query"}->{"pages"} as $page) {
		   if (isset($page->{'thumbnail'})) {
		     file_put_contents("/var/www/html/images-tmp/pageimg", file_get_contents($page->{"thumbnail"}->{"source"}));
		     print("<img src='/images-tmp/pageimg'>");
		   }
		 }
		 print("</td>");
		 print("<tr>");
		 print("<td colspan=2>");
                 foreach($result_json->{"query"}->{"pages"} as $page) {
		   print($page->{"extract"});
		 }
		 print("</td>");
		 print("</table>");
      }
      if(!isset($_GET["display"])) {
      // Main page
      print("<center><h3>Welcome to LegacyWeb Wikipedia.</h3><br><img src='images/wikilogo.gif'><br><small>Copyright WikiMedia</small><br><br>Enter a query above to get a list of wikipedia pages.<br><br></center>");
      }
	       ?>
	  </td>
      </table>
    </center>
  </body>
</html>
