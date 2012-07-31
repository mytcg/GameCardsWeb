function objCard(){
  this.divCard;
  this.imgFront;
  this.imgBack;
  this.slotNr = 0;
  this.barNr = 0;
  this.health = 0;
  this.maxHealth = 0;
};

function cGame(){
  this.rootPath = "_app/play/";
  this.imagePath = "img/cards/";
  this.iStatus = 0;
  this.sStatus = ["Draw your cards","Choose a Attacker"];
  this.iNo = 0;
  this.windowWidth = 0;
  this.windowHeight = 0;
  this.divContainer = null;
  this.divPicHolder = null;
  this.divConsole = null;
  this.divStatus = null;
  
  this.hpBars = [];
  
  this.divDeck1 = null;
  this.player1 = [];
  this.player1["card1"] = null;
  this.player1["card2"] = null;
  this.player1["card3"] = null;
  
  this.divDeck2 = null;
  this.player2 = [];
  this.player2["card1"] = null;
  this.player2["card2"] = null;
  this.player2["card3"] = null;
  
  cGame.prototype.console=function(sPlayer,sString){
    var txtList = $(iGame.divConsole).html();
    $(iGame.divConsole).empty();
    $(iGame.divConsole).html("["+sPlayer+"]: "+sString+"<br>");
    $(iGame.divConsole).append(txtList);
  };
    
  cGame.prototype.updateStatus = function(iNr){
    if (iGame.iStatus != iNr){
      iGame.iStatus = iNr;
      $(iGame.divStatus).html(iGame.sStatus[iNr]);  
    }
  };
  
  cGame.prototype.buildDeck=function(iPlayer,iDeckCount){
    //alert(divDeck.childNodes[divDeck.childNodes.length-1].id);
    //Create Deck holder div
    var divDeck = ZA.createDiv(iGame.divContainer,"","divDeck","div");
    (iPlayer == 1) ? iGame.divDeck1 = divDeck : iGame.divDeck2 = divDeck; 
    var iTop = (iPlayer == 1) ? 304-iDeckCount : 22-iDeckCount;
    var iLeft = (iPlayer == 1) ? 846-iDeckCount : 222-iDeckCount;
    $(divDeck).css({position:"absolute",width:60,height:83,top:iTop,left:iLeft,zIndex:2});
    
    //Player one can click to draw cards
    if ((iPlayer==1)&&(iGame.iStatus==0)){
       $(divDeck).click(function(e){
          iGame.drawCards(1,3);
          setTimeout(function(){ iGame.drawCards(2,3) },1800);
       });
    }
    
    //Create cards in deck
    for (i=1;i <= iDeckCount;i++){
      var divDeckCard = ZA.createDiv(divDeck,"","divDeckCard"+i,"img");
      divDeckCard.src = iGame.imagePath+"/back.png";
      $(divDeckCard).css({position:"absolute",width:56,height:79,top:i-4,left:i-4,opacity:0,zIndex:iDeckCount-i});
      $(divDeckCard).delay(300*(i-1)).animate({ top:i,left:i,opacity:1 },300, function() {
        
      });
    }
  };
  
  cGame.prototype.setHP=function(objCard){
    var divBarMid = iGame.hpBars[objCard.barNr].childNodes[1];
    var health = [objCard.health,objCard.maxHealth];
    var iWidth = (health[0] / health[1]) * 114;
    $(divBarMid).animate({width:iWidth},200);
  }
  
  cGame.prototype.takeDamage=function(iPlayer,iSlot,iDamage){
    var objCard = (iPlayer==1) ? iGame.player1["card"+iSlot] : iGame.player2["card"+iSlot] ;
    (objCard.health > iDamage) ? objCard.health -= iDamage : objCard.health = 0;
    var divCard = (iPlayer == 1) ? iGame.player1["card"+iSlot].divCard : iGame.player2["card"+iSlot].divCard;
    var iLeft = parseInt(divCard.style.left);
    $(divCard).animate({left:iLeft-6},50).animate({left:iLeft+6},50).animate({left:iLeft-6},50).animate({left:iLeft+6},50).animate({left:iLeft},50);
    iGame.setHP(objCard);
  }
  
  cGame.prototype.drawCards=function(iPlayer,iCards){
    iGame.updateStatus(1);
    var iTop = (iPlayer == 1) ? parseInt(iGame.divDeck1.style.top) : parseInt(iGame.divDeck2.style.top);
    var iLeft = (iPlayer == 1) ? parseInt(iGame.divDeck1.style.left) : parseInt(iGame.divDeck2.style.left);
    var iWidth = parseInt(iGame.divDeck1.style.width);
    var iHeight = parseInt(iGame.divDeck1.style.height);
    
    var iPlayerFinalTop = (iPlayer == 1) ? 286 : 15;
    var iPlayerFinalLeft = (iPlayer == 1) ? 348 : 300;
    var iPlayerInterval = (iPlayer == 1) ? 161 : 160;
    
    var iCount = 0;
    var divCard = null;
    var iNewLeft = 0;
    var intBuildCards = self.setInterval(function(){
      iNewLeft = iPlayerFinalLeft + (iCount * iPlayerInterval);
      iCount++;
      divCard = iGame.createCard(iPlayer,iCount,iTop,iLeft,iWidth,iHeight);
      $(divCard).delay(200).animate({ top:iPlayerFinalTop,left:iNewLeft,width:148,height:207 },250, function() { }); 
      if (iCount == iCards){ window.clearInterval(intBuildCards); }
    },600);
  };
  
  cGame.prototype.createCard=function(iPlayer,iSlot,iTop,iLeft,iWidth,iHeight){
    
    var divCard = ZA.createDiv(iGame.divContainer,"","divCard","div");
    var currentCard = (iPlayer == 1) ? iGame.player1["card"+iSlot] = new objCard : iGame.player2["card"+iSlot] = new objCard;    
    $(divCard).css({position:"absolute",width:iWidth,height:iHeight,top:iTop,left:iLeft,zIndex:3});
    currentCard.divCard = divCard;
    currentCard.slotNr = iSlot;
    currentCard.barNr = (iPlayer == 1) ? iSlot-1 : iSlot+2 ;
    
    //GET HEALTH STATS - TESTING
    var randomCard = Math.round(Math.random()*17+71);
    currentCard.health = Math.round(Math.random()*200+1);
    currentCard.maxHealth = currentCard.health;
    
    $(divCard).mouseenter(function() {
      iGame.divPicHolder.childNodes[0].src = iGame.imagePath+randomCard+"_back.png";        
    });
    if (iPlayer == 1){
      $(divCard).click(function(e){
        if( parseInt($(divCard.childNodes[0]).css("width")) > 0 )
          $(divCard.childNodes[0]).animate({ width:0 },200, function() { $(divCard.childNodes[1]).animate({ width:148 },200, function() { }); });
        else
          $(divCard.childNodes[1]).animate({ width:0 },200, function() { $(divCard.childNodes[0]).animate({ width:148 },200, function() { }); });
      }); 
    }
    
    //Card images
    var divCardFront = ZA.createDiv(divCard,"","divCardImgF","img");
    divCardFront.src = iGame.imagePath+randomCard+"_front.png";
    $(divCardFront).css({width:"100%",height:"100%"});
    var divCardBack = ZA.createDiv(divCard,"","divCardImgB","img");
    divCardBack.src = iGame.imagePath+randomCard+"_back.png";
    $(divCardBack).css({width:0,height:"100%"});
    
    //create card Health bar
    var iBar = (iPlayer==1) ? iSlot-1 : iSlot+2 ;
    var picBarLeft = ZA.createDiv(iGame.hpBars[iBar],"","","div");
    $(picBarLeft).css({width:10,height:17,position:"relative",cssFloat:"left",backgroundImage:"url(_app/play/images/bar_left.png)"});
    var picBarMid = ZA.createDiv(iGame.hpBars[iBar],"","","div");
    $(picBarMid).css({width:1,height:17,position:"relative",cssFloat:"left",backgroundImage:"url(_app/play/images/bar_mid.png)"});
    var picBarRight = ZA.createDiv(iGame.hpBars[iBar],"","","div");
    $(picBarRight).css({width:7,height:17,position:"relative",cssFloat:"left",backgroundImage:"url(_app/play/images/bar_right.png)"});
    
    //SET HEALTH
    setTimeout(function(){ iGame.setHP(currentCard) },300);
    
    return divCard;
    
  };
  
};

