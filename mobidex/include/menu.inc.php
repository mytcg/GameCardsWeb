<div class="left"></div>
<div class="right"></div>
<div class="menu-items">
<?php

$active = '';

if(isUserLoggedIn())
{
	$active = ($page == 'browse') ? ' active' : '';
	echo '<div class="menu-item'.$active.' first" id="browse">browse</div>';
	echo '<div class="menu-spacer"></div>';
	$active = (substr($page,0,6) == 'create') ? ' active' : '';
	echo '<div class="menu-item'.$active.'" id="create">create</div>';
	echo '<div class="menu-spacer"></div>';
	$active = ($page == 'track') ? ' active' : '';
	echo '<div class="menu-item'.$active.'" id="track">track</div>';
	echo '<div class="menu-spacer"></div>';
	$active = ($page == 'contact') ? ' active' : '';
	echo '<div class="menu-item'.$active.' last" id="contact">contact us</div>';
	$style = 'style="left:151px;"';
}
else
{
	$active = ($page == 'home') ? ' active' : '';
	echo '<div class="menu-item'.$active.' first" id="home">home</div>';
	echo '<div class="menu-spacer"></div>';
	$active = ($page == 'signup') ? ' active' : '';
	echo '<div class="menu-item'.$active.'" id="signup">sign up plans</div>';
	echo '<div class="menu-spacer"></div>';
	$active = (substr($page,0,6) == 'create') ? ' active' : '';
	echo '<div class="menu-item'.$active.'" id="create">create a card</div>';
	echo '<div class="menu-spacer"></div>';
	$active = ($page == 'contact') ? ' active' : '';
	echo '<div class="menu-item'.$active.' last" id="contact">contact us</div>';
	$style = 'style="left:302px;"';
}

	?>
	<div class="clear-left"></div>
</div>
<div id="submenu-items" <?=$style?>>
	<div class="submenu-item" id="0">FREE</div>
	<!-- <div class="submenu-item" id="1">CUSTOM PRO</div> -->
	<div class="submenu-item last" id="2">UPLOAD PRO</div>
</div>