<?php 

if($_SESSION['userID']){ ?>
<ul id="navmenu">
	<li><a href="index.php?page=album_list" class="button"><img alt="Album" width="77px" height="108px" src="images/Album.png" /></a></li>
	<li><a href="index.php?page=game" class="button"><img alt="Play" width="77px" height="108px"  src="images/Play.png" /></a></li>
	<li><a href="index.php?page=deck" class="button"><img alt="Deck" width="77px" height="108px"  src="images/Decks.png" /></a></li>
	<li><a href="index.php?page=shop_list" class="button"><img alt="Shop" width="77px" height="108px"  src="images/Shop.png" /></a></li>
	<li><a href="index.php?page=auction_list" class="button"><img alt="Auction" width="77px" height="108px"  src="images/Auctions.png" /></a></li>
	<li><a href="index.php?page=credits" class="button"><img alt="Credits" width="77px" height="108px"  src="images/Credits.png" /></a></li>
	<li><a href="index.php?page=profile" class="button"><img alt="Profile" width="77px" height="108px"  src="images/Profile.png" /></a></li>
	<li><a href="index.php?page=notifications" class="button"><img alt="Notifications" width="77px" height="108px"  src="images/Notifications.png" /></a></li>
	<li><a href="index.php?page=ranking_list" class="button"><img alt="Ranking" width="77px" height="108px"  src="images/Rankings.png" /></a></li>
	<li><a href="index.php?page=friends_rank_list" class="button"><img alt="Friend Rank" width="77px" height="108px"  src="images/FriendRanks.png" /></a></li>
	<li><a href="index.php?page=friends" class="button"><img alt="Friend" width="77px" height="108px"  src="images/Friends.png" /></a></li>
	<li><a href="index.php?page=invite_friends" class="button"><img alt="Invite" width="77px" height="108px"  src="images/Invite.png" /></a></li>
	<li><a href="index.php?page=redeem" class="button"><img alt="Redeem Credits" width="77px" height="108px"  src="images/Redeem.png" /></a></li>
	<li><a href="index.php?page=logout" class="button"><img alt="LOGOUT" width="77px" height="108px"  src="images/Logout.png" /></a></li>
</ul>
<?php } else { 
	echo("You are not Logged in<br><a href='index.php?page=index'>Login in</a>");
 }?>