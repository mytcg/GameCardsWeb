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
	$user['user_id'] = 6284;
	$user['username'] = "cole@mytcg.net";
	$user['credits'] = 865;
	$user['premium'] = 865;
	$user['gameswon'] = 2;
	$user['xp'] = 110;
	$user['facebook_process'] = 3;
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
  	
    <title>MyTCG Trading Card Games</title>
    
    <link rel="icon" href="favicon.ico" />
    <link href="css/jquery.jscrollpane.css" type="text/css" rel="stylesheet" media="all" />
    <link href="ui/jquery-ui-1.8.13.custom.css"  rel="stylesheet" type="text/css"/>
    <link href="css/stylesheet.css" type="text/css" rel="stylesheet" media="all"  />
	
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
    <div id="fb-root"></div>
    <div id="divTopMenu">
    	<div class="navSpacer"></div>
    	<a href="index.php?page=dashboard"><div id="mainIconContainer"></div></a>
    	<a href="index.php?page=play"><div id="play" class="navMenuItem <?php fHighlightMenu($page_url,"play"); ?>">PLAY</div></a>
	    <a href="index.php?page=credits"><div id="shop" class="navMenuItem <?php fHighlightMenu($page_url,"credits"); ?>">CREDITS</div></a>    	
    	<a href="index.php?page=shop"><div id="shop" class="navMenuItem <?php fHighlightMenu($page_url,"shop"); ?>">SHOP</div></a>
    	<a href="index.php?page=auction"><div id="auction" class="navMenuItem <?php fHighlightMenu($page_url,"auction"); ?>">AUCTION</div></a>
    	<a href="index.php?page=deck"><div id="deck" class="navMenuItem <?php fHighlightMenu($page_url,"deck"); ?>">DECK</div></a>
    	<a href="index.php?page=album"><div id="album" class="navMenuItem <?php fHighlightMenu($page_url,"album"); ?>">ALBUM</div></a>    	
    	<div id="mainNavIconContainer">
    		<div id="dateLastVisited">
    		<?php 
	
			$user_id = $_SESSION['userDetails']['user_id'];
			
			$query = "SELECT 
			IF (
				(mobile_date_last_visit > date_last_visit) && (mobile_date_last_visit IS NOT NULL),
				mobile_date_last_visit,
				IF (
					date_last_visit IS NULL,
					mobile_date_last_visit,
					date_last_visit
				)
			),
				mobile_date_last_visit,
				date_last_visit
			FROM 
				mytcg_user
			WHERE 
				user_id = $user_id;";
						
			$result = myqu($query);
			$_most_recent_visit_time = $result[0][0];
			$_most_recent_visit_time = strtotime($_most_recent_visit_time); 
			
			$_most_recent_visit_time = date('m/d',$_most_recent_visit_time);
			echo $_most_recent_visit_time;

			?>
			</div>
			<div id="creditContainer">
    			<a href="index.php?page=credits"><span id="creditAvailable"><?php echo $user['premium']; ?></span></a>
			</div>
			<a href="index.php?page=profile" title="profile" alt="profile"><div id="userIcon"></div></a>
    	</div>
    	<div class="lineLeft"></div>
    	<div class="lineRight"></div>
    </div>
    
    <div class="divContainer">
      <?php
      if($page_url!=""){ // sanitize this
        require_once("_app/views/".$page_url.".php");
      }
      ?>
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