function WORK_Album(){
	this.iComponentNo=4;
	this.divData=0;
	this.sURL="_app/album/";
	this.imgAll = "url(_site/all.png)";
	this.sXML="";
  this.iWindowWidth = 784;  //Hardcoded value
  this.iWindowHeight = 160; //Hardcoded value
  this.iWidthOfList = 0;
	this.itemCount = 0;
	this.imagePath = 'img/card';
  this.pageCount = 0;
  this.perPage = 7;         //Hardcoded value
  this.perRow = 7;        //Hardcoded value
  this.currentPage = 0;
	this.divList = null;
	this.divListBlock = null;
	this.divMenu = null;
  this.divListLarge = null;
  this.divScroll = null;
  this.divScrollBar = null;
  this.currentAlbum = 0;
  this.iMenuBlockHeight = 28;
  this.menuMoves = 0;
  this.menuMovesMax = 0;
  this.dragged = false;
  //Create auction popup
  this.aWindowAuction = null;
	
	if (typeof WORK_Album._iInited=="undefined"){

    WORK_Album.prototype.init=function(sXML){
      //Get INIT values and properties
      ZL.divData=document.getElementById("window_"+ZL.iComponentNo);
      ZL.sXML=sXML;
      
	  var receivedCount = parseInt(ZA.getXML(sXML,"received/count"));
	  if(receivedCount > 0)
	  {
	  	//let the user accept/reject the cards
	  	//then load the album
	  	if(ZA.sUsername && false){
	  		ZL.showReceivedCards(sXML);
	  	}
	  }
	  else
	  {
	      $(ZL.divData).empty();
	      ZL.imagePath = ZA.getXML(sXML,"album_0/cards/card_0/path");
	      
	      //Calc pages
	      var albumCount = parseInt(ZA.getXML(sXML,"albumcount"));
	      if(albumCount > 0)
	      {
		      ZL.itemCount = ZA.getXML(sXML,"album_0/totalcards");
		      ZL.pageCount = Math.ceil(ZL.itemCount / ZL.perPage);
		      ZL.iWidthOfList = ZL.pageCount*(ZL.iWindowWidth-120);
		
		      //Draw divs for page
		      ZL.divList = ZA.createDiv(ZL.divData,"","","div");
		      $(ZL.divList).css({ top:0,left:0,width:ZL.iWidthOfList,height:ZL.iWindowHeight });      
		      
				
		      ZL.divMenu = ZA.createDiv(ZL.divData,"bgRightMenu","","div");
		      $(ZL.divMenu).css({ zIndex:5,overflow:"hidden",top:0,right:0});
				if (ZA.aComponents[ZL.iComponentNo].iIsMaximized) {
					$(ZL.divMenu).css({ height:ZL.iWindowHeight,width:138 });
				}
				else{
					$(ZL.divMenu).css({ height:ZL.iWindowHeight,width:138 });
				}
				
		      ZL.divListLarge = ZA.createDiv(ZL.divData,"","","div");
		      $(ZL.divListLarge).css({ display:"none",opacity:0,position:"relative",width:ZL.iMaxWidth,height:ZL.iMaxHeight,backgroundColor:"#EBEBEB" });
		      
		      ZL.divScroll = ZA.createDiv(ZL.divData,"","divScroll","div");
		      $(ZL.divScroll).css({ bottom:0,left:0,width:ZL.iWindowWidth,height:20 });
		      
		      ZL.buildMenu();
		      ZL.showAlbum(ZL.currentAlbum);
		  }
		  else
		  {
		  	var divNone = ZA.createDiv(ZL.divData);
		  	$(divNone).css({
		  		width:"100%",
		  		top:110
		  	});
		  	$(divNone).html('No cards');
		  }
	  }
    };
    
    
    WORK_Album.prototype.showReceivedCards=function(xml)
    {
    	var receivedCount = parseInt(ZA.getXML(xml,"received/count"));
        var cardtext = (receivedCount > 1) ? 'cards' : 'a card';
    	
    	ZA.createWindowPopup(789,"",410,480,1,0);
        var divWindow=document.getElementById("window_789");
        var divData=ZA.createDiv(divWindow);
        $(divData).css({
          width:"100%",
          height:"100%"
        });
        
        //pack title
        var title = ZA.createDiv(divData);
        $(title).css({
        	textAlign:"left",
        	left:15,
        	top:18,
        	fontSize:16,
        	fontWeight:"bold",
        	"text-shadow":"1px 1px 1px #fff"
        });
        $(title).html('You received '+cardtext);
        
        //memo
        var memo = ZA.createDiv(divData);
        $(memo).css({
        	left:15,
        	top:40,
        	textAlign:"left"
        });
        $(memo).html('You have received '+cardtext+' from a friend. You can accept or reject the card(s).');
        
        //number of cards
        var number = ZA.createDiv(divData);
        $(number).css({
        	bottom:17,
        	left:15,
        	color:"#666"
        });
        $(number).html('<span id="cardsTotal">'+receivedCount.toString()+'</span> card(s)');
        
        //cards
        var divCardsHolder = ZA.createDiv(divData,"","receivedCards");
        $(divCardsHolder).css({
        	left:10,
        	top:60,
        	width:370,
        	height:330,
        	border:"2px solid #999",
        	background:"url(_site/line.gif) repeat",
        	"-moz-border-radius":"5px"
        });
    	var divCards = ZA.createDiv(divCardsHolder);
    	$(divCards).css({
    		position:"relative",
    		width:"100%"
    	});
    	var divCard;
    	var background;
    	var color;
    	for(var i=0; i<receivedCount; i++){
    		background = (i % 2) ? 'transparent' : '#EFEFEF';
    		divCard = ZA.createDiv(divCards,"receivedCard",i.toString());
    		$(divCard).css({
    			position:"relative",
    			width:"100%",
    			height:109,
    			background:background,
    			borderBottom:"1px solid #999"
    		});
    		var card_id = ZA.getXML(xml,"received/cards/card_"+i+"/card_id");
    		var description = ZA.getXML(xml,"received/cards/card_"+i+"/description");
    		var thumb = ZA.getXML(xml,"received/cards/card_"+i+"/path")+'cards/'+ZA.getXML(xml,"received/cards/card_"+i+"/image")+'_web.jpg';
			var quality = ZA.getXML(xml,"received/cards/card_"+i+"/quality");
			var ranking = ZA.getXML(xml,"received/cards/card_"+i+"/ranking");
			var avgranking = ZA.getXML(xml,"received/cards/card_"+i+"/avgranking");
			var value = ZA.getXML(xml,"received/cards/card_"+i+"/value");
			var possess = ZA.getXML(xml,"received/cards/card_"+i+"/possess");
    		$(divCard).html(
    			'<img src="'+thumb+'" align="left" style="margin:9px auto auto 10px;border-right:1px solid #000;border-bottom:1px solid #000;" />'+
    			'<div style="left:84px;top:18px;text-align:left;">'+
			    	'<span style="font-weight:bold;font-size:16px;">'+description+'</span><br /><br />'+
			    	'Quality: <span style="font-weight:bold;">'+quality+'</span><br />'+
			    	'Ranking: <span style="font-weight:bold;">'+ranking+'</span><br />'+
			    	'Average Ranking: <span style="font-weight:bold;">'+avgranking+'</span><br />'+
			    	'Value: <span style="font-weight:bold;">'+value+' TCG</span>'+
    			'</div>'+
    			'<div class="cmdButton acceptCard" id="'+i.toString()+'" style="top:60px;right:15px;width:40px;">Accept</div>'+
    			'<div class="cmdButton rejectCard" id="'+i.toString()+'" style="top:60px;right:85px;width:40px;">Reject</div>'
    		);
    		if(possess > 0){
    			var own = ZA.createDiv(divCard);
    			$(own).attr('title','You already own this card').css({
    				background:"url(_site/all.png) -400px -5px no-repeat",
					color:"#956A0D",
    				width:21,
					height:17,
					paddingTop:9,
    				top:8,
    				left:51
    			});
    			if(possess > 1){
    				$(own).html(possess.toString());
    			}
    		}
    	}
    	//add scrollbar if required
        if(receivedCount > 3){
	    	$(divCardsHolder).jScrollPane({
	    		enableKeyboardNavigation:false,
				mouseWheelSpeed:110,
				trackClickSpeed:110,
				verticalGutter:0
	    	});
    	}
    	
    	//accept card
    	$(".acceptCard").click(function(){
    		var i = $(this).attr('id');
    		var usercard = ZA.getXML(xml,"received/cards/card_"+i+"/usercard_id");
    		ZA.addLoader($(this).parent(),i);
        	ZA.callAjax(ZL.sURL+"?accept="+usercard,function(xml){
        		ZA.removeLoader(i);
        		$(".receivedCard[id='"+i+"']").hide('blind',150,function(){
        			$(this).remove();
        			ZL.checkReceivedCards();
        		});
        	});
    	});
    	//reject card
    	$(".rejectCard").click(function(){
    		var i = $(this).attr('id');
    		var usercard = ZA.getXML(xml,"received/cards/card_"+i+"/usercard_id");
    		ZA.addLoader($(this).parent(),i);
        	ZA.callAjax(ZL.sURL+"?reject="+usercard,function(xml){
        		ZA.removeLoader(i);
        		$(".receivedCard[id='"+i+"']").hide('blind',150,function(){
        			$(this).remove();
        			ZL.checkReceivedCards();
        		});
        	});
    	});
        
        //accept all cards
        var btn = ZA.createDiv(divData,"cmdButton");
        $(btn).css({
        	width:67,
        	right:10,
        	bottom:10
        });
        $(btn).html('Accept All');
        $(btn).click(function(){
        	ZA.addLoader($(divData));
        	ZA.callAjax(ZL.sURL+"?accept=all",function(xml){
				ZA.removeLoader();
				$(".receivedCard").remove();
				ZL.checkReceivedCards();
        	});
        });
        
        //reject all cards
        var btn = ZA.createDiv(divData,"cmdButton");
        $(btn).css({
        	width:67,
        	right:107,
        	bottom:10
        });
        $(btn).html('Reject All');
        $(btn).click(function(){
        	if(confirm('Reject all cards?')){
        		ZA.addLoader($(divData));
	        	ZA.callAjax(ZL.sURL+"?reject=all",function(xml){
					ZA.removeLoader();
					$(".receivedCard").remove();
					ZL.checkReceivedCards();
	        	});
        	}
        });
    }
    
    
    WORK_Album.prototype.checkReceivedCards=function()
    {
    	var receivedCount = $("#receivedCards").find(".receivedCard").size();
    	$("#cardsTotal").html(receivedCount.toString());
        //reinitialize scrollbar
    	$("#receivedCards").jScrollPane({
    		enableKeyboardNavigation:false,
			mouseWheelSpeed:110,
			trackClickSpeed:110,
			verticalGutter:0
    	});
    	if(receivedCount > 0){
    		var i = 0;
	    	$("#receivedCards").find(".receivedCard").each(function(){
	    		var background = (i % 2) ? 'transparent' : '#EFEFEF';
	    		$(this).css({background:background});
	    		i++;
	    	});
	    }
	    else{
	    	//all cards accepted/rejected
	    	ZL = new WORK_Album();
	    	ZA.addLoader($("#window_789"));
			ZA.callAjax(ZL.sURL+"?init=1",function(xml){
				ZA.removeLoader();
				$("#bodycloak_789").remove();
				$("#windowcontainer_789").remove();
				$("#window_789").remove();
				ZL.init(xml);
			});
	    }
    }
    
    
    WORK_Album.prototype.moveMenu=function()
	 {
		var end = false;
		if(ZA.aComponents[ZL.iComponentNo].iIsMaximized){
			var cats = $("#menuHolder").find(".menuBlock").size();
			//17 category menus shown for max window
			if(ZL.menuMoves > (cats-17)){
				ZL.menuMoves--;
				end = true;
			}
		}
		if(!end){
			var newTop = (ZL.menuMoves * -ZL.iMenuBlockHeight) + 16;
			$("#menuHolder").animate({top:newTop},300);
		}
    };

    WORK_Album.prototype.buildMenu=function(){
    
      //Get INIT values and properties
      sXML=ZL.sXML;
      var divMenu = ZL.divMenu;
      var iCount = ZA.getXML(sXML,"albumcount");
      ZL.menuMoves = 0;
      ZL.menuMovesMax = iCount - 7;

	  //Menu scroller: UP
		// var upArrow = ZA.createDiv(ZL.divMenu,"menuArrow","upArrow","div");
		// $(upArrow).css({
			// cursor:"pointer",
			// top:0,
			// left:0,
			// width:"100%",
			// height:12,
			// padding:2,
			// textAlign:"center",
			// color:"#FFF",
			// background:"#b2b2b2",
			// "z-index":99,
			// "border-bottom":"1px solid black",
// 			
		// });
		// $(upArrow).html('&#x25B2;');
		// $(upArrow).click(function(){
			// if(ZL.menuMoves > 0) {
				// ZL.menuMoves--;
				// ZL.moveMenu();
			// }
		// });
		// var divLine = ZA.createDiv(upArrow,"","","div");
        // $(divLine).css({ bottom:-2,left:0,width:"100%",height:2,backgroundImage:"url(_site/repeatx.png)",backgroundPosition:"0px -121px" });

	  //Menu holder for menu blocks
		var menuHolder = ZA.createDiv(ZL.divMenu,"","menuHolder","div");
		$(menuHolder).css({width:"100%",top:16});

    var CatCounter = 0;
    var maxCards = 0;

    //New Cards Menu
    var newTotal = ZA.getXML(sXML,"album_new/totalcards");
    var newOwned = ZA.getXML(sXML,"album_new/ownedcards");
    if(newTotal > 0){
      maxCards = 10000000;
      ZL.currentAlbum = "new";
      var menuBlock = ZA.createDiv(menuHolder,"menuBlock","","div");
      $(menuBlock).addClass('menuSelected');
      $(menuBlock).attr('alt',-1);
      $(menuBlock).css({
      		fontWeight:"bold",
      		"text-shadow":"0px -1px -1px #d1d1d1",
	      	cursor:"pointer",
	      	top:0,
	      	left:-15,
	      	width:"100%",
	      	height:10,
	      	padding:8,
	      	textAlign:"right",
	      	color:"#FFFFFF",
	  }); 
      $(menuBlock).html("New Cards ("+newOwned+")");
       $(menuBlock).click(function(){
         ZL.currentAlbum="new";
         $(".menuBlock").removeClass('menuSelected');
         $(this).addClass('menuSelected');
         ZL.showAlbum(ZL.currentAlbum);
       });
       var divLine = ZA.createDiv(menuBlock,"","","div");
       $(divLine).css({ bottom:-2,left:22,width:"80%",height:1,backgroundColor:"#383838" });
       CatCounter++;
     }
    

    //All Cards Menu
    var newTotal = ZA.getXML(sXML,"album_all/totalcards");
    var newOwned = ZA.getXML(sXML,"album_all/ownedcards");
    var menuBlock = ZA.createDiv(menuHolder,"menuBlock","","div");
    
    $(menuBlock).attr('alt',0);
    $(menuBlock).css({ cursor:"pointer",top:(CatCounter*ZL.iMenuBlockHeight),left:-15,width:"100%",height:10,padding:8,fontWeight:"bold",textAlign:"right","text-shadow":"0px -1px -1px #d1d1d1",color:"#ffffff" }); 
    $(menuBlock).html("All Cards ("+newOwned+"/"+newTotal+")");
     $(menuBlock).click(function(){
       ZL.currentAlbum="all";
       $(".menuBlock").removeClass('menuSelected');
       $(this).addClass('menuSelected');
       ZL.showAlbum(ZL.currentAlbum);
     });
     var divLine = ZA.createDiv(menuBlock,"whiteLine","","div");
     $(divLine).css({ bottom:-2,left:22,width:"80%",height:1,backgroundColor:"#383838" });
     CatCounter++;
     //END All Cards Menu
     
     //Cats
      for (i=0;i<iCount;i++){
        var menuBlock = ZA.createDiv(menuHolder,"menuBlock","","div");
        $(menuBlock).attr('alt',i);
        
        var iTop = (i+CatCounter)*ZL.iMenuBlockHeight;
        $(menuBlock).css({ cursor:"pointer",top:iTop,left:-15,width:"100%",height:10,padding:8,textAlign:"right",fontWeight:"bold","text-shadow":"0px -1px -1px #d1d1d1",color:"#ffffff" }); 
        var newTotal = ZA.getXML(sXML,"album_"+i+"/totalcards");
        var newOwned = parseInt(ZA.getXML(sXML,"album_"+i+"/ownedcards"));
        var newDesc = ZA.getXML(sXML,"album_"+i+"/description");
        $(menuBlock).html(newDesc+" ("+newOwned+"/"+newTotal+")");
      
        if(newOwned > maxCards){
          ZL.currentAlbum = i;
          $(".menuBlock").removeClass('menuSelected');
          $(menuBlock).addClass('menuSelected');
          maxCards = newOwned;
        }
        
        
        menuBlock.onclick = (function() {
            var current_i = i;
            return function() {
              // Hide fullcard if user clicks a different album
              // or if the current page is not the first page of the same album
              if(ZL.currentAlbum != current_i) {
          		ZL.hideFullCards();
              }
              else {
              	if(ZL.currentPage != 0) {
              		ZL.hideFullCards();
              	}
              }
              ZL.currentAlbum=current_i;
              $(".menuBlock").removeClass('menuSelected');
              $(this).addClass('menuSelected');
              
              ZL.showAlbum(current_i);
            }
         })();
        var divLine = ZA.createDiv(menuBlock,"","","div");
        $(divLine).css({ bottom:-2,left:22,width:"80%",height:1,backgroundColor:"#383838" }); 
      }
      
      //Menu scroller: DOWN
		// var downArrow = ZA.createDiv(ZL.divMenu,"menuArrow","downArrow","div");
		// $(downArrow).css({
			// cursor:"pointer",
			// left:0,
			// bottom:0,
			// width:"100%",
			// height:13,
			// padding:2,
			// textAlign:"center",
			// color:"#FFF",
			// background:"#ababab",
// 			
		// }); 
		// $(downArrow).html('&#x25BC;');
		// $(downArrow).click(function(){
			// if(ZL.menuMoves < ZL.menuMovesMax+1) {
				// ZL.menuMoves++;
				// ZL.moveMenu();
			// }
		// });
		// var divLine = ZA.createDiv(downArrow,"","","div");
      // $(divLine).css({ top:-2,left:0,width:"100%",height:2,backgroundColor:"#292929"});
    };
    
    WORK_Album.prototype.hideFullCards=function(){
    	//Hide any open fullcards
    	var divFull=document.getElementById("cardfull");
		if (divFull){
			var divBody=document.getElementsByTagName("body")[0];
			divBody.removeChild(divFull);
		}
    };
    
    WORK_Album.prototype.showAlbum=function(albumNr){
      if(albumNr=="new"){
        ZA.callAjax(ZL.sURL+"?viewed=1",function(xml){ });
      }
      
      
      //Get INIT values and properties
      sXML=ZL.sXML;
      xmlShort = "album_"+albumNr+"/cards/card_";
      var divList = ZL.divList;
      $(divList).html("");
      ZL.currentPage = 0;
      ZL.itemCount = ZA.getXML(sXML,"album_"+albumNr+"/totalcards");
      ZL.pageCount = Math.ceil(ZL.itemCount / ZL.perPage);
      ZL.iWidthOfList = ZL.pageCount*(ZL.iWindowWidth-120);
      $(divList).css({width:ZL.iWidthOfList});
      var iRows = Math.ceil(ZL.itemCount / ZL.perRow);
      var itemCount = 0;
      var itemRowCount = 0;
      var iLeft = 30;
      var iTop = 35;
      var iStart = 0;
      if (ZA.aComponents[ZL.iComponentNo].iIsMaximized) {
        var lMod = 138;
      }else{
        var lMod = 138;
      }
      while(itemCount < ZL.itemCount){
        if (itemRowCount == ZL.perRow) {
          iTop += 145;
          iLeft = (ZL.iWindowWidth-lMod) * iStart + 30;
          itemRowCount = 0;
        }
        if ((itemCount % ZL.perPage==0)&&(itemCount != 0)) { 
          iStart++;
          iTop = 35;
          iLeft = (ZL.iWindowWidth-lMod) * iStart + 30;
        }
        var cardBlock = ZA.createDiv(divList,"cblock","albumthumbnail_"+itemCount,"div");
        $(cardBlock).css({ top:iTop,left:iLeft,width:85,height:115 });
        var imgBlock = ZA.createDiv(cardBlock,"","","img");
        $(imgBlock).css({ width:70,height:95,marginBottom:3 });
        imgBlock.src = ZL.imagePath+"cards/jpeg/"+ZA.getXML(sXML,xmlShort+itemCount+"/img")+"_web.jpg";
        var shadowBlock = ZA.createDiv(cardBlock,"","","div");
        $(shadowBlock).css({ top:0,left:7,width:70,height:95,backgroundImage:"url(_site/all_1.png)",backgroundPosition:"-615px -119px" });
        var description = ZA.getXML(sXML,xmlShort+itemCount+"/description");
        var label = ZA.getLimitedString(description, 14, ' ');
        var labeltitle = '';
        if(label.length < description.length){
        	label+='..';
        	labeltitle = ' title="'+description+'"';
        }
        $(cardBlock).append('<div style="width:100%;overflow:hidden;text-align:center;"'+labeltitle+'>'+label+'</div>');
        
        //Add click event to owned card
        if (parseInt(ZA.getXML(sXML,xmlShort+itemCount+"/qty")) > 0){
       		$(cardBlock).attr('alt',itemCount);
          $(shadowBlock).click(function(){
            var id = $(this).parent().attr('alt');
            ZL.clickShowFullImage(albumNr, id);
          });
          $(shadowBlock).css('cursor','pointer');
          
          //Display number of card owned
          var avail = parseInt(ZA.getXML(sXML,xmlShort+itemCount+"/qty"));
          var availDisplay = ZA.createDiv(cardBlock,"avail","","div");
          $(availDisplay).html(avail);
          $(availDisplay).css({
            top:-6,
            right:-1,
            width:"18px",
            paddingTop:2,
            height:"19px",
            "z-index":2,
            "background-image":"url(_site/all.png)",
            "background-position":"-460px -41px",
            "color":"#F2C126",
            "font-weight":"bold",
          });
        }
        else{
        	$(imgBlock).css({ opacity:0.1 });
        }
        
        iLeft += 85
        itemCount++;
        itemRowCount++;
      }
      $(ZL.divScroll).remove();
      ZL.divScroll = ZA.createDiv(ZL.divData,"","divScroll","div");
      $(ZL.divScroll).css({ bottom:0,left:0,width:"100%",height:20 });
      ZL.buildScroller();
    };

	WORK_Album.prototype.flipCard=function(iCardNo,iIsFront){
		return function(){
			if(!ZL.dragged)
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
				ZL.dragged = false;
			}
		};
	};

	WORK_Album.prototype.clickCloseFullImage=function(){
		return function(){
			var divBody=document.getElementsByTagName("body")[0];
			var divFull=document.getElementById("cardfull");
			if (divFull){
				divBody.removeChild(divFull);
			}
		};
	};

	WORK_Album.prototype.clickShowFullImage=function(iAlbumNo, iThumbnailNo){
		
		var divThumbnail=document.getElementById("albumthumbnail_"+iThumbnailNo);
		var divWindow=document.getElementById("window_"+ZL.iComponentNo);
		var iWidthWindow=parseInt(divWindow.style.width);
		var sImg=ZA.getXML(ZL.sXML,"album_"+iAlbumNo+"/cards/card_"+iThumbnailNo+"/path")
				+"cards/jpeg/"
				+ZA.getXML(ZL.sXML,"album_"+iAlbumNo+"/cards/card_"+iThumbnailNo+"/img");
		var xy = ZA.findXY(divThumbnail);
		var iLeft = xy[0]+42;
		var iTop = xy[1]-65;
		var divBody=document.getElementsByTagName("body")[0];
		var divFull=document.getElementById("cardfull");
		
		if (divFull){
			divBody.removeChild(divFull);
		}
		
		//Avoid card displaying off page on maximized window
		if(iTop < 250){ iTop = 250; }
		if(iLeft-125 < 0){ iLeft = 125; }
    
		var divFull=ZA.createDiv(divBody,"cardfull","cardfull");
		
		var divInfo=ZA.createDiv(divFull,"cardfullinfo");
		$(divInfo).attr('title','Close');
		divInfo.onclick=ZL.clickCloseFullImage();
		var divImg=ZA.createDiv(divFull,"cardfullimage","cardfull1","img");
		divImg.onclick=ZL.flipCard(iThumbnailNo,1);
		$(divImg).css({display:"none","z-index":"100"});
		divImg.src=sImg+"_front.jpg";
		var divImg2=ZA.createDiv(divFull,"cardfullimage","cardfull0","img");
		divImg2.onclick=ZL.flipCard(iThumbnailNo,0);
		$(divImg2).css({"z-index":"100"});
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
			$(divMenu).show();
		});
		ZA.setNextZIndex(divFull);

		/*
		 * Card menu tabs
		 */

		var divMenu = ZA.createDiv(divFull,"cardMenu");
		/*
		//Compare tab
		var divTab = ZA.createDiv(divMenu,"menuTab","tabCompare");
		$(divTab).css({
			background:"url(_site/all.png) -735px -125px no-repeat",
			top:70
		});
		$(divTab).attr('title','Compare card');
		$(divTab).click(function(){
			var cardid = ZA.getXML(ZL.sXML,"album_"+iAlbumNo+"/cards/card_"+iThumbnailNo+"/cardid");
			ZA.showCompare(cardid);
		});
		*/
		//Auction tab
		var divTab = ZA.createDiv(divMenu,"menuTab","tabAuction");
		$(divTab).css({
			background:"url(_site/all_1.png) -735px -60px no-repeat",
			//top:130
			top:70
		});
		$(divTab).attr('title','Sell card on auction');
		$(divTab).click(function(){
			ZL.aWindowAuction = new WORK_CreateAuction();
			ZL.aWindowAuction.create(iAlbumNo, iThumbnailNo);
		});
		
		/* 
		//Give-to-friend tab
		var divTab = ZA.createDiv(divMenu,"menuTab");
		$(divTab).css({
			background:"url(_site/all.png) -775px -60px no-repeat",
			top:190
		});
		$(divTab).attr('title','Give card to friend');
		$(divTab).click(function(){
			var card_id = ZA.getXML(ZL.sXML,"album_"+iAlbumNo+"/cards/card_"+iThumbnailNo+"/cardid");
			ZA.sendCardScreen(card_id);
		});
		
		//Deck tab
		var divDeckTab = ZA.createDiv(divMenu,"menuTab");
		$(divDeckTab).css({
			background:"url(_site/all.png) -815px -60px no-repeat",
			top:40
		}).hide();
		$(divDeckTab).attr('title','Add card to deck');
		$(divDeckTab).click(function(){
			alert('Add card to a deck...');
		});
		*/
		/*
		 * END OF: Card menu tabs
		 */
		
		
		$(divFull).draggable("destroy");
		$(divFull).draggable({
			start: function(){
				ZL.dragged = true;
			},
			containment: "body",
			cancel:".menuTab, .cardfullinfo"
		});
	};
	

    WORK_Album.prototype.buildScroller=function(){
      
      var divArrowLeft = ZA.createDiv(ZL.divScroll,"controlspageleft","","div");
      $(divArrowLeft).click(function(e){
        if(ZL.currentPage != 0){
          ZL.hideFullCards();
          ZL.currentPage--;
          ZL.gotoPage(ZL.currentPage);
        }
      });
      
      var divPageCountList = ZA.createDiv(ZL.divScroll,"","","div");
      var offsetCount = (ZL.iWindowWidth-40-(ZL.pageCount*14))/2;
      var iLeft = offsetCount-50;
      $(divPageCountList).css({ top:0,left:20,width:"100%",height:20});

      for(i=0;i<ZL.pageCount;i++){
        var divPageIcon = ZA.createDiv(divPageCountList,"dot","","div");
        iLeft += 14;
        $(divPageIcon).css({ top:7,left:iLeft,cursor:"pointer",width:7,height:7,backgroundImage:ZS.imgAll,backgroundPosition:"-421px -60px"});
        divPageIcon.onclick = (function() {
            var current_i = i;
            return function() {
            	if(ZL.currentPage!=current_i){
            		ZL.hideFullCards();
            	}
                ZL.currentPage=current_i;
                ZL.gotoPage(current_i);
            }
         })();
      }
      
      ZL.gotoPage(0);
      var divArrowRight = ZA.createDiv(ZL.divScroll,"controlspageright","albumarrowright","div");
      $(divArrowRight).click(function(){
        if(ZL.currentPage != ZL.pageCount-1){
          ZL.hideFullCards();
          ZL.currentPage++;
          ZL.gotoPage(ZL.currentPage);
        }
      });
    };
    
    WORK_Album.prototype.gotoPage=function(page){
      for (i=0;i<ZL.pageCount;i++){
        $(ZL.divScroll.childNodes[1].childNodes[i]).css({ backgroundPosition:"-441px -60px" }); 
      }
      $(ZL.divScroll.childNodes[1].childNodes[page]).css({ backgroundPosition:"-421px -60px" });
      if (ZA.aComponents[ZL.iComponentNo].iIsMaximized) {
        var newPos = page*-814;
      }else{
        var newPos = page*-646;
      }
      $(ZL.divList).animate({left:newPos},600);
    };

    WORK_Album.prototype.maximize=function(){
      if (ZA.aComponents[ZL.iComponentNo].iIsMaximized) {
			ZL.perRow = 7;
			ZL.perPage = 28;
			ZL.iWindowWidth = 955;
			ZL.iWindowHeight = 580;
			ZL.currentPage = 0;
			$(ZL.divList).css({ height:ZL.iWindowHeight-26 });
			$(ZL.divMenu).css({ height:ZL.iWindowHeight-26,width:138 });
			ZL.showAlbum(ZL.currentAlbum);
      } else {
			ZL.perRow = 7;
			ZL.perPage = 7;
			ZL.iWindowWidth = 784;
			ZL.iWindowHeight = 185;
			ZL.currentPage = 0;
			$(ZL.divList).css({ height:ZL.iWindowHeight-25 });
			$(ZL.divMenu).css({ height:ZL.iWindowHeight-25,width:138 });
			ZL.showAlbum(ZL.currentAlbum);
			if($(".menuSelected").attr('alt') >= 7) {
				ZL.menuMoves = ($(".menuSelected").attr('alt') - 7) + 1;
				ZL.moveMenu();
			}
      }
      ZL.menuMoves = 0;
      $("#menuHolder").css({top:16});
    };


		}
	WORK_Album._iInited=1;
};



