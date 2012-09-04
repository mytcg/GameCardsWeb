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
			<?php $cardOfDay = getCardOfDay($_SESSION['userDetails']['user_id']); ?>
			<div class="headCardOfDay" id="<?php echo $cardOfDay[0][1];  ?>">
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
			<?php $bestCard = getBestCard($_SESSION['userDetails']['user_id']); ?>
			<div class="headBestCard" id="44">
				<img id="" src="https://sarugbycards.com/img/cards/44_web.jpg" width="64px" height="90px" />
			</div>
			<div class="segregation" style="top:20px;left:0px;"></div>
		</div>
	</div>
</div>
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
			<div style="margin-left:50px;"></div>
			<div id="trophies">
				<div class="leaderLeftArrow"></div>
				<div id="achiev">
					<div id="scrollAchi">
	<?php
	$achiList = ("SELECT A.id AS achievement_id, CONCAT(I.description , 'achi/' , A.incomplete_image) AS imageurl
				  FROM mytcg_achievement A
				  INNER JOIN mytcg_imageserver I ON (I.imageserver_id = A.imageserver_id)");
	$achiList = myqu($achiList);
	for($i=0;$i<sizeof($achiList);$i++){
		$achiID = $achiList[$i]['achievement_id'];
		$achiQu = ("SELECT UAL.progress, UAL.date_completed, UAL.date_updated, AL.id AS achievementlevel_id, AL.target, CONCAT(I.description , 'achi/' , AL.complete_image) AS imageurl
					FROM mytcg_achievementlevel AL
					INNER JOIN mytcg_userachievementlevel UAL ON (UAL.achievementlevel_id = AL.id)
					INNER JOIN mytcg_imageserver I ON (I.imageserver_id = AL.imageserver_id)
					WHERE AL.achievement_id = {$achiID} AND UAL.user_id = ".$_SESSION['userDetails']['user_id']."
					ORDER BY AL.target ASC");
		$achiQuery = myqu($achiQu);
		
		$imgComplete = $achiList[$i]['imageurl'];
		for($a=0;$a<sizeof($achiList);$a++){
			$target = intval($achiQuery[$a]['target']);
			$progress = intval($achiQuery[$a]['progress']);
			
			if($progress >= $target && $target > 0){
				$imgComplete = $achiQuery[$a]['imageurl'];
			}
		}
		
		echo("<div id='{$achiID}' class='achiev_holder'><img src='{$imgComplete}' border='0' /></div>");
	}
	?>
					</div>
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
	$query = "select a.user_id, IFNULL(a.name, SUBSTRING_INDEX(a.username, '@', 1)) as username, cnt as val, a.username as usr,a.facebook_user_id as fbid
						from
					(
										select distinct a.username, a.name, a.user_id, count(distinct d.card_id) as cnt, a.facebook_user_id
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
			<div class="inviteFriend" style="margin-left: 10px; margin-top: 19px;"><span>Invite</span> Friend</div>
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
	
	var iScroll = 0;
	var iMax = 2;
	$(".leaderRightArrow").click(function(){
		if(iScroll < iMax){
			$("#scrollAchi").animate({left:"-=555"},500);
			iScroll++;
		}
	});
	
	$(".leaderLeftArrow").click(function(){
		if(iScroll > 0){
			$("#scrollAchi").animate({left:"+=555"},500);
			iScroll--;
		}
	});
});
</script>