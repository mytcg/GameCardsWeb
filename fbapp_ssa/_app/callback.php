<?php

require_once("../configuration.php");
require_once("../functions.php");


  // Enter your app information below
  $api_key = '173115312813690';
  $secret = '5976d79461bfd3c1c96993694da72764';

// prepare the return data array
$data = array('content' => array());

// parse signed data
$request = parse_signed_request($_REQUEST['signed_request'], $secret);

if ($request == null) {
  // handle an unauthenticated request here
}

$payload = $request['credits'];

// retrieve all params passed in
$func = $_REQUEST['method'];
$order_id = 0;
$order_id = $_REQUEST['order_id'];

if ($func == 'payments_status_update') {
  $status = $payload['status'];

  // write your logic here, determine the state you wanna move to
  if ($status == 'placed') {
	$next_state = 'settled';
	$data['content']['status'] = $next_state;

	$buyer_info = json_decode($request['credits']['order_details'], true);
	$buyer_id = $request['user_id'];
	$item = $buyer_info['items'][0];
	
	$order_id = json_decode($request['credits']['order_id'], true);
	
	if ($item['item_id'] == "350") {
		$val = 350;
	} else if ($item['item_id'] == "700") {
		$val = 700;
	} else if ($item['item_id'] == "1400") {
		$val = 1400;
	}
	
	
	$description = "Purchased ".$item['description']." for ".$item['price']." Facebook Credits." ;
	
	$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where facebook_user_id = '".$buyer_id."'),'".$description."', now(), ".$item['item_id'].", 5, 2, 'Settled', 2, ".$buyer_id.", ".$order_id.")";
	
	myqu($query);
	
	myqu("INSERT INTO tcg_transaction_log (fk_user, fk_boosterpack, fk_usercard, fk_card, transaction_date, description, tcg_credits, tcg_freemium, tcg_premium, fk_payment_channel, application_channel, mytcg_reference_id, fk_transaction_type, order_id)
		VALUES((select user_id from mytcg_user where facebook_user_id = '".$buyer_id."'), NULL, NULL, NULL, 
			now(), '".$description."', ".$item['item_id'].", 0, ".$item['item_id'].", 5, 'facebook',  (SELECT max(transaction_id) FROM mytcg_transactionlog WHERE user_id = (select user_id from mytcg_user where facebook_user_id = '".$buyer_id."')), 15, '".$order_id."')");
	
	$query = "update mytcg_user set premium=ifnull(premium,0)+".$item['item_id']." where facebook_user_id = '".$buyer_id."'";
	
	myqu($query);
	
  } else if ($status == 'canceled') {
	$buyer_info = json_decode($request['credits']['order_details'], true);
	$buyer_id = $buyer_info['buyer'];
	$item = $buyer_info['items'][0];
	
	$order_id = $buyer_info['order_id'];
	
	if ($item['item_id'] == "350") {
		$val = 350;
	} else if ($item['item_id'] == "700") {
		$val = 700;
	} else if ($item['item_id'] == "1400") {
		$val = 1400;
	}
	
	$description = "Purchased ".$item['description']." for ".$item['price']." Facebook Credits." ;
	$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where facebook_user_id = '".$buyer_id."'),'".$description."', now(), ".$item['item_id'].", 5, 4, 'Cancelled', 2, ".$buyer_id.", ".$order_id.")";
	myqu($query);
	
	/*$query = "update mytcg_user set premium=ifnull(premium,0)+1 where facebook_user_id = '".$buyer_id."'";
	myqu($query);*/
	
  }
  // compose returning data array_change_key_case
  $data['content']['order_id'] = $order_id;
} else if ($func == 'payments_get_items') {
  
    // remove escape characters  
	$order_info = json_decode($request['credits']['order_info'], true);
	$item_id = $order_info['item_id'];
	$buyer_id = json_decode($request['credits']['buyer'], true);
	//$order_id =json_decode($request['credits']['order_id'], true);
	
	/*$order_info = stripcslashes($payload['order_info']);
    $item_info = json_decode($order_info, true);
	$item_id = $order_info['item_id'];*/
	
    //Per the credits api documentation, 
    //you should pass in an item reference
    // and then query your internal DB for the proper 
    //information. Then set the item 
    //information here to be returned to facebook 
    //then shown to the user for confirmation.
    if ($item_id == "350") {
	 $item['item_id'] = 350;	
     $item['title'] = '350 Credits';
     $item['price'] = 10;
     $item['description']='350 TCG Credits';
     $item['image_url']='https://sarugbycards.com/fbapp/_site/350.png';
     $item['product_url']='https://sarugbycards.com/fbapp/_site/350.png';
    }
	
	if ($item_id == "700") {
	$item['item_id'] = 700;	
     $item['title'] = '700 Credits';
     $item['price'] = 20;
     $item['description']='700 TCG Credits';
     $item['image_url']='https://sarugbycards.com/fbapp/_site/700.png';
     $item['product_url']='https://sarugbycards.com/fbapp/_site/700.png';
    }

	if ($item_id == "1400") {
	$item['item_id'] = 1400;	
     $item['title'] = '1400 Credits';
     $item['price'] = 40;
     $item['description']='1400 TCG Credits';
     $item['image_url']='https://sarugbycards.com/fbapp/_site/1400.png';
     $item['product_url']='https://sarugbycards.com/fbapp/_site/1400.png';
    }
	
	$description = "Purchased ".$item['description']." for ".$item['price']." Facebook Credits." ;
	$query = "insert into mytcg_transactionlog (user_id, description, date, val, transactiontype_id, transactionstatus_id, response, transactionlogtype_id, facebook_user_id, order_id) values ((select user_id from mytcg_user where facebook_user_id = '".$buyer_id."'),'".$description."', now(), ".$item['item_id'].", 5, 1, 'Placed', 2, ".$buyer_id.", ".$order_id.")";
	
	//myqu($query);
	
	
    //for url fields, if not prefixed by http:,
    //prefix them
    /*$url_key = array('product_url', 'image_url');  
    foreach ($url_key as $key) {
      if (substr($item[$key], 0, 7) != 'http://') {
        $item[$key] = 'http://'.$item[$key];
      }
    }*/
    $data['content'] = array($item);
}

  // required by api_fetch_response()
  $data['method'] = $func;
  echo json_encode($data);
?>
