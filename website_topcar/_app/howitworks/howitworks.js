function WORK_HowItWorks(){
	this.iComponentNo=0;
	this.divData=0;
	this.iMaxStrips=8;
	this.iActiveStrip=0;
	this.iCloseStrip=0;
	this.iHeightStrip=31;
	var aMovies=[];

	
	if (typeof WORK_HowItWorks._iInited=="undefined"){

WORK_HowItWorks.prototype.clickStrip=function(iStripNo){
	return function() {
		ZH.clickStripA(iStripNo);
	};
};


WORK_HowItWorks.prototype.clickStripA=function(iStripNo){
	if (!ZA.aComponents[ZH.iComponentNo].iIsMaximized) {
		ZA.maximizeWindowA(ZH.iComponentNo);
	}
	var divStrip=document.getElementById
	("howitworksstrip_"+iStripNo);
	var divStripData=document.getElementById
	("howitworksstripdata_"+iStripNo);
	var divContent=document.getElementById
		("howitworksstripcontent_"+iStripNo);
	var divMovie=document.getElementById
		("howitworksstripmovie_"+iStripNo);
	var divStripName=document.getElementById
	("howitworksstripname_"+iStripNo);
	var divStripDataOld=document.getElementById
		("howitworksstripdata_"+ZH.iActiveStrip);
	var divContentOld=document.getElementById
		("howitworksstripcontent_"+ZH.iActiveStrip);
	var divMovieOld=document.getElementById
		("howitworksstripmovie_"+ZH.iActiveStrip);
	var divStripNameOld=document.getElementById
		("howitworksstripname_"+ZH.iActiveStrip);
	var divStripOld=document.getElementById
		("howitworksstrip_"+ZH.iActiveStrip);
	$(divStripName).css({
		backgroundPosition:"-231px -"+((ZH.iHeightStrip*iStripNo)+364)+"px"
	});
	if (divStripNameOld) {
		$(divStripOld).css({
			backgroundPosition:"-1px -"
			+(ZH.iHeightStrip*ZH.iActiveStrip+621)+"px"
		});
		$(divStripNameOld).css({
			backgroundPosition:"-1px -"+((ZH.iHeightStrip*ZH.iActiveStrip)+364)+"px"
		});		
	}
	$(divStrip).css({backgroundPosition:"-1px -900px"});
	if (!ZH.iActiveStrip) {
		$(divContent).css({display:"block"});
		$(divMovie).css({display:"block"});
		$(divStripData).animate({height:"470px"},function(){
			$(divMovie).html(aMovies[iStripNo]);
		});
	} else {
		if (ZH.iActiveStrip==iStripNo) {
			$(divStrip).css({
				backgroundPosition:"-1px -"
				+(ZH.iHeightStrip*iStripNo+621)+"px"
			});
			$(divMovie).html("");			
			$(divStripDataOld).animate({height:"0px"},function(){
				$(divContentOld).css({display:"none"});
				$(divMovieOld).css({display:"none"});				
			});
			iStripNo=0;
		} else {
			$(divContent).css({display:"block"});
			$(divMovie).css({display:"block"});
			$(divStripDataOld).animate({height:"0px"},function(){
				$(divContentOld).css({display:"none"});
				$(divMovieOld).css({display:"none"});
			});
			$(divStripData).animate({height:"470px"},function(){
				$(divMovie).html(aMovies[iStripNo]);
			});
		}
	}
	ZH.iActiveStrip=iStripNo;	
};


WORK_HowItWorks.prototype.getHTMLcontent=function(iCount){
  var sHTML = "";
  switch(iCount){
    case 1:
      sHTML = "<span class='txtBlue'><h2>Register</h2></span><i>[re-je-ster]</i><br>def. the place on our website where confirming details is so last year<br><br><b>Step 1:</b> Locate the Register window on the front page<br><b>Step 2:</b> Fill in the email and password fields<br><b>Step 3:</b> Click on the Register button or press Enter<br><br><b>Pro Tip 1:</b> The lines are where you type<br><b>Pro Tip 2:</b> Remember your password<br><br><br><span class='txtBlue'><b>Congratulations!</b></span> You are now officially a mytcg user.";
    break;
    case 2:
      sHTML = "<span class='txtBlue'><h3>Login</h3></span><i>[log-in]</i><br>def. the place on our website which gives you access to your virtual game card world<br><br><b>Step 1:</b> Navigate to the Login menu button in the top right hand corner of the page<br><b>Step 2:</b> Type in your email/username and password<br><b>Step 3:</b> Click on the Login button<br><br><b>Pro Tip 1:</b> Recall your password<br><b>Pro Tip 2:</b> In case of emergency, click on the &quot;forgotten password&quot; link<br><b>Pro Tip 3:</b> If Tip 2 becomes a habit, please consult a doctor<br><br><br>You are now ready to buy, sell, build and play.";
    break;
    case 3:
      sHTML = "<span class='txtBlue'><h3>Buy Credits</h3></span>Coming soon...";
    break;
    case 4:
      sHTML = "<span class='txtBlue'><h3>Shop</h3></span><i>[shop]</i><br>def. the place on our website where you don’t need to be a woman to do this action good<br><br><b>Step 1:</b> Navigate yourself to the top left panel<br><b>Step 2:</b> Double click on the top panel bar or click on the green or rectangular icon<br><b>Step 3:</b> Click on the Buy button to buy the booster pack<br><br><b>Pro Tip 1:</b> Be click happy and press the buy button<br><b>Pro Tip 2:</b> If you see a car you’re most likely in the cars category<br><b>Pro Tip 3:</b> The little arrows are for navigation<br><b>Pro Tip 4:</b> Green Dot = Grey Boxy = Big Screen<br><br><br>You are now the proud owner of new game cards!";
    break;
    case 5:
      sHTML = "<span class='txtBlue'><h3>Auction</h3></span><i>[ok-sh-en]</i><br>def. the place on our website where choosing the right cards is an absolutely crucial component of winning<br><br><b>Step 1:</b> Navigate yourself to the bottom right panel<br><b>Step 2:</b> Double click on the top panel bar or click on the green or rectangular icon<br><b>Step 3:</b> Click on the View button to bid or buy the card<br><br><b>Option I: Bid</b><br><b>Step 4:</b> Select your bid amount by clicking on the up or down arrows<br><b>Step 5:</b> Click on the Place Bid button to place your bid<br>Your bid has been placed. If no one else places a higher bid than you before the designated expiry date, the card will belong to you.<br><br><b>Option II: Buy</b><br><i>[This functionality is only available if the seller has set a buy-out price]</i><br><b>Step 4:</b> Click the Buy Now button to buy the card out immediately.<br><br><b>Pro Tip 1:</b> Buy = No wait = Your card<br><b>Pro Tip 2:</b> Bid = Wait = Maybe your card";
    break;
    case 6:
      sHTML = "<span class='txtBlue'><h3>My Album</h3></span><i>[mi al-bem]</i><br>def. the place on our website where your whole game card collection is under one window [or is it roof...?]<br><br><ul><li>Navigate yourself to the bottom left panel</li><li>For a quick view, use the arrows at the bottom to pan through your album</li><li>To maximize the panel, click on the green icon to the top right and the rectangular icon to the top right corner of the panel</li><li>Use the menu to the right to select the desired album</li><li>To view an enlarged view of a card, just click on any one</li><li>Clicking on the icon on the left edge of the card will send the card to auction</li><li>Clicking on a maximized card again will flip it around for a background view</li><li>Greyed out cards haven&#39;t been bought yet</li></ul><br><b>Pro Tip 1:</b> The greyed out images means you still need to collect them<br><b>Pro Tip 2:</b> The numbers on the top-left of that card indicate how many of that card you own<br><b>Pro Tip 3:</b> If you spot an image of yourself in the album, please report it...<br>";
    break;
    case 7:
      sHTML = "<span class='txtBlue'><h3>Deck</h3></span><i>[dek]</i><br>def. the place on our website where creating a deck might be your next step to fame<br><br><b>Creating a deck</b><br><b>Step 1:</b> Navigate yourself to the top right panel<br><b>Step 2:</b> Double click on the top panel bar or click on the green or rectangular icon<br><b>Step 3:</b> Click on the New button<br><b>Step 4:</b> Type in your deck name, select your category from the drop down and select your deck image by clicking on the arrows<br><b>Step 5:</b> Click on the Create Deck button<br><br>You have just created a new deck, ready for you to add new cards to it.<br><b>Adding Cards</b><br><b>Step 1:</b> Click on one of the decks to add cards to it<br><b>Step 2:</b> Drag any of the available cards in the right column to the left column<br><b>Step 3:</b> Click the Close button to go back to the full list of decks available<br><b>Step 4:</b> Delete a deck by clicking on the red cross to the right of the deck<br><br><b>Pro Tip 1:</b> Test your new decks by playing against the computer<br><b>Pro Tip 2:</b> Keep collecting more and better cards<br>";
    break;
    case 8:
      sHTML = "<span class='txtBlue'><h3>Play</h3></span><i>[play]</i><br>def. the place on our website where the games begin<br><br><b>Step 1:</b> Select the stat that you would like to play <i>[a green bar will appear next to it]</i><br><b>Step 2:</b> Wait for your opponent&#39;s card to be revealed<br><b>Step 3:</b> If you win, repeat step 1 and 2 until you loose<br><b>Step 4:</b> If you lose, wait your turn to play again<br><br><b>The game will continue until one player has won all his opponent&#39;s playing cards.</b>";
    break;
  }
  return sHTML;
};

WORK_HowItWorks.prototype.init=function(iComponentNo){
	ZH.iComponentNo=iComponentNo;
	ZH.divData=document.getElementById("window_"+iComponentNo);
	var iWidth=parseInt(ZH.divData.style.width);
	for (var iCount=1;iCount<=ZH.iMaxStrips;iCount++) {
		var divStrip=ZA.createDiv
			(ZH.divData,"howitworksstrip","howitworksstrip_"+iCount);
			divStrip.onclick=ZH.clickStrip(iCount);
			var divStripData=ZA.createDiv
			(ZH.divData,"howitworksstripdata","howitworksstripdata_"+iCount);
		var divContent=ZA.createDiv(divStripData,"howitworksstripcontent","howitworksstripcontent_"+iCount);
		
		$(divContent).html(ZH.getHTMLcontent(iCount));
		
		var divMovie=ZA.createDiv(divStripData,"howitworksstripmovie","howitworksstripmovie_"+iCount);
		var sMovie=ZA.createMovie(390,280,"_site/"+(iCount)+".mov","f0f0ff");
		aMovies[iCount]=sMovie;
		var divName=ZA.createDiv
			(divStrip,"howitworksstripname","howitworksstripname_"+iCount);
		$(divName).css({
			backgroundPosition:"-1px -"+((ZH.iHeightStrip*iCount)+364)+"px"
		});
		$(divStrip).css({
			height:ZH.iHeightStrip+"px"
			,backgroundPosition:"-"+(998-iWidth)+"px -"
				+(ZH.iHeightStrip*iCount+621)+"px"
		});
	}
};


WORK_HowItWorks.prototype.maximize=function(){
	for (var iCount=1;iCount<=ZH.iMaxStrips;iCount++) {
		var divStrip=document.getElementById("howitworksstrip_"+iCount);
		$(divStrip).css({
			backgroundPosition:"-2px -"+(ZH.iHeightStrip*iCount+621)+"px"
		});
	}
};


WORK_HowItWorks.prototype.restore=function(){
	var iWidth=parseInt(ZA.aComponents[4].aPos[2]);
	for (var iCount=1;iCount<=ZH.iMaxStrips;iCount++) {
		var divStrip=document.getElementById("howitworksstrip_"+iCount);
		$(divStrip).animate({
			backgroundPosition:"-"+(998-iWidth)+"px -"+(ZH.iHeightStrip*iCount+621)+"px"
		});
	}
	if (ZH.iActiveStrip) {
	  var divStripBG=document.getElementById
      ("howitworksstrip_"+ZH.iActiveStrip);
		var divStripDataOld=document.getElementById
			("howitworksstripdata_"+ZH.iActiveStrip);
		var divStripNameOld=document.getElementById
		("howitworksstripname_"+ZH.iActiveStrip);
		var divContentOld=document.getElementById
			("howitworksstripcontent_"+ZH.iActiveStrip);
		var divMovieOld=document.getElementById
			("howitworksstripmovie_"+ZH.iActiveStrip);
		$(divStripDataOld).animate({height:"0px"},function(){
			$(divContentOld).css({display:"none"});
			$(divMovieOld).css({display:"none"});				
		});
		$(divStripNameOld).css({backgroundPosition:"-1px -"+(ZH.iHeightStrip*ZH.iActiveStrip+364)+"px"});
		var bgpos = 621 + (31*ZH.iActiveStrip);
		$(divStripBG).css({backgroundPosition:"-518px -"+bgpos+"px"});
	}
};


  }
	WORK_HowItWorks._iInited=1;
};



var ZH = new WORK_HowItWorks();
//ZH.init(4);
ZA.callAjax("_app/howitworks/?init=1",function(xml){ ZH.init(4,xml); },4);
