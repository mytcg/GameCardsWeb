	<?php
	if ($iLeadID = $_GET['leaderboard_id']){
	$query = "SELECT leaderboard_id, description, lquery, fquery 
				FROM mytcg_leaderboards 
				WHERE active = 1 
				AND leaderboard_id=".$iLeadID." ";
	$aQueries=myqu($query);
	?>
	<ul id="item_list">
			<?php
				$aBoard=myqu($aQueries[0]['lquery']);
		    	$iCount = 0;
				echo "<li><strong><p>".$aQueries[$iCount]['description']."</p></strong></li>";
				while ($iList=$aBoard[$iCount]['usr']){
			   	echo "<div class='info_textbox'>
			         <div><p>".$aBoard[$iCount]["usr"]."</p></div>
			         <div class='info_box'>".$aBoard[$iCount]["val"]."</div>
			         </div>";
		     	$iCount++;
			}
			?>
    </ul>
	<?php }
	else{
	
	$query = "SELECT leaderboard_id, description 
				FROM mytcg_leaderboards
				WHERE active = 1";
	$aQueries=myqu($query);
	$iCount = 0;
	?>
	<ul id="item_list">
		<?php
	    	while ($iLeadID=$aQueries[$iCount]['leaderboard_id']){
			echo "<li><a href='index.php?page=ranking_list&leaderboard_id=".$iLeadID."'><p>".$aQueries[$iCount]['description']." </p></a></li>";
			$iCount++;
		}
		?>
    </ul>
    <?php } ?>