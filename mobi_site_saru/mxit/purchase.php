<?php 
require_once("conn.php");

?>
<html>
   <head>
   <style>p,a {font-size:12px;font-family:"Arial","Arial Black";font-weight:900;text-decoration:none;color:#777777}</style>
      <title>SA Rugby Cards Mxit App</title>
   </head>
   <body>
       <img src="images/header_left.png" border="0" /><br />
       <?php if ($_SESSION['userID']){ ?>
       <a href="confirm.php?a=500">350 Credits for 500 Moola</a><br />
       <a href="confirm.php?a=1000">700 Credits for 1000 Moola</a><br />
       <a href="confirm.php?a=2000">1400 Credits for 2000 Moola</a>
       <br /><br />
       <?php } else { echo 'No user to assign credits, <a href="info.php">try again?</a><br />';} ?>
       <a href="index.php">Back</a>
   </body>

<html>

