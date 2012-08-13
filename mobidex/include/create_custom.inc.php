<?php

//

?>
<div id="page-title">Create your own card - With your style, image and logo</div>

<div style="position:relative;">
	
	
	<input type="text" id="txtField" value="4.3" />
	
	<input type="button" id="cmdLoad" value="Load Image" />
	
	<img id="img" src="" alt="" />
	
</div>

<script>
$(document).ready(function(){
   
	$("#cmdLoad").click(function(){
		$("#img")
		.attr('src','generate_image.php?text='+$("#txtField").val())
		.attr('alt','Writing 4.3% to an Image With PHP');
	});
	
	
});
</script>