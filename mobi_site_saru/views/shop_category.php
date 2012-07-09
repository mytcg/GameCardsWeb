<?php
$iUserID = $user['user_id'];
$boosterid = $_GET['product_id'];
	
	$query = 'SELECT (ifnull(premium,0)+ifnull(credits,0)) credits, credits cred, premium prem FROM mytcg_user WHERE user_id='.$iUserID;
	$aCreditsVal=myqu($query);
	
	$qu = 'SELECT A.card_id, count(*) quantity, B.image, A.usercard_id,  B.value, 
		B.description, B.thumbnail_phone_imageserver_id, B.front_phone_imageserver_id, B.back_phone_imageserver_id, B.ranking, D.description quality,
		0 updated, D.note, D.date_updated  
		FROM mytcg_card B 
		INNER JOIN mytcg_usercard A 
		ON A.card_id=B.card_id 
		INNER JOIN mytcg_cardquality D
		ON B.cardquality_id=D.cardquality_id
		INNER JOIN mytcg_usercardstatus C 
		ON C.usercardstatus_id=A.usercardstatus_id 
		LEFT OUTER JOIN 
		(SELECT note, date_updated, user_id, card_id
			FROM mytcg_usercardnote
			WHERE user_id = '.$iUserID.'
			AND usercardnotestatus_id = 1
		) D 
		ON A.user_id = D.user_id 
		AND A.card_id = D.card_id 
		INNER JOIN 
		(SELECT c.card_id 
			FROM mytcg_product a, mytcg_productcard b, mytcg_card c, mytcg_cardquality d 
			WHERE a.product_id = b.product_id 
			AND c.card_id = b.card_id 
			AND d.cardquality_id = c.cardquality_id 
			AND a.product_id = '.$boosterid.'
		) E
		ON E.card_id = B.card_id
		WHERE A.user_id='.$iUserID.'
		AND C.usercardstatus_id=1 	
		GROUP BY B.card_id 
		UNION 
		SELECT B.card_id, 0, B.image, 0,  B.value, 
					B.description, B.thumbnail_phone_imageserver_id, "", "", B.ranking, D.description quality, 
					0, "", 0 
		FROM mytcg_card B 
		INNER JOIN mytcg_cardquality D
		ON B.cardquality_id=D.cardquality_id
		INNER JOIN 
		(SELECT c.card_id 
			FROM mytcg_product a, mytcg_productcard b, mytcg_card c, mytcg_cardquality d 
			WHERE a.product_id = b.product_id 
			AND c.card_id = b.card_id 
			AND d.cardquality_id = c.cardquality_id 
			AND a.product_id = '.$boosterid.'
		) E
		ON E.card_id = B.card_id
		WHERE B.card_id NOT IN (SELECT uc.card_id from mytcg_usercard uc, mytcg_usercardstatus ucs 
			where uc.user_id = '.$iUserID.' and uc.usercardstatus_id = ucs.usercardstatus_id and ucs.usercardstatus_id=1) 
		GROUP BY B.card_id 
		ORDER BY description';
					
		$cardList=myqu($qu);
		$iCount=0;
		foreach ($cardList as $cardInList[$iCount]){ ?>
		<div class="album_card_pic"><img src="http://sarugbycards.com/img/cards/jpeg/<?php echo($cardInList[$iCount]['image']); ?>_web.jpg" width="64" height="90" title="View potential cards"></div>
		<?php
		$iCount++;
		}
		?>
		<div id="button">Buy</div>		



