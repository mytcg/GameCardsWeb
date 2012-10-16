<?php if($_SESSION['userID']){
	$iUserID = $_SESSION['userID'];
		if($_GET['game_continue']){
			$query=	'SELECT GP.game_id, GD.description as "level", G.date_start, C.description as "category", U.username as "opponent" '
					.'FROM mytcg_gameplayer AS GP '
					.'INNER JOIN mytcg_game AS G ON GP.game_id = G.game_id '
					.'INNER JOIN mytcg_user AS U ON U.user_id = 1 '
					.'INNER JOIN mytcg_gamedifficulty AS GD ON GD.gamedifficulty_id = G.gamedifficulty_id '
					.'INNER JOIN mytcg_category AS C ON G.category_id = C.category_id '
					.'INNER JOIN mytcg_gamestatus AS GS ON G.gamestatus_id = GS.gamestatus_id '
					.'WHERE GP.user_id = '.$iUserID. ' '
					.'AND GP.is_active = 1 '
					.'AND GS.description = "incomplete" '
					.'ORDER BY G.date_start ASC '
					.'LIMIT 10';
		
			$aGames = myqu($query);
			$aGameID = $aGames[$iCount]['game_id'];
			$iCount = 0;
			$iSize = $_GET['size'];
		?>
		<ul id="item_list">
			<?php
		if ($aGameID == null){
			while ($iGame = $aGames[$iCount]['game_id']){
		    	echo "<li style='text-align:left;height:50px;'><a href='index.php?page=game_play&game_id=".$iGame."&size=".$iSize."'><p style='padding-top:0px';>".$aGames[$iCount]['date_start']."&nbsp;".$aGames[$iCount]['category']."  VS ".$aGames[$iCount]['opponent']." <span>[".$aGames[$iCount]['level']."]</span></p></a></li>";
				$iCount++;
			}
			?>
		</ul>
			<?php exit;
			}
		else{
			echo ("<li><p style='text-align:left;color:#CC0000'>You have no games to continue, create new game</p></li>");
			}
			
		}
			 
		if($_GET['gamescreensize'] == 1) { ?>
			<p>Select your screen size for New Game</p>
			<ul id="item_list">
				<li><a href="index.php?page=game_play&game_id=0&size=1"><p>Large-Portrait</p></a></li>
				<li><a href="index.php?page=game_play&game_id=0&size=2"><p>Large-Landscape</p></a></li>
				<li><a href="index.php?page=game_play&game_id=0&size=3"><p>Small-Portrait</p></a></li>
				<li><a href="index.php?page=game_play&game_id=0&size=4"><p>Small-Landscape</p></a></li>
			</ul>
	<?php exit; } else {
		  if ($_GET['gamescreensize'] == 2)	{ ?>
			<p>Select your screen size to Continue Game</p>
			<ul id="item_list">
				<li><a href="index.php?page=game&game_continue=1&size=1" ><p>Large-Portrait</p></a></li>
				<li><a href="index.php?page=game&game_continue=1&size=2" ><p>Large-Landscape</p></a></li>
				<li><a href="index.php?page=game&game_continue=1&size=3" ><p>Small-Portrait</p></a></li>
				<li><a href="index.php?page=game&game_continue=1&size=4" ><p>Small-Landscape</p></a></li>
			</ul>
	<?php  exit; };
		   }; ?>
	<ul id="item_list">
		<li><a href="index.php?page=game&gamescreensize=1" title="Album list"><p>New Game</p></li></a>
		<li><a href="index.php?page=game&gamescreensize=2"><p>Continue Game</p></li></a>
	</ul>
<?php } ?>