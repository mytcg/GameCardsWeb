<?php
$query = "SELECT * FROM mytcg_notifications WHERE user_id = ".$user['user_id']." ORDER BY notedate DESC LIMIT 10";
$aNotifications=myqu($query);
$iCount = 0;
?>
<div class="table-container" id="notification-container" >
	<div class="notification-table-container">
		<table class="notification-chart">
			<tr><th>Notifications</th></tr>
			<?php
			while ($iNote=$aNotifications[$iCount]['notification_id']){
		   echo "<tr>
		         <td>".$aNotifications[$iCount]['notification']."</td>
		       </tr>";
	     $iCount++;
			}
			?>
		</table>
	</div>
</div>
