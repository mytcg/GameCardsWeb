function WORK_Toptrumps()
{
	this.iComponentNo=8;
	this.divData=0;
	this.sURL="_app/toptrumps/";
	this.sXML="";
	this.cardsXML="";
	this.cardsArray=[];
	this.gameCards=[];
	this.iWindowHeight=0;
	this.currentPage=0;
	this.deckPages=0;
	this.dragged=false;
	this.divGameScreens=null;
	this.playerOpponent=-1;
	this.playerDifficulty=-1;
	this.playerCategoryID=-1;
	this.playerDeckID=-1;
	this.playerFriendID=-1;
	this.playerFriend='';
	this.playerGameID=0;
	this.gameStatus='';
	this.gameMessage='';
	this.creditsWon=0;
	this.p1card="";
	this.p2card="";
	this.moves=0;
	this.activePlayer=0;
	this.p1score=0;
	this.p2score=0;
	this.aMenu=null;
	this.paused=false;
	this.playerLog=0;
	this.looker=null;
	this.listener=null;
	this.p1Cards=null;
	this.p2Cards=null;
	this.drawCards=null;
	this.viewEnd=0;
	

	if(typeof WORK_Toptrumps._iInited=="undefined"){


WORK_Toptrumps.prototype.init=function()
{
	ZT.divData = document.getElementById("window_"+ZT.iComponentNo);
	//$(ZT.divData).css("background","#ccc url(_site/gameboard.png) -130px -145px no-repeat");
	
	// Initialise
	if(!ZA.sUsername){
		//No user logged in - Random Play vs CPU
		ZT.showMainboard();
	}
	else{
		//User is loggen in - Show Game Menu
		ZT.showGameMenu();
		//ZT.showDeckSelector();
	}
};


WORK_Toptrumps.prototype.maximize=function(){
	//ZA.callAjax(ZT.sURL+"?init=1",function(xml){
		//ZT.sXML = xml;
		
		if(ZA.aComponents[ZT.iComponentNo].iIsMaximized){
			if(ZT.divData == 0){
				ZT.init();
			}
		}
		else{
			//minimize window
			//if waiting for player, cancel it
			if(ZT.looker != null){
				ZT.stopLooking();
				ZT.showGameScreen('opponent','player');
			}
		}
	//});
};


WORK_Toptrumps.prototype.gotoPage=function(page)
{
	var newPos = (ZT.currentPage * -456) + 10;
	$(".divDeckBook").animate({left:newPos},600);
	$(".divpagebutton").removeClass('dotselected');
	$(".divpagebutton[id='"+page+"']").addClass('dotselected');
};


//**************************************************************************************************************************
//**************************************************************************************************************************
//**************************************************************************************************************************
// GAME SETUP MENU
//**************************************************************************************************************************
//**************************************************************************************************************************
//**************************************************************************************************************************


WORK_Toptrumps.prototype.showGameMenu=function()
{
	if($("#gameScreensHolder").size()){
		ZT.playerOpponent=-1;
		ZT.playerDifficulty=-1;
		ZT.playerCategoryID=-1;
		ZT.playerDeckID=-1;
		ZT.playerGameID=0;
		$("#gameScreensHolder").remove();
	}
	var divHolder = ZA.createDiv(ZT.divData,"","gameScreensHolder");
	ZT.divGameScreens = divHolder;
	
	var div = ZA.createDiv(divHolder,"gameScreenBg");
	
	//Game main menu screen
	var divScreen = ZA.createDiv(divHolder,"gameScreen","menu");
	 $(divScreen).show();
	
		

		var gameMenuBg=ZA.createDiv(divScreen,"gameMenuBg");
		var div = ZA.createDiv(gameMenuBg,"gameMenuItemContainer");
		
		//new game
		var divMenuItem = ZA.createDiv(div,"gameMenuItem_1");
		var divMenuItemBg=ZA.createDiv(divMenuItem,"gameMenuItemBg_1");
		var divMenuItemTitle=ZA.createDiv(divMenuItem,"gameMenuItemTitle_1")
		$(divMenuItemTitle).html('New Game');
		$(divMenuItem).click(function(){
			ZT.playerDeckID = -1;
			$(".deckOptionBlockHolder").removeClass('deckOptionSelected');
			ZT.playerOpponent = -1;
			$(".versusOptionBlock").removeClass('versusOptionSelected');
			ZT.playerDifficulty = -1;
			$(".difficultyOption").removeClass('difficultyOptionSelected');
			//if only 1 category, select it and go to deck screen
			if($(".categoryOptions").find(".categoryOption").size() > 1){
				ZT.playerCategoryID = -1;
				$(".categoryOption").removeClass('categoryOptionSelected');
				ZT.showGameScreen('category','menu');
			}
			else{
				var cat = $(".categoryOptions").find(".categoryOption");
				ZT.playerCategoryID = cat.attr('id');
				cat.addClass('categoryOptionSelected');
				ZT.buildDeckScreen('menu');
			}
		});
		//load game
		var divMenuItem = ZA.createDiv(div,"gameMenuItem_2");
		var divMenuItemBg=ZA.createDiv(divMenuItem,"gameMenuItemBg_2");
		var divMenuItemTitle=ZA.createDiv(divMenuItem,"gameMenuItemTitle_2")
		$(divMenuItemTitle).html('Load Game');
		$(divMenuItem).click(function(){
			ZT.buildLoadScreen();
		});
		//settings
		// var divMenuItem = ZA.createDiv(divMenuItems,"gameMenuItem");
		// $(divMenuItem).css({
			// "background-position":"-850px -69px"
		// });
		// $(divMenuItem).html('Settings');
		// $(divMenuItem).click(function(){
			// ZT.showGameScreen('settings','menu');
		// });
		//quit
		// var divMenuItem = ZA.createDiv(divMenuItems,"gameMenuItem");
		// $(divMenuItem).css({
			// "background-position":"-850px -100px"
		// });
		// $(divMenuItem).html('Quit');
		// $(divMenuItem).click(function(){
			// ZA.maximizeWindowA(ZT.iComponentNo);
		// });
	
	//Settings screen
	
	// var divScreen = ZA.createDiv(divHolder,"gameScreen","settings");
// 	
// 		
		// //Next
		// var divNav = ZA.createDiv(divScreen,"gameNext","settings");
		// $(divNav).hide();
		// $(divNav).click(function(){
			// ZT.showGameScreen('menu','settings');
		// });
		// //Back to main menu
		// var divMenuReturn = ZA.createDiv(divScreen,"gameMenuReturn");
		// var divMenuItem = ZA.createDiv(divMenuReturn,"gameMenuItem");
		// $(divMenuItem).html('Return to main menu');
		// $(divMenuItem).click(function(){
			// ZT.showGameScreen('menu','settings');
		// });
// 		
	//Category screen
	
	var divScreen = ZA.createDiv(divHolder,"gameScreen","category");
	
			var divOpts = ZA.createDiv(divScreen,"categoryOptions");
			$(divOpts).css({
				width:"100%",
				marginTop:20
			});
			
			var div = ZA.createDiv(divOpts,"categoryOption","2");
			$(div).css({
				background:"transparent url(_site/gameboard.png) -20px -145px no-repeat",
				marginLeft:"auto",
				marginRight:"auto"
			});
			$(div).attr('title','TopCar');
			
			//Event handler
			$(".categoryOption").click(function(){
				$(this).addClass('categoryOptionSelected');
				var id = $(this).attr('id');
				ZT.playerCategoryID = id;
				ZT.buildDeckScreen();
			});
			
		//Back to main menu
		var divMenuReturn = ZA.createDiv(divScreen,"gameMenuReturn");
		var divMenuItem = ZA.createDiv(divMenuReturn,"gameMenuItem");
		//$(divMenuItem).html('return to main menu');
		$(divMenuItem).click(function(){
			ZT.showGameScreen('menu','category');
		});
		
	//Opponent screen
	
	var divScreen = ZA.createDiv(divHolder,"gameScreen","opponent");
	var gameMenuBg=ZA.createDiv(divScreen,"gameMenuBg");
		
			//Opponent options
			var div = ZA.createDiv(gameMenuBg);
			$(div).css({
				position:"relative",
				marginBottom:5,
				top:0,
			});
			$(div).html(
				'<table style="width:100%" align="center">'+
					'<tr>'+
						'<td style="width:50%" align="center">CHOOSE OPPONENT</td>'+
					'</tr>'+
				'</table>'
			);
			var divOpts = ZA.createDiv(gameMenuBg);
			$(divOpts).css({
				position:"relative",
				height:100,
				width:"100%",
				top:10,
			});
			
			//Computer
			var divBlock = ZA.createDiv(divOpts,"versusOptionBlockHolder","vb0");
			var div = ZA.createDiv(divBlock,"versusOptionBlock","vs0");
			$(div).attr('alt','0');
			var div = ZA.createDiv(divBlock,"opponent","0");
			$(div).html('Computer');
			//Player
			var divBlock = ZA.createDiv(divOpts,"versusOptionBlockHolder","vb1");
			var div = ZA.createDiv(divBlock,"versusOptionBlock","vs1");
			$(div).attr('alt','1');
			var div = ZA.createDiv(divBlock,"opponent","1");
			$(div).html('Player');
			//Friend
			var divBlock = ZA.createDiv(divOpts,"versusOptionBlockHolder","vb2");
			var div = ZA.createDiv(divBlock,"versusOptionBlock","vs2");
			$(div).attr('alt','2');
			var div = ZA.createDiv(divBlock,"opponent","2");
			$(div).html('Friend');
		
		//Back
		var divNav = ZA.createDiv(divScreen,"gameBack");
		$(divNav).click(function(){
			ZT.showGameScreen('deck','opponent');
		});
		//Next
		var divNav = ZA.createDiv(divScreen,"gameNext","opponent");
		$(divNav).click(function(){
			if(ZT.playerOpponent > -1){
				if(ZT.playerOpponent == '0'){
					$(".gameNext[id='difficulty']").hide();
					ZT.showGameScreen('difficulty','opponent');
				}
				else if(ZT.playerOpponent == '1'){
					ZT.buildPlayerScreen();
				}
				else if(ZT.playerOpponent == '2'){
					ZT.buildFriendScreen();
				}
			}
			else{
				var response = "No opponent selected";
				var icon = "-697px -63px";
				ZA.showWindow(icon,response);
			}
		});
		//Back to main menu
		var divMenuReturn = ZA.createDiv(divScreen,"gameMenuReturn");
		var divMenuItem = ZA.createDiv(divMenuReturn,"gameMenuItem_back");
		//$(divMenuItem).html('Return to main menu');
		$(divMenuItem).click(function(){
			ZT.showGameScreen('menu','opponent');
		});
		
		//Event handlers
		$(".versusOptionBlock").click(function(){
			var opp = $(this).attr('alt');
			ZT.playerOpponent = opp;
			if(ZT.playerOpponent == '0'){
				$(".gameNext[id='difficulty']").hide();
				ZT.showGameScreen('difficulty','opponent');
			}
			else if(ZT.playerOpponent == '1'){
				ZT.buildPlayerScreen();
			}
			else if(ZT.playerOpponent == '2'){
				ZT.buildFriendScreen();
			}
		});
	
	//Difficulty screen
	
	var divScreen = ZA.createDiv(divHolder,"gameScreen","difficulty");
	var gameMenuBg = ZA.createDiv(divScreen,"gameMenuBg");
		
		
			//Diffifulty options
			var divOpts = ZA.createDiv(gameMenuBg);
			$(divOpts).css({
				position:"relative",
				top:10,
				height:100,
				width:"100%",
				marginLeft:"auto",
				marginRight:"auto"
			});
			//Easy
			var easyContainer=ZA.createDiv(divOpts,"difficultyContainer","dc01");
			var div = ZA.createDiv(easyContainer,"difficultyOption","easy");
			$(div).attr('alt','1');
			$(div).html('Easy<input type="hidden" value="Computer has good cards. You start. You receive 20TCG for a win!" />');
			//Normal
			var normalContainer=ZA.createDiv(divOpts,"difficultyContainer","dc02");
			var div = ZA.createDiv(normalContainer,"difficultyOption","normal");
			$(div).attr('alt','2');
			$(div).html('Normal<input type="hidden" value="Computer has great cards. You start. You receive 25TCG credits for a win!" />');
			//Hard
			var hardContainer=ZA.createDiv(divOpts,"difficultyContainer","dc03");
			var div = ZA.createDiv(hardContainer,"difficultyOption","hard");
			$(div).attr('alt','3');
			$(div).html('Hard<input type="hidden" value="Computer has cards with best stats. Computer starts. You receive 30TCG credits for a win!" />');
			
			//info text
			var div = ZA.createDiv(gameMenuBg,"txtBlue","difficultyInfo");
			$(div).css({
				marginLeft:8,
				width:"90%",
				fontWeight:"bold"
			});
			$(div).html();
			
			//Event handlers
			$(".difficultyOption").hover(function(){
				$("#difficultyInfo").html($(this).find('input').val());
			},function(){
				$("#difficultyInfo").html('');
			});
			$(".difficultyOption").click(function(){
				var level = $(this).attr('alt');
				ZT.playerDifficulty = level;
				$("#gameScreensHolder").hide("scale",{percent:0},150);
				ZT.showMainboard();
			});
		
		//Back
		var divNav = ZA.createDiv(divScreen,"gameBack");
		$(divNav).click(function(){
			ZT.playerDifficulty = -1;
			ZT.showGameScreen('opponent','difficulty');
		});
		//Back to main menu
		var divMenuReturn = ZA.createDiv(divScreen,"gameMenuReturn");
		var divMenuItem = ZA.createDiv(divMenuReturn,"gameMenuItem_back");
		//$(divMenuItem).html('Return to main menu');
		$(divMenuItem).click(function(){
			ZT.showGameScreen('menu','difficulty');
		});

};


WORK_Toptrumps.prototype.showGameScreen=function(divShowId,divHideId)
{
	$(".gameScreen[id='"+divHideId+"']").hide("scale",{percent:0},150,function(){
		$(".gameScreen[id='"+divShowId+"']").show("scale",{percent:100},150);
	});
};


WORK_Toptrumps.prototype.buildDeckScreen=function(gameScreen)
{
	if(typeof(gameScreen) == "undefined"){
		gameScreen = 'category';
	}
	//Deck screen
	if($(".gameScreen[id='deck']").length){
		$(".gameScreen[id='deck']").remove();
	}
	var divScreen = ZA.createDiv(ZT.divGameScreens,"gameScreen","deck");
	var gameMenuBg=ZA.createDiv(divScreen,"gameMenuBg");
		
			
			//display selected category
			var div = ZA.createDiv(gameMenuBg);
			$(div).css({
				position:"relative",
				marginBottom:5
			});
			$(div).html(
				'<table style="width:100%" align="center">'+
					'<tr>'+
						'<td style="width:50%" align="right">CATEGORY:</td>'+
						'<td style="width:50%" align="left"><strong>'+$(".categoryOptions").find(".categoryOptionSelected").attr('title')+'</strong></td>'+
					'</tr>'+
				'</table>'
			);
			
			// Get and display user's decks
			var divDecksHolder = ZA.createDiv(gameMenuBg,"userDecksHolder");
			
			var divDecks = ZA.createDiv(divDecksHolder,"divDeckBook");
			
			var deckCount = 0;
			for(i=0; i<parseInt(ZA.getXML(ZD.sXML,"deckcount")); i++){
				if(ZA.getXML(ZD.sXML,"decks/deck_"+i+"/categoryid") == ZT.playerCategoryID){
					var cardcount = parseInt(ZA.getXML(ZD.sXML,"decks/deck_"+i+"/cardcount"));
					if(cardcount == 10){
						var divDeckHolder = ZA.createDiv(divDecks,"deckOptionBlockHolder",ZA.getXML(ZD.sXML,"decks/deck_"+i+"/index"),"div");
						var divDeck = ZA.createDiv(divDeckHolder,"deckOptionBlock");
						$(divDeck).css({
							backgroundImage:"url("+ZA.getXML(ZD.sXML,"decks/deck_"+i+"/image")+")",
							backgroundSize: "80%",
							backgroundRepeat: "no-repeat",
						});
						var div = ZA.createDiv(divDeckHolder,"deckName");
						$(div).css({
							bottom:0,
							paddingTop:5,
							paddingBottom:5,
							width:100,
							cursor:"inherit",
						});
						$(div).html(ZA.getXML(ZD.sXML,"decks/deck_"+i+"/description")+" ("+ZA.getXML(ZD.sXML,"decks/deck_"+i+"/ranking")+")");
						// var div = ZA.createDiv(divDeckHolder);
						// $(div).css({
							// color:"#fff",
							// top:4,
							// right:2,
							// cursor:"inherit",
							// fontWeight:"bold",
						// });
						// $(div).html(ZA.getXML(ZD.sXML,"decks/deck_"+i+"/ranking"));
						deckCount++;
					}
				}
			}
			var nodecks = false;
			if(deckCount == 0){
				$(divDecks).html('<div style="margin-top:40px;margin-left:20px;width:300px;">No full decks found. Your 10 highest ranked cards will play.</div>');
				ZT.deckPages = 1;
				ZT.playerDeckID = -1;
				nodecks = true;
			}
			else{
				ZT.deckPages = Math.ceil(deckCount/4);
			}
			$(divDecks).css({
				width:(456*ZT.deckPages)
			});
			
			//Event Handler
			$(".deckOptionBlockHolder").click(function(){
				var id = $(this).attr('id');
				ZT.playerDeckID = id;
				$(".gameNext[id='opponent']").hide();
				ZT.playerOpponent = -1;
				ZT.showGameScreen('opponent','deck');
			});
			
			//Scroller
			var divScroll = ZA.createDiv(gameMenuBg,"divscroll","","div");
			$(divScroll).css({
				width:"100%",
				height:20,
				left:0,
				bottom:6
			});
			var divArrowLeft = ZA.createDiv(divScroll,"divarrowleft","","div");
			$(divArrowLeft).css({
				background:"url(_site/all.png) -180px -60px no-repeat",
				width:20,
				height:20,
				left:10,
				bottom:0,
				cursor:"pointer"
			});
			var iWidth = (ZT.deckPages*14);
			var iOffset = parseInt($(divScroll).css('width'),10)/2 - (iWidth/2) + 4;
			var divPageIconsHolder = ZA.createDiv(divScroll,"","","div");
			$(divPageIconsHolder).css({
				height:13,
				width:iWidth,
				bottom:0,
				left:iOffset,
			});
			for(i=0;i<ZT.deckPages;i++){
				var divPageIcon = ZA.createDiv(divPageIconsHolder,"divpagebutton",i.toString(),"div");
				if(i==0){
					$(divPageIcon).addClass('dotselected');
				}
			}
			$(".divpagebutton").click(function(){
				if(!$(this).hasClass('dotselected')){
					var current_i = $(this).attr('id');
					ZT.currentPage=current_i;
					ZT.gotoPage(current_i);
				}
			});
			var divArrowRight = ZA.createDiv(divScroll,"divarrowright","","div");
			$(divArrowRight).css({
				background:"url(_site/all.png) -260px -60px no-repeat",
				width:20,
				height:20,
				right:10,
				bottom:0,
				cursor:"pointer"
			});
			
			//Scroll buttons click handlers
			
			$(divArrowLeft).click(function(){
				if(ZT.currentPage > 0){
					ZT.currentPage--;
					ZT.gotoPage(ZT.currentPage);
				}
			});
			$(divArrowRight).click(function(){
				if(ZT.currentPage < ZT.deckPages-1){
					ZT.currentPage++;
					ZT.gotoPage(ZT.currentPage);
				}
			});
		
		//Back
		var divNav = ZA.createDiv(divScreen,"gameBack");
		if(gameScreen == 'menu'){
			$(divNav).hide();
		}
		$(divNav).click(function(){
			$(".categoryOption").removeClass('categoryOptionSelected');
			ZT.showGameScreen('category','deck');
		});
		//Next
		var divNav = ZA.createDiv(divScreen,"gameNext","deck");
		if(!nodecks){
			$(divNav).hide();
		}
		$(divNav).click(function(){
			$(".gameNext[id='opponent']").hide();
			$(".versusOptionBlock").removeClass('versusOptionSelected');
			ZT.playerDeckID = -1;
			ZT.showGameScreen('opponent','deck');
		});
		//Back to main menu
		var divMenuReturn = ZA.createDiv(divScreen,"gameMenuReturn");
		var divMenuItem = ZA.createDiv(divMenuReturn,"gameMenuItem_back");
		//$(divMenuItem).html('Return to main menu');
		$(divMenuItem).click(function(){
			ZT.showGameScreen('menu','deck');
		});
	
	ZT.showGameScreen('deck',gameScreen);
};


WORK_Toptrumps.prototype.buildLoadScreen=function()
{
	//Load game screen
	if($(".gameScreen[id='load']").length){
		$(".gameScreen[id='load']").remove();
	}
	ZA.callAjax(ZT.sURL+'?savedgames=1',function(xml)
	{
		var divScreen = ZA.createDiv(ZT.divGameScreens,"gameScreen","load");
		var gameMenuBg = ZA.createDiv(divScreen,"gameMenuBg");
		
		var divGames = ZA.createDiv(gameMenuBg,"savedGames");
		
		//saved games
		var i;
		var iLength = parseInt(ZA.getXML(xml,"count"));
		if(iLength > 0)
		{
			var gamecounter = 0;
			for(i=0; i<iLength; i++){
				var game_id = ZA.getXML(xml,"game_"+i+"/game_id");
				var category_id = ZA.getXML(xml,"game_"+i+"/category_id");
				var active = ZA.getXML(xml,"game_"+i+"/active");
				var gameover = ZA.getXML(xml,"game_"+i+"/over");
				var opponent_type = ZA.getXML(xml,"game_"+i+"/opponent/type");
				var opponent = ZA.getXML(xml,"game_"+i+"/opponent/name");
				var scores = ZA.getXML(xml,"game_"+i+"/score")+'-'+ZA.getXML(xml,"game_"+i+"/draw")+'-'+ZA.getXML(xml,"game_"+i+"/opponent/score");
				if( (gameover=='0') || (gameover=='1' && active=='0') )
				{
					var div = ZA.createDiv(divGames,"savedGameOption");
					$(div).html(ZA.getXML(xml,"game_"+i+"/date_start")+' - '+ZA.getXML(xml,"game_"+i+"/category")+' VS '+opponent+' ('+scores+')');
					$(div).attr('id',game_id);
					$(div).attr('alt',category_id);
					if(opponent_type=="0"){
						$(div).addClass('cpu');
					}
					if(gameover=='1'){
						var div = ZA.createDiv(div,"gameOver");
						ZT.viewEnd = '1';
					}
					else
					{
						if(active=='1'){
							var div = ZA.createDiv(div,"activeDot");
						}
					}
					gamecounter++;
				}
			}
			if(gamecounter > 0)
			{
				$(".savedGameOption").click(function(){
					if(!$(this).hasClass('txtGreen')){
						$(".savedGameOption").removeClass('txtGreen');
						$(this).addClass('txtGreen');
						ZT.playerGameID = $(this).attr('id');
						ZT.playerCategoryID = $(this).attr('alt');
						$(".gameNextPlay[id='load']").show('fast');
						if($(this).hasClass('cpu')){
							ZT.playerOpponent = '0';
						}
						else{
							ZT.playerOpponent = '1';
						}
					}
				});
			}
			else
			{
				var div = ZA.createDiv(divGames);
				$(div).css({
					position:"relative"
				});
				$(div).html('No saved games found.');
			}
		}
		else
		{
			var div = ZA.createDiv(divGames);
			$(div).css({
				position:"relative"
			});
			$(div).html('No saved games found.');
		}
		
		//Next
		var divNav = ZA.createDiv(divScreen,"gameNextPlay","load");
		$(divNav).hide();
		$(divNav).click(function(){
			$("#gameScreensHolder").hide("scale",{percent:0},150,function(){
				ZT.showMainboard();
			});
		});
		//Back to main menu
		var divMenuReturn = ZA.createDiv(divScreen,"gameMenuReturn");
		var divMenuItem = ZA.createDiv(divMenuReturn,"gameMenuItem_back");
		//$(divMenuItem).html('Return to main menu');
		$(divMenuItem).click(function(){
			ZT.showGameScreen('menu','load');
		});
		
		ZT.showGameScreen('load','menu');
	});
};


WORK_Toptrumps.prototype.buildPlayerScreen=function()
{
	//Waiting screen
	if($(".gameScreen[id='player']").length){
		$(".gameScreen[id='player']").remove();
	}
	var divScreen = ZA.createDiv(ZT.divGameScreens,"gameScreen","player");
	var gameMenuBg=ZA.createDiv(divScreen,"gameMenuBg");
		
		
		var div = ZA.createDiv(gameMenuBg);
		$(div).css({
			position:"relative",
			marginBottom:10
		});
		var chosenDeck;
		if(ZT.playerDeckID > -1){
			chosenDeck = ZA.getXML(ZD.sXML,"decks/deck_"+ZT.playerDeckID+"/description")+' ('+
						 ZA.getXML(ZD.sXML,"decks/deck_"+ZT.playerDeckID+"/ranking")+')';
		}
		else{
			chosenDeck = 'Top 10 Cards ('+ZA.getXML(ZD.sXML,"top10")+')';
		}
		$(div).html(
			'<table style="width:100%" align="center">'+
				'<tr>'+
					'<td style="width:50%" align="right">CATEGORY:</td>'+
					'<td style="width:50%" align="left"><strong>'+$(".categoryOptions").find(".categoryOptionSelected").attr('title')+'</strong></td>'+
				'</tr><tr>'+
					'<td style="width:50%" align="right">CHOSEN DECK:</td>'+
					'<td style="width:50%" align="left"><strong>'+chosenDeck+'</strong></td>'+
				'</tr>'+
			'</table>'
		);
		
		var divOpp = ZA.createDiv(gameMenuBg,"","gameOpponent");
		$(divOpp).css({
			position:"relative",
			width:"90%",
			height:80,
			marginTop:10,
			paddingTop:5,
			paddingBottom:10,
			marginLeft:"auto",
			marginRight:"auto",
			border:"1px solid #ccc"
		});
		$(divOpp).html('<div style="position:relative;margin-top:25px;">Searching for online players...<br /><img src="_site/busy2.gif" /></div>');
		
	//Back
	var divNav = ZA.createDiv(divScreen,"gameBack");
	$(divNav).click(function(){
		ZT.stopLooking();
		ZT.showGameScreen('opponent','player');
	});
	//Next
	var divNav = ZA.createDiv(divScreen,"gameNextPlay","player");
	$(divNav).hide();
	$(divNav).click(function(){
		//another player found
		//play the game
		if(ZT.playerGameID != 0){
			$("#gameScreensHolder").hide("scale",{percent:0},150);
			ZT.showMainboard();
		}
	});
	//Back to main menu
	var divMenuReturn = ZA.createDiv(divScreen,"gameMenuReturn");
	var divMenuItem = ZA.createDiv(divMenuReturn,"gameMenuItem_back");
	//$(divMenuItem).html('Return to main menu');
	$(divMenuItem).click(function(){
		ZT.stopLooking();
		ZT.showGameScreen('menu','player');
	});
	
	ZT.showGameScreen('player','opponent');
	
	ZT.startLooking();
};


WORK_Toptrumps.prototype.startLooking=function()
{
	ZT.stopLooking();
	ZT.looker = setInterval("ZT.gameLooker()",5000);
};


WORK_Toptrumps.prototype.stopLooking=function()
{
	clearInterval(ZT.looker);
	ZT.looker = null;
};


WORK_Toptrumps.prototype.gameLooker=function()
{
	var deckID = -1;
	if(ZT.playerDeckID > -1){
		deckID = ZA.getXML(ZD.sXML,"decks/deck_"+ZT.playerDeckID+"/deckid");
	}
	ZA.callAjax(ZT.sURL+'?onlinegame=1&category='+ZT.playerCategoryID+'&deck='+deckID,function(xml){
		var status = ZA.getXML(xml,"status");
		if(status == 'ready'){
			ZT.stopLooking();
			//Game found and joined
			ZT.playerGameID = ZA.getXML(xml,"game");
			ZT.playerOpponent = '1'; //1 - player
			var opponent = ZA.getXML(xml,"opponent/username");
			var deckranking = ZA.getXML(xml,"opponent/deckranking");
			$("#gameOpponent").html(
				'<div style="position:relative;margin-top:30px;">Found online player:<br />'+
				'<strong>'+opponent+' ('+deckranking+')'+'</strong></div>'
			);
			$(".gameNextPlay[id='player']").show('fast');
		}
		else if(status == 'invite'){
			ZT.stopLooking();
			//Found a user that invited this user for a game
			//This user can accept or reject
			ZT.playerGameID = ZA.getXML(xml,"game");
			ZT.playerOpponent = '1'; //1 - player
			var opponent = ZA.getXML(xml,"opponent/username");
			var deckranking = ZA.getXML(xml,"opponent/deckranking");
			$("#gameOpponent").html(
				'<div style="position:relative;margin-top:25px;">Invite from player:<br />'+
				'<strong>'+opponent+' ('+deckranking+')'+'</strong></div>'+
				'<div class="cmdButton" id="gameDecline" style="bottom:5px;left:5px;" alt="'+ZT.playerGameID+'">Decline</div>'+
				'<div class="cmdButton" id="gameAccept" style="bottom:5px;right:5px;" alt="'+ZT.playerGameID+'">Accept</div>'
			);
			//click event handlers
			$("#gameDecline").click(function(){
				var id = $(this).attr('alt');
				ZA.callAjax(ZT.sURL+'?decline=1&game='+id,function(xml){
					$("#gameOpponent").html('<div style="position:relative;margin-top:25px;">Searching for online players...<br /><img src="_site/busy2.gif" /></div>');
					ZT.startLooking();
				});
			});
			$("#gameAccept").click(function(){
				var id = $(this).attr('alt');
				ZA.callAjax(ZT.sURL+'?accept=1&game='+id+'&deck='+deckID,function(xml){
					//Game found and joined
					ZT.playerGameID = ZA.getXML(xml,"game");
					ZT.playerOpponent = '1'; //1 - player
					var opponent = ZA.getXML(xml,"opponent/username");
					var deckranking = ZA.getXML(xml,"opponent/deckranking");
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


WORK_Toptrumps.prototype.buildFriendScreen=function()
{
	//Waiting screen
	if($(".gameScreen[id='friend']").length){
		$(".gameScreen[id='friend']").remove();
	}
	var divScreen = ZA.createDiv(ZT.divGameScreens,"gameScreen","friend");
	var gameMenuBg=ZA.createDiv(divScreen,"gameMenuBg");
		
		var div = ZA.createDiv(gameMenuBg);
		$(div).css({
			position:"relative"
		});
		var chosenDeck;
		if(ZT.playerDeckID > -1){
			chosenDeck = ZA.getXML(ZD.sXML,"decks/deck_"+ZT.playerDeckID+"/description")+' ('+
						 ZA.getXML(ZD.sXML,"decks/deck_"+ZT.playerDeckID+"/ranking")+')';
		}
		else{
			chosenDeck = 'Top 10 Cards ('+ZA.getXML(ZD.sXML,"top10")+')';
		}
		$(div).html(
			'<table style="width:100%" align="center">'+
				'<tr>'+
					'<td style="width:50%" align="right">CATEGORY:</td>'+
					'<td style="width:50%" align="left"><strong>'+$(".categoryOptions").find(".categoryOptionSelected").attr('title')+'</strong></td>'+
				'</tr><tr>'+
					'<td style="width:50%" align="right">CHOSEN DECK:</td>'+
					'<td style="width:50%" align="left"><strong>'+chosenDeck+'</strong></td>'+
				'</tr>'+
			'</table>'
		);
		
		var divSearch = ZA.createDiv(gameMenuBg);
		$(divSearch).css({
			position:"relative",
			width:"90%",
			height:90,
			marginTop:10,
			paddingTop:5,
			paddingBottom:10,
			marginLeft:"auto",
			marginRight:"auto",
			border:"1px solid #ccc"
		});
		
		var divForm = ZA.createDiv(divSearch,"","friendSearchForm");
		$(divForm).css({width:"100%",paddingTop:10});
		$(divForm).html(
			'<div style="position:relative;margin-bottom:5px;">Find Friend:</div>'+
			'<div style="position:relative;margin-bottom:10px;"><input type="text" id="txtFriend" style="width:190px;" /></div>'+
			'<div class="cmdButton" id="cmdFindFriend" style="left:132px;">Search</div>'
		);
		
		var divResults = ZA.createDiv(divSearch,"","friendSearchResults");
		$(divResults).css({width:"100%",height:100});
		$(divResults).html(
			'<div style="position:relative;margin-bottom:5px;font-weight:bold;">Search Results:</div>'+
			'<div id="friendSearchResultsList" style="position:relative;margin-bottom:5px;height:50px;width:90%;margin-left:auto;margin-right:auto;overflow-y:auto;"></div>'+
			'<div class="cmdButton" id="searchAgain" style="bottom:5px;left:5px;">Search again</div>'+
			'<div style="bottom:0px;right:0px;width:100px;display:none;" id="inviteFriendHolder"><div class="cmdButton" id="inviteFriend" style="bottom:5px;right:5px;">Invite friend</div></div>'
		);
		$(divResults).hide();
		
		var divWaiting = ZA.createDiv(divSearch,"","friendSearchWaiting");
		$(divWaiting).css({width:"100%",height:100});
		$(divWaiting).html(
			'<div style="position:relative;margin-top:25px;">Waiting for <span id="friendUsername" style="font-weight:bold;"></span> to join...<br /><img src="_site/busy2.gif" /></div>'+
			'<div class="cmdButton" id="cancelGame" style="bottom:10px;left:115px;">Cancel game</div>'
		);
		$(divWaiting).hide();
		
		$("#searchAgain").click(function(){
			$("#friendSearchResults").hide('fast');
			$("#friendSearchForm").show('fast');
		});
		
		$("#inviteFriend").click(function(){
			var id = $("#friendSearchResultsList").find(".txtGreen").attr('alt');
			$("#friendSearchResults").hide('fast');
			$("#friendUsername").html($("#friendSearchResultsList").find(".txtGreen").html());
			$("#friendSearchWaiting").show('fast');
			setTimeout("ZT.startFriending()",1000);
			ZT.gameFriender();
		});
		
		$("#cancelGame").click(function(){
			$("#friendSearchWaiting").hide('fast');
			$("#friendSearchForm").show('fast');
			ZT.stopFriending();
		});
		
		$("#txtFriend").keydown(function(event){
			if(event.which == 13){
				$("#cmdFindFriend").click();
			}
		});
		
		$("#cmdFindFriend").click(function(){
			var searchstring = $("#txtFriend").val().trim();
			$("#txtFriend").val(searchstring);
			//validation
			if(searchstring.length < 1){
				var response = "Please enter a friend's username.";
				var icon = "-697px -63px";
				ZA.showWindow(icon,response);
				$("#txtFriend").focus();
				return false;
			}
			else{
				ZA.callAjax(ZT.sURL+"?search=1&friend="+searchstring,function(xml){
					var results = parseInt(ZA.getXML(xml,"found"));
					if(results > 0)
					{
						if(results > 1)
						{
							//found more than 1 possibility
							$("#friendSearchResultsList").empty();
							var i = 0;
							for(i=0; i<results; i++)
							{
								$("#friendSearchResultsList").append('<div class="searchResult" alt="'+ZA.getXML(xml,"results/result_"+i+"/user_id")+'">'+ZA.getXML(xml,"results/result_"+i+"/username")+'</div>');
							}
						}
						else
						{
							//found the friend
							var i = 0;
							$("#friendSearchResultsList").html('<div class="searchResult" alt="'+ZA.getXML(xml,"results/result_"+i+"/user_id")+'">'+ZA.getXML(xml,"results/result_"+i+"/username")+'</div>');
						}
						//click event handler
						$(".searchResult").unbind().click(function(){
							if(!$(this).hasClass('txtGreen')){
								ZT.playerFriendID = $(this).attr('alt');
								ZT.playerFriend = $(this).html();
								$(".searchResult").removeClass('txtGreen');
								$(this).addClass('txtGreen');
								$("#inviteFriendHolder").hide().show('fast');
							}
						});
					}
					else
					{
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
		
	//Back
	var divNav = ZA.createDiv(divScreen,"gameBack");
	$(divNav).click(function(){
		ZT.stopFriending();
		ZT.showGameScreen('opponent','friend');
	});
	//Next
	var divNav = ZA.createDiv(divScreen,"gameNextPlay","friend");
	$(divNav).hide();
	$(divNav).click(function(){
		//friend found
		//play the game
		if(ZT.playerGameID != 0){
			$("#gameScreensHolder").hide("scale",{percent:0},150);
			ZT.showMainboard();
		}
	});
	//Back to main menu
	var divMenuReturn = ZA.createDiv(divScreen,"gameMenuReturn");
	var divMenuItem = ZA.createDiv(divMenuReturn,"gameMenuItem_back");
	//$(divMenuItem).html('Return to main menu');
	$(divMenuItem).click(function(){
		ZT.stopFriending();
		ZT.showGameScreen('menu','friend');
	});
	
	ZT.showGameScreen('friend','opponent');
};


WORK_Toptrumps.prototype.startFriending=function()
{
	ZT.stopFriending();
	ZT.friender = setInterval("ZT.gameFriender()",10000);
};


WORK_Toptrumps.prototype.stopFriending=function()
{
	clearInterval(ZT.friender);
	ZT.friender = null;
};


WORK_Toptrumps.prototype.gameFriender=function()
{
	var deckID = -1;
	if(ZT.playerDeckID > -1){
		deckID = ZA.getXML(ZD.sXML,"decks/deck_"+ZT.playerDeckID+"/deckid");
	}
	ZA.callAjax(ZT.sURL+'?friendgame=1&username='+ZT.playerFriend+'&category='+ZT.playerCategoryID+'&deck='+deckID,function(xml){
		var status = ZA.getXML(xml,"status");
		if(status == 'ready'){
			//Game found and joined
			ZT.stopFriending();
			ZT.playerGameID = ZA.getXML(xml,"game");
			ZT.playerOpponent = '1'; //1 - player
			var opponent = ZA.getXML(xml,"opponent/username");
			var deckranking = ZA.getXML(xml,"opponent/deckranking");
			$("#friendSearchWaiting").html(
				'<div style="position:relative;margin-top:30px;">Friend has joined the game:<br />'+
				'<strong>'+opponent+' ('+deckranking+')</strong></div>'
			);
			$(".gameNextPlay[id='friend']").show('fast');
		}
		else if(status == 'declined')
		{
			//friend has declined invitation
			ZT.stopFriending();
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
					ZT.stopFriending();
				});
			});
		}
		else
		{
			//waiting for friend to join game
		}
	});
};


//**************************************************************************************************************************
//**************************************************************************************************************************
//**************************************************************************************************************************
// GAME MAINBOARD
//**************************************************************************************************************************
//**************************************************************************************************************************
//**************************************************************************************************************************


WORK_Toptrumps.prototype.showMainboard=function()
{
	shuffle=function(v){
    	for(var j, x, i = v.length; i; j = parseInt(Math.random() * i), x = v[--i], v[i] = v[j], v[j] = x);
    	return v;
	};
	
	var deckId = '';
	if(!ZA.sUsername)
	{
		//no user logged in
		deckId = '0';
		ZT.playerCategoryID = '2';
		ZT.playerOpponent = '0';
		ZT.playerDifficulty = '1';
	}
	else
	{
		//user logged in
		if(ZT.playerDeckID != -1){
			deckId = ZA.getXML(ZD.sXML,"decks/deck_"+ZT.playerDeckID+"/deckid");
		}
		else{
			deckId = ZT.playerDeckID;
		}
	}
	
	//INIT THE GAME
	ZA.callAjax(ZT.sURL+'?init=1&game='+ZT.playerGameID+'&opponent='+ZT.playerOpponent+'&category='+ZT.playerCategoryID+'&deck='+deckId+'&difficulty='+ZT.playerDifficulty,function(tXML)
	{
		ZT.playerGameID = ZA.getXML(tXML,"game");
		ZT.gameStatus = "incomplete";
		ZT.playerDifficulty = ZA.getXML(tXML,"difficulty");
		ZT.playerLog = ZA.getXML(tXML,"log");
		ZA.callAjax(ZT.sURL+'?cards=1&category='+ZT.playerCategoryID,function(cXML)
		{
			//Build cardsArray - array holding all available cards in system for category
			ZT.cardsXML = cXML;
			var cardcount = parseInt(ZA.getXML(cXML,"cardcount"));
			for(i=0; i<cardcount; i++)
			{
				ZT.cardsArray[ZA.getXML(cXML,"card_"+i+"/cardid")] = i;
			}
			
			//Mainboard Container
			var divHolder = ZA.createDiv(ZT.divData,"","mainboardHolder");
			$(divHolder).hide();
			var scoreBlockBg=ZA.createDiv(divHolder,"scoreBgDiv");
			var scoreHolder=ZA.createDiv(scoreBlockBg,"scoreHolder");
			
			
			//PLAYER 1 AREA
			
			//var divP1 = ZA.createDiv(divHolder,"player1Area");

			//$(divP1).html('<div style="width:100%;height:100%;opacity:0.5;background:black;"></div>');
			
				//Player 1 avatar
				var divAvatar = ZA.createDiv(divHolder,"playerAvatarHolder");
				$(divAvatar).css({
					left:15,
				});
				var div = ZA.createDiv(divAvatar,"playerAvatar");
				$(div).css({
					backgroundImage:"url(_site/gameboard_1.png)",
					backgroundRepeat:"no-repeat",
					backgroundPosition:"-210px -45px",
				});
				var div = ZA.createDiv(divAvatar,"playerName","1");
				$(div).html(ZA.getXML(tXML,"p1name"));
				var div = ZA.createDiv(divAvatar,"turnIndicator","1");
				$(div).css({
					left:0
				}).show();
				
				//Player 1 cards holder
				var divPlayer1Cards = ZA.createDiv(divHolder,"playerCards","1");
				$(divPlayer1Cards).css({
					left:15,
					bottom:0
				});
				
				//Create Player 1 cards array
				ZT.p1Cards = [];
				if(!ZA.sUsername)
				{
					//Get 10 random cards for Player 1
					var keepTrack = [];
					for(i=0; i<cardcount; i++){
						keepTrack[i]=0;
					}
					var id;
					var count;
					for(i=0; i<10; i++){
						do{
							id = ZT.getRandomNumber(0,cardcount-1);
							count = parseInt(keepTrack[id]);
						}
						while(count > 0); //only 1 copy a card allowed
						ZT.p1Cards[i] = id;
						keepTrack[id] = count+1;
					}
					//Shuffle P1 cards
					ZT.p1Cards = shuffle(ZT.p1Cards);
					
					//these are actual card_id's
					//ZT.p1Cards = [48,69,25,54,11,43,37,53,4,13];
				}
				else
				{/*
					var cards = ZA.getXML(tXML,"p1cards");
					if(cards.length > 0)
					{
						cards = cards.split(',');
						//Get P1 cards
						for(i=0; i<cards.length; i++){
							p1Cards[i] = ZT.cardsArray[cards[i]];
						}
					}*/
				}
				
				//Player 1 score
				if(ZA.sUsername){
					ZT.p1score = ZA.getXML(tXML,"p1score");
				}
				else{
					ZT.p1score = ZT.p1Cards.length;
				}
				var divScore = ZA.createDiv(scoreHolder);
				$(divScore).css({
					width:65,
					height:45,
					left:0,
					margin:"10px 0px 0px 5px",
					borderRight:"1px solid #8A8A8A",
				});
				$(divScore).html('<div id="score1"></div>');
				
				//Add P1 cards to stack
				for(i=0; i<parseInt(ZT.p1score); i++)
				{
					var id = ZT.p1Cards[i];
					ZT.gameCards[i] = ZA.getXML(cXML,"card_"+id+"/cardid");
					var div = ZA.createDiv(divPlayer1Cards,"gameCard",i.toString());
					$(div).css({
						background:"url(_site/back_small.jpg) no-repeat",
						left:(i*12),
					});
				}
			
			//PLAYER 2 AREA
			
			// var divP2 = ZA.createDiv(divHolder,"player2Area");
			// $(divP2).css({
				// width:525,
				// height:125,
				// right:5,
				// bottom:5,
			// });
			//$(divP2).html('<div style="width:100%;height:100%;opacity:0.5;background:black;"></div>');
			
				//Player 2 avatar
				var divAvatar = ZA.createDiv(divHolder,"playerAvatarHolder");
				$(divAvatar).css({
					right:15,
				});
				var iBgLeft = (ZT.playerOpponent.toString()=='0') ? '-131px' : '-210px';
				var div = ZA.createDiv(divAvatar,"playerAvatar");
				$(div).css({
					right:0,
					background:"#ccc url(_site/gameboard_1.png) no-repeat "+iBgLeft+" -45px"
				});
				$(div).html('<div id="p2loader"><img src="_site/loading.gif" /></div>');
				var div = ZA.createDiv(divAvatar,"playerName","2");
				$(div).css({right:0});
				$(div).html(ZA.getXML(tXML,"p2name"));
				var div = ZA.createDiv(divAvatar,"turnIndicator","2");
				$(div).css({
					right:0
				}).show();
				
				//Player 2 cards stack
				var divPlayer2Cards = ZA.createDiv(divHolder,"playerCards","2");
				$(divPlayer2Cards).css({
					right:8,
					bottom:0
				});
				
				//Create player 2 cards array
				ZT.p2Cards = [];
				if(!ZA.sUsername)
				{
					//Get 10 random cards for Player 2
					var keepTrack = [];
					for(i=0; i<cardcount; i++){
						keepTrack[i]=0;
					}
					var id;
					var count;
					for(i=0; i<10; i++){
						do{
							id = ZT.getRandomNumber(0,cardcount-1);
							count = parseInt(keepTrack[id]);
						}
						while(count > 0); //only 1 copy of a card allowed
						ZT.p2Cards[i] = id;
						keepTrack[id] = count+1;
					}
					//Shuffle P2 cards
					ZT.p2Cards = shuffle(ZT.p2Cards);
					//ZT.p2Cards = [57,71,36,47,48,4,67,25,43,33];
				}
				else
				{/*
					var cards = ZA.getXML(tXML,"p2cards");
					if(cards.length > 0)
					{
						cards = cards.split(',');
						//Get P2 cards
						for(i=0; i<cards.length; i++){
							p2Cards[i] = ZT.cardsArray[cards[i]];
						}
					}*/
				}
				
				//Player 2 score
				if(ZA.sUsername){
					ZT.p2score = ZA.getXML(tXML,"p2score");
				}
				else{
					ZT.p2score = ZT.p2Cards.length;
				}
				var divScore = ZA.createDiv(scoreHolder);
				$(divScore).css({
					width:68,
					height:45,
					right:0,
					margin:"10px 0px 0px 10px",
					borderLeft:"1px solid #B6B6B6",
				});
				$(divScore).html('<div id="score2"></div>');
			
				//Add P2 cards to stack
				for(var i=0; i<parseInt(ZT.p2score); i++)
				{
					var id = ZT.p2Cards[i];
					ZT.gameCards[i+parseInt(ZT.p1score)] = ZA.getXML(cXML,"card_"+id+"/cardid");
					var div = ZA.createDiv(divPlayer2Cards,"gameCard",(i+parseInt(ZT.p1score)).toString());
					$(div).css({
						background:"url(_site/back_small.jpg) no-repeat",
						right:(i*12),
					});
				}
				
			//Set active player
			ZT.activePlayer = ZA.getXML(tXML,"activeplayer");
			
			//Set game moves if multiplayer game
			if(ZT.playerOpponent != '0'){
				ZT.moves = parseInt(ZA.getXML(tXML,"moves"));
			}
			
			/*
			 * get list of all cards for game, in random order!
			 *
			if(ZA.sUsername){
				//
			}
			else{
				//
			}
			//Pre load the fullsize back images in the game in random order
			var cache = ZA.createDiv(ZT.divData);
			$(cache).css({
				top:0,
				left:2000,
				height:0,
				width:0,
				overflow:"hidden"
			}).hide();
			var gameCardsShuffled = [];
			for(var i=0; i<$(ZT.gameCards).size(); i++){
				gameCardsShuffled[i] = ZT.gameCards[i];
			}
			gameCardsShuffled = shuffle(gameCardsShuffled);
			for(var i=0; i<$(gameCardsShuffled).size(); i++){
				var backimage = ZA.getXML(ZT.cardsXML,"card_"+ZT.cardsArray[gameCardsShuffled[i]]+"/fullimage")+"_back.jpg";
				$(cache).append('<img src="'+backimage+'" />');
			}
			*/
			
			//PLAY AREA
			
			var divPlay = ZA.createDiv(divHolder,"","playArea");
			$(divPlay).css({
				width:"100%",
				height:675,
			});
				
				//Info Message
				var divInfo = ZA.createDiv(divPlay,"infoMessage");
				// var div = ZA.createDiv(divInfo);
				// $(div).css({
					// width:"100%",
					// height:"100%",
					// background:"#000",
					// opacity:0.5,
					// "-moz-border-radius":"5px",
					// "border-left":"1px solid #999",
					// "border-top":"1px solid #efefef",
					// "border-right":"1px solid #999",
					// "border-bottom":"1px solid #333"
				// });
				var div = ZA.createDiv(divInfo);
				$(div).css({
					position:"relative"
				});
				$(div).html(
					'<table><tr><td id="gameMessage">This is your message</td></tr></table>'
				);
				
			//Cards in play
				
				var divCards = ZA.createDiv(divPlay,"cardsInPlayHolder");
				
				var div = ZA.createDiv(divPlay,"roundResult","youWin");
				$(div).css({background:"url(_site/gameboard.png) -256px -140px no-repeat"});
				var div = ZA.createDiv(divPlay,"roundResult","youLose");
				$(div).css({background:"url(_site/gameboard.png) -394px -140px no-repeat"});
				var div = ZA.createDiv(divPlay,"roundResult","youDraw");
				$(div).css({background:"url(_site/gameboard.png) -394px 0px no-repeat"});
				
				//Player 1 Card
				var div = ZA.createDiv(divCards,"cardInPlayHolder");
				$(div).css({
					left:10
				});
				$(div).html(
					'<div class="blank"><img src="_site/back.jpg" /></div>'+
					'<img src="_site/back.jpg" class="back" id="1" style="display:none;" />'+
					'<img src="" class="playerCard" id="1" />'
				);
				
				//Player 2 Card
				var div = ZA.createDiv(divCards,"cardInPlayHolder");
				$(div).css({
					right:10
				});
				$(div).html(
					'<div class="blank"><img src="_site/back.jpg" /></div>'+
					'<img src="_site/back.jpg" class="back" id="2" style="display:none;" />'+
					'<img src="" class="playerCard" id="2" />'
				);
				
			//Cards Pool
				
				var divPoolHolder = ZA.createDiv(divCards,"cardsPoolHolder");
				$(divPoolHolder).css({
					width:64,
					height:90,
					bottom:115,
					left:340,
					"-moz-border-radius":2,
					display:"none"
				});
				var divPool = ZA.createDiv(divPoolHolder,"cardsPool");
				var div = ZA.createDiv(divPoolHolder);
				$(div).css({
					width:"100%",
					height:"100%",
					background:"url(_site/gameboard.png) -330px 0px",
				});
				var divSize = ZA.createDiv(divPoolHolder,"","poolSize");
				$(divSize).css({
					textShadow:"0px 1px 1px #d1d1d1",
					color:"#ca0000",
					fontSize:36,
					position:"relative",
					marginLeft:"auto",
					marginRight:"auto",
					top:40
				});
				
				//Add drawn cards to cards pool (load game only)
				if(ZA.sUsername)
				{
					var drawCards = [];
					if(ZA.getXML(tXML,"drawcards").length > 1)
					{
						var cards = ZA.getXML(tXML,"drawcards").split(',');
						if(cards.length > 1)
						{
							$(divSize).html(cards.length);
							for(i=0; i<cards.length; i++)
							{
								drawCards[i] = ZT.cardsArray[cards[i]];
							}
							var player = ZT.activePlayer;
							var index = ZT.p1score + ZT.p2score;
							for(i=0; i<drawCards.length; i++)
							{
								id = drawCards[i];
								var div = ZA.createDiv(divPool,"gameCard");
								$(div).css({
									background:"url(_site/back_small.jpg) no-repeat",
									top:0
								}).hide();
								$(div).attr('id', index.toString());
								$(div).attr('alt', ZA.getXML(cXML,"card_"+id+"/cardid"));
								index++;
							}
							$(divPoolHolder).show();
						}
					}
				}
				else
				{
					ZT.drawCards = [];
				}
			
			//Stat Selectors
			
				//Player 1 Stat Selector
				var divStatSel = ZA.createDiv(divCards,"statSelectorHolder","1","div");
				$(divStatSel).css({
					position:"absolute",
					width:250,
					height:340,
					left:21,
					top:34,
					display:"none"
				});
				//add individual selectors and indicators
				var statscount = parseInt(ZA.getXML(ZT.cardsXML,"allstats/statscount"));
				for(var i=0; i<statscount; i++){
					var div = ZA.createDiv(divStatSel,"statSelector",i.toString(),"div");
					$(div).css({
						top:ZA.getXML(ZT.cardsXML,"allstats/top_"+i)+'px',
						height:ZA.getXML(ZT.cardsXML,"allstats/height")+'px',
						width:248
					});
					$(div).html(
						'<div class="statIndicator" style="right:-35px;"></div>'+
						'<div class="statSelectIndicator" style="right:-35px;"></div>'+
						'<div class="statSelectedIndicator" style="right:-35px;"></div>'
					);
					$(div).hover(function(){
						if($(this).hasClass('noselect')){
							return false;
						}
						$(this).find(".statSelectIndicator").show();
					},
					function(){
						$(this).find(".statSelectIndicator").hide();
					});
				}
				
				//Player 2 Stat Selector
				var divStatSel = ZA.createDiv(divCards,"statSelectorHolder","2","div");
				$(divStatSel).css({
					position:"absolute",
					width:250,
					height:240,
					right:21,
					top:34,
					display:"none"
				});
				//add individual selectors and indicators
				var statscount = parseInt(ZA.getXML(ZT.cardsXML,"allstats/statscount"));
				for(var i=0; i<statscount; i++){
					var div = ZA.createDiv(divStatSel,"statSelector",i.toString(),"div");
					$(div).css({
						top:ZA.getXML(ZT.cardsXML,"allstats/top_"+i)+'px',
						height:ZA.getXML(ZT.cardsXML,"allstats/height")+'px',
						width:248
					})
					.addClass('noselect');
					$(div).html(
						'<div class="statIndicator" style="left:-35px;"></div>'+
						'<div class="statSelectIndicator" style="left:-35px;"></div>'+
						'<div class="statSelectedIndicator" style="left:-35px;"></div>'
					);
					$(div).hover(function(){
						if($(this).hasClass('noselect')){
							return false;
						}
						$(this).find(".statSelectIndicator").show();
					},
					function(){
						$(this).find(".statSelectIndicator").hide();
					});
				}
			
			//Menu button
			var divButton = ZA.createDiv(divPlay,"gameMenuButton");
			$(divButton).hide()
			.click(function(){
				ZT.showMenu();
			});
			
			//Continue button - for when game is over
			var divButton = ZA.createDiv(divPlay,"gameContinueButton");
			$(divButton).click(function(){
				if(!ZA.sUsername){
					$(ZT.divData).html('');
					ZT.divData = 0;
					ZA.maximizeWindowA(ZT.iComponentNo);
				}
				else{
					ZT.stopListening();
					$("#mainboardHolder").remove();
					ZT.showGameMenu();
				}
			});
			
			//Start the Game
			$("#mainboardHolder").show( "scale", {percent:100}, 500, function(){
				ZT.nextRound();
			});
/*
 *
 * DEV CHEAT
 *  
 *
$(".turnIndicator").dblclick(function(){
	
	if($(this).attr('id')=='1'){
		ZT.playerDifficulty = '3';
		ZA.callAjax(ZT.sURL+"?choosestat="+ZT.p1card+"&cheat=1&difficulty="+ZT.playerDifficulty,function(html){
			alert(html);
		});
	}
	else
	{
		ZA.callAjax(ZT.sURL+"?choosestat="+ZT.p2card+"&cheat=1&difficulty="+ZT.playerDifficulty,function(html){
			alert(html);
		});
	}
	/*
	ZA.callAjax(ZT.sURL+"?gamelog=1&game="+ZT.playerGameID,function(xml){
		alert(xml);
	});
	*
});
 *
 * 
 * 
 * 
 */
		});
		
	});
};


/**
=============================================================================
IN-GAME MENU CLASS
*/
function WORK_MenuWindow()
{
	if(typeof WORK_MenuWindow._iInited=="undefined")
	{
		/*********** close in-game menu window */
		WORK_MenuWindow.prototype.clickClose=function(){
			return function() {
				ZT.aMenu.clickCloseA();
			};
		};
		
		/*********** close in-game menu window action */
		WORK_MenuWindow.prototype.clickCloseA=function()
		{
			var divBody=document.getElementsByTagName("body")[0];
			var divCloak=document.getElementById("bodycloak_0");
			var divMenuWindow=document.getElementById("windowcontainer_0");
			var divData=document.getElementById("window_0");
			if (divMenuWindow) {
				divBody.removeChild(divMenuWindow);
				divBody.removeChild(divData);
			}
			if (divCloak) {
				divBody.removeChild(divCloak);
			}
			ZT.paused = false;
		};
		
		/*********** create in-game menu window */
		WORK_MenuWindow.prototype.create=function(ID)
		{
			ZT.paused = true;
			
			var divBody=document.getElementsByTagName("body")[0];
			var iDocHeight=document.documentElement.scrollHeight;
			ZA.createWindowPopup(0,"MenuWindow",305,255,1,0);
			var divData=document.getElementById("window_0");
			var iTop=0;
			var iLeftL=105;
			var iLeftR=190;
			
			//Title
			var div = ZA.createDiv(divData,"gameScreenTitle");
			$(div).css({
				background:"transparent url(_site/gameboard.png) -860px -660px no-repeat",
				width:120,
				marginTop:20,
				marginBottom:10
			});
			
			var divMenuItems = ZA.createDiv(divData);
			$(divMenuItems).css({
				position:"relative",
				width:248,
				marginLeft:20
			});
			
			//Quit game
			var divMenuItem = ZA.createDiv(divMenuItems,"gameMenuItem");
			$(divMenuItem).html('Quit Game');
			$(divMenuItem).click(function(){
				if(!ZA.sUsername){
					$(ZT.divData).empty();
					ZT.divData = 0;
					ZA.maximizeWindowA(ZT.iComponentNo);
				}
				else{
					ZT.stopListening();
					$("#mainboardHolder").remove();
					ZT.showGameMenu();
				}
				ZT.aMenu.clickCloseA();
			});
		
			//Forfeit game
			/*
			var divMenuItem = ZA.createDiv(divMenuItems,"gameMenuItem");
			$(divMenuItem).html('Forfeit Game');
			$(divMenuItem).click(function(){
				if(confirm("WARNING: You are about to forfeit the game, which is the same as losing!\nPress OK to forfeit.")){
					if(ZA.sUsername){
						ZT.forfeitGame();
					}
					else{
						$(ZT.divData).empty();
						ZT.divData = 0;
						ZT.aMenu.clickCloseA();
						ZA.maximizeWindowA(ZT.iComponentNo);
					}
				}
			});
			*/
			
			//Return to game
			var divMenuItem = ZA.createDiv(divMenuItems,"gameMenuItem");
			$(divMenuItem).html('Return to game');
			$(divMenuItem).click(function(){
				ZT.aMenu.clickCloseA();
			});
		};
		
		WORK_MenuWindow.prototype.closeError=function(){
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
	
		WORK_MenuWindow.prototype.closeFirstVisit=function(){
			ZA.refreshBrowser();
		};
	
		WORK_MenuWindow._iInited=1;
	}
};
/** 
=============================================================================
finish IN-GAME MENU CLASS
*/


//**************************************************************************************************************************
//**************************************************************************************************************************
//**************************************************************************************************************************
// GAMEPLAY FUNCTIONS
//**************************************************************************************************************************
//**************************************************************************************************************************
//**************************************************************************************************************************


WORK_Toptrumps.prototype.showMenu=function()
{
	ZT.aMenu = new WORK_MenuWindow();
	ZT.aMenu.create();
};


WORK_Toptrumps.prototype.forfeitGame=function()
{
	ZA.callAjax(ZT.sURL+"?forfeit=1&game="+ZT.playerGameID,function(xml){
		ZT.stopListening();
		var result = ZA.getXML(xml,"result");
		$(".gameMenuButton").hide('fast');
		$("#gameMessage").html(ZA.getXML(xml,"message"));
		$("#p2loader").hide();
		ZT.aMenu.clickCloseA();
		ZT.forfeitAnimation(result);
	});
};


WORK_Toptrumps.prototype.forfeitAnimation=function(result)
{
	//show cards pool
	if(result=='0'){
		//opponent has forfeited the game
		var quitter = '2';
		var winner = '1';
		var left = -427;
	}
	else{
		//this player has forfeited the game
		var quitter = '1';
		var winner = '2';
		var left = 427;
	}
	//move winner's card in play back to stacks
	$(".statSelectorHolder").hide();
	var p = winner;
	var card = $(".playerCards[id='"+p+"']").find('.gameCard').last();
	// $(".playerCard[id='"+p+"']").animate({
// 		
	// })
	$(".playerCard[id='"+p+"']").animate({
		width:10,
		height:350,
		left:434
	},
	150,
	function(){
		$(".playerCard[id='"+p+"']").hide();
		//show inactive player's generic back
		$(".back[id='"+p+"']").show()
		.animate({
			width:250,
			height:350,
			left:10
		},
		150,
		function(){
			//hide player's generic back and show top card (stretched)
			$(".back[id='"+p+"']").hide();
			card.show();
			//move incative player's card to draw pool
			card.addClass('topAuto').animate({
				rotate:'0deg', 
				scale:'1',
				left:0,
				bottom:(parseInt(card.prev().css('bottom'),10) + 12)
			},
			500,
			function(){
				ZT.updatePlayer(winner);
				//move quiter's cards in play back to stack
				p = quitter;
				card = $(".playerCards[id='"+p+"']").find('.gameCard').last();
				$(".playerCard[id='"+p+"']").animate({
					width:10,
					height:350,
					left:434
				},
				150,
				function(){
					$(".playerCard[id='"+p+"']").hide();
					//show inactive player's generic back
					$(".back[id='"+p+"']").show()
					.animate({
						width:250,
						height:350,
						left:10
					},
					150,
					function(){
						//hide player's generic back and show top card (stretched)
						$(".back[id='"+p+"']").hide();
						card.show();
						//move incative player's card to draw pool
						card.addClass('topAuto').animate({
							rotate:'0deg',
							scale:'1',
							left:0,
							bottom:(parseInt(card.prev().css('bottom'),10) + 12)
						},
						500,
						function(){
							ZT.updatePlayer(quitter);
							//move all quitter's cards to draw pool
							$(".playerCards[id='"+quitter+"']").find(".gameCard").each(function(){
								$(this).addClass('topAuto').animate({
									bottom:0
								},
								150,
								function(){
									if($(this).attr('id') != card.attr('id')){
										$(this).hide();
									}
								});
							});
							setTimeout(function(){
								card.removeClass('topAuto').animate({
									rotate:'0deg',
									scale:'1',
									left:left,
									top:-243
								},
								500,
								function(){
									card.hide();
									$(".cardsPool").append($(".playerCards[id='"+p+"']").html()).hide();
									$(".playerCards[id='"+p+"']").empty();
									ZT.updatePlayer(0);
									$(".cardsPoolHolder").show();
									if(ZT.activePlayer=='1'){
										setTimeout('ZT.nextRound()', 2000);
									}
								});
							},
							150);
						});
					});
				});
			});
		});
	});
};


WORK_Toptrumps.prototype.getRandomNumber=function(numLow,numHigh)
{
    var adjustedHigh = (parseFloat(numHigh) - parseFloat(numLow)) + 1;
    var rand = Math.floor(Math.random()*adjustedHigh) + parseFloat(numLow);
	return rand;
};


WORK_Toptrumps.prototype.gameListener=function()
{
	ZA.callAjax(ZT.sURL+"?listen=1&game="+ZT.playerGameID+"&moves="+ZT.moves,function(xml)
	{
		var logId = parseInt(ZA.getXML(xml,"log"));
		if(logId > ZT.playerLog)
		{
			ZT.stopListening();
			$(".gameMenuButton").hide('fast');
			var stat = parseInt(ZA.getXML(xml,"stat"));
			var status = ZA.getXML(xml,"status");
			var winner = ZA.getXML(xml,"winner");
			ZT.gameMessage = ZA.getXML(xml,"message");
			//show result
			if(status!='forfeit'){
				ZT.moves++;
				ZT.playerLog = logId;
				stat--;
				//show the round result
				$("#gameMessage").hide().show('fast').html('Comparing cards<br /><img src="_site/busy2.gif" />');
				ZT.selectStat(stat,winner);
			}
			else{
				//opponent forfeited the game
				$(".gameMenuButton").hide('fast');
				$("#gameMessage").html(ZA.getXML(xml,"message"));
				$("#p2loader").hide();
				ZT.creditsWon = parseInt(ZA.getXML(xml,"creditswon"));
				ZT.forfeitAnimation('0');
			}
		}
	});
};


WORK_Toptrumps.prototype.startListening=function()
{
	ZT.stopListening();
	ZT.listener = setInterval("ZT.gameListener()",5000);
};


WORK_Toptrumps.prototype.stopListening=function()
{
	clearInterval(ZT.listener);
	ZT.listener = null;
};


WORK_Toptrumps.prototype.updatePlayer=function(player)
{
	if(player=='0')
	{
		//update draw pool display
		$(".cardsPool").find(".gameCard").each(function(){
			$(this).css({
				left:-13,
				top:13
			});
		});
		var size = $(".cardsPool").find(".gameCard").size();
		$("#poolSize").html(size);
	}
	else
	{
		//Reposition player stack cards
		
		// var i = 0;
		// $(".playerCards[id='"+player+"']").find(".gameCard").each(function(){
			// if($(this).is(":visible")){
				// $(this).css({
					// top:"auto",
					// left:(i*12),
					// right:"auto",
					// bottom:"auto",
					// zIndex:i
				// }).show().animate({rotate:'0deg',scale:'1'},0);
			// }
			// i++;
		// });
	}	
	//Update players scores
	ZT.p1score = parseInt($(".playerCards[id='1']").find(".gameCard:visible").size());
	$("#score1").html(ZT.p1score);
	ZT.p2score = parseInt($(".playerCards[id='2']").find(".gameCard:visible").size());
	$("#score2").html(ZT.p2score);
};


WORK_Toptrumps.prototype.moveDrawPoolCards=function(winner)
{
	if($(".cardsPool").find(".gameCard").size() > 0){
		var iLeft = (winner=='1') ? -240 : 214;
		var iCardsInPool = $(".cardsPool").find(".gameCard").size();
		var i = 0;
		$(".cardsPool").find(".gameCard").each(function(){
			i++;
			var poolCard = $(this);
			$(this).show().animate({
				rotate:'0deg',
				left:iLeft,
				top:16
			},
			500,
			function(){
				$(this).remove();
				iCardsInPool = $(".cardsPool").find(".gameCard").size();
				$("#poolSize").html(iCardsInPool);
				$(".playerCards[id='"+winner+"']").prepend(poolCard);
				if(i >= iCardsInPool){
					$("#poolSize").empty();
					$(".cardsPoolHolder").hide();
					ZT.updatePlayer(winner);
				}
			});
		});
		if(!ZA.sUsername){
			//freeplay
			for(var i=0; i<ZT.drawCards; i++){
				if(winner=='1'){
					ZT.p1Cards.push(ZT.drawCards.shift());
				}
				else if(winner=='2'){
					ZT.p2Cards.push(ZT.drawCards.shift());
				}
			}
		}
	}
};


WORK_Toptrumps.prototype.endRound=function(winner)
{
	$("#p2loader").hide();
	//winner=0;
	//indicate round result and move cards to winning/draw stack
	if(winner=='0'){
		if(ZT.activePlayer=='1'){
			var card1 = $(".playerCards[id='1']").find(".gameCard").last();
			var card2 = $(".playerCards[id='2']").find(".gameCard").last();
			var activeplayer = '1';
			var inactiveplayer = '2';
			var left1 = 330;
			var left2 = -80;
			var top = -170;
			if(!ZA.sUsername){
				//freeplay
				ZT.drawCards.push(ZT.p2Cards.shift());
				ZT.drawCards.push(ZT.p1Cards.shift());
			}
		}else if(ZT.activePlayer=='2'){
			var card1 = $(".playerCards[id='2']").find(".gameCard").last();
			var card2 = $(".playerCards[id='1']").find(".gameCard").last();
			var activeplayer = '2';
			var inactiveplayer = '1';
			var left1 = -330;
			var left2 = 80;
			var top = -170;
			if(!ZA.sUsername){
				//freeplay
				ZT.drawCards.push(ZT.p1Cards.shift());
				ZT.drawCards.push(ZT.p2Cards.shift());
			}
		}
		//round is a draw - move cards in play to draw pool
		$(".statSelectorHolder[id='1']").find(".statSelected").find(".statSelectedIndicator").effect('pulsate',{},250);
		$(".statSelectorHolder[id='2']").find(".statSelected").find(".statSelectedIndicator").effect('pulsate',{},
		250,
		function(){
			$("#youDraw").show('pulsate',{},500,function(){
				//hide and reset the stat selectors
				$(".statSelectorHolder").hide('fade',{opacity:0},150,function(){
					$(this).find(".statSelector").removeClass('statSelected statLost').addClass('noselect');
				});
				//show cards pool
				$(".cardsPoolHolder").show();
				//move cards in play to draw pool
				//flip inactive player's card to show generic back
				$(".playerCard[id='"+inactiveplayer+"']").animate({
					width:10,
					height:350,
					left:134
				},
				150,
				function(){
					$(".playerCard[id='"+inactiveplayer+"']").hide();
					//show inactive player's generic back
					$(".back[id='"+inactiveplayer+"']").show()
					.animate({
						width:250,
						height:350,
						left:10
					},
					150,
					function(){
						//hide inactive player's generic back and show top card (stretched)
						$(".back[id='"+inactiveplayer+"']").hide();
						card2.show();
						//move inactive player's card to draw pool
						card2.animate({
							rotate:'0deg', 
							scale:'1', 
							bottom:0,
							left:left2,
							top:top
						},
						500,
						function(){
							card2.hide();
							//remove inactive player's card from player's stack and add to draw pool
							var drawcard = card2;
							card2.remove();
							$(".cardsPool").append(drawcard);
							ZT.updatePlayer(0);
							//flip active player's card in play to generic back
							$(".playerCard[id='"+activeplayer+"']")
							.animate({
								width:10,
								height:350,
								left:134
							},
							150,
							function(){
								$(".playerCard[id='"+activeplayer+"']").hide();
								//show active player's generic back
								$(".back[id='"+activeplayer+"']").show()
								.animate({
									width:250,
									height:350,
									left:10
								},
								150,
								function(){
									//hide generic back and show active player's top card (stretched)
									$(".back[id='"+activeplayer+"']").hide();
									card1.show();
									//move active player's card to draw pool
									card1.animate({
										rotate:'0deg', 
										scale:'1', 
										bottom:0,
										left:left1,
										top:top
									},
									500,
									function(){
										card1.hide();
										//remove active player's card from player's stack and add to draw pool
										var drawcard = card1;
										card1.remove();
										$(".cardsPool").append(drawcard);
										ZT.updatePlayer(0);
										//start next round
										setTimeout("ZT.nextRound()",600);
									});
								});
							});
						});
					});
				});
				//hide result graphic
				$(this).hide('fast');
				$("#gameMessage").empty();
			});
		});
		//active player does not change with a draw
	}
	else
	{
		//one of the players won the round
		if(winner=='1')
		{
			//player1 is the winner - move cards in draw pool and cards in play to player1's stack
			var loser = '2';
			var result = "#youWin";
			var leftWinnerToWinnerStack = -80;
			var leftLoserToWinnerStack = -400;
			if(!ZA.sUsername){
				//freeplay
				while(ZT.drawCards.length > 0){
					if(ZT.activePlayer=='1'){
						ZT.p1Cards.push(ZT.drawCards.shift());
					}
					else{
						ZT.p1Cards.push(ZT.drawCards.pop());
					}
				}
				ZT.p1Cards.push(ZT.p2Cards.shift());
				ZT.p1Cards.push(ZT.p1Cards.shift());
			}
		}
		else if(winner=='2')
		{
			//player2 is the winner - move cards in draw pool and cards in play to player1's stack
			var loser = '1';
			var result = "#youLose";
			var leftWinnerToWinnerStack = 320;
			var leftLoserToWinnerStack = 660;
			if(!ZA.sUsername){
				//freeplay
				while(ZT.drawCards.length > 0){
					if(ZT.activePlayer=='2'){
						ZT.p2Cards.push(ZT.drawCards.shift());
					}
					else{
						ZT.p2Cards.push(ZT.drawCards.pop());
					}
				}
				ZT.p2Cards.push(ZT.p1Cards.shift());
				ZT.p2Cards.push(ZT.p2Cards.shift());
			}
		}
		//animate round result
		$(".statSelectorHolder[id='"+winner+"']").find(".statSelected").find(".statSelectedIndicator").effect('pulsate',{},250,function()
		{
			//show loser's stat selector as lost
			$(".statSelectorHolder[id='"+loser+"']").find(".statSelected")
			.addClass('statLost')
			.removeClass('statSelected');
			var winnerscard = $(".playerCards[id='"+winner+"']").find(".gameCard").last();
			var loserscard = $(".playerCards[id='"+loser+"']").find(".gameCard").last();
			var winningcard = winnerscard;
			var woncard = loserscard;
			//animate cards move to winner stack
			$(result).show('pulsate',{},500,function(){
				//hide and reset the stat selectors
				$(".statSelectorHolder").hide('fade',{opacity:0},150,function(){
					$(this).find(".statSelector").removeClass('statSelected statLost').addClass('noselect');
				});
				//move cards from draw pool to player1's stack
				ZT.moveDrawPoolCards(winner);
				//flip loser's card in play
				$(".playerCard[id='"+loser+"']")
				.animate({
					width:10,
					height:350,
					left:134
				},
				150,
				function(){
					$(".playerCard[id='"+loser+"']").hide();
					//show loser's generic back
					$(".back[id='"+loser+"']").show()
					.animate({
						width:250,
						height:350,
						left:10
					},
					150,
					function(){
						//hide loser's generic back and show top card (stretched)
						$(".back[id='"+loser+"']").hide();
						loserscard.show();
						//move loser's card to winner's stack
						loserscard.animate({
							rotate:'0deg', 
							scale:'1', 
							bottom:0,
							left:leftLoserToWinnerStack,
							top:0
						},
						500,
						function(){
							//remove loser's card from loser's stack and add to bottom of winner's stack
							loserscard.remove();
							$(".playerCards[id='"+winner+"']").prepend(woncard);
							ZT.updatePlayer(winner);
							//flip winner's card in play to generic back
							$(".playerCard[id='"+winner+"']")
							.animate({
								width:10,
								height:350,
								left:134
							},
							150,
							function(){
								$(".playerCard[id='"+winner+"']").hide();
								//show winner's generic back
								$(".back[id='"+winner+"']").show()
								.animate({
									width:250,
									height:350,
									left:10
								},
								150,
								function(){
									//hide generic back and show winner's top card (stretched)
									$(".back[id='"+winner+"']").hide();
									winnerscard.show();
									//move winner's card to winner's stack
									winnerscard.animate({
										rotate:'0deg', 
										scale:'1', 
										bottom:0,
										left:leftWinnerToWinnerStack,
										top:0
									},
									500,
									function(){
										//remove winner's card from winner's stack and add to bottom of winner's stack
										winnerscard.remove();
										$(".playerCards[id='"+winner+"']").prepend(winningcard);
										ZT.updatePlayer(winner);
										//start next round
										setTimeout("ZT.nextRound()",600);
									});
								});
							});
						});
					});
				});
				//hide result graphic
				$(this).hide('fast');
				$("#gameMessage").empty();
			});
		});
		ZT.activePlayer = winner;
	}
}


WORK_Toptrumps.prototype.selectStat=function(stat, winner)
{
	var freeplay = '';
	if(!ZA.sUsername){
		freeplay = "&p1="+ZT.p1card+"&p2="+ZT.p2card;
	}
	if(ZT.activePlayer=='1')
	{
		//show opponent's card
		$("#p2loader").show();
		ZA.callAjax(ZT.sURL+"?play=1&game="+ZT.playerGameID+"&player="+ZT.activePlayer+"&stat="+stat+freeplay,function(xml)
		{
			//show round result
			var status = ZA.getXML(xml,"status");
			if(status!='forfeit')
			{
				ZT.moves++;
				ZT.playerLog = ZA.getXML(xml,"log");
				winner = ZA.getXML(xml,"winner");
				var p2card = ZA.getXML(xml,"p2card");
				ZT.gameMessage = ZA.getXML(xml,"message");
				//show opponent's card
				var backimage = ZA.getXML(ZT.cardsXML,"card_"+ZT.cardsArray[p2card]+"/fullimage")+"_back.jpg";
				$(".playerCard[id='2']").attr('src',backimage).css({width:10,height:350,left:134});
				//Flip P2 card: Hide generic back
				$(".back[id='2']").animate({
					width:10,
					left:134
				},
				150,
				function(){
					$(this).hide();
					//Flip P2 card: Show card stats
					$(".playerCard[id='2']").show().animate({
						width:250,
						left:10
					},
					150,
					function(){
						//select player1's stat for player2
						$(".statSelectorHolder[id='2']").find("statSelector").each(function(){
							if($(this).hasClass('statSelected')){
								$(this).removeClass('statSelected');
							}
						});
						$(".statSelectorHolder[id='2']").removeClass("hideSelection")
						.show()
						.find(".statSelector[id='"+stat+"']")
						.addClass('statSelected');
						//show the round result
						$("#gameMessage").hide('fade',{opacity:0},150,function(){
							$("#gameMessage").html(ZT.gameMessage);
							$("#gameMessage").show('fade',{opacity:100},150);
						})
						//end the round - indicate the winning player move card to winning player's stack
						ZT.endRound(winner);
						//get game ready for next round
						if(status=='incomplete'){
							//ZT.p1card = ZA.getXML(xml,"p1card");
							//ZT.p2card = ZA.getXML(xml,"p2card");
						}
						else if(status=='complete'){
							ZT.creditsWon = parseInt(ZA.getXML(xml,"creditswon"));
						}
					});	
				});
			}
			else
			{
				//opponent forfeited the game
				$(".gameMenuButton").hide('fast');
				$("#gameMessage").html(ZA.getXML(xml,"message"));
				$("#p2loader").hide();
				ZT.creditsWon = parseInt(ZA.getXML(xml,"creditswon"));
				ZT.forfeitAnimation('0');
			}
		});
	}
	else if(ZT.activePlayer=='2')
	{
		if(ZT.playerOpponent=='0')
		{
			//versus computer
			ZA.callAjax(ZT.sURL+"?play=1&game="+ZT.playerGameID+"&player="+ZT.activePlayer+"&difficulty="+ZT.playerDifficulty+freeplay,function(xml)
			{
				//show round result
				stat = ZA.getXML(xml,"stat");
				var status = ZA.getXML(xml,"status");
				var message = ZA.getXML(xml,"message");
				winner = ZA.getXML(xml,"winner");
				//select stat for both players
				$(".statSelectorHolder[id='2']").removeClass("hideSelection")
				.show()
				.find(".statSelector[id='"+stat+"']")
				.addClass('statSelected');
				$(".statSelectorHolder[id='1']").removeClass("hideSelection")
				.show()
				.find(".statSelector[id='"+stat+"']")
				.addClass('statSelected');
				//display the round result
				$("#gameMessage").hide('fade',{opacity:0},150,function(){
					$(this).html(message).show('fade',{opacity:100},150);
				});
				//end the round - indicate the winning player move card to winning player's stack
				ZT.endRound(winner);
			});
		}
		else{
			//versus human player
			//select stat for both players
			$(".statSelectorHolder[id='2']").removeClass("hideSelection")
			.show()
			.find(".statSelector[id='"+stat+"']")
			.addClass('statSelected');
			$(".statSelectorHolder[id='1']").removeClass("hideSelection")
			.show()
			.find(".statSelector[id='"+stat+"']")
			.addClass('statSelected');
			//display the round result
			$("#gameMessage").hide('fade',{opacity:0},150,function(){
				$(this).html(ZT.gameMessage).show('fade',{opacity:100},150);
			});
			//end the round - indicate the winning player move card to winning player's stack
			ZT.endRound(winner);
			
		}
	}
};


WORK_Toptrumps.prototype.initStatSelectors=function()
{
	$(".gameMenuButton").show('fast');
	if(ZT.activePlayer == '1')
	{
		//set click handler
		$(".statSelectorHolder[id='1']").show()
		.find(".statSelector")
		.removeClass('statSelected noselect')
		.click(function(){
			var stat = $(this).attr('id');
			if(!$(this).hasClass('statSelected') && !$(this).hasClass('noselect')){
				$(this).parent().find(".statSelector").addClass('noselect');
				//show loading
				$("#gameMessage").hide().show('fast').html('Comparing cards<br /><img src="_site/busy2.gif" />');
				//show and hide elements
				$(".gameMenuButton").hide('fast');
				//show stat selector for player1
				$(".statSelectorHolder[id='1']").find("statSelector").each(function(){
					if($(this).hasClass('statSelected')){
						$(this).removeClass('statSelected');
					}
				});
				$(".statSelectorHolder[id='1']").removeClass("hideSelection")
				.show()
				.find(".statSelector[id='"+stat+"']")
				.addClass('statSelected');
				//compare active cards on selected stat
				setTimeout("ZT.selectStat("+stat+")",1000);
			}
		});
		$("#gameMessage").html("Choose a stat when you are ready");
	}
	else
	{
		//player 2 active
		$("#p2loader").show();
		//setup the stat selectors
		$(".statSelectorHolder[id='1']").show()
		.find(".statSelector").each(function(){
			$(this).addClass('noselect');
		});
		$(".statSelectorHolder[id='2']").addClass('hideSelection').show();
		//check if the opponent is human or computer
		if(ZT.playerOpponent == '0'){
			//computer selects a stat
			setTimeout("ZT.selectStat()",2000);
		}
		else{
			//start listening for opponent's move
			ZT.startListening();
		}
	}
};


WORK_Toptrumps.prototype.nextRound=function()
{
	//reset board for next round
	$("#gameMessage").hide().html('Loading<br /><img src="_site/busy2.gif" />').show('fast');
	$(".roundResult").hide();
	$(".turnIndicator").hide();
	//get next player game card(s)
	ZA.callAjax(ZT.sURL+"?nextround=1&game="+ZT.playerGameID,function(xml)
	{
		if(ZT.playerGameID==0){
			var status = 'incomplete';
			var p1card = ZT.p1Cards[0];
			var p2card = ZT.p2Cards[0];
			var p1cardindex = ZT.cardsArray[p1card];
			var p2cardindex = ZT.cardsArray[p2card];
			var p1score = ZT.p1Cards.length;
			var p2score = ZT.p2Cards.length;
			ZT.p1card = p1card;
			ZT.p2card = p2card;
			//alert('ALL card_index:\n'+ZT.cardsArray+'\n\n'+p1card+' : P1 top card_id = card_'+p1cardindex+' ... card_ids: '+ZT.p1Cards+'\n'+ZT.p2card+' : P2 top card_id = card_'+p2cardindex+' ... card_ids: '+ZT.p2Cards+'\n\nDRAW:'+ZT.drawCards);
		}
		else{
			var status = ZA.getXML(xml,"status");
			var p1card = ZA.getXML(xml,"p1card");
			var p1cardindex = ZT.cardsArray[p1card];
			var p2card = ZA.getXML(xml,"p2card");
			var p2cardindex = ZT.cardsArray[p2card];
			var p1score = parseInt(ZA.getXML(xml,"p1score"));
			var p2score = parseInt(ZA.getXML(xml,"p2score"));
			ZT.activePlayer = ZA.getXML(xml,"activeplayer");
		}
		
		//Hide all cards in play area
		$(".playerCard[id='1']").attr('src','').hide();
		$(".playerCard[id='2']").attr('src','').hide();
		
		//Set the players' card stacks and scores
		for(var player=1; player<=2; player++){
			var i = 0;
			$(".playerCards[id='"+player+"']").find(".gameCard").each(function(){
				$(this).css({
					top:"auto",
					bottom:"auto",
					right:"auto",
					left:(i*12),
					"z-index":i
				}).show().animate({rotate:'0deg',scale:'1'},0);
				i++;
			});
		}
		for(var player=2; player<=2; player++){
			var i = 0;
			$(".playerCards[id='"+player+"']").find(".gameCard").each(function(){
				$(this).css({
					top:"auto",
					bottom:"auto",
					left:"auto",
					right:(i*12),
					"z-index":i
				}).show().animate({rotate:'0deg',scale:'1'},0);
				i++;
			});
		}
		$("#score1").html(p1score.toString());
		$("#score2").html(p2score.toString());
		
		//set up the mainboard for the next round
		//if the game is not over
		if(p1score!=0 && p2score!=0 && status=='incomplete')
		{
			//Indicate active player
			$(".turnIndicator[id='"+ZT.activePlayer+"']").show();
			//Set user's card
			var backimage = ZA.getXML(ZT.cardsXML,"card_"+p1cardindex+"/fullimage")+"_back.jpg";
			$(".playerCard[id='1']").attr('src',backimage).css({width:10,height:350,left:134});
			
			$(".back[id='1']").css({width:250,height:350,left:10}).hide();
			$(".back[id='2']").css({width:250,height:350,left:10}).hide();
			var gameCard1 = $(".playerCards[id='1']").find(".gameCard").last();
			gameCard1.css({"z-index":"1001"});
			var gameCard2 = $(".playerCards[id='2']").find(".gameCard").last();
			gameCard2.css({"z-index":"1001"});
			
			//START OF ANIMATION PROCESS//
			
			if(ZT.activePlayer == '1')
			{
				//Player 1 active
				//Place player 1 card from stack on gameboard
				$("#score1").html(p1score-1);
				$(gameCard1).animate({
					rotate:'0deg', 
					scale:'3.95', 
					left:105, 
					top:-248,
				},
				500,
				function(){
					$(".back[id='1']").show();
					$(this).hide();
					//Place player 2 card from stack on gameboard
					$("#score2").html(p2score-1);
					$(gameCard2).animate({
						rotate:'0deg', 
						scale:'3.95', 
						left:144, 
						top:-248
					},
					500,
					function(){
						$(".back[id='2']").show();
						$(this).hide();
						//Flip P1 card: Hide generic back
						$(".back[id='1']").animate({
							width:10,
							height:350,
							left:134
						},
						150,
						function(){
							$(this).hide();
							//Flip P1 card: Show card back
							$(".playerCard[id='1']").show().animate({
								width:250,
								height:350,
								left:10
							},
							150,
							function(){
								//All visual loading complete
								ZT.initStatSelectors();
							});
						});
					});
				});
			}
			else
			{
				//Player 2 active
				//set the back image only when player 2 is active
				var backimage = ZA.getXML(ZT.cardsXML,"card_"+p2cardindex+"/fullimage")+"_back.jpg";
				$(".playerCard[id='2']").attr('src',backimage).css({width:10,height:350,left:134});
				//Place player 2 card from stack on gameboard
				$("#score2").html(p2score-1);
				$(gameCard2).animate({
					rotate:'0deg', 
					scale:'3.95', 
					left:145, 
					top:-248,
				},
				500,
				function(){
					$(".back[id='2']").show();
					$(this).hide();
					//Place player 1 card from stack on gameboard
					$("#score1").html(p1score-1);
					$(gameCard1).animate({
						rotate:'0deg', 
						scale:'3.95', 
						left:105, 
						top:-248
					},
					500,
					function(){
						$(".back[id='1']").show();
						$(this).hide();
						//Flip P2 card: Hide generic back
						$(".back[id='2']").animate({
							width:10,
							height:350,
							left:134
						},
						150,
						function(){
							$(this).hide();
							//Flip P1 card: Hide generic back
							$(".back[id='1']").animate({
								width:10,
								height:350,
								left:134
							},
							150,
							function(){
								$(this).hide();
								//Flip P2 card: Show card back
								$(".playerCard[id='2']").show().animate({
									width:250,
									height:350,
									left:10
								},
								150,
								function(){
									//Flip P1 card: Show card back
									$(".playerCard[id='1']").show().animate({
										width:250,
										height:350,
										left:10
									},
									150,
									function(){
										//All visual loading complete
										$("#gameMessage").hide().html("Waiting for your opponent to choose a stat...").show('fast');
										ZT.initStatSelectors();
									});
								});
							});
						});
					});
				});
			}
		}
		else
		{
			//One of the players has 0 cards left or forfeit
//			  _____          __  __ ______    ______      ________ _____  
//			 / ____|   /\   |  \/  |  ____|  / __ \ \    / /  ____|  __ \ 
//			| |  __   /  \  | \  / | |__    | |  | \ \  / /| |__  | |__) |
//			| | |_ | / /\ \ | |\/| |  __|   | |  | |\ \/ / |  __| |  _  / 
//			| |__| |/ ____ \| |  | | |____  | |__| | \  /  | |____| | \ \ 
//			 \_____/_/    \_\_|  |_|______|  \____/   \/   |______|_|  \_\

			$(".gameMenuButton").hide('fast',function(){
				$(".gameContinueButton").show('fast');
			});
			$(".cardsInPlayHolder").hide();
			if(status=='forfeit'){
				$("#gameMessage").html(ZA.getXML(xml,"message"));
			}
			else{
				$("#gameMessage").empty();
			}
			$(".infoMessage").animate({
				height:425
			},
			250,
			function()
			{
				//show game ending
				//$(".infoMessage").find("table").css('height',425);
				$("#gameMessage").css("font-size","36px");
				var score = p1score+'-'+p2score;
				//check who won
				if(ZT.p1score == 0){
					//user lost
					$("#gameMessage").html('<div style="width:225px;height:126px;background:url(_site/gameboard_1.png) -254px -875px no-repeat;position:relative;margin-left:auto;margin-right:auto;padding-top:45px;font-weight:bold;">'+score+'</div>');
				}
				else if(ZT.p2score == 0){
					//user won
					$("#gameMessage").html('<div style="width:225px;height:126px;background:url(_site/gameboard_1.png) -15px -874px no-repeat;position:relative;margin-left:auto;margin-right:auto;padding-top:45px;font-weight:bold;">'+score+'</div>');
					//check if the user got credits for winning
					if(ZT.creditsWon > 0){
						var newCredits = parseInt(ZA.sUserCredits) + ZT.creditsWon;
						ZA.gotCredits(ZT.creditsWon.toString()+" credits for winning the game",newCredits);
					}
				}
			});
			//for multiplayer game, has the losing player seen the game ending?
			/*
				//has the game been closed
				if(ZT.viewEnd == '1')
				{
					ZT.updateGame('0');
				}
			});
			*/
		}
	});
};

		}
	WORK_Toptrumps._iInited=1;
};


var ZT = new WORK_Toptrumps();
ZA.aComponents[ZT.iComponentNo].fMaximizeFunction=ZT.maximize;
ZT.init();