/** ========================================================================
CREATE AUCTION CLASS
*/
function WORK_CreateAuction(){

if (typeof WORK_CreateAuction._iInited=="undefined"){

/*********** close auction window */
WORK_CreateAuction.prototype.clickClose=function(){
	return function() {
		ZL.aWindowAuction.clickCloseA();
	};
};

/*********** close auction window action */
WORK_CreateAuction.prototype.clickCloseA=function(){
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

/*********** create auction window */
WORK_CreateAuction.prototype.create=function(albumID, cardID){
	
	var divBody=document.getElementsByTagName("body")[0];
	var iDocHeight=document.documentElement.scrollHeight;
	ZA.createWindowPopup(0,"NewDeck",580,485,1,0);
	var divData=document.getElementById("window_0");
	var iTop=10;
	var iLeft=10;
	
	//Card details
	
	var divCard = ZA.createDiv(divData,"auctionAlbumContainer");
	$(divCard).css({
		width:"93%",
		height:"350px",
		margin:10,
		padding:10,
	});
		
		//Card thumbnail
		var divCardImage = ZA.createDiv(divCard);
		$(divCardImage).css({
			backgroundImage:"url("
				+ZA.getXML(ZL.sXML,"album_"+albumID+"/cards/card_"+cardID+"/path")
				+"cards/jpeg/"
				+ZA.getXML(ZL.sXML,"album_"+albumID+"/cards/card_"+cardID+"/img")
				+"_front.jpg)",
			"background-repeat":"no-repeat",
			right:iLeft+"px",
			top:iTop+"px",
			width:250,
			height:350,
		});
		//iLeft=83;
		
		//Card title
		var divCardTitle = ZA.createDiv(divCard,"txtGreen","cardDescription","div");
		$(divCardTitle).css({
			left:iLeft,
			top:iTop+5,
			fontSize:20,
			fontWeight:"bold"
		});
		$(divCardTitle).html(ZA.getXML(ZL.sXML,"album_"+albumID+"/cards/card_"+cardID+"/description"));
		iTop+=25;
		
		//Card Category
		var divCardTitle = ZA.createDiv(divCard);
		$(divCardTitle).css({
			color:"#999",
			left:iLeft,
			top:iTop,
			fontSize:12,
			fontWeight:"bold"
		});
		$(divCardTitle).html(ZA.getXML(ZL.sXML,"album_"+albumID+"/description"));
		iTop+=35;
		
	//Auction details
		
		//Minimum bid amount
		var minBid = ZA.getXML(ZL.sXML,"album_"+albumID+"/cards/card_"+cardID+"/value");
		var divInput=ZE.createInput(divCard,iLeft,iTop,200,10,"Minimum Bid Amount","minimum_bid");
		$("#minimum_bid").addClass("txtBlue").css({
			background:"transparent",
			fontSize:16,
			boxShadow:"none",
			fontWeight:"900",
			fontFamily:"Arial",
			border:"0px none",
			"-moz-user-select":"none",
			cursor:"default",
		});
		$("#minimum_bid").attr("readonly",true);
		$("#minimum_bid").val(minBid+' TCG');
		$("#minimum_bid").focus(function(){
			$(this).blur();
		});
		iTop+=35;
		var div = ZA.createDiv(divCard,"","slideMin");
		$(div).css({
			top:iTop,
			left:iLeft,
			width:235
		});
		$("#slideMin").slider({
			range: "min",
			min: 1,
			max: 500,
			value: minBid,
			slide: function(event, ui){
				$("#minimum_bid").val(ui.value+' TCG');
				if($("#slidePrice").slider("value") != 0 && $("#slidePrice").slider("value") < (ui.value*2)){
					$("#slidePrice").slider("value",ui.value*2);
				}
			}
		});
		$("#slideMin").find(".ui-slider-range").css({
			background:"#F2C126"
		})
		iTop+=40;
		
		//Buyout price
		var divInput=ZE.createInput(divCard,iLeft,iTop,200,10,"Buyout Price","price");
		$("#price").css({
			background:"transparent",
			color:"#cc0000",
			fontSize:16,
			boxShadow:"none",
			fontWeight:"900",
			fontFamily:"Arial",
			border:"0px none",
			"-moz-user-select":"none",
			cursor:"default"
		});
		$("#price").attr("readonly",true);
		$("#price").val(parseInt(minBid)*2+' TCG');
		$("#price").focus(function(){
			$(this).blur();
		});
		iTop+=15;
		var div = ZA.createDiv(divCard,"","slidePrice");
		$(div).css({
			top:iTop,
			left:iLeft,
			width:235
		});
		$("#slidePrice").slider({
			range: "min",
			min: 0,
			max: 1000,
			step: 10,
			value: (parseInt(minBid)*2),
			change: function(event, ui){
				if(ui.value > 0){
					if($("#slideMin").slider("value") > (ui.value/2)){
						var newval = ui.value/2;
						$("#slideMin").slider("value",newval);
						$("#minimum_bid").val(newval+' TCG');
					}
				}
				slidePriceChange(ui.value); 
			},
			slide: function(event, ui){ slidePriceChange(ui.value); }
		});
		$("#slidePrice").find(".ui-slider-range").css({
			background:"#cc0000"
		})
		iTop+=40;
		
		function slidePriceChange(val){
			var tcg = 'No buyout';
			if(val > 0){
				tcg = val+' TCG';
			}
			$("#price").val(tcg);
		}
		
		//Auction expiry date
		var divInput=ZE.createInput(divCard,iLeft,iTop,200,16,"Expiry Date","date_expired");
		$("#date_expired").css({
			background:"transparent",
			border:"0px none",
			color:"#999",
			fontSize:16,
			boxShadow:"none",
			fontWeight:"900",
			fontFamily:"Arial"
		});
		$("#date_expired").attr('readonly',true);
		iTop+=35;
		var divDate = ZA.createDiv(divCard,"","expirydate");
		$(divDate).css({
			top:iTop,
			left:iLeft
		});
		$("#expirydate").datepicker({
			altField: "#date_expired",
			altFormat: "yy-mm-dd",
			inline: true,
			dateFormat: "yy-mm-dd",
			minDate: 0,
			defaultDate: +5
		});
		
	
	//Command buttons
	var div = ZA.createDiv(divData,"cmdButton");
	$(div)
		.html('Cancel')
		.css({bottom:10,right:135})
		.click(function(){
			ZL.aWindowAuction.clickCloseA();
		});
	$()
	var div = ZA.createDiv(divData,"cmdButton");
	$(div)
		.html('Create Auction')
		.css({bottom:10,right:10})
		.click(function(){
			ZL.aWindowAuction.clickSave(ZA.getXML(ZL.sXML,"album_"+albumID+"/cards/card_"+cardID+"/cardid"));
		});
	$()
	
};

/*********** click save button */
WORK_CreateAuction.prototype.clickSave=function(card_id){
	if($("#price").val() == 'No buyout'){
		var cost = parseInt(parseInt($("#minimum_bid").val()) * 0.1,10);
	}
	else{
		var cost = parseInt(parseInt($("#price").val()) * 0.1,10);
	}
	cost = (cost < 5) ? 5 : cost;
	
	if(parseInt(ZA.sUserCredits) >= cost){
		if(confirm(
			'AUCTION DETAILS\n========================================'
			+"\nCard:\t\t\t "+$("#cardDescription").html()
			+"\nMinimum Bid:\t "+$("#minimum_bid").val()
			+"\nBuyout Price:\t\t "+$("#price").val()
			+"\nExpiry Date:\t\t "+$("#date_expired").val()
			+"\nCost:\t\t\t "+cost+" TCG"
			+"\n========================================"
			+"\nIMPORTANT:\n* Once the card is placed on auction it cannot be revoked\n   and will be sold to the highest bidder.\n* Should there be no bids when the auction expires, the card\n   will be removed from auction and returned to the owner.\n* A minimum cost of 5 TCG credits or 10% of highest value\n   between the minimum bid and buyout price will apply\n   for creating of this auction."
			+"\n\nClick OK to create auction.\n "
		)){
				
			var minimum_bid = parseInt($("#minimum_bid").val(),10);
			var price = $("#price").val();
			if(price == 'No buyout'){
				price = '0';
			}
			else{
				price = parseInt($("#price").val(),10);
			}
			var date_expired = $("#date_expired").val();
				
			ZA.callAjax(ZL.sURL+"?auction=1"
				+"&card_id="+card_id
				+"&minimum_bid="+minimum_bid
				+"&price="+price
				+"&date_expired="+date_expired
			,
			function(xml){
				var result = ZA.getXML(xml,"result");
				if(result=='success')
				{
					//Success
					ZL.aWindowAuction.clickCloseA();
					$(".cardfullinfo").click();
						
		        	//Reload my album
					$(ZL.divData).html('<div class="loader"></div>');
					ZA.callAjax(ZL.sURL+"?init=1",function(xml){ZL.init(xml);});
		        	
		        	//Reload auctions
					ZU.init();
					
					//Inform user
					var cost = ZA.getXML(xml,"cost");
					var credits = ZA.getXML(xml,"credits");
		        	ZA.sUserCredits=credits;
		        	ZA.oPlayerBar.update({credits:ZA.sUserCredits});
					var icon = "-667px -63px";
					var response = "Auction was created successfully. Cost: "+cost+" TCG";
					ZS.showWindow(icon,response);
				}
				else
				{
					//Failure
					var icon = "-697px -63px";
	      			var response = "Unexpected error. Auction was not created.";
					ZS.showWindow(icon,response);
				}
			});
		}
	}
	else{
		var icon = "-697px -63px";
		ZS.showWindow(icon,'Insufficient credits to create auction.',5000);
	}
};

WORK_CreateAuction.prototype.closeError=function(){
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

WORK_CreateAuction.prototype.closeFirstVisit=function(){
	ZA.refreshBrowser();
};

/**
=============================================================================
	finish NEWDECK CLASS */	
	WORK_CreateAuction._iInited=1;
	}
};//END function WORK_CreateAuction()



var ZL = new WORK_Album();
ZA.aComponents[ZL.iComponentNo].fMaximizeFunction=ZL.maximize;
ZA.callAjax(ZL.sURL+"?init=1",function(xml){ZL.init(xml);});
