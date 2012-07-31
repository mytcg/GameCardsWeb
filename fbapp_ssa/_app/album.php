<?php
require_once("../configuration.php");
require_once("../functions.php");
require_once("portal.php");

if($_GET['cat']){
   $catID = $_GET['cat'];
  if($catID == "all"){
  	
  	 $query = 'SELECT DISTINCT C.category_id, 
  	 C.description, 
  	 C.card_id, 
  	 I.description AS path, 
  	 C.image AS image 
	 FROM mytcg_card C 
    INNER JOIN mytcg_imageserver I 
    ON (C.front_imageserver_id = imageserver_id) 
	 WHERE C.category_id > 52
    ORDER BY C.description ASC';
	 
  } else {
  	
  	 $query = 'SELECT DISTINCT C.category_id, 
  	 C.description, 
  	 C.card_id, 
  	 I.description AS path, 
  	 C.image AS image 
  	 FROM mytcg_card C 
  	 INNER JOIN mytcg_imageserver I 
  	 ON (C.front_imageserver_id = imageserver_id) 
  	 WHERE C.category_id = '.$catID.'
  	 ORDER BY C.description ASC';
	 
  }
  
  $aCards=myqu($query);
  
  if(sizeof($aCards) > 0){
    echo '<cards>'.$sCRLF;
    echo $sTab.'<count>'.sizeof($aCards).'</count>'.$sCRLF;
        $k = 0;
        foreach($aCards as $card){
          $iCC = getCardOwnedCount($card['card_id'],$_SESSION['userDetails']['user_id']);
          echo $sTab.'<card_'.$k.'>'.$sCRLF;
          echo $sTab.$sTab.'<cardid val="'.$card['card_id'].'" />'.$sCRLF;
          echo $sTab.$sTab.'<cardcount val="'.$iCC.'" />'.$sCRLF;
          echo $sTab.$sTab.'<cardname val="'.$card['description'].'" />'.$sCRLF;
          echo $sTab.$sTab.'<path val="'.$card['path'].'" />'.$sCRLF;
          echo $sTab.$sTab.'<image val="'.$card['image'].'" />'.$sCRLF;
          echo $sTab.'</card_'.$k.'>'.$sCRLF;
          $k++;
        }
        echo '</cards>'.$sCRLF;
  }
  exit;
}
?>