	<ul id="item_list">
		<li><a href="#"><p>Create New Auction</p></a></li>
		<?php
			$query= "SELECT CA.description AS 'category', C.description, C.category_id, UC.usercard_id, UC.card_id, M.market_id,
					(SELECT COUNT(usercard_id) FROM mytcg_usercard WHERE user_id=".$user['user_id']." AND card_id=UC.card_id AND usercardstatus_id=1) AS 'owned'
					FROM mytcg_market M
					JOIN mytcg_usercard UC USING (usercard_id)
					JOIN mytcg_card C USING (card_id)
					JOIN mytcg_category CA ON C.category_id = CA.category_id
					WHERE M.markettype_id = 1 AND M.marketstatus_id = 1
					GROUP BY C.category_id
					ORDER BY C.category_id ASC";
			$aAuctions=myqu($query);
			$iCount = 0;

		while ($iCat=$aAuctions[$iCount]['category_id']){
	    	echo "<li><a href='index.php?page=auction_card&category_id=".$iCat."'><p>".$aAuctions[$iCount]['category']. " </p></a></li>";
			$iCount++;
		}
		?>
	</ul>