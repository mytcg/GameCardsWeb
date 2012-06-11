<?php
   $user_id = $facebook->getUser();
   $request_ids = explode(',', $_REQUEST['request_ids']);

   //build the full_request_id from request_id and user_id 
   function build_full_request_id($request_id, $user_id) {
      return $request_id . '_' . $user_id; 
   }
   
   foreach ($request_ids as $request_id)
   {
      echo ("reqeust_id=".$request_id."<br>");
      $full_request_id = build_full_request_id($request_id, $user_id);  
      echo ("full_request_id=".$full_request_id."<br>");
  
      try {
         $delete_success = $facebook->api("/$full_request_id",'DELETE');
         if ($delete_success) {
            echo "Successfully deleted " . $full_request_id;}
         else {
           echo "Delete failed".$full_request_id;}
        }          
      catch (FacebookApiException $e) {
      echo "error";}
    }
?>