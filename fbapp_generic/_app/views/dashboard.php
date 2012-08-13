<div id="header" >
	<div class="headTitle">
		<div class="lineGrey" style="width:315px;margin-left: 30px;margin-right: 15px;"></div>
		<div class="headDashboard"></div>
		<div class="lineGrey" style="width:30px;margin-left: 10px;"></div>
	</div>
	<div class="headBody">
		<div class="headCreditsDash">
			<span class="creditsText"><?php echo( $user['premium']); ?></span>Credits
		</div>
		<div class="headBlocks">
			<div class="headMiniTitle">Cards Collected</div>
			<?php $cardsCollected = getCardInAlbumCount($_SESSION['userDetails']['user_id']); ?>
			<div class="headMiniText"><span style="color:#FFF;"><?php echo $cardsCollected[0]; ?></span>/<?php echo $cardsCollected[1]; ?></div>
			<div class="segregation" style="top:55px;left:140px;"></div>
		</div>
		<div class="headBlocks">
			<div class="headMiniTitle">Best Card</div>
			<div class="headBestCard">
				<?php $bestCard = getBestCard($_SESSION['userDetails']['user_id']); ?>
				<img id="" src="http://www.mytcg.net/img/cards/<?php echo($bestCard[0]['image']);  ?>_web.jpg" width="64" height="90" />
			</div>
		</div>
		<div class="headBlocks">
			<div class="headMiniTitle">Album Strength</div>
			<?php $albumStrength = getUserAlbumStrength($_SESSION['userDetails']['user_id']); ?>
			<div class="headMiniText"><span style="color:#FFF;"><?php echo $albumStrength[0][0]; ?></span></div>
			<div class="segregation" style="top:55px;left:-20px;"></div>
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
		<div class="lineGreySmaller" style="width:40px;margin-left: 30px;margin-right: 15px;"></div>
		<div class="headLeaderboard"></div>
		<div class="lineGreySmaller" style="width:458px;margin-left: 10px;"></div>
	</div>
	<div class="headLeaderBody">
		<div class="headLeaderModes">
			<div class="leaderBox leaderBoxActive" id="1">Richest</div>
			<div class="leaderBox" id="2">Games Won</div>
			<div class="leaderBox" id="3">Games Lost</div>
			<div class="leaderBox" id="4">Cards Collected</div>
			<div class="leaderBox" id="5">Most Cards</div>
		</div>
		<div class="leaderSect leaderSectLeft">
			<span class="leaderSel">Overall</span><!-- <br /><span class="leaderDesel">Friends</span> -->
			<div class="segregation" style="top:5px;left:90px;"></div>
		</div>
		<div class="leaderSect" style="width:543px;margin-left: 13px;margin-top:10px;">
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
					&nbsp;<span><?php echo(substr($aBoard[$iCount]["usr"], 0,7)); ?></span><br />&nbsp;<?php echo($aBoard[$iCount]["val"]); ?>
				</div>
				<?php
				$iCount++;
				}
				?>
				</div>
			</div>
			<div class="leaderRightArrow"></div>
		</div>
		<div class="leaderSect" id="addFriend">
			<div class="leaderSectAddFriend"></div>
			<div class="segregation" style="top:5px;left:5px;"></div>
		</div>
	</div>
</div>
<div id="credits" >
	<div class="leaderTitle">
		<!-- <div class="lineGreySmaller" style="width:40px;margin-left: 30px;margin-right: 15px;"></div>
		<div class="headCreditsTitle"></div> -->
		<div class="lineGreySmaller" style="width:417px;margin-left: 10px;margin-right: 15px;"></div>
		<div class="headRecentActivity"></div>
		<div class="lineGreySmaller" style="width:100px;margin-left: 10px;"></div>
	</div>	
	<div class="headCreditsBody">
		<div class="creditsBody">
				<div id="buy350" class="buyFBcredits creditIcon1" style="top:30px;left:10px;"></div>
				<div id="buy700" class="buyFBcredits creditIcon2" style="top:30px;left:120px;"></div>
				<div id="buy1400" class="buyFBcredits creditIcon3" style="top:30px;left:240px;"></div>
				<div class="creditsResponse">Your details have been <span>saved successfully</span>.</div>
		</div>
		<div class="activityBody">
			<div class="activityLeftArrow"></div>
			<div class="activityScroll">
				<div id="activityMove" style="width:900px;position: absolute;">
				<?php
					$sql = "SELECT U.username AS 'owner', I.description AS 'imageserver', CA.description AS 'category', C.description, C.image, C.category_id, UC.usercard_id, UC.card_id, M.*
					FROM mytcg_market M
					JOIN mytcg_usercard UC USING (usercard_id)
					JOIN mytcg_card C USING (card_id)
					JOIN mytcg_imageserver I ON C.back_imageserver_id = I.imageserver_id
					JOIN mytcg_category CA ON C.category_id = CA.category_id
					JOIN mytcg_user U ON M.user_id = U.user_id
					WHERE M.markettype_id = 1 AND M.marketstatus_id = 1
					ORDER BY M.market_id DESC
					LIMIT 9";
					$aAllCards = myqu($sql);
					foreach($aAllCards as $card)
					{
						$sql = "SELECT MC.*, U.username, U.name
								FROM mytcg_marketcard MC
								JOIN mytcg_user U USING (user_id)
								WHERE MC.market_id = ".$card['market_id']."
								ORDER BY MC.price DESC
								LIMIT 1";
						$aHistory = myqu($sql);
						if(sizeof($aHistory)>0){
							$val = $aHistory[0]["price"];
							$type = "Last Bid";
						}else{
							$val = $card['minimum_bid'];
							$type = "Opening Bid";
						}
				?>
				<div class="activityColumb">
					<span><?php echo(substr($card['category'], 0, 9)); ?></span><br />
					<img src="<?php echo($card['imageserver']); ?>cards/<?php echo($card['image']); ?>_web.jpg" class="imgActivity" width="" height="" /><br />
					<span style="line-height:20px;font-size:10px;"><?php echo($type); ?></span><br />
					<span style="line-height:20px;color:#FFF;"><?php echo($val); ?></span> TCG
				</div>
				<?php
					}
				?>
				</div>
			</div>
			<div class="activityRightArrow"></div>
		</div>
	</div>
</div>
<script language="JavaScript">
// $(document).ready(function(){
	// var span_collected = $("#creditAvailable").html();
	// var iCount = parseInt(span_collected);
	// if (iCount == 0) {
		// setTimeout(function() {App.showDid("Did you know?<br/><br/>To get going trade in some facebook credits and get shopping.",1,true);},1000);
	// }
	// App.getItem(2);
// });
</script>