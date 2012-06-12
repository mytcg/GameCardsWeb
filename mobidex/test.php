<?php
	include('include/system.inc.php');
	$cardID = $_GET['card'];
	$sql = "SELECT CS.*
			FROM mytcg_card C
			INNER JOIN mytcg_cardstat CS ON (C.card_id = CS.card_id)
			WHERE C.card_id = {$cardID}";
	$cardStats = myqu($sql);
	$iListCount = sizeof($cardStats);
?>
<html>
	<head>
		<style>
			body{
				margin: 0px;
			}
			div{
				position:absolute;
				border: 1px solid #FFF;
			}
		</style>
	</head>
	<body>
		<img id="front" src="http://mobidex.biz/img/cards/<?php echo($cardID); ?>_front.jpg" />
		<?php for($i=0;$i<$iListCount;$i++){ ?>
		<div style="top:<?php echo($cardStats[$i]['top']); ?>px;left:<?php echo($cardStats[$i]['left']); ?>px;width:<?php echo($cardStats[$i]['width']); ?>px;height:<?php echo($cardStats[$i]['height']); ?>;"></div>
		<?php } ?>
	</body>
</html>