 function APP_Main(){
   this.something=null;
   this.cardTarget = 0;
   this.gamePage = "mainGameMenu";
   this.gameCurrentPageDivID = "game_menu";
   this.gameDeckID = 0;
   this.deckImageNumber = null;
   this.gameDifficultyID = null;
   this.gameCategoryID = 2;
   this.gameOpponentID = null;
   this.blinker = 0;
   this.winningStatNumber;
   this.looker=null;
   this.sURL=null;
   this.friendPos = 0;
   this.friendListMode = 0;
   this.activityPos = 0;
   this.creditsType = "credOnline";
   this.creditsAmount = 1;
   
   if (typeof APP_Main._iInited=="undefined"){
     APP_Main.prototype.init=function(sXML){
     	App.initXML = sXML;
     	App.userName = App.getXML(sXML,"username");
     	//CHECK FOR USERS ACCEPTING FRIEND REQUESTS HERE
     }
     
     APP_Main.prototype.browserPopup=function(){
		userAgent = navigator.userAgent;
		uaMatch = userAgent.match(/(Firefox|Chrome|MSIE 9.0|Safari|Opera)/i);
		if(uaMatch == ""){
		 App.showNotice("We see you are using an outdated browser. Support for older version are very limited. Too fix this, you can simply update your current browser.",0,true)
		}
	 };
     
     APP_Main.prototype.calcAuctionCost=function(sMin,sPrice){
     	sMin = parseInt(sMin);
     	sPrice = parseInt(sPrice);
     	var value = 0;
     	if(sMin > sPrice){
     		value = sMin * 0.1;
     	}else{
     		value = sPrice * 0.1;
     	}
     	value = Math.round(value); 
     	if(value <= 5){
     		value = 5;
     	}
     	$(".albumAuctionCostNotice").html("Creating this auction will cost <span style=\"color: #AC3030\">"+value+" TCG</span>");
     }
     
     APP_Main.prototype.setSelectNone=function(divName){
		if (!divName){
			divName=document.getElementById("page");
		}
		if (typeof divName.onselectstart!="undefined"){
			divName.onselectstart=function(){return false;};//MSIE
		}
		else if (typeof divName.style.MozUserSelect!="undefined"){
			divName.style.MozUserSelect="none";//Firefox
		}
		else {
			divName.onmousedown=function(){return false;};//Other
		}
	};
     
     APP_Main.prototype.validateEmail=function(email){
     	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
   		return re.test(email);
	}
     
     APP_Main.prototype.modalCardView=function(imgID){
		var divModalPic = App.createDiv(divBody,"modal-picture-holder");
		var divClose = App.createDiv(divModalPic,"close-button");
		
		$(divClose).click(function() {
	        window.flipped = false;
	        $('.modal-window').fadeTo("fast",1);
	        $(".modal-window").remove();
	        $(".modal-picture-holder").remove();
	        $("#mask").hide();
	    });
		
		var divFullPic = App.createDiv(divModalPic,"modal-full-picture");
		var imgPic = App.createDiv(divFullPic,"","","img");
		imgPic.src = "https://sarugbycards.com/img/cards/jpeg/"+imgID+"_front.jpg";
		
		 //Get the screen height and width
	     // var maskHeight = $(document).height();
	     // var maskWidth = $(window).width();
	     //Set height and width of mask to fill up the whole screen
	     // $('#mask').css({'width':maskWidth,'height':maskHeight});
      
     //transition effect - show the mask  
     $('#mask').fadeIn('fast');   
     $('#mask').fadeTo("medium",0.6); 
    
	$('.modal-picture-holder').css({top:'200px',left:'250px',zIndex:1});
		$('.modal-picture-holder').show('300');
		
		$('.modal-full-picture').click(function(){
			if (window.flipped!=true){
				$(this).flip({
				speed:200,
				direction:'lr',
				content:"<img src='https://sarugbycards.com/img/cards/jpeg/"+imgID+"_back.jpg'/>"
				});
				window.flipped = true;
			} else {
				$(this).flip({
				speed:200,
				direction:'lr',
				content:"<img src='https://sarugbycards.com/img/cards/jpeg/"+imgID+"_front.jpg'/>"
				});
				window.flipped = false;
			}	
		});
     };
     
     APP_Main.prototype.stopFriending=function() {
		clearInterval(App.friender);
		App.friender = null;
		};

		APP_Main.prototype.redrawLeaderboardList=function(sXML) {
			$(".leaderScrollBox").html("CLICK ON RIGHT SIDE TO ADD FRIENDS");
			var divBox = $(".leaderScrollBox").get(0);
			var iCount = App.getXML(sXML,"count");
			for(i=0; i<iCount; i++){
				var fbid = parseInt(App.getXML(sXML,"leader_"+i+"/facebook_id"));
				var imgPath = (fbid > 0) ? "http://graph.facebook.com/"+fbid+"/picture" : "_site/no-profile.jpg" ;
				var divFriendBox = App.createDiv(divBox,"friendBox","");
				var divFriendBoxPic = App.createDiv(divFriendBox,"friendBoxPic","","img");
				divFriendBoxPic.src = imgPath;
				var divFriendSpeechBubble = App.createDiv(divFriendBox,"friendSpeechBubble","");
				$(divFriendSpeechBubble).html(i+1);
				$(divFriendBox).append("&nbsp;");
				var span1 = App.createDiv(divFriendBox,"","","span");
				var sUsername = App.getXML(sXML,"leader_"+i+"/username");
				$(span1).html(sUsername.substr(0,7));
				$(divFriendBox).append("<br />&nbsp;"+App.getXML(sXML,"leader_"+i+"/value"));
			}
		};

     
     APP_Main.prototype.gameFriender=function() {
			var deckID = -1;
			// if(ZT.playerDeckID > -1){
				// deckID = ZA.getXML(ZD.sXML,"decks/deck_"+ZT.playerDeckID+"/deckid");
			// }
			App.callAjax('_app/play.php?friendgame=1&username='+App.playerFriend+'&category='+App.gameCategoryID+'&deck='+deckID,function(xml){
				var status = App.getXML(xml,"status");
				if(status == 'ready'){
					//Game found and joined
					App.stopFriending();
					App.gameID = App.getXML(xml,"game");
					App.playerOpponent = '1'; //1 - player
					var opponent = App.getXML(xml,"opponent/username");
					var deckranking = App.getXML(xml,"opponent/deckranking");
					$("#friendSearchWaiting").html(
						'<div style="position:relative;margin-top:30px;">Friend has joined the game:<br />'+
						'<strong>'+opponent+' ('+deckranking+')</strong></div>'
					);
					$(".gameNextPlay[id='friend']").show('fast');
				}
				else if(status == 'declined')
				{
					//friend has declined invitation
					App.stopFriending();
					$("#friendSearchWaiting").html(
						'<div style="position:relative;margin-top:35px;">Player <strong>'+ZT.playerFriend+'</strong> has declined your invitation!</div>'+
						'<div class="cmdButton" id="searchAgain2" style="bottom:5px;left:5px;">Search again</div>'
					);
					$("#searchAgain2").click(function(){
						$("#friendSearchWaiting").hide('fast');
						$("#friendSearchForm").show('fast');
						$("#friendSearchWaiting").html(
							'<div style="position:relative;margin-top:25px;">Waiting for <span id="friendUsername" style="font-weight:bold;"></span> to join...<br /><img src="_site/busy2.gif" /></div>'+
							'<div class="cmdButton" id="cancelGame2" style="bottom:10px;left:115px;">Cancel game</div>'
						);
						$("#cancelGame2").click(function(){
							$("#friendSearchWaiting").hide('fast');
							$("#friendSearchForm").show('fast');
							App.stopFriending();
						});
					});
				}
				else
				{
					//waiting for friend to join game
				}
			});
		};
     
    APP_Main.prototype.startFriending=function(){
			App.stopFriending();
			App.friender = setInterval("App.gameFriender()",10000);
		};
		
	APP_Main.prototype.moveFriends = function(modifier){
		var mb = document.getElementById("leaderScrollBox");
		var fmax = mb.getAttribute("val");
		var friends = parseInt(fmax);
		if((modifier > 0)&&(App.friendPos > 0)){
			App.friendPos--;
			$(".leaderScrollBox").animate({left:'+='+(modifier*110)},300);
		}else if((modifier < 0)&&(friends > App.friendPos+5)){
			App.friendPos++;
			$(".leaderScrollBox").animate({left:'+='+(modifier*110)},300);
		}
	};
	
	APP_Main.prototype.moveActivity = function(modifier){
		if((modifier > 0)&&(App.activityPos > 0)){
			App.activityPos--;
			$("#activityMove").animate({left:'+='+(modifier*300)},300);
		}else if((modifier < 0)&&(App.activityPos < 2)){
			App.activityPos++;
			$("#activityMove").animate({left:'+='+(modifier*300)},300);
		}
	};

   APP_Main.prototype.viewAuctionButton=function(iMarketID){
     	
		//var marketID = $(this).get(0).id;
		// var marketID = 40;
		var marketID = iMarketID;
		
		App.callAjax("_app/auction.php?market="+marketID,function(xml){ 
			sXML = xml;
			
			var maskHeight = $(document).height();
	      var maskWidth = $(window).width();
	      //Set height and width of mask to fill up the whole screen
	      $('#mask').css({'width':maskWidth,'height':maskHeight});
	         
	      //transition effect - show the mask  
	      $('#mask').fadeIn('fast');   
	      $('#mask').fadeTo("medium",0.6); 
			
			var divBody = document.body;
			var divAuctionWindow = App.createDiv(divBody,"modal-window","auction-modal-window");
			
			var divCloseContainer = App.createDiv(divAuctionWindow,"closeButtonContainer");
			var divClose = App.createDiv(divCloseContainer,"close-button");
			$(divClose).html("<span>CLOSE</span>");
			$(divClose).click(function() {
	            $('.modal-window').fadeTo("fast",1);
	            $(".modal-window").remove();
	            $("#mask").hide();
	          });
			
			$(divAuctionWindow).css({height:"315px"});
        	var divAuctionInfo = App.createDiv(divAuctionWindow,"auction-info");
        	var divAuctionPic = App.createDiv(divAuctionInfo,"auction-pic","","img");
        	$(divAuctionPic).attr("src",App.getXML(sXML,"details/imageserver")+"cards/"+App.getXML(sXML,"details/image")+"_front.jpg");
        	

			var divAuctionName = App.createDiv(divAuctionInfo,"item-name");
        	$(divAuctionName).html("<span>"+App.getXML(sXML,"details/description")+"</span>");
        	// var divAuctionCat = App.createDiv(divAuctionInfo,"item-type");
        	// $(divAuctionCat).html(App.getXML(sXML,"details/category"));
        	var divAuctionSeller = App.createDiv(divAuctionInfo,"seller-name");
        	var owner = App.getXML(sXML,"details/owner");
			var owner = owner.substring(0, owner.indexOf('@'));
        	//owner = owner.substring(0, owner.indexof("@"));
        	$(divAuctionSeller).html(owner);
        	$(divAuctionSeller).prepend("Seller: ");
        	
        	var divAuctionOwned = App.createDiv(divAuctionPic,"already-owned-icon");
        	
        	var divAuctionTimeContainer = App.createDiv(divAuctionInfo,"auction-time-container");
        	var divAuctionTimeRemaining = App.createDiv(divAuctionTimeContainer,"auction-time-remaining");
        	
        	var enddate = new Date(App.getXML(sXML,"details/expire"));
			var timeleftdisplay = App.getTimeLeft(enddate);
			
			var finished = false;
			if(timeleftdisplay != 'Finished'){
				var timeleftdisplay = timeleftdisplay.split(":");
				for(var i = 0; i < timeleftdisplay.length; i++){
					var timeStamp = App.createDiv(divAuctionTimeRemaining,"time_stamp");
					$(timeStamp).html(timeleftdisplay[i]);
				}
			}else{
				finished = true;
				timeleftdisplay = 'Finished'
				var timeleftdisplay = timeleftdisplay.split(":");
				for(var i = 0; i < timeleftdisplay.length; i++){
					var timeStamp = App.createDiv(divAuctionTimeRemaining,"time_stamp");
					$(timeStamp).html(timeleftdisplay[i]);
				}
			}
        	
        	var bidCount = App.getXML(sXML,"details/history/bid_count");
        	
        	
        	
        	var divAuctionInfoTable = App.createDiv(divAuctionInfo,"","auction-info-table");
        	
        	
        	var tr = App.createDiv(divAuctionInfoTable,"auction-end");
        	var end = App.getXML(sXML,"details/expire");
        	$(tr).html("<div>Ends:</div>"+(end.substr(0,15))+"");
        	
        	var tr = App.createDiv(divAuctionInfoTable,"auction-start");
        	$(tr).html("<div>Started:</div>"+App.getXML(sXML,"details/started")+"");
        	
        	var tr = App.createDiv(divAuctionInfoTable,"bid-count");
        	$(tr).html("<div>Bid Count:</div>"+bidCount+"");
        	
        	var tr = App.createDiv(divAuctionInfoTable,"highest-bidder");
        	
        	if(bidCount > 0){
        		var highest = App.getXML(sXML,"details/history/bid_0/user");
        	}else{
        		var highest = "None";
        	}
        	
        	$(tr).html("<div>Highest Bidder:</div>"+(highest.substr(0,15))+"");
        	var tr = App.createDiv(divAuctionInfoTable,"starting-bid");
        	$(tr).html("<div>Starting Bid:</div>"+App.getXML(sXML,"details/starting_bid")+"");

			var divBuyoutPanel = App.createDiv(divAuctionInfoTable,"buyout-panel");
			var buyoutAmn = App.getXML(sXML,"details/price");
			if (buyoutAmn > 0) {
				$(divBuyoutPanel).html("<div>Buyout Amount</div><span>"+buyoutAmn+" TCG</span>");
			}
		
			if (!finished) {
				var bidMin = parseInt(App.getXML(sXML,"details/starting_bid"));
				var bidRecommend = parseInt(App.getXML(sXML,"details/history/bid_0/amount"));
				if(bidRecommend > bidMin){
					bidMin = bidRecommend;
				}
				totalCredits = parseInt(App.getXML(sXML,"credits"));
				
				if (totalCredits > bidMin) {
					var divBidPanel = App.createDiv(divAuctionInfo,"bid-panel");
					var divBidPanelLabel = App.createDiv(divBidPanel,"panel-label");
					$(divBidPanelLabel).html("<span>Bid</span> Amount");
					var divBidAmountSelector = App.createDiv(divBidPanel,"bid-amount-selector");
					
					if (buyoutAmn > 0) {
						var divBuyoutButton = App.createDiv(divBidPanel,"buyout-button");
						$(divBuyoutButton).html("Buy Now");
						$(divBuyoutButton).click(function() {
							App.bidORbuy("buy",marketID);
						});
					}
					var inputBid = App.createDiv(divBidAmountSelector,"bid-amount");
					//$(inputBid).css({width:"60px"});
					
					var bidMin = parseInt(App.getXML(sXML,"details/starting_bid"));
					var bidRecommend = parseInt(App.getXML(sXML,"details/history/bid_0/amount"));
					if(bidRecommend > bidMin){
						bidMin = bidRecommend;
					}
					bidMin = bidMin+1;
					
					//$(inputBid).attr({id:"inputBid",value:bidMin});
					$(inputBid).html("<span>"+bidMin+"</span> TCG");
					
					totalCredits = parseInt(App.getXML(sXML,"credits"));
					
					if (totalCredits < bidMin) {
						bidMin = totalCredits;
					}

					var divArrowBox = App.createDiv(divBidAmountSelector,"counter-arrow-box");
					$(".counter-arrow-box").slider({ 
						range:"min",
						value: bidMin,
						min: bidMin,
						max: totalCredits,
						animate: true,
						slide: function(event,ui){
							//$('.bid-amount').html((ui.value).toString())
							$('.bid-amount').html("<span>"+(ui.value).toString()+"</span> TCG");
						}
					});
					
					var divBidButton = App.createDiv(divBidPanel,"bid-button");
					$(divBidButton).html("Place Bid");
					$(divBidButton).click(function() {
					   var value = parseInt($(inputBid).text());
					   if(value >= bidMin){
						App.bidORbuy("bid",marketID,value);
					   }else{
						App.showNotice("Oops, we noticed an incorrect bidding amount. Bidding amount has to be at least 1 TCG Credit more than current bid.",0,false)
					   }
					});
				} else {
					var divResponse = App.createDiv(divAuctionInfo,"auctionResponse");
					$(divResponse).html("Unfortunately you do not have enough credits to bid, why not go <a href=\"index.php?page=credits\" style=\"text-decoration:none;\"><span style=\"color:snow;\">buy</span></a> some more?")
				}
			}
			var divBidList = App.createDiv(divAuctionWindow,"bid-list");
			var divBidHeading = App.createDiv(divBidList,"bid-list-heading");

			var ul = App.createDiv(divBidList,"","","ul");
			if(bidCount > 0){
				$(divBidHeading).html("<span>Bidding</span> History");
				for(q=0;q<bidCount;q++){
					var li = App.createDiv(ul,"bid-list-item","","li");
					var span = App.createDiv(li,"bid-date","","span");
					$(span).html(App.getXML(sXML,"details/history/bid_"+q+"/date"));
					var span = App.createDiv(li,"bid-name","","span");
					$(span).html(App.getXML(sXML,"details/history/bid_"+q+"/user"));
					var span = App.createDiv(li,"bid-value","","span");
					$(span).html(App.getXML(sXML,"details/history/bid_"+q+"/amount"));
				}
			}else{
				$(divBidHeading).html("No Bids Yet");
			}
			
			//show the modal window
        	$('.modal-window').show("scale",300);
		});
	}
         
     APP_Main.prototype.redrawAuction=function(sID){
     	App.callAjax("_app/auction.php?filter="+sID,function(sXML){
     		$("#auction_scroll_pane").remove();
     		jQuery("<div></div>",{"id":"auction_scroll_pane"}).insertAfter('#auction_buttons');
     		var divContainer = document.getElementById("auction_scroll_pane");
     		var iCount = App.getXML(sXML,"iCount");
			
     		for(i=0;i<iCount;i++){
     			var iAuctionID = App.getXML(sXML,"auction_"+i+"/auction_id");
     			var sPath = App.getXML(sXML,"auction_"+i+"/imageserver");
     			var sImg = App.getXML(sXML,"auction_"+i+"/image");
     			var iOwned = parseInt(App.getXML(sXML,"auction_"+i+"/owned"));
     			
     			var divAuction = App.createDiv(divContainer,"auctionBlockLarge","win_"+iAuctionID);
     				var divPicCon = App.createDiv(divAuction,"picture-box-container");
     				var divPictureBox = App.createDiv(divPicCon,"picture-box",iAuctionID);
					$(divPictureBox).attr("id",App.getXML(sXML,"auction_"+i+"/auction_id"));
	     			$(divPictureBox).css({width:64,height:90,backgroundImage:"url("+sPath+"cards/jpeg/"+sImg+"_web.jpg)"});
					$(divPictureBox, ".picture-box").click(function (){
						marketID = $(this).attr('id');
						App.viewAuctionButton(marketID);
					});
	     			
	     			/*$(divPictureBox).click({s:sImg},function(event) {
						App.showCardModal(event.data.s);
				    });*/
     			
     			// if(iOwned > 0){
     				// var divOwned = App.createDiv(divPictureBox,"already-owned-icon");
     				// $(divOwned).html(iOwned);
     			// }else if(iOwned == 1){
     				// var divOwned = App.createDiv(divPictureBox,"already-owned-icon");
     			// }
     			
     			//var divAuctionBlockLarge = App.createDiv(divAuction,"auctionBlockLarge","win_"+iAuctionID);
     			var divAuctionBlockDetails = App.createDiv(divAuction,"auction_block_details","win_"+iAuctionID);
     			
	     			var divCarName = App.createDiv(divAuctionBlockDetails,"auction_car_name");
	     			var divCarNamespan = App.createDiv(divCarName,"","","span");
	     			$(divCarNamespan).html(App.getXML(sXML,"auction_"+i+"/description"));
     				$(divCarNamespan).css({color:"#EFEFEF",})
     			
	     			var divSellerName = App.createDiv(divAuctionBlockDetails,"auction_seller_name");
					var sellerName = App.getXML(sXML,"auction_"+i+"/owner");
					var sellerName = sellerName.substring(0,sellerName.indexOf("@"));
					
	     			$(divSellerName).html("Seller: "+sellerName);
	     			
	     			var divTime = App.createDiv(divAuctionBlockDetails,"auction_time_remaining");
	     			var date = App.getXML(sXML,"auction_"+i+"/date_end");
	     			$(divTime).html("Time Left: "+App.getTimeLeft(new Date(date), true)+"");

	     			var divBidContainer = App.createDiv(divAuctionBlockDetails,"bids-info-container");
	     			$(divBidContainer).append('<br>');
     					var divCurrentPrice = App.createDiv(divBidContainer,"current_bid_price");
		     			var spanCurrentBidPrice = App.createDiv(divCurrentPrice,"","","span");
		     			$(spanCurrentBidPrice).html(App.getXML(sXML,"auction_"+i+"/price"));
		     			$(spanCurrentBidPrice).css({color:"#c89544;"});
		     			$(spanCurrentBidPrice).append(" TCG&nbsp;&nbsp;&nbsp;&nbsp;");
		     			$(divCurrentPrice).append("["+App.getXML(sXML,"auction_"+i+"/bids")+" bids]");
						
						
				var buyout = App.getXML(sXML,"auction_"+i+"/buyout");
				if (buyout > 0) {
					var divBuyout = App.createDiv(divAuctionBlockDetails,"buyout");
					var divTrolley = App.createDiv(divAuctionBlockDetails,"trolleyIcon");
					var spanBuyout = App.createDiv(divBuyout,"","","span");
					$(spanBuyout).html(App.getXML(sXML,"auction_"+i+"/buyout"));
					$(divBuyout).append(" TCG");
				} else {
					var divBuyout = App.createDiv(divAuctionBlockDetails,"blank");
				}
     			// var divBids = App.createDiv(spanCurrentBidPrice,"number_of_bids");
     			// var spanNumberBids = App.createDiv(divBids,"","","span");
//      			
     			// $(spanNumberBids).html(App.getXML(sXML,"auction_"+i+"/bids"));
     			
     			
				
				var divIconsBlock = App.createDiv(divAuction,"auctionBlockIcons");
				if(iOwned > 0){
     			var divStarIcon = App.createDiv(divIconsBlock,"starIcon");
     			}
     			
     			var divTrophyIcon = App.createDiv(divIconsBlock,"trophyIcon");
     			var divFlagIcon = App.createDiv(divIconsBlock,"flagIcon");
     			
     			//var divEyeIcon = App.createDiv(divIconsBlock,"eyeIcon viewAuctionButton");
     			
     			var divButton = App.createDiv(divAuctionBlockDetails,"eyeIcon viewAuctionButton");
				$(divButton).attr("id",App.getXML(sXML,"auction_"+i+"/auction_id"));
     			$(divButton).html("View");
					$(divButton, ".viewAuctionButton").click(function (){
						marketID = $(this).attr('id');
						App.viewAuctionButton(marketID);
					});
     			//Click view auction
			   /*$(divButton,".picture-box").click({a:iAuctionID},function(event){
						App.viewAuctionButton(event.data.a);
				});  */
				
											      
     			// var divCart = App.createDiv(divAuctionBlockDetails,"shopping_cart");
     		}
			
			if (iCount <= 0) {
				App.showNotice('No cards matched your search query.',0,true);
     		}
     		$('#auction_scroll_pane').jScrollPane();
     		
     	});
     };
     
    	APP_Main.prototype.showWindow=function(icon,message,delay){
			if (typeof delay == "undefined") delay = 1500;
			var divBody = document.getElementsByTagName("body")[0];
			var divWin = App.createDiv(divBody,"messageWindow","","div");
			$(divWin).html(message);
			var divCheckIcon = App.createDiv(divWin,"","","div");
			$(divCheckIcon).css({
				top:5,
				left:5,
				width:23,
				height:21,
				backgroundImage:"url("+App.imgAll+")",
				backgroundPosition:icon
			});
		   if(delay > 0){
		    $(divWin).animate({ top:-2 },600).delay(delay).animate({ top:-38 },600,function(){
		       $(divWin).remove();
		    });
		   }
		   else{
		    $(divWin).animate({top:-2},600);
		    $(divWin).find('.closeMessageWindow').click(function(){
		      $(this).parent().animate({ top:-38 },600,function(){
		         $(this).remove();
		      });
		      return false;
		    });
		    //$(divWin).remove();
		   }
		};
     
     
     APP_Main.prototype.createDiv=function(divParent,sClassName,sID,sType){
      var divA;
      if (!sType) sType="div";
      divA=document.createElement(sType);
      if (sClassName) divA.className=sClassName;
      if (sID) divA.setAttribute("id",sID);
      try{
        divParent.appendChild(divA);
      }
      catch(err){
        txt=err+".\r\n";
        txt+="DivParent -> "+divParent+".\r\n";
        txt+="DivA -> "+divA+".\r\n";
        console.log(txt);
      }
      return divA;
    };
     
     APP_Main.prototype.callAjax = function(pageQuery,callback){
       $("#divLoader").css({display:"block"});
       $.get(pageQuery,function(xml){
         if(callback){
           callback(xml);
           $("#divLoader").css({display:"none"});
         }
       });
     };
     
     APP_Main.prototype.startLooking=function(){
			App.stopLooking();
			App.looker = setInterval("App.gameLooker()",5000);
		};
     
     APP_Main.prototype.stopLooking=function(){
			clearInterval(App.looker);
			App.looker = null;
		};
	
	APP_Main.prototype.buyCredits=function(){
	 	
	 	var cost;
	 	
		var sType = App.creditsType;
		var iNr = parseInt(App.creditsAmount);
		//assign amount of credits
		switch(iNr){
			case 1: iNr = 350; break;
			case 2: iNr = 700; break;
			case 3: iNr = 1050; break;
		}
		//assign cost in rands per package of credits
		switch(iNr){
			case 350: cost = 5; 
			dollar = '1.00'; 
			break;
			case 700: cost = 10; 
			dollar = '1.50';
			break;
			case 1050: cost = 15; 
			dollar = '2.00';
			break;
		}
		
		var purchaseItem = iNr;
		
		var divBody=document.getElementsByTagName("body")[0];
		var divWindow = App.createDiv(divBody,"modal-window","paymentModalWindow");
		
		var divCloseButtonContainer = App.createDiv(divWindow,"closeButtonContainer");
		var divClose = App.createDiv(divCloseButtonContainer,"creditsCloseButton");
		var spanClose = App.createDiv(divClose);
		$(spanClose).html("Close");
		App.createDiv(divCloseButtonContainer,"half","topHalf");
		App.createDiv(divCloseButtonContainer,"half","bottomHalf");
		
		$(divClose).click(function() {
		  $('.modal-window').fadeTo("fast",1);
	        $(".modal-window").remove();
	        $(".modal-picture-holder").remove();
	        $("#mask").hide();
	    });
		
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		$('#mask').css({'width':maskWidth,'height':maskHeight});
		$('#mask').fadeIn('fast');
		$('#mask').fadeTo("medium",0.6);
		
		if(sType == "sms"){
			var smsCheckout = App.createDiv(divWindow,"","smsCheckout");
			var smsCheckoutPoster = App.createDiv(smsCheckout,"","smsCheckoutPoster");
			var smsUserCode = App.createDiv(smsCheckoutPoster,"","smsUserCode");
			var smsShortCode = App.createDiv(smsCheckoutPoster,"","smsShortCode");
			var smsPurchasedItem = App.createDiv(smsCheckoutPoster,"","smsPurchasedItem");
			var smsItemSpan = App.createDiv(smsPurchasedItem,"","","span");
			var smsItemPrice = App.createDiv(smsCheckoutPoster,"","smsItemPrice");
			
			$(smsUserCode).append('Topcar Cards <br>'+App.userName);
			$(smsShortCode).append('36262');
			
			$(smsPurchasedItem).prepend(iNr);
			$(smsItemSpan).append(' TCG');
			$(smsPurchasedItem).append(' credits');
			$(smsItemPrice).append('R.500');
			
			
		}else if(sType == "cc"){
			
			var creditCardPic = App.createDiv(divWindow,"","creditCardPic");
			var creditCardPurchasedItem = App.createDiv(divWindow,"","creditCardPurchasedItem");
			var creditCardItemSpan = App.createDiv(creditCardPurchasedItem,"","","span");
			var creditCardItemCost = App.createDiv(divWindow,"","creditCardItemCost");
			
			$(creditCardPurchasedItem).prepend(iNr);
			$(creditCardItemSpan).append(' TCG');
			$(creditCardItemCost).append('R.500');
			
			var formCreditCard = App.createDiv(divWindow,"","","form");
			$(formCreditCard).attr({
				'id':'formCreditCard',
				'action':'https://www.vcs.co.za/vvonline/ccform.asp',
				'method':'POST'
			});

			$(formCreditCard).html('<input type="hidden" value="8043" name="p1">');
			$(formCreditCard).append('<input type="hidden" value="" name="p2" id="referenceNumber">');
			$(formCreditCard).append('<input type="hidden" value="'+purchaseItem+' TCG credits" name="p3">');
			$(formCreditCard).append('<input type="hidden" value="R'+cost+'.00" name="p4">');
			$(formCreditCard).append('<input type="hidden" value="https://sarugbycards.com/index.php?page=credits&cancel=1" name="p10">');
			$(formCreditCard).append('<input type="hidden" value="N" name="Budget">');
			$(formCreditCard).append('<input type="hidden" value="'+purchaseItem+'" name="m_1">');
			$(formCreditCard).append('<input id="cmdPayByCreditCard" type="button" value="Pay By Credit Card"></input>');
			
			var creditCardsManyPic = App.createDiv(divWindow,"","creditCardsManyPic");
			
			$("#cmdPayByCreditCard").click(function(){
				if (!$(this).hasClass('cmdButtonDisabled')){
					$(this).addClass('cmdButtonDisabled');
					App.callAjax("_app/credits.php" + "?payment=1&gateway=" + 'creditcard' + "&amount=" + purchaseItem + "&cost=" + cost, function (xml) {
						var result = App.getXML(xml, "result");
						if (result == "success") {
							var ref = App.getXML(xml, "reference");
							$("#referenceNumber").attr('value',ref);
							$("#formCreditCard").submit();
						} else {
							App.showNotice('Unexpected error. Please try again.',0,true);
							$(".close-button").click();
						}
					})
				}
			});
			
		}else if(sType == "pp"){
			
			var paypalPic = App.createDiv(divWindow,"","paypalPic");
			var paypalForm = App.createDiv(divWindow,"","","form");
			$(paypalForm).attr({
				'id':'frmPaypal',
				'action':'https://www.paypal.com/cgi-bin/webscr',
				'method':'post'
			});
			
			$(paypalForm).append('<input type="hidden" value="_s-xclick" name="cmd">');
			$(paypalForm).append('<input type="hidden" name="hosted_button_id" value="C3N4C4RT8JE5W">')
			$(paypalForm).append('<div style="margin-top:50px;text-align:center;"><input type="hidden" name="on0" value="Credits"><span style="font-weight:bold;font-size:16px;color:#404040;">Credits</span></div>');

			$(paypalForm).append(
		       '<select name="os0" style="margin-left:44px;">'+
		       '<option value="550">550$1.00 USD</option>'+
		       '<option value="850">850$1.50 USD</option>'+
		       '<option value="1200">1200$2.00 USD</option>'+
		       '</select>');
			$(paypalForm).append('<input id="referenceNumber" type="hidden" name="os1">');
			$(paypalForm).append('<input type="hidden" name="currency_code" value="USD">')
			$(paypalForm).append('<input id="cmdPayByPaypal" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">')
			$(paypalForm).append('<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">')
		}
		
		//SHOW MODAL WINDOW
		$('.modal-window').fadeIn('fast');
		
		
	 };
		
     
     APP_Main.prototype.gameLooker=function() {
			var deckID = App.gameDeckID;
			// if(ZT.playerDeckID > -1){
				// deckID = ZA.getXML(ZD.sXML,"decks/deck_"+ZT.playerDeckID+"/deckid");
			// }
			App.callAjax('_app/play.php?onlinegame=1&category='+App.gameCategoryID+'&deck='+deckID,function(xml){
				var status = App.getXML(xml,"status");
				if(status == 'ready'){
					App.stopLooking();
					//Game found and joined
					App.gameID = App.getXML(xml,"game");
					App.playerOpponent = '1'; //1 - player
					var opponent = App.getXML(xml,"opponent/username");
					var deckranking = App.getXML(xml,"opponent/deckranking");
					$("#gameOpponent").html(
						'<div style="position:relative;margin-top:30px;">Found online player:<br />'+
						'<strong>'+opponent+' ('+deckranking+')'+'</strong></div>'
					);
					$(".gameNextPlay[id='player']").show('fast');
				}
				else if(status == 'invite'){
					App.stopLooking();
					//Found a user that invited this user for a game
					//This user can accept or reject
					App.gameID = App.getXML(xml,"game");
					App.playerOpponent = '1'; //1 - player
					var opponent = App.getXML(xml,"opponent/username");
					var deckranking = App.getXML(xml,"opponent/deckranking");
					$("#gameOpponent").html(
						'<div style="position:relative;margin-top:25px;">Invite from player:<br />'+
						'<strong>'+opponent+' ('+deckranking+')'+'</strong></div>'+
						'<div class="cmdButton" id="gameDecline" style="bottom:5px;left:5px;" alt="'+App.gameID+'">Decline</div>'+
						'<div class="cmdButton" id="gameAccept" style="bottom:5px;right:5px;" alt="'+App.gameID+'">Accept</div>'
					);
					//click event handlers
					$("#gameDecline").click(function(){
						var id = $(this).attr('alt');
						App.callAjax('_app/play.php?decline=1&game='+id,function(xml){
							$("#gameOpponent").html('<div style="position:relative;margin-top:25px;">Searching for online players...<br /><img src="_site/busy2.gif" /></div>');
							App.startLooking();
						});
					});
					$("#gameAccept").click(function(){
						var id = $(this).attr('alt');
						App.callAjax('_app/play.php?accept=1&game='+id+'&deck='+deckID,function(xml){
							//Game found and joined
							App.gameID = App.getXML(xml,"game");
							App.playerOpponent = '1'; //1 - player
							var opponent = App.getXML(xml,"opponent/username");
							var deckranking = App.getXML(xml,"opponent/deckranking");
							$("#gameOpponent").html(
								'<div style="position:relative;margin-top:30px;">Ready to play against friend:<br />'+
								'<strong>'+opponent+' ('+deckranking+')'+'</strong></div>'
							);
							$(".gameNextPlay[id='player']").show('fast');
						});
					});
				}
			});
		};

     APP_Main.prototype.showGameScreen = function(sSelect){
     	if(App.gamePage == "selectGameDeck"){
	     	App.callAjax("_app/play.php?decklist=1",function(sXML){
	     		$("#game_menu").animate({left:-600},400,function(){
	     			$(this).css({left:765});
	     		});
				$('#choose_deck').animate({left:0},400);
				//Draw the deck list
				if ($('#deck_list').children().length == 0) {
					var divList = $("#deck_list").get(0);
					var divBlock = App.createDiv(divList,"");
					$(divBlock).css({overflow:"hidden",width:280,height:100});
					
					iCount = parseInt(App.getXML(sXML,"iCount"));
					
					var divSlider = App.createDiv(divBlock,"","");
					
					if(iCount > 0){
						$("#next-button").hide();
						for(i=0;i<iCount;i++){
							var deckID = App.getXML(sXML,"deck_"+i);
							var divDeck = App.createDiv(divSlider,"deckBlock game-menu-item");
							var path = App.getXML(sXML,"deck_"+i+"/server") + "decks/" + App.getXML(sXML,"deck_"+i+"/img")+".jpg"
							// $(divDeck).css({background:"url("+path+")",backgroundPosition:"-15px -60px"});
							$(divDeck).append('<img src="'+path+'" alt="Deck"  height="75" />');
							var divDeckTitle = App.createDiv(divDeck,"deckBlockTitle");
							$(divDeckTitle).html(App.getXML(sXML,"deck_"+i+"/title"));
							//var categoryID = App.getXML(sXML,"deck_"+i+"/categoryID");
							
							$(divDeck).click({d:deckID},function(event){
						    	App.gameDeckID = event.data.d;
						    	App.gamePage = "selectGameOpponent";
						    	$(this).parent().parent().remove();
						    	App.showGameScreen();
						    });
						}
					} else {
						$("#next-button").show();
						$(divSlider).addClass('noDeckGamePrompt');
						$(divSlider).html("No decks available. Using top ten user cards.");
						$('.noDeckGamePrompt').click(function(){
							App.gameDeckID = 0;
						   App.gamePage = "selectGameOpponent";
						   App.showGameScreen();
						});
					}
				}
	     	});
     	}else if(App.gamePage == "selectGameOpponent"){
     		$("#choose_deck").animate({left:-600},400,function(){
	     		$(this).css({left:765});
	     	});
			$('#choose_opponent').animate({left:0},400);
			//comp player friend
     	}else if(App.gamePage == "selectGameDifficultyLevel"){
     		$("#choose_opponent").animate({left:-600},400,function(){
	     		$(this).css({left:765});
	     	});
			$('#difficulty_level').animate({left:0},400);
     	}else if(App.gamePage == "gamingCards"){
     		App.activeGamePlayer;
     		App.gameCategoryID = 2;
     		App.playerCardImageURL = "_site/card_back_big.png";
     		App.opponentCardImageURL = "_site/card_back_big.png";
     		$("#load_game").animate({left:-600},400,function(){
	     		$(this).css({left:765});
	     	});
	     	
     		$("#difficulty_level").animate({left:-600},400,function(){
	     		$(this).css({left:765});
	     	});
	     	
	     	APP_Main.prototype.initGame = function(){
	  			App.callAjax("_app/play.php?init=1&game="+App.gameID+"&opponent="+App.gameOpponentID+"&category="+App.gameCategoryID+"&difficulty="+App.gameDifficultyID+"&deck="+App.gameDeckID,function(sXML){
	  				App.playerScore = App.getXML(sXML,"p1score"); 
	  				App.opponentScore = App.getXML(sXML,"p2score");
	  				App.playerCardNumber = App.getXML(sXML,"p1card");
	  				App.opponentName = App.getXML(sXML,"p2name");
	  				$('#opponent-card-side .name').text(App.opponentName);
	  				App.gameID = App.getXML(sXML,"game");
	  				App.playerCardImageURL = $(App.allCards).find('cardid[val='+App.playerCardNumber+'] ~ fullimage').attr('val')+"_back.jpg";
	  				adjustVisualCardStacks();
	  				$("#active_game").unbind('initGame');
	  				App.nextRound(App.gameID);
	  			});
	  		}
	  		
	  		$("#active_game").bind('initGame',function(){
  				App.initGame();
  			});
  			
  			$('#active_game').animate({left:0},400,function(){
				App.callAjax("_app/play.php?cards=1&category="+App.gameCategoryID+"",function(cXML){
     				App.allCards = $.parseXML(cXML);
     				$('#active_game').trigger('initGame');
     			});
			});
			
			$("#player-card-side .attribute-overlay").addClass('ready');
			
			$('#player-card-side .attribute-overlay').click(function(){
				if ($("#player-card-side .attribute-overlay").hasClass('ready')) {
					$("#player-card-side .attribute-overlay").removeClass('ready');
					$("#player-card-side .attribute-overlay").addClass('attribute-overlay-disabled');
					PlayerStatNumber = $(this).attr('id');
					$('#'+PlayerStatNumber+' .indicator-active').show("pulsate",{times:3},400,function(){
	     				App.playCard(App.activeGamePlayer,App.gameID,PlayerStatNumber);
	     			});
				}
			});
     		
  			function displayCardBack(cardOwner,cardImageURL,callback) {
  				switch(cardOwner) {
  					case "player":
  						var divSelector = "#playerCardSideImage";
  						//var sAltText = "Player Card";
  					break;
  					case "opponent":
  						var divSelector = "#opponentCardSideImage";
  						//var sAltText = "Opponent Card";
  					break;
  				}
				
				$(divSelector).flip({
					speed:200,
					direction:'lr',
					content:"<img src="+cardImageURL+" alt='' />",
					color:"#0d0d0d",
					onEnd: function(){
						callback()
					}
				});
  			}
  			
  			APP_Main.prototype.getRoundResult = function(roundResultVal) {
  				switch(roundResultVal) {
  					case "2": return "youLose";
  					break;
  					case "1": return "youWin";
  					break;
  					case "0": return "youDraw";
  					break;
  				}
  			}
  			
  			APP_Main.prototype.playCard = function(activeGamePlayer,gameID,statNumber) {
  				if (activeGamePlayer == 1) {
  					App.callAjax('_app/play.php?play=1&game='+gameID+'&player=1&stat='+statNumber,function(pXML){
  						App.creditsWon = App.getXML(pXML,"creditswon"); 
  						App.opponentCardNumber = App.getXML(pXML,"p2card");
  						roundResultVal = App.getXML(pXML,"winner");
  						App.opponentCardImageURL = $(App.allCards).find('cardid[val='+App.opponentCardNumber+'] ~ fullimage').attr('val')+"_back.jpg";
  						displayCardBack("opponent",App.opponentCardImageURL,function(){
  							roundResultID = App.getRoundResult(roundResultVal);
  							$('.roundResult').attr('id',roundResultID);
  							$('#'+statNumber+' .indicator-active').show("pulsate",{times:8},400,function(){
  								$(this).hide();
  								App.nextRound(App.gameID);
  							});
  							$('.roundResult').show("pulsate",{times:8},400,function(){
  								$(this).hide();
  							});
  						});
  					});
  				} else if (activeGamePlayer == 2) {
  					$("#player-card-side .attribute-overlay").addClass('attribute-overlay-disabled');
     				App.callAjax('_app/play.php?play=1&game='+gameID+'&player=2',function(pXML){
     					App.creditsWon = App.getXML(pXML,"creditswon"); 
     					App.opponentCardNumber = App.getXML(pXML,"p2card");
     					App.winningStatNumber = App.getXML(pXML,"stat");
     					roundResultVal = App.getXML(pXML,"winner");
     					App.opponentCardImageURL = $(App.allCards).find('cardid[val='+App.opponentCardNumber+'] ~ fullimage').attr('val')+"_back.jpg";
     					statNumber = App.winningStatNumber;
     					setTimeout(function(){
	     					displayCardBack("opponent",App.opponentCardImageURL,function(){
								roundResultID = App.getRoundResult(roundResultVal);
	     						$('.roundResult').attr('id',roundResultID);
	     						$('#'+statNumber+' .indicator-active').show("pulsate",{times:8},400,function(){
	  								$(this).hide();
	  								App.nextRound(App.gameID);
	  							});
	  							$('.roundResult').show("pulsate",{times:8},400,function(){
	  								$(this).hide();
	  							});
	     					});
     					},550);
     				});
     			}
     		}
     			
     		function adjustVisualCardStacks() {
     			
     			$('.card-container img').remove();
     			
  				$('#cardNumberLeft').text(App.playerScore);
				playerCardCount = 0
				
				 while (playerCardCount < App.playerScore) {
					 $('#cardContainerLeft').append('<img src="_site/card_back_small.png" />');
					 playerCardCount++;
				}
				
				$('#cardNumberRight').text(App.opponentScore);
				opponentCardCount = 0 
				
				while (opponentCardCount < App.opponentScore) {
					 $('#cardContainerRight').append('<img src="_site/card_back_small.png" />');
					 opponentCardCount++;
				 }
  					
				//Evenly spread the deck of player's cards along the bottom of the active game screen
	
				function addCardMargin (cardStackSelector,marginMultiple,marginCSSref) {
					
					var count = 1;
					
					playerCardPics = $(cardStackSelector).get();
					
					$.each(playerCardPics, function() {
						marginValue = ((count*marginMultiple).toString())+'px';
						$(this).css(marginCSSref,marginValue);
						count++;
						}
					);
				}
					
				addCardMargin('#cardContainerLeft *',5,'margin-left');
				
				addCardMargin('#cardContainerRight *',5,'margin-right');
				
				//Keep the card number in line with the spread out deck of cards
				
			   playerLastChildMargin = $('#cardContainerLeft img:last-child').css('margin-left');
			   opponentLastChildMargin = $('#cardContainerRight img:last-child').css('margin-right');
			   
			   $('#cardContainerLeft').css('left',(parseInt(playerLastChildMargin)-173)+'px');
			   $('#cardContainerRight').css('right',(parseInt(opponentLastChildMargin)-173)+'px');
  			}
  			
  			APP_Main.prototype.nextRound = function(gameID){
				App.callAjax('_app/play.php?nextround=1&game='+gameID,function(nXML){
					App.gameStatus = App.getXML(nXML,"status"); 
					App.playerScore = App.getXML(nXML,"p1score"); 
					App.opponentScore = App.getXML(nXML,"p2score");
					App.playerCardNumber = App.getXML(nXML,"p1card");
					App.playerCardImageURL = $(App.allCards).find('cardid[val='+App.playerCardNumber+'] ~ fullimage').attr('val')+"_back.jpg";
					App.opponentCardImageURL = "_site/card_back_big.png";
					nXMLdoc = $.parseXML(nXML);
					App.activeGamePlayer = $(nXMLdoc).find('activeplayer').attr('val');
					displayCardBack("player",App.playerCardImageURL,function(){
						if (App.activeGamePlayer == 1) {
							$("#player-card-side .attribute-overlay").removeClass('attribute-overlay-disabled');
							$("#player-card-side .attribute-overlay").addClass('ready');
						}
					});
					displayCardBack("opponent",App.opponentCardImageURL,function(){
						
					});
					var drawCardsAmount = (20 - (parseInt(App.playerScore) + parseInt(App.opponentScore)));
					
					$('#gameDrawCard').fadeOut();
					if (drawCardsAmount > 0) {
						$('#drawCardNumber').html(drawCardsAmount);
						$('#gameDrawCard').fadeIn();
					}
					
					adjustVisualCardStacks();
					if  (App.activeGamePlayer == 2) {
						if (App.gameStatus == "incomplete") {
							App.playCard(App.activeGamePlayer,gameID,0);
						} else if (App.gameStatus == "complete") {
							App.gamePage = 'mainGameMenu';
							App.gameCurrentPageDivID = 'active_game';
							//App.gameCurrentPageDivID = $(this).parents(".game-view").attr("id");
							//App.showNotice("You have lost the game. Better luck next time.",0,true);
							App.showGameScreen();
							App.showNotice("You have lost the game. Better luck next time.",0,true);
						}
					} else if (App.activeGamePlayer == 1) {
						if (App.gameStatus == "complete") {
							App.gamePage =  'mainGameMenu';
							App.gameCurrentPageDivID = 'active_game';
							//App.gameCurrentPageDivID = $(this).parents(".game-view").attr("id");
							App.showGameScreen();
							if (!(App.creditsWon>0)) {
								App.showNotice("You have won.",1,true);
							} else {
								App.showNotice("You have received "+App.creditsWon+" credits for winning the game. Spend it wisely.",1,true);
							}
						}
					}
				});
  			}
			
     	}else if(App.gamePage == "player"){
     		
     		$("#choose_opponent").animate({left:-765},400,function(){
	     		$(this).css({left:765});
	     	});
	     	
			$('#game_player_searcher').animate({left:0},400);
			
			App.startLooking();
			
     	}else if(App.gamePage == "friend"){
     		
     		$("#choose_opponent").animate({left:-765},400,function(){
	     		$(this).css({left:765});
	     	});
	     	
			$('#game_friend_searcher').animate({left:0},400);
			
     	}else if(App.gamePage == "mainGameMenu"){
     		
	     	$('#'+App.gameCurrentPageDivID).animate({left:-765},400,function(){
	     		$(this).css({left:765});
	     	});
	     	
	     	$('#game_menu').animate({left:0},400);
	     	
	   }else if(App.gamePage == "gameConclusion"){
	   	
     		$("#active_game").animate({left:-765},400,function(){
	     		$(this).css({left:765});
	     	});
	     	
	     	$('#gameConclusion').animate({left:0},400);
	     	
	     	if (App.activeGamePlayer==1) {
	     		$('.gameConclusionImage').attr('id','youWonImage');
	     		$('.gameConclusionImage').prepend("You Won!");
	     	} else if (App.activeGamePlayer==2) {
	     		$('.gameConclusionImage').attr('id','youLostImage');
	     		$('.gameConclusionImage').prepend("You Lost!");
	     	}
	     	
	     	$('#gameConclusionScore').text(App.playerScore+'-'+App.opponentScore);
	     	
	     	$('#gameContinueButton').click(function(){
	     		
				$("#active_game").animate({left:-765},400,function(){
	     			$(this).css({left:765});
	     		});
	     		$('#game_menu').animate({left:0},400);
	     		App.gameCurrentPageDivID = $(this).parents(".game-view").attr("id");
				App.gamePage = "mainGameMenu";
				App.showGameScreen();
				
			});
			
     	}else if(App.gamePage == "load"){
     		
     		$("#game_menu").animate({left:-765},400,function(){
	     			$(this).css({left:765});
	     		});
	     		
			$('#load_game').animate({left:0},400);
			
			$('#game_list').empty();
			$('#game_list').append(
				'<div id="loadGamesIndicator" class="loadingIndicator"><p>Loading previous games...</p><img alt="Loading" src="_site/loading2.gif" /></div>'
			);
			
			App.callAjax("_app/play.php?load=1",function(lXML){
     			
     			var divList = $("#game_list").get(0);
     			var ulList = App.createDiv(divList,"","","ul");
     			
     			iCount = parseInt(App.getXML(lXML,"count"));
     			for(i=0;i<iCount;i++){
					var liItem = App.createDiv(ulList,"","","li");
					var active = App.getXML(lXML,"game_"+i+"/active");
					var gameID = App.getXML(lXML,"game_"+i+"/game_id");
					var dateStart = App.getXML(lXML,"game_"+i+"/date_start");
					var categoryName = App.getXML(lXML,"game_"+i+"/category");
					var opponentName = App.getXML(lXML,"game_"+i+"/opponent/name");
					var yourScore = App.getXML(lXML,"game_"+i+"/score");
					var theirScore = App.getXML(lXML,"game_"+i+"/opponent/score");
					var drawScore = App.getXML(lXML,"game_"+i+"/draw");
					
					$(liItem).html(dateStart+" - "+categoryName+" VS "+opponentName+" ("+yourScore+"-"+drawScore+"-"+theirScore+")");
					if (active == 1) {
						App.createDiv(liItem,"activeDot");
					}
					$(liItem).click({g:gameID},function(event){
				    	App.gameID = event.data.g;
					    App.gamePage = "gamingCards";
				    	App.showGameScreen();
				   });	
				}
				$('#loadGamesIndicator').empty();
				$('.saved-game-list').jScrollPane();
			});
     	}
     	
     };
     
     APP_Main.prototype.auctionSave = function(cardID,targetMod){
     	
     		var price = $("#price").attr('val');
     		var minimumBid = $("#minimum_bid").attr('val');
     	
			if(price == 'No buyout'){
				var cost = parseInt(parseInt(minimumBid) * 0.1,10);
			}else{
				var cost = parseInt(parseInt(price) * 0.1,10);
			}
			cost = (cost < 5) ? 5 : cost;
	
	
			var minimum_bid = parseInt(minimumBid);
			var price = price
			if(price == 'No buyout'){
				price = '0';
			}else{
				price = parseInt(price,10);
			}
			var date_expired = $("#date_expired").val();
				
			App.cardTarget = cardID-1;
			App.callAjax("_app/auction.php?create=1"
				+"&card_id="+cardID
				+"&minimum_bid="+minimum_bid
				+"&price="+price
				+"&date_expired="+date_expired
			,
			function(xml){
				var result = App.getXML(xml,"result");
				if(result=='success') {
					$('#auction-modal-window').fadeTo("fast",1);
					$("#auction-modal-window").remove();
					$(".modal-picture-holder").remove();
					//App.showNotice("Your auction has been created.",1,true);
					var cardCount = parseInt(App.getXML(xml,"count"));
					if(cardCount > 1){
						$("#count_"+App.cardTarget).html(cardCount);
					}else if(cardCount == 1){
						$("#count_"+App.cardTarget).replaceWith("<div class=\"albumCardCount\" id=count_"+App.cardTarget+"\"></div>");
					}else{
						$("#count_"+App.cardTarget).replaceWith("<div class=\"albumCardCount\" id=count_"+App.cardTarget+"\"></div>");
						$("#img_"+(App.cardTarget+1)).remove();
						$("#"+(App.cardTarget)+" .album_card_title").css("opacity",0.1);
					}
				 	//App.showCards('all');
					App.updateCreditView(App.getXML(xml,"cost"));
				 	//window.location.reload();
				}
				else
				{
					App.showBuy("Unfortunately you have insufficient credits to create this auction, why not go buy some more on the Credits tab?",0,true);
				}
			});
			
     };
     
     APP_Main.prototype.updateCredits = function(iCredits){
       var currentCredits = $("#uCredits").html();
       if(currentCredits > iCredits){
       	var sColor = "#FF0000";
       }else{
       	var sColor = "#00FF00";
       }
       $("#uCredits").css({color:sColor});
       $("#uCredits").animate({color:"#000"},1500)
       $("#uCredits").html(iCredits);
     };
     
     APP_Main.prototype.showNotice = function(sMessage,iStatus,removeMask){
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		
		if($('#mask').css("display")=="none"){
			$('#mask').css({'width':maskWidth,'height':maskHeight});
			$('#mask').fadeIn('fast');
			$('#mask').fadeTo("medium",0.6);
		}
       
		var divBody = document.body;
		var divErrorWindow = App.createDiv(divBody,"modal-window","notice-modal-window");
		$(divErrorWindow).css({top:180,left:200,height:"120px",width:"400px"});
       	var divCenter = App.createDiv(divErrorWindow,"modal-window-error");
       	var divText = App.createDiv(divCenter,"modal-error-text");
       	if (iStatus==0) {
       		var divIcon = App.createDiv(divCenter,"modal-error-icon");
       		$(divText).html(sMessage);
       	} else if (iStatus==1) {
       		var divIcon = App.createDiv(divCenter,"modal-success-icon");
       		$(divText).html(sMessage);
       	} else if (iStatus==2) {
       		var divIcon = App.createDiv(divCenter,"modal-success-icon");
       		$(divText).html(sMessage);
       	} 
       	
		var divClose = App.createDiv(divErrorWindow,"buttonGrey");
		$(divClose).css({bottom:10,right:10});
		$(divClose).append('Close');
		$(divClose).click(function() {
			$('#notice-modal-window').fadeTo("fast",1);
			$("#notice-modal-window").remove();
			if (removeMask){ $("#mask").hide(); }
		});
        $('.modal-window').fadeIn('fast');
     };
	 
	 APP_Main.prototype.showDid = function(sMessage,iStatus,removeMask){
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		
		if($('#mask').css("display")=="none"){
			$('#mask').css({'width':maskWidth,'height':maskHeight});
			$('#mask').fadeIn('fast');
			$('#mask').fadeTo("medium",0.6);
		}
       
		var divBody = document.body;
		var divErrorWindow = App.createDiv(divBody,"modal-window","notice-modal-window");
		$(divErrorWindow).css({top:180,left:200,height:"120px",width:"400px"});
       	var divCenter = App.createDiv(divErrorWindow,"modal-window-error");
       	var divText = App.createDiv(divCenter,"modal-error-text");
		$(divText).css({top:13});
       	if (iStatus==0) {
       		var divIcon = App.createDiv(divCenter,"modal-error-icon");
       		$(divText).html(sMessage);
       	} else if (iStatus==1) {
       		var divIcon = App.createDiv(divCenter,"modal-success-icon");
       		$(divText).html(sMessage);
       	} else if (iStatus==2) {
       		var divIcon = App.createDiv(divCenter,"modal-success-icon");
       		$(divText).html(sMessage);
       	} 
       	
		var divClose = App.createDiv(divErrorWindow,"buttonGrey");
		$(divClose).css({bottom:10,right:10});
		$(divClose).append('Ok');
		$(divClose).click(function() {
			$('#notice-modal-window').fadeTo("fast",1);
			$("#notice-modal-window").remove();
			if (removeMask){ $("#mask").hide(); }
		});
		var divBuy = App.createDiv(divErrorWindow,"buttonBuy","","a");
		$(divBuy).css({bottom:10,right:70});
		$(divBuy).append('Credits');
		$(divBuy).attr('href', "index.php?page=credits");
		/*$(divClose).click(function() {
			$('#notice-modal-window').fadeTo("fast",1);
			$("#notice-modal-window").remove();
			if (removeMask){ $("#mask").hide(); }
		});*/
        $('.modal-window').fadeIn('fast');
     };
	 
	 APP_Main.prototype.showBuy = function(sMessage,iStatus,removeMask){
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		
		if($('#mask').css("display")=="none"){
			$('#mask').css({'width':maskWidth,'height':maskHeight});
			$('#mask').fadeIn('fast');
			$('#mask').fadeTo("medium",0.6);
		}
       
		var divBody = document.body;
		var divErrorWindow = App.createDiv(divBody,"modal-window","notice-modal-window");
		$(divErrorWindow).css({top:180,left:200,height:"120px",width:"400px"});
       	var divCenter = App.createDiv(divErrorWindow,"modal-window-error");
       	var divText = App.createDiv(divCenter,"modal-error-text");
       	if (iStatus==0) {
       		var divIcon = App.createDiv(divCenter,"modal-error-icon");
       		$(divText).html(sMessage);
       	} else if (iStatus==1) {
       		var divIcon = App.createDiv(divCenter,"modal-success-icon");
       		$(divText).html(sMessage);
       	} else if (iStatus==2) {
       		var divIcon = App.createDiv(divCenter,"modal-success-icon");
       		$(divText).html(sMessage);
       	} 
       	
		var divClose = App.createDiv(divErrorWindow,"buttonBuy","","a");
		$(divClose).css({bottom:10,right:10});
		$(divClose).append('Buy');
		$(divClose).attr('href', "index.php?page=credits");
		/*$(divClose).click(function() {
			$('#notice-modal-window').fadeTo("fast",1);
			$("#notice-modal-window").remove();
			if (removeMask){ $("#mask").hide(); }
		});*/
        $('.modal-window').fadeIn('fast');
     };
     
     APP_Main.prototype.createInput=function(divParent,iLeft,iTop,iWidth,iSize,sDesc,sID){
		var divInput=App.createDiv(divParent,"inputdiv");
		divInput.style.left=iLeft+"px";
		divInput.style.top=iTop+"px";
		divInput.style.width=iWidth+"px";
		var divDesc=App.createDiv(divInput,"inputdesc");
		divDesc.innerHTML=sDesc;
		var divBox=App.createDiv(divInput,"inputbox");
		var divInputBox=App.createDiv(divBox,"text",sID,"input");
		divInputBox.size=iSize;
		return divInputBox;
	  };
     
     //HERE ------------------
     APP_Main.prototype.showAuctionWin = function(sXML){
		   var divBody=document.getElementsByTagName("body")[0];
			var divWindow = App.createDiv(divBody,"modal-window","albumAuctionModalWindow");
			
			var divCloseButtonContainer = App.createDiv(divWindow,"closeButtonContainer");
			var divClose = App.createDiv(divCloseButtonContainer,"close-button");
			$(divClose).html("<span>Close</span>");
			$(divClose).click(function() {
		        window.flipped = false;
		        $('.modal-window').fadeTo("fast",1);
		        $(".modal-window").remove();
		        $(".modal-picture-holder").remove();
		        $("#mask").hide();
		    });
			
			var maskHeight = $(document).height();
			var maskWidth = $(window).width();
			$('#mask').css({'width':maskWidth,'height':maskHeight});
			$('#mask').fadeIn('fast');
			$('#mask').fadeTo("medium",0.6);
	
			iTop = 10;
			iLeft = 10;
		
		//Card thumbnail
		var divCardImage = App.createDiv(divWindow,"","albumAuctionCardImage","img");
		
		var path = App.getXML(sXML,"path");
		var image = App.getXML(sXML,"image");
		
		$(divCardImage).attr({
			height:240,
			width:175,
			src:path+"cards/jpeg/"+image+"_front.jpg"
			});
		iLeft=83;
		
		iTop+=25;
		
		//Minimum bid amount
		var minBid = App.getXML(sXML,"value");
		var divMinBidInputDesc=App.createDiv(divWindow,"inputdesc","minBidLabel");
		var divInput=App.createDiv(divWindow,"","minimum_bid");
		$(divInput).attr('val',minBid);
		$(divMinBidInputDesc).append("<span>Min Bid</span> Amount");
		$("#minimum_bid").append(parseInt(minBid));
		minBidSpan = App.createDiv(divInput,"","","span");
		$(minBidSpan).text(" TCG");
		
		$("#minimum_bid").focus(function(){
			$(this).blur();
		});
		iTop+=25;
		var div = App.createDiv(divWindow,"counter-arrow-box ","slideMin");
		
		$("#slideMin").slider({
			range: "min",
			min: 1,
			max: 250,
			value: minBid,
			animate: true,
			slide: function(event, ui){
				$("#minimum_bid").html(ui.value+' <span>TCG</span>');
				$("#minimum_bid").attr('val',ui.value);
				App.calcAuctionCost($("#minimum_bid").attr('val'),$("#price").attr('val'));
				if($("#slidePrice").slider("value") != 0 && $("#slidePrice").slider("value") < (ui.value*2)){
					$("#slidePrice").slider("value",ui.value*2);
				}
			}
		});
		
		iTop+=30;
		
		//Buyout price
		var divPriceInputDesc=App.createDiv(divWindow,"inputdesc","priceLabel");
		var divInput=App.createDiv(divWindow,"","price");
		$(divInput).attr('val',minBid);
		$(divPriceInputDesc).append("<span>Buyout</span> Price");
		var buyout = minBid*2;
		$("#price").append(parseInt(buyout));
		
		priceSpan = App.createDiv(divInput,"","","span");
		$(priceSpan).text(" TCG");
		
		$("#price").focus(function(){
			$(this).blur();
		});
		iTop+=15;
		var div = App.createDiv(divWindow,"counter-arrow-box ","slidePrice");
		
		$("#slidePrice").slider({
			range: "min",
			min: 0,
			max: 500,
			step: 10,
			animate: true,
			value: buyout,
			slide: function(event, ui){
				if(ui.value > 0){
					if($("#slideMin").slider("value") > (ui.value/2)){
						var newval = ui.value/2;
						$("#slideMin").slider("value",newval);
						$("#minimum_bid").html(newval+' <span>TCG</span>');
						$("#minimum_bid").attr('val',newval);
						App.calcAuctionCost($("#minimum_bid").attr('val'),$("#price").attr('val'));
					}
				}
				slidePriceChange(ui.value); 
			},
			change: function(event, ui){ slidePriceChange(ui.value); }
		});
		$("#slidePrice").find(".ui-slider-range").css({
			
		})
		iTop+=40;
		
		function slidePriceChange(val){
			var tcg = 'No buyout';
			if(val > 0){
				tcg = val+' <span>TCG</span>';
			}
			$("#price").html(tcg);
			$("#price").attr('val',val);
			App.calcAuctionCost($("#minimum_bid").attr('val'),$("#price").attr('val'));
		}
		
		//Auction expiry date
		var divInput=App.createInput(divWindow,iLeft,iTop,200,16,"<span>Expiry</span> Date","date_expired");
		$("#date_expired").css({
			background:"transparent",
			border:"0px none",
			color:"white",
			fontSize:13,
			fontWeight:"400"
		});
		$("#date_expired").attr('readonly',true);
		iTop+=35;
		var divDate = App.createDiv(divWindow,"","expirydate");
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
		
		var div = App.createDiv(divWindow,"albumAuctionCostNotice");
		
		var div = App.createDiv(divWindow,"albumAuctionButton");
		$(div)
			.html('Auction')
			.click(function(event){
				App.auctionSave(App.getXML(sXML,"cardid"));
			})
			.click(function() {
	        window.flipped = false;
	        $('.modal-window').fadeTo("fast",1);
	        $(".modal-window").remove();
	        $(".modal-picture-holder").remove();
	        $("#mask").hide();
	    	});
		
		//SHOW MODAL WINDOW
		App.calcAuctionCost($("#minimum_bid").attr('val'),$("#price").attr('val'));
		$('.modal-window').fadeIn('fast');
};
     
     
     APP_Main.prototype.updateCredits = function(iCredits){
       var currentCredits = $("#uCredits").html();
       if(currentCredits > iCredits){
       	var sColor = "#FF0000";
       }else{
       	var sColor = "#00FF00";
       }
       $("#uCredits").css({color:sColor});
       $("#uCredits").animate({color:"#000"},1500)
       $("#uCredits").html(iCredits);
     };
     
     APP_Main.prototype.loadBids = function(sXML){
     	$(".bid-list").html("");
     	var divBidList = $('.bid-list').get(0);
		var divBidHeading = App.createDiv(divBidList,"bid-list-heading");
		var ul = App.createDiv(divBidList,"","","ul");
		bidCount = App.getXML(sXML,"bid_count")
		if(bidCount > 0){
			$(divBidHeading).html("Bidding History");
			for(q=0;q<bidCount;q++){
				var li = App.createDiv(ul,"bid-list-item","","li");
				var span = App.createDiv(li,"bid-date","","span");
				$(span).html(App.getXML(sXML,"bid_"+q+"/date"));
				var span = App.createDiv(li,"bid-name","","span");
				$(span).html(App.getXML(sXML,"bid_"+q+"/user"));
				var span = App.createDiv(li,"bid-value","","span");
				$(span).html(App.getXML(sXML,"bid_"+q+"/amount"));
			}
		}else{
			$(divBidHeading).html("No Bids Yet");
		}
     };
	 
	 APP_Main.prototype.updateCreditView=function(iValue) {
		var span_credit = document.getElementById('creditAvailable').innerHTML;
		var iCredits = parseInt(span_credit);
		var iVal = parseInt(iValue);
		var iDiff = iCredits - iVal;
		$("#creditAvailable").empty();
		$("#creditAvailable").append(iDiff);
	};
	
     APP_Main.prototype.bidORbuy=function(sType,iID,iValue){
		if(sType=="bid"){
			App.callAjax("_app/auction.php?bid="+iID+"&val="+iValue,function(sXML){
				var success = parseInt(App.getXML(sXML,"value"));
				var message = App.getXML(sXML,"message");
				if(success==0){
					App.showNotice(message,0,true);
				}else{
					App.loadBids(sXML);
					App.updateCreditView(iValue);
				}
				
       		});
		}else if(sType=="buy"){
			App.callAjax("_app/auction.php?buy="+iID,function(sXML){
				var success = parseInt(App.getXML(sXML,"value"));
				if(success==1){
					App.showNotice("You have purchased this card successfully",1,true);
					$("#auction-modal-window").remove();
					$("#win_"+App.getXML(sXML,"id")).remove();
				}else{
					App.showBuy("Unfortunately you have insufficient credits, why not go buy some more on the Credits tab?",0,true);
				}
			});
		}
	};
     
     APP_Main.prototype.getDoubleDigits=function(number){
		if(parseInt(number,10) < 10){
			return '0'+number;
		}
		else{
			return number;
		}
	};
     
   		APP_Main.prototype.getTimeLeft=function(enddate,minute)
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
				var h = App.getDoubleDigits(parseInt(msleft/diffh,10));
				msleft = msleft - (h*diffh);
				var m = App.getDoubleDigits(parseInt(msleft/diffm,10));
				msleft = msleft - (m*diffm);
				var s = App.getDoubleDigits(parseInt(msleft/diffs,10));
				timeleftdisplay = '';
				if(d > 0){
					timeleftdisplay = d+'d:';
				}
				if(typeof(minute) == "undefined"){
					timeleftdisplay+= h+'h:'+m+'m:'+s+'s';
				}
				else{
					timeleftdisplay+= h+':'+m;
				}
			}
			
			return timeleftdisplay;
			
		};
     
     APP_Main.prototype.showCards = function(catID){
    	App.callAjax("_app/album.php?cat="+catID,function(sXML){
         $("#album_scroll_pane").remove();
         // $('#cardBigView .auctionIcon, #cardBigView .eyeIcon').attr('id','').unbind();
         // $('#cardBigView img').fadeOut('medium',function(){
         	// $(this).remove();
         // });
         //$('#cardBigView .eyeIcon, #cardBigView .auctionIcon').removeClass('enabled');
         var divBody = $('#albumPlate').get(0);
			var divMain = App.createDiv(divBody,null,"album_scroll_pane");
      
         var iCount = parseInt(App.getXML(sXML,"count"));
      	for(var i=0; i<iCount; i++){
      		var cardID = App.getXML(sXML,"card_"+i+"/cardid")
	         var cardCount = parseInt(App.getXML(sXML,"card_"+i+"/cardcount"));
	         var cardImgPath = App.getXML(sXML,"card_"+i+"/path")+"cards/"+App.getXML(sXML,"card_"+i+"/image")+"_web.jpg";
	         var divCardblock = App.createDiv(divMain,"cardBlock");
	         $(divCardblock).attr("id",i);
	         var divCardblockCount = App.createDiv(divCardblock,"albumCardCount");
	         if(cardCount > 1){
	           // var divCardblockCount = App.createDiv(divCardblock,"albumCardCount");
	           $(divCardblockCount).addClass("active");
	           $(divCardblockCount).attr("id","count_"+i);
	           $(divCardblockCount).html(cardCount);
	         }
	         var divCardDropShadow = App.createDiv(divCardblock,"album-card-drop-shadow");
	         var divCardblockPic = App.createDiv(divCardblock,"album_card_pic");
	         if(cardCount > 0){
	           var imgCard = App.createDiv(divCardblockPic,"","","img");
	           $(imgCard).attr("id","img_"+cardID);
	           imgCard.src = cardImgPath;
	         }
	         var divCardblockContainer = App.createDiv(divCardblock,"album-card-pic-container");
	         divCardblockContainer.id = App.getXML(sXML,"card_"+i+"/cardid");
	         $(divCardblockContainer).css('background-image', 'url(' + cardImgPath + ')');
	         
	         var divCardblockTitle = App.createDiv(divCardblock,"album_card_title");
	         $(divCardblockTitle).html(App.getXML(sXML,"card_"+i+"/cardname"));
         }
         $('.menu-scroll-button').appendTo('#albumPlate');
       	$('#album_scroll_pane').jScrollPane();
			$('.album-card-pic-container').click(function(){
				var cardID = $(this).attr('id');
				App.selectAlbumCard(cardID);
			});
			App.assignScrollAction();
      });
     };
     
     APP_Main.prototype.loadLeaderboard=function(listID){
     		App.callAjax("_app/leaderboard.php?list="+listID,function(lXML){
     			var username;
     			var userImageURL;
     			var userTrophyAmount;
     		});
     };
     
     APP_Main.prototype.buyItem=function(packID){
      App.callAjax("_app/shop.php?buy="+packID,function(xml){
      
        //SHOW RECEIVED CARDS
	     var sXML = xml;
	     
	     if (parseInt(App.getXML(sXML,"value"))!=1) {
	     	
	     	App.showBuy("Unfortunately you have insufficient credits, why not go buy some more on the Credits tab?",0,true);
	     	return;
	     	
	     }
		 
		 App.updateCreditView(App.getXML(sXML,"cost"));
        
       	var divBody = document.body;
			var divPurchasedWindow = App.createDiv(divBody,"modal-window","booster-purchased-window");
		
			var divCloseButtonContainer = App.createDiv(divPurchasedWindow,"closeButtonContainer");
		  	var divClose = App.createDiv(divCloseButtonContainer,"close-button");
			$(divClose).html("<span>Close</span>");
		  $(divClose).click(function() {
		        $('.modal-window').fadeTo("fast",1);
		        $(".modal-window").remove();
		        $(".modal-picture-holder").remove();
		        $("#mask").hide();
		  });
		
			var divHeading = App.createDiv(divPurchasedWindow,"booster-purchase-heading");
			$(divHeading).html("<span>Purchase Successful</span><br>You have received the following cards...");
        var divFullSize = App.createDiv(divPurchasedWindow,"game-card-logo-container");
        App.setSelectNone(divFullSize);
        var divReceivedWindow = App.createDiv(divPurchasedWindow,"scroll_pane","booster-possibles-window");
        $(divReceivedWindow).css("width",320);
        var iCount = App.getXML(sXML,"count");
        
        for (i = 0; i < iCount; i++) {
        	
            var iQty = parseInt(App.getXML(sXML,"cards/card_"+i+"/qty"));
            
            for (z = 0; z < iQty; z++) {
            	
	        		var divPlace = App.createDiv(divReceivedWindow,"booster-list-placeholder");
	        		$(divPlace).css("paddingRight",10)
		        	var divPicList = App.createDiv(divPlace,"booster-list-pic");
		        	var divImg = App.createDiv(divPicList,"","","img");
					$(divImg).css("cursor","pointer");
		        	var sSource = App.getXML(sXML,"cards/card_"+i+"/path")+'cards/'+App.getXML(sXML,"cards/card_"+i+"/img")+'_web.jpg';
		        	divImg.src = sSource;
		        	
		        	$(divImg).click({a:sSource},function(event){
			        	var display = event.data.a.replace("web","front");
			        	$(".game-card-logo-container").html("");
						$(".game-card-logo-container").css({"background":"url("+display+")"});
				   });
	        	
	        	    var divPicCaption = App.createDiv(divPicList,"booster-list-pic-caption");
	        	    $(divPicCaption).css("bottom",0);
	        	    $(divPicCaption).html(App.getXML(sXML,"cards/card_"+i+"/description"));
            }
        }
        
        $("#booster-possibles-window").jScrollPane();
        $(divFullSize).click(function(){
        	var sSide = $(divFullSize).css("background-image");
	        sSide = (sSide.indexOf("front") > 0) ? sSide.replace("front","back") : sSide.replace("back","front");
        	$(divFullSize).css({"background":sSide});
        	sSide = sSide.replace("url(\"","");
        	sSide = sSide.replace("\")","");
			$(divFullSize).flip({
				speed:150,
				direction:'lr',
				content:""//<img src='"+sSide+"'/>"
			});
				
			
		});
        
        
        //Get the screen height and width
	     var maskHeight = $(document).height();
	     var maskWidth = $(window).width();
	     
	     //Set height and width of mask to fill up the whole screen
	     $('#mask').css({'width':maskWidth,'height':maskHeight});
	      
	     //transition effect - show the mask  
	     $('#mask').fadeIn('fast');   
	     $('#mask').fadeTo("medium",0.6); 
        
        $('#booster-purchased-window').css({top:'120px',left:'50px',zIndex:2});
		  $('#booster-purchased-window').show('300');
        
        App.updateCredits(App.getXML(sXML,"credits"));
        
      });
    
     }
	 
	 APP_Main.prototype.getItem=function(packID){
      App.callAjax("_app/shop.php?free="+packID,function(xml){
      
	  
	  
        //SHOW RECEIVED CARDS
	     var sXML = xml;
		 
		 if (parseInt(App.getXML(sXML,"value"))!=1) {
	     	
	     	return;
	     	
	     }
        
       	var divBody = document.body;
			var divPurchasedWindow = App.createDiv(divBody,"modal-window","booster-purchased-window");
		
			var divCloseButtonContainer = App.createDiv(divPurchasedWindow,"closeButtonContainer");
		  	var divClose = App.createDiv(divCloseButtonContainer,"close-button");
			$(divClose).html("<span>Close</span>");
		  $(divClose).click(function() {
		        $('.modal-window').fadeTo("fast",1);
		        $(".modal-window").remove();
		        $(".modal-picture-holder").remove();
		        $("#mask").hide();
		  });
		
			var divHeading = App.createDiv(divPurchasedWindow,"booster-purchase-heading");
			if (packID == 2) {
				$(divHeading).html("<span>Welcome and Thank You for registering.</span><br>To say thank you, we have given you the following cards...");
			} else if (packID == 1) {
				$(divHeading).html("<span>Thank you for completing your profile.</span><br>As a reward you get the following card...");
			}
        var divFullSize = App.createDiv(divPurchasedWindow,"game-card-logo-container");
        App.setSelectNone(divFullSize);
        var divReceivedWindow = App.createDiv(divPurchasedWindow,"scroll_pane","booster-possibles-window");
        $(divReceivedWindow).css("width",320);
        var iCount = App.getXML(sXML,"count");
        
        for (i = 0; i < iCount; i++) {
        	
            var iQty = parseInt(App.getXML(sXML,"cards/card_"+i+"/qty"));
            
            for (z = 0; z < iQty; z++) {
            	
	        		var divPlace = App.createDiv(divReceivedWindow,"booster-list-placeholder");
	        		$(divPlace).css("paddingRight",10)
		        	var divPicList = App.createDiv(divPlace,"booster-list-pic");
		        	var divImg = App.createDiv(divPicList,"","","img");
					$(divImg).css("cursor","pointer");
		        	var sSource = App.getXML(sXML,"cards/card_"+i+"/path")+'cards/'+App.getXML(sXML,"cards/card_"+i+"/img")+'_web.jpg';
		        	divImg.src = sSource;
		        	
		        	$(divImg).click({a:sSource},function(event){
			        	var display = event.data.a.replace("web","front");
			        	$(".game-card-logo-container").html("");
						$(".game-card-logo-container").css({"background":"url("+display+")"});
				   });
	        	
	        	    var divPicCaption = App.createDiv(divPicList,"booster-list-pic-caption");
	        	    $(divPicCaption).css("bottom",0);
	        	    $(divPicCaption).html(App.getXML(sXML,"cards/card_"+i+"/description"));
            }
        }
        
        $("#booster-possibles-window").jScrollPane();
        $(divFullSize).click(function(){
        	var sSide = $(divFullSize).css("background-image");
	        sSide = (sSide.indexOf("front") > 0) ? sSide.replace("front","back") : sSide.replace("back","front");
        	$(divFullSize).css({"background":sSide});
        	sSide = sSide.replace("url(\"","");
        	sSide = sSide.replace("\")","");
			$(divFullSize).flip({
				speed:150,
				direction:'lr',
				content:""//<img src='"+sSide+"'/>"
			});
				
			
		});
        
        
        //Get the screen height and width
	     var maskHeight = $(document).height();
	     var maskWidth = $(window).width();
	     
	     //Set height and width of mask to fill up the whole screen
	     $('#mask').css({'width':maskWidth,'height':maskHeight});
	      
	     //transition effect - show the mask  
	     $('#mask').fadeIn('fast');   
	     $('#mask').fadeTo("medium",0.6); 
        
        $('#booster-purchased-window').css({top:'120px',left:'50px',zIndex:2});
		  $('#booster-purchased-window').show('300');
        
		var collected = $('#collected').html();
		var iCol = parseInt(collected);
		iCol = iCol + 3;
		$('#collected').html(iCol);
        //App.updateCredits(App.getXML(sXML,"credits"));
        
      });
    
     }
     
     APP_Main.prototype.showCardModal=function(imgID){
        
			// var imgID = $('#cardBigView img').attr('id');
			App.cardTarget = imgID;
			var divBody = document.body;
			var divModalPic = App.createDiv(divBody,"modal-picture-holder modal-window");
			var divAuctionText = App.createDiv(divModalPic,"albumViewAuctionText");
			$('.albumViewAuctionText').click(function(){
				$('.modal-window').fadeTo("fast",1);
				$(".modal-window").remove();
				$(".modal-picture-holder").remove();
		        $("#mask").hide();
				App.callAjax("_app/auction.php?load="+App.cardTarget,function(sXML){
					App.showAuctionWin(sXML);
					
				});
			});
			$(divAuctionText).html("Auction");
			var divCloseButtonContainer = App.createDiv(divModalPic,"closeButtonContainer");
			var divClose = App.createDiv(divCloseButtonContainer,"close-button");
			$(divClose).html("<span>CLOSE</span>");
			
			$(divClose).click(function() {
		        window.flipped = false;
		        $('.modal-window').fadeTo("fast",1);
		        $(".modal-window").remove();
		        $(".modal-picture-holder").remove();
		        $("#mask").hide();
		    });
			
			var auctionBtn = App.createDiv(divModalPic,"auctionIcon");
			$('.auctionIcon').click(function(){
				$('.modal-window').fadeTo("fast",1);
				$(".modal-window").remove();
				$(".modal-picture-holder").remove();
		        $("#mask").hide();
				App.callAjax("_app/auction.php?load="+App.cardTarget,function(sXML){
					App.showAuctionWin(sXML);
					
				});
			});
			
			var divFullPic = App.createDiv(divModalPic,"modal-full-picture");
			var imgPic = App.createDiv(divFullPic,"","","img");
			imgPic.src = "https://sarugbycards.com/img/cards/jpeg/"+imgID+"_front.jpg";
			
			
		   //Get the screen height and width
	    	var maskHeight = $(document).height();
	    	var maskWidth = $(window).width();
	    	
	      //Set height and width of mask to fill up the whole screen
	    	$('#mask').css({'width':maskWidth,'height':maskHeight});
      
     		//transition effect - show the mask  
     		$('#mask').fadeIn('fast');   
     		$('#mask').fadeTo("medium",0.6); 
    
			$('.modal-picture-holder').css({top:'200px',left:'215px'});
			$('.modal-picture-holder').show('300');
		
			$('.modal-full-picture').click(function() {
					if (window.flipped!=true){
						$(this).flip({
						speed:200,
						direction:'lr',
						content:"<img src='https://sarugbycards.com/img/cards/jpeg/"+imgID+"_back.jpg'/>"
						});
						window.flipped = true;
					} else {
						$(this).flip({
						speed:200,
						direction:'lr',
						content:"<img src='https://sarugbycards.com/img/cards/jpeg/"+imgID+"_front.jpg'/>"
						});
						window.flipped = false;
					}
			});
     }
     
		APP_Main.prototype.assignScrollAction=function(){
			var scrollPane = $('#auction_scroll_pane, #album_scroll_pane').jScrollPane();
			// shopScrollPane
			if (typeof scrollPane != "undefined") {
				var scrollPaneAPI = scrollPane.data('jsp');
				$('#menu-right-button').click(function(){
						scrollPaneAPI.scrollByY(370,1);
					}
				);
				$('#menu-left-button').click(function(){
						scrollPaneAPI.scrollByY(-370,1);
					}
				);
			}
		};
     
     APP_Main.prototype.selectAlbumCard=function(cardID){
        if(document.getElementById("img_"+cardID) !=null){
			  	var cardID = cardID;
				var imgHeight = 240;
				var img = '<img id='+cardID+' alt="selected card" height='+imgHeight+' src="https://sarugbycards.com/img/cards/jpeg/'+cardID+'_front.jpg"/>';
				
				// function removeBigCard(){
					// $('#cardBigView img').remove();
					// $('#cardBigView').append(img);
					// $('#cardBigView img').hide();
					// $('#cardBigView img').load(function(){
						// $(this).fadeIn('medium');
					// });
				// }
// 		
				// $('#cardBigView img').fadeOut('slow');
				// setTimeout(removeBigCard,400);
			
				$('.auctionIcon, .eyeIcon').unbind('click');
				$('.auctionIcon').attr('id',cardID);
				//$('#cardBigView .eyeIcon, #cardBigView .auctionIcon').addClass('enabled');
				
				$('.auctionIcon').click(function(){
					imgID = $(this).attr('id');
					App.callAjax("_app/auction.php?load="+imgID,function(sXML){
						App.showAuctionWin(sXML);
					});
				});
				
				// $('.auctionBlockLarge .picture-box').click(function(){
					// var imgID = $('#cardBigView img').attr('id');
					// App.showCardModal(imgID);
				// });
				
				// $('#cardBigView .eyeIcon').click(function(){
					// var imgID = $('#cardBigView img').attr('id');
					// App.showCardModal(imgID);
				// });
        }
     }
     
     APP_Main.prototype.highlightPurchaseItems=function(currentTarget){
		
		$(".selDots").removeClass("selDots-active");
		
		switch(currentTarget.attr('class')) {
			case 'selDots': 
				currentTarget.addClass("selDots-active");
				var iNr = parseInt(currentTarget.attr("id"));
				break;
			case 'selDotsText':
				currentTarget.prev().addClass("selDots-active");
				var iNr = parseInt(currentTarget.prev().attr("id"));
				break;
		}
		App.creditsAmount = iNr;
		}
		
		APP_Main.prototype.assignDragsDrops=function(deckID){
			
			var availableCardCount = parseInt($('.deck-available-heading span').text());
			
			//assign all 'drag and drop' jquery UI event handlers
			
			$('#deck-available .cardBlock').draggable({
				appendTo:'body',
				start: function(){ 
							$(this).css('opacity',0);
							},
				stop: function(){ 
							$(this).css('opacity',1); 
							},
				helper: 'clone',
				revert: "invalid",
				revertDuration: "250",
				zIndex: 3
			});
		
			$('#deck-select').droppable({
				//accept: '#deck-available .cardBlock',
				accept: function(ui){
					var doreturn = false;
					cardTitle = $($(ui.draggable).children(' .album_card_title')).text();
					deckCardsTitles = $.each(
					    $(
					       $('#deck-select').find('.album-card-title')
					    ),
					    function(index,value){
					        if ($(value).text() == cardTitle) {
					            doReturn = true;
					           
					        }
					        return false;
					    }
					)
					if (doReturn==true) {
						return true;
					}
				},
				drop: function(event,ui){
					var result = ($(ui.draggable).appendTo($('#deck-select .cardBlockContainer:empty')[0]));
					if (result.length>0) {
						var cardID = $(ui.draggable).attr('id');
						App.callAjax("_app/deck.php?add=1&cardid="+cardID+"&deckid="+deckID+"");
						availableCardCount -= 1;
						$('.deck-available-heading span').html(availableCardCount);
						$('#deck-select .cardBlock:has(img)').draggable();
					}
				}
			});
		
			$('#deck-select .cardBlock:has(img)').draggable({
				revert: "invalid",
				revertDuration: "250"
			});
	
			$('#deck-available').droppable({
				accept: '#deck-select .cardBlock',
				drop: function(event,ui){
					$(ui.draggable).appendTo($('#deck-available .cardBlockContainer:empty')[0]);
					var cardID = $(ui.draggable).attr('id');
					availableCardCount += 1;
					$('.deck-available-heading span').html(availableCardCount);
					$('#deck-available .cardBlock:has(img)').draggable();
					$(ui.draggable).css({
						'top':'0',
						'left':'0',
						'position':'static'
					});
					App.callAjax("_app/deck.php?remove=1&cardid="+cardID+"&deckid="+deckID+"");
				}
			});
		}
		
		APP_Main.prototype.createDeckModal = function(){
			var divBody = document.body;
			var deckInitModalWindow = App.createDiv(divBody,"modal-window","deckInitModalWindow");
			var divCloseButtonContainer = App.createDiv(deckInitModalWindow,"closeButtonContainer");
			var divClose = App.createDiv(divCloseButtonContainer,"close-button");
			App.createDiv(divCloseButtonContainer,"half","topHalf");
			App.createDiv(divCloseButtonContainer,"half","bottomHalf");
			
			$(divClose).click(function() {
		        $('.modal-window').fadeTo("fast",1);
		        $(".modal-window").remove();
		        $(".modal-picture-holder").remove();
		        $("#mask").hide();
		        App.showDeckSelection();
		    });
		    
		   var newDeckNameLabel = App.createDiv(deckInitModalWindow,"","","label");
		   $(newDeckNameLabel).attr('for','newDeckName');
		   $(newDeckNameLabel).text('Deck Name');
		   var newDeckName = App.createDiv(deckInitModalWindow,"","newDeckName","input");
		   
		   var screenFlowInstruction = App.createDiv(deckInitModalWindow,"screenFlowInstruction");
		   $(screenFlowInstruction).text('Deck Image');
		   
		   var deckCreateScreenFlowContainer = App.createDiv(deckInitModalWindow,"deck-create-screenflow-container");
		   var deckModalLeftButton = App.createDiv(deckCreateScreenFlowContainer,"deck-modal-button","deck-modal-left-button");
		   var deckModalImageContainer = App.createDiv(deckCreateScreenFlowContainer,"deck-create-image-container");
		   	
		   var deckCreateImage1 = App.createDiv(deckModalImageContainer,"deck-create-image","deckImage11",'img');
		   $(deckCreateImage1).attr('src','https://sarugbycards.com/img/decks/11.jpg');
		   var deckCreateImage2 = App.createDiv(deckModalImageContainer,"deck-create-image","deckImage12",'img');
		   $(deckCreateImage2).attr('src','https://sarugbycards.com/img/decks/12.jpg');
		   var deckCreateImage3 = App.createDiv(deckModalImageContainer,"deck-create-image","deckImage13",'img');
		   $(deckCreateImage3).attr('src','https://sarugbycards.com/img/decks/13.jpg');
		   var deckCreateImage4 = App.createDiv(deckModalImageContainer,"deck-create-image","deckImage14",'img');
		   $(deckCreateImage4).attr('src','https://sarugbycards.com/img/decks/14.jpg');
		   $(".deck-create-image").attr('height','200');
		   var deckModalRightButton = App.createDiv(deckCreateScreenFlowContainer,"deck-modal-button","deck-modal-right-button");
			
			
			var createDeckButton = App.createDiv(deckInitModalWindow,"","createDeckButton");
			$(createDeckButton).text('Create Deck');
		   
		    //Get the screen height and width
	    	var maskHeight = $(document).height();
	    	var maskWidth = $(window).width();
	      //Set height and width of mask to fill up the whole screen
	    	$('#mask').css({'width':maskWidth,'height':maskHeight});
      
     		//transition effect - show the mask  
     		$('#mask').fadeIn('fast');   
     		$('#mask').fadeTo("medium",0.6); 
    
			$('#deckInitModalWindow').css({top:'150px',left:'250px'});
			$('#deckInitModalWindow').show('300');
			
			function createDeck() {
				var deckName = $("#newDeckName").val();
				var imageID = App.deckImageNumber;
				if ((deckName.length) > 0) {
					App.callAjax("_app/deck.php?save=1&category=2&description="+deckName+"&image="+imageID+"",function(xml){
						$('.close-button').click();
						var deckID = $(xml).attr('val');
						//App.showDeckModal('edit',deckID+"");
					}); 
				} else {
					//alert user to type a name
					$('#deckInitModalWindow label').css('color','#ED1324');
				}
			}
			
			// make .deck-create-image-container a scroll window
			var deckImageScrollPane = $('.deck-create-image-container').jScrollPane();
			
			App.deckImageScroll(
				".deck-create-image-container img",
				"#deck-modal-right-button",
				"#deck-modal-left-button",
				deckImageScrollPane,
				11,
				'deckImage11'
			);
			
			$('#createDeckButton').click(function(){
				createDeck();
			});
			
		}
		
		APP_Main.prototype.showDeleteDeckModal = function(deckID){
			
			App.callAjax("_app/deck.php?deck_image=1&deck_id="+deckID,function(xml){
				
				var deckDeleteImageSrc = App.getXML(xml,"deck_image_url");
				
				var divBody = document.body;
				var deckDeleteModalWindow = App.createDiv(divBody,"modal-window","deckDeleteModalWindow");
				var divCloseButtonContainer = App.createDiv(deckDeleteModalWindow,"closeButtonContainer");
				var divClose = App.createDiv(divCloseButtonContainer,"close-button");
				
				$(divClose).click(function(){
		        $('.modal-window').fadeTo("fast",1);
		        $(".modal-window").remove();
		        $(".modal-picture-holder").remove();
		        $("#mask").hide();
		        App.showDeckSelection();
			   });
				
				App.createDiv(divCloseButtonContainer,"half","topHalf");
				App.createDiv(divCloseButtonContainer,"half","bottomHalf");
				var deckImage = App.createDiv(deckDeleteModalWindow,"","deckDeleteDeckImage",'img');
				$(deckImage).attr('src',deckDeleteImageSrc);
				var deckDeletePromptMessage = App.createDiv(deckDeleteModalWindow,"","deckDeletePromptMessage",'span');
				$(deckDeletePromptMessage).append('Do you want to delete this deck? (cards will still be available in your collection)');
				var deckDeleteCancelButton = App.createDiv(deckDeleteModalWindow,"","deckDeleteCancelButton");
				$(deckDeleteCancelButton).append('Cancel');
				
				$(deckDeleteCancelButton).click(function(){
		   		$(divClose).click();
			   });
			   
				var deckDeleteConfirmButton = App.createDiv(deckDeleteModalWindow,"","deckDeleteConfirmButton");
				$(deckDeleteConfirmButton).append('Delete Deck');
				
				$(deckDeleteConfirmButton).click(function(){
		   		App.callAjax("_app/deck.php?deck_delete=1&deck_id="+deckID,function(dxml){
		   			if ((App.getXML(dxml,"result"))==0) {
		   				$("#deckDeleteModalWindow > * ").fadeOut(250,function(){
		   					$("#deckDeleteModalWindow").append(App.getXML(dxml,"result_message"));
		   					setTimeout($(divClose).click(),500);
		   				});
		   			} else {
		   				$(divClose).click();
		   			}
		   		});
		   	});
			});
		}
		
		APP_Main.prototype.showDeckSelection=function(){
			
			App.callAjax("_app/deck.php?draw_selection=1",function(xml){
				
				var deckPlate = document.getElementById('deckPlate');
				$(deckPlate).empty();
				var deckTopMenuBar = App.createDiv(deckPlate,"deck-top-menu-bar");
				var createDeckModalButton = App.createDiv(deckTopMenuBar,"","createDeckModalButton");
				$(createDeckModalButton).append("Create A New Deck");
				var deckCount = (App.getXML(xml,"deck_count"))-1;
				
				for (count=0;count<=deckCount;count++) {
					
					var deckID = App.getXML(xml,"deck_"+count+"/deck_id");
					var deckContainer = App.createDiv(deckPlate,"deck-container",deckID);
					var deckTitle = App.createDiv(deckContainer,"deck-title");
					$(deckTitle).append(App.getXML(xml,"deck_"+count+"/description"));
					var deckDeleteButton = App.createDiv(deckContainer,"deckDeleteButton");
					
					var deckIcons = App.createDiv(deckContainer,"deck-icons");
					var editIcon = App.createDiv(deckIcons,"editIcon");
					var deleteIcon = App.createDiv(deckIcons,"deleteIcon");
					
					var deckImageContainer = App.createDiv(deckContainer,"deck-image-container");
					var deckImage = App.createDiv(deckImageContainer,"","","img");
					var imagePath = App.getXML(xml,"deck_"+count+"/image_path");
					
					$(deckImage).attr({
						'height':'140',
						'alt':'deck-image',
						'src':imagePath
					});
				
					var deckAttributes = App.createDiv(deckContainer,"deck-attributes");
					var deckAttributesTable = App.createDiv(deckAttributes,"deck-attributes-table","","table");
					var deckAttributesTbody = App.createDiv(deckAttributesTable,"","","tbody");
					
					var deckAttributesCardsTableRow = App.createDiv(deckAttributesTbody,"","","tr");
					var deckAttributesCardsLabelTableData = App.createDiv(deckAttributesCardsTableRow,"","","td");
					$(deckAttributesCardsLabelTableData).append('Cards: ');
					var deckAttributesCardsTableData = App.createDiv(deckAttributesCardsTableRow,"","deck-cards","td");
					var cardCount = App.getXML(xml,"deck_"+count+"/card_count");
					$(deckAttributesCardsTableData).append(cardCount+"/10");
					
					var deckAttributesCategoryTableRow = App.createDiv(deckAttributesTbody,"","","tr");
					var deckAttributesCategoryLabelTableData = App.createDiv(deckAttributesCategoryTableRow,"","","td");
					$(deckAttributesCategoryLabelTableData).append('Category: ');
					var deckAttributesCategoryTableData = App.createDiv(deckAttributesCategoryTableRow,"","","td");
					$(deckAttributesCategoryTableData).append(App.getXML(xml,"deck_"+count+"/deck_category"));
					
					var deckAttributesRankingTableRow = App.createDiv(deckAttributesTbody,"","","tr");
					var deckAttributesRankingLabelTableData = App.createDiv(deckAttributesRankingTableRow,"","","td");
					$(deckAttributesRankingLabelTableData).append('Ranking: ');
					var deckAttributesRankingTableData = App.createDiv(deckAttributesRankingTableRow,"","","td");
					
					var deckAttributesValueTableRow = App.createDiv(deckAttributesTbody,"","","tr");
					var deckAttributesValueLabelTableData = App.createDiv(deckAttributesValueTableRow,"","","td");
					$(deckAttributesValueLabelTableData).append('Value: ');
					var deckAttributesValueTableData = App.createDiv(deckAttributesValueTableRow,"","deck-value","td");
					$(deckAttributesValueTableData).append('0 TCG');
					
					App.getXML(xml,"decks/deck"+count+"");
					
					$(deckContainer).click(function(e){
						if (($(e.target).attr('class')) != 'deckDeleteButton') {
							$('#mask').fadeIn(500);
							$('#deck-create-modal-window').show();
							App.showDeckModal('edit',($(this).attr('id')));	
						} else {
							$('#mask').fadeIn(500);
							App.showDeleteDeckModal($(this).attr('id'));
						}
					});
						
				}
				
				$('#createDeckModalButton').click(function(){
					// App.showDeckModal('create');
					App.createDeckModal();
				});
				
			});
		}
		
		APP_Main.prototype.deckImageScroll = function(containerElem,rightElem,leftElem,scrollPaneInstance,imageNumber,currentDeckID) {
			
			var deckImageScrollPaneAPI = scrollPaneInstance.data('jsp');
			var deckImageScrollRange = [];
			this.deckImageNumber = parseInt(imageNumber);
			
			//add each deck image ID value to an array
			$(containerElem).each(function(index){
				deckImageID = $(this).attr('id');
				deckImageScrollRange[index] = deckImageID
			});
			
			deckImageScrollPaneAPI.scrollToElement('#'+currentDeckID+'');
			var scrollIndex = $.inArray(currentDeckID, deckImageScrollRange);
			
			$(rightElem).click(function(){
				if (typeof(deckImageScrollRange[scrollIndex+1]) != "undefined") {
					scrollIndex = (scrollIndex+1);
					deckImageScrollPaneAPI.scrollToElement('#'+deckImageScrollRange[scrollIndex],true,true);
					App.deckImageNumber++;
				}
			});
			
			$(leftElem).click(function(){
				if (typeof(deckImageScrollRange[scrollIndex-1]) != "undefined") {
					scrollIndex = (scrollIndex-1);
					deckImageScrollPaneAPI.scrollToElement('#'+deckImageScrollRange[scrollIndex],true,true);
					App.deckImageNumber--;
				}
			});
			
		}
	
		APP_Main.prototype.showDeckModal = function(task,deckID){
			$('.divContainer').append('<div id="deck-create-modal-window" class="modal-window"></div>');
			var deckCreateModalWindow = document.getElementById('deck-create-modal-window');	
			
			var closeButtonContainer = App.createDiv(deckCreateModalWindow,"closeButtonContainer");
				var closeButton = App.createDiv(closeButtonContainer,"close-button");
				var topHalf = App.createDiv(closeButtonContainer,"half","topHalf");
				var bottomHalf = App.createDiv(closeButtonContainer,"half","bottomHalf");
			
			var deckCreateForm = App.createDiv(deckCreateModalWindow,"","","form");
				var nameDeckLabel = App.createDiv(deckCreateForm,"","name-deck-label","label");
				$(nameDeckLabel).attr('for','name-deck');
				$(nameDeckLabel).append('Deck Name');
				//var nameDeck = App.createDiv(deckCreateForm,"","","input");
				//$(nameDeck).attr({'type':'text','size':30});
				$(deckCreateForm).append('<input id="name-deck" type="text" size="30" value="Hello">');
				var deckImageLabel = App.createDiv(deckCreateForm,"deck-image-label","");
			
				var deckCreateScreenflowContainer = App.createDiv(deckCreateForm,"deck-create-screenflow-container","");
					var deckModalLeftButton = App.createDiv(deckCreateScreenflowContainer,"deck-modal-button","deck-modal-left-button");
					var deckCreateImageContainer = App.createDiv(deckCreateScreenflowContainer,"deck-create-image-container");
						var deckCreateImage11 = App.createDiv(deckCreateImageContainer,"deck-create-image","deckImage11","img");
						$(deckCreateImage11).attr({'height':'200','src':'https://sarugbycards.com/img/decks/11.jpg'});
						var deckCreateImage12 = App.createDiv(deckCreateImageContainer,"deck-create-image","deckImage12","img");
						$(deckCreateImage12).attr({'height':'200','src':'https://sarugbycards.com/img/decks/12.jpg'});
						var deckCreateImage13 = App.createDiv(deckCreateImageContainer,"deck-create-image","deckImage13","img");
						$(deckCreateImage13).attr({'height':'200','src':'https://sarugbycards.com/img/decks/13.jpg'});
						var deckCreateImage14 = App.createDiv(deckCreateImageContainer,"deck-create-image","deckImage14","img");
						$(deckCreateImage14).attr({'height':'200','src':'https://sarugbycards.com/img/decks/14.jpg'});
					var deckModalRightButton = App.createDiv(deckCreateScreenflowContainer,"deck-modal-button","deck-modal-right-button");
			
			var deckModalButtonContainer = App.createDiv(deckCreateForm,"deck-modal-button-container","");
				var createSaveDeckButton = App.createDiv(deckModalButtonContainer,"cmdButton","createSaveDeckButton");
			
			var deckSelect = App.createDiv(deckCreateModalWindow,"","deck-select");
			var deckAvailableHeading = App.createDiv(deckCreateModalWindow,"deck-available-heading","");
			var deckAvailable = App.createDiv(deckCreateModalWindow,"","deck-available");
			
			$('#deck-create-modal-window').show();
			//var  = App.createDiv(,"",);
			
			//$('#deck-select, #deck-available').empty();
			//$('#name-deck').attr('value','');
			
			App.deckID = deckID;
			
			App.callAjax("_app/deck.php?available_cards=1",function(xml){
				
				var availableCardCount =  App.getXML(xml,"count");
				
				var deckAvailableDiv = document.getElementById('deck-available');
				$('.deck-available-heading').html('Available Cards: ');
				$('.deck-available-heading').append('<span>'+availableCardCount+'</span>');
				
				for (count = 0;count < availableCardCount; count++) {
					var imagePath = App.getXML(xml,"card_"+count+"/imagepath");
					var cardDesc = App.getXML(xml,"card_"+count+"/carddescription");
					var imageID = App.getXML(xml,"card_"+count+"/imageid");
					var usercardID = App.getXML(xml,"card_"+count+"/usercardid");
					
					var cardBlockContainer = App.createDiv(deckAvailableDiv,"cardBlockContainer");
					var cardBlock = App.createDiv(cardBlockContainer,"cardBlock",usercardID);
					var albumCardPicContainer = App.createDiv(cardBlock,"album_card_pic");
					var albumCardPic = App.createDiv(albumCardPicContainer,"","","img");
					$(albumCardPic).attr('src',""+imagePath+"cards/"+imageID+"_web.jpg");
					var albumCardTitle = App.createDiv(cardBlock,"album_card_title");
					$(albumCardTitle).html(cardDesc);
				}
				
				$('.close-button').click(function(){
					$(".modal-window").fadeTo("fast", 1);
 					$(".modal-window").remove();
 					$("#mask").hide();
					App.showDeckSelection();
				});
				
				if (task == 'edit') {
					
					$('#createSaveDeckButton').html('Save Deck');
				
					App.callAjax("_app/deck.php?deck_id="+deckID,function(xml){
						
						//get this deck's image number and join to make an id value'
						this.deckImageNumber = App.getXML(xml,"deckimage");
						currentDeckID = 'deckImage'+this.deckImageNumber+'';
						
						// make .deck-create-image-container a scroll window
						var deckImageScrollPane = $('.deck-create-image-container').jScrollPane();
						
						if (typeof deckImageScrollPane != "undefined") {
							
							App.deckImageScroll(
								".deck-create-image-container img",
								"#deck-modal-right-button",
								"#deck-modal-left-button",
								deckImageScrollPane,
								this.deckImageNumber,
								currentDeckID
							);
							
							// var deckImageScrollPaneAPI = deckImageScrollPane.data('jsp');
							// var deckImageScrollRange = [];
// 							
							// //add each deck image ID value to an array
							// $('.deck-create-image-container img').each(function(index){
								// deckImageID = $(this).attr('id');
								// deckImageScrollRange[index] = deckImageID
							// });
// 							
							// deckImageScrollPaneAPI.scrollToElement('#'+currentDeckID+'');
							// var scrollIndex = $.inArray(currentDeckID, deckImageScrollRange);
// 							
							// $('#deck-modal-right-button').click(function(){
									// if (typeof(deckImageScrollRange[scrollIndex+1]) != "undefined") {
										// scrollIndex = (scrollIndex+1);
										// deckImageScrollPaneAPI.scrollToElement('#'+deckImageScrollRange[scrollIndex],true,true);
										// deckImageNumber++;
// 										
									// }
								// }
							// );
// 							
							// $('#deck-modal-left-button').click(function(){
									// if (typeof(deckImageScrollRange[scrollIndex-1]) != "undefined") {
										// scrollIndex = (scrollIndex-1);
										// deckImageScrollPaneAPI.scrollToElement('#'+deckImageScrollRange[scrollIndex],true,true);
										// deckImageNumber--;
// 										
									// }
								// }
							// );
						}
						
						var deckDescription = App.getXML(xml,"deck");
						if  (deckDescription == "") {
							deckDescription = " ";
						}
						
						var deckAvailableDiv = document.getElementById('deck-available');
						
						$('#name-deck').attr('value',deckDescription);
						
						var deckSelectDiv = document.getElementById('deck-select');
						var deckCardsHeading = App.createDiv(deckSelectDiv,"",'deck-cards-heading');
						$(deckCardsHeading).html('Cards In Deck: '+deckDescription);
						
						var deckCardsCount = App.getXML(xml,"count");
						
						for (count = 0; count <= 9; count++) {
							var cardBlockContainer = App.createDiv(deckSelectDiv,"cardBlockContainer");
							
							if ((count+1) <= deckCardsCount) {
								
								var deckImagePath = App.getXML(xml,"card_"+count+"/imagepath");
								var deckCardImageID = App.getXML(xml,"card_"+count+"/imageid");
								var deckUserCardID = App.getXML(xml,"card_"+count+"/usercardid");
								var deckCardDesc = App.getXML(xml,"card_"+count+"/carddescription");
								var userCardID = App.getXML(xml,"card_"+count+"/usercardid");
								
								var cardBlock = App.createDiv(cardBlockContainer,"cardBlock",userCardID);
								//var cardImageHolder = App.createDiv(cardBlock,"cardImageHolder");
								var albumCardPic = App.createDiv(cardBlock,"album-card-pic");
								var deckCardImage = App.createDiv(albumCardPic,"","","img");
								$(deckCardImage).attr('src',deckImagePath+"cards/"+deckCardImageID+'_web.jpg');
								var albumCardTitle = App.createDiv(cardBlock,"album-card-title");
								$(albumCardTitle).html(deckCardDesc);
								var cardBlockContainer = App.createDiv(deckAvailableDiv,"cardBlockContainer");
								
							};
						}
						
						function saveDeckName(deckImageNumber) {
							
							newDeckName = $('#name-deck').attr('value');
							App.callAjax("_app/deck.php?deckname="+newDeckName+"&deckimage="+deckImageNumber+"&deckid="+deckID);
							
						}
						
						// function saveDeckNameTimer () {
							// if ((typeof(saveNameTimeoutID))=="undefined") {
								// var saveNameTimeoutID = window.setTimeout(saveDeckName,5000);
								// window.clearTimeout(saveNameTimeoutID);
							// } else {
								// window.clearTimeout(saveNameTimeoutID);
								// var saveNameTimeoutID = window.setTimeout(saveDeckName,2000);
							// }
						// }
// 						
						// $('#name-deck').keyup(saveDeckNameTimer);
						
						$('#createSaveDeckButton').click(function(){
							saveDeckName(App.deckImageNumber);
							$('#deck-create-modal-window .close-button').click();
						});
						
						
						if ((typeof App.deckAvailableScrollPane)!="undefined") {
							if ((typeof App.deckAvailableScrollPane.data('jsp'))!="undefined") {
								var deckAvailableScrollPaneAPI = App.deckAvailableScrollPane.data('jsp');
								console.log(deckAvailableScrollPaneAPI.destroy());
							}
						}
						
						App.deckAvailableScrollPane = $('#deck-available').jScrollPane();
						
						App.assignDragsDrops(deckID);
					});
				
				} else if (task == 'create') {
					
					$('#createSaveDeckButton').html('Create Deck');
					
						// var deckSelectDiv = document.getElementById('deck-select');
						// var deckCardsHeading = App.createDiv(deckSelectDiv,"",'deck-cards-heading');
	
					// for(count = 1; count <= 10; count++) {
// 							
							// var cardBlock = App.createDiv(deckSelectDiv,"cardBlock");
							// var cardImageHolder = App.createDiv(cardBlock,"cardImageHolder");
							// var albumCardPic = App.createDiv(cardBlock,"album-card-pic");
							// var albumCardTitle = App.createDiv(cardBlock,"album-card-title");
					// }
						
					App.assignDragsDrops();
					
				}
			});
			
		}
		
     APP_Main.prototype.getXML=function(sData,sElement){
    	 sBrowserName=navigator.appName;
 	
		 if (sBrowserName=="Microsoft Internet Explorer"){
			 sBrowserName="MSIE";
		 }
 	
  		 if (sBrowserName=="MSIE"){
    		 var xData=new ActiveXObject("Microsoft.XMLDOM");
    		 xData.async="false";
    		 xData.loadXML(sData);     
  		 } else {
    		 var xData=new DOMParser();  
    		 xData=xData.parseFromString(sData,"text/xml");
  		 }
   
  		 if (sBrowserName=="MSIE"){
    		 sElement="//"+sElement;
    		 xData.setProperty("SelectionLanguage","XPath");
    
    		 if ((xData.selectSingleNode(sElement))
    			 &&(xData.selectSingleNode(sElement).attributes.getNamedItem("val")))
      		 sAnswer=xData.selectSingleNode(sElement).attributes.getNamedItem("val").value;
    		 else if($(xData.selectSingleNode(sElement)).text())
      		sAnswer=$(xData.selectSingleNode(sElement)).text(); 
    		 else
      		 sAnswer="";
      	 return sAnswer;
  		 } else {
    		 var oEvaluator=new XPathEvaluator();
    		 var oResult=oEvaluator.evaluate(sElement,xData.documentElement
     	  , null,XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
    		if (oResult!=null) {
      		 var oElement=oResult.iterateNext();
      		 /** return first match */
      		 if (oElement){
        			 if (oElement.getAttribute("val"))
          		 return oElement.getAttribute("val");
        			 else if($(oElement).text())
          		 return $(oElement).text();
        		 else
          		 return "";
      		 }
    		 }
  		  }
	   };
     
     APP_Main._iInited=1;
   }
 };
 
