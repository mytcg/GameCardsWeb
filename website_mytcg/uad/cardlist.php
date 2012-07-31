<?php
require_once("config.php");
include_once('functions.php');
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo($sApplicationName); ?> Application Download</title>
<meta http-equiv="content-type" content="application/xhtml+xml" />
<meta http-equiv="cache-control" content="max-age=300" />
<style><?php require("style.css"); ?></style>
</head>
<body>
  <table border=0 cellspacing=0 cellpadding=0 width="331" align="center">
      <tr>
        <td class="menucell"><img src="images/<?php echo($sLogoFilename); ?>" /></td>
      </tr>
      <tr>
        <td class="menucell"><a href="index.php">Back to App download page</a></td>
      </tr>
      <?php if(!$_POST['cellnr']) { ?>
      <form action="cardlist.php" method="POST">
      <tr>
        <td class="menucell">Please provide your cellphone number<br /> to retrieve your cards.<br /><br /><input type="text" name="cellnr" class="borders" /><br /><br /><input type="submit" class="submitz" value="Get list" /></td>
      </tr>
      </form>
      <?php } else {
      //Do some stuff here
      	$sql = "SELECT C.card_id,CIS.description,C.image
      			FROM mytcg_tradecard TC
      			INNER JOIN mytcg_card C ON TC.card_id = C.card_id
      			INNER JOIN mytcg_imageserver CIS ON C.front_imageserver_id = CIS.imageserver_id
      			WHERE TC.detail = '".$_POST['cellnr']."'";
  		$aRes = mysql_query($sql);
  		while ($aRow=mysql_fetch_array($aRes)){
  		$path = $aRow['description']."cards/".$aRow['image'];
      ?>
      <tr>
        <td class="menucell">
        	<img src="<?php echo($path); ?>_front.jpg" border="0" width="150" />
        	<img src="<?php echo($path); ?>_back.jpg" border="0" width="150" />
        </td>
      </tr>
      <?php 
		}
	  }
	  ?>
  </table>
</body>
</html>