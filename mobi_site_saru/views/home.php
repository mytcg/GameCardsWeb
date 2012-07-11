<?php 
// user is logging in
if($_SESSION['userID']){ ?>
<ul id="navmenu">
	<li><a href="index.php?page=album_list" class="button"><img alt="Album" src="images/Album.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=shop_list" class="button"><img alt="Shop" src="images/Shop.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=auction_card" class="button"><img alt="Auction" src="images/Auctions.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=credits" class="button"><img alt="Credits" src="images/Credits.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=profile" class="button"><img alt="Profile" src="images/Profile.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=notifications" class="button"><img alt="Notifications" src="images/Notifications.png" width="115px" height="82px" /></a></li>
	<li><a href="index.php?page=logout" class="button"><img alt="LOGOUT" src="images/Logout.png" width="115px" height="82px" /></a></li>
</ul>
<?php } else { ?>
	You are not Logged in<br>
	<a href='index.php?page=index'>Login in</a>
<?php } ?>