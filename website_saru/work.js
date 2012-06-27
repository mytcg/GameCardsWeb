//Piero
//=====  Player bar Class  ============

function objPlayerBar(){
  this.divBar = null;
  this.divLevel = null;
  this.spanLevelNr = null;
  this.divXP = null;
  this.divXPbar = null;
  this.divXPPercent = null;
  this.divCredits = null;
  this.spanCredits = null;
  this.iLevel = 1;
  this.iXP = 0;
  this.iCredits = 0;
  
  objPlayerBar.prototype.create=function(credits,xp){
    var divBody=document.getElementsByTagName("body")[0];
    var objBar = ZA.oPlayerBar;
    objBar.iCredits = (credits >= 0) ? credits : 0 ;
    //Create Bar window
    // objBar.divBar = ZA.createDiv(divBody,"","divPlayerBar","div");
    // ZA.setSelectNone(objBar.divBar);
    // $(objBar.divBar).css({zIndex:2,top:20,left:4,width:169,height:51,position:"absolute"});

	if (ZA.sUsername) {
		var divBlah = $(".left_banner_info").get(0);
		objBar.divCredits = ZA.createDiv(divBlah,"","","div");
    	$(objBar.divCredits).css({zIndex:2,width:210,height:19,bottom:12});
    	$(objBar.divCredits).html("<span>YOU </span>HAVE <span id='spanCreditsID' class='txtBlue' style='font-size:12px'>"+objBar.iCredits+"</span><span class='txtRed'> </span>credits</div");
	}
    objBar.spanCredits = document.getElementById("spanCreditsID");
    //$(objBar.divCredits).draggable();
  };
  
  objPlayerBar.prototype.update=function(objProps){
    if (objProps.credits){
      var hexColor = (objProps.credits > ZA.oPlayerBar.iCredits) ? "#0C0" : "#C00";
      $(ZA.oPlayerBar.spanCredits).html(objProps.credits);
      $(ZA.oPlayerBar.spanCredits).css({color:hexColor});
      $(ZA.oPlayerBar.spanCredits).delay(600).animate({color:"#F2C126"},1500);
    }
    if (objProps.xp){
      var oldXP = ZA.oPlayerBar.iXP;
      var oldLevel = Math.floor(Math.sqrt(oldXP/50 + 0.25) + 0.5);
      var oldNeeded = oldLevel*(oldLevel+1)*50;
      ZA.oPlayerBar.iXP += objProps.xp;
      var newXP = ZA.oPlayerBar.iXP;
      
      if (newXP < oldNeeded){
        var lastLevel = (Math.floor(Math.sqrt(newXP/50 + 0.25) + 0.5)) - 1;
        var LastNeeded = lastLevel*(lastLevel+1)*50;
        
        var currentLevel = Math.floor(Math.sqrt(newXP/50 + 0.25) + 0.5);
        var currentNeeded = currentLevel*(currentLevel+1)*50;
        if (lastLevel == 0)
          var diff = newXP / currentNeeded;
        else
          var diff = (newXP - LastNeeded) / (currentNeeded-LastNeeded);
        var pxIncrease = 150 * diff;
        diff = Math.round(diff*100);
        $(ZA.oPlayerBar.divXPbar).animate({width:pxIncrease},300);
        $(ZA.oPlayerBar.divXP).html(newXP+" / "+oldNeeded);
        $(ZA.oPlayerBar.divXPPercent).html(diff+"%");
      } else {
        var newLevel = Math.floor(Math.sqrt(newXP/50 + 0.25) + 0.5);
        var newNeeded = newLevel*(newLevel+1)*50;
        
        $(ZA.oPlayerBar.divXPbar).animate({width:150},300,function(){ 
          $(ZA.oPlayerBar.divXPbar).css({width:0}); 
        });
        var diff = (newXP - oldNeeded) / (newNeeded-oldNeeded);
        var pxIncrease = 150 * diff;
        diff = Math.round(diff*100);
        $(ZA.oPlayerBar.divXPbar).delay(301).animate({width:pxIncrease},300);
        $(ZA.oPlayerBar.divXP).html(newXP+" / "+newNeeded);
        $(ZA.oPlayerBar.spanLevelNr).html(newLevel);
        $(ZA.oPlayerBar.divXPPercent).html(diff+"%");
      }
      
    }
  };
};
 
