<div class="headTitle">
		<div class="headHelp">
			<span>HELP Tutorials</span>
		</div>
</div>

<div id="helpPlate">
	<div class="helpcredits"><img id="cr" src="_site/help_credits.png" /></div>
	<div class="helpshop"><img id="sh" src="_site/help_shop.png" /></div>
	<div class="helpalbum"><img id="al" src="_site/help_album.png" /></div>
	<div class="helpauction"><img id="au" src="_site/help_auction.png" /></div>
	<div class="helpdeck"><img id="de" src="_site/help_deck.png" /></div>
</div>
<div class="help_display">
	<div class="help_left"><</div>
	<div class="help_viewport">
		<img id="the_img" src="" width="762" />
	</div>
	<div class="help_right">></div>
</div>
<script>
$(document).ready(function(){
	var page = "";
	var maxPage = 0;
	var curPage = 1;
	
	$(".helpcredits").click(function(){
		$(".help_display").show();
		page = "credits";
		curPage = 1;
		maxPage = 3;
		showPage(page,curPage);
	});
	$(".helpshop").click(function(){
		$(".help_display").show();
		page = "shop";
		curPage = 1;
		maxPage = 4;
		showPage(page,curPage);
	});
	$(".helpalbum").click(function(){
		$(".help_display").show();
		page = "album";
		curPage = 1;
		maxPage = 5;
		showPage(page,curPage);
	});
	$(".helpauction").click(function(){
		$(".help_display").show();
		page = "auction";
		curPage = 1;
		maxPage = 4;
		showPage(page,curPage);
	});
	$(".helpdeck").click(function(){
		$(".help_display").show();
		page = "deck";
		curPage = 1;
		maxPage = 5;
		showPage(page,curPage);
	});
	
	$(".help_left").click(function(){
		if(curPage > 1){
			curPage--;		
			showPage(page,curPage);	
		}else if(curPage == 1){
			$(".help_display").hide();
		}
	});
	
	$(".help_right").click(function(){
		if(curPage < maxPage){
			curPage++;		
			showPage(page,curPage);	
		}
	});
	
	$(".help_viewport").click(function(){
		$(".help_display").hide();
	});
	
	var showPage = function(page,curPage){
		$(".help_display").css({backgroundColor:"#777"});
		var imgPic = $("#the_img").get(0);
		imgPic.src = "_site/loading51.gif";
		imgPic.alt = "";
		$(imgPic).css({width:100,height:100,'margin-top':'280px','margin-left':'350px'});
		
		var img = new Image();
		img.src = "_site/tutorials/"+page+"/"+curPage+".jpg";
		img.onload = function() {
			$(".help_display").css({backgroundColor:"none"});
			$(imgPic).css({width:762,height:708,'margin-top':'0px','margin-left':'0px'});
			imgPic.src = "_site/tutorials/"+page+"/"+curPage+".jpg";
		}
	}
	
});
</script>








