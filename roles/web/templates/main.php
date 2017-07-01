<html>
<title>LegacyWeb - Main Page</title>
<body bgcolor="black">
  <center>
    <font color=white>
    <img src="images/legacyweb_logo.gif">
    <h2>LegacyWeb Home</h2></font>
  </center>
  <center>
  <table bgcolor="white" border=2 width="80%">
    <?php
    $redis = new Redis();
    $redis->connect('127.0.0.1');
    $redis->select(1);
    $categories = $redis->keys('*');
    foreach ($categories as $category) {
      echo "<td colspan=2 bgcolor=#99a3a4><h3>" . $category . "</h3></td><tr>";
      $pages = $redis->lrange($category, 0, -1);
      foreach ($pages as $page) {
        $pagearr = explode(',', $page);
        $pageLabel = trim($pagearr[0]);
        $pageurl = trim($pagearr[1]);
        echo "<td><b>".$pageLabel."</b></td>";
        echo '<td><a href="http://'.$pageurl.'/">'.$pageurl.'</a></td>';
        echo '<tr>';
      }
    }
      
    ?>
  </table>
  </center>
</body>
</html>
