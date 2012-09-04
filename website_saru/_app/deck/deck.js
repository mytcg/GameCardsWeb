function WORK_Deck()
{
  this.iComponentNo=3;
  this.divData=0;
  this.sXML = "";
  this.productCount = 0;
  this.perPage = 6;
  this.currentPage = 0;
  this.divHolder = null;
  this.divList = null;
  this.divListLarge = null;
  this.divLargeDeckList = null;
  this.divScroll = null;
  this.buttonNew = null;
  this.buttonClose = null;
  this.imgAll = "url(_site/all.png)";
  this.iDeckID = null;
  this.addDropped = false;
  this.addDropzone = 0;
  this.remDropped = false;
  this.userCards = null;
  this.dragging = false;
  this.dragged = false;
  this.deckChanged = false;
  this.aWindowNewDeck = null;
  this.cXML = "";
  this.deckLo = 1;
  this.deckHi = 16;
  this.location = null;
  var obj = null;
  
	if (typeof WORK_Deck._iInited=="undefined"){

    WORK_Deck.prototype.init=function(sXML){
      ZD.divData=document.getElementById("window_"+ZD.iComponentNo);
      ZD.sXML = sXML;
      
      $(ZD.divData).html("");
      
      //DRAW DIVS FOR LIST AND SCROLLER
      ZD.productCount = ZA.getXML(sXML, "deckcount");
      var iHeightOfList = ZD.productCount*165;
      
      ZD.divHolder = ZA.createDiv(ZD.divData,"","divDeckList","div");
      $(ZD.divHolder).css({ position:"absolute",top:0,left:0,width:"100%",margin:"0 auto",height:165,overflow:"hidden" });
      
      ZD.divList = ZA.createDiv(ZD.divHolder,"","divDeckList","div");
      //$(ZD.divList).css({ position:"absolute",top:0,left:0,width:"100%",height:iHeightOfList });
      
      ZD.divScroll = ZA.createDiv(ZD.divData,"","divDeckScroll","div");
      //$(ZD.divScroll).css({ top:161,left:0,position:"absolute",width:"100%",height:20 });
      
      ZD.buildList();
    };

    WORK_Deck.prototype.buildList=function(){
		var itemCount = 0;
		if(ZD.productCount > 0)
		{
			for(a=0; a<ZD.productCount; a++)
			{
		        var pageBlock = ZA.createDiv(ZD.divList,"","","div");
		        $(pageBlock).css({ width:152,height:165,position:"relative",margin:"0 auto" });
		        
		        var infoBox = ZA.createDiv(pageBlock,"","","div");
            	$(infoBox).css({ width:152,height:42,margin:"0 auto",opacity:1,background:"url(_site/info_title_bg.png)" });
		        
		        var itemImage = ZA.createDiv(pageBlock,"deckImage","","img");
		        $(itemImage).css({ width:85,height:120,marginTop:40});
		        itemImage.src = ZA.getXML(ZD.sXML,"decks/deck_"+a+"/image");
		        itemImage.alt = a;
		        
		        var textBox = ZA.createDiv(pageBlock,"","","div");
	            $(textBox).css({ paddingTop:4,paddingRight:4,fontSize:9,lineHeight:"8px",width:110,height:20,top:10,color:"#FFF",right:5,textAlign:"right" });          
	            $(textBox).html(ZA.getXML(ZD.sXML,"decks/deck_"+a+"/description")+' ('+ZA.getXML(ZD.sXML,"decks/deck_"+a+"/ranking")+')');
	            
	            var editButton = ZA.createDiv(infoBox,"editButton","","div");

		    }
			$(".editButton").click(function(){
		      	ZA.maximizeWindowA(ZD.iComponentNo);
		      	$(".editButton[alt='"+$(this).attr('alt')+"']").click();
			});
      		ZD.buildScroller();
		}
		else
		{
			var divNone = ZA.createDiv(ZD.divList);
			$(divNone).css({
				width:"100%",
				top:90
			});
			$(divNone).html('No decks<br /><div class="cmdButton" id="createNewDeck" style="position:relative;width:105px;margin-left:auto;margin-right:auto;margin-top:5px;">Create a deck now</div>');
			$("#createNewDeck").click(function(){
				ZA.maximizeWindowA(ZD.iComponentNo);
				ZD.aWindowNewDeck = new WORK_Newdeck();
				ZD.aWindowNewDeck.create();
				return false;
			});
		}
    };

	WORK_Deck.prototype.flipCard=function(iIsFront){
		return function(){
			if(!ZD.dragged)
			{
				var divFull=document.getElementById("cardfull");
				if (iIsFront){
					var divImage=document.getElementById("cardfull1");
					var divImage2=document.getElementById("cardfull0");
				} else {
					var divImage=document.getElementById("cardfull0");
					var divImage2=document.getElementById("cardfull1");			
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
				ZD.dragged = false;
			}
		};
	};
	
	WORK_Deck.prototype.clickCloseFullImage=function(){
		return function(){
			var divBody=document.getElementsByTagName("body")[0];
			var divFull=document.getElementById("cardfull");
			if (divFull){
				divBody.removeChild(divFull);
			}
		};
	};

	WORK_Deck.prototype.clickShowFullImage=function(iThumbnail, sImg){
		var divThumbnail=document.getElementById(iThumbnail);
		var xy = ZA.findXY(divThumbnail);
		var divWindow=document.getElementById("window_"+ZD.iComponentNo);
		var iWidthWindow=parseInt(divWindow.style.width);
		var iLeft = xy[0]+42;
		var iTop = xy[1]-65;
		var divBody=document.getElementsByTagName("body")[0];
		var divFull=document.getElementById("cardfull");
		if (divFull){
			divBody.removeChild(divFull);
		}
		
		//Avoid card displaying off page on maximized window
		if(iTop < 250){ iTop = 250; }
		if(iTop > 275){}

		var divFull=ZA.createDiv(divBody,"cardfull","cardfull");
		var divInfo=ZA.createDiv(divFull,"cardfullinfo");
		//divInfo.innerHTML="Close";
		$(divInfo).attr('title','Close');
		divInfo.onclick=ZD.clickCloseFullImage();
		var divImg=ZA.createDiv(divFull,"cardfullimage","cardfull1","img");
		divImg.onclick=ZD.flipCard(1);
		$(divImg).css({display:"none"});
		divImg.src=sImg+"_front.jpg";
		var divImg2=ZA.createDiv(divFull,"cardfullimage","cardfull0","img");
		divImg2.onclick=ZD.flipCard(0);
		divImg2.src=sImg+"_web.jpg";
			$(divFull).css({
			left:iLeft+"px",
			top:iTop+"px"
		});
		
		$(divFull).animate({
			left:(iLeft-125)+"px",
			top:(iTop-175)+"px",
			width:"250px",
			height:"350px"
		},
		function(){
			$(divImg).css({display:"block",height:"350px"});
			$(divImg2).css({display:"none",height:"350px"});
			divImg2.src=sImg+"_back.jpg";
		});
		ZA.setNextZIndex(divFull);
		
		$("#cardfull").draggable("destroy");
		$("#cardfull").draggable({
			start: function(){
				ZD.dragged = true;
			},
			containment: "body"
		});
		
		//-----------------------------------------------------
		//Card menu tabs
		//-----------------------------------------------------
		/*
		var divMenu = ZA.createDiv(divFull,"cardMenu");
		
		//Compare tab
		var divTab = ZA.createDiv(divMenu,"menuTab","tabCompare");
		$(divTab).css({
			background:"url(_site/all.png) -735px -125px no-repeat",
			top:70
		});
		$(divTab).attr('title','Compare card');
		$(divTab).click(function(){
			var cardid = $(divThumbnail).find(".card_id").val();
			ZA.showCompare(cardid);
		});
		*/
		//-----------------------------------------------------
		//END OF: Card menu tabs
		//-----------------------------------------------------
	};
    
    
    WORK_Deck.prototype.buildDeckViewer=function(deckIndex, aXML)
    {
    	$(ZD.divData).find(".mainContainer").remove();
    	
    	var deckCardLimit = 23;
    	var deckCardCount = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cardcount");
    	var userCardCount = ZA.getXML(aXML, "cardcount");
    	
    	ZD.addDropzone = userCardCount;
    	ZD.iDeckID = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/deckid");
    	
    	var mainContainer = ZA.createDiv(ZD.divData,"mainContainer","","div");
    	$(mainContainer).css({
    		top:0,
    		width:"100%",
    		display:"none"
    	});
    	
    	// Cards in deck
    	
    	var deckCardsHolder = ZA.createDiv(mainContainer,"","deckcontainer","div");
    	$(deckCardsHolder).css({
    		top:0,
    		height:652,
    		width:385,
    	});
    	$(deckCardsHolder).html('<h2>Cards in Deck: '+ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/description")+'</h2>');
    	var deckCards = ZA.createDiv(deckCardsHolder,"deckcardholders","","div");
    	$(deckCards).css({
    		left:5
    	});
    	var iLeft = 0;
    	var iTop = 0;
    	for(var i=0; i<deckCardLimit; i++)
        {
        	var id = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cards/card_"+i+"/usercardid");
        	var position = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cards/card_"+i+"/description");
        	var cardHolder = ZA.createDiv(deckCards,"","deckcardholder_"+i,"div");
        	$(cardHolder).css({
        		width:70,
        		height:95,
        		margin:5,
        		left:iLeft,
        		top:iTop
        	});
        	var card_id = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cards/card_"+i+"/cardid");
        	
        	// Thumbnail display of card in deck
        	
    		var cardThumb = ZA.createDiv(cardHolder,"thumbholder","deckcard_"+i,"div");
    		$(cardThumb).css({
    			height:90,
    			width:64,
    			"box-shadow":"inset 0px 0px 10px #000",
    		});
    		
        	
    		if(i < deckCardCount)
    		{
	    		if(card_id){
		    		$(cardThumb).html(
		    			'<input type="hidden" class="usercard_id" value="'+id+'" />'+
		    			'<input type="hidden" class="card_id" value="'+card_id+'" />'
		    		);
		    	}else{
	    		$(cardThumb).html(position);
	    		};
    			var cardImage = ZA.createDiv(cardThumb,"cardImage",i.toString(),"img");
	        	var description = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cards/card_"+i+"/description");
	        	$(cardImage).attr({
	        		'title':description,
	        		'src':ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cards/card_"+i+"/thumbnail")
	        	});
	        	$(cardImage).css({
	        		width:64,
	        		height:90
	        	});
	        	
	        	// var cardDescription = ZA.createDiv(cardThumb,"","","div");
	        	// $(cardDescription).css({
	        		// width:"100%",
	        		// 'text-align':'center'
	    		// });
	    		// var label = ZA.getLimitedString(description, 24, ' ');
	    		// var labeltitle = description;
		        // if(label.length < description.length){
		        	// label+='..';
		        // }
	        	// $(cardDescription).html('<span title="'+labeltitle+'">'+label+'</span>');
	        	
    		}
        	
	        iLeft += 75;
	        if ((i+1) % 5 == 0) {
	          iLeft = 0;
	          iTop += 110;
	          
	        }
        }
    	
    	// User's available cards
    	
    	var userCardsHolder = ZA.createDiv(mainContainer,"","cardscontainer","div");
    	$(userCardsHolder).css({
    		"border":"1px solid transparent",
    		paddingLeft:15,
    		top:0,
    		left:385,
    		width:382,
    		height:652,
    	});
    	$(userCardsHolder).html('<h2>Available Cards: Your Collection</h2>');
    	var userCards = ZA.createDiv(userCardsHolder,"","availablecardholders","div");
    	$(userCards).css({
    		top:35,
    		width:547
    	});
    	//add user cards
		for(var i=0; i<userCardCount; i++)
        {
    		var description = ZA.getXML(aXML, "cards/card_"+i+"/description");
        	var cardHolder = ZA.createDiv(userCards,"","availablecardholder_"+i,"div");
        	$(cardHolder).css({
        		width:64,
        		height:105,
        		"margin":"5px 10px 0 0",
        		"float":"left",
        		"position":"relative"
        	});
        	var card_id = ZA.getXML(aXML, "cards/card_"+i+"/cardid");
    		$(cardHolder).html(
    			'<input type="hidden" class="card_id" value="'+card_id+'" />'
    		);
        	var cardEmpty = ZA.createDiv(cardHolder,"noimage","","div");
        	var img = ZA.getXML(aXML, "cards/card_"+i+"/image");
        	var cardThumb = ZA.createDiv(cardHolder,"thumb","","img");
        	cardThumb.src = ZA.getXML(aXML, "cards/card_"+i+"/thumbnail");
        	$(cardThumb).attr({
        		'id':card_id,
        		'alt':i.toString(),
        		'title':description
        	});
        	$(cardThumb).css({
        		width:64,
        		height:90
        	});
        	
        	// var cardDescription = ZA.createDiv(cardHolder,"","","div");
        	// $(cardDescription).css({
        		// width:"100%",
        		// 'text-align':'center'
    		// });
    		// var label = ZA.getLimitedString(description, 11, ' ');
    		// var labeltitle = description;
	        // if(label.length < description.length){
	        	// label+='..';
	        // }
        	// $(cardDescription).html('<span title="'+labeltitle+'">'+label+'</span>');
        	
    		// var avail = ZA.getXML(aXML, "cards/card_"+i+"/avail");
        	// var availDisplay = ZA.createDiv(cardHolder,"avail","","div");
        	// $(availDisplay).html(avail);
        	// $(availDisplay).css({
        		// top:-5,
        		// left:-2,
        		// padding:"0px 2px",
        		// "z-index":99,
        		// "background":"#000",
        		// "color":"#fff",
        		// "font-weight":"bold",
        		// "-moz-border-radius":5
        	// });
        	// if(avail == '1'){
        		// $(availDisplay).hide();
        	// }
        }
        
    	// Fresh start for deck viewer - nothing has changed yet
      	//ZD.deckChanged = false;
      	
        ZD.activateRemzone();
        ZD.activateRemovables();
		ZD.activateAddzone();
        ZD.activateAddables();
    	
    	//Show the deck viewer
    	$(mainContainer).fadeIn(300);
    	
		ZD.activateCardsScrollbar();
    };
    
    WORK_Deck.prototype.activateCardsScrollbar=function(){
		$("#availablecardholders").jScrollPane();
		$("#availablecardholders").find(".jspContainer").css({
			'overflow':'visible',
			width:375
		});
		$("#availablecardholders").find(".jspPane").css({
			width:375,
			"padding-bottom":10
		});
		// $("#cardscontainer").css('width','390px');
		$("#availablecardholders").css('width','390px');
    };

    WORK_Deck.prototype.activateRemzone=function()
    {
    	// Remove all previous droppable implementations
    	$("#cardscontainer").droppable("destroy");
    	// Droppable area for cards in deck
    	// to remove cards from deck
    	$("#cardscontainer").droppable({
    		accept: ".cardImage",
    		activeClass: "highlight",
    		hoverClass: "lightup",
    		drop: function(event, ui){
    			ZD.remDropped = true;
    		}
    	});	
    };
    
    
    WORK_Deck.prototype.activateRemovables=function()
    {
    	// Add card viewer to click event
    	$(".cardImage").unbind();
        $(".cardImage").click(function(){
        	if(!ZD.dragging)
        	{
	        	var id = "deckcard_"+$(this).attr('id');
	        	var image = $(this).attr('src').split('_')[0];
	        	ZD.clickShowFullImage(id, image);
        	}
        });
        $(".cardImage").css('cursor','pointer');
    	
    	// Remove previous draggable implementations
    	$(".cardImage").each(function(){
    		$(this).draggable("destroy");
    	});
    	
    	// Card dragging from deck to available cards
    	$(".cardImage").draggable({
    		containment: "#window_"+ZD.iComponentNo,
    		helper: "original",
    		revert: "invalid",
    		stack: ".cardImage",
    		start: function(event, ui){
    			$("#cardscontainer").css('overflow','hidden');
    			ZD.remDropped = false;
    			ZD.dragging = true;
    		},
    		stop: function(event, ui){
    			ZD.dragging = false;
    			if(ZD.remDropped)
    			{
    				var card_id = $(this).parent().find(".card_id").val();
    				var usercard_id = $(this).parent().find(".usercard_id").val();
    				var card_src = $(this).attr('src');
    				var description = $(this).attr('title');
    				$(this).parent().html('');
    				$(this).remove();
    				
					ZA.callAjax("_app/deck/?remove=1&deckid="+ZD.iDeckID+"&id="+card_id,function(reply){
	    				if(reply == '1')
	    				{
							//Adding removed card back to available cards
							// var i = $("img[id='"+card_id+"']").attr('alt');
							var availHolder = $("#availablecardholder_"+i);
							// if($(availHolder).size())
							// {
		    					// var avail = parseInt(availHolder.text(),10);
		    					// if(avail == 0){
		    						// $("#availablecardholder_"+i).find(".noimage").hide();
		    						// $("#availablecardholder_"+i).show();
		    					// }
		    					// else if(avail == 1){ 
		    						$(availHolder).show(); 
		    						// }
		    					// avail++;
		    					// $(availHolder).text(avail);
							// }
							// else
							// {
	    						// var i = $("div[id^='availablecardholder_']").size();
	    						// var userCards = $("#availablecardholders").find(".jspPane").get()[0];
					        	// var cardHolder = ZA.createDiv(userCards,"","availablecardholder_"+i,"div");
					        	// $(cardHolder).css({
					        		// width:64,
					        		// height:105,
					        		// "margin":"5px 10px 0 0",
					        		// "float":"left",
					        		// "position":"relative"
					        	// });
					        	// var cardEmpty = ZA.createDiv(cardHolder,"noimage","","div");
					        	// var cardThumb = ZA.createDiv(cardHolder,"thumb",card_id,"img");
					        	// $(cardThumb).attr({
					        		// 'alt':i,
					        		// 'src':card_src,
					        		// 'title':description
					        	// });
					        	// $(cardThumb).css({
					        		// width:64,
					        		// height:90
					        	// });
// 					        	
					        	// // var cardDescription = ZA.createDiv(cardHolder,"","","div");
					        	// // $(cardDescription).css({
					        		// // width:"100%",
					        		// // 'text-align':'center'
					    		// // });
					    		// // var label = ZA.getLimitedString(description, 14, ' ');
					    		// // var labeltitle = description;
						        // // if(label.length < description.length){
						        	// // label+='..';
						        // // }
					        	// // $(cardDescription).html('<span title="'+description+'">'+label+'</span>');
// 					        	
					    		// var avail = 1;
					        	// var availDisplay = ZA.createDiv(cardHolder,"avail","","div");
					        	// $(availDisplay).html(avail);
					        	// $(availDisplay).css({
					        		// top:-6,
					        		// left:-2,
					        		// padding:"0px 2px",
					        		// "z-index":99,
					        		// "background":"#000",
					        		// "color":"#fff",
					        		// "font-weight":"bold",
					        		// "-moz-border-radius":5
					        	// });
					        	// $(availDisplay).hide();
// 					        	
							// }
							
							ZD.setAddDropzone();
	    					ZD.activateAddables();
	    					ZD.activateAddzone();
	    					ZD.deckChanged = true;
	    					
    						//re-initialise the scrollbar
    						ZD.activateCardsScrollbar();
						}
	    				else
	    				{
	    					alert('Remove failed!');
	    				}
					});
    			}
    		},
    		zIndex: 2700
    	});
    	
    };
    
    
    WORK_Deck.prototype.activateAddables=function()
    {
    	// Add card viewer to click event
    	$(".thumb").unbind();
        $(".thumb").click(function(){
        	if(!ZD.dragging)
        	{
	        	var id = "availablecardholder_"+$(this).attr('alt');
	        	var image = $(this).attr('src').split('_')[0];
	        	ZD.clickShowFullImage(id, image);
        	}
        });
        $(".thumb").css('cursor','pointer');
	        
    	// Remove previous draggable implementations
    	$(".thumb").each(function(){
    		$(this).draggable("destroy");
    	});
    	
    	// Card dragging to deck
    	$(".thumb").draggable({
    		containment: "#window_"+ZD.iComponentNo,
    		cursor: "move",
    		helper: "clone",
    		revert: "invalid",
    		stack: ".thumb",
    		start: function(event, ui){
    			$("#cardscontainer").css('overflow','visible');
    			ZD.addDropped = false;
    			var a = "#availablecardholder_"+$(this).attr('alt');
    			// if($(a).size())
    			// {
    				// $(a).find(".thumb").css("z-index", 1000);
        			// var availVal = parseInt($(a).find(".avail").text());
        			// $(a).find(".avail").text(--availVal);
        			// if(availVal < 2)
        			// {
        				// $(a).find(".avail").hide();
        				// if(availVal < 1)
        				// {
        					// $(a).find('.noimage').show();
        				// }
        			// }
    			// }
    		},
    		stop: function(event, ui) {
    			var a = "#availablecardholder_"+$(this).attr('alt');
    			//var availVal = parseInt($(a).find(".avail").text());
    			if($(a).size())
    			{
    				if(ZD.addDropped)
    				{
    					
    					var cardid = $(this).attr('id');
    					var src = $(this).attr('src');
    					var description = $(this).attr('title');
    					var position = "deckcard_"+$(this).attr('title');
    					ZD.setAddDropzone();

    					ZA.callAjax("_app/deck/?add=1&cardid="+cardid+"&deckid="+ZD.iDeckID+"&position="+ZD.addDropzone,function(usercardid){
    						if(usercardid != '0')
    						{
    							var thumbHolder = document.getElementById('deckcard_'+ZD.addDropzone);
    							$(thumbHolder).html(
					    			//'<input type="hidden" class="usercard_id" value="'+usercardid+'" />'+
					    			'<input type="hidden" class="card_id" value="'+cardid+'" />'
					    		);
				    			var cardImage = ZA.createDiv(thumbHolder,"cardImage","","img");
					        	cardImage.src = src;
					        	$(cardImage).attr({
					        		'id':ZD.addDropzone,
					        		'src':src,
					        		'title':description
					        	});
					        	$(cardImage).css({
					        		width:64,
					        		height:90
					        	});
					        	
					        	// var cardDescription = ZA.createDiv(thumbHolder,"","","div");
					        	// $(cardDescription).css({
					        		// width:"100%",
					        		// 'text-align':'center'
					    		// });
					    		// var label = ZA.getLimitedString(description, 24, ' ');
						        // if(label.length < description.length){
						        	// label+='..';
						        // }
					        	// $(cardDescription).html('<span title="'+description+'">'+label+'</span>');
					        	
					    		ZD.activateRemovables();
					    		ZD.setAddDropzone();
		    					ZD.activateAddables();
		    					ZD.activateAddzone();
      							ZD.deckChanged = true;
      							
    						}
    						else
    						{
    							alert('Add failed!');
    						}
						});
    					// if(availVal == '0')
    					// {
    						// //no cards left so hide this card placeholder
    						// $(a).hide();
    						// //re-initialise the scrollbar
    						// ZD.activateCardsScrollbar();
    					// }
    					ZD.activateAddables();
    					ZD.activateAddzone();
    				}
    			}
    		},
    		zIndex: 2700
    	});
    };
    
    WORK_Deck.prototype.setAddDropzone=function(){
    	ZD.addDropzone = ZD.location;
    	// ZD.addDropzone = 23;
		// $("div[id^='deckcardholder_']").each(function(){
			// if(!$(this).find('img').size())
			// {
				// ZD.addDropzone = i;
				 // console.log(ZD.addDropzone);
				// return false;
			// }
			// i++;
		// });
    };
    
    WORK_Deck.prototype.activateAddzone=function(){
    	
    	// Remove all previous droppable implementations
    	$(".thumbholder").droppable("destroy");
    	
    	// Droppable area for available cards
    	// to add card to deck
		$(".thumbholder").droppable(
		{
		    accept: ".thumb",
		    activeClass: "highlight",
		    hoverClass: "lightup",
		    drop: function(event, ui){
		    	// Check that deck is not full
		    	if(ZD.addDropzone >= 23)
				{
					var icon = "-697px -63px";
					ZS.showWindow(icon,"Deck is full");
				}
				else
				{
						ZD.location = $(this).attr('title');
						ZD.addDropped = true;
					
				}
		    }
		});
		
    };
    
	
    WORK_Deck.prototype.deckCardCount=function(card_id){
    	return parseInt($("#deckcontainer").find("input.card_id[value='"+card_id+"']").size(),10);
    };
    
    
    WORK_Deck.prototype.buildListLarge=function(){
      ZD.divListLarge = ZA.createDiv(ZD.divData,"","divDeckListLarge","div");
      $(ZD.divListLarge).css({ opacity:0,position:"relative",width:"100%",height:535,});

      //Top controls main page
      var topMenuMain = ZA.createDiv(ZD.divData,"","deckTopMenu","div");
      $(topMenuMain).css({ bottom:0,left:0,width:774,height:23,"z-index":"9999",padding:5,});
      //Button: New
      var buttonNew = ZA.createDiv(topMenuMain,"deckMenuItem","deckNew","div");
      // $(buttonNew).html("Create a New Deck");
      // $(buttonNew).addClass('cmdButton');
      // $(buttonNew).click(function(){
		// ZD.aWindowNewDeck = new WORK_Newdeck();
		// ZD.aWindowNewDeck.create();
      //});
      //Button: Close deck viewer
	  var buttonClose = ZA.createDiv(topMenuMain,"deckMenuItem","closeDeckViewer","div");
	  $(buttonClose).html('Close');
	  $(buttonClose).addClass('cmdButton');
	  $(buttonClose).hide();
	  $(buttonClose).click(function(){
		$(".cardfullinfo").click();
    	$("div.mainContainer").fadeOut(300);
		$("div.mainContainer").remove();
		if(ZD.deckChanged)
		{
        	//Reload my album
			$(ZL.divData).html('<div class="loader"></div>');
			ZA.callAjax(ZL.sURL+"?init=1",function(xml){ZL.init(xml);});
        	
			//Reset the decks
    		ZD = new WORK_Deck();
			ZA.callAjax("_app/deck/?init=1",function(xml){
				ZD.init(xml);
				var iSpeed = 300;
		        $(ZD.divList).animate({opacity:0},iSpeed);
		        $(ZD.divScroll).animate({opacity:0},iSpeed);
		        if (!ZD.divListLarge){
		          ZD.buildListLarge();
		        }
		        $(ZD.divListLarge).animate({opacity:1},iSpeed,function(){  });
		        ZD.activateDecksScrollbar();
			});
		}
		else
		{
			$(ZD.divListLarge).fadeIn(600);
			$(ZD.buttonClose).hide();
			$(ZD.buttonNew).show();
		}
	  });
	  
	  ZD.buttonNew = buttonNew;
	  ZD.buttonClose = buttonClose;
      
      //Deck list
      var deckList = ZA.createDiv(ZD.divListLarge,"","decksholder","div");
      $(deckList).css({ top:28,left:0,width:740,height:507 });
      ZD.divLargeDeckList = deckList;
      
      if(ZD.productCount > 0)
      {
	      for(a=0; a<ZD.productCount; a++)
	      {
	        var deckBlock = ZA.createDiv(deckList,"deckBlock","","div");
	        $(deckBlock).css({
	        	"border":"1px solid #ccc",
	        	position:"relative",
	        	"float":"left",
	        	width:150,
	        	height:232,
	        	"margin-top":8,
	        	"margin-left":8
	        });
	        
	        //Deck name
	        var blockTitle = ZA.createDiv(deckBlock,"","","div");
	        $(blockTitle).css({ paddingTop:3,top:5,left:0,width:150,height:16,fontWeight:"bold" });
	        var description = ZA.getXML(ZD.sXML, "decks/deck_"+a+"/description");
			var label = ZA.getLimitedString(description, 24, ' ');
	        if(label.length < description.length){
	        	label+='..';
	        }
	        $(blockTitle).html(label);
	        
	        //Edit deck icon
	        // var iconEdit = ZA.createDiv(deckBlock,"editIcon","","div");
	        // $(iconEdit).css({
				// top:25,
				// right:5,
	        // });
	        // $(iconEdit).attr('id',a.toString());
	        
	        //Delete deck icon
	        // var iconDelete = ZA.createDiv(deckBlock,"deleteIcon","","div");
	        // $(iconDelete).css({
				// top:40,
				// right:5,
	        // });
	        // $(iconDelete).attr('id',ZA.getXML(ZD.sXML, "decks/deck_"+a+"/deckid"));
	        // $(iconDelete).attr('alt',description);
	        
	        var imgBlock = ZA.createDiv(deckBlock,"editDeck","","img");
	        $(imgBlock).css({ cursor:"pointer",width:104,height:140,top:25,left:23,position:"absolute" });
	        $(imgBlock).attr('alt',a);
	        $(imgBlock).attr('id',ZA.getXML(ZD.sXML, "decks/deck_"+a+"/deckid"));
	        $(imgBlock).attr('title','View Deck');
	        imgBlock.src = ZA.getXML(ZD.sXML, "decks/deck_"+a+"/image");
	        
	        var iTop = 170;
	        //Deck cards count
	        var lbl = ZA.createDiv(deckBlock);
	        $(lbl).css({
	        	top:iTop,
	        	left:5
	        });
	        $(lbl).html('Cards:');
	        var blockInfo = ZA.createDiv(deckBlock,"","","div");
	        $(blockInfo).css({
	        	top:iTop,
	        	right:5
	        });
	        //Number of cards in deck label
	        var cardCount = ZA.getXML(ZD.sXML, "decks/deck_"+a+"/cardcountowned");
	        $(blockInfo).html(cardCount+' / 23');
	        if(cardCount != 23){
	        	$(blockInfo).addClass('incomplete');
	        }
	        else{
	        	$(blockInfo).addClass('txtGreen');
	        }
	        iTop+=15;
	        //Deck category
	        var lbl = ZA.createDiv(deckBlock);
	        $(lbl).css({
	        	top:iTop,
	        	left:5
	        });
	        $(lbl).html('Category:');
	        var blockInfo = ZA.createDiv(deckBlock,"","","div");
	        $(blockInfo).css({
	        	top:iTop,
	        	right:5
	        });
	        $(blockInfo).html( ZA.getXML(ZD.sXML, "decks/deck_"+a+"/categoryid") );
	        iTop+=15;
	        //End Date
	        var lbl = ZA.createDiv(deckBlock);
	        $(lbl).css({
	        	top:iTop,
	        	left:5
	        });
	        $(lbl).html('End Date:');
	        
	        var description = ZA.getXML(ZD.sXML, "decks/deck_"+a+"/enddate");
	        var label = ZA.getLimitedString(description, 14, ' ');
	        var labeltitle = '';
	        if(label.length < description.length){
	        	label+='..';
	        	labeltitle = ' title="'+description+'"';
	        }
	        var blockInfo = ZA.createDiv(deckBlock,"","","div");
	        $(blockInfo).css({
	        	top:iTop,
	        	right:5
	        });
	        $(blockInfo).html(label);
	        
	        //Deck ranking
	        // var lbl = ZA.createDiv(deckBlock);
	        // $(lbl).css({
	        	// top:iTop,
	        	// left:5
	        // });
	        // $(lbl).html('Ranking:');
	        // var blockInfo = ZA.createDiv(deckBlock,"","","div");
	        // $(blockInfo).css({
	        	// top:iTop,
	        	// right:5
	        // });
	        // $(blockInfo).html( ZA.getXML(ZD.sXML, "decks/deck_"+a+"/ranking") );
	        iTop+=15;
	        //Deck value
	        // var lbl = ZA.createDiv(deckBlock);
	        // $(lbl).css({
	        	// top:iTop,
	        	// left:5
	        // });
	        // $(lbl).html('Value:');
	        // var blockInfo = ZA.createDiv(deckBlock,"","","div");
	        // $(blockInfo).css({
	        	// top:iTop,
	        	// right:5,
	        	// fontWeight:"bold"
	        // });
	        // $(blockInfo).html( ZA.getXML(ZD.sXML, "decks/deck_"+a+"/value")+' TCG' );
	        // $(blockInfo).addClass('txtBlue');
	        
	      }
		  
	      //Click event handler for viewing deck
	      $(".editDeck").unbind();
	      $(".editDeck").click(function(){
	    	$(ZD.divListLarge).fadeOut(300);
	    	var index = $(this).attr('alt');
	    	var category_id = ZA.getXML(ZD.sXML, "decks/deck_"+index+"/categoryid");
	    	var active = ZA.getXML(ZD.sXML, "decks/deck_"+index+"/active");
	      	ZA.callAjax("_app/deck/?cards=1&category_id="+category_id,function(xml){
	      		if(active == 1){
	      			ZD.buildDeckViewer(index, xml);
			      	$(ZD.buttonNew).hide();
			      	$(ZD.buttonClose).show();
	      		}else{
	      			alert('Error occured, please try again');
	      		}
	  		});
	      });
	      
	      //Click event handler for editing deck
	      $(".editIcon").unbind();
	      $(".editIcon").click(function(){
	    	ZD.aWindowNewDeck = new WORK_Newdeck();
	    	ZD.aWindowNewDeck.create($(this).attr('id'));
	    	return false;
	      });
	      
	      //Click event handler for deleting deck
	      $(".deleteIcon").unbind();
	      $(".deleteIcon").click(function(){
	      	if(confirm('Click OK to delete deck: '+$(this).attr('alt')))
	      	{
	      		ZA.callAjax("_app/deck/?delete=1&deck_id="+$(this).attr('id'),function(response){
	      			if(response == '1')
	      			{
						//Delete successful
						var icon = "-667px -63px";
						ZS.showWindow(icon,"Deck successfully deleted",5000);
						
			        	//Reload my album
						$(ZL.divData).html('<div class="loader"></div>');
						ZA.callAjax(ZL.sURL+"?init=1",function(xml){ZL.init(xml);});
			        		
						//Reload the deck window
						$(ZD.divData).html('');
			    		ZD = new WORK_Deck();
						ZA.callAjax("_app/deck/?init=1",function(xml){
							ZD.init(xml);
							var iSpeed = 300;
					        $(ZD.divList).hide();
					        $(ZD.divScroll).hide();
					        if (!ZD.divListLarge){
					          ZD.buildListLarge();
					        }
					        $(ZD.divListLarge).animate({opacity:1},iSpeed,function(){ });
						});
	      			}
	      		});
	      	}
	      	return false;
	      });
	      
		  //Vertical scrollbar for decksholder
		  ZD.activateDecksScrollbar();
      }
      else
      {
      	var divNone = ZA.createDiv(deckList);
      	$(divNone).css({
      		width:"100%",
      		top:220
      	});
      	$(divNone).html('No decks');
      }
    };
    

    WORK_Deck.prototype.activateDecksScrollbar=function(){
		$(ZD.divLargeDeckList).jScrollPane();
		$(ZD.divLargeDeckList).find(".jspContainer").css({
		'overflow':'visible',
			width:"100%",
		});
		$(ZD.divLargeDeckList).find(".jspPane").css({
			width:"100%",
			"padding-bottom":8
		});
    };
    

    WORK_Deck.prototype.buildScroller=function(){
      var divArrowLeft = ZA.createDiv(ZD.divScroll,"","","div");
      $(divArrowLeft).css({ cursor:"pointer",position:"relative",cssFloat:"left",width:20,height:20,backgroundImage:ZD.imgAll,backgroundPosition:"-180px -60px" });
      $(divArrowLeft).click(function(e){
        if(ZD.currentPage != 0){
          ZD.currentPage--;
          ZD.gotoPage(ZD.currentPage);
        }
      });
      
      var divPageCountList = ZA.createDiv(ZD.divScroll,"","","div");
      var offsetCount = (194-(ZD.productCount*14))/2;
      $(divPageCountList).css({ marginLeft:offsetCount,marginTop:7,position:"relative",cssFloat:"left",width:(194-offsetCount),height:20});

      for(i=0;i<ZD.productCount;i++){
        var divPageIcon = ZA.createDiv(divPageCountList,"","","div");
        $(divPageIcon).css({ cursor:"pointer",marginRight:7,position:"relative",cssFloat:"left",width:7,height:7,backgroundImage:ZD.imgAll,backgroundPosition:"-130px -80px"});
        divPageIcon.onclick = (function() {
            var current_i = i;
            return function() {
                ZD.currentPage=current_i;
                ZD.gotoPage(current_i);
            }
         })();
      }
      
      ZD.gotoPage(0);
      var divArrowRight = ZA.createDiv(ZD.divScroll,"","","div");
      $(divArrowRight).css({ cursor:"pointer",position:"relative",cssFloat:"left",width:20,height:20,backgroundImage:ZD.imgAll,backgroundPosition:"-260px -60px" });
      $(divArrowRight).click(function(e){
        if(ZD.currentPage != ZD.productCount-1){
          ZD.currentPage++;
          ZD.gotoPage(ZD.currentPage);
        }
      });
    };

    WORK_Deck.prototype.gotoPage=function(page){
      for (i=0;i<ZD.productCount;i++){
        $(ZD.divScroll.childNodes[1].childNodes[i]).css({ backgroundPosition:"-130px -80px" }); 
      }
      $(ZD.divScroll.childNodes[1].childNodes[page]).css({ backgroundPosition:"-120px -80px" });
      var newPos = page*-165;
      $(ZD.divList).animate({top:newPos},600);
    };

    WORK_Deck.prototype.toggleMax=function(){
      var iSpeed = 300;
      if (ZA.aComponents[ZD.iComponentNo].iIsMaximized) {
        $(ZD.divList).animate({opacity:0},iSpeed);
        $(ZD.divScroll).animate({opacity:0},iSpeed);
        if (!ZD.divListLarge){
          ZD.buildListLarge();
        }
        $(ZD.divListLarge).animate({opacity:1},iSpeed,function(){  });
        $(ZD.divListLarge).show();
        $("div#deckTopMenu").show();
		$(ZD.buttonClose).hide();
		$(ZD.buttonNew).show();
      } else {
      	if(ZD.deckChanged)
      	{
        	//Reload my album
			$(ZL.divData).html('<div class="loader"></div>');
			ZA.callAjax(ZL.sURL+"?init=1",function(xml){ZL.init(xml);});
        	
			//Reset the decks
      		ZD = new WORK_Deck();
			ZA.callAjax("_app/deck/?init=1",function(xml){
				ZD.init(xml);
		        $(ZD.divList).animate({opacity:1},iSpeed);
		        $(ZD.divScroll).animate({opacity:1},iSpeed);
		        $(ZD.divListLarge).animate({opacity:0},iSpeed);
		        $(ZD.divListLarge).hide();
		        //reset the scroller
		        /*
		        ZD.currentPage = 0;
		        $(ZD.divScroll).remove();
		        ZD.divScroll = ZA.createDiv(ZD.divData,"","divDeckScroll","div");
		      	$(ZD.divScroll).css({ top:209,left:0,position:"absolute",width:294,height:20 });
		        ZD.buildScroller();
		        */
			});
      	}
      	else
      	{
	        $(ZD.divList).animate({opacity:1},iSpeed);
	        $(ZD.divScroll).animate({opacity:1},iSpeed);
	        $(ZD.divListLarge).animate({opacity:0},iSpeed);
	        $(ZD.divListLarge).hide();
	        //reset the scroller
	        /*
	        ZD.currentPage = 0;
	        $(ZD.divScroll).remove();
	        ZD.divScroll = ZA.createDiv(ZD.divData,"","divDeckScroll","div");
	      	$(ZD.divScroll).css({ top:209,left:0,position:"absolute",width:294,height:20 });
	        ZD.buildScroller();
	        */
		}
  		$("div.mainContainer").remove();
  		$("div#deckTopMenu").hide();
  		$(ZD.divList).show();
  		$(ZD.divScroll).show();
      }
    };


	}
	WORK_Deck._iInited=1;
};




/** ========================================================================
NEWDECK CLASS
*/
function WORK_Newdeck(){

if (typeof WORK_Newdeck._iInited=="undefined"){

/*********** close newdeck window */
WORK_Newdeck.prototype.clickClose=function(){
	return function() {
		ZD.aWindowNewDeck.clickCloseA();
	};
};

/*********** close newdeck window action */
WORK_Newdeck.prototype.clickCloseA=function(){
	var divBody=document.getElementsByTagName("body")[0];
	var divCloak=document.getElementById("bodycloak_0");
	var divNewdeck=document.getElementById("windowcontainer_0");
	var divData=document.getElementById("window_0");
	if (divNewdeck) {
		divBody.removeChild(divNewdeck);
		divBody.removeChild(divData);
	}
	if (divCloak) {
		divBody.removeChild(divCloak);
	}
};

/*********** create newdeck window */
WORK_Newdeck.prototype.create=function(deckID){
	
	var divBody=document.getElementsByTagName("body")[0];
	var iDocHeight=document.documentElement.scrollHeight;
	ZA.createWindowPopup(0,"NewDeck",300,420,1,0);
	var divData=document.getElementById("window_0");
	var iTop=15;
	var iLeft=10;
	
	ZA.callAjax("_app/deck/?category=1",function(xml)
	{
		//By default show dragonball
		ZD.deckLo = 11;
		ZD.deckHi = 16;
		
		//Deck name textbox
		var divInput=ZE.createInput(divData,iLeft,iTop,100,40,"Deck Name","deckname");
		//$(divInput).focus();
		iTop+=40;
		
		//Deck category dropdown
		var divInput=ZA.createDiv(divData,"","","div");
		$(divInput).css({
			top:iTop,
			left:iLeft+4
		});
		$(divInput).html('Deck Category');
		iTop+=15;
		var divInput=ZA.createDiv(divData,"","","div");
		$(divInput).css({
			top:iTop,
			left:iLeft
		});
		$(divInput).html(xml);
		$(divInput).change(function(){
			var catId = $(this).find("option:selected").val();
			if(catId == '2'){
				ZD.deckLo = 11;
				ZD.deckHi = 16;
			}/*
			else if(catId == '2'){
				ZD.deckLo = 1;
				ZD.deckHi = 10;
			}*/
			else{
				ZD.deckLo = 1;
				ZD.deckHi = 16;
			}
			$("#deckimage").val(ZD.deckLo);
			var newTop = (ZD.deckLo-1) * -200;
			$(divImages).animate({'top':newTop},150);
		});
		iTop+=40+5;
		
		var divInput=ZA.createDiv(divData,"","","div");
		$(divInput).css({
			top:iTop-15,
			width:"100%",
			"text-align":"center"
		});
		$(divInput).html('Deck Image');
		//iTop+=15;
		var divImagesHolder=ZA.createDiv(divData,"","deckimagesholder","div");
		$(divImagesHolder).css({
			top:iTop,
			left:iLeft,
			width:260,
			height:200,
			overflow:"hidden"
		});
		var divImages=ZA.createDiv(divImagesHolder,"","","div");
		$(divImages).css({
			top:(ZD.deckLo-1) * -200
		});
		for(i=1; i<=16; i++)
		{
			var img = ZA.createDiv(divImages,"","","img");
			img.src = "img/decks/"+i+".jpg";
			$(img).css({"-moz-user-select":"none"});
		}
		
		var arrowLeft = ZA.createDiv(divImagesHolder,"","imageLeft","div");
		$(arrowLeft).css({
			"background":"url(_site/all.png) -180px -60px",
			width:20,
			height:20,
			top:90,
			left:0,
			cursor:"pointer"
		});
		$(arrowLeft).click(function(){
			var index = parseInt($("#deckimage").val());
			if(index == ZD.deckLo)
			{
				var newTop = (ZD.deckLo-1) * -200;
				$(divImages).animate({'top':newTop},150,function(){
					$("#deckimage").val(ZD.deckLo);
				});
			}
			else if(index > ZD.deckLo)
			{
				index--;
				var newTop = (index-1) * -200;
				$(divImages).animate({'top':newTop},150,function(){
					$("#deckimage").val(index);
				});
			}
		});
		
		var arrowRight = ZA.createDiv(divImagesHolder,"","imageRight","div");
		$(arrowRight).css({
			"background":"url(_site/all.png) -260px -60px",
			width:20,
			height:20,
			top:90,
			right:0,
			cursor:"pointer"
		});
		$(arrowRight).click(function(){
			var index = parseInt($("#deckimage").val());
			if((index+1) <= ZD.deckHi)
			{
				var newTop = index * -200;
				index++;
				$(divImages).animate({'top':newTop},150,function(){
					$("#deckimage").val(index);
				});
			}
		});
		
		var divInput=ZA.createDiv(divData,"","","div");
		$(divInput).html('<input type="hidden" id="deckimage" value="'+ZD.deckLo+'" />');
		
		
		//Check if this deck is new or to be edited
		if (typeof deckID == "undefined"){
				
			//Command buttons for creating new deck
			var divCreate = ZA.createDiv(divData,"cmdButton","","div");
      $(divCreate).html('Create Deck');
      $(divCreate).css({right:10,top:345,zIndex:1000});
      $(divCreate).click(function(){
        ZD.aWindowNewDeck.clickSave()
      });
			
			var divCancel = ZA.createDiv(divData,"cmdButton","","div");
      $(divCancel).html('Cancel');
      $(divCancel).css({right:118,top:345,zIndex:1000});
      $(divCancel).click(function(){
        ZD.aWindowNewDeck.clickCloseA()
      });
		}
		else {
			var description = ZA.getXML(ZD.sXML,"decks/deck_"+deckID+"/description");
			var category = ZA.getXML(ZD.sXML,"decks/deck_"+deckID+"/categoryid");
			var image = ZA.getXML(ZD.sXML,"decks/deck_"+deckID+"/imageid");
			$("#deckname").val(description);
			$("#deckcategory").find("option[value='"+category+"']").attr('selected',true);
			$("#deckcategory").attr('disabled',true);
			$("#deckimage").val(image);
			var imageTop = (parseInt($("#deckimage").val())-1) * -200;
			$(divImages).css('top', imageTop);
			
			if(category == '2'){
				ZD.deckLo = 11;
				ZD.deckHi = 16;
			}/*
			else if(category == '2'){
				ZD.deckLo = 1;
				ZD.deckHi = 10;
			}*/
			else{
				ZD.deckLo = 1;
				ZD.deckHi = 16;
			}
			
			//Command buttons for updating existing deck
			var divCreate = ZA.createDiv(divData,"cmdButton","","div");
      $(divCreate).html('Save Deck');
      $(divCreate).css({left:200,top:345,zIndex:1000});
      $(divCreate).click(function(){
        ZD.aWindowNewDeck.clickSave(deckID)
      });
      
      var divCancel = ZA.createDiv(divData,"cmdButton","","div");
      $(divCancel).html('Cancel');
      $(divCancel).css({left:130,top:345,zIndex:1000});
      $(divCancel).click(function(){
        ZD.aWindowNewDeck.clickCloseA()
      });
		}
	});

};

/*********** click save button */
WORK_Newdeck.prototype.clickSave=function(update){
		var validated = true;
		var sDeckName = jQuery.trim($("#deckname").val());
		if(sDeckName.length < 1){
			var icon = "-697px -63px";
			ZS.showWindow(icon,'Please enter a name for the new deck',5000);
			$("#deckname").val('');
			$("#deckname").focus();
			validated = false;
		}
		var sDeckCategory = $("#deckcategory").val();
		var sDeckImage = $("#deckimage").val();
		
		if(validated)
		{
			var action;
			var success;
			var sDeckId = "";
			if(typeof update == "undefined"){
				action = 'save';
				success = 'New deck created succesfully';
			}
			else{
				action = 'update';
				success = 'Deck changes saved successfully';
				sDeckId = "&deckid="+ZA.getXML(ZD.sXML,"decks/deck_"+update+"/deckid");
			}
			ZA.callAjax("_app/deck/?"+action+"=1&deckname="+sDeckName+"&deckcategory="+sDeckCategory+"&deckimage="+sDeckImage+sDeckId,
			function(response){
				if(response == '1')
				{
					//Save successful
					var icon = "-667px -63px";
					ZS.showWindow(icon,success,5000);
					
					//Close the popup window
					ZD.aWindowNewDeck.clickCloseA();
					
		        	//Reload my album
					$(ZL.divData).html('<div class="loader"></div>');
					ZA.callAjax(ZL.sURL+"?init=1",function(xml){ZL.init(xml);});
	        		
					//Reload the deck window
					$(ZD.divData).html('');
		    		ZD = new WORK_Deck();
					ZA.callAjax("_app/deck/?init=1",function(xml){
						ZD.init(xml);
						var iSpeed = 300;
				        $(ZD.divList).hide();
				        $(ZD.divScroll).hide();
				        if (!ZD.divListLarge){
				          ZD.buildListLarge();
				        }
				        $(ZD.divListLarge).animate({opacity:1},iSpeed,function(){ });
					});
				}
				else
				{
					//Save failed
					var icon = "-697px -63px";
					ZS.showWindow(icon,response,5000);
				}
			});
		}
};

WORK_Newdeck.prototype.closeError=function(){
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

WORK_Newdeck.prototype.closeFirstVisit=function(){
	ZA.refreshBrowser();
};

/** 
=============================================================================
	finish NEWDECK CLASS */	
	WORK_Newdeck._iInited=1;
	}
};//END function WORK_Newdeck()




var ZD = new WORK_Deck();
ZA.aComponents[ZD.iComponentNo].fMaximizeFunction=ZD.toggleMax;
ZA.callAjax("_app/deck/?init=1",function(xml){ ZD.init(xml); });