function WORK_Play(){
	
	if (typeof WORK_Play._iInited=="undefined"){
  
    WORK_Play.prototype.init=function(iComponentNo){
      
      //Set default properties into game object and start building page
      iGame.iNo=iComponentNo;
      iGame.divContainer = document.getElementById("window_" + iGame.iNo);
      iGame.windowWidth = iGame.divContainer.offsetWidth;
      iGame.windowHeight = iGame.divContainer.offsetHeight;
      ZP.buildPage();
      
    };
    
    WORK_Play.prototype.startGame=function(){
      iGame.buildDeck(1,4);
      iGame.buildDeck(2,4);
      
      //iGame.createCard(1,1,289,372);
    };
    
    WORK_Play.prototype.buildPage=function(){
      
      //**TEMP -> Might move to actual css file **
      var css = document.createElement('link');
      css.rel = 'stylesheet';
      css.type = 'text/css';
      css.href = iGame.rootPath+'css/game.css';
      var head = document.getElementsByTagName('head').item(0);
      head.appendChild(css);
      
      //Apply playing board background
      var divWin = iGame.divContainer;
      divWin.style.backgroundImage = "url(" + iGame.rootPath + "images/board2.png)";
      
      //Create preview window with blank starting image
      iGame.divPicHolder = ZA.createDiv(divWin,"picHolder","divPicHolder","div");
      var divPic = ZA.createDiv(iGame.divPicHolder,"","imgPic","img");
      divPic.src = "_site/cards/no-preview.gif";
	//divPic.src = "img/cards/back.png";
      $(divPic).css({width:170,height:237});
      
      //Create status bar
      iGame.divStatus = ZA.createDiv(divWin,"status","divStatus","div");
      $(iGame.divStatus).html(iGame.sStatus[iGame.iStatus]);
      
      //Create console div
      iGame.divConsole = ZA.createDiv(divWin,"console","divConsole","div");
      
      //Card HP bars - Player 1
      iGame.hpBars[0] = ZA.createDiv(divWin,"hpBar","divBar0","div");
      $(iGame.hpBars[0]).css({top:503,left:358});
      iGame.hpBars[1] = ZA.createDiv(divWin,"hpBar","divBar1","div");
      $(iGame.hpBars[1]).css({top:503,left:519});
      iGame.hpBars[2] = ZA.createDiv(divWin,"hpBar","divBar2","div");
      $(iGame.hpBars[2]).css({top:503,left:680});
      
      //Card HP bars - Player 2
      iGame.hpBars[3] = ZA.createDiv(divWin,"hpBar","divBar3","div");
      $(iGame.hpBars[3]).css({top:235,left:308});
      iGame.hpBars[4] = ZA.createDiv(divWin,"hpBar","divBar4","div");
      $(iGame.hpBars[4]).css({top:235,left:469});
      iGame.hpBars[5] = ZA.createDiv(divWin,"hpBar","divBar5","div");
      $(iGame.hpBars[5]).css({top:235,left:630});
      
      //Start game button
      var divButton =  ZA.createDiv(divWin,"","idButton","div");
      $(divButton).css({cursor:"pointer",color:"#FFF",padding:"10px",top:15,left:890,zIndex:50}); //backgroundColor:"#FFF",
      $(divButton).html("Start Game");
      $(divButton).click(function(e) { ZP.startGame() });
      
      //Test anim stuff
      //divButton =  ZA.createDiv(divWin,"","idButton2","div");
      //$(divButton).css({backgroundColor:"#FFF",padding:"10px",top:50,left:900,zIndex:50});
      //$(divButton).html("Take damage");
      //$(divButton).click(function(e) { iGame.takeDamage(1,1,10) });
      
    };    
    
    
    WORK_Play.prototype.moveTo=function(div,iTop,iLeft,iWidth,iHeight){
      $(div).animate(
        { top : iTop + "px",left : iLeft + "px",width : iWidth + "px",height : iHeight + "px" },
        { duration : 300 },
        function() {  }
      );
    };
  
    WORK_Play._iInited=1;
  }
};
var iGame = new cGame();
var ZP = new WORK_Play();
ZP.init(7);
//ZA.callAjax("_app/play/?init=1",function(xml){ ZP.init(xml,7); });
ZA.callAjax("_app/play/?img_server=1",function(xml){ iGame.imagePath = ZA.getXML(xml,"data/fullimageserver") + 'cards/';  });
