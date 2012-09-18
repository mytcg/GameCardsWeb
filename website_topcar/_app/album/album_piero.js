function WORK_Album(){
	this.iComponentNo=0;
	this.divData=0;
	this.sURL="_app/album/";
	this.sXML="";
  this.iWindowWidth = 484;  //Hardcoded value
  this.iWindowHeight = 254; //Hardcoded value
  this.iMaxWidth = 984;  //Hardcoded value
  this.iMaxHeight = 535; //Hardcoded value
  this.iWidthOfList = 0;
	this.albumCount = 0;
  this.pageCount = 0;
  this.perPage = 6;         //Hardcoded value
  this.currentPage = 0;
	this.divList = null;
	this.divListBlock = null;
	this.divMenu = null;
  this.divListLarge = null;
  this.divScroll = null;
  this.divScrollBar = null;
	
	if (typeof WORK_Album._iInited=="undefined"){

    WORK_Album.prototype.init=function(sXML){
      //Get INIT values and properties
      ZL.divData=document.getElementById("window_"+ZL.iComponentNo);
      ZL.sXML=sXML;
      
      //Calc pages
      ZL.albumCount = ZA.getXML(ZL.sXML,"albumcount");
      ZL.pageCount = Math.ceil(ZL.albumCount / ZL.perPage);
      ZL.iWidthOfList = ZL.pageCount*(ZL.iWindowWidth-120);

      //Draw divs for page
      ZL.divList = ZA.createDiv(ZL.divData,"","","div");
      $(ZL.divList).css({ top:0,left:0,width:ZL.iWidthOfList,height:ZL.iWindowHeight-25 });      
      
      ZL.divMenu = ZA.createDiv(ZL.divData,"","","div");
      $(ZL.divMenu).css({ top:0,right:0,width:120,height:ZL.iWindowHeight-25,backgroundColor:"#C00" });
      
      ZL.divListLarge = ZA.createDiv(ZL.divData,"","","div");
      $(ZL.divListLarge).css({ display:"none",opacity:0,position:"relative",width:ZL.iMaxWidth,height:ZL.iMaxHeight,backgroundColor:"#EBEBEB" });
      
      ZL.divScroll = ZA.createDiv(ZL.divData,"","divScroll","div");
      $(ZL.divScroll).css({ bottom:0,left:0,width:ZL.iWindowWidth,height:20 });
      
      //ZL.buildMenu();
      ZL.buildScroller();
    };









    WORK_Album.prototype.buildScroller=function(){
      
      var divArrowLeft = ZA.createDiv(ZL.divScroll,"","","div");
      $(divArrowLeft).css({ top:0,left:0,cursor:"pointer",width:20,height:20,backgroundImage:ZS.imgAll,backgroundPosition:"-221px -41px" });
      $(divArrowLeft).click(function(e){
        if(ZL.currentPage != 0){
          ZL.currentPage--;
          ZL.gotoPage(ZL.currentPage);
        }
      });
      
      var divPageCountList = ZA.createDiv(ZL.divScroll,"","","div");
      var offsetCount = (ZL.iWindowWidth-40-(ZL.pageCount*14))/2;
      var iLeft = offsetCount-14;
      $(divPageCountList).css({ top:0,left:20,width:ZL.iWindowWidth-40,height:20});

      for(i=0;i<ZL.pageCount;i++){
        var divPageIcon = ZA.createDiv(divPageCountList,"","","div");
        iLeft += 14;
        $(divPageIcon).css({ top:7,left:iLeft,cursor:"pointer",width:7,height:7,backgroundImage:ZS.imgAll,backgroundPosition:"-281px -41px"});
        divPageIcon.onclick = (function() {
            var current_i = i;
            return function() {
                ZL.currentPage=current_i;
                ZL.gotoPage(current_i);
            }
         })();
      }
      
      ZL.gotoPage(0);
      var divArrowRight = ZA.createDiv(ZL.divScroll,"","","div");
      $(divArrowRight).css({ top:0,right:0,cursor:"pointer",width:20,height:20,backgroundImage:ZS.imgAll,backgroundPosition:"-251px -41px" });
      $(divArrowRight).click(function(e){
        if(ZL.currentPage != ZL.pageCount-1){
          ZL.currentPage++;
          ZL.gotoPage(ZL.currentPage);
        }
      });
    };
    
    WORK_Album.prototype.gotoPage=function(page){
      for (i=0;i<ZL.pageCount;i++){
        $(ZL.divScroll.childNodes[1].childNodes[i]).css({ backgroundPosition:"-281px -41px" }); 
      }
      $(ZL.divScroll.childNodes[1].childNodes[page]).css({ backgroundPosition:"-291px -41px" });
      var newPos = page*-ZL.iWidthOfList;
      $(ZL.divList).animate({left:newPos},600);
    };




		}
	WORK_Album._iInited=1;
};



var ZL = new WORK_Album();
ZL.iComponentNo=4;
ZA.aComponents[ZL.iComponentNo].fMaximizeFunction=ZL.maximize;
ZA.callAjax(ZL.sURL+"?init=1",function(xml){ZL.init(xml);});
