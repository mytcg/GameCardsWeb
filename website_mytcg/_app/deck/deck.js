function WORK_Deck()
{
	this.iComponentNo=3;
	this.divData=0;
	this.sURL="_app/deck/";
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
	this.changed = false;
	this.aWindowDeckViewer = null;
	this.cXML = "";
	this.deckSize = 10;
	this.deckLo = 1;
	this.deckHi = 16;
	this.cardsArray=null;
	this.updateDeck='0';
	this.updateDeckCards='0';
	this.deckIndex = null;


	if (typeof WORK_Deck._iInited=="undefined"){


	WORK_Deck.prototype.init=function(sXML)
	{
		ZD.divData=document.getElementById("window_"+ZD.iComponentNo);
		ZD.sXML = sXML;
      	$(ZD.divData).empty();
      
		//draw divs for list and scroller
		ZD.productCount = ZA.getXML(sXML, "deckcount");
		var iHeightOfList = ZD.productCount*204;
		
		ZD.divHolder = ZA.createDiv(ZD.divData,"","divDeckList","div");
		$(ZD.divHolder).css({ position:"absolute",top:0,left:0,width:294,height:204,overflow:"hidden" });
		
		ZD.divList = ZA.createDiv(ZD.divHolder,"","divDeckList","div");
		$(ZD.divList).css({ position:"absolute",top:0,left:0,width:294,height:iHeightOfList });
      
		ZD.divScroll = ZA.createDiv(ZD.divData,"","divDeckScroll","div");
		$(ZD.divScroll).css({ top:209,left:0,position:"absolute",width:294,height:20 });
      
		ZD.buildList();
    };


    WORK_Deck.prototype.buildList=function()
    {
		var itemCount = 0;
		if(ZD.productCount > 0)
		{
			for(a=0; a<ZD.productCount; a++)
			{
		        var pageBlock = ZA.createDiv(ZD.divList,"","","div");
		        $(pageBlock).css({ width:294,height:204,position:"relative" });
		        var itemBlock = ZA.createDiv(pageBlock,"deckImage","","img");
		        $(itemBlock).css({ cursor:"pointer",width:135,height:185,top:2,left:73,position:"absolute" });
		        itemBlock.src = ZA.getXML(ZD.sXML,"decks/deck_"+a+"/image");
		        itemBlock.alt = a;
		        itemBlock.id =  ZA.getXML(ZD.sXML,"decks/deck_"+a+"/type");
		        var itemTitle = ZA.createDiv(pageBlock,"","","div");
		        $(itemTitle).css({
		        	"background":"transparent",
		        	width:"100%",
		        	top:190
		        });
		        $(itemTitle).html(ZA.getXML(ZD.sXML,"decks/deck_"+a+"/description")+' ('+ZA.getXML(ZD.sXML,"decks/deck_"+a+"/ranking")+')');
			}
			//click event handler for deck image
			$(".deckImage").click(function(){
		      	var index = $(this).attr('alt');
		      	var deckType = $(this).attr('id');
				ZD.showDeckBuilder(index,deckType);
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
			$(divNone).html('No decks<br /><div class="cmdButton" id="createDeck" style="position:relative;width:105px;margin-left:auto;margin-right:auto;margin-top:5px;">Create a deck now</div>');
			$("#createDeck").click(function(){
				ZD.showDeckBuilder();
			});
		}
    };
    
    WORK_Deck.prototype.activateAddzone=function(){
    	
    	// Remove all previous droppable implementations
    	$(".thumbholder").droppable("destroy");
    	
    	// Droppable area for available cards
    	// to add card to deck
		$(".thumbholder").droppable(
		{
		    accept: ".cardImage",
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
						ZD.location = parseInt($(this).attr('id').split("_")[1]);
						ZD.addDropped = true;
					
				}
		    }
		});
		
    };
    
    WORK_Deck.prototype.activateRemzone=function()
    {
    	// Remove all previous droppable implementations
    	$("#availableCardsHolder").droppable("destroy");
    	// Droppable area for cards in deck
    	// to remove cards from deck
    	$("#availableCardsHolder").droppable({
    		accept: ".cardImage",
    		activeClass: "highlight",
    		hoverClass: "lightup",
    		drop: function(event, ui){
    			ZD.remDropped = true;
    		}
    	});	
    };
    
    WORK_Deck.prototype.activateAddables=function()
    {
    	// Add card viewer to click event
    	$(".cardImage").unbind();
        $(".cardImage").click(function(){
        	if(!ZD.dragging)
        	{
	        	var id = "availablecardholder_"+$(this).attr('alt');
	        	var image = $(this).attr('src').split('_')[0];
	        	ZD.clickShowFullImage(id, image);
        	}
        });
        $(".cardImage").css('cursor','pointer');
	        
    	// Remove previous draggable implementations
    	$(".cardImage").each(function(){
    		$(this).draggable("destroy");
    	});
    	
    	// Card dragging to deck
    	$(".cardImage").draggable({
    		containment: "#window_"+ZD.iComponentNo,
    		cursor: "move",
    		helper: "clone",
    		revert: "invalid",
    		stack: ".cardImage",
    		start: function(event, ui){
    			$("#availableCardsHolder").css('overflow','visible');
    			ZD.addDropped = false;
    			var a = "#availablecardholder_"+$(this).attr('alt');
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
					    			'<input type="hidden" class="usercard_id" value="'+usercardid+'" />'+
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
    					ZD.activateAddables();
    					ZD.activateAddzone();
    				}
    			}
    		},
    		zIndex: 2700
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
    			$("#availableCardsHolder").css('overflow','hidden');
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
    				
					ZA.callAjax("_app/deck/?remove=1&cardid="+card_id+"&deckid="+ZD.iDeckID+"&id="+card_id,function(reply){
	    				if(reply == '1')
	    				{
							//Adding removed card back to available cards
							var availHolder = $("#availablecardholder_"+i);
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
    
    
    
    // new deck view
    WORK_Deck.prototype.newDeckBuilder=function(deckIndex)
    {
		var divData = document.getElementById("window_222");
		//$(ZD.divData).find(".mainContainer").remove();
    	
    	var deckCardLimit = 6;
    	var deckCardCounts = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cardcountowned");
    	var deckCardCount = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cardcount");
    	var userCardCount = ZA.getXML(aXML, "cardcount");
    	
    	ZD.addDropzone = deckCardCounts;
    	ZD.iDeckID = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/deckid");
    	
    	var mainContainer = ZA.createDiv(ZD.divData,"mainContainer","","div");
    	$(mainContainer).css({
    		top:0,
    		height:"100%",
    		width:"100%",
    		"overflow":"hidden"
    	});
    	var heading = ZA.createDiv(mainContainer,"heading");
    	$(heading).html('<h2>Cards in Deck: '+ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/description")+'</h2>');
    	
    	var heading = ZA.createDiv(mainContainer,"heading");
    	$(heading).html('<h2>Available Cards: Your Collection</h2>');
    	
    	var overflowCon = ZA.createDiv(mainContainer,"overflowCon");
    	$(overflowCon).css({
    		width:785,
    		height:593,
    		"overflow":"hidden",
    		"position":"relative",
    	})
    	// Cards in deck
    	var deckCardsHolder = ZA.createDiv(overflowCon,"","deckcontainer","div");
    	$(deckCardsHolder).css({
    		top:0,
    		height:610,
    		width:385,
    	});
    	
    	var deckCards = ZA.createDiv(deckCardsHolder,"deckcardholders","","div");
    	$(deckCards).css({
    		left:5
    	});
    	var iLeft = 0;
    	var iTop = 0;
    	for(var i=0; i<deckCardLimit; i++)
        {
        	var id = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cards/card_"+i+"/usercardid");
        	var cardDescription = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cards/card_"+i+"/description");
        	var cardLocation = ZA.getXML(ZD.sXML, "decks/deck_"+deckIndex+"/cards/card_"+i+"/position");
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
        	
    		var cardThumb = ZA.createDiv(cardHolder,"thumbholder","deckcard_"+cardLocation,"div");
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
	        	}
	        	else
	        	{
	    			$(cardThumb).html(cardDescription);
	    		};
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
    	
    	var userCardsHolder = ZA.createDiv(overflowCon,"","cardscontainer","div");
    	$(userCardsHolder).css({
    		"border":"1px solid transparent",
    		paddingLeft:15,
    		top:0,
    		left:385,
    		width:382,
    		height:590,
    	});
    	
    	var userCards = ZA.createDiv(userCardsHolder,"","availablecardholders","div");
    	$(userCards).css({
    		top:0,
    		// width:547
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
    
    
    
    
    WORK_Deck.prototype.initDeckBuilder=function(deckIndex)
    {
		var divData = document.getElementById("window_222");
		$(divData).css({"-moz-user-select":"-moz-none"});
		//settings
		var iTop = 15;
		var iLeft = 10;
		var scrollAmount = 220;
		//show topcar deck images by default
		ZD.deckLo = 11;
		ZD.deckHi = 16;
		
    	//deck name textbox
		var divInput = ZE.createInput(divData,iLeft-1,iTop,100,40,"Deck Name","deckname");
		$("#deckname").css({
			"-moz-user-select":"text"
		});
		iTop+=40;
		
			//change event handler
			$("#deckname").bind("keypress keyup keydown",function(){
				var title = $(this).val().trim();
				if(title.length == 0){
					title = '(Untitled Deck)';
				}
				$("#windowtitledesc_222").html(title);
				ZD.deckChanged();
			});
		
    	//deck category dropdown
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
		var categoryCount = parseInt(ZA.getXML(ZA.sXML,"categories/iCount"));
		var categories = '';
		for(var l=0; l<categoryCount; l++){
			if(ZA.getXML(ZA.sXML,"categories/category_"+l+"/parent_id") == 'main'){
				categories += '<optgroup label="'+ZA.getXML(ZA.sXML,"categories/category_"+l+"/description")+'">';
				var category = ZA.getXML(ZA.sXML,"categories/category_"+l+"/category_id");
				for(var i=0; i<categoryCount; i++){
					if(ZA.getXML(ZA.sXML,"categories/category_"+i+"/parent_id") == category){
						categories += '<option value="'+ZA.getXML(ZA.sXML,"categories/category_"+i+"/category_id")+'">'+ZA.getXML(ZA.sXML,"categories/category_"+i+"/description")+'</option>';
					}
				}
				categories += '</optgroup>';
			}
		}
		$(divInput).html('<select id="deckcategory" style="width:263px">'+categories+'</select>');
		iTop+=30;
		$("#deckcategory").attr('disabled',true);
			//click event handler for category dropdown
			$(divInput).change(function(){
				var catId = $(this).find("option:selected").val();
				if(catId == '2'){
					ZD.deckLo = 11;
					ZD.deckHi = 16;
				}else{
					ZD.deckLo = 1;
					ZD.deckHi = 16;
				}
				$("#deckimage").val(ZD.deckLo);
				var newTop = (ZD.deckLo-1) * -(scrollAmount);
				$(divImages).animate({'top':newTop},150);
				ZD.deckChanged();
			});
		
    	//deck image selector
		var label = ZA.createDiv(divData);
		$(label).css({
			top:iTop,
			left:iLeft+4,
			"text-align":"center"
		});
		$(label).html('Deck Image');
		iTop+=15;
		var divImagesHolder = ZA.createDiv(divData,"","deckimagesholder","div");
		$(divImagesHolder).css({
			top:iTop,
			left:iLeft,
			width:262,
			height:scrollAmount,
			background:"#FFF",
			border:"1px solid #999",
			overflow:"hidden"
		});
		var divImages=ZA.createDiv(divImagesHolder,"","","div");
		$(divImages).css({
			top:(ZD.deckLo-1) * -(scrollAmount)
		});
		for(i=1; i<=16; i++)
		{
			var img = ZA.createDiv(divImages,"","","img");
			img.src = "img/decks/"+i+".png";
			$(img).css({"-moz-user-select":"none",margin:10});
		}
		//arrow left
		var arrowLeft = ZA.createDiv(divImagesHolder,"","imageLeft","div");
		$(arrowLeft).css({
			"background":"url(_site/all.png) -221px -41px",
			width:20,
			height:20,
			top:100,
			left:20,
			cursor:"pointer"
		});
		$(arrowLeft).click(function(){
			var index = parseInt($("#deckimage").val());
			if(index == ZD.deckLo)
			{
				var newTop = (ZD.deckLo-1) * -(scrollAmount);
				$(divImages).animate({'top':newTop},150,function(){
					$("#deckimage").val(ZD.deckLo);
				});
				ZD.deckChanged();
			}
			else if(index > ZD.deckLo)
			{
				index--;
				var newTop = (index-1) * -(scrollAmount);
				$(divImages).animate({'top':newTop},150,function(){
					$("#deckimage").val(index);
				});
				ZD.deckChanged();
			}
		});
		//arrow right
		var arrowRight = ZA.createDiv(divImagesHolder,"","imageRight","div");
		$(arrowRight).css({
			"background":"url(_site/all.png) -251px -41px",
			width:20,
			height:20,
			top:100,
			right:20,
			cursor:"pointer"
		});
		$(arrowRight).click(function(){
			var index = parseInt($("#deckimage").val());
			if((index+1) <= ZD.deckHi)
			{
				var newTop = index * -(scrollAmount);
				index++;
				$(divImages).animate({'top':newTop},150,function(){
					$("#deckimage").val(index);
				});
				ZD.deckChanged();
			}
		});
		var divInput=ZA.createDiv(divData,"","","div");
		$(divInput).html('<input type="hidden" id="deckimage" value="'+ZD.deckLo+'" />');
		iTop+=240;
		
    	//deck ranking
    	var holder = ZA.createDiv(divData);
    	$(holder).css({
    		width:262,
    		height:82,
    		top:iTop,
    		left:10,
    		border:"1px solid #999",
    		background:"#FFF"
    	});
		var div = ZA.createDiv(holder);
		$(div).css({
			position:"relative",
			marginTop:18
		})
		.html('Deck Ranking');
		var div = ZA.createDiv(holder);
		$(div).css({
			position:"relative",
			marginTop:15,
			fontSize:16,
			fontWeight:"bold"
		})
		.html('<span id="deckRanking">0</span>');
		iTop+=102;
		
    	//deck value
    	var holder = ZA.createDiv(divData);
    	$(holder).css({
    		width:262,
    		height:82,
    		top:iTop,
    		left:10,
    		border:"1px solid #999",
    		background:"#FFF"
    	});
		var div = ZA.createDiv(holder);
		$(div).css({
			position:"relative",
			marginTop:18
		})
		.html('Deck Value');
		var div = ZA.createDiv(holder,"txtBlue");
		$(div).css({
			position:"relative",
			marginTop:15,
			fontSize:16,
			fontWeight:"bold"
		})
		.html('<span id="deckValue">0</span> TCG');
		
		//all cards
		var label = ZA.createDiv(divData);
		$(label).css({
			top:12,
			left:287
		});
		$(label).html('Deck Cards');
		var label = ZA.createDiv(divData);
		$(label).css({
			top:12,
			left:446
		});
		$(label).html('Available Cards');
		var cardsContainer = ZA.createDiv(divData,"","cardsContainer");
		$(cardsContainer).css({
			top:27,
			left:282,
			width:548,
			height:510,
			overflow:"hidden",
        	background:"url(_site/line.gif) repeat",
        	border:"1px solid #999"
		});
			
	        //deck cards holder
	        var deckCardsHolder = ZA.createDiv(cardsContainer,"","deckCardsHolder");
	        $(deckCardsHolder).css({
	        	width:158,
	        	height:510,
	        	top:0,
	        	left:0,
	        	background:"#FFF",
	        	borderRight:"1px solid #999"
	        });
	        var deckCards = ZA.createDiv(deckCardsHolder,"","deckCards");
	        ZD.deckSize = 10
	        for(var i=0; i<ZD.deckSize; i++){
	        	var card = ZA.createDiv(deckCards,"deckCard",i.toString());
	        	$(card).css({
	        		background:"url(_site/all.png) -900px -125px no-repeat",
	        		position:"relative",
	        		"float":"left",
	        		width:64,
	        		height:90,
	        		marginTop:10,
	        		marginLeft:10
	        	});
	        }
	        //add droppable for available cards
	        $(deckCardsHolder).droppable({
	        	accept: ".cardImage",
	        	activeClass: "highlight",
	        	hoverClass: "lightup",
	        	drop: function(event, ui){
	        		ui.draggable.draggable('option','revert',true);
	        		//add card to first open deck card slot
					var cardid = ui.draggable.attr('id');
					var index = ZD.cardsArray[cardid];
					ZD.createDeckCard(index);
					ZD.resetDeckCards();
					ZD.deckCardsChanged();
	        	}
	        });
	        
	        //available cards holder
	        var divCardsHolder = ZA.createDiv(cardsContainer,"","availableCardsHolder");
	        $(divCardsHolder).css({
	        	top:0,
	        	left:158,
	        	width:390,
	        	height:510,
	        	paddingBottom:10
	        });
	    	var divCards = ZA.createDiv(divCardsHolder,"","availableCards");
	    	$(divCards).css({
	    		position:"relative",
	    		width:"100%",
	    		overflow:"visible"
	    	});
	    	var cardsCount = parseInt(ZA.getXML(ZL.sXML,"album_all/totalcards"));
	    	ZD.cardsArray = [];
	    	//all available cards
	    	for(var i=0; i<cardsCount; i++){
	    		var possess = parseInt(ZA.getXML(ZL.sXML,"album_all/cards/card_"+i+"/qty"));
	    		if(possess > 0){
					var description = ZA.getXML(ZL.sXML,"album_all/cards/card_"+i+"/description");
					var image = ZA.getXML(ZL.sXML,"album_all/cards/card_"+i+"/path")+'cards/'+ZA.getXML(ZL.sXML,"album_all/cards/card_"+i+"/img");
					var cardid = ZA.getXML(ZL.sXML,"album_all/cards/card_"+i+"/cardid");
					ZD.cardsArray[cardid] = i;
	    			var card = ZA.createDiv(divCards,"cardBlock",i.toString());
	    			$(card).html(
						'<img src="'+image+'_web.jpg" class="cardImage" id="'+cardid+'" alt="'+description+'" title="'+description+'" />'+
						'<div class="cardName" style="cursor:default;">'+description+'</div>'
	    			);
	    		}
	    	}
	    	//clear floating cards
			var div = ZA.createDiv(divCards);
			$(div).css({
				position:"relative",
				clear:"left"
			});
			//available cards click handler
			$("#availableCards").find(".cardImage").click(function(){
				if(!ZD.dragging){
					ZD.clickShowFullImage($(this));
				}
				else{
					ZD.dragging = false;
				}
			});
			//add draggable for available cards
			$("#availableCards").find(".cardImage").draggable({
	    		containment: "#cardsContainer",
	    		helper: "original",
				revert: "invalid",
				stack: ".cardImage",
				start: function(event, ui){
					//allow overflow
					$("#availableCardsHolder").css({overflow:'visible'})
					.find(".jspContainer").css({overflow:'visible'});
				},
				drag: function(event, ui){
					ZD.dragging = true;
				},
				stop: function(event, ui){
					//remove overflow
					$("#availableCardsHolder").css({overflow:'hidden'})
					.find(".jspContainer").css({overflow:'hidden'});
				},
				zIndex:901
			});
	        //add droppable for deck cards
	        $(divCardsHolder).droppable({
	        	accept: ".deckCardImage",
	        	activeClass: "highlight",
	        	hoverClass: "lightup",
	        	drop: function(event, ui){
		    		var id = ui.draggable.attr('id');
		    		var index = ZD.cardsArray[id];
		    		//remove card from deck and add to available cards
		    		ui.draggable.parent().empty();
		    		$("#availableCards").find(".cardBlock[id='"+index+"']").show();
		    		ZD.resetDeckCards();
		    		ZD.deckCardsChanged();
	        	}
	        });
		
        //cards total
        var cardsTotal = ZA.createDiv(divData);
        $(cardsTotal).css({
        	top:554,
        	left:282,
        	width:160,
        	fontSize:16,
        	fontWeight:"bold",
        	color:"#666"
        })
        .html('<span id="cardsTotal">0</span> / 10');
        
    	//save button
        var command = ZA.createDiv(divData,"cmdButton","saveDeck");
        $(command).css({
        	bottom:10,
        	right:80
        })
        .html('Save Changes')
        .addClass('cmdButtonDisabled')
        .click(function(){
        	if(!$(this).hasClass('cmdButtonDisabled')){
        		if(true){
        			//display loader
        			ZA.addLoader($("#window_222"),222);
        			//update existing deck
					var deckId = ZA.getXML(ZD.sXML,"decks/deck_"+ZD.deckIndex+"/deckid");
					var deckDescription = $("#deckname").val();
					var deckCategory = $("#deckcategory").val();
					var deckImage = $("#deckimage").val();
					var deckCards = [];
					$("#deckCards").find(".deckCardImage").each(function(){
						var cardId = $(this).attr('id');
						deckCards.push(cardId);
					});
					deckCards = deckCards.join(',');
	        		ZA.callAjax(ZD.sURL+"?update=1&deck="+deckId+"&updatedeck="+ZD.updateDeck+"&updatedeckcards="+ZD.updateDeckCards+"&description="+deckDescription+"&category="+deckCategory+"&image="+deckImage+"&cards="+deckCards,function(xml){
	        			ZD.resetDeck();
	        			//reset deck and album
	        			ZA.callAjax(ZD.sURL+"?init=1",function(xml){
	        				ZD.init(xml);
	        				ZA.removeLoader(222);
	        			});
	        		});
	        	}
	        	else{
	        		//create new deck
	        	}
        		//$("#cancelDeck").click();
			}
        });
        //close button
        var command = ZA.createDiv(divData,"cmdButton","closeDeck");
        $(command).css({
        	bottom:10,
        	right:10,
        	width:40
        })
        .html('Close')
        .click(function(){
			$("#bodycloak_222").remove();
			$("#windowcontainer_222").remove();
			$("#window_222").remove();
        });
        
        //check if this is an existing or new deck
    	if(typeof(deckIndex) == "undefined"){
    		//create new deck
    		deckIndex = -1;
	    	$("#saveDeck").html('Create Deck');
	    	$("#closeDeck").html('Cancel');
    	}
    	else{
    		//load deck info
			var description = ZA.getXML(ZD.sXML,"decks/deck_"+ZD.deckIndex+"/description");
			var category = ZA.getXML(ZD.sXML,"decks/deck_"+ZD.deckIndex+"/categoryid");
			var image = ZA.getXML(ZD.sXML,"decks/deck_"+ZD.deckIndex+"/imageid");
			var ranking = ZA.getXML(ZD.sXML,"decks/deck_"+ZD.deckIndex+"/ranking");
			var value = ZA.getXML(ZD.sXML,"decks/deck_"+ZD.deckIndex+"/value");
			$("#deckname").val(description);
			$("#deckcategory").find("option[value='"+category+"']").attr('selected',true);
			$("#deckcategory").attr('disabled',true);
			$("#deckimage").val(image);
			var imageTop = (parseInt($("#deckimage").val())-1) * -(scrollAmount);
			$(divImages).css('top', imageTop);
			if(category == '2'){
				ZD.deckLo = 11;
				ZD.deckHi = 16;
			}
			else{
				ZD.deckLo = 1;
				ZD.deckHi = 16;
			}
			$("#deckRanking").html(ranking);
			$("#deckValue").html(value);
			//load deck cards
			var cardcount = parseInt(ZA.getXML(ZD.sXML,"decks/deck_"+ZD.deckIndex+"/cardcount"));
			for(var i=0; i<cardcount; i++){
				var cardid = ZA.getXML(ZD.sXML,"decks/deck_"+ZD.deckIndex+"/cards/card_"+i+"/cardid");
				var index = ZD.cardsArray[cardid];
				ZD.createDeckCard(index);
			}
			ZD.resetDeckCards();
    	}
    	ZA.removeLoader();
   };
    
    WORK_Deck.prototype.showDeckBuilder=function(deckIndex,type)
    {
    	//create popup window
    	var windowTitle = (typeof(deckIndex)=="undefined") ? ' (Untitled Deck)' : ' '+ZA.getXML(ZD.sXML,"decks/deck_"+deckIndex+"/description");
		ZA.createWindowPopup(222,windowTitle,858,624,1,0);
		ZA.addLoader($("#window_222"));
		//delay initialisation of window contents
		ZD.deckIndex = deckIndex;
		ZD.deckType = type;
		if(ZD.deckType == 4){
			setTimeout("ZD.newDeckBuilder("+ZD.deckIndex+")",750);
		}else{
			setTimeout("ZD.initDeckBuilder("+ZD.deckIndex+")",750);
		};
    };
    
    
    WORK_Deck.prototype.resetDeck=function()
    {
    	ZD.updateDeck = '0';
    	ZD.updateDeckCards = '0';
    	$("#saveDeck").html('Save Changes').addClass('cmdButtonDisabled');
    	$("#closeDeck").html('Close');
    };
    
    
    WORK_Deck.prototype.deckChanged=function()
    {
    	ZD.updateDeck = '1';
    	$("#saveDeck").removeClass('cmdButtonDisabled');
    };
    
    
    WORK_Deck.prototype.deckCardsChanged=function()
    {
    	ZD.updateDeckCards = '1';
    	$("#saveDeck").removeClass('cmdButtonDisabled');
    };
    
    
    WORK_Deck.prototype.createDeckCard=function(index)
    {
		var cardid = ZA.getXML(ZL.sXML,"album_all/cards/card_"+index+"/cardid");
		var image = ZA.getXML(ZL.sXML,"album_all/cards/card_"+index+"/path")+'cards/'+ZA.getXML(ZL.sXML,"album_all/cards/card_"+index+"/img")+'_web.jpg';
		var description = ZA.getXML(ZL.sXML,"album_all/cards/card_"+index+"/description");
		var ranking = ZA.getXML(ZL.sXML,"album_all/cards/card_"+index+"/ranking");
		var value = ZA.getXML(ZL.sXML,"album_all/cards/card_"+index+"/value");
		$("#deckCards").find(".deckCard").each(function(){
			if(!$(this).find(".deckCardImage").size()){
				$(this).html(
					'<img src="'+image+'" class="deckCardImage" id="'+cardid+'" title="'+description+'" />'+
					'<input type="hidden" class="ranking" value="'+ranking+'" />'+
					'<input type="hidden" class="value" value="'+value+'" />'
				);
				return false;
			}
		});
    };
    
    WORK_Deck.prototype.addDeckCard=function(index,xml)
    {
		var cardid = ZA.getXML(xml,"cards/card_"+index+"/cardid");
		var image = ZA.getXML(xml,"cards/card_"+index+"/thumbnail");
		var description = ZA.getXML(xml,"cards/card_"+index+"/description");
		var ranking = ZA.getXML(xml,"cards/card_"+index+"/ranking");
		var value = ZA.getXML(xml,"cards/card_"+index+"/value");
		$("#deckCards").find(".deckCard").each(function(){
			if(!$(this).find(".deckCardImage").size()){
				$(this).html(
					'<img src="'+image+'" class="deckCardImage" id="'+cardid+'" title="'+description+'" />'+
					'<input type="hidden" class="ranking" value="'+ranking+'" />'+
					'<input type="hidden" class="value" value="'+value+'" />'
				);
				return false;
			}
		});
    };
    
    
    WORK_Deck.prototype.resetDeckCards=function()
    {
    	//deck cards click handler
    	$(".deckCardImage").unbind().click(function(){
    		if(!ZD.dragging){
	    		//show full card
				ZD.clickShowFullImage($(this));
	    	}
	    	else{
	    		ZD.dragging = false;
	    	}
    	});
		//add draggable for deck cards
		$(".deckCardImage").draggable("destroy");
		$(".deckCardImage").draggable({
    		containment: "#cardsContainer",
    		helper: "original",
			revert: "invalid",
			stack: ".deckCardImage",
			start: function(event, ui){
				//
			},
			drag: function(event, ui){
				ZD.dragging = true;
			},
			stop: function(event, ui){
				//
			},
    		zIndex:901
		});
    	//refresh the deck details
    	var total = $("#deckCards").find(".deckCardImage").size();
    	var ranking = 0;
    	var value = 0;
    	$("#deckCards").find(".deckCardImage").each(function(){
    		ranking+= parseInt($(this).parent().find(".ranking").val());
    		value+= parseInt($(this).parent().find(".value").val());
    	});
    	$("#deckRanking").html(ranking);
    	$("#deckValue").html(value);
    	$("#cardsTotal").html(total);
    	if(total == ZD.deckSize){
    		$("#cardsTotal").parent().removeClass('incomplete').addClass('txtGreen');
    		$("#availableCardsHolder").find(".cardImage").draggable("disable");
    	}
    	else{
    		$("#cardsTotal").parent().removeClass('txtGreen').addClass('incomplete');
    		$("#availableCardsHolder").find(".cardImage").draggable("enable");
    	}
    	//hide available cards which are in deck
    	$(".deckCardImage").each(function(){
    		var id = $(this).attr('id');
    		var index = ZD.cardsArray[id];
    		$("#availableCards").find(".cardBlock[id='"+index+"']").hide();
    	});
    	ZD.resetAvailableCardsScrollbar();
    };
    
    
    WORK_Deck.prototype.resetAvailableCardsScrollbar=function()
    {
		//reset available cards scrollbar
    	$("#availableCardsHolder").jScrollPane({
    		enableKeyboardNavigation:false,
			mouseWheelSpeed:125,
			trackClickSpeed:125,
			verticalGutter:0
    	});
    };
    
    
    WORK_Deck.prototype.refreshCardsTotal=function()
    {
    	var total = $("#deckCards").find(".deckCardImage").size();
    	$("#cardsTotal").html(total);
    };
    

	WORK_Deck.prototype.flipCard=function(iIsFront)
	{
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
	
	WORK_Deck.prototype.clickCloseFullImage=function()
	{
		return function(){
			var divBody=document.getElementsByTagName("body")[0];
			var divFull=document.getElementById("cardfull");
			if (divFull){
				divBody.removeChild(divFull);
			}
		};
	};

	WORK_Deck.prototype.clickShowFullImage=function(card)
	{
		var divThumbnail=card.get()[0];
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
		var sImg = card.attr('src');
		sImg = sImg.substring(0, sImg.length-8);
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
		
		var divMenu = ZA.createDiv(divFull,"cardMenu");
		
		//Compare tab
		var divTab = ZA.createDiv(divMenu,"menuTab","tabCompare");
		$(divTab).css({
			background:"url(_site/all.png) -735px -125px no-repeat",
			top:70
		});
		$(divTab).attr('title','Compare card');
		$(divTab).click(function(){
			var cardid = card.attr('id');
			ZA.showCompare(cardid);
		});
		
		//-----------------------------------------------------
		//END OF: Card menu tabs
		//-----------------------------------------------------
	};
    
	
    WORK_Deck.prototype.deckCardCount=function(card_id){
    	return parseInt($("#deckcontainer").find("input.card_id[value='"+card_id+"']").size(),10);
    };
    
    
    WORK_Deck.prototype.buildListLarge=function()
    {
		ZD.divListLarge = ZA.createDiv(ZD.divData,"","divDeckListLarge","div");
		$(ZD.divListLarge).css({ opacity:0,position:"relative",width:984,height:535,backgroundColor:"#D4D5D6" });
		
		//Top controls main page
		var topMenuMain = ZA.createDiv(ZD.divData,"","deckTopMenu","div");
		$(topMenuMain).css({ top:0,left:0,width:974,height:23,"z-index":"9999",padding:5 });
		//Button: New
		var buttonNew = ZA.createDiv(topMenuMain,"deckMenuItem","deckNew","div");
		$(buttonNew).addClass('cmdButton')
		.html("Create a New Deck")
		.click(function(){
      		ZD.showDeckBuilder();
      	});
      	
		//Deck list
		var deckList = ZA.createDiv(ZD.divListLarge,"","decksholder");
		$(deckList).css({ top:28,left:0,width:984,height:507 });
		ZD.divLargeDeckList = deckList;
		
		if(ZD.productCount > 0){
			for(a=0; a<ZD.productCount; a++){
				var deckBlock = ZA.createDiv(deckList,"deckBlock");
				$(deckBlock).css({
		        	position:"relative",
		        	"float":"left",
					border:"1px solid #CCC",
		        	backgroundColor:"#EFEFEF",
		        	width:150,
		        	height:232,
		        	marginTop:8,
		        	marginLeft:8
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
		        
		        //Delete deck icon
		        var iconDelete = ZA.createDiv(deckBlock,"deleteIcon","","div");
		        $(iconDelete).css({
					top:25,
					right:5,
		        });
		        $(iconDelete).attr('id',ZA.getXML(ZD.sXML, "decks/deck_"+a+"/deckid"));
		        $(iconDelete).attr('alt',description);
		        
		        var imgBlock = ZA.createDiv(deckBlock,"imageBlock","","img");
		        $(imgBlock).css({ cursor:"pointer",width:104,height:140,top:25,left:23,position:"absolute" });
		        $(imgBlock).attr('alt',a);
		        $(imgBlock).attr('id',ZA.getXML(ZD.sXML, "decks/deck_"+a+"/type"));
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
		        var cardCount = ZA.getXML(ZD.sXML, "decks/deck_"+a+"/cardcount");
		        $(blockInfo).html(cardCount+' / 10');
		        if(cardCount != 10){
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
		        $(blockInfo).html( ZA.getXML(ZD.sXML, "decks/deck_"+a+"/category") );
		        iTop+=15;
		        //Deck ranking
		        var lbl = ZA.createDiv(deckBlock);
		        $(lbl).css({
		        	top:iTop,
		        	left:5
		        });
		        $(lbl).html('Ranking:');
		        var blockInfo = ZA.createDiv(deckBlock,"","","div");
		        $(blockInfo).css({
		        	top:iTop,
		        	right:5
		        });
		        $(blockInfo).html( ZA.getXML(ZD.sXML, "decks/deck_"+a+"/ranking") );
		        iTop+=15;
		        //Deck value
		        var lbl = ZA.createDiv(deckBlock);
		        $(lbl).css({
		        	top:iTop,
		        	left:5
		        });
		        $(lbl).html('Value:');
		        var blockInfo = ZA.createDiv(deckBlock,"txtBlue");
		        $(blockInfo).css({
		        	top:iTop,
		        	right:5,
		        	fontWeight:"bold"
		        });
		        $(blockInfo).html(ZA.getXML(ZD.sXML,"decks/deck_"+a+"/value")+' TCG');
			}
			
			//Click event handler for viewing deck
			$(".imageBlock").unbind()
			.click(function(){
				var index = $(this).attr('alt');
				var deckType = $(this).attr('id');
				ZD.showDeckBuilder(index,deckType);
			});
			
			//Click event handler for deleting deck
			$(".deleteIcon").unbind()
			.click(function(){
				var index = $(this).attr('alt');
	      		if(confirm('Click OK to delete deck: '+index)){
	      			ZA.callAjax("_app/deck/?delete=1&deck_id="+$(this).attr('id'),function(response){
		      			if(response == '1'){
							//Delete successful
							var icon = "-667px -63px";
							ZS.showWindow(icon,"Deck successfully deleted",5000);
				        	//Reload my album
							$(ZL.divData).html('<div class="loader"></div>');
							ZA.callAjax(ZL.sURL+"?init=1",function(xml){ZL.init(xml);});
							//Reload the deck window
							$(ZD.divData).empty();
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
    
    
	WORK_Deck.prototype.buildScroller=function()
	{
		var divArrowLeft = ZA.createDiv(ZD.divScroll);
		$(divArrowLeft).css({ cursor:"pointer",position:"relative",cssFloat:"left",width:20,height:20,backgroundImage:ZD.imgAll,backgroundPosition:"-221px -41px" })
		.click(function(e){
			if(ZD.currentPage != 0){
				ZD.currentPage--;
				ZD.gotoPage(ZD.currentPage);
			}
		});
		
		var divPageCountList = ZA.createDiv(ZD.divScroll);
		var offsetCount = (254-(ZD.productCount*14))/2;
		$(divPageCountList).css({ marginLeft:offsetCount,marginTop:7,position:"relative",cssFloat:"left",width:(254-offsetCount),height:20});
		
		for(i=0;i<ZD.productCount;i++){
			var divPageIcon = ZA.createDiv(divPageCountList,"","","div");
			$(divPageIcon).css({ cursor:"pointer",marginRight:7,position:"relative",cssFloat:"left",width:7,height:7,backgroundImage:ZD.imgAll,backgroundPosition:"-281px -41px"});
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
		$(divArrowRight).css({ cursor:"pointer",position:"relative",cssFloat:"left",width:20,height:20,backgroundImage:ZD.imgAll,backgroundPosition:"-251px -41px" });
		$(divArrowRight).click(function(e){
			if(ZD.currentPage != ZD.productCount-1){
				ZD.currentPage++;
				ZD.gotoPage(ZD.currentPage);
			}
		});
	};
	
	
    WORK_Deck.prototype.gotoPage=function(page)
    {
		for (i=0;i<ZD.productCount;i++){
			$(ZD.divScroll.childNodes[1].childNodes[i]).css({ backgroundPosition:"-281px -41px" }); 
		}
		$(ZD.divScroll.childNodes[1].childNodes[page]).css({ backgroundPosition:"-291px -41px" });
		var newPos = page * -204;
		$(ZD.divList).animate({top:newPos},600);
	};
	
	
    WORK_Deck.prototype.toggleMax=function()
    {
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
			if(ZD.changed)
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
			} else {
				$(ZD.divList).animate({opacity:1},iSpeed);
				$(ZD.divScroll).animate({opacity:1},iSpeed);
				$(ZD.divListLarge).animate({opacity:0},iSpeed);
				$(ZD.divListLarge).hide();
				/*
				//reset the scroller
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


var ZD = new WORK_Deck();
ZA.aComponents[ZD.iComponentNo].fMaximizeFunction=ZD.toggleMax;
ZA.callAjax("_app/deck/?init=1",function(xml){ ZD.init(xml); });
