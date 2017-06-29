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
       foreach ($categories as $category)
           echo "<td bgcolor=grey><b>" . $category . "</b></td><tr>";
           
      ?>
  </table>
  </center>
</body>
</html>