//==============================================================================
//APP CLASS 
function WORK_App(){
	this.sBrowserName="";
	this.sURL="";
	this.sXML="";
	this.imgAll="_site/all_skin.png";
	this.iOpacityHeader=0;
	this.iOpacityWindow=0;
	this.iOpacityWindowInActive=0;
	this.iWidthContainer=833;
	this.iHeightContainer=730;
	this.iHeightFooter=0;
	this.iHeightHeader=0;
	this.iWidthMargin=0;
	this.iHeightMargin=0;
	this.iHeightWindowTitle=0;
	this.iSizeWindowDecor=0;
	this.aComponents=[];
	this.sMenuItemsLeft="";
	this.sMenuItemsBottom="";	
	this.sMenuItemsTop="";	
	this.aWins=[];
	this.sUsername="";
	this.iCheckBoxChecked=false;
	this.iZIndex=1;
	this.aWindowLogin=0;
	this.aWindowRegister=0;
	this.aWindowPlay=0;
	this.aWindowUserActivated=0;
	this.sUserCredits=null;
	//System and User Cards
	this.aCards=null;
	//User's Login variables
	this.sUserLogin='';
	this.sUserPassword='';
	//User activation variables
	this.iActivationStatus=0;
	this.sActivationMessage="";
	
	this.divProcessF=null;
	this.divProcessD=null;
	this.iProcess = 0;
	this.categoryID=0;
	this.updateLevel=0;
	
	if (typeof WORK_App._iInited=="undefined"){
	
//create body
WORK_App.prototype.createBody=function(){
	ZA.sBrowserName=navigator.appName;
	
	if (ZA.sBrowserName=="Microsoft Internet Explorer"){
		ZA.sBrowserName="MSIE";
	}
	ZA.callAjax("_app/?init=1",function(xml){ 
		ZA.init(xml);
	});
	
};

WORK_App.prototype.browserPopup=function(){
  ZA.createWindowPopup(30,"",350,150,1,0);
  var divWindow=document.getElementById("window_30");
  var divData=ZA.createDiv(divWindow);
  $(divData).css({width:"100%",padding:5});
  
  var divMemo=ZA.createDiv(divData);
  $(divMemo).css({textAlign:"left",position:"absolute",left:"10px",top:"10px"});
  $(divMemo).html('<b>Congratulations</b><br>Your current browser is not fully supported. <br><br> We recommend Firefox, Chrome, Safari or Opera. \r\n IE9 is ok aswell.');
  
  var divButton = ZA.createDiv(divData,"cmdButton");
  $(divButton).css({top:73,right:20});
  $(divButton).html('Close');
  $(divButton).click(function(){
    $("#bodycloak_30").remove();
    $("#windowcontainer_30").remove();
    $("#window_30").remove();
  });
};

WORK_App.prototype.setCookie=function(c_name,username,password,exdays)
{
	var divider = "-----"
	var value = username + divider +password;
	value = ZA.base64Encode(value);
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}

WORK_App.prototype.getCookie=function(c_name)
{
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++)
	{
	  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
	  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
	  x=x.replace(/^\s+|\s+$/g,"");
	  if (x==c_name)
	    {
	    return unescape(y);
	    }
	 }
}

/* General Functions */
WORK_App.prototype.getRandomNumber=function(numLow,numHigh)
{
    var adjustedHigh = (parseFloat(numHigh) - parseFloat(numLow)) + 1;
    var numRand = Math.floor(Math.random()*adjustedHigh) + parseFloat(numLow);
	return numRand;

}

WORK_App.prototype.getDoubleDigits=function(number){
	if(parseInt(number,10) < 10){
		return '0'+number;
	}
	else{
		return number;
	}
}

WORK_App.prototype.findXY=function(obj) {
	var x = 0;
	var y = 0;
	while (obj){
		x += obj.offsetLeft;
		y += obj.offsetTop;
		obj = obj.offsetParent;
	}
	var values = [x,y];
	return values;
}

WORK_App.prototype.getLimitedString=function(str, max, separator) {
	var stringval = str;
	var trimmed = false;
    if (str.length > max){
    	stringval = str.split(separator);
    	stringval.pop();
    	stringval = stringval.join(separator);
    	stringval = this.getLimitedString(stringval, max, separator);
    	trimmed = true;
    }
    return stringval;
}

/* AJAX CALLS */
WORK_App.prototype.callAjax=function
	(pageQuery,callback,iComponentNo,iForceBusy){
	if (iComponentNo) {
		var divData=document.getElementById("window_"+iComponentNo);
		var iWidth=parseInt(divData.style.width);
		var iHeight=parseInt(divData.style.height);
		var divBusy=ZA.createDiv(divData,"busy","busy_"+iComponentNo);
		$(divBusy).css({
			left:parseInt((iWidth-40)/2)+"px"
			,top:parseInt((iHeight-40)/2)+"px"
			});
	}
  jQuery.get(pageQuery, function(data,status){
  	if ((!iForceBusy)&&(divData)) {
  		divData.removeChild(divBusy);
  	}
    if(callback){ 
    	callback(data);
    }
  });
};


WORK_App.prototype.clickPageDot=function(aObj,iPageNo){
	return function() {
		aObj.iCurrentPage=iPageNo;
		var iLeft=aObj.iCurrentPage*
			((aObj.iCellsPerPage/aObj.iMaxRows)*(aObj.iCellWidth+aObj.iCellMargin))+2;
		var divData=document.getElementById("pagebg_"+aObj.iComponentNo);
		$(divData).animate({left:-iLeft+"px"});
		ZA.showPageDots(aObj);
	};
};

WORK_App.prototype.clickPageLeft=function(aObj){
	return function() {
		aObj.iCurrentPage--;
		if (aObj.iCurrentPage<0) {
			aObj.iCurrentPage=0;
			return;
		}
		var iLeft=aObj.iCurrentPage*
			((aObj.iCellsPerPage/aObj.iMaxRows)*(aObj.iCellWidth+aObj.iCellMargin))+2;
		var divData=document.getElementById("pagebg_"+aObj.iComponentNo);
		$(divData).animate({left:-iLeft+"px"});
		ZA.showPageDots(aObj);
	};
};


WORK_App.prototype.clickPageRight=function(aObj){
	return function() {
		aObj.iCurrentPage++;
		if (aObj.iCurrentPage>aObj.iMaxPages) {
			aObj.iCurrentPage=aObj.iMaxPages;
			return;
		}
		var iLeft=aObj.iCurrentPage*
			((aObj.iCellsPerPage/aObj.iMaxRows)*(aObj.iCellWidth+aObj.iCellMargin))+2;
		var divData=document.getElementById("pagebg_"+aObj.iComponentNo);
		$(divData).animate({left:-iLeft+"px"});
		ZA.showPageDots(aObj);
	};
};


WORK_App.prototype.showPageDots=function(aObj){
	var iDotWidth=15;
	aObj.iMaxPages=Math.floor(aObj.iCellCount/aObj.iCellsPerPage);
	if (aObj.iCellCount % aObj.iCellsPerPage==0){
		aObj.iMaxPages--;
	}
	if (ZA.aComponents[aObj.iComponentNo].iIsMaximized && aObj.iComponentNo!=5) {
		var iLeft=(ZA.iWidthContainer/2);
	} else {
		var iLeft=(parseInt(ZA.aComponents[aObj.iComponentNo].aPos[2])/2);		
	}
	iLeft-=parseInt(aObj.iMaxPages*iDotWidth/2);
	var divDots=document.getElementById("controlspagedots_"+aObj.iComponentNo);
	if (divDots) {
		aObj.divData.removeChild(divDots);
	}
	var divDots=ZA.createDiv
		(aObj.divData,"controlspagedots","controlspagedots_"+aObj.iComponentNo);
	$(divDots).css({
		left:iLeft+"px",
		width:(iDotWidth*(aObj.iMaxPages+1))+"px"
	});
	for (var iCount=0;iCount<=aObj.iMaxPages;iCount++) {
		var sClass="controlspagedot";
		if (aObj.iCurrentPage==iCount) {
			sClass+="active";
		}
		var divDot=ZA.createDiv(divDots,sClass);
		divDot.onclick=ZA.clickPageDot(aObj,iCount);
		$(divDot).css({left:(iDotWidth*iCount+"px")});
	}
};

/*********** create html element */
WORK_App.prototype.createDiv=function(divParent,sClassName,sID,sType){
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


WORK_App.prototype.createMovie=function
	(iWidth,iHeight,sFileName,sColor){
	var sFlashVars="fpFileURL="+ZA.sURL+sFileName
	+"&fpButtonOpacity=0.1"
	+"&fpPreviewImageURL=_site/moviebg.png"
	+"&cpVolumeStart=25&colorScheme="+sColor;
	var sMovie=
	"<object "
	+"width='498' height='280'><param name='movie' "
	+"value='"+ZA.sURL+"_bin/mcmp_0.8.swf'>"
	+"<param name='wmode' value='transparent' />"
	+"<param name='allowScriptAccess' value='always' />"
	+"<param name='quality' value='high' />"
	+"<param name='allowFullScreen' value='true' />"
	+"<param name='autoStart' value='true' />"
	+"<param name='FlashVars' value='"+sFlashVars+"' />"
	+"<embed src='"+ZA.sURL+"_bin/mcmp_0.8.swf' "
	+"width='498' height='280' quality='high' "
	+"allowFullScreen='true' "
	+"allowscriptaccess='always' "
	+"wmode='transparent' "
	+"type='application/x-shockwave-flash' "
	+"FlashVars='"+sFlashVars+"'>"
	+"</embed>"
	+"</object>";
	return sMovie;
};

WORK_App.prototype.updateProcess=function(){
  switch(ZA.iProcess){
    case 3:
      $(ZA.divProcessD).animate({left:220},300);
      $(ZA.divProcessF).animate({width:215},300);
      $(ZA.divProcessT).css({backgroundPosition:"-418px -60px"});
      $("#divtext2").css({ backgroundPosition:"-5px -31px" });
      $("#divtext3").css({ backgroundPosition:"-214px -62px" });
    break;
    case 4:
      $(ZA.divProcessD).animate({left:340},300);
      $(ZA.divProcessF).animate({width:335},300);
      $(ZA.divProcessT).css({backgroundPosition:"-418px -90px"});
      $("#divtext3").css({ backgroundPosition:"-5px -62px" });
      $("#divtext4").css({ backgroundPosition:"-214px -93px" });
    break;
    case 5:
      $(ZA.divProcessD).animate({left:500},300);
      $(ZA.divProcessF).animate({width:495},300);
      $(ZA.divProcessT).css({backgroundPosition:"-418px -123px"});
      $("#divtext4").css({ backgroundPosition:"-5px -93px" });
      $("#divtext5").css({ backgroundPosition:"-214px -124px" });
    break;
    case 6:
      $(ZA.divProcessD).animate({left:650},300);
      $(ZA.divProcessF).animate({width:645},300);
      $(ZA.divProcessT).css({backgroundPosition:"-418px -153px"});
      $("#divtext5").css({ backgroundPosition:"-5px -124px" });
      $("#divtext6").css({ backgroundPosition:"-214px -155px" });
    break;
    case 7:
      $(ZA.divProcessD).animate({left:770},300);
      $(ZA.divProcessF).animate({width:765},300);
      $(ZA.divProcessT).css({backgroundPosition:"-418px -186px"});
      $("#divtext6").css({ backgroundPosition:"-5px -155px" });
      $("#divtext7").css({ backgroundPosition:"-214px -186px" });
    break;
    case 8:
      $(ZA.divProcessD).animate({left:835},300);
      $(ZA.divProcessF).animate({width:835},300);
      $(ZA.divProcessT).css({backgroundPosition:"-418px -216px"});
      $("#divtext7").css({ backgroundPosition:"-214px -0px" });
    break;
    default: //NOT LOGGED IN
      
    break;
  }
};

//create page
WORK_App.prototype.createPage=function(){
	var divBody=document.getElementsByTagName("body")[0];
	var divPage=ZA.createDiv(divBody,"","bodypage");
	
	
	// var divLeftInfo=ZA.createDiv(divLeft,"left_banner_info","");
	// $(divLeftInfo).html("<span class='txtGrey'>HALLO "+ZA.sUsername+"</span><br /> YOU HAVE: <span class='txtRed'>"+ZA.sUserCredits+" TCG</span>");
	// var divLeftSms=ZA.createDiv(divLeft,"left_banner_sms","");	
	var divContainer=ZA.createDiv(divPage,"","bodycontainer");
	// var divRight=ZA.createDiv(divPage,"","right_banner_container");
	// var divBanner = ZA.createDiv(divRight,"right_banner","");
	// var insideBanner = ZA.createDiv(divRight,"right_banner_inside","");
	
	// var divArrow=ZA.createDiv(divContainer,"","arrow_2");
	// var divArrow=ZA.createDiv(divContainer,"","arrow_3");
	// var divArrow=ZA.createDiv(divContainer,"","arrow_4");
	// var divArrow=ZA.createDiv(divContainer,"","arrow_5");
	// var divArrow=ZA.createDiv(divContainer,"","arrow_7");
	// var divArrow=ZA.createDiv(divContainer,"","arrow_8");
	
	var iHeight=ZA.iHeightContainer-ZA.iHeightHeader-ZA.iHeightFooter;
	
	//INSERT USER COMPLETION BAR
	// var divCompletion = ZA.createDiv(divContainer,"divcompletion","divcompletion");
	// var divTextbg = ZA.createDiv(divCompletion,"divtextbg","divtextbg");
	// var divText1 = ZA.createDiv(divCompletion,"divtext1","divtext1");
	// var divText2 = ZA.cre ateDiv(divCompletion,"divtext2","divtext2");
	// var divText3 = ZA.createDiv(divCompletion,"divtext3","divtext3");
	// var divText4 = ZA.createDiv(divCompletion,"divtext4","divtext4");
	// var divText5 = ZA.createDiv(divCompletion,"divtext5","divtext5");
	// var divText6 = ZA.createDiv(divCompletion,"divtext6","divtext6");
	// var divText7 = ZA.createDiv(divCompletion,"divtext7","divtext7");
// 
// 	
	// var divProcessE = ZA.createDiv(divCompletion,"divprocesse","divprocesse");
	// ZA.divProcessF = ZA.createDiv(divCompletion,"divprocessf","divprocessf");
	// ZA.divProcessD = ZA.createDiv(divCompletion,"divprocessd","divprocessd");
	// ZA.divProcessT = ZA.createDiv(divCompletion,"divprocesst","divprocesst");
	
	ZA.updateProcess();
	//show all nav category
	ZA.aComponents[1]=new WORK_Component();
	ZA.aComponents[1].create(1,"180px,275px");
	//shop window
	ZA.aComponents[2]=new WORK_Component();
	ZA.aComponents[2].create(2,"500px");
	//deck window
	ZA.aComponents[3]=new WORK_Component();
	ZA.aComponents[3].create(3,"");
	//album window
	ZA.aComponents[4]=new WORK_Component();
	ZA.aComponents[4].create(4,"500px");
	//auction window
	ZA.aComponents[5]=new WORK_Component();
	ZA.aComponents[5].create(5,"500px");
	//play window
	ZA.aComponents[6]=new WORK_Component();
	ZA.aComponents[6].create(6,"");
	ZA.aComponents[7]=new WORK_Component();
	//window components for non visible windows
	ZA.aComponents[7].create(7,"850px,420px,140px,100px");
	ZA.aComponents[8]=new WORK_Component();
	ZA.aComponents[8].create(8,"560px,620px,140px,100px");
	ZA.aComponents[9]=new WORK_Component();
	ZA.aComponents[9].create(9,"430px,270px,140px,100px");
	ZA.aComponents[10]=new WORK_Component();
	ZA.aComponents[10].create(10,"515px,-50px,75px,55px");
	ZA.drawComponents();
	ZA.createWindow(2,"Card <span style='font-family: Arial; color: #575757; font-weight: normal; text-shadow: none;'>Shop</span>");
	if (!ZA.sUsername) {
		//not logged in
		ZA.createWindow(3,"<span style='margin-left: 622px;'>Register <span style='font-family: Arial; color: #575757; font-weight: normal; text-shadow: none;'>here</span></span>");
		ZA.startJS("register");
	}
	ZA.createWindow(5,"Auction <span style='font-family: Arial; color: #575757; font-weight: normal; text-shadow: none;'>Cards</span>");
	var divComponent=document.getElementById("component_7");
	$(divComponent).css({display:"none"});
	var divComponent=document.getElementById("component_8");
	$(divComponent).css({display:"none"});
	var divComponent=document.getElementById("component_9");
	$(divComponent).css({display:"none"});
	//ZA.createWindow(7,"Play");
	ZA.createWindow(8,"TTGame");
	ZA.createWindow(9,"Compare");
	//not logged in
	if (ZA.sUsername) {
		ZA.createWindow(4,"My Album");
		ZA.startJS("album");
	}
	ZA.startJS('shop');
	ZA.startJS('auction');
	ZA.startJS('userprofile');
	ZA.startJS('paypal');
	ZA.startJS('fbconnect');
	ZA.startJS('twitterconnect');
	ZA.startJS('leaderboard');
	ZA.startJS('notifications');
	//ZA.startJS('compare');
	userAgent = navigator.userAgent;
  uaMatch = userAgent.match(/(Firefox|Chrome|MSIE 9.0|Safari|Opera)/i);
  if(uaMatch == ""){
    ZA.browserPopup();
  }
	
	// ZA.createWindow(6,"Play");
	// var divComponent=document.getElementById("window_6");
	// var divPlay=ZA.createDiv(divComponent,"playgamebutton");
	// divPlay.onclick=ZA.showPlayWindow();

	// ********
	// ********
	// ********
	// ********
   
   if(ZA.sUsername)
   {
      ZA.createWindow(10,"Credits");
      ZA.startJS('credits');
      $("#component_10").hide();
   }
   
	// ********
	// ********
	// ********
	// ********
	// ********
   
	//==========================================//
	//Check for successfull user activation
  
  
         if (ZA.iActivationStatus==1) {
         //displays activation message
            ZA.aWindowUserActivated = new WORK_User_Activated();
            ZA.aWindowUserActivated.create(ZA.sActivationMessage);
            
            ZA.iActivationStatus=0;
            ZA.sActivationMessage="";
            
        }
	//==========================================//
	
};


WORK_App.prototype.createWindow=function(iComponentNo,sName,sPos){
	var iIsModal=0;
	var iControls=1;
	if (!sPos) {
		var aModal=[0,0];
		var divComponent=document.getElementById("component_"+iComponentNo);
		sPos="0px,0px,"+divComponent.style.width+","+divComponent.style.height;
		var aPos=sPos.split(",");
		var iMaxWidth=parseInt(aPos[2]);
		var iMaxHeight=parseInt(aPos[3]);
		ZA.aComponents[iComponentNo].aMaxSize
			=[ZA.iSizeWindowDecor,
		  ZA.iHeightWindowTitle+ZA.iSizeWindowDecor,
		  iMaxWidth-2*ZA.iSizeWindowDecor,
		  iMaxHeight-ZA.iHeightWindowTitle
			-ZA.iSizeWindowDecor-ZA.iSizeWindowDecor];
		var aDataPos=ZA.aComponents[iComponentNo].aMaxSize;
	} else {
		iIsModal=1;
		var divComponent=document.getElementsByTagName("body")[0];
		var aPos=sPos.split(",");
		var aModal=[parseInt(aPos[0]),parseInt(aPos[1])];
		var iMaxWidth=parseInt(aPos[2]);
		var iMaxHeight=parseInt(aPos[3]);
		if (aPos[4]) {
			iControls=parseInt(aPos[4]);
		} else {
			iControls=0;
		}
		var aDataPos
			=[parseInt(aPos[0])+8
		  ,parseInt(aPos[1])+38
		  ,parseInt(aPos[2])-16
		  ,parseInt(aPos[3])-45];
	}
	var divWin=ZA.createDiv(divComponent,"","windowcontainer_"+iComponentNo);
	ZA.setNextZIndex(divComponent);		
	//ZA.setNextZIndex(divWin);
	$(divWin).css({left:aPos[0],top:aPos[1],width:aPos[2],height:aPos[3],opacity:0});
	var divLeft=ZA.createDiv(divWin,"windowleft");
	$(divLeft).css({width:ZA.iSizeWindowDecor+"px"});
	var divTop=ZA.createDiv(divLeft,"windowlefttop");
	$(divTop).css({height:"8px",top:"30px"});
	var divCenter=ZA.createDiv
		(divLeft,"windowleftcenter","windowleft_"+iComponentNo);
	$(divCenter).css({bottom:"7px"
		,height:(iMaxHeight-(ZA.iHeightWindowTitle+ZA.iSizeWindowDecor+7))+"px"});
	var divBottom=ZA.createDiv(divLeft,"windowleftbottom");
	$(divBottom).css({height:ZA.iSizeWindowDecor+"px"});
	var divRight=ZA.createDiv(divWin,"windowright");
	$(divRight).css({width:ZA.iSizeWindowDecor+"px"});
	var divTop=ZA.createDiv(divRight,"windowrighttop");
	$(divTop).css({height:"8px",top:"30px"});
	var divCenter=ZA.createDiv
		(divRight,"windowrightcenter","windowright_"+iComponentNo);
	$(divCenter).css({bottom:"7px"
		,height:(iMaxHeight-(ZA.iHeightWindowTitle+ZA.iSizeWindowDecor+7))+"px"});
	var divBottom=ZA.createDiv(divRight,"windowrightbottom");
	$(divBottom).css({height:ZA.iSizeWindowDecor+"px"});
	var divTitle=ZA.createDiv(divWin,"windowtitle","windowtitle_"+iComponentNo);
	/*if (iControls) {
		if(!ZA.sUsername){
			if(iComponentNo!=6 && iComponentNo!=3){
			divTitle.ondblclick=ZA.maximizeWindow(iComponentNo);
			}
		}else{
			if(iComponentNo!=6){
			divTitle.ondblclick=ZA.maximizeWindow(iComponentNo);
			}
		}
	}*/
	$(divTitle).css({width:(iMaxWidth-2*ZA.iSizeWindowDecor)+"px"
		,height:ZA.iHeightWindowTitle+"px",left:ZA.iSizeWindowDecor+"px"});
		//just write the title, no picture
		var divDesc=ZA.createDiv(divTitle,"windowtitlewords","windowtitledesc_"+iComponentNo);
		divDesc.innerHTML=sName;

	var divMaximize;
	if (iControls==1) {
		if(!ZA.sUsername){
			if(iComponentNo!=6 & iComponentNo!=3){
			divMaximize=ZA.createDiv(divTitle,"windowmaximize2");
			divMaximize.onclick=ZA.maximizeWindow(iComponentNo);

			}
		}else{
			if(iComponentNo!=6){
			divMaximize=ZA.createDiv(divTitle,"windowmaximize2");
			divMaximize.onclick=ZA.maximizeWindow(iComponentNo);

			}
		}
	}
	else{
	// close window
	if (iControls==2){
		divMaximize=ZA.createDiv(divTitle,"windowmaximize2");
		divMaximize.onclick=ZA.closeCloakedWindow(iComponentNo);
		}
	}
	
	var divBottom=ZA.createDiv
		(divWin,"windowtitlebottom","windowtitlebottom_"+iComponentNo);
	$(divBottom).css({width:(iMaxWidth-2*ZA.iSizeWindowDecor)+"px"
		,height:ZA.iSizeWindowDecor+"px",left:ZA.iSizeWindowDecor+"px"
		,top:ZA.iHeightWindowTitle});
	var divBottom=ZA.createDiv(divWin,"windowbottom","windowbottom_"+iComponentNo);
	$(divBottom).css({width:(iMaxWidth-2*ZA.iSizeWindowDecor)+"px"
		,height:ZA.iSizeWindowDecor+"px",left:ZA.iSizeWindowDecor+"px"});
	
	var divData=ZA.createDiv(divComponent,"window","window_"+iComponentNo);
	
//	divData.style.display="none"
	$(divData).css({opacity:0
		,left:(iMaxWidth/2+aModal[0])+"px"
		,top:(iMaxHeight/2+aModal[1])+"px"
		,width:(10)+"px"
		,height:(10)+"px"
		,display:"block"
		});
	$(divWin).animate({opacity:1},1000);
	$(divData).animate({
		left:aDataPos[0]+"px"
		,top:aDataPos[1]+"px"
		,width:aDataPos[2]+"px"
		,height:aDataPos[3]+"px"
		,opacity:1});
		
/*	$(divData).animate({
		left:aDataPos[0]+"px"
		,top:aDataPos[1]+"px"
		,width:aDataPos[2]+"px"
		,height:aDataPos[3]+"px"
		,opacity:1},500,function(){
			var sJSName=(sName.toLowerCase()).replace(/ /g,"");
			ZA.startJS(sJSName);
		});*/
	ZA.setNextZIndex(divData);
	//ZA.setNextZIndex(divMaximize);
};

// close cloaked windows
WORK_App.prototype.closeCloakedWindow=function(iComponentNo){
	var divBody=document.getElementsByTagName("body")[0];
	var divCloak=document.getElementById("bodycloak_"+iComponentNo);
	var divWindow=document.getElementById("windowcontainer_"+iComponentNo);
	var divData=document.getElementById("window_"+iComponentNo);
	if(divWindow) {
		divBody.removeChild(divWindow);
		divBody.removeChild(divData);
	}
	if(divCloak) {
		divBody.removeChild(divCloak);
	}
}

//add loader over any element
WORK_App.prototype.addLoader=function(element,id,loaderbg)
{
	if(typeof(loaderbg) == "undefined"){
		loaderbg = "transparent";
	}
	else{
		if(loaderbg.substr(0,1) != "#"){
			loaderbg = "#"+loaderbg;
		}
	}
	element.append(
		'<div style="top:0px;left:0px;width:100%;height:100%;z-index:9000" id="loader_overlay_'+id+'">'+
			'<div style="background:#000;width:100%;height:100%;opacity:0.65;"></div>'+
			'<table style="width:100%;height:100%;"><tr><td style="text-align:center;vertical-align:middle;">'+
				'<div style="position:relative;background:'+loaderbg+';width:62px;margin-left:auto;margin-right:auto;-moz-border-radius:3px;">'+
					'<img src="_site/busy2.gif" />'+
				'</div>'+
			'</td></tr></table>'+
		'</div>'
	);
}

WORK_App.prototype.removeLoader=function(id)
{
   if($("#loader_overlay_"+id).size()){
      $("#loader_overlay_"+id).remove();
   }
}

WORK_App.prototype.updateCreditView=function(iValue) {
	var span_credit = document.getElementById('spanCreditsID').innerHTML;
	var iCredits = parseInt(span_credit);
	var iVal = parseInt(iValue);
	var iDiff = iCredits - iVal;
	$("#spanCreditsID").empty();
	$("#spanCreditsID").append(iDiff);
};


//create popup window
WORK_App.prototype.createWindowPopup=function(iComponentNo,sName,iWidth,iHeight,iIsModal,iControls){
	var divBody=document.getElementsByTagName("body")[0];
	if (iIsModal){
		var divCloak=ZA.createDiv(divBody,"bodycloak","bodycloak_"+iComponentNo);
		$(divCloak).css({opacity:0});
		if (ZA.sBrowserName=="MSIE"){
      iBrowserHeight=document.documentElement.clientHeight;
    } else {
      iBrowserHeight=window.innerHeight;   
    }
		var iCloakHeight = (iBrowserHeight > 980) ? iBrowserHeight : 980;
		$(divCloak).css({display:"block",height:iCloakHeight});
		ZA.setNextZIndex(divCloak);
		$(divCloak).animate({opacity:0.8});
	}
	var iLeft=parseInt(($(window).width()-iWidth)/2);
	var iTop=parseInt(($(window).height()-iHeight)/2);
	var sPos=iLeft+"px,"+iTop+"px,"+iWidth+"px,"+iHeight+"px";
	if (iControls) {
		sPos+=","+iControls;
	}
	ZA.createWindow(iComponentNo,sName,sPos);
	var divWin=document.getElementById("windowcontainer_"+iComponentNo);
};


WORK_App.prototype.drawComponents=function(){
	var divContainer=document.getElementById("bodycontainer");
	for (var iCount=1;iCount<=10;iCount++) {
		var aPos=ZA.aComponents[iCount].aPos;
		var divComponent=ZA.createDiv(divContainer,"component","component_"+iCount);
		$(divComponent).css({left:aPos[0],top:aPos[1],width:aPos[2],height:aPos[3]});
	}
};


/*********** get browser width and height */
WORK_App.prototype.getBrowserSize=function(){
	if (ZA.sBrowserName=="MSIE"){
		ZA.iBrowserWidth=document.documentElement.clientWidth;
		ZA.iBrowserHeight=document.documentElement.clientHeight;
	} else {
		ZA.iBrowserWidth=window.innerWidth;
		ZA.iBrowserHeight=window.innerHeight;		
	}
	//TODO: ai IE
	if (!ZA.iBrowserHeight){
		ZA.iBrowserHeight=500;
	}
};


/*********** lightweight scanning for valid email address */
WORK_App.prototype.getValidEmailAddress=function(sEmailAddress){
   var sReg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   if(!sReg.test(sEmailAddress)) {
      return 0;
   } else {
	 		return 1;
	 }
};


/*********** base64 Encoder */
WORK_App.prototype.base64Encode=function(data){
	if (typeof(btoa) == 'function') return btoa(data);//use internal base64 functions if available (gecko only)
	var b64_map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
	var byte1, byte2, byte3;
	var ch1, ch2, ch3, ch4;
	var result = new Array(); //array is used instead of string because in most of browsers working with large arrays is faster than working with large strings
	var j=0;
	for (var i=0; i<data.length; i+=3) {
		byte1 = data.charCodeAt(i);
		byte2 = data.charCodeAt(i+1);
		byte3 = data.charCodeAt(i+2);
		ch1 = byte1 >> 2;
		ch2 = ((byte1 & 3) << 4) | (byte2 >> 4);
		ch3 = ((byte2 & 15) << 2) | (byte3 >> 6);
		ch4 = byte3 & 63;
		
		if (isNaN(byte2)) {
			ch3 = ch4 = 64;
		} else if (isNaN(byte3)) {
			ch4 = 64;
		}

		result[j++] = b64_map.charAt(ch1)+b64_map.charAt(ch2)+b64_map.charAt(ch3)+b64_map.charAt(ch4);
	}

	return result.join('');
}


/********* Decodes Base64 formatted data **/

WORK_App.prototype.base64Decode=function(data){
	data = data.replace(/[^a-z0-9\+\/=]/ig, '');// strip none base64 characters
	if (typeof(atob) == 'function') return atob(data);//use internal base64 functions if available (gecko only)
	var b64_map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
	var byte1, byte2, byte3;
	var ch1, ch2, ch3, ch4;
	var result = new Array(); //array is used instead of string because in most of browsers working with large arrays is faster than working with large strings
	var j=0;
	while ((data.length%4) != 0) {
		data += '=';
	}
	
	for (var i=0; i<data.length; i+=4) {
		ch1 = b64_map.indexOf(data.charAt(i));
		ch2 = b64_map.indexOf(data.charAt(i+1));
		ch3 = b64_map.indexOf(data.charAt(i+2));
		ch4 = b64_map.indexOf(data.charAt(i+3));

		byte1 = (ch1 << 2) | (ch2 >> 4);
		byte2 = ((ch2 & 15) << 4) | (ch3 >> 2);
		byte3 = ((ch3 & 3) << 6) | ch4;

		result[j++] = String.fromCharCode(byte1);
		if (ch3 != 64) result[j++] = String.fromCharCode(byte2);
		if (ch4 != 64) result[j++] = String.fromCharCode(byte3);	
	}

	return result.join('');
}


/*********** returns node value from XML string data */
WORK_App.prototype.getXML=function(sData,sElement){
  if (ZA.sBrowserName=="MSIE"){
    var xData=new ActiveXObject("Microsoft.XMLDOM");
    xData.async="false";
    xData.loadXML(sData);
  } else {
    var xData=new DOMParser();  
    xData=xData.parseFromString(sData,"text/xml");
  }
  if (ZA.sBrowserName=="MSIE"){
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





WORK_App.prototype.formatUsername=function(username)
{
   var formattedUsername;
   if(username.indexOf('@') > -1)
   {
      formattedUsername = username.substring(0,username.indexOf('@'));
   }
   else
   {
      formattedUsername = username;
   }
   return formattedUsername;
};


//initialize variables and build page
WORK_App.prototype.init=function(sXMLInit){
	//console.log(sXMLInit)
	ZA.sXML = sXMLInit;
	
	//load activation status
	ZA.iActivationStatus=ZA.getXML(sXMLInit,"activate_status");
	  ZA.sActivationMessage=ZA.getXML(sXMLInit,"activate_message");
	  /////////////////////////
  
	ZA.sUsername=ZA.getXML(sXMLInit,"username");
	ZA.sUserCredits=ZA.getXML(sXMLInit,"credit");
	ZA.iProcess=parseInt(ZA.getXML(sXMLInit,"process"));
	ZA.iWidthContainer=parseInt("833px");
	ZA.iHeightContainer=parseInt("780px");
	ZA.iHeightFooter=parseInt(ZA.getXML(sXMLInit,"footerheight"));
	ZA.iHeightHeader=parseInt(ZA.getXML(sXMLInit,"headerheight"));
	ZA.iHeightHeader=parseInt(ZA.getXML(sXMLInit,"headerheight"));
	ZA.iWidthMargin=parseInt(ZA.getXML(sXMLInit,"componentmarginx"));
	ZA.iHeightMargin=parseInt(ZA.getXML(sXMLInit,"componentmarginy"));
	ZA.iHeightWindowTitle=parseInt(ZA.getXML(sXMLInit,"windowtitleheight"));
	ZA.iSizeWindowDecor=parseInt(ZA.getXML(sXMLInit,"windowdecorsize"));
	ZA.iOpacityHeader=1 ;
	//ZA.getXML(sXMLInit,"headeropacity")
	ZA.iOpacityWindowInActive=1;
	//ZA.getXML(sXMLInit,"windowopacityinactive")
	ZA.iOpacityWindow=ZA.getXML(sXMLInit,"windowopacity");
	ZA.sMenuItemsLeft=ZA.getXML(sXMLInit,"menuleft");
	//ZA.sMenuItemsTop=ZA.getXML(sXMLInit,"menutop");
	ZA.sMenuItemsBottom=ZA.getXML(sXMLInit,"menubottom");
	ZA.sURL=ZA.getXML(sXMLInit,"url");
	var divBody=document.getElementsByTagName("body")[0];
	var divHeader=ZA.createDiv(divBody,"","bodyheader");
	$(divHeader).css({height:112,opacity:1});
	
	
	ZA.createPage();
	
	var divFooter=ZA.createDiv(divBody,"","bodyfooter");
	var divFooterCon=ZA.createDiv(divFooter,"bodyfooter_container")
	var divFooterBlock=ZA.createDiv(divFooterCon,"bodyfooter_social");
	var divFB = ZA.createDiv(divFooterBlock,"social_fb","","a");
	  divFB.href="https://apps.facebook.com/sarugbycards";
	  divFB.target="_blank";
	var divTwitter = ZA.createDiv(divFooterBlock,"social_twitter","","a");
	  divTwitter.href="https://twitter.com/#!/bokrugby";
	  divTwitter.target="_blank";
	var divWeb = ZA.createDiv(divFooterBlock,"social_web","","a");
	  divWeb.href="http://www.sarugby.net";
	  divWeb.target="_blank";
	// var divYou = ZA.createDiv(divFooterBlock,"social_youtube");
	// var divVimeo = ZA.createDiv(divFooterBlock,"social_vimeo");
	
	var xbarlogo = ZA.createDiv(divFooterCon,"xbarlogo");
	var mytcglogo = ZA.createDiv(divFooterCon,"mytcglogo");
	
	ZM.createTop();
	// ZM.createSocial();
	ZA.oPlayerBar = new objPlayerBar;
    ZA.oPlayerBar.create(ZA.getXML(sXMLInit,"credit"),ZA.getXML(sXMLInit,"xp"));
    
	if(ZA.getXML(sXMLInit,"popup")=="1")
	{
		var iWidth = 480;
		var iHeight = 320;
		var iLeft = 380;
		var iTop = 240;
		
		if(ZA.getXML(sXMLInit,"freebie")!="1"){
			iWidth = 320;
			iHeight = 240;
			iLeft = 220;
			iTop = 160;
		}
		
		
		if(ZA.getXML(sXMLInit,"freebie")=="1"){
		  
		  ZA.createWindowPopup(-1,"",iWidth,iHeight,1,0);
	      var divWindow=document.getElementById("window_-1");
	      var divData=ZA.createDiv(divWindow);
	      $(divData).css({
	        width:"100%",
	      });
		
		  var divHead = ZA.createDiv(divData);
      	  $(divHead).css({position:"absolute",top:30,left:100});
     	  $(divHead).html("<b>Welcome to SA Rugby Cards</b><br>Here is a little something to start you off with.");
		    
			var divFree = ZA.createDiv(divData);
			$(divFree).css({
				width:"100%",
				top:75
			});
			
			var starterPacks = parseInt(ZA.getXML(sXMLInit,"starterpackscount"));
			var divText = ZA.createDiv(divFree);
			$(divText).css({position:"relative"});
			$(divText).html('<strong>Choose your FREE starter pack:</strong>');
			var divPacks = ZA.createDiv(divFree);
			$(divPacks).css({
				position:"relative",
				width:(starterPacks*109),
				marginTop:20,
				marginLeft:"auto",
				marginRight:"auto"
			});
			//add starter packs
			if(starterPacks > 0){
				var i;
				for(i=0; i<starterPacks; i++){
					var div = ZA.createDiv(divPacks,"starterPack",ZA.getXML(sXMLInit,"starterpacks/starterpack_"+i+"/product_id"));
					$(div).html(
						'<img src="'+ZA.getXML(sXMLInit,"starterpacks/starterpack_"+i+"/image")+'" width="72" height="102" />'+
						'<div style="width:90px;text-align:center;margin-top:15px;">'+ZA.getXML(sXMLInit,"starterpacks/starterpack_"+i+"/description")+'</div>'
					);
				}
				//click event handler
				$(".starterPack").click(function(){
					var productId = $(this).attr('id');
					ZA.callAjax("_app/?starter=1&product="+productId,function(xml){
						var result = ZA.getXML(xml,"result");
						if(result == '0'){
							var icon = "-697px -63px";
							ZA.showWindow(icon,ZA.getXML(xml,"message"),5000);
							ZA.clickCloseA();
						}
						else{
				        	//Reload my album
							ZA.clickCloseA();
							$(ZL.divData).html('<div class="loader"></div>');
							ZA.callAjax(ZL.sURL+"?init=1",function(xml){
								ZL.init(xml);
							});
						}
					});
				});
			}
			
			var divButton=ZE.createButton(divData,iLeft,iTop,70,"OK",function(){
        	ZA.clickCloseA();
      });
			
		}
		
		if(divFree){
			$(divButton).hide();
		}
	}
    
};

  WORK_App.prototype.gotCredits=function(txtNotice,credits){
        ZA.createWindowPopup(-2,"",480,160,1,0);
        var divWindow=document.getElementById("window_-2");
        var divData=ZA.createDiv(divWindow);
        $(divData).css({
          width:"100%",
          padding:5
        });
        var divIcon=ZA.createDiv(divData,"divgotcredits");
        var divMemo=ZA.createDiv(divData);
        $(divMemo).css({textAlign:"left",position:"absolute",left:"140px",top:"10px"});
        $(divMemo).html('<b>Congratulations</b><br>You have received '+txtNotice+'.<br> Spend it wisely.');
        if(credits){
          ZA.oPlayerBar.update({credits:credits});
        }
        var divButton = ZA.createDiv(divData,"cmdButton");
        $(divButton).css({
        	top:83,
        	right:20
        });
        $(divButton).html('Close');
        $(divButton).click(function(){
          $("#bodycloak_-2").remove();
          $("#windowcontainer_-2").remove();
          $("#window_-2").remove();
        });
      };

//close any popup window
WORK_App.prototype.clickCloseA=function()
{
	var divBody=document.getElementsByTagName("body")[0];
	var divCloak=document.getElementById("bodycloak_-1");
	var divWindow=document.getElementById("windowcontainer_-1");
	var divData=document.getElementById("window_-1");
	if(divWindow) {
		divBody.removeChild(divWindow);
		divBody.removeChild(divData);
	}
	if(divCloak) {
		divBody.removeChild(divCloak);
	}
};


//maximize window
WORK_App.prototype.maximizeWindow=function(iComponentNo){
	return function() {
	  if((iComponentNo!=6)||(ZA.sUsername)){
	    ZA.maximizeWindowA(iComponentNo);
	  }
		if(iComponentNo!=9){
			$("#cardfull").remove();
		}
	};
};


//maximize window action
WORK_App.prototype.maximizeWindowA=function(iComponentNo){
	var fFunction=0;
	if (ZA.aComponents[iComponentNo].fMaximizeFunction) {
		fFunction=ZA.aComponents[iComponentNo].fMaximizeFunction;
	}
	if (ZA.aComponents[iComponentNo].iIsMaximized) {
		//set this window as minimized
		ZA.aComponents[iComponentNo].iIsMaximized=0;
	} else {
		if(iComponentNo != 9){
			//check if another window is maximized and close it
			var i;
			var temp = [];
			for(i=2; i<=10; i++){
				if(i!=iComponentNo && i!=6){
					if(ZA.aComponents[i].iIsMaximized){
						ZA.maximizeWindowA(i);
					}
				}
			}
		}
		//set this window as maximized
		ZA.aComponents[iComponentNo].iIsMaximized=1;
	}
	var divComponent=document.getElementById("component_"+iComponentNo);
	ZA.setNextZIndex(divComponent);		
	var divTitle=document.getElementById("windowtitle_"+iComponentNo);
	var divData=document.getElementById("window_"+iComponentNo);
	var divWindow=document.getElementById("windowcontainer_"+iComponentNo);
	var divBottom=document.getElementById("windowbottom_"+iComponentNo);
	var divTitleBottom=document.getElementById("windowtitlebottom_"+iComponentNo);
	var divLeft=document.getElementById("windowleft_"+iComponentNo);
	var divRight=document.getElementById("windowright_"+iComponentNo);
	var divTitleDesc=document.getElementById("windowtitledesc_"+iComponentNo);
	
	if (ZA.aComponents[iComponentNo].iIsMaximized) {
		//maximize window
		var sClass=divTitleDesc.className;
		divTitleDesc.className=sClass+"_active";
		ZA.showArrows(iComponentNo);
		$(divComponent).animate({
			left:0+"px"
			,top:"10px"
			,width:(ZA.iWidthContainer)+"px"
			,height:(ZA.iHeightContainer-80)+"px"});
		$(divTitle).animate({width:(ZA.iWidthContainer-49)+"px"});
		$(divData).animate({
			width:(ZA.iWidthContainer-49)+"px"
			,height:(ZA.iHeightContainer-125)+"px"});
		$(divWindow).animate({
			width:(ZA.iWidthContainer-33)+"px"
			,height:(ZA.iHeightContainer-80)+"px",opacity:1});
		$(divBottom).animate({width:(ZA.iWidthContainer-49)+"px"});
		$(divTitleBottom).animate({width:(ZA.iWidthContainer-49)+"px"});
		$(divLeft).animate({height:(ZA.iHeightContainer-125)+"px"});
		$(divRight).animate({height:(ZA.iHeightContainer-125)+"px"});
		
		//how it works window strips animate
		if ((!ZA.sUsername)&&(iComponentNo==4)) {
			ZH.maximize();
		}
	}
	else
	{
		//restore window
		var aClass=divTitleDesc.className.split("_");
		divTitleDesc.className=aClass[0];
		ZA.showArrows(0);
		$(divComponent).animate({
			left:ZA.aComponents[iComponentNo].aPos[0]
			,top:ZA.aComponents[iComponentNo].aPos[1]
			,width:ZA.aComponents[iComponentNo].aPos[2]
			,height:ZA.aComponents[iComponentNo].aPos[3]});
		$(divTitle).animate({
			width:(parseInt(ZA.aComponents[iComponentNo].aPos[2])-16)+"px"});
		$(divData).animate({
			width:(parseInt(ZA.aComponents[iComponentNo].aPos[2])-16)+"px"
			,height:(parseInt(ZA.aComponents[iComponentNo].aPos[3])-46)+"px"
			});
		$(divWindow).animate({
			width:(parseInt(ZA.aComponents[iComponentNo].aPos[2]))+"px"
			,height:(parseInt(ZA.aComponents[iComponentNo].aPos[3]))+"px"
			,opacity:ZA.iOpacityWindowInActive},function(){
				//hide play window
				if (iComponentNo==7) {
					$(divComponent).css({display:"none"});
				}
				//hide play window
				if (iComponentNo==8) {
					$(divComponent).css({display:"none"});
				}
				//hide compare window
				if (iComponentNo==9) {
					$(divComponent).css({display:"none"});
				}
				//hide credits window
				if (iComponentNo==10) {
					$(divComponent).css({display:"none"});
				}
			});
		$(divBottom).animate({
			width:(parseInt(ZA.aComponents[iComponentNo].aPos[2])-16)+"px"});
		$(divTitleBottom).animate({
			width:(parseInt(ZA.aComponents[iComponentNo].aPos[2])-16)+"px"});
		$(divLeft).animate({
			height:(parseInt(ZA.aComponents[iComponentNo].aPos[3])-45)+"px"});
		$(divRight).animate({
			height:(parseInt(ZA.aComponents[iComponentNo].aPos[3])-45)+"px"});
		
		//how it works window strips animate
		if ((!ZA.sUsername)&&(iComponentNo==4)) {
			ZH.restore();
		}
	}
	if (fFunction) {
		fFunction();
	}
};


//refreshes the browser 
WORK_App.prototype.refreshBrowser=function(){
	location.reload(true);
};


//set next z-index
WORK_App.prototype.setNextZIndex=function(divName){
	ZA.iZIndex++;
	divName.style.zIndex=ZA.iZIndex;
};


/*********** disable selecting of text on div, e.g. while dragging mouse */
WORK_App.prototype.setSelectNone=function(divName){
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


//set the input type
WORK_App.prototype.setType=function(divName,sType){
	if (ZA.sBrowserName=="MSIE"){
		var divTemp=divName.cloneNode(false);
		divTemp.type=sType;
		divName.parentNode.replaceChild(divTemp,divName);
	} else {
		divName.type=sType;
	}
};


WORK_App.prototype.showArrows=function(iComponentNo){
	if (!iComponentNo) {
		for (var iCount=2;iCount<8;iCount++) {
			var divArrow=document.getElementById("arrow_"+iCount);
			if (divArrow) {
				$(divArrow).css({display:"block"});				
			}
		}
	} else {
		for (var iCount=2;iCount<8;iCount++) {
			var divArrow=document.getElementById("arrow_"+iCount);
			if (divArrow) {
				if (iCount!=iComponentNo) {
					$(divArrow).css({display:"none"});				
				}
			}
		}		
	}
};


WORK_App.prototype.showPlayWindow=function(){
	return function() {
		var divComponent=document.getElementById("component_8");
		$(divComponent).css({display:"block"});
		ZA.maximizeWindowA(8);
		ZA.startJS("toptrumps");
	};
};


WORK_App.prototype.showLeaderboard=function(){
	CLB.init();
};

WORK_App.prototype.showNotifications=function(){
 	ZA.callAjax("_app/notifications/?init=1",function(xml)
	{
		CRT.init(xml);
	});
};

WORK_App.prototype.showCompare=function(cardid){
	if(typeof(cardid)!="undefined"){
		//set left card in component and reset right
		ZC.loadCard(cardid);
	}
	var divComponent=document.getElementById("component_9");
	$(divComponent).css({display:"block"});
	ZA.maximizeWindowA(9);
};


WORK_App.prototype.showMessage=function(message,sentiment,interval)
{
	var icon = "-697px -63px";
	if(typeof(sentiment)=="undefined" || sentiment=="+"){
		icon = "-667px -63px";
	}
	if(typeof(interval)=="undefined"){
		interval = 5000;
	}
	ZA.showWindow(icon,message,interval);
};



WORK_App.prototype.showWindow=function(icon,message,delay)
{
	if (typeof delay == "undefined") delay = 500;
	var divBody = document.getElementsByTagName("body")[0];
	var divWin = ZA.createDiv(divBody,"messageWindow","","div");
	$(divWin).html(message);
	var divCheckIcon = ZA.createDiv(divWin,"","","div");
	$(divCheckIcon).css({
		top:5,
		left:5,
		width:23,
		height:21,
		backgroundImage:"url("+ZA.imgAll+")",
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
    $(divWin).remove();
   }
};

WORK_App.prototype.sendCardScreen=function(card_id)
{
    var i = ZC.cards[card_id];
	var description = ZA.getXML(ZC.sXML,"cards/card_"+i+"/description");
	var image = ZA.getXML(ZC.sXML,"cards/card_"+i+"/path")+'cards/'+ZA.getXML(ZC.sXML,"cards/card_"+i+"/image");
	var quality = ZA.getXML(ZC.sXML,"cards/card_"+i+"/quality");
	var ranking = ZA.getXML(ZC.sXML,"cards/card_"+i+"/ranking");
	var avgranking = ZA.getXML(ZC.sXML,"cards/card_"+i+"/avgranking");
	var value = ZA.getXML(ZC.sXML,"cards/card_"+i+"/value");
	//var possess = ZA.getXML(ZC.sXML,"cards/card_"+i+"/possess");
	
	ZA.createWindowPopup(777,"Send to Friend",480,360,1,0);
    var divWindow=document.getElementById("window_777");
    var divData=ZA.createDiv(divWindow);
    $(divData).css({
      width:"100%",
      height:"100%"
    });
    
    //card to send
    var divDetail = ZA.createDiv(divData);
    $(divDetail).css({
    	width:432,
    	height:110,
    	top:10,
    	left:10,
    	background:"url(_site/line.gif) repeat",
    	border:"5px solid #999",
    	"-moz-border-radius":"5px"
    });
        
	    //card thumbnail
	    var thumb = ZA.createDiv(divDetail);
	    $(thumb).css({
	    	width:64,
	    	height:90,
	    	top:10,
	    	left:10,
	    	background:"#CCC",
	    	borderRight:"1px solid #000",
	    	borderBottom:"1px solid #000"
	    });
	    $(thumb).html(
	    	'<img src="'+image+'_web.png" />'
	    );/*
		if(possess > 0){
			var own = ZA.createDiv(thumb,"","ownIndicator");
			$(own).css({
				background:"url(_site/all.png) -400px -5px no-repeat",
				color:"#956A0D",
				width:21,
				height:17,
				paddingTop:9,
				top:-1,
				right:3
			});
			if(possess > 1){
				$(own).html(possess.toString());
			}
		}*/
	    //card info
	    var info = ZA.createDiv(divDetail);
	    $(info).css({
	    	left:84,
	    	top:20,
	    	color:"#FFF",
	    	textAlign:"left"
	    });
	    $(info).html(
	    	'<span style="font-weight:bold;font-size:16px;">'+description+'</span><br /><br />'+
	    	'Quality: '+quality+'<br />'+
	    	'Ranking: '+ranking+'<br />'+
	    	'Average Ranking: '+avgranking+'<br />'+
	    	'Value: '+value+' TCG'
	    );
	    
	    //send info
	    var sendinfo = ZA.createDiv(divDetail);
	    $(sendinfo).css({
	    	width:150,
	    	height:90,
	    	top:10,
	    	right:10,
	    	background:"#EFEFEF",
	    	border:"1px solid #2A333A",
	    	"-moz-border-radius":"5px"
	    });
	    var title = ZA.createDiv(sendinfo);
	    $(title).css({
	    	marginTop:5,
	    	position:"relative",
	    	color:"#999"
	    });
	    $(title).html('Send to Friend:');
	    var friend = ZA.createDiv(sendinfo,"","friendName");
	    $(friend).css({
	    	position:"relative",
	    	marginTop:13,
	    	fontSize:16,
	    	height:16,
	    	fontWeight:"bold",
	    	overflow:"hidden"
	    });
	    var send = ZA.createDiv(sendinfo,"cmdButton","sendCard");
	    $(send).css({
	    	bottom:10,
	    	left:10,
	    	width:110
	    });
	    $(send).html('Send Card').addClass('cmdButtonDisabled');
	    
	    var sent = ZA.createDiv(divData,"","cardSent");
	    $(sent).css({
	    	width:442,
	    	height:130,
	    	top:142,
	    	left:10,
	    	border:"1px solid #999",
	    	background:"#EFEFEF",
	    	fontSize:16,
	    	fontWeight:"bold",
	    	"-moz-border-radius":"5px"
	    }).hide();
	    
		//send card event handler
		$("#sendCard").click(function(){
			if(!$(this).hasClass('cmdButtonDisabled')){
				$(this).addClass('cmdButtonDisabled');
				$("#searchHolder").hide();
				$("#cardSent").show();
				ZA.addLoader($("#window_777"),77);
				var friend = $("#friendResultsList").find(".selected").attr('alt');
				var name = $("#friendName").html();
				//run php
				ZA.callAjax(ZL.sURL+"?send=1&card="+card_id+"&friend="+friend+"&name="+name,function(xml){
					var result = ZA.getXML(xml,"result");
					if(result == '1'){
						var icon = "-667px -63px";
						ZA.showWindow(icon,'Card was sent to your friend',5000);
						//remove the opened card
						$("#cardfull").remove();
						//reload the card comparison component
						ZC = new WORK_Compare();
						$(ZC.divData).empty();
						ZA.callAjax(ZC.sURL+"?init=1",function(xml){
							ZC.init(xml);
							//reload the album
							$(ZL.divData).empty();
							ZA.callAjax(ZL.sURL+"?init=1",function(xml){
								ZL.init(xml);
								//close send screen
								$("#bodycloak_777").remove();
								$("#windowcontainer_777").remove();
								$("#window_777").remove();
							});
						});
					}
					else{
						ZA.removeLoader(77);
						var icon = "-697px -63px";
						ZA.showWindow(icon,ZA.getXML(xml,"message"),500);
						$("#cardSent").hide();
						$("#searchHolder").show('fast');
						$("#sendCard").removeClass('cmdButtonDisabled');
					}
				});
			}
		});
	
	var divSearch = ZA.createDiv(divData,"","searchHolder");
	$(divSearch).css({
		top:140,
		height:175
	});
	
	//search form
	var divForm = ZA.createDiv(divSearch,"","searchForm");
	$(divForm).css({
		width:442,
		left:10,
		top:2,
		height:130,
		background:"#EFEFEF",
		border:"1px solid #999",
		"-moz-border-radius":"5px"
	});
	$(divForm).html(
		'<div style="position:relative;margin:30px auto 5px;">Find Friend:</div>'+
		'<div style="position:relative;margin-bottom:10px;"><input type="text" id="txtFriend" style="width:190px;" /></div>'+
		'<div class="cmdButton" id="cmdFindFriend" style="position:relative;margin-left:auto;margin-right:auto;margin-bottom:15px;width:40px;">Search</div>'
	);
	
		//search event
		$("#txtFriend").keydown(function(event){
			if(event.which == 13){
				$("#cmdFindFriend").click();
			}
		});
		$("#cmdFindFriend").click(function(){
			$("#txtFriend").blur();
			ZA.addLoader($("#searchForm"));
			var searchstring = $("#txtFriend").val().trim();
			$("#txtFriend").val(searchstring);
			ZA.callAjax(ZL.sURL+'?search=1&string='+searchstring,function(xml){
				ZA.removeLoader();
				$("#searchForm").hide('fast');
				var found = parseInt(ZA.getXML(xml,"found"));
				if(found > 0){
					$("#friendResultsList").empty();
					var username;
					var user_id;
					var rowclass;
					for(var i=0; i<found; i++){
						username = ZA.getXML(xml,"results/result_"+i+"/username");
						user_id = ZA.getXML(xml,"results/result_"+i+"/user_id");
						rowclass = (i%2)?'even':'odd';
						$("#friendResultsList").append(
							'<p class="'+rowclass+'" alt="'+user_id+'">'+username+'</p>'
						);
					}
					$("#friendResultsList").find("p").click(function(){
						if(!$(this).hasClass('selected')){
							$("#friendResultsList").find(".selected").removeClass('selected');
							$(this).addClass('selected');
							$("#sendCard").removeClass('cmdButtonDisabled');
							$("#friendName").hide().html($(this).html()).show('fast');
						}
					});
				}
				else{
					$("#friendResultsList").empty().html(
						'<div style="position:relative;margin-top:45px;color:#fff;">No results for `'+searchstring+'`</div>'
					);
				}
				$("#friendResults").show('fast',function(){
					$("#searchAgain").show();
					$("#friendResultsHolder").jScrollPane({
						enableKeyboardNavigation:false,
						mouseWheelSpeed:18,
						trackClickSpeed:18,
						verticalGutter:1
					});
				});
			});
		});
	
	//search results
	var divResults = ZA.createDiv(divSearch,"","friendResults");
	$(divResults).css({
		width:442,
		top:0,
		left:10
	});
	$(divResults).html(
		'<div style="position:relative;margin-bottom:10px;font-weight:bold;">Search Results:</div>'+
		'<div id="friendResultsHolder" style="position:relative;width:100%;height:108px;overflow:hidden;border:1px solid #999;background:#999;-moz-border-radius:5px;">'+
			'<div id="friendResultsList" style="position:relative;width:100%;"></div>'+
		'</div>'
	).hide();
	
	var div = ZA.createDiv(divSearch,"cmdButton","searchAgain");
	$(div).css({
		bottom:10,
		left:10,
		width:90
	}).hide();
	$(div).html("&#9668; Search again");
	
		//search again
		$("#searchAgain").click(function(){
			$(this).hide();
			$("#friendResults").hide('fast');
			$("#searchForm").show('fast',function(){
				$("#txtFriend").focus();
			});
		});
		
    //close window button
    var close = ZA.createDiv(divData,"cmdButton");
    $(close).css({
    	right:10,
    	bottom:10
    });
    $(close).html('Close');
    $(close).click(function(){
		$("#bodycloak_777").remove();
		$("#windowcontainer_777").remove();
		$("#window_777").remove();
    });
};

WORK_App.prototype.startJS=function(docName){
  var bAccept = true;
  var sSrc=docName.toLowerCase();
  var e=document.getElementsByTagName("script");
  for(var i = 0; i < e.length; i++){
    if(e[i].src.indexOf(sSrc+".js") > 0){
      bAccept = false;
      break;
    }
  }
  if(bAccept){
    // Create the Script Object
    var script = document.createElement('script');    
    script.src ="_app/"+sSrc+"/"+sSrc +'.js';
    script.type = 'text/javascript';
    var head = document.getElementsByTagName('head').item(0);
    head.appendChild(script);
  }
};

WORK_App.prototype.xmlFilter=function(sXML,sKey,sFilter){
  var xmlDom = ZA.stringToXML(sXML);
  var iCount = 0;
  var someText = $(xmlDom).find("pack_0");
  console.log(someText);
  //while(){}
};

WORK_App.prototype.stringToXML = function(sXML){
  if (window.ActiveXObject){
    var doc=new ActiveXObject('Microsoft.XMLDOM');
    doc.async='false';
    doc.loadXML(text);
  } else {
    var parser=new DOMParser();
    var doc=parser.parseFromString(sXML,'text/xml');
  }
  return doc;
};

//finish APP class	
		WORK_App._iInited=1;
	}
}


//==============================================================================
//COMPONENT CLASS 
function WORK_Component(){
	this.aMaxSize=0;
	this.aPos=[];
	this.iAnimating=0;
	this.iIsMaximized=0;
	this.fMaximizeFunction=0;
	
	if (typeof WORK_Component._iInited=="undefined"){


WORK_Component.prototype.create=function(iComponentNo,sSize){
	var aPos=[];
	var aSize=sSize.split(",");
	if (!ZA.sUsername){ 
	//logged out
	switch (iComponentNo) {
		case 1:
		//left
			//aPos[0]="505px";
		//top
			//aPos[1]=parseInt(ZA.aComponents[1].aPos[3])+(2*ZA.iHeightMargin-78)+"px";
		//width
			//aPos[2]="250px";
		//height
			//aPos[3]="230px";
		break;
		case 2:
			aPos[0]="0px";
			aPos[1]="480px";
			aPos[2]="800px";
			aPos[3]="230px";
		break;
		case 3:
			aPos[0]="0px";
			aPos[1]="10px";
			aPos[2]="800px";
			aPos[3]="230px";
		break;
		case 4:
			// aPos[0]="0px";
			// aPos[1]=parseInt(ZA.aComponents[1].aPos[3])+(2*ZA.iHeightMargin+155)+"px";
			// aPos[2]=aSize[0];
			// aPos[3]="300px";
		break;
		case 5:
			aPos[0]="0px";
			aPos[1]="245px";
			aPos[2]="800px";
			aPos[3]="230px";
		break;
		case 6:
			// aPos[0]="505px";
			// aPos[1]=parseInt(ZA.aComponents[1].aPos[3])+(2*ZA.iHeightMargin-78)+"px";
			// aPos[2]="250px";
			// aPos[3]="230px";
		break;
		}
	}
	//logged in
	else{
	switch (iComponentNo) {
		case 1:
			//left
			aPos[0]="505px";
			//top
			aPos[1]="0px";
			//width
			aPos[2]="250px";
			//height
			aPos[3]="230px";
		break;
		
		case 2:
			aPos[0]="0px";
			aPos[1]=parseInt(ZA.aComponents[1].aPos[3])+(2*ZA.iHeightMargin-78)+"px";
			aPos[2]="800px";
			aPos[3]="230px";
		break;
		
		case 3:
			// aPos[0]="505px";
			// aPos[1]=parseInt(ZA.aComponents[1].aPos[3])+(2*ZA.iHeightMargin-78)+"px";
			// aPos[2]="250px";
			// aPos[3]="230px";
		break;
		
		case 4:
			aPos[0]="0px";
			aPos[1]="10px";
			aPos[2]="800px";
			aPos[3]="230px";
		break;
		
		case 5:
			aPos[0]="0px";
			aPos[1]="480px";
			aPos[2]="800px";
			aPos[3]="230px";
		break;
		
		case 6:
			// aPos[0]="505px";
			// aPos[1]=parseInt(ZA.aComponents[2].aPos[2])+(2*ZA.iHeightMargin-45)+"px";
			// aPos[2]="250px";
			// aPos[3]="230px";
		break;
		
		case 7:
			aPos[0]=aSize[0];
			aPos[1]=aSize[1];
			aPos[2]=aSize[2];
			aPos[3]=aSize[3];
		break;
		
		case 8:
			aPos[0]=aSize[0];
			aPos[1]=aSize[1];
			aPos[2]=aSize[2];
			aPos[3]=aSize[3];
		break;
		
		case 9:
			aPos[0]=aSize[0];
			aPos[1]=aSize[1];
			aPos[2]=aSize[2];
			aPos[3]=aSize[3];
		break;
   
		case 10:
			aPos[0]=aSize[0];
			aPos[1]=aSize[1];
			aPos[2]=aSize[2];
			aPos[3]=aSize[3];
		break;
		}
	}
	ZA.aComponents[iComponentNo].aPos=aPos;
};


		//finish COMPONENT class	
		WORK_Component._iInited=1;
	}
}


/** ========================================================================
LOGIN CLASS
*/
function WORK_Login(){
if (typeof WORK_Login._iInited=="undefined"){


/*********** close login window */
WORK_Login.prototype.clickClose=function(){
	return function() {
		ZA.aWindowLogin.clickCloseA();
	};
};


/*********** close login window action */
WORK_Login.prototype.clickCloseA=function(){
	var divBody=document.getElementsByTagName("body")[0];
	var divCloak=document.getElementById("bodycloak_0");
	var divLogin=document.getElementById("windowcontainer_0");
	var divData=document.getElementById("window_0");
	if (divLogin) {
		divBody.removeChild(divLogin);
		divBody.removeChild(divData);
	}
	if (divCloak) {
		divBody.removeChild(divCloak);
	}
};


/*********** create login window */
WORK_Login.prototype.create=function(){
	var divBody=document.getElementsByTagName("body")[0];
	var iDocHeight=document.documentElement.scrollHeight;
	ZA.createWindowPopup(0,"Login",350,240,1,0);
	var divData=document.getElementById("window_0");
	//$(divData).css({"background":"-webkit-linear-gradient(top,#DAD8D9,#8C8C8C)","background":"-moz-linear-gradient(top,#DAD8D9,#8C8C8C)","background":"-ms-linear-gradient(top,#DAD8D9,#8C8C8C)","background":"-o-linear-gradient(top,#DAD8D9,#8C8C8C)","background-color":"#DAD8D9"});
	$(divData).css({"border-radius":"9px"});
	
	var divInput=ZE.createInput(divData,5,25,100,30,"Email","loginusername");
	divInput.focus();
	
	$(divData).keydown(function (e){
    if(e.keyCode == 13){
        var keydown =eval("ZA.aWindowLogin.clickLogin()");
        keydown();
    }
  });
	
	var divInput=ZE.createInput(divData,5,65,100,30,"Password","loginpassword");
	ZA.setType(divInput,"password");
		
	var checkBoxLabel=ZA.createDiv(divData,"","","div");
		$(checkBoxLabel).css({top:105,left:5,height:20});
		checkBoxLabel.innerHTML="<input type='checkbox' id='chkRemember' /> Remember me";
		$("#chkRemember").change(function(){
		  ZA.iCheckBoxChecked = this.checked;
    });
	
	var divATag=ZE.createATag(divData,"",5,130,"Forgot Username or Password","ZA.aWindowLogin.clickForgotPassword()");
	
	//Login response area
	var loginResponse=ZA.createDiv(divData,"","loginResponse","div");
  $(loginResponse).css({color:"#F2C126",top : '35px',left : '220px',width : '100px',height : '50px'});
		
	var divLogin = ZA.createDiv(divData,"cmdButton","","div");
    $(divLogin).html('Login');
    $(divLogin).css({left:275,top:185});
    $(divLogin).click(function(){
		
		var divUserName=document.getElementById("loginusername");
		var sUserName=divUserName.value.trim();
		var divPassword=document.getElementById("loginpassword");
		var sPassword=divPassword.value.trim();
		if (sUserName.length <= 0) {
			$("#loginResponse").html("Please enter your email address");
		} else if (sPassword.length <= 0) {
			$("#loginResponse").html("Please enter your password");
		}else {
			ZA.aWindowLogin.clickLogin()
		}
    });
	
	var divCancel = ZA.createDiv(divData,"cmdButton","","div");
    $(divCancel).html('Cancel');
    $(divCancel).css({left:210,top:185});
    $(divCancel).click(function(){
      ZA.aWindowLogin.clickCloseA()
    });
	
	var fbarea=ZA.createDiv(divData,"fbarea","fbarea","div");
	$(fbarea).css({
	  top : '158px',
	  left : '21px',
	  width : '118px',
	  height : '28px'
	});
	//FBC.init($("#fbarea"));
	
	var twarea=ZA.createDiv(divData,"twarea","twarea","div");
  $(twarea).css({
    top : '186px',
    left : '21px',
    width : '118px',
    height : '28px'
  });
	//TC.init($("#twarea"));
	
};


/*********** click forgot password */
WORK_Login.prototype.clickForgotPassword=function(){
	return function() {
		var divData=document.getElementById("window_0");
		var divForget=ZA.createDiv(divData,"","divForget","div");
		$(divForget).css({ width:"350px",height:"220px",backgroundColor:"#3A3B3A" });
		
		var divFEmail=ZE.createInput(divForget,5,25,100,30,"Email","forgetEmail");
    divFEmail.focus();
		
		var divNote=ZA.createDiv(divForget,"","","div");
    $(divNote).css({ top:"60px",left:"8px"});
		
		$(divNote).append("Enter your email address and we'll send you a new password");
		
		var divSend = ZA.createDiv(divForget,"cmdButton","","div");
    $(divSend).html('Send');
    $(divSend).css({left:280,top:185});
    $(divSend).click(function(){
      ZA.aWindowLogin.sendNewPassword();
    });
  
  var divClose = ZA.createDiv(divForget,"cmdButton","","div");
    $(divClose).html('Close');
    $(divClose).css({left:220,top:185});
    $(divClose).click(function(){
      ZA.aWindowLogin.clickClosePassword()
    });
		
		var divResponse=ZA.createDiv(divForget,"","divReturnForget","div");
    $(divResponse).css({ top:"100px",left:"78px"});
		
		var divLoad=ZA.createDiv(divResponse,"","divForgetLoad","img");
		divLoad.src="_site/busy.gif";
		$(divLoad).css({ position:"absolute",top:"20px",left:"140px",display:"none"});
		
	};
};

WORK_Login.prototype.sendNewPassword=function(){
  var email = $("#forgetEmail").val();
  $("#divForgetLoad").css({ display:"block" });
  ZA.callAjax("_app/?forget="+email,function(xml){
    $("#divForgetLoad").css({ display:"none" });
    var textResponse = ZA.getXML(xml,"epicmessage");
    $("#divReturnForget").html(textResponse);
  });
};

WORK_Login.prototype.clickClosePassword=function(){
    $("#divForget").remove();
};
/*********** click login button */
WORK_Login.prototype.clickLogin=function(){
  $("#loginpassword").blur();
  $("#loginusername").blur();
  $("#loginResponse").html("");
	var divUserName=document.getElementById("loginusername");
	var sUserName=divUserName.value;
	ZA.sUserLogin = sUserName;
	var divPassword=document.getElementById("loginpassword");
	var sPassword=divPassword.value;
	ZA.sUserPassword = sPassword;
	ZA.callAjax("_app/?login=1&username="+sUserName+"&password="+sPassword+"&r="+ZA.iCheckBoxChecked,function(xml){
	  ZA.aWindowLogin.clickLoginReturn(xml);
	});
};


/*********** login return from server */
WORK_Login.prototype.clickLoginReturn=function(sXML){
	if (ZA.getXML(sXML,"action")=="success") {
		ZM.createTop();
		ZA.aWindowLogin.clickCloseA();
		ZA.refreshBrowser();
	} else {
		$("#loginResponse").html(ZA.getXML(sXML,"message"));
	}
};


WORK_Login.prototype.closeError=function(){
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


WORK_Login.prototype.closeFirstVisit=function(){
	ZA.refreshBrowser();
};


/** 
=============================================================================
	finish LOGIN CLASS */	
	WORK_Login._iInited=1;
	}
};//END function WORK_Login()


/** ========================================================================
REGISTER CLASS
*/
function WORK_Register(){
if (typeof WORK_Register._iInited=="undefined"){


/*********** close register window */
WORK_Register.prototype.clickClose=function(){
	return function() {
		ZA.aWindowRegister.clickCloseA();
	};
};


/*********** close register window action */
WORK_Register.prototype.clickCloseA=function(){
	var divBody=document.getElementsByTagName("body")[0];
	var divCloak=document.getElementById("bodycloak_0");
	var divRegister=document.getElementById("windowcontainer_0");
	var divData=document.getElementById("window_0");
	if (divRegister) {
		divBody.removeChild(divRegister);
		divBody.removeChild(divData);
	}
	if (divCloak) {
		divBody.removeChild(divCloak);
	}
};


/* create register window */
WORK_Register.prototype.create=function(){
	var divBody=document.getElementsByTagName("body")[0];
	var iDocHeight=document.documentElement.scrollHeight;
	ZA.createWindowPopup(0,"Register",450,290,1,0);
	var divData=document.getElementById("window_0");
	
	var iTop=5;
	var divMemo=ZA.createDiv(divData);
	divMemo.style.textAlign="left";
//	divMemo.style.background="red";
	divMemo.style.width="420px";
	divMemo.style.left="10px";
	divMemo.style.top=iTop+"px";
	divMemo.style.lineHeight="28px";
	var sHTML="I would like to use MyTCG.<br />";
	sHTML+="I want my username to be ";
	sHTML+="<input id='fbuid' "
    +"name='fbuid' type='hidden' /><span><input id='registerusername' size='20' "
		+"class='dotted' /></span>";
	sHTML+=" and I want my password to be ";
	sHTML+="<span><input id='registerpassword' type='password' size='20' "
		+"class='dotted' /></span>.";
	sHTML+=" Here I have retyped my password to verify that it is correct ";
	sHTML+="<span><input id='registerpassword2' type='password' size='20' "
		+"class='dotted' /></span>.<br />";
	sHTML+=" My email address is ";
	sHTML+="<span><input id='registeremailaddress' size='30' "
		+"class='dotted' /></span>";
	sHTML+=" and my name is ";
	sHTML+="<span><input id='registerfullname' size='30' "
		+"class='dotted' /></span>.";
	divMemo.innerHTML=sHTML;
	
	 $(divMemo).keydown(function (e){
    if(e.keyCode == 13){
        var keydown =eval("ZA.aWindowRegister.clickRegister()");
        keydown();
    }
  });
  
	iTop+=200;
	var divButton=ZE.createButton
	(divData,340,iTop,80,"Register","ZA.aWindowRegister.clickRegister()");
	var divButton=ZE.createButton
	(divData,250,iTop,80,"Cancel","ZA.aWindowRegister.clickClose()");

};


/***

		var sUsername=document.getElementById("signupusername").value;
		var sPassword=document.getElementById("signuppassword").value;
		var sPassword2=document.getElementById("signuppassword2").value;
		var sEmailAddress=document.getElementById("signupemailaddress").value;
		var sFullName=document.getElementById("signupfullname").value;
		var sError="";
		if (sUsername.length<5) {
			sError+="Username is too short.<br />";
		}
		if (sPassword.length<5) {
			sError+="Password is too short.<br />";
		}
		if (!ZA.getValidEmailAddress(sEmailAddress)) {
			sError+="Invalid Email Address.<br />";
		}
		if (sPassword!=sPassword2) {
			sError+="Passwords do not match.<br />";
		}
		if (sError) {
			sError="Please correct these problems before continuing.<br />"+sError;
			ZA.ZMsgBox=new WORK_MessageBox();
			ZA.ZMsgBox.sTitle="Errors Detected";
			ZA.ZMsgBox.sContent=sError;
			ZA.ZMsgBox.display();
		} else {
			ZA.createAjax
			(ZA.sURL+"_app/?signup=1&username="+sUsername
			+"&password="+sPassword
			+"&fullname="+sFullName+"&email="+sEmailAddress
			,"ZA.aSignUpWindow.signupReturn");
		}
	};

*/

/*********** click register button */
WORK_Register.prototype.clickRegister=function(){
	return function() {
		var sUsername=document.getElementById("registerusername").value;
		var sPassword=document.getElementById("registerpassword").value;
		var sPassword2=document.getElementById("registerpassword2").value;
		var sEmailAddress=document.getElementById("registeremailaddress").value;
		var sFullName=document.getElementById("registerfullname").value;
		var fbuid = document.getElementById("fbuid").value;
		var sError="";
		if (sUsername.length<5) {
			sError+="Username is too short.<br />";
		}
		if (sPassword.length<5) {
			sError+="Password is too short.<br />";
		}
		if (!ZA.getValidEmailAddress(sEmailAddress)) {
			sError+="Invalid Email Address.<br />";
		}
		if (sPassword!=sPassword2) {
			sError+="Passwords do not match.<br />";
		}
		if (sError) {
			sError="Please correct these problems before continuing.<br />"+sError;
			ZA.createWindowPopup(-1," Errors Detected",300,200,1,0);
			var divData=document.getElementById("window_-1");
			var divMemo=ZA.createDiv(divData);
			divMemo.style.textAlign="left";
			divMemo.innerHTML=sError;
			var divButton=ZE.createButton
				(divData, 200,120,70,"OK","ZA.aWindowRegister.closeError")
		} else {
			ZA.callAjax("_app/?register=1&username="+sUsername
				+"&password="+sPassword
				+"&fullname="+sFullName+"&email="+sEmailAddress+"&fbuid="+fbuid
				,function(xml){ ZA.aWindowRegister.clickRegisterReturn(xml); });
		}
	};
};


/*********** register return from server */
WORK_Register.prototype.clickRegisterReturn=function(sXML){
//	alert(sXML)
	if (ZA.getXML(sXML,"action")=="success") {
		//registration success, remove registration window
		var divBody=document.getElementsByTagName("body")[0];
		var divWin=document.getElementById("windowcontainer_0");
		var divData=document.getElementById("window_0");
		var divCloak=document.getElementById("bodycloak_0");
		if (divWin) {
			divBody.removeChild(divWin);
		}
		if (divData) {
			divBody.removeChild(divData);
		}
		if (divCloak) {
			divBody.removeChild(divCloak);
		}
		//show instructions popup
		ZA.createWindowPopup(-1," Successful Registration",300,200,1,0);
		var divData=document.getElementById("window_-1");
		var divMemo=ZA.createDiv(divData);
		divMemo.style.textAlign="left";
		divMemo.innerHTML=ZA.getXML(sXML,"message");
		var divButton=ZE.createButton
			(divData, 200,120,70,"OK","ZA.aWindowRegister.closeError")
		
	} else {
		//registration failed
		ZA.createWindowPopup(-1," Registration Failed",300,200,1,0);
		var divData=document.getElementById("window_-1");
		var divMemo=ZA.createDiv(divData);
		divMemo.style.textAlign="left";
		divMemo.innerHTML=ZA.getXML(sXML,"message");
		var divButton=ZE.createButton
			(divData, 200,120,70,"OK","ZA.aWindowRegister.closeError");		
	}
};


WORK_Register.prototype.closeError=function(){
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


/** 
=============================================================================
	finish REGISTER CLASS */	
	WORK_Register._iInited=1;
	}
};//END function WORK_Register()


/** ========================================================================
ELEMENT CLASS
*/
function WORK_Elements(){
if (typeof WORK_Elements._iInited=="undefined"){

//create a href tag
WORK_Elements.prototype.createATag=function
	(divParent,sID,iLeft,iTop,sValue,sAction){
	var divATag=ZA.createDiv(divParent,"atag",sID);
	divATag.style.left=iLeft+"px";
	divATag.style.top=iTop+"px";
	divATag.innerHTML="&nbsp;"+sValue+"&nbsp;";
	ZA.setSelectNone(divATag);
	divATag.onclick=eval(sAction);
	return divATag;
};
	
	
/*********** create button */
WORK_Elements.prototype.createButton=function
	(divParent,iLeft,iTop,iWidth,sDesc,sAction,sID){
	var divButton=ZA.createDiv(divParent,"button",sID);
	divButton.style.left=iLeft+"px";
	divButton.style.top=iTop+"px";
	ZA.setSelectNone(divButton);
	divButton.style.width=iWidth+"px";
	if (sAction){
		divButton.onclick=eval(sAction);
	}
	var divCenter=ZA.createDiv(divButton,"buttoncenter");
	divCenter.style.width=(iWidth-20)+"px";
	var divLeft=ZA.createDiv(divButton,"buttonleft");
	var divRight=ZA.createDiv(divButton,"buttonright");
	var divDesc=ZA.createDiv(divButton,"buttondesc");
	divDesc.innerHTML=sDesc;
	return divButton;
};


//create input box
WORK_Elements.prototype.createInput=function
	(divParent,iLeft,iTop,iWidth,iSize,sDesc,sID){
	var divInput=ZA.createDiv(divParent,"inputdiv");
	divInput.style.left=iLeft+"px";
	divInput.style.top=iTop+"px";
	divInput.style.width=iWidth+"px";
	var divDesc=ZA.createDiv(divInput,"inputdesc");
	divDesc.innerHTML=sDesc;
	var divBox=ZA.createDiv(divInput,"inputbox");
	var divInputBox=ZA.createDiv(divBox,"text",sID,"input");
	divInputBox.size=iSize;
	return divInputBox;
};


//create selectbox
WORK_Elements.prototype.createSelect=function
	(divParent,sID,iLeft,iTop,sHeading,aOptions,sAction){
	var divBox=ZA.createDiv(divParent);
	divBox.style.left=iLeft+"px";
	divBox.style.top=iTop+"px";
	var divHeading=ZA.createDiv(divBox);
	var aHeading=sHeading.split("`");
	if (aHeading[1]){
		divHeading.style.left="0"+"px";
		divHeading.style.top="-15"+"px";
		divHeading.innerHTML=aHeading[0];
	} else {
		divHeading.style.left="-40"+"px";
		divHeading.style.top="0"+"px";
	}
	divHeading.innerHTML="&nbsp;"+aHeading[0];
	var divSelect=ZA.createDiv(divBox,"selectbox",sID,"select");
	divSelect.onchange=eval(sAction);
	var iCount=0;
	var sOption="";
	while (sOption=aOptions[iCount]){
		if ((!iCount)&&(aHeading[2])) {
			divSelect.style.width=aHeading[2]+"px";
		}
		var divOption=ZA.createDiv(divSelect,"","","option");
		divOption.text=sOption;
		divOption.value=(iCount+1);
		iCount++;
	}
	return divSelect;
};


/** 
=============================================================================
	finish ELEMENTS CLASS */	
	WORK_Elements._iInited=1;
	}
};//END function WORK_Elements()



/** ========================================================================
				MENU CLASS
*/
function WORK_Menu(){
  this.sXML = "";
  this.divScrollWindow = null;
  this.divScrollTrack = null;
  
	if (typeof WORK_Menu._iInited=="undefined"){
	
	
/*********** handle menu actions */
WORK_Menu.prototype.action=function(sAction){
	return function(){
		var aAction=sAction.split("_");
		switch (aAction[0]){
			case "left":
				alert(aAction[0]+" "+aAction[1]);
			break;
			case "top":
				// logged out
				if (!ZA.sUsername) {
					switch (aAction[1]) {
						case "0":
							ZA.aWindowLogin=new WORK_Login();
							ZA.aWindowLogin.create();
						break;
						case "1":
		        			ZA.maximizeWindowA(5);
		        		break;
		        		case "2":
		        			ZA.maximizeWindowA(2);
		        		break;
		        		case "3":
		        			ZA.maximizeWindowA(4);
		        		break;
						
					}
				// logged in
				} else {
					switch (aAction[1]) {
						case "0":
							ZA.callAjax("_app/?logout=1",function(xml){ ZA.refreshBrowser(); });
						break;
						case "1":
							ZA.callAjax("_app/userprofile/?init=1",function(xml){ ZUP.init(xml); });
						break;
						case "2":
							ZA.maximizeWindowA(4);
						break;				
						case "3":
							ZA.maximizeWindowA(5);
						break;		
						case "4":
	            			ZA.maximizeWindowA(2);
						break;
						case "5":
							ZA.showLeaderboard();
						break;
						case "6":
                     $("#component_10").show();
                     ZA.maximizeWindowA(10);
						break;
						case "7":
							ZA.showNotifications();
						break;
					}
				}
			break;// case top
			case "bottom":
				alert(aAction[0]+" "+aAction[1]);
			break;
			case "social":
				alert(aAction[0]+" "+aAction[1]);
			break;
		}
	};
};

//LEFT MENU FILTER COMMANDS
WORK_Menu.prototype.filterWindows=function(iCategory){
  return function(){
    console.log(iCategory);
    //Shop
    var divWindow = document.getElementById("window_"+ZS.iComponentNo);
    $(divWindow).html("");
    var xml = ZS.sXML;
    ZA.xmlFilter(xml);
    
    
    if (!ZA.sUsername) { }

  }
};

/*********** create bottom menu */
WORK_Menu.prototype.createBottom=function(){
	var divFooter=document.getElementById("bodyfooter");
	var divMenu=ZA.createDiv(divFooter,"menubottom");
	var aMenus=ZA.sMenuItemsBottom.split("|");
	var iCount=0;
	var sDesc="";
	var iLeft=0;
	while (sDesc=aMenus[iCount]){
		var iLength=sDesc.length;
		var divItem=ZA.createDiv(divMenu,"menubottomitem");
		divItem.onclick=ZM.action("bottom_"+iCount);
		$(divItem).css({left:iLeft+"px"});
		iLeft+=(iLength*8)+10;
		divItem.innerHTML=sDesc;
		iCount++;
	}
};


/*********** create social icons */
WORK_Menu.prototype.createSocial=function(){
  var iCount = 0;
	var divComponent=document.getElementById("component_6");
	var divSocial=ZA.createDiv(divComponent,"menusocial");
	
	var hrefHolder = ZA.createDiv(divSocial,"","","a");
  hrefHolder.href="http://twitter.com/#!/mytcg";
  hrefHolder.target="_blank"
  var divIcon=ZA.createDiv(hrefHolder,"menusocialicon");
  $(divIcon).css({backgroundPosition:"-"+(91+40*iCount)+"px -1px",left:(iCount*40+5)+"px"});
	
	iCount++;
	
  var hrefHolder = ZA.createDiv(divSocial,"","","a");
  hrefHolder.href="http://www.facebook.com/pages/Mobile-Game-Card-Applications/137172149641477";
  hrefHolder.target="_blank"
  var divIcon=ZA.createDiv(hrefHolder,"menusocialicon");
  $(divIcon).css({backgroundPosition:"-"+(91+40*iCount)+"px -1px",left:(iCount*40+5)+"px"});
};


/*********** create top menu */
WORK_Menu.prototype.createTop=function(){
	var divHeader=document.getElementById("bodyheader");
	
	var divMenu=document.getElementById("menutop");	
	if (divMenu) {
		divHeader.removeChild(divMenu);
	}
	// logged out
	if (!ZA.sUsername) {
		ZA.sMenuItemsTop = "Login|Auction|Shop";
	}
	// logged in
	else{
		//ZA.sMenuItemsTop = "Logout|Profile|My Deck|Album|Auction|Buy Cards|Leaderboard|Credits|Notifications";
		ZA.sMenuItemsTop = "Logout|Profile|Album|Auction|Shop|Leaderboard|Credits|Notifications";
	}
	
	var divMenu=ZA.createDiv(divHeader,"menutop","menutop");
	var divLogo=ZA.createDiv(divMenu,"logo");
	var divLeft=ZA.createDiv(divMenu,"","left_banner");
	var divLeftInfo=ZA.createDiv(divLeft,"left_banner_info","");
	// var homeurl = ZA.createDiv(divLeft,"homeurl");
	// $(homeurl).html("home");
	if (!ZA.sUsername) {
		//not logged in
		$(divLeftInfo).html("<span class='txtGrey' style='top:33px;left:-40px;'>to SA Rugby Cards</span>");
	}
	var aMenus=[];
	aMenus=ZA.sMenuItemsTop.split("|");
	var iCount=0;
	var sDesc="";
	var iRight=0;
	var side = ZA.createDiv(divMenu,"menu_right");
	while (sDesc=aMenus[iCount]){
		if(!ZA.sUsername){
			if(iCount!=2){
				var iLength=sDesc.length;
				var divItem=ZA.createDiv(divMenu,"menutopitem");
				divItem.onclick=ZM.action("top_"+iCount);
				divItem.innerHTML=sDesc;
				var split = ZA.createDiv(divMenu,"menu_split");
			}else{
				var iLength=sDesc.length;
				var divItem=ZA.createDiv(divMenu,"menutopitem");
				divItem.onclick=ZM.action("top_"+iCount);
				divItem.innerHTML=sDesc;
			}
		}else{
			if(iCount!=7){
				var iLength=sDesc.length;
				var divItem=ZA.createDiv(divMenu,"menutopitem");
				divItem.onclick=ZM.action("top_"+iCount);
				divItem.innerHTML=sDesc;
				var split = ZA.createDiv(divMenu,"menu_split");
			}else{
				var iLength=sDesc.length;
				var divItem=ZA.createDiv(divMenu,"menutopitem");
				divItem.onclick=ZM.action("top_"+iCount);
				divItem.innerHTML=sDesc;
			}
		}
		iCount++;
	}
	var side = ZA.createDiv(divMenu,"menu_left");
};

WORK_Menu.prototype.showMenuLeftScrollbar=function(){
  var divScroll = ZM.divScrollWindow;
  var iHeight = parseInt(divScroll.offsetHeight);  
  var divScrolltrack = ZM.divScrollTrack;
  
  var iComponentHeight=parseInt(ZA.aComponents[2].aPos[3])-26;
  var overAmount=iHeight-iComponentHeight;
  var iSizeH=Math.round((iComponentHeight/iHeight)*iComponentHeight);
  var sRatio=overAmount/(iComponentHeight-iSizeH);
  var divBar=document.getElementById("menuleftscrollbar");
  var divWin=document.getElementById("menuleftscrollwindow");
  if (iComponentHeight<iHeight){
    if(!divBar) {
      divBar=ZA.createDiv(divScrolltrack,"menuleftbar","menuleftscrollbar");
      divBar.style.top = "0px";
    }
    $(divBar).draggable("destroy");
    $(divBar).draggable({containment:"parent",drag: function() {
        var iTmp=parseInt(this.style.top)*sRatio;
        divWin.style.top=-iTmp+"px";
      }
    });
    
    $('.menuleftmain').mousewheel(function(event, delta){
      //Scroll 5px per wheel and negative to counter the style.top thing
      delta *= -5;
      var newpos = parseInt(divBar.style.top) + delta;
      if (newpos <= 0){
        divBar.style.top = "0px";
      }else if(newpos >= (248 - parseInt(divBar.style.height))){
        divBar.style.top = (248 - parseInt(divBar.style.height))+"px";
      }else{
        divBar.style.top = newpos + "px";
      }
      var iTmp=parseInt(divBar.style.top)*sRatio;
      divWin.style.top=-iTmp+"px";
      return false;
    });
    divBar.style.height=iSizeH+"px";
  }
  else {
    if(divBar){
      $('.menuleftmain').unbind();
      divWin.style.top = "0px";
      $(divBar).remove(); 
    }
  }
};

/*********** createleft menu */
WORK_Menu.prototype.createLeft=function(sXML){
  ZM.sXML = sXML;
	var divComponent=document.getElementById("component_1");
	var iWidth=parseInt(divComponent.style.width);
	var iHeight=parseInt(divComponent.style.height);
	var divMenu=ZA.createDiv(divComponent,"menuleft");
	var divMenuTop=ZA.createDiv(divMenu,"menulefttop");
	divMenuTop.style.width=(iWidth-16)+"px";
	var divLeft=ZA.createDiv(divMenuTop,"menulefttopleft");
	var divRight=ZA.createDiv(divMenuTop,"menulefttopright");
	var divMenuBottom=ZA.createDiv(divMenu,"menuleftbottom");
	divMenuBottom.style.width=(iWidth-16)+"px";
	var divLeft=ZA.createDiv(divMenuBottom,"menuleftbottomleft");
	var divRight=ZA.createDiv(divMenuBottom,"menuleftbottomright");
	$(divMenu).css({opacity:0.9,height:(iHeight-16)+"px",width:(iWidth-2)+"px"});
	
	var iScrollHeight=parseInt(divComponent.style.height)-16;
	var divAllMenu=ZA.createDiv(divMenu,"menuleftmain","");
  	$(divAllMenu).css({height:iScrollHeight+"px"});
	var divHeightMeasure=ZA.createDiv(divAllMenu,"menuleftscrollwindow","menuleftscrollwindow");
	ZM.divScrollWindow = divHeightMeasure;
  	var divScroll=ZA.createDiv(divAllMenu,"menuleftscroll","");
  	$(divScroll).css({height:(iScrollHeight-9)+"px"});
  	ZM.divScrollTrack = divScroll;
  
  //SHOW ALL MENU
  var divListHolder=ZA.createDiv(divHeightMeasure,"menuleftholder","");
  $(divListHolder).attr("parent","main");
  var divList=ZA.createDiv(divListHolder,"menuleftlist","");
  divList.innerHTML="Show All";
  ZA.setSelectNone(divList);
  $(divList).css({width:"100%"});
  $(divList).click(function(event) {
    $(".menuleftlist").css({backgroundColor:""});
    $(this).css({backgroundColor:"#000"});
    ZM.hardcoreFilter(0,0);
  });
  
	var iCount=parseInt(ZA.getXML(ZM.sXML,"categories/iCount"));
	for(i=0;i<iCount;i++){
	  var catID = ZA.getXML(ZM.sXML,"categories/category_"+i+"/category_id");
	  var parentID = ZA.getXML(ZM.sXML,"categories/category_"+i+"/parent_id");
	  var sLevel = ZA.getXML(ZM.sXML,"categories/category_"+i+"/level");
	  
	  if (ZA.getXML(sXML,"categories/category_"+i+"/parent_id")=="main"){
	    //Build main category list
      var divListHolder=ZA.createDiv(divHeightMeasure,"menuleftholder","");
      $(divListHolder).attr("parent","main");
      var divList=ZA.createDiv(divListHolder,"menuleftlist","");
      divList.innerHTML=ZA.getXML(ZM.sXML,"categories/category_"+i+"/description");
      ZA.setSelectNone(divList);
      $(divList).click({c:catID,l:sLevel}, function(event) {
        $(".menuleftlist").css({backgroundColor:""});
        $(this).css({backgroundColor:"#58B1FD"});
        ZM.hardcoreFilter(event.data.c,event.data.l);
      });
      
      //Create dropdown button if menu has children.
      var iChildCount = ZM.hasChildMenu(catID);
	   if(iChildCount > 0){
  	   var divDownIconBox=ZA.createDiv(divListHolder,"menulefticonholder","");
  	   var divDownIcon=ZA.createDiv(divDownIconBox,"menulefticon","");
  	   $(divDownIconBox).click({cid:catID,div:divListHolder}, function(event) {
  	     if($(this.childNodes[0]).css("backgroundPosition")=="-291px -61px"){
  	       $(this.childNodes[0]).css({backgroundPosition:"-281px -61px"});
           ZM.createDropDownList(event.data.cid,event.data.div);
           ZM.showMenuLeftScrollbar();
  	     }else{
  	       $(this.childNodes[0]).css({backgroundPosition:"-291px -61px"});
  	       ZM.RemoveDropDownList(event.data.cid,"main");
  	       ZM.showMenuLeftScrollbar();
  	     }
        });
	    }
	  }
	}
	
};

WORK_Menu.prototype.hardcoreFilter=function(categoryID,updateLevel){
  ZA.categoryID = categoryID;
  ZA.updateLevel = updateLevel;
  ZA.addLoader($("#window_"+ZU.iComponentNo),200);
  if(categoryID > 0){
   if(updateLevel < 3){
       ZA.callAjax("_app/shop/?cat="+categoryID+"&l="+updateLevel,function(xml){ ZS.init(xml); },2);
   }
   ZA.callAjax("_app/auction/?cat="+categoryID+"&l="+updateLevel,function(xml){ZU.init(xml);});
   
   if (ZA.sUsername) {
     ZA.callAjax("_app/album/?cat="+categoryID+"&l="+updateLevel,function(xml){ZL.init(xml);});
     // if(updateLevel < 3){
      // ZA.callAjax("_app/deck/?cat="+categoryID+"&l="+updateLevel,function(xml){ ZD.init(xml); });
     // }
   }
  }else{
    if (ZA.sUsername) {
      ZA.callAjax("_app/album/?init=1",function(xml){ZL.init(xml);});
      // ZA.callAjax("_app/deck/?init=1",function(xml){ ZD.init(xml); });
    }
    ZA.callAjax("_app/shop/?init=1",function(xml){ ZS.init(xml); },2);
    ZA.callAjax("_app/auction/?init=1",function(xml){ZU.init(xml);});
  }
};


WORK_Menu.prototype.RemoveDropDownList=function(categoryID,parentID){
  var remove = false;
  $('.menuleftholder').each(function(){
    var thisParent = $(this).attr("parent");
    if(thisParent == categoryID){
      remove=true;
    }
    if((remove)&&((thisParent == parentID)||(thisParent=="main"))){
      remove=false;
    }
    if(remove){
      $(this).remove();
    }
  });
};

WORK_Menu.prototype.createDropDownList=function(cID,divMarker){
  var iCount = ZA.getXML(ZM.sXML,"categories/iCount");
  for(ass=iCount;ass>=0;ass--){
    var catID = ZA.getXML(ZM.sXML,"categories/category_"+ass+"/category_id");
    var parentID = ZA.getXML(ZM.sXML,"categories/category_"+ass+"/parent_id");
    var level = ZA.getXML(ZM.sXML,"categories/category_"+ass+"/level");
    if(parentID == cID){
      $(divMarker).after('<div class="menuleftholder" parent='+parentID+'></div>');
      var divSpot = $(divMarker).next().get(0);
      var divList=ZA.createDiv(divSpot,"menuleftlist","");
      $(divList).click({c:catID,l:level}, function(event) {
        $(".menuleftlist").css({backgroundColor:""});
        $(this).css({backgroundColor:"#58B1FD"});
        ZM.hardcoreFilter(event.data.c,event.data.l);
      });
      divList.innerHTML=ZA.getXML(ZM.sXML,"categories/category_"+ass+"/description");
      
      var childCount = ZM.hasChildMenu(catID);
      if(childCount > 0){
       var divDownIconBox=ZA.createDiv(divSpot,"menulefticonholder","");
       var divDownIcon=ZA.createDiv(divDownIconBox,"menulefticon","");
       $(divDownIconBox).click({cid:catID,div:divSpot,pid:parentID}, function(event) {
          if($(this.childNodes[0]).css("backgroundPosition")=="-291px -61px"){
           $(this.childNodes[0]).css({backgroundPosition:"-281px -61px"});
           ZM.createDropDownList(event.data.cid,event.data.div);
           ZM.showMenuLeftScrollbar();
         }else{
           $(this.childNodes[0]).css({backgroundPosition:"-291px -61px"});
           ZM.RemoveDropDownList(event.data.cid,event.data.pid);
           ZM.showMenuLeftScrollbar();
         }
       });
      }else{
        $(divList).css({width:"100%"});
      }
    }
  }
};

WORK_Menu.prototype.hasChildMenu=function(categoryID){
  var iChildCount = 0;
  var iCountCat=ZA.getXML(ZM.sXML,"categories/iCount");
  for(iCat=0;iCat<iCountCat;iCat++){
    if (ZA.getXML(ZM.sXML,"categories/category_"+iCat+"/parent_id")==categoryID){
      iChildCount++;
    }
  }
  return iChildCount;
};


/** 
=============================================================================
					finish MENU CLASS */	
		WORK_Menu._iInited=1;
	}
};//END function WORK_Menu()


/** ========================================================================
// ACTIVATION CLASS 
//
// Class to display a message box contain the results of the user's activation. (Successfull or Fail)
*/
function WORK_User_Activated(){

if (typeof WORK_User_Activated._iInited=="undefined"){


/*********** close ACTIVATION window */
WORK_User_Activated.prototype.clickClose=function(){
  return function() {
    ZA.aWindowUserActivated.clickCloseA();
  };
};


/*********** close ACTIVATION window action */
WORK_User_Activated.prototype.clickCloseA=function(){
  var divBody=document.getElementsByTagName("body")[0];
  var divCloak=document.getElementById("bodycloak_0");
  var divRegister=document.getElementById("windowcontainer_0");
  var divData=document.getElementById("window_0");
  if (divRegister) {
    divBody.removeChild(divRegister);
    divBody.removeChild(divData);
  }
  if (divCloak) {
    divBody.removeChild(divCloak);
  }
};


/*********** create activation window */
WORK_User_Activated.prototype.create=function(message){
  var divBody=document.getElementsByTagName("body")[0];
  var iDocHeight=document.documentElement.scrollHeight;
  ZA.createWindowPopup(0,"Register",300,150,1,0);
  var divData=document.getElementById("window_0");
  
  var iTop=5;
  var divMemo=ZA.createDiv(divData);
  divMemo.style.textAlign="left";
  divMemo.style.width="250px";
  divMemo.style.left="10px";
  divMemo.style.top=iTop+"px";
  divMemo.style.lineHeight="28px";
  var sHTML=message;
  divMemo.innerHTML=sHTML;
  iTop+=70;
  var divButton=ZE.createButton
  (divData,200,iTop,80,"Continue","ZA.aWindowUserActivated.clickClose()");

};



WORK_User_Activated.prototype.closeError=function(){
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


/** 
=============================================================================
  finish ACTIVATION CLASS */  
  WORK_User_Activated._iInited=1;
  }
};//END function WORK_User_Activated()


String.prototype.capitalize = function(){
	return this.charAt(0).toUpperCase() + this.slice(1);
}


var ZA=new WORK_App();
var ZM=new WORK_Menu();
var ZE=new WORK_Elements();

$(document).ready (function(){
  $.post("process_auctions.php",function(){
    ZA.createBody();
  });
});
