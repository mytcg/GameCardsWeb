<?php
$iUserID = $_SESSION['userID'];
$query='select a.username, a.credits, b.description
						from
					(
										select distinct a.username, a.user_id, a.credits, c.card_id, c.description, max(c.avgranking) avg
										from mytcg_user a, mytcg_frienddetail b, mytcg_card c, mytcg_usercard d
										where a.user_id = b.friend_id
										and d.user_id = a.user_id
										and c.card_id = d.card_id
										and b.user_id = '.$iUserID.'
										group by a.username
					) a, mytcg_usercard d, mytcg_card b
					where a.user_id = d.user_id
					and d.card_id = b.card_id
					and b.avgranking = a.avg
					group by username';
$aFriends = myqu($query);
$iCount = 0;
?>
<ul id="item_list">
	<li style='text-align:center;'><p><strong>Friends</strong></p></li>
<?php foreach ($aFriends as $aFriend[$iCount]) { ?>

		<li style='text-align:left; height:50px;'><a><p style='padding-top:0px';>
		Username: <?php echo ($aFriend[$iCount]['username']); ?><br />	
		Credits: <?php echo ($aFriend[$iCount]['credits']) ;?><br />
		Best Card: <?php echo ($aFriend[$iCount]['description']); ?>
		</p></a></li>
<?php $iCount++; } ?>
</ul>