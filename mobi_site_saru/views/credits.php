<?php
	$iUserID = $user['user_id'];
	$qu = 'SELECT (ifnull(premium,0)+ifnull(credits,0)) premium, ifnull(premium,0) prem, ifnull(credits,0) cred FROM mytcg_user WHERE user_id ='.$iUserID;
	$aCreditsVal=myqu($qu);
	
    $sql = "SELECT TL.* 
    		FROM mytcg_transactionlog AS TL
            WHERE user_id=".$iUserID."
            AND (TL.transactionstatus_id=2 OR TL.transactionstatus_id IS NULL)
            ORDER BY TL.transaction_id DESC LIMIT 6";
    $aCredits=myqu($sql);
    $iCount=0;

?>	
	<div><p>Need some more credits? <a href="?page=credits_buy">Click here</a></p></div>
	<ul id="item_list">
	<li style="text-align:center;"><p>Your Credit Balance:</p></li>
	</ul>
	<div class="info_textbox">
		<div class="info_box"><?php echo($aCreditsVal[$iCount]['premium']); ?></div>
		<ul id="item_list">
			<li style='text-align:center;'><p>Transactions:</p></li>
		</ul>
	</div>		
	<ul id="item_list">
		<?php
		    while($iCreditID=$aCredits[$iCount]['transaction_id']){
			echo "<li style='text-align:left;'><a><p style='padding-top:0px;'>".$aCredits[$iCount]['date']."&nbsp;-&nbsp;".$aCredits[$iCount]['description']. "</p></a></li>";
   			$iCount++;
			}
		?>
	</ul>