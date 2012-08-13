<?php
require_once("configuration.php");
require_once("functions.php");
if(!$localhost){
	require_once("fbuser.php");
}

$ie = ieversion();
if(($ie < 9)&&($_SESSION['stable']!=true)){
	$_SESSION['stable'] = true;
	header("Location:browser_error.php");
}

if(!$_SESSION['auctions']){
	require_once("process_auctions.php");
}

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
if($localhost){
	$user['user_id'] = 2;
	$user['username'] = "cole@mytcg.net";
	$user['credits'] = 60;
	$user['premium'] = 300;
	$user['gameswon'] = 0;
	$user['xp'] = 0;
	$_SESSION['userProfile'] = "690746721";
	$_SESSION['userDetails']['user_id'] = 2;
}

if($user['premium']==NULL){
	$user['premium'] = 0;
}
$user['premium'] = $user['premium'] + $user['credits'];

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
    			<a href="index.php?page=credits"><Span>You</span> have <span id="creditAvailable"><?php echo $user['premium']; ?></span> credits</a>
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
    <div id="mask" style="display:none"></div>
  </body>
</html>