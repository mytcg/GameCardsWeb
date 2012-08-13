<div class="inner">
	<?php

if(isUserLoggedIn())
{
	$linklists = array
	(
		/*array
		(
			array(Title => "Create a Card", Url => "http://"),
			array(Title => "Upload your Card", Url => "http://"),
			array(Title => "Browse Contacts", Url => "http://"),
			array(Title => "Track Your Card", Url => "http://")
		),*/
		array
		(
			array(Title => "Browse", Url => "browse"),
			array(Title => "Create A Card", Url => "create"),
			array(Title => "Track", Url => "track"),
			array(Title => "Contact Us", Url => "contact")
		)
	);
}
else
{
	$linklists = array
	(
		array
		(
			array(Title => "Home", Url => "home"),
			array(Title => "Sign Up Plans", Url => "signup"),
			array(Title => "Create A Card", Url => "create"),
			array(Title => "Contact Us", Url => "contact")
		)
	);
}

?>
<div id="icon"></div>
<?php

foreach($linklists as $linklist)
{
	echo '<div class="footer-links">';
	foreach($linklist as $link)
	{
		$disabled = ($page == $link['Url']) ? ' class="disabled" ' : '';
		echo '<div class="footer-link"><a href="?page='.$link['Url'].'"'.$disabled.'">'.$link['Title'].'</a></div>';
	}
	echo '</div>';
}
	?>
	<div class="clear-left"></div>
	<div id="social-links">
		<div class="social-link" id="facebook"><a href="http://www.facebook.com/sharer.php?u=http://mobidex.biz&t=Check out Mobidex" target="_blank"></a></div>
		<div class="social-link" id="twitter"><a href="http://twitter.com/home?status=Check out Mobidex. Digital business cards that work on all types of phones! http://mobidex.biz" target="_blank"></a></div>
	</div>
	<div id="rights">all rights reserved mobidex 2011</div>
	
	<!-- full browser window loader overlay -->
	
	<div id="loader-html" style="display: none;">
		<div class="small loader">
			<div class="overlay"></div>
			<div class="animation">
				<img src="img/loading51.gif" />
				<br />
				<p id="loader-text">&nbsp;&nbsp;&nbsp;Loading...</p>
			</div>
		</div>
	</div>
	
</div>