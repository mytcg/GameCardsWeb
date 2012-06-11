<?php
require_once("configuration.php");
require_once("functions.php");
require_once("fbuser.php");

function TopMenu($need,$have) {
	if ($have >= $need) {
	echo "top-menu-item-active";
	} else {
	echo "top-menu-item"; 
	}
 }
 function fHighlightMenu($page,$mark){
 	if($page == $mark){
 		echo("navMenuItem_active");
 	}
 }

// $user['user_id'] = 92;
// $user['username'] = "Senjiro";
// $user['premium'] = 1865;
// $user['gameswon'] = 2;
// $user['xp'] = 110;
// $user['facebook_process'] = 3;
if($user['premium']==NULL){
	$user['premium'] = 0;
}

$_SESSION['userDetails'] = $user;
if (isset($_GET['page'])) {
	$page_url = $_GET['page'];
 } else {
 	$page_url = "dashboard";
	header("Location: index.php?page=dashboard");
 }
 if(($page_url=="album")||($page_url=="auction")||($page_url=="play")){
	$wideBG = " windowbackground_wide";
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="https://www.facebook.com/2008/fbml">
  <head>
    <title>Mytcg Trading Card Games</title>
    <link rel="icon" href="favicon.ico" />
    <link href="css/stylesheet.css" type="text/css" rel="stylesheet" media="all"  />
    <link href="css/jquery.jscrollpane.css" type="text/css" rel="stylesheet" media="all" />
    <link rel="stylesheet" type="text/css" href="ui/jquery-ui-1.8.13.custom.css" />
	
	<?php
		 $cache_expire = 60*60*24*365;
		 header("Pragma: public");
		 header("Cache-Control: max-age=".$cache_expire);
		 header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
 	?>
    <script src="//connect.facebook.net/en_US/all.js"></script>
    <script type="text/javascript" src="jquery/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="jquery/jquery-ui-1.8.13.custom.min.js"></script>
    <script type="text/javascript" src="jquery/jquery.jscrollpane.min.js"></script>
    <script type="text/javascript" src="jquery/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="jquery/jquery.countdown.min.js"></script>
    <script type="text/javascript" src="jquery/jquery.tools.min.js"></script>
    <script type="text/javascript" src="jquery/jquery.flip.min.js"></script>
    <script type="text/javascript" src="_app/application.js"></script>
  </head>
  <body>
  	<div id="fb-ui-return-data"></div>
    <div id="fb-root"></div>
    
    <div id="divTopMenu">
    	<div class="navSpacer"></div>
    	<a href="index.php?page=dashboard"><div id="mainIconContainer">HOME</div></a>
    	<a href="index.php?page=profile"><div id="play" class="navMenuItem <?php fHighlightMenu($page_url,"profile"); ?>">PROFILE</div></a>
    	<a href="index.php?page=credits"><div id="shop" class="navMenuItem <?php fHighlightMenu($page_url,"credits"); ?>">CREDITS</div></a>
    	<a href="index.php?page=auction"><div id="auction" class="navMenuItem <?php fHighlightMenu($page_url,"auction"); ?>">AUCTION</div></a>
    	<a href="index.php?page=shop"><div id="deck" class="navMenuItem <?php fHighlightMenu($page_url,"shop"); ?>">SHOP</div></a>
    	<a href="index.php?page=album"><div id="album" class="navMenuItem <?php fHighlightMenu($page_url,"album"); ?>">ALBUM</div></a>    	
    	<div id="mainNavIconContainer">
    	</div>
		<div id="creditContainer">
    			<a href="index.php?page=credits">YOU HAVE <span id="creditAvailable"><?php echo $user['premium']; ?></span> CREDITS</a>
		</div>		
    </div>
    
    <div class="divContainer">
      <?php
      if($page_url!=""){ // sanitize this
        require_once("_app/views/".$page_url.".php");
      }
      ?>
    </div>
    <!-- footer -->
    <div id="footer">
    	<div class="official"></div>
    	<div class="poweredby"></div>
    </div>
    <!-- mask for modal window -->
    <?php if($dailyCreds == true){ ?>
    <div id="mask" style="display:block"></div>
    <div class="modal-window" id="credits-modal-window" style="height: 130px;">
    	<div class="closeButtonContainer">
    		<div class="half" id="topHalf"></div>
    		<div class="half" id="bottomHalf"></div>
    		<div class="close-button"></div>
    	</div>
    	<div class="modal-error-text" style="top:45px;left:60px;width: 200px;"><span>Congratulation</span><br />You have received 50 credits for logging in today</div>
    </div>
    <?php } else { ?>
    <div id="mask" style="display:none"></div>
    <?php } ?>
  </body>
</html>