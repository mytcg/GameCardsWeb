<?php
/**
 * Mobidex
 * user ajax script
 * 
 * @author Jaco Horn <jaco@mytcg.net>
 * @version 1.0
 * @package user
 */

session_start();
include_once('../include/system.inc.php');


//AJAX ACTIONS

if(isset($_POST['action']))
{
   $action = $_POST['action'];
   switch($action)
   {
   	  case 'edit':
         
         $card_id = $_POST['card'];
         
         editCard($card_id);
         
         break;
		 
      case 'deletealbum':
         
         $deck_id = $_POST['id'];
         
         deleteAlbum($deck_id);
         
         break;
      
      case 'addalbum':
         
         $description = $_POST['description'];
         
         addAlbum($description);
         
         break;
      
      case 'getalbums':
         
         echo getAlbums();
         
         break;
      
      case 'deletenote':
         
         $usercardnote_id = $_POST['noteid'];
         
         deleteUsercardNote($usercardnote_id);
         
         break;
      
      case 'getnotes':
         
         $usercard_id = $_POST['usercard'];
         
         echo getUsercardNotes($usercard_id);
         
         break;
      
      case 'addnote':
         
         $usercard_id = $_POST['usercard'];
         $note = $_POST['note'];
         
         addNoteToCard($usercard_id, $note);
         
         break;
      
      
      
      case 'getcategories':
      
         $categories = getCategories();
         
         echo $categories;
         
         exit;
         
         break;
      
      
      
      case 'saveimage':
         
         $pro = $_POST['pro'];
         $get = $_POST['getstring'];
         $side = $_POST['side'];
         $image = $_POST['image'];
         $src = '';
         $dir = '';

         $ext = getExtension($image);
         
         if($pro == '1')
         {
            if($_SERVER['HTTP_HOST']=='localhost')
            {
               $src = 'http://localhost/mobidex/generate_image_pro.php'.$get;
               $dir = 'C:/wamp/www/mobidex/img/temp/';
            }
            else
            {
               $src = 'http://'.$_SERVER['HTTP_HOST'].'/generate_image_pro.php'.$get;
               $dir = $_SERVER['DOCUMENT_ROOT'].'/img/temp/';
            }
         }
         else
         {
            if($_SERVER['HTTP_HOST']=='localhost')
            {
               $src = 'http://localhost/mobidex/generate_image.php'.$get;
               $dir = 'C:/wamp/www/mobidex/img/temp/';
            }
            else
            {
               $src = 'http://'.$_SERVER['HTTP_HOST'].'/generate_image.php'.$get;
               $dir = $_SERVER['DOCUMENT_ROOT'].'/img/temp/';
            }
         }
            
         //echo $src.CRLF;
         $filename = date('YmdHis').'_'.$side;
         //$dst = $dir.$filename.'.'.$ext;
         $dst = $dir.$filename.'.jpg';
         
          /**
          * Initialize the cURL session
          */
          $ch = curl_init();
         
          /**
          * Set the URL of the page or file to download.
          */
         
          curl_setopt($ch, CURLOPT_URL, $src);
         
          /**
          * Create a new file
          */
          $fp = fopen($dst, 'w');
         
          /**
          * Ask cURL to write the contents to a file
          */
          curl_setopt($ch, CURLOPT_FILE, $fp);
         
          /**
          * Execute the cURL session
          */
          curl_exec ($ch);
         
          /**
          * Close cURL session and file
          */
          curl_close ($ch);
          fclose($fp);

         //echo $filename.'.'.$ext;
         echo $filename.'.jpg';
         
         break;
         
         
      case 'deleteusercard':
         
         $id = $_POST['id'];
         
         deleteUserCard($id);
         
         exit;
         
         break;
         
         
      case 'deletecard':
         
         $id = $_POST['id'];
         
         deleteCard($id);
         
         exit;
         
         break;
         
      
      
      case 'getusercards':
         
         printUserCards(true);
         
         break;
      
      
      case 'movecard':
         
         $usercard_id = $_POST['card'];
         $deck_id = $_POST['deck'];
         
         moveUsercard($usercard_id, $deck_id);
         
         break;
      
      
      case 'savecard':
         
         //echo print_r($_POST,true);exit;
         $description = $_POST['description'];
         $orientation = $_POST['orientation'];
         $imageFront = $_POST['imagefront'];
         $imageBack = $_POST['imageback'];
         $fieldsFront = $_POST['fieldsfront'];
         $fieldsBack = $_POST['fieldsback'];
         $searchTags = $_POST['searchtags'];
         $username = $_POST['user'];
		 $cardtype = $_POST['cardtype'];
		 $template = $_POST['template'];
         
         //prepare front fields
         $aFieldsFront = array();
         if(is_array($fieldsFront) && sizeof($fieldsFront) > 0)
         {
            foreach($fieldsFront as $field)
            {
               $aFieldsFront[] = explode('|', $field);
            }
         }
         
         //prepare back fields
         $aFieldsBack = array();
         if(is_array($fieldsBack) && sizeof($fieldsBack) > 0)
         {
            foreach($fieldsBack as $field)
            {
               $aFieldsBack[] = explode('|', $field);
            }
         }
         
         //print_r($aFieldsFront);print_r($aFieldsBack);exit;
         
         //save card details in database
         if( saveCard($description, $orientation, $imageFront, $imageBack, $aFieldsFront, $aFieldsBack, $searchTags, $pro, $cardtype, $template, $username) )
         {
            echo '1';
         }
         else
         {
            echo CRLF.'*** ERROR: Your card was not saved. ***';
         }
         
         
         break;
      
      
      case 'createimage':
         
         echo print_r($_POST,true);exit;
                  
         //createImage($filename);
         
         break;
      
      
      
      case 'login':
         
         $u = $_POST['username'];
         $p = $_POST['password'];
         
         if(authenticateUser($u, $p))
         {
            echo '1';
            exit;
         }
         else
         {
            echo 'Invalid username and/or password. Please try again.';
         }
         exit;
         break;
      
      
      
      case 'logout':
         
         //set user session offline
         session_unset('user');
         session_unset('username');
         
         echo '1';
         
         exit;
         break;
      
      
      
      case 'register':
         
         $name = trim($_POST['name']);
         $surname = trim($_POST['surname']);
         $username = preg_replace("/[^A-Za-z0-9]/", "", $name.$surname);
         
         $aDetails['username'] = $username;
         $aDetails['name'] = $name.' '.$surname;
         $aDetails['password'] = $_POST['password']; 
         $aDetails['confirm'] = $_POST['confirm']; 
         $aDetails['email'] = $_POST['email'];
		 $aDetails['ccode'] = $_POST['ccode'];
         $aDetails['mobile'] = $_POST['mobile'];
		 $aDetails['country'] = $_POST['country'];
         $aDetails['pro'] = $_POST['pro'];
         
         if($result = registerUser($aDetails))
         {
            echo $result;
            exit;
         }
         
         break;
      
      
      
	  case 'tradelist':
         getTradeCards($_POST['tradecard']);
         exit;
      break;
   
   
      case 'getsubtrack':
         $card_id = $_POST['card'];
         $user_id = $_POST['user'];
         $aTracks = getCardSubTracking($card_id, $user_id);
         if(sizeof($aTracks) > 0)
         {
            $counter = 0;
            foreach($aTracks as $track)
            {
               $counter++;
               $detail = $track['detail'];
               if($track['username']!=NULL)
               {
						$detail = $track['username'].' ('.$track['detail'].')';
               }
               
               $title = 'Sent: '.$track['date'].' - Note: '.$track['note'];
               $c = strtolower(substr($detail,0,1));
            
               //check for lower level sharing
               $subnumber = '';
               $user_id == '';
               if($track['receiver']!=NULL)
               {
                  $aSubTracking = getCardSubTracking($card_id,$track['receiver']);
                  if(sizeof($aSubTracking) > 0)
                  {
                     $subnumber = '<div class="number">'.sizeof($aSubTracking).'</div>';
                  }
                  $user_id = $track['receiver'];
               }
               
               $thumb = 'img/cards/'.$card_id.'_thumb.jpg';
               $last = ($counter >= sizeof($aTracks)) ? ' last' : '';
               echo $trackingData[] = <<<STR
               <div class="track-item{$last}" id="{$track['tradecard_id']}" alt="{$card_id}">
                  <input type="hidden" class="user_id" value="{$user_id}" />
                  <div class="thumb" style="background:url({$thumb}) center center no-repeat;"></div>
                  <p title="{$title}">{$detail}</p>
                  {$subnumber}
               </div>
STR;
            }
         }
         break;
      
      
      case 'send':
         
         print_r($_POST);
         
         exit;
         break;
      
      default:
      
         //
   }
}

?>