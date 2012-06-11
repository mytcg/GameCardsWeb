<?php
$query = "SELECT * FROM mytcg_leaderboards";
$aQueries=myqu($query);
$iCount = 0;
?>
<div class="table-container" id="leaderboard-container" >
	<div class="leaderboard_menu">
	 <?php while ($iBoardID=$aQueries[$iCount]['leaderboard_id']){ ?>
		<div class="cmdButton" id="<?php echo($iBoardID); ?>"><?php echo($aQueries[$iCount]['description']); ?></div>
		<?php $iCount++; } ?>
	</div>
	<div class="leaderboard-table-container">
		<table class="leaderboard_chart">
			<tr><th>Rank</th><th>Name</th><th>Score</th></tr>
			<?php
			$aBoard=myqu($aQueries[0]['lquery']);
	    $iCount = 0;
			while ($iList=$aBoard[$iCount]['usr']){
		   echo "<tr>
		         <td>".($iCount + 1)."</td>
		         <td>".$aBoard[$iCount]["usr"]."</td>
		         <td>".$aBoard[$iCount]["val"]."</td>
		       </tr>";
	     $iCount++;
			}
			?>
			
		</table>
	</div>
</div>
