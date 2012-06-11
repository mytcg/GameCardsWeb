<div id="header" >
	<div class="headTitle">
		
		<div class="headDashboard">
			<span>YOUR</span> DASHBOARD
		</div>
		
	</div>
	
	<div class="headBody">
		
		<div class="dashCredits">
			<a href="index.php?page=credits"><div class="direct_credits"></div></a>
			<div class="creditsText"><span><?php echo($user['premium']); ?></span><br />Credits</div>
			<div class="segregation" style="top:20px;left:200px"></div>
		</div>
		<div class="headBlocks">
			<div class="headMiniTitle"><span>Card</span> of the <span>Day</span></div>
			<div class="headBestCard">
				<?php $cardOfDay = getCardOfDay($_SESSION['userDetails']['user_id']); ?>
				<img id="" src="https://sarugbycards.com/img/cards/<?php echo $cardOfDay[0][1];  ?>_web.jpg" width="64px" height="90px" />
			</div>
		</div>
		<div class="headBlocks">
			<div class="headMiniTitle"><span>Cards</span> Collected</div>
			<?php $cardsCollected = getCardInAlbumCount($_SESSION['userDetails']['user_id']); ?>
			<div class="headMiniText"><span id="collected"><?php echo $cardsCollected[0]; ?></span>/<?php echo $cardsCollected[1]; ?></div>
			<div class="segregation" style="top:20px;left: 0px;"></div>
		</div>
		<div class="headBlocks">
			<div class="headMiniTitle"><span>Best</span> Card</div>
			<div class="headBestCard">
				<?php $bestCard = getBestCard($_SESSION['userDetails']['user_id']); ?>
				<img id="" src="https://sarugbycards.com/img/cards/<?php echo $bestCard[0]['image'];  ?>_web.jpg" width="64px" height="90px" />
			</div>
			<div class="segregation" style="top:20px;left:0px;"></div>
		</div>
	</div>
</div>
<?php 
	$query = "SELECT * FROM mytcg_leaderboards";
	$aQueries=myqu($query);
	$aBoard=myqu($aQueries[0]['lquery']);
	$iCount = 0;
?>
<div id="leaderboard" >
	<div class="leaderTitle">
		<div class="headLeaderboard">
			<span>ACHIEVEMENTS</span>
		</div>
	</div>
	<div class="headLeaderBody">
		<div class="leaderSect">
			<div id="title_trophies">
				<div class="leaderSelAchieve"></div>
				<span>TROPHIES</span>
			</div>
			<div id="title_wall">
				<div class="leaderSelWall"></div>
				<span>Wall </span>of Fame
			</div>
			<div class="segregation" style="top:5px;right:0px;"></div>
		</div>
		<div id="aContainer">
			<div style="font-size:24px;margin:13% 32% 13%;width:250px;">COMING SOON</div>
			<div id="trophies" style="display:none">
				<div class="leaderLeftArrow"></div>
				<div id="achiev">
					<div id="" class="achiev_holder"></div>
					<div id="" class="achiev_holder"></div>
					<div id="" class="achiev_holder"></div>
					<div id="" class="achiev_holder"></div>
					<div id="" class="achiev_holder"></div>
					<div id="" class="achiev_holder"></div>
					<div id="" class="achiev_holder"></div>
					<div id="" class="achiev_holder"></div>
				</div>
				<div class="leaderRightArrow"></div>
			</div>
			
			<div id="wall_of_fame" style="display:none">
				<div class="headLeaderModes">
					<div class="leaderBox leaderBoxActive" id="1">Richest</div>
					<div class="leaderBox" id="2">Games Won</div>
					<div class="leaderBox" id="3">Games Lost</div>
					<div class="leaderBox" id="4">Cards Collected</div>
					<div class="leaderBox" id="5">Most Cards</div>
				</div>
				<div class="leaderSect" style="width:100%;margin:0px">
					<div class="leaderLeftArrow"></div>
					<div class="leaderScrolltainer">
						<div class="leaderScrollBox">
						<?php while ($iList=$aBoard[$iCount]['usr']){
							$picURL = getUserPic($aBoard[$iCount]["usr"]);
							if(!$picURL){
								$picURL = "_site/no-profile.jpg";
							}else{
								$picURL = "http://graph.facebook.com/".$picURL."/picture";
							}
						?>
						<div class="friendBox">
							<img class="friendBoxPic" src="<?php echo($picURL); ?>">
							<div class="friendSpeechBubble"><?php echo($iCount + 1); ?></div>
							<p style="margin:0px;padding-left:16px;"><span><?php echo(substr($aBoard[$iCount]["usr"], 0,7)); ?></span><br /><?php echo($aBoard[$iCount]["val"]); ?></p>
						</div>
						<?php
						$iCount++;
						}
						?>
						</div>
					</div>
					<div class="leaderRightArrow"></div>
				</div>
			</div>
			
		</div>
	</div>
</div>
<?php 
	$query = "select a.user_id, SUBSTRING_INDEX(a.username, '@', 1) as username, cnt as val, a.username as usr,a.facebook_user_id as fbid
						from
					(
										select distinct a.username, a.user_id, count(distinct d.usercard_id) as cnt, a.facebook_user_id
										from mytcg_user a
										inner join mytcg_frienddetail b
										on b.friend_id = a.user_id
										left outer join mytcg_usercard d
										on d.user_id = a.user_id
										left outer join mytcg_card c
										on c.card_id = d.card_id
										where b.user_id = ".$user['user_id']."
										group by a.username
					) a
					group by username
					order by val desc";
	$aBoard=myqu($query);
	$iCount = 0;
?>
<div id="credits" >
	<div class="leaderTitle">
		
		<div class="headRecentActivity">
			<span>YOUR</span> FRIENDS
		</div>
		
	</div>	
	<div class="headCreditsBody">
		
		<div class="leaderSect" style="width:543px;margin-left: 13px;margin-top:10px;">
			<div class="leaderLeftArrow"></div>
			<div class="leaderScrolltainer">
				<div class="leaderScrollBox" id="leaderScrollBox" val="<?php echo(sizeof($aBoard)); ?>">
				<?php while ($iCount!=sizeof($aBoard)){
					$picURL = getUserPic($aBoard[$iCount]["usr"]);
					if(!$picURL){
						$picURL = "_site/no-profile.jpg";
					}else{
						$picURL = "http://graph.facebook.com/".$picURL."/picture";
					}
				?>
				<div class="friendBox">
					<img class="friendBoxPic" src="<?php echo($picURL); ?>">
					<div class="friendSpeechBubble"><?php echo($iCount + 1); ?></div>
					&nbsp;<span><?php 
					$username = $aBoard[$iCount]["usr"];
					$username = substr($username, 0,strrpos($username, "@"));
					$username = substr($username, 0, 10);
					echo $username; ?></span><br />&nbsp;Collected:&nbsp;<span><?php echo $aBoard[$iCount]["val"] ?></span>
				</div>
				<?php
					$iCount++;
				}
				?>
				</div>
			</div>
			<div class="leaderRightArrow"></div>
		</div>
		<div id="invite_friend" class="leaderSect">
			<div class="leaderSectAddFriend"></div>
			<div class="segregation" style="top:15px;left:5px;"></div>
	    </div>
	</div>
		
</div>
<script language="JavaScript">
$(document).ready(function(){
	var span_collected = $("#creditAvailable").html();
	var iCount = parseInt(span_collected);
	if (iCount == 0) {
		setTimeout(function() {App.showDid("Did you know?<br/><br/>To get going trade in some facebook credits and get shopping.",1,true);},1000);
	}
	App.getItem(2);
});
</script>