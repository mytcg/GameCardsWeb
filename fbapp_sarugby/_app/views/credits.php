<?php
$sql = "SELECT premium
		FROM mytcg_user
		WHERE user_id = ".$user['user_id'];
$iCredits = myqu($sql);
$iCredits = $iCredits[0]['premium'];
if($iCredits==null){
	$iCredits = 0;
}

$sql = "SELECT * FROM mytcg_transactionlog
		WHERE user_id={$user['user_id']}
		AND (transactionstatus_id=2 OR transactionstatus_id IS NULL)
		ORDER BY transaction_id DESC LIMIT 20";
$aTransactions = myqu($sql);
?>
<div id="header" >
	<div class="headTitle">
		<div class="headCredits">
			<span>YOUR</span> CREDITS
		</div>
	</div>
	<div class="creditsBody">
		<div class="creditsAmount">
			<div class="cTitle"><span>Current</span> Amount</div>
			<div class="cAmount"><?php echo($iCredits); ?></div>
			<div class="cCredits">Credits</div>
		</div>
		<div class="creditsGateWindows">
			<div class="creditsBuyHeader"><span>Buy</span> Credits</div>
			<div id="buy350" class="buyFBcredits creditIcon1" style="left:45px;"></div>
			<div id="buy700" class="buyFBcredits creditIcon2" style="left:220px;"></div>
			<div id="buy1400" class="buyFBcredits creditIcon3" style="left:390px;"></div>
			<div class="creditsResponse">Your details have been <span>saved successfully</span>.</div>
    	</div>
		<div class="creditsTransaction"><span>Transaction</span> Logs</div>
		<div class="creditsRheaders">
			<div style="width:170px;">Date</div>
			<div style="width:450px;">Description</div>
			<div style="width:85px;">Amount</div>
		</div>
		<div id="creditsScroller" class="creditsScroller" style="width:762px;height:259px;">
			<?php
			foreach($aTransactions as $sData){
				$c = ($c=="")? "credRDark" : "";
			?>
			<div class="credRow <?php echo($c); ?>">
				<div style="width:170px;"><?php echo($sData['date']); ?></div>
				<div style="width:450px;"><?php echo($sData['description']); ?></div>
				<div style="width:85px;"><span><?php echo($sData['val']); ?></span> TCG</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<script language="JavaScript">
$(document).ready(function(){
	//Globals
	var sMethod = "";
	var sAmount = "";
	
	//Active scrolling on credits log
	$('#creditsScroller').jScrollPane();
	
	
	
	
});
</script>
