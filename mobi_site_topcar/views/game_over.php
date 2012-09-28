<div id="card_game">
<?php if($_GET['game_over'] == "draw"){ ?>
	<div id="gameOver">
		<img src="images/draw.png" border="0" />
	</div>
	<ul id="item_list">
		<li><a title="Game Menu" href="index.php?page=game"><p>Game Menu</p></a></li>
	</ul>
</div>
<?php exit; } ?>
<?php if($_GET['game_over'] == 'player'){ ?>
	<div id="gameOver">
		<img src="images/win.png" border="0" />
	</div>
	<ul id="item_list">
		<li><a title="Game Menu" href="index.php?page=game"><p>Game Menu</p></a></li>
	</ul>
</div>
<?php exit; } ?>
<?php if($_GET['game_over'] == 'opp'){ ?>
	<div id="gameOver">
		<img src="images/lose.png" border="0" />
	</div>
	<ul id="item_list">
		<li><a title="Game Menu" href="index.php?page=game"><p>Game Menu</p></a></li>
	</ul>
</div>
<?php exit; } ?>