var App = new APP_Main();

//THE REAL SLIM SHADY... I mean DOC.READY
$(document).ready (function(){
	
	App.browserPopup();
	
	App.callAjax("_app/main.php?init=1",function(xml){
		App.init(xml);
	});
	if(sURL.indexOf("localhost") == 0){
	     FB.init({
	       appId      : '342203842518329', // App ID
	       status     : true, // check login status
	       cookie     : true, // enable cookies to allow the server to access the session
	       oauth       : true, 
	       channelUrl : '//www.sarugbycards.com/fbapp/index.php', // Channel File
	     });
     }
    
    function buy($credits){
		App.creditsAmount=$credits;
        var obj = {
          method: 'pay',
          action: 'buy_item',
          // You can pass any string, but your payments_get_items must
          // be able to process and respond to this data.
          order_info: {'item_id': $credits},
          dev_purchase_params: {'oscif': true}
        };

        FB.ui(obj, js_callback);
      }

      // This JavaScript callback handles FB.ui's return data and differs
      // from the Credits Callbacks.
      var js_callback = function(data) {
        if (data['order_id']) {
          // Facebook only returns an order_id if you've implemented
          // the Credits Callback payments_status_update and settled
          // the user's placed order.

          // Notify the user that the purchased item has been delivered
          // without a complete reload of the game.
          if(data['status']=="settled"){
			var span_credit = $(".cAmount").html();
			
			var iCredits = parseInt(span_credit);
			var iDiff = iCredits + App.creditsAmount;
			$(".cAmount").html(iDiff);
          	$(".creditsResponse").html("Your purchase was successful. Updating credits.");
          	$(".creditsResponse").fadeIn().delay(4000).fadeOut(function(){
          		//window.location.reload();
          	});
			App.updateCreditView((-1*App.creditsAmount));
          }
          // write_callback_data(
                    // "<br><b>Transaction Completed!</b> </br></br>"
                    // + "Data returned from Facebook: </br>"
                    // + "Order ID: " + data['order_id'] + "</br>"
                    // + "Status: " + data['status']);
        } else if (data['error_code']) {
          // Appropriately alert the user.
          // write_callback_data(
                    // "<br><b>Transaction Failed!</b> </br></br>"
                    // + "Error message returned from Facebook:</br>"
                    // + data['error_code'] + " - "
                    // + data['error_message']);
        } else {
          // Appropriately alert the user.
          //write_callback_data("<br><b>Transaction failed!</b>");
        }
      };

      function write_callback_data(str) {
        document.getElementById('fb-ui-return-data').innerHTML=str;
      }
    
    
    
	//Get the screen height and width
	var maskHeight = $(document).height();
	var maskWidth = $(window).width();
	//Set height and width of mask to fill up the whole screen
	$('#mask').css({'width':maskWidth,'height':maskHeight});
	
	function sendRequestViaMultiFriendSelector() {
		var iCount = App.getXML(App.initXML,"requests/iCount");
		var aIDS = [];
		for(i=0;i<iCount;i++){
			aIDS.push(App.getXML(App.initXML,"requests/request_"+i+"/fbid"));
		}
		
     var receiverUserIds = FB.ui({
       method: 'apprequests',
       message: 'How would you like to join the MyTCG trend?',
       filters: ['app_non_users'],
       display: 'iframe'
     }, function(receiverUserIds){
     	var arrayIDS = receiverUserIds.to;
     	var iCount = arrayIDS.length;
     	for(i=0;i<iCount;i++){
     		App.callAjax("_app/main.php?request_ids="+arrayIDS[i],function(sXML){ });
     	}
     	App.showNotice("Your requests has been sent.",1,true);
     });
   }

		// $('.auctionBlockLarge .picture-box').click(function(){
				// var imgID = $(this).attr('id');
				// App.showCardModal(imgID);
		// });

	// $(".credOnline").click(function(){
		// $(".credSMS").removeClass("credSMS-active");
		// $(".credPaypal").removeClass("credPaypal-active");
		// $(".credOnline").addClass("credOnline-active");
		// App.creditsType = "creditCard";
	// });
	// $(".credSMS").click(function(){
		// $(".credOnline").removeClass("credOnline-active");
		// $(".credPaypal").removeClass("credPaypal-active");
		// $(".credSMS").addClass("credSMS-active");
		// App.creditsType = "SMS";
	// });
	// $(".credPaypal").click(function(){
		// $(".credOnline").removeClass("credOnline-active");
		// $(".credSMS").removeClass("credSMS-active");
		// $(".credPaypal").addClass("credPaypal-active");
		// App.creditsType = "Paypal";
	// });
	
	$('.credOnline, .credSMS, .credPaypal').click(function(){
		
		$('.credOnline, .credSMS, .credPaypal').removeClass('credOnline-active credSMS-active credPaypal-active');
		var currentClassName = $(this).attr('class');
		$(this).addClass(currentClassName+'-active');
		switch(currentClassName){
			case 'credOnline': App.creditsType='creditCard';
				break;
			case 'credSMS': App.creditsType='SMS';
				break;
			case 'credPaypal': App.creditsType='Paypal';
				break;
		}
		
		$(".selDots, .selDotsText").unbind();
		
		if (App.creditsType=='SMS') {
			$(".selDots:nth-child(4), .selDotsText:nth-child(5)").click(function(){
		 		App.highlightPurchaseItems($(this));
			});
			$(".selDots:nth-child(4)").trigger('click');
		} else {
			$(".selDots, .selDotsText").click(function(){
		 		App.highlightPurchaseItems($(this));
			});
		}
		
		$("#buyCredits").unbind();
		$("#buyCredits").click(function(){
			App.buyCredits();
		});
	});
	
	$('.credOnline').trigger('click');

	$(".activityLeftArrow").click(function(){
		App.moveActivity(1);
	});
	$(".activityRightArrow").click(function(){
		App.moveActivity(-1);
	});

	//DO NOT DELETE THIS 
	// $("#title_wall").click(function(){
		// var wall = $("#wall_of_fame");
		// var sHurr = wall.css({display:"block"});
// 		
		// if(sHurr){
			// App.friendListMode = 1;
		// }
		// App.callAjax("_app/achievements.php?list=1&f="+App.friendListMode,function(sXML){ 
			// App.redrawLeaderboardList(sXML);
		// });
		// var trophie = $("#trophies").css({display:"none"});
	// });
	
	//DO NOT DELETE THIS
	// $("#title_trophies").click(function(){
		// var wall = $("#trophies");
		// var sHurr = wall.css({display:"block"});
		// // if(sHurr){
			// // App.friendListMode = 1;
		// // }
		// // App.callAjax("_app/achievements.php?list=1&f="+App.friendListMode,function(sXML){ 
			// // App.redrawLeaderboardList(sXML);
		// // });
		// var trophie = $("#wall_of_fame").css({display:"none"});
	// });
	
	$(".leaderBox").click(function(){
		boardID = $(this).attr('id');
		$(".leaderBox").removeClass("leaderBoxActive");
		$(this).addClass("leaderBoxActive");
		App.callAjax("_app/achievements.php?list="+boardID+"&f="+App.friendListMode,function(sXML){ 
			App.redrawLeaderboardList(sXML);
		});
	});

	$(".leaderSectAddFriend").click(function(){
		sendRequestViaMultiFriendSelector();
	});
	
	$("#buy350").click(function(){
		buy(350);
	});
	$("#buy700").click(function(){
		buy(700);
	});
	
	$("#buy1400").click(function(){
		buy(1400);
	});

	$(".leaderLeftArrow").click(function(){
		App.moveFriends(1);
	});
	$(".leaderRightArrow").click(function(){
		App.moveFriends(-1);
	});
	
	$(".leaderBox").click(function(){
		//$(this).class("");
	});
	
	$("#auction_buttons #notowned,#auction_buttons #all,#auction_buttons #other,#auction_buttons #mine").click(function(){
		App.redrawAuction($(this).attr('id'));
	});
	
	$("#auction_search_button").click(function(){
		App.redrawAuction($("#auction_search_field").attr('value'));
	});
	
	$("#auction_search_field").keydown(function(event){
		if(event.which == 13){
			App.redrawAuction($("#auction_search_field").attr('value'));
		}
	});
	
	
	$("#addFriend").click(function(){
		sendRequestViaMultiFriendSelector();
	});

	$("#new-game").click(function(){
		App.gamePage = "selectGameDeck";
		App.gameID = 0;
		App.showGameScreen();
	});
	
	$("#load-game").click(function(){
		App.gamePage = "load";
		App.showGameScreen();
	});
	
	$(".return-button").click(function(){
		App.gamePage = "mainGameMenu";
		App.gameCurrentPageDivID = $(this).parents(".game-view").attr("id");
		App.showGameScreen();
	});
	
	$("#gameMenuButton").click(function(){
		App.gamePage = "mainGameMenu";
		App.gameCurrentPageDivID = $(this).parents(".game-view").attr("id");
		App.showGameScreen();
	});
	
	$("#next-button").click(function(){
		App.gamePage = "next";
		App.showGameScreen();
	});
	
	$(".choose-computer").click(function(){
		App.gamePage = "selectGameDifficultyLevel";
		App.gameOpponentID = 0;
		App.showGameScreen();
	});
	
	$(".choose-player").click(function(){
		App.gamePage = "player";
		App.showGameScreen();
	});
	
	$("#choose-easy").click(function(){
		App.gameDifficultyID = 1;
		App.gamePage = "gamingCards";
		App.showGameScreen();
	});
	
	$("#choose-normal").click(function(){
		App.gameDifficultyID = 2;
		App.gamePage = "gamingCards";
		App.showGameScreen();
	});
	
	$("#choose-hard").click(function(){
		App.gameDifficultyID = 3;
		App.gamePage = "gamingCards";
		App.showGameScreen();
	});
	
	$(".choose-friend").click(function(){
		App.gameOpponentID = null;
		App.gamePage = "friend";
		App.showGameScreen();
	});
	
	$('#searchAgain').click(function(){
    $("#friendSearchResults").hide("fast");
    $("#friendSearchForm").show("fast");
	});
	
	$("#inviteFriend").click(function(){
		var id = $("#friendSearchResultsList").find(".txtGreen").attr('alt');
		$("#friendSearchResults").hide('fast');
		$("#friendUsername").html($("#friendSearchResultsList").find(".txtGreen").html());
		$("#friendSearchWaiting").show('fast');
		setTimeout("App.startFriending()",1000);
		App.gameFriender();
	});
	
	$("#friendSearch").click(function(){
		var searchstring = $("#friend-finder-input").val().trim();
		$("#friend-finder-input").val(searchstring);
		//validation
		if(searchstring.length < 1){
			var response = "Please enter a friend's username.";
			var icon = "-697px -63px";
			App.showWindow(icon,response);
			$("#friend-finder-input").focus();
			return false;
		} else {
			App.callAjax("_app/play.php?search=1&friend="+searchstring,function(xml){
				var results = parseInt(App.getXML(xml,"found"));
				if(results > 0) {
					if(results > 1) {
						//found more than 1 possibility
						$("#friendSearchResultsList").empty();
						var i = 0;
						for(i=0; i<results; i++){
							$("#friendSearchResultsList").append('<div class="searchResult" alt="'+App.getXML(xml,"results/result_"+i+"/user_id")+'">'+App.getXML(xml,"results/result_"+i+"/username")+'</div>');
						}
					} else {
						//found the friend
						var i = 0;
						$("#friendSearchResultsList").html('<div class="searchResult" alt="'+App.getXML(xml,"results/result_"+i+"/user_id")+'">'+App.getXML(xml,"results/result_"+i+"/username")+'</div>');
					}
					//click event handler
					$(".searchResult").unbind().click(function(){
						if(!$(this).hasClass('txtGreen')){
							App.playerFriendID = $(this).attr('alt');
							App.playerFriend = $(this).html();
							$(".searchResult").removeClass('txtGreen');
							$(this).addClass('txtGreen');
							$("#inviteFriendHolder").hide().show('fast');
						}
					});
				} else {
					//no users found
					$("#friendSearchResultsList").html('<div style="width:100%;cursor:default!important;">`'+searchstring+'` not found</div>');
				}
				$("#friendSearchForm").hide('fast');
				$("#inviteFriendHolder").hide();
				$("#friendSearchResults").show('fast',function(){
					$(this).show();
				});
			});
		}
	});
});

