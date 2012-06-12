<?php 
/* Include PanaceaApi class */
require_once("panacea_api.php");

$api = new PanaceaApi();
$api->setUsername("donald");
$api->setPassword("abc123");

$result = $api->message_send("0832659217", "Hello", "27832659217");

if($api->ok($result)) {
	echo "Message sent! ID was {$result['details']}\n";	
} else {
	/* There was an error */
	echo $api->getError();
}

?>