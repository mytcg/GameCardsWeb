<?php
$query = "SELECT leaderboard_id, description 
				FROM mytcg_leaderboards
				WHERE active = 1";
$aQueries=myqu($query);

echo $aQueries['description'];



?>