$(document).ready(function(){
	
	//VIEW AUCTION BUTTONS
	$(".auctionBlockLarge .viewAuctionButton").click(function (){
		marketID = $(this).attr('id');
		App.viewAuctionButton(marketID);
	});
	
	$(".auctionBlockLarge .picture-box").click(function (){
		marketID = $(this).attr('id');
		App.viewAuctionButton(marketID);
	});
	
	// animates the friend blocks left or right, setting a counter upont event firing, and will not go beyond the ends 	of the defined sequence to scroll.
	
	var prevButton;
	var nextButton;
	var scrollDistance;
	var unitCount = $('.friend_info').length+1;
	var unitWidth = parseInt($('.friend_block').css('width'));
	var coverflowWidth = parseInt($('#coverflow').css('width'));
	var viewWidth = (unitCount*unitWidth);
	
	$("#friend_view").css('width',viewWidth);
	
	if ((viewWidth/coverflowWidth)!=1){
		var totalPositionCount = Math.ceil(parseInt(viewWidth/coverflowWidth));	
	} else {
		totalPositionCount = 0;
	}

	var positionCounter = 0;
	$("#next_block").click(function (){
		if (positionCounter!=totalPositionCount){
			positionCounter+=1;
			$("#friend_view").animate({"left":"-=672"},400);
		}
	});
	
	$("#prev_block").click(function (){
		if (positionCounter!=0) {
			positionCounter-=1;
			$("#friend_view").animate({"left":"+=672"},400);
		}	 
	});
    
	 $(".booster-list-pic").click(function() {
	 	var cardID = $(this).get(0).id;
	 	//alert(cardID+" WIN");
	  });
    
    //ALBUM CHANGE CATEGORY
    $('.right_menu_item').click(function(){
      var catID = $(this).get(0).id;
      App.showCards(catID);
    });
    
    //SHOW NEW LEADERBOARD LIST
    $('.leaderboard_menu .cmdButton').click(function(){
      var boardID = $(this).get(0).id;
      App.callAjax("_app/leaderboard.php?list="+boardID,function(xml){
        var sXML = xml;
        $('.leaderboard_chart').remove();
        var divBody = $(".leaderboard-table-container").get(0);
        var tblChart = App.createDiv(divBody,"leaderboard_chart","","table");
        var tr = App.createDiv(tblChart,"","","tr");
        var th = App.createDiv(tr,"","","th");
        $(th).html('Rank');
        var th = App.createDiv(tr,"","","th");
        $(th).html('Name');
        var th = App.createDiv(tr,"","","th");
        $(th).html('Score');
          var iCount = parseInt(App.getXML(sXML,"count"));
          for(var i=0; i<iCount; i++){
            var tr = App.createDiv(tblChart,"","","tr");
            var td = App.createDiv(tr,"","","td");
            $(td).html(i+1);
            var td = App.createDiv(tr,"","","td");
            $(td).html(App.getXML(sXML,"leader_"+i+"/username"));
            var td = App.createDiv(tr,"","","td");
            $(td).html(App.getXML(sXML,"leader_"+i+"/value"));
          }
		
      });
    });
    
    //SHOW SHOP BOOSTER CARD LIST

    $('.shop-pic, .view_button').click({'id':$(this).attr('id')},function(){
      var packID = $(this).get(0).id;
      App.callAjax("_app/shop.php?boosterpack="+packID,function(xml){
        var sXML = xml;
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();
        
        //Set height and width of mask to fill up the whole screen
        $('#mask').css({'width':maskWidth,'height':maskHeight});
         
        //transition effect - show the mask  
        $('#mask').fadeIn('fast');
        $('#mask').fadeTo("medium",0.6); 
        
        var divBody = document.body;
        
        var divModalWindow = App.createDiv(divBody,"modal-window","booster-modal-window");
        
        var divCloseButtonContainer = App.createDiv(divModalWindow,"closeButtonContainer");
		  var divClose = App.createDiv(divCloseButtonContainer,"close-button");
		  $(divClose).html("<span>CLOSE</span>");
		  
		  $(divClose).click(function() {
		        window.flipped = false;
		        $('.modal-window').fadeTo("fast",1);
		        $(".modal-window").remove();
		        $(".modal-picture-holder").remove();
		        $("#mask").hide();
		  });
        
        var divBoosterHeading = App.createDiv(divModalWindow,"booster-heading");
        $(divBoosterHeading).html("Possible Cards In Pack");
        var divBoosterPrice = App.createDiv(divModalWindow,"booster-price");
        var divBoosterPriceSpan = App.createDiv(divBoosterPrice,"","","span");
        $(divBoosterPrice).append("<span>"+App.getXML(sXML,"price")+"</span> TCG");
        
        var divBoosterPackPreview = App.createDiv(divModalWindow,"booster-pack-preview");
        var imgBoosterImg = App.createDiv(divBoosterPackPreview,"booster-pack-pic-holder","","img");
        imgBoosterImg.src = App.getXML(sXML,"path")+'products/'+App.getXML(sXML,"image")+'.jpg';
        $(imgBoosterImg).css({width:200});
        var divBoosterDescription = App.createDiv(divBoosterPackPreview,"boosterDescription");
        $(divBoosterDescription).html(App.getXML(sXML,"desc"));
        var divBoosterPackCount = App.createDiv(divBoosterPackPreview,"boosterNumberOfCards");
        $(divBoosterPackCount).html(App.getXML(sXML,"size")+" cards in pack");
        var divBoosterCards = App.createDiv(divModalWindow,"","booster-possibles-window");
         var iCount = parseInt(App.getXML(sXML,"count"));
        for(var i=0; i<iCount; i++){
	        var divPlaceHolder = App.createDiv(divBoosterCards,"booster-list-placeholder");
	        var divListPic = App.createDiv(divPlaceHolder,"booster-list-pic");
	        var imgBoosterIcon = App.createDiv(divListPic,"","","img");
	        imgBoosterIcon.src = App.getXML(sXML,"cards/card_"+i+"/path")+'cards/'+App.getXML(sXML,"cards/card_"+i+"/image")+'_web.jpg';
	        var divListPicCaption = App.createDiv(divPlaceHolder,"booster-list-pic-caption");
	        $(divListPicCaption).html(App.getXML(sXML,"cards/card_"+i+"/description"));
	        var boosterListPicIconContainer = App.createDiv(divListPic,"boosterListPicIconContainer");
	        var divListPicIcon = App.createDiv(boosterListPicIconContainer,"booster-list-pic-icon");
	        if (App.getXML(sXML,"cards/card_"+i+"/possess")>0){
               $(divListPicIcon).css('display','block');
	        }
        }
        
        var divBoosterBuyButton = App.createDiv(divModalWindow,"buyItemButton",packID);
        $(divBoosterBuyButton).html("Buy");
       
       //show the modal window
        $('.modal-window').show("scale",300);
        $('#booster-possibles-window').jScrollPane();
        $('#booster-modal-window .buyItemButton').click(function(){
            var packID = $(this).attr('id');
        		App.buyItem(packID);
        		$('#booster-modal-window').fadeTo("fast",1);
        		$("#booster-modal-window").remove();
        		$("#mask").hide();	
        });
      });
    });
     
    //if close button or mask is clicked
    $('#mask').click(function() {
        window.flipped = false;
        $('.modal-window').fadeTo("fast",1);
        $(".modal-window").remove();
        $(".modal-picture-holder").remove();
        $("#mask").hide();
    });
    
    $('.productBlock .buyItemButton').click(function(){
    	  var packID = $(this).attr('id');
        App.buyItem(packID);	
    });     
	
	$('.scroll_pane').jScrollPane();
	
	$('#right_menu').jScrollPane();
	
	
	
	//Leaderboard XML for new Dashboard
	
	$('.rankingType').click({
		richest: "1",
		gamesWon: "2",
		gamesLost: "3",
		cardsCollected: "4",
		mostCards: "5"
	},function(event){
		$('#rankingTypesContainer .selected').animate({color:'#222327'},400);
		$('.rankingType').removeClass('selected');
		$(this).animate({color:'#89b005'},400);
		$(this).addClass('selected');
		var rankingType = $(this).attr('id');
		var listID = event.data[''+rankingType+''];
		App.loadLeaderboard(listID);
	});
	
	$('.activityOption').click(function(){
		$('#activityOptionContainer .selected').animate({color:'#222327'},400); 
		$('.activityOption').removeClass('selected');
		$(this).animate({color:'#89b005'},400);
		$(this).addClass('selected');
		// $(this).addClass('selected');
	});
	
	//Light up the right menu draggable scroll bar (red) when mousing over the whole area.
	
	// $('.right_menu_item').mouseover(function() {
		// if (rightMenuRed == false) {
				// rightMenuRed = true;
			// $('#right_menu .jspDrag').animate({'backgroundColor':'#ED1324'},200);
		// }
	// });
	// $('.right_menu_item').mouseout(function() {
		// if (rightMenuRed == true) {
				// rightMenuRed = false;
			// $('#right_menu .jspDrag').animate({'backgroundColor':'#171918'},200);
		// }
	// });
	
	//Zoom the menu items in the album category right-hand menu when mousing over
	
	$('.right_menu_item').mouseover(function(){
		$(this).stop().animate({'font-size':'15px'},100);
	});
	
	$('.right_menu_item').mouseout(function() {
		$(this).stop().animate({'font-size':'12px'},100);
	});
	
	$('.album-card-pic-container').click(function(){
		var cardID = parseInt($(this).attr('id'));
		if(cardID > 0){
			App.showCardModal(cardID);
		}
	});
	
	App.assignScrollAction();
	
	// $('.picture-box').click(function(){
		// imgID = $(this).attr('id');
		// App.showCardModal(imgID);
	// });
	
	$('#createDeckModalButton').click(function(){
		// App.showDeckModal('create');
		App.createDeckModal();
	});
	
	$('.deck-container').click(function(e){
		if (($(e.target).attr('class')) != 'deckDeleteButton') {
			$('#mask').fadeIn(500);
			$('#deck-create-modal-window').show();
			App.showDeckModal('edit',($(this).attr('id')));	
		} else {
			$('#mask').fadeIn(500);
			App.showDeleteDeckModal($(this).attr('id'));
		}
	});
	
	$('#auction_search_field').text('search');
	
	$('#auction_search_field').focus(function(){
		$(this).attr('value',' ');
	});
	
	$('#auction_search_field').focusout(function(){
		$(this).attr('value','search');
	});
	
	$('#shopScrollPane').jScrollPane();
	
	// ZU.getTimeLeft(new Date(ZA.getXML(ZU.sXML,"cards/card_"+iCount+"/expire")), true)
	
});
	








