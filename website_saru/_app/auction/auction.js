function WORK_Auction(){
	this.iComponentNo=0;
	this.divData=0;
	this.sURL="_app/auction/";
	this.sXML="";
	this.divList = null;
	this.divListLarge = null;
	this.iCurrentAuctionID=0;
	this.iCellCount=0;
	this.iCellCountAll=0;
	this.iCellCountMine=0;
	this.iCellWidth=147;
	this.iCellHeight=109;
	this.iCellMargin=8;
	this.iCellsPerPage=4;
	this.iMaxRows=1;
	this.iCurrentPage=0;
	this.iWindowHeight=0;
	this.dragged=false;
	this.aWindowAuction=null;
	this.iAuctionBlock=null;
	this.timeLeftInterval=null;
	this.enddate=null;
	this.listener=null;
	this.loaded=true;
	this.milliMax=15000;
	this.milliMin=60000;
	this.catString = "";
	this.searchString = "";
	this.searchResults = "";
	
	if (typeof WORK_Auction._iInited=="undefined"){

WORK_Auction.prototype.init=function()
{
	ZU.divData=document.getElementById("window_"+ZU.iComponentNo);
	$(ZU.divData).empty();
	ZA.addLoader($(ZU.divData),ZU.iComponentNo);
	if($(".messageWindow").size()){
		$(".closeMessageWindow").click();
	}
	//load xml data
	ZA.callAjax(ZU.sURL+"?init=1",function(sXML)
	{
		ZU.loaded = true;
		ZU.sXML=sXML;
		ZU.divList = null;
		ZU.divListLarge = null;
		ZU.iCellCountAll = parseInt(ZA.getXML(sXML,"cards/no_of_cards"));
		ZU.iCellCountMine = parseInt(ZA.getXML(sXML,"cards/no_of_cards_mine"));
		ZU.iCellCount = ZU.iCellCountAll-ZU.iCellCountMine;
		ZU.iCurrentPage = 0;
		
		if(ZA.categoryID > 0){
		  ZU.catString = "_app/auction/?cat="+ZA.categoryID+"&l="+ZA.updateLevel;
		}else{
		  ZU.catString = "_app/auction/?init=1";
		}
		
		var divControlsLeft = ZA.createDiv(ZU.divData,"controlspageleft");
		divControlsLeft.onclick = ZA.clickPageLeft(ZU);
		var divControlsRight = ZA.createDiv(ZU.divData,"controlspageright");
		divControlsRight.onclick = ZA.clickPageRight(ZU);
		
		ZU.maximize();
		
		ZA.removeLoader(ZU.iComponentNo);
		
		//Update user credits
		if(ZA.getXML(sXML,"credits") != ZA.sUserCredits){
			ZA.sUserCredits=ZA.getXML(sXML,"credits");
			ZA.oPlayerBar.update({credits:ZA.sUserCredits});
		}
	});
	
};

WORK_Auction.prototype.resetListening=function(){
	ZU.stopListening();
	if(ZA.aComponents[ZU.iComponentNo].iIsMaximized){
		//Listen for auction changes
		ZU.listener = setInterval("ZU.auctionListener()",ZU.milliMax);
	}
	else{
		//Listen for auction changes
		ZU.listener = setInterval("ZU.auctionListener()",ZU.milliMin);
	}
};

WORK_Auction.prototype.stopListening=function(){
	clearInterval(ZU.listener);
	ZU.listener = null;
};


WORK_Auction.prototype.maximize=function()
{
	if (ZA.aComponents[ZU.iComponentNo].iIsMaximized) {
		if(ZU.divList){
			$(ZU.divList).hide('fast',function(){
				ZU.showAuctionsLarge();
			});
		}
		else{
			ZU.showAuctionsLarge();
		}
		$("#window_"+ZU.iComponentNo).find(".controlspagedots").hide();
		$("#window_"+ZU.iComponentNo).find(".controlspageleft").hide();
		$("#window_"+ZU.iComponentNo).find(".controlspageright").hide();
	}
	else{
		if(ZU.divListLarge){
			$(ZU.divListLarge).hide('fast',function(){
				ZU.showAuctions();
			});
		}
		else{
			ZU.showAuctions();
		}
		$("#window_"+ZU.iComponentNo).find(".controlspagedots").show();
		$("#window_"+ZU.iComponentNo).find(".controlspageleft").show();
		$("#window_"+ZU.iComponentNo).find(".controlspageright").show();
	}
	ZU.resetListening();
};

		
WORK_Auction.prototype.flipCard=function(iCardNo,iIsFront){
	return function(){
		if(!ZU.dragged)
		{
			var divFull=document.getElementById("cardfull");
			if (iIsFront){
				var divImage=document.getElementById("auctioncardfull1");
				var divImage2=document.getElementById("auctioncardfull0");
			} else {
				var divImage=document.getElementById("auctioncardfull0");
				var divImage2=document.getElementById("auctioncardfull1");			
			}
			$(divFull).animate({
				width:"10px",
				marginLeft:"120px"
			},150,function(){
				divImage.style.display="none";
				divImage2.style.display="block";
				$(divFull).animate({
					width:"250px",
					marginLeft:"0px"
				},150);
			});
		}
		else
		{
			ZU.dragged = false;
		}
	};
};

		
WORK_Auction.prototype.clickCloseFullImage=function(){
	return function(){
		var divBody=document.getElementsByTagName("body")[0];
		var divFull=document.getElementById("cardfull");
		if (divFull){
			divBody.removeChild(divFull);
		}
	};
};

		
WORK_Auction.prototype.clickShowFullImage=function(iThumbnailNo, popup){
	return function(){
		if(typeof popup == "undefined"){popup = '';}
		var divThumbnail=document.getElementById("auctionthumbnail_"+iThumbnailNo+popup);
		var xy = ZA.findXY(divThumbnail);
		var iLeft = xy[0]+42;
		var iTop = xy[1]-65;
		var divWindow=document.getElementById("window_"+ZU.iComponentNo);
		var iWidthWindow=parseInt(divWindow.style.width);
		var sImg=ZA.getXML(ZU.sXML,"cards/card_"+iThumbnailNo+"/imageserver")
		+"cards/jpeg/"
		+ZA.getXML(ZU.sXML,"cards/card_"+iThumbnailNo+"/image");

		var divBody=document.getElementsByTagName("body")[0];
		
		//Avoid card displaying off page on maximized window
		if(iTop < 250){ iTop = 250; }
		
		var divFull=document.getElementById("cardfull");
		if (divFull){
			divBody.removeChild(divFull);
		}
		var divFull=ZA.createDiv(divBody,"cardfull","cardfull");
		$(divFull).draggable("destroy");
		$(divFull).draggable({
			start: function(){
				ZU.dragged = true;
			},
			containment: "body"
		});
		var divInfo=ZA.createDiv(divFull,"cardfullinfo");
		//divInfo.innerHTML="Close";
		$(divInfo).attr('title','Close');
		divInfo.onclick=ZU.clickCloseFullImage();
		var divImg=ZA.createDiv
			(divFull,"cardfullimage","auctioncardfull1","img");
		divImg.onclick=ZU.flipCard(iThumbnailNo,1);
		$(divImg).css({display:"none"});
		divImg.src=sImg+"_front.jpg";
		var divImg2=ZA.createDiv
			(divFull,"cardfullimage","auctioncardfull0","img");
		divImg2.onclick=ZU.flipCard(iThumbnailNo,0);
		divImg2.src=sImg+"_web.jpg";
			$(divFull).css({
			left:iLeft+"px",
			top:iTop+"px"
		});
		$(divFull).animate({
			left:(iLeft-125)+"px",
			top:(iTop-175)+"px",
			width:"250px",
			height:"380px"
		},
		function(){
			$(divImg).css({display:"block",height:"350px"});
			$(divImg2).css({display:"none",height:"350px"});
			divImg2.src=sImg+"_back.jpg";
		});
		ZA.setNextZIndex(divFull);
		
		//-----------------------------------------------------
		//Card menu tabs
		//-----------------------------------------------------
		/*
		var divMenu = ZA.createDiv(divFull,"cardMenu");
		
		if(ZA.sUsername){
			//Compare tab
			var divTab = ZA.createDiv(divMenu,"menuTab","tabCompare");
			$(divTab).css({
				background:"url(_site/all.png) -735px -125px no-repeat",
				top:70
			});
			$(divTab).attr('title','Compare card');
			$(divTab).click(function(){
				var cardid = ZA.getXML(ZU.sXML,"cards/card_"+iThumbnailNo+"/card_id");
				ZA.showCompare(cardid);
			});
		}
		*/
		//-----------------------------------------------------
		//END OF: Card menu tabs
		//-----------------------------------------------------
	};
};


WORK_Auction.prototype.showAuctions=function()
{
	if(ZU.divList){
		$(ZU.divList).show('fast',function(){
			ZU.auctionListener();
		});
	}
	else{
		ZU.loadAuctions();
		ZA.removeLoader(ZU.iComponentNo);
	}
};


WORK_Auction.prototype.loadAuctions=function()
{
	ZU.iCellWidth=190;
	ZU.iCellHeight=109;
	ZU.iCellMargin=8;
	if($("#page_"+ZU.iComponentNo).size()){
		$("#page_"+ZU.iComponentNo).remove();
	}
	var divAuctions=ZA.createDiv(ZU.divData,"pagebg","pagebg_"+ZU.iComponentNo);
	
	var iCount=0;
	var iLeft=15;//ZU.iCellMargin;
	var iTop=0;//ZU.iCellMargin;
	
	if(ZU.iCellCountAll > 0)
	{
		$(divAuctions).css({
			width:(ZU.iCellCount*(ZU.iCellWidth+ZU.iCellMargin))+"px",
			height:165,
			marginTop:5,
		});
		var divAuctionsHolder = ZA.createDiv(divAuctions,"","auctionsHolder","div");
		$(divAuctionsHolder).css({width:"90%"}).show();
		
		while (iCount<(ZU.iCellCountAll))
		{
			var auctionContainerBlock=ZA.createDiv(divAuctionsHolder,"itemBlock");
			$(auctionContainerBlock).css({
				left:(iLeft+10)+"px",
				top:(iTop+6)+"px"
			});
			var divAuctionBlock = ZA.createDiv(auctionContainerBlock,"auctionBlock",iCount.toString(),"div");
			$(divAuctionBlock).attr('alt', ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/usercard_id"));
			var backgroundimage = "url("
					+ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/imageserver")
					+"cards/jpeg/"
					+ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/image")
					+"_front.jpg)";
			$(divAuctionBlock).css({
				background:backgroundimage,
				backgroundPosition:"64% 7%",
				backgroundRepeat:"no-repeat",
				bottom:10,
			});
			
			var dataLeft=3;
			var dataTop=1;
			
			var divAuctionData = ZA.createDiv(auctionContainerBlock,"data");
			$(divAuctionData).css({
				width:152,
				height:42,
				top:0,
			});
			var bg = ZA.createDiv(divAuctionData,"fadedBackground");
			var textBox = ZA.createDiv(divAuctionData,"","","div");
            $(textBox).css({ top:0,paddingTop:3,paddingRight:6,fontSize:9,width:138,height:30,color:"#FFF",textAlign:"left" });          
           	
			//card owned indicator
			var cardOwned = parseInt(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/owned"),10);
			if(cardOwned > 0){
				var divCardOwned = ZA.createDiv(divAuctionBlock,"iconCardOwned",iCount.toString(),"div");
				$(divCardOwned).css({
					right:5
				});
				$(divCardOwned).attr('title','You already own this card');
				if(cardOwned > 1){
					$(divCardOwned).html(cardOwned.toString());
				}
			}
			
			var divHighestBidder = ZA.createDiv(textBox,"iconHighestBidder",iCount.toString(),"div");
			$(divHighestBidder).attr('title','You are the highest bidder');
			
			var divAuctionDetails = ZA.createDiv(textBox,"","","div");
			
			var bidCount = parseInt(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/history/bid_count"),10);
			var bidAmount;
			if(bidCount > 0){
				bidAmount = parseInt(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/history/bid_0/amount"),10);
				var bidder = ZA.formatUsername(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/history/bid_0/user"));
				if(bidder == 'You'){
					$(divHighestBidder).show();
				}
			}
			else{
				bidAmount = parseInt(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/starting_bid"),10);
			}
			var bids = bidCount+' bid';
			if(bidCount != 1){
				bids = bids+'s';
			}
			var owner;
			var sellerName=ZA.getXML(ZU.sXML,'cards/card_'+iCount+'/description');
			var sellerTrimName=sellerName.substr(0,13);
			var mine = ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/mine");
			if(mine == '1'){
				owner = '<span style="font-weight:bold;">*** Your Auction ***</span>';
				$(divAuctionBlock).addClass("mine").hide();
			}
			else{
				owner = 'Seller: '+ZA.formatUsername(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/owner"));
			}
			$(textBox).html(
				'<span class="txtGreen">'+sellerTrimName+'</span><br />'+
				'Current Bid: <span class="txtBlue" style="font-weight:bold;"><span class="currentBid">'+bidAmount+'</span>&nbsp;TCG</span><br />'+
				'<span>Time Left: <span class="timeLeft">'+ZU.getTimeLeft(new Date(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/expire")), true)+'</span>'+
				'<input type="hidden" class="endDate" value="'+ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/expire")+'" />'
			);
			
			// var divAuctionLink = ZA.createDiv(divAuctionData,"viewAuction",iCount.toString(),"div");
			
			if(mine == '0'){
				iTop+=ZU.iCellHeight+ZU.iCellMargin;
				if (iTop>=(ZU.iMaxRows*(ZU.iCellHeight+ZU.iCellMargin))) {
					iTop=0;//ZU.iCellMargin;
					iLeft+=ZU.iCellWidth+ZU.iCellMargin;
				}
			}
			
			iCount++;
		}
		
		// $(".viewAuction").unbind().click(function(){
			// var id = $(this).attr('id');
			// ZU.aWindowAuction = new WORK_AuctionWindow();
			// ZU.iAuctionBlock = id;
			// ZU.aWindowAuction.create(id);
		// });
	}
	else
	{
		$(divAuctions).css({
			width:"100%",
			height:228
		});
		var divNone = ZA.createDiv(divAuctions);
		$(divNone).css({
			width:"100%",
			top:110
		});
		$(divNone).html('No auctions');
	}
	ZU.divList = divAuctions;
	ZA.showPageDots(ZU);
};


WORK_Auction.prototype.applySearchResults=function()
{
	//obey search results if any
	if(ZU.searchString.length > 0)
	{
		//something was searched
		$("#noresults").hide();
		var results = [];
		if(ZU.searchResults.length > 0){
			results = ZU.searchResults.split(',');
		}
		$(".auctionBlockLarge").each(function(){
			if(results.indexOf($(this).attr('alt')) == -1){
				$(this).hide();
			}
		});
		//check if there were results
		if($(".auctionBlockLarge").size() > 0 && $(".auctionBlockLarge:visible").size() < 1){
			//no search results found
			if($(".auctionBlockLarge:visible").size() == 0 && $("#txtSearch").val().length > 0){
				$(".auctionBlockLarge").hide();
				$("#noresults").show();
			}
		}
		//indicate search results
		$("#resultsCount").html($(".auctionBlockLarge:visible").size());
	}
	else
	{
		//nothing was searched
	}
	//reset vertical scrollbar
	$("#auctionsHolderLarge").css({
	}).jScrollPane({enableKeyboardNavigation:false});
}


WORK_Auction.prototype.clearSearchResults=function(keepsearch)
{
	if(typeof(keepsearch) == "undefined"){
		$("#noresults").hide();
		$("#txtSearch").val('');
		ZU.searchString = "";
		ZU.searchResults = "";
		$("#searchString").html('');
		$("#searchResults").hide();
	}
	ZU.applyFilter($(".filterSelected").attr('id'));
}


WORK_Auction.prototype.applyFilter=function(filter)
{
	$(".auctionMenuItem").removeClass("filterSelected");
	$(".auctionMenuItem[id='"+filter+"']").addClass('filterSelected');
	$(".auctionBlockLarge").show();
	switch(filter)
	{
		case 'all':
			//show all
		break;
		case 'other':
			$(".auctionBlockLarge").each(function(){
				if($(this).hasClass("mine")){
					$(this).hide();
				}
			});
		break;
		case 'my':
			$(".auctionBlockLarge").each(function(){
				if(!$(this).hasClass("mine")){
					$(this).hide();
				}
			});
		break;
	}
	ZU.applySearchResults();
}


WORK_Auction.prototype.showAuctionsLarge=function()
{
	if(ZU.divListLarge){
		$(ZU.divListLarge).show('fast',function(){
			//activate scrollbar (if required)
			$("#auctionsHolderLarge").css({
			}).jScrollPane({enableKeyboardNavigation:false});
			ZU.auctionListener();
		});
	}
	else{
		ZA.addLoader($(ZU.divData), ZU.iComponentNo);
		setTimeout("ZU.loadAuctionsLarge()",1000);
	}
};
		
WORK_Auction.prototype.loadAuctionsLarge=function()
{
	if($("#auctionsListLarge").size()){$("#auctionsListLarge").remove();}
	var divAuctionsLarge = ZA.createDiv(ZU.divData,"","auctionsListLarge");
	$(divAuctionsLarge).css({
		width:"100%",
		paddingBottom:0
	}).hide();
	
	//no search results
	var divNone = ZA.createDiv(divAuctionsLarge,"","noresults");
	$(divNone).css({
		width:"100%",
		top:250
	});
	$(divNone).html('No results found.');
	$(divNone).hide();
	
	//Top controls main page
	var topMenuMain = ZA.createDiv(divAuctionsLarge,"","auctionTopMenu","div");
	$(topMenuMain).css({ width:730,height:28,backgroundColor:"transparent","z-index":"9999",position:"relative",padding:5 });
	//Clear button
	var btn = ZA.createDiv(topMenuMain,"auctionMenuItem","","div");
	$(btn).html("Clear");
	$(btn).addClass('cmdButton');
	$(btn).click(function(){
		ZU.clearSearchResults();
	});
	//Search button
	var btnSearch = ZA.createDiv(topMenuMain,"auctionMenuItem","cmdSearch","div");
	$(btnSearch).html("Search");
	$(btnSearch).addClass('cmdButton');
	$(btnSearch).click(function(){
		$("#noresults").hide();
		var searchstring = $("#txtSearch").val().trim();
		$("#txtSearch").val(searchstring);
		ZU.searchString = searchstring;
		if(searchstring.length > 0){
			$("#searchString").html(searchstring);
			$("#searchResults").show();
			ZA.callAjax(ZU.sURL+"?search=1&string="+searchstring,function(xml){
				ZU.searchResults = ZA.getXML(xml,"results");
				//clear current search if any
				ZU.clearSearchResults(true);
			});
		}
		else
		{
			var icon = "-697px -63px";
			ZS.showWindow(icon,'No search text entered',5000);
		}
	});
	//Search label and textbox
	var div = ZA.createDiv(topMenuMain,"","","div");
	$(div).css({
		position:"relative",
		"float":"right"
	});
	$(div).html('<input type="text" id="txtSearch" />');
	$("#txtSearch").keydown(function(event){
		if(event.which == 13){
			$("#cmdSearch").click();
		}
	});
	var div = ZA.createDiv(topMenuMain,"","","div");
	$(div).css({
		position:"relative",
		"float":"right",
		padding:4,
		marginLeft:15
	});
	$(div).html('Search:');
	//Filter Button: All auctions
	var filterAll = ZA.createDiv(topMenuMain,"auctionMenuItem","all","div");
	$(filterAll).css({"float":"left"});
	$(filterAll).html("All auctions");
	$(filterAll).addClass('cmdButton');
	$(filterAll).click(function(){
		if(!$(this).hasClass('filterSelected')){
			ZU.applyFilter($(this).attr('id'));
		}
	});
	$(filterAll).addClass('filterSelected');
	//Filter Button: Other auctions
	var filterOther = ZA.createDiv(topMenuMain,"auctionMenuItem","other","div");
	$(filterOther).css({"float":"left"});
	$(filterOther).html("Other auctions");
	$(filterOther).addClass('cmdButton');
	$(filterOther).click(function(){
		if(!$(this).hasClass('filterSelected')){
			ZU.applyFilter($(this).attr('id'));
		}
	});
	//Filter Button: My auctions
	if(ZA.sUsername){
	  var filterMy = ZA.createDiv(topMenuMain,"auctionMenuItem","my","div");
    $(filterMy).css({"float":"left"});
    $(filterMy).html("My auctions");
    $(filterMy).addClass('cmdButton');
    $(filterMy).click(function(){
      if(!$(this).hasClass('filterSelected')){
        ZU.applyFilter($(this).attr('id'));
      }
    });
	}
	
	//Search results display
	var div = ZA.createDiv(topMenuMain,"","searchResults");
	$(div).css({
		position:"relative",
		"float":"left",
		padding:4,
		marginLeft:10
	});
	$(div).html('<span id="resultsCount"></span> result(s) for `<span id="searchString"></span>`');
	$(div).hide();
	
	var iCount=0;
	var iLeft=ZU.iCellMargin;
	var iTop=ZU.iCellMargin;
	
	if(ZU.iCellCountAll > 0)
	{
		var divAuctionsHolder = ZA.createDiv(divAuctionsLarge,"","auctionsHolderLarge","div");
		$(divAuctionsHolder).css({
			position:"relative",
			height:610,
			width:"100%",
		});
	
		while (iCount<ZU.iCellCountAll)
		{
			var divAuctionBlock = ZA.createDiv(divAuctionsHolder,"auctionBlockLarge",iCount.toString(),"div");
			$(divAuctionBlock).attr('alt', ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/usercard_id"));
			
			var pictureCon = ZA.createDiv(divAuctionBlock,"auctionBlockBg");
			var divAuctionImage=ZA.createDiv(pictureCon,"pagebox","auctionthumbnail_"+iCount);
			$(divAuctionImage).css({
				backgroundImage:"url("
					+ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/imageserver")
					+"cards/jpeg/"
					+ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/image")
					+"_web.jpg)",
				backgroundRepeat:"no-repeat",
				"-moz-box-shadow":"inset 0px 0px 2px #000",
				"box-shadow":"inset 0px 0px 2px #000",
				width:64,
				height:90,
				marginLeft: 5,
    			marginTop: 10,
			});
			var divAuctionInfo = ZA.createDiv(divAuctionBlock,"auctionInfoBlock");
			var divAuctionTitle = ZA.createDiv(divAuctionInfo,"txtGreen","","div");
			$(divAuctionTitle).css({
				fontSize:12,
				textAlign:"left",
				height:12,
				overflow:"hidden"
			});
			$(divAuctionTitle).html('<b>'+ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/description")+'</b>');
			
			//card owned indicator
			var cardOwned = parseInt(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/owned"),10);
			if(cardOwned > 0){
				var divCardOwned = ZA.createDiv(divAuctionImage,"iconCardOwned",iCount.toString(),"div");
				$(divCardOwned).css({
					top: 93,
					left: "auto",
					right: -1,
				});
				$(divCardOwned).attr('title','You already own this card');
				if(cardOwned > 1){
					$(divCardOwned).html(cardOwned.toString());
				}
			}
			
			//highest bidder indicator
			var divHighestBidder = ZA.createDiv(divAuctionInfo,"iconHighestBidder",iCount.toString(),"div");
			$(divHighestBidder).attr('title','You are the highest bidder');
			
			var divAuctionDetails = ZA.createDiv(divAuctionInfo,"","","div");
			$(divAuctionDetails).css({
				top:22,
				"line-height":1.5,
				"text-align":"left",
				fontWeight:"600"
			});
			var bidCount = parseInt(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/history/bid_count"),10);
			var bidAmount;
			if(bidCount > 0){
				bidAmount = parseInt(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/history/bid_0/amount"),10);
				var bidder = ZA.formatUsername(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/history/bid_0/user"));
				if(bidder == 'You'){
					$(divHighestBidder).show();
				}
			}
			else{
				bidAmount = parseInt(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/starting_bid"),10);
			}
			var bids = bidCount+' bid';
			if(bidCount != 1){
				bids = bids+'s';
			}
			var owner;
			var sellerName=ZA.formatUsername(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/owner"));
			var sellerTrimName=sellerName.substr(0,12);
			if(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/mine") == '1'){
				owner = '<span style="font-weight:bold;">*** Your Auction ***</span>';
				$(divAuctionBlock).addClass("mine");
			}
			else{
				owner = 'Seller: '+sellerTrimName;
			}
			$(divAuctionDetails).html(
				'<span style="font-size:10px;">'+owner+'</span><br />'+
				'<span>Time Left: <span class="timeLeft">'+ZU.getTimeLeft(new Date(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/expire")),true)+'</span><br />'+
				'<input type="hidden" class="endDate" value="'+ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/expire")+'" />'+
				'<span class="txtBlue" style="font-size:16px;font-weight:bold;"><span class="currentBid">'+bidAmount+'</span>&nbsp;TCG</span>&nbsp;&nbsp;'+
				'<span style="position:absolute;top:34px;"><nobr>[<span class="bidCount">'+bids+'</span>]</nobr></span>'
			);
			
			var divAuctionLink = ZA.createDiv(divAuctionInfo,"viewAuction",iCount.toString(),"div");
			
			$(divAuctionLink).html('View');
			$(divAuctionLink).addClass('cmdButton');
			$(divAuctionLink).css({
				padding:"4px 5px 2px",
    			top: 102,
    			width: 30,
    			textAlign:"center",
			});
			var buyoutPrice = ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/price");
			if(buyoutPrice != '0')
			{
				var div = ZA.createDiv(divAuctionInfo,"","","div");
				$(div).css({
					left:62,
					width:28,
					height:25,
					bottom:11,
					background:"url(_site/all.png) -482px -40px no-repeat"
				});
				$(div).attr('title','Buy out available');
				var divBuyout = ZA.createDiv(divAuctionInfo,"buyoutPrice");
				$(divBuyout).html("<span>"+buyoutPrice+"</span> TCG");
			}
			
			iCount++;
		}
		
		var clear = ZA.createDiv(divAuctionsHolder);
		$(clear).css({
			clear:"both"
		});
		
		$(".viewAuction").unbind().click(function(){
			var id = $(this).attr('id');
			ZU.aWindowAuction = new WORK_AuctionWindow();
			ZU.iAuctionBlock = id;
			ZU.aWindowAuction.create(id);
		});
	}
	else
	{
		var divNone = ZA.createDiv(divAuctionsLarge);
		$(divNone).css({
			width:"100%",
			top:250
		});
		$(divNone).html('No auctions');
	}
	
	ZU.divListLarge = divAuctionsLarge;
	$(ZU.divListLarge).show('fast',function(){
		$("#auctionsHolderLarge").css({
		}).jScrollPane({enableKeyboardNavigation:false});
		ZA.removeLoader(ZU.iComponentNo);
	});
};
	}
	WORK_Auction._iInited=1;
};


WORK_Auction.prototype.auctionListener=function()
{
	if(ZU.loaded){
		ZU.loaded = false;
		return false;
	}
	var lString="";
	if(ZA.categoryID > 0){
		lString = "&c="+ZA.categoryID;
	}
	ZA.callAjax(ZU.sURL+"?listen=1"+lString,function(xml){
	
		var i = 0;
		var auctions;
		var credits = parseInt(ZA.getXML(xml,"credits"));
		var str = ZA.getXML(xml,"string");
		var aAuctions = str.split('^!%');
		
		//update user credits if changed
		if(credits != ZA.sUserCredits){
        	//Update user credits
        	ZA.sUserCredits=credits;
        	//ZA.oPlayerBar.update({credits:ZA.sUserCredits});
		}
		
		for(i=0; i<aAuctions.length; i++){
			aAuctions[i] = aAuctions[i].split('|');
		}

		var reload = false;
		var message = '';
		if(aAuctions.length > 0)
		{
			var temp = [];
			var auctionblock = ".auctionBlock";
			if(ZA.aComponents[ZU.iComponentNo].iIsMaximized){
				auctionblock+= "Large";
			}
			for(var i=0; i<aAuctions.length; i++)
			{
				var id = aAuctions[i][0];
				//check auction id
				if(id != $(auctionblock+"[id='"+i+"']").attr('alt')){
					ZU.auctionsChanged();
				}
				
				//update auction details
				if($(auctionblock+"[alt='"+id+"']").size() < 1)
				{
					//this is a new auction
					ZU.auctionsChanged('New auction(s) available.');
				}
				else
				{
					var auction = $(auctionblock+"[alt='"+id+"']");
					var currentBid = auction.find(".currentBid").html();
					var youBidder = auction.find(".iconHighestBidder").is(":visible").toString();
					
					//Check bid count
					if(auction.find(".bidCount").length){
						var bidCount = parseInt(auction.find(".bidCount").html());
						if(bidCount != aAuctions[i][1]){
							bidCount = aAuctions[i][1];
							var bids = bidCount+' bid';
							if(bidCount != 1){
								bids = bids+'s';
							}
							auction.find(".bidCount").effect("pulsate",{},250,function(){$(this).html(bids).show();});
						}
					}
					
					//Check current bid
					if(currentBid != aAuctions[i][2]){
						var bid = aAuctions[i][2];
						auction.find(".currentBid").effect("pulsate",{},250,function(){$(this).html(bid).show();});
					}
					
					//Check highest bidder
					if(youBidder != aAuctions[i][3]){
						youBidder = aAuctions[i][3];
						if(youBidder == 'true'){
							auction.find(".iconHighestBidder").show("fast");
						}else{
							auction.find(".iconHighestBidder").hide("fast");
							//let user know that highest bid was lost
							ZA.showMessage('Auction Update: You lost the highest bid on the '+auction.find(".txtGreen").html(),'-',5000);
						}
					}
					
					//Update time left
					var newTimeLeft = ZU.getTimeLeft(new Date(auction.find(".endDate").val()),true);
					if(auction.find(".timeLeft").html() != newTimeLeft){
						auction.find(".timeLeft").hide().html(newTimeLeft).show('fast');
					}
				}
			}
		}
		else
		{
			if(str.length > 0){
				//reload = true;
				//message = 'New auctions available.';
				ZU.reloadAuctionComponent();
			}
		}
		
		if(reload){
			ZU.auctionsChanged(message);
		}
		
	});
};


WORK_Auction.prototype.auctionsChanged=function(message)
{
	if(typeof(message)=="undefined" || message==''){
		message = 'Auctions have changed.';
	}
	message+= ' <a href="#" class="reloadAuctions">Reload</a><a href="#" class="closeMessageWindow">Close</a>';
	if($(".messageWindow").size() < 1){
		ZA.showMessage(message,'=',0);
		$(".reloadAuctions").click(function(){
			$(".closeMessageWindow").click();
			ZU.reloadAuctionComponent();
			return false;
		});
	}
};


WORK_Auction.prototype.reloadAuctionComponent=function()
{
	if($("#windowcontainer_0").size()){
		ZU.loaded = true;
		ZU.aWindowAuction.clickCloseA();
	}
	ZU.loaded = true;
	ZU.stopListening();
	ZU.init();
};


WORK_Auction.prototype.getTimeLeft=function(enddate,minute)
{
	var today = new Date();
	var diffs = 1000;
	var diffm = diffs*60;
	var diffh = diffm*60;
	var diffd = diffh*24;
	var msleft = parseInt(enddate-today,10);
	var timeleftdisplay = 'Finished';
	
	if(msleft > 0)
	{
		var d = parseInt(msleft/diffd,10);
		msleft = msleft - (d*diffd);
		var h = ZA.getDoubleDigits(parseInt(msleft/diffh,10));
		msleft = msleft - (h*diffh);
		var m = ZA.getDoubleDigits(parseInt(msleft/diffm,10));
		msleft = msleft - (m*diffm);
		var s = ZA.getDoubleDigits(parseInt(msleft/diffs,10));
		timeleftdisplay = '';
		if(d > 0){
			timeleftdisplay = d+'d&nbsp;';
		}
		if(typeof(minute) == "undefined"){
			timeleftdisplay+= h+':'+m+':'+s;
		}
		else{
			timeleftdisplay+= h+':'+m;
		}
	}
	
	return timeleftdisplay;
	
};

/** ========================================================================
AUCTION WINDOW CLASS
*/
function WORK_AuctionWindow(){

if (typeof WORK_AuctionWindow._iInited=="undefined"){

/*********** close auction window */
WORK_AuctionWindow.prototype.clickClose=function(){
	return function() {
		clearInterval(ZU.timeLeftInterval);
		ZU.aWindowAuction.clickCloseA();
	};
};

/*********** close auction window action */
WORK_AuctionWindow.prototype.clickCloseA=function()
{
  clearInterval(ZU.timeLeftInterval);
	var divBody=document.getElementsByTagName("body")[0];
	var divCloak=document.getElementById("bodycloak_0");
	var divAuctionWindow=document.getElementById("windowcontainer_0");
	var divData=document.getElementById("window_0");
	if (divAuctionWindow) {
		divBody.removeChild(divAuctionWindow);
		divBody.removeChild(divData);
	}
	if (divCloak) {
		divBody.removeChild(divCloak);
	}
	if(!ZU.loaded){
		ZU.auctionListener();
	}
};

/*********** create auction window */
WORK_AuctionWindow.prototype.create=function(ID)
{
	var divBody=document.getElementsByTagName("body")[0];
	var iDocHeight=document.documentElement.scrollHeight;
	ZA.createWindowPopup(0,"AuctionWindow",620,440,1,0);
	var divData=document.getElementById("window_0");
	var iTop=0;
	var iLeftL=10
	var iLeftR=110;
	
	var auctionID = ZA.getXML(ZU.sXML,"cards/card_"+ID+"/market_id");
	
	ZA.callAjax(ZU.sURL+"?auction=1&market="+auctionID,function(aXML){
		
		var divAuctionData = ZA.createDiv(divData,"auctionWindowContainer","","div");
		$(divAuctionData).css({
			width:585,
			height:380,
			padding:10,
			"-moz-user-select":"-moz-none",
		});
		
		var auctionTopContainer = ZA.createDiv(divAuctionData,"auctionTopContainer");
		//Title
		var divAuctionTitle = ZA.createDiv(auctionTopContainer,"txtGreen","","div");
		$(divAuctionTitle).css({
			fontSize:20,
			marginTop:5
		});
		$(divAuctionTitle).html('<b>'+ZA.getXML(aXML,"details/description")+'</b>');
		iTop+=35;
	
		//Category
		var val = ZA.createDiv(auctionTopContainer,"","","div");
		$(val).css({top:iTop,fontWeight:"bold",fontSize:12,color:"#999"});
		$(val).html(ZA.getXML(aXML,"details/category"));
		iTop+=20;
		
		//Owner
		var val = ZA.createDiv(auctionTopContainer,"","","div");
		$(val).css({top:iTop,fontSize:12});
		var owner = ZA.formatUsername(ZA.getXML(aXML,"details/owner"));
		var mine = ZA.getXML(aXML,"details/mine");
		if(mine == '1'){
			owner = '<span style="color:#cc0000;font-weight:bold;">You</span>';
		}
		$(val).html('Seller: '+owner);
		iTop+=25;
		
		//Image
		var pictureCon = ZA.createDiv(divAuctionData,"auctionBlockBg");
		var divAuctionImage=ZA.createDiv(pictureCon,"pagebox","auctionthumbnail_"+ID+"p");
		$(divAuctionImage).css({
			backgroundImage:"url("
				+ZA.getXML(aXML,"details/imageserver")
				+"cards/jpeg/"
				+ZA.getXML(aXML,"details/image")
				+"_web.jpg)",
			backgroundRepeat:"no-repeat",
			left:6,
			top:10,
			width:64,
			height:90,
		});
	
		//card owned indicator
		var cardOwned = parseInt(ZA.getXML(ZU.sXML,"cards/card_"+ID+"/owned"),10);
		if(cardOwned > 0){
			var divCardOwned = ZA.createDiv(divAuctionImage,"iconCardOwned",ID,"div");
			$(divCardOwned).css({
				right:-1,
				top:93
			});
			$(divCardOwned).attr('title','You already own this card');
			if(cardOwned > 1){
				$(divCardOwned).html(cardOwned.toString());
			}
		}
		
		var auctionInfoContainer = ZA.createDiv(divAuctionData,"auctionInfoContainer")
		iTop = 0;
		
		//Time Left
		var val = ZA.createDiv(auctionInfoContainer,"","auctionTimeLeft","div");
		$(val).css({
			left:iLeftL,
			top:iTop,
		});
		
		var enddate = new Date(ZA.getXML(aXML,"details/expire"));
		ZU.enddate = enddate;
		var timeleftdisplay = ZU.getTimeLeft(ZU.enddate);
		
		var finished = false;
		if(timeleftdisplay != 'Finished'){
			finished = false;
		}else{
			finished = true;
		}
		
		$(val).html(timeleftdisplay);
		iTop+=50;
		
		//End date and time
		var lbl = ZA.createDiv(auctionInfoContainer,"","lblEnds","div");
		$(lbl).css({left:iLeftL,top:iTop});
		$(lbl).html('Ends:');
		var val = ZA.createDiv(auctionInfoContainer,"","","div");
		$(val).css({left:iLeftR,top:iTop});
		var expire = ZA.getXML(aXML,"details/expire");
		$(val).html( expire.substring(0,expire.length-9) );
		iTop+=23;
		
		//Start date and time
		var lbl = ZA.createDiv(auctionInfoContainer,"","","div");
		$(lbl).css({left:iLeftL,top:iTop});
		$(lbl).html('Started:');
		var val = ZA.createDiv(auctionInfoContainer,"","","div");
		$(val).css({left:iLeftR,top:iTop});
		$(val).html(ZA.getXML(aXML,"details/started"));
		iTop+=23;
		
		//highest bidder indicator
		var divHighestBidder = ZA.createDiv(auctionInfoContainer,"iconHighestBidder","iconHighestBidder","div");
		$(divHighestBidder).attr('title','You are the highest bidder');
		$(divHighestBidder).css({
			right:"auto",
			left:360,
			top:32,
			"z-index":999
		});
		
		//History
		var bidCount = ZA.getXML(aXML,"details/history/bid_count");
		var bidAmount = 0;
		var yourBid = 0;
		var lblCurrentBid;
		var highestBidder;
		
		var lbl = ZA.createDiv(auctionInfoContainer,"","","div");
		$(lbl).css({left:iLeftL,top:iTop});
		$(lbl).html('Bid Count:');
		var val = ZA.createDiv(auctionInfoContainer,"","bidCount","div");
		$(val).css({left:iLeftR,top:iTop});
		var bids = bidCount + ' bid';
		if(parseInt(bidCount) > 1 || parseInt(bidCount) < 1){bids = bids+'s';}
		$(val).html(bids);
		iTop+=23;
		
		if(bidCount != '0')
		{
			bidAmount = ZA.getXML(aXML,"details/history/bid_0/amount");
			yourBid = parseInt(bidAmount) + 1;
			lblCurrentBid = 'Current Bid:';
			highestBidder = ZA.formatUsername(ZA.getXML(aXML,"details/history/bid_0/user"));
			if(highestBidder == 'You'){
				$(divHighestBidder).show();
			}
		}
		else
		{
			bidAmount = ZA.getXML(aXML,"details/starting_bid");
			yourBid = bidAmount;
			lblCurrentBid = 'Starting Bid:';
			highestBidder = 'none';
		}
		
		//Highest Bidder
		var lbl = ZA.createDiv(auctionInfoContainer,"","lblBidder","div");
		$(lbl).css({left:iLeftL,top:iTop});
		$(lbl).html('Highest Bidder:');
		var val = ZA.createDiv(auctionInfoContainer,"","highestBidder","div");
		$(val).css({left:iLeftR,top:iTop});
		$(val).html(highestBidder);
		iTop+=23;
		
		//Current Bid
		var lbl = ZA.createDiv(auctionInfoContainer,"","lblCurrentBid","div");
		$(lbl).css({left:iLeftL,top:iTop});
		$(lbl).html(lblCurrentBid);
		var val = ZA.createDiv(auctionInfoContainer,"","","div");
		$(val).css({left:iLeftR,top:iTop});
		$(val).html('<span class="txtBlue" style="font-size:16px;font-weight:bold;"><span id="currentBid">'+bidAmount+'</span> TCG</span>');
		iTop+=128;
		
		var buyoutPrice = ZA.getXML(aXML,"details/price");
		
		var divActions = ZA.createDiv(divAuctionData,"","actionsHolder","div");
		$(divActions).css({
			left:10,
			top:iTop,
		});
		
		var divBid = ZA.createDiv(divActions,"placeBidHolder","placeBidHolder","div");
		
		if(finished)
		{
			$("#lblEnds").html('Ended:');
			$("#lblBidder").html('Winner:');
			$("#lblCurrentBid").html('Winning Bid:');
			$("#actionsHolder").html('<div style="position:relative;padding:26px;font-size:16px;color:#999;">Bidding has ended on this item.</div>');
		}
		else
		{
			if(!ZA.sUsername)
			{
				$("#actionsHolder").css({top:iTop+25});
				$("#actionsHolder").html('<div style="position:relative;padding:26px;font-size:16px;color:#999;">User must login to place a bid.</div>');
			}
			else
			{
				if(mine == '1')
				{
					if(buyoutPrice != '0')
					{
						//Buyout price
						var lbl = ZA.createDiv(divAuctionData,"","","div");
						$(lbl).css({left:iLeftL,top:iTop-5});
						$(lbl).html('Buyout Price:');
						var val = ZA.createDiv(divAuctionData,"","","div");
						$(val).css({left:iLeftR,top:iTop-5});
						$(val).html('<span style="font-size:16px;font-weight:bold;color:#cc0000;">'+buyoutPrice+' TCG</span>');
						iTop+=15;
					}
					
					$("#actionsHolder").css({top:iTop+25});
					$("#actionsHolder").html('<div style="position:relative;padding:26px;font-size:16px;color:#999;">This is your auction.</div>');
				}
				else
				{
					//Your Bid Label
					var div = ZA.createDiv(divBid,"bidLable","","div");
					$(div).html('<span>Bid</span> Amount:');
					//Bid Amount Textbox
					var div = ZA.createDiv(divBid,"","","div");
					$(div).css({
						top:16,
						left:100
					});
					$(div).html(
						'<input type="text" id="bidValue" value="'+yourBid+'" size="4" />'+
						'<div class="bidAdjuster" id="bidMore" style="top:0px;left:65px;">&#9650;</div>'+
						'<div class="bidAdjuster bidAdjusterDisabled" id="bidLess" style="top:15px;left:65px;">&#9660;</div>'+
						'<div class="txtBlue" style="left:84px;top:7px;font-weight:bold;">TCG</div>'
					);
					//Bid Button
					var div = ZA.createDiv(divBid,"","placeBid","div");
					$(div).html('Place Bid');
					if(highestBidder == 'You'){
						$(div).addClass('bidDisabled');
					}
					
					//Bid Events
					//----------
					$("#bidMore").click(function(){
						yourBid = parseInt($("#bidValue").val(),10);
						yourBid++;
						$("#bidValue").val(yourBid);
						if($("#bidLess").hasClass("bidAdjusterDisabled")){
							var bid = parseInt($("#currentBid").html(),10);
							if(yourBid > bid){
								$("#bidLess").removeClass("bidAdjusterDisabled");
							}
						}
					});
					$("#bidLess").click(function(){
						if(!$(this).hasClass("bidAdjusterDisabled"))
						{
							var bid = parseInt($("#currentBid").html(),10);
							yourBid = parseInt($("#bidValue").val(),10);
							yourBid--;
							if($("#bidCount").html() != '0 bids'){
								if((yourBid-1) <= bid){
									$(this).addClass("bidAdjusterDisabled");
								}
							}
							else{
								if(yourBid <= bid){
									$(this).addClass("bidAdjusterDisabled");
								}
							}
							$("#bidValue").val(yourBid);
						}
					});
					$("#placeBid").click(function(){
						if(!$(this).hasClass('bidDisabled'))
						{
							//Validation
							var minAllowedBid = parseInt($("#currentBid").html());
							if($("#highestBidder").html()!='none') minAllowedBid++;
							var yourBid = $("#bidValue").val().trim();
							$("#bidValue").val(yourBid);
							if(isNaN(yourBid) || yourBid.length < 1 || parseInt(yourBid) < minAllowedBid){
								//Inform the user
								var icon = "-697px -63px";
			          			var response = "Invalid bid amount.";
								ZS.showWindow(icon,response);
								//Reset the bid amount
								$("#bidValue").val(minAllowedBid).focus();
							}
							else
							{
								if($("#highestBidder").html() == 'You'){
									//user cannot bid if currently highest bidder
									var icon = "-697px -63px";
				          			var response = "You are already the highest bidder!";
									ZS.showWindow(icon,response);
									return false;
								}
								else
								{
									if(confirm('Click OK to place a bid of '+yourBid+' TCG credits')){
										$(this).addClass('bidDisabled');
										var marketID = ZA.getXML(aXML,"details/market_id");
										ZU.aWindowAuction.clickBid(marketID, yourBid);
									}
								}
							}
						}
					});
					
					//------------------
					//END OF: Bid Events
					
					if(buyoutPrice != '0')
					{
						//Split line
						var divSplit = ZA.createDiv(divActions,"splitLine","","div");
						$(divSplit).css({
							position:"relative",
							height:13
						});
						$(divSplit).html('<div style="color:#777;text-shadow:none;right:46px;">OR</div>');
						var divLine = ZA.createDiv(divSplit,"","","div");
						$(divLine).css({
							height:1,
							top:6,
							left:20,
							width:258,
							borderTop:"1px solid #444",
						});
						var divLine = ZA.createDiv(divSplit,"","","div");
						$(divLine).css({
							height:1,
							top:6,
							right:16,
							width:18,
							borderTop:"1px solid #444",
						});
						
						//Buy Now
						var divBuy = ZA.createDiv(divActions,"","buyNowHolder","div");
						$(divBuy).css({
							height:50,
							position:"relative",
							width:"100%"
						});
						var div = ZA.createDiv(divBuy,"buyOutBid","","div");
						$(div).html('<span>Buyout</span> Price:');
						//Price display
						var div = ZA.createDiv(divBuy,"","","div");
						$(div).css({
							width:100,
							left:125,
							top:20,
							fontSize:14,
							fontWeight:"900",
							color:"#F2C126"
						});
						$(div).html(
							buyoutPrice+'&nbsp;TCG'
						);
						//Buy Button
						var div = ZA.createDiv(divBuy,"","buyNow","div");
						$(div).html('Buy Now');
						
						//Buy Out events
						$("#buyNow").click(function(){
							if(parseInt(ZA.sUserCredits) >= parseInt(buyoutPrice))
							{
								//User has enough credits to buy item - confirm buyout
								if(confirm('Click OK to buy out this item for '+buyoutPrice+' TCG credits\n\nWARNING: This transaction cannot be reversed!\n ')){
									var marketID = ZA.getXML(aXML,"details/market_id");
									ZU.aWindowAuction.clickBuy(marketID, buyoutPrice);
								}
							}
							else
							{
								//User has insuffient credits to buy item
								var response = "Insufficient credits to purchase item.";
      							var icon = "-697px -63px";
								ZS.showWindow(icon,response);
							}
						});
						
					}
					else
					{
						$(divActions).css({top:iTop+25});
						$(divBid).css({"padding-bottom":"7px"});
					}
				
				}
			}
			
			ZU.timeLeftInterval = setTimeout("ZU.aWindowAuction.updateTimeLeft()",1000);
		}
		
		var divClose = ZA.createDiv(divData,"cmdButton","","div");
      $(divClose).html('Close');
      $(divClose).css({left:535,top:365,zIndex:1000});
      $(divClose).click(function(){
        ZU.aWindowAuction.clickCloseA()
      });
		
		//Bidding History
		var divBiddingHistory = ZA.createDiv(divData,"","biddingHistoryHolder","div");
		$(divBiddingHistory).css({
			width:220,
			height:365,
			left:365,
			borderLeft:"1px solid #bbb",
			padding:10
		});
		
		ZU.aWindowAuction.setBiddingHistory(aXML);
	
	});
	
};

WORK_AuctionWindow.prototype.setBiddingHistory=function(aXML){

	var divBiddingHistory = $("#biddingHistoryHolder").get()[0];
	var div = ZA.createDiv(divBiddingHistory,"txtGreen","","div");
	$(div).css({
		position:"relative",
		fontSize:12,
		fontWeight:"bold",
		marginBottom:10
	});
	$(div).html("Bidding History");
	var bidCount = parseInt(ZA.getXML(aXML,"details/history/bid_count"),10);
	var i;
	var rowbg;
	if(bidCount > 0)
	{
		for(i=0; i<bidCount; i++)
		{
			if(i > 9){return false;}
			if(i%2==1){rowbg='#555';}else{rowbg='#666';}
			var div = ZA.createDiv(divBiddingHistory,"","","div");
			$(div).css({
				position:"relative",
				textAlign:"left",
				background:rowbg,
				padding:3
			});
			$(div).html(
				'<table width="100%"><tr><td width="105">'+
				ZA.getXML(aXML,"details/history/bid_"+i+"/date")+'</td><td align="left">'+
				ZA.formatUsername(ZA.getXML(aXML,"details/history/bid_"+i+"/user"))+'</td><td align="right" width="25">'+
				ZA.getXML(aXML,"details/history/bid_"+i+"/amount")+
				'</td></tr></table>'
			);
		}
	}
	else
	{
		var div = ZA.createDiv(divBiddingHistory,"","","div");
		$(div).css({
			padding:10,
			position:"relative"
		});
		$(div).html('No bids');
	}
};


/*********** click bid button */
WORK_AuctionWindow.prototype.clickBid=function(marketID, bidAmount){
	//return function() {
		ZA.callAjax(ZU.sURL+"?auction=1&market="+marketID+"&placebid="+bidAmount,function(xml){
			$("#placeBid").removeClass('bidDisabled');
			if(ZA.getXML(xml,"value") == '1'){
				$("#currentBid").html(bidAmount);
				$("#bidValue").val(parseInt(bidAmount)+1);
				$("#bidLess").addClass("bidAdjusterDisabled");
				$("#lblCurrentBid").html('Current Bid:');
				$("#highestBidder").html('You');
				var bidCount = parseInt(ZA.getXML(xml,"details/history/bid_count"),10);
				var bids = bidCount + ' bid';
				if(parseInt(bidCount) > 1 || parseInt(bidCount) < 1){bids = bids+'s';}
				$("#bidCount").html(bids);
				$("#biddingHistoryHolder").html('');
				ZU.aWindowAuction.setBiddingHistory(xml);
				$("#iconHighestBidder").show('fast');
				//update user credits display
				var credits = ZA.getXML(xml,"credits");
	        	ZA.sUserCredits=credits;
	        	ZA.oPlayerBar.update({credits:ZA.sUserCredits});
	        	//let user know
				var icon = "-667px -63px";
	        	ZS.showWindow(icon,'Auction: Your bid has been placed',5000);
			}
			else{
				var response = ZA.getXML(xml,"message");
				var icon = "-697px -63px";
	        		ZS.showWindow(icon,response,5000);
			}
		});
	//};
};

/*********** click buy button */
WORK_AuctionWindow.prototype.clickBuy=function(marketID, price){
	//return function() {
		//alert('buying out...'+marketID+'\n'+price);
		ZA.callAjax(ZU.sURL+"?buyout=1&market="+marketID+"&price="+price,function(xml){
			
			if(ZA.getXML(xml,"value")=='1')
			{
				ZU.stopListening();
				ZU.purchased = true;
				
				var response = "Purchase successful. Your card has been added to your album.";
	        	var icon = "-667px -63px";
	        	ZS.showWindow(icon,response);
	        	
	        	//Update user credits
	        	ZA.sUserCredits=ZA.getXML(xml,"credits");
	        	ZA.oPlayerBar.update({credits:ZA.sUserCredits});
	        	
	        	//Reload my album
				$(ZL.divData).html('<div class="loader"></div>');
				ZA.callAjax(ZL.sURL+"?init=1",function(xml){ZL.init(xml);});
				
	        	//Close auction window
	        	//This will reload auction component
	        	ZU.aWindowAuction.clickCloseA();
				
				//Reload auction
				ZU.init();
				
				//reload card comparison
				//ZA.callAjax(ZC.sURL+"?init=1",function(xml){ZC.init(xml);});
			}
			else{
				var response = "Purchase unsuccessful. Please try again.";
				var icon = "-697px -63px";
	        	ZS.showWindow(icon,response);
			}
        	
		});
	//};
};

WORK_AuctionWindow.prototype.updateTimeLeft=function()
{
	var timeleft = ZU.getTimeLeft(ZU.enddate);
	$("#auctionTimeLeft").html(timeleft);
	if(timeleft == 'Finished'){
		$("#lblEnds").html('Ended:');
		$("#lblBidder").html('Winner:');
		$("#lblCurrentBid").html('Winning Bid:');
		$("#actionsHolder").html('<div style="position:relative;padding:26px;font-size:16px;color:#999;">Bidding has ended on this item.</div>');
	}
	else{
		ZU.timeLeftInterval = setTimeout("ZU.aWindowAuction.updateTimeLeft()",1000);
	}
};

WORK_AuctionWindow.prototype.closeError=function(){
	var divBody=document.getElementsByTagName("body")[0];
	var divWin=document.getElementById("windowcontainer_-1");
	var divData=document.getElementById("window_-1");
	var divCloak=document.getElementById("bodycloak_-1");
	if (divWin) {
		divBody.removeChild(divWin);
	}
	if (divData) {
		divBody.removeChild(divData);
	}
	if (divCloak) {
		divBody.removeChild(divCloak);
	}
};

WORK_AuctionWindow.prototype.closeFirstVisit=function(){
	ZA.refreshBrowser();
};

/** 
=============================================================================
	finish NEWDECK CLASS */	
	WORK_AuctionWindow._iInited=1;
	}
};//END function WORK_Newdeck()



var ZU=new WORK_Auction();
ZU.iComponentNo=5;
ZA.aComponents[ZU.iComponentNo].fMaximizeFunction=ZU.maximize;
ZU.init();
