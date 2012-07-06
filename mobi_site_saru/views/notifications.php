<?php
$query = "SELECT * FROM mytcg_notifications
		  ORDER BY notedate DESC LIMIT 10";
$aNotifications=myqu($query);
$iCount = 0;
?>
<ul id="item_list">
		<li style='text-align:center;'><p><strong>Notifications</strong></p></li>
        <?php
			while ($iNote=$aNotifications[$iCount]['notification_id']){
			echo "<li style='text-align:left; height:50px;'><a><p style='padding-top:0px';>".$aNotifications[$iCount]['notedate'].":&nbsp;&nbsp;".$aNotifications[$iCount]['notification']."</p></a></li>";
			$iCount++;
			}
        ?>
</ul>