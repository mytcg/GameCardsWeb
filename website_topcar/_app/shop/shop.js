function WORK_Shop(){
	this.iComponentNo=2;
	this.divData=0;
	this.sXML = "";
	this.productCount = 0;
	this.pageCount = 0;
	this.perPage = 3;
	this.currentPage = 0;
	this.divList = null;
	this.divListLarge = null;
	this.divProductList = null;
	this.divScroll = null;
	this.divScrollBar = null;
	this.imgAll = "url(_site/all.png)";
	
	if (typeof WORK_Shop._iInited=="undefined"){

    WORK_Shop.prototype.init=function(sXML){
    	ZS.divData=document.getElementById("window_"+ZS.iComponentNo);
    	$(ZS.divData).html("");
    	ZS.sXML = sXML;
    	
    	//DRAW DIVS FOR LIST AND SCROLLER
    	ZS.productCount = ZA.getXML(ZS.sXML,"iCount");
    	ZS.pageCount = Math.ceil(ZS.productCount / ZS.perPage);
    	var iWidthOfList = ZS.pageCount*484;
    	
    	ZS.divList = ZA.createDiv(ZS.divData,"","divList","div");
    	$(ZS.divList).css({ top:0,left:0,width:iWidthOfList,height:165});
    	
    	ZS.divScroll = ZA.createDiv(ZS.divData,"","divScroll","div");
      $(ZS.divScroll).css({ bottom:3,left:0,width:484,height:20 });
      
      ZS.buildList();
      ZS.buildScroller();
    };

    WORK_Shop.prototype.showWindow=function(icon,message,delay){
      if (typeof delay == "undefined"){
      	delay = 1500;
      }
      var divBody=document.getElementsByTagName("body")[0];
      var divWin = ZA.createDiv(divBody,"","","div");
      $(divWin).css({ border:"1px solid #FFF",opacity:0.7,color:"#FFF",
                      paddingTop:10,paddingLeft:35,top:-38,left:(window.innerWidth/2)-300,width:565,
                      height:20,backgroundColor:"#000",zIndex:200,fontSize:11,fontWeight:"bold",textAlign:"left"
                    });
      $(divWin).html(message);
      var divCheckIcon = ZA.createDiv(divWin,"","","div");
      $(divCheckIcon).css({ top:5,left:5,width:23,height:21,backgroundImage:ZS.imgAll,backgroundPosition:icon});

      $(divWin).animate({ top:-2 },600).delay(delay).animate({ top:-38 },600,function(){
        $(divWin).remove();
      });
    };
    
    WORK_Shop.prototype.buildListLarge=function(){
      var sXML = ZS.sXML;
      var iProducts = ZA.getXML(ZS.sXML,"iCount");
      var perRow = 4;
      var iRows = Math.ceil(iProducts / perRow);
      var itemCount = 0;
      var itemRowCount = 0;
      var iLeft = 7;
      var iTop = 7;
      
    	ZS.divListLarge = ZA.createDiv(ZS.divData,"","divListLarge","div");
      $(ZS.divListLarge).css({ display:"none",opacity:0,position:"relative",width:744,height:720 });
    	
    	var productList = ZA.createDiv(ZS.divListLarge,"","productsholder","div");
    	$(productList).css({
    		width:740,
    		height:720,
    		"padding-bottom":8
    	});
    	ZS.divProductList = productList;
    	
      while(itemCount < iProducts){
        if (itemRowCount == perRow) {
          iTop += 135;
          iLeft = 7;
          itemRowCount = 0;
        }
        var itemBlock = ZA.createDiv(productList,"productBlock","","div");
        //$(itemBlock).css({ top:iTop,left:iLeft,width:230,height:115});
        $(itemBlock).css({ backgroundColor:"#EFEFEF","position":"relative","float":"left",width:235,height:135,"margin-left":8,"margin-top":8,"border":"1px solid #ccc"});

        var divLeft = ZA.createDiv(itemBlock,"",itemCount.toString(),"img");
        $(divLeft).css({ top:10,left:10,width:82,height:115,position:"absolute",cursor:"pointer" });
        divLeft.src = ZA.getXML(ZS.sXML,"pack_"+itemCount+"/fullimageserver")+"products/"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/img")+".jpg";
        $(divLeft).attr('title','View potential cards')
        .click(function(){
        	var index = $(this).attr('id');
        	ZA.addLoader($(this).parent(),101,"EFEFEF");
        	ZA.callAjax("_app/shop/?getcards=1&pack="+ZA.getXML(ZS.sXML,"pack_"+index+"/id"),function(xml){
        		ZA.removeLoader(101);
        		ZS.viewPackCards(xml);
        	});
        });

        var divTop = ZA.createDiv(itemBlock,"","","div");
        $(divTop).css({ lineHeight:1.5,textAlign:"left",paddingLeft:6,top:10,left:92,width:127,height:58});
        $(divTop).html("<b><span class='txtGreen' style='font-size:12px;'>"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/desc")+"</span><br />"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/cards")+" Cards in pack<br /><span class=\"txtBlue\" style=\"font-size:16px;\">"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/price")+" TCG</span></b>");
        
        var divBuy = ZA.createDiv(itemBlock,"cmdButton","","div");
        $(divBuy).attr('alt',itemCount.toString());
        $(divBuy).html('Buy Now');
        $(divBuy).css({top:90,left:98});
        divBuy.onclick = (function() {
          var current_i = ZA.getXML(ZS.sXML,"pack_"+itemCount+"/id");
          return function() { 
              ZS.buyItem(current_i,$(this).attr('alt'));
          }
        })();
        
        iLeft += 237;
        itemCount++;
        itemRowCount++;
      }
      //$(ZS.divListLarge).css({height:iTop+135});

    };
    
    
    WORK_Shop.prototype.viewPackCards=function(xml)
    {
    	ZA.createWindowPopup(555,"",732,530,1,0);
        var divWindow=document.getElementById("window_555");
        var divData=ZA.createDiv(divWindow,"cardsInShop");
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
        $(title).html(ZA.getXML(xml,"desc"));
        
        //pack price
        var price = ZA.createDiv(divData);
        $(price).css({
        	textAlign:"right",
        	right:15,
        	top:18,
        	fontSize:16,
        	fontWeight:"bold",
        	color:"#58B1FD"
        });
        $(price).html(ZA.getXML(xml,"price")+' TCG');
        
        //memo
        var memo = ZA.createDiv(divData);
        $(memo).css({
        	left:15,
        	top:50,
        	width:240,
        	textAlign:"left"
        });
        $(memo).html('A '+ZA.getXML(xml,"desc")+' pack contains '+ZA.getXML(xml,"size")+' random cards from this collection.');
        
        //pack image
        var image = ZA.createDiv(divData);
        $(image).css({
        	left:15,
        	top:90
        });
        $(image).html('<img src="'+ZA.getXML(xml,"path")+'products/'+ZA.getXML(xml,"image")+'.jpg" />');
        
        //number of cards
        var number = ZA.createDiv(divData);
        $(number).css({
        	left:15,
        	width:250,
        	bottom:20,
        	fontSize:16,
        	fontWeight:"bold",
        	color:"#666"
        });
        $(number).html(ZA.getXML(xml,"size")+' cards');
        
        //cards
        var divCardsHolder = ZA.createDiv(divData,"","packCards");
        $(divCardsHolder).css({
        	left:280,
        	top:40,
        	width:420,
        	height:400,
        	paddingBottom:10,
        	border:"2px solid #EFEFEF",
        	background:"url(_site/line.gif) repeat",
        	"-moz-border-radius":"5px"
        });
    	var divCards = ZA.createDiv(divCardsHolder);
    	$(divCards).css({
    		position:"relative",
    		width:"100%"
    	});
    	var divCard;
    	var count = parseInt(ZA.getXML(xml,"count"));
    	for(var i=0; i<count; i++){
    		divCard = ZA.createDiv(divCards);
    		$(divCard).css({
    			position:"relative",
    			"float":"left",
    			marginLeft:15,
    			marginTop:15,
    			width:64,
    			height:115
    		});
    		var card_id = ZA.getXML(xml,"cards/card_"+i+"/card_id");
    		var description = ZA.getXML(xml,"cards/card_"+i+"/description");
    		var thumb = ZA.getXML(xml,"cards/card_"+i+"/path")+'cards/'+ZA.getXML(xml,"cards/card_"+i+"/image")+'_web.jpg';
    		$(divCard).html(
    			'<img src="'+thumb+'" style="border-right:1px solid #000;border-bottom:1px solid #000;" />'+
    			'<div style="width:100%;height:24px;overflow:hidden;color:#000;padding-top:1px;">'+description+'</div>'
    		);
    		var possess = parseInt(ZA.getXML(xml,"cards/card_"+i+"/possess"));
    		if(possess > 0){
    			var own = ZA.createDiv(divCard);
    			$(own).attr('title','You already own this card').css({
    				background:"url(_site/all.png) -105px -40px no-repeat",
					color:"#CC0000",
					"font-weight":"bold",
    				width:21,
					height:17,
    				top:5,
    				right:3
    			});
    			if(possess > 0){
    				$(own).html(possess.toString());
    			}
    		}
    	}
    	//clear cards float
    	var clear = ZA.createDiv(divCards);
    	$(clear).css({"clear":"left"});
    	//add scrollbar
    	$(divCardsHolder).jScrollPane({enableKeyboardNavigation:false});
        
        //close window button
        var close = ZA.createDiv(divData,"cmdButton");
        $(close).css({
        	right:14,
        	bottom:10
        });
        $(close).html('Close');
        $(close).click(function(){
			$("#bodycloak_555").remove();
			$("#windowcontainer_555").remove();
			$("#window_555").remove();
        });
    };
    
    WORK_Shop.prototype.activateProductsScrollbar=function(){
        $(ZS.divProductList).jScrollPane();
		$(ZS.divProductList).find(".jspContainer").css({
		'overflow':'hidden',
			width:745
		});
		$(ZS.divProductList).find(".jspPane").css({
			width:745,
			"padding-bottom":8
		});
    };
    
    WORK_Shop.prototype.showMenuLeftScrollbar=function(divMain,divScrollWindow){
      var iComponentHeight=parseInt(divMain.style.height);
      var iComponentWidth=parseInt(divMain.style.width);
      var iScrollHeight=parseInt(divScrollWindow.style.height);
      
      var overAmount=iScrollHeight-iComponentHeight;
      var iSizeH=Math.round((iComponentHeight/iScrollHeight)*iComponentHeight);
      var sRatio=overAmount/(iComponentHeight-iSizeH);
      
      var divScrollBar = ZA.createDiv(divMain,"menuleftscroll","","div");
      $(divScrollBar).css({ height:iComponentHeight });
      ZS.divScrollBar = divScrollBar;
      
      var divBar=document.getElementById("menuleftscrollbar");
      if (iComponentHeight < iScrollHeight){
        if(!divBar) {
          divBar=ZA.createDiv(divScrollBar,"menuleftbar","menuleftscrollbar");
        }
        $(divBar).draggable("destroy");
        $(divBar).draggable({containment:"parent",drag: function() {
            var iTmp=parseInt(this.style.top)*sRatio;
            divScrollWindow.style.top=-iTmp+"px";
          }
        });
        divBar.style.height=iSizeH+"px";
      }
      else {
        if(divBar){
          $(divBar).remove(); 
        }
      }
    };

    WORK_Shop.prototype.buildList=function(){
      var divBody=document.getElementsByTagName("body")[0];
      var sXML = ZS.sXML;
      var blockInterval = 159;
      var perRow = ZS.perPage;
      var itemCount = 0;
      for(a = 0;a < ZS.pageCount; a++){
        var pageBlock = ZA.createDiv(ZS.divList,"","","div");
        $(pageBlock).css({ width:484,height:160,position:"relative",cssFloat:"left",styleFloat:"left" });
        
        for(b = 0;b < ZS.perPage; b++){
          if (ZA.getXML(ZS.sXML,"pack_"+itemCount+"/id")){
          	// var itemBlockContainer = ZA.createDiv(pageBlock,"itemBlockContainer");
            var itemBlock = ZA.createDiv(pageBlock,"itemBlock","","div");
            if (b < perRow)
              $(itemBlock).css({ top:6,left:(b*blockInterval)+7 });
            else
              $(itemBlock).css({ top:105,left:((b-perRow)*blockInterval)+7 });
                        
            //var itemID = ZA.getXML(ZS.sXML,"pack_"+itemCount+"/id");
            // if(ZA.sUsername){
              // $(itemBlock).draggable({
                // cursor:"pointer",
                // cursorAt:{top:70,left:50},
                // helper:function(event,ui) {
                  // var img = ZA.createDiv(document.body,"","dragImg","img");
                  // $(img).css({zIndex:20,width:100,height:140});
                  // imgPath = $(this).css('background-image').replace("url(\"","");
                  // imgPath = imgPath.replace("\")","");
                  // img.src=imgPath;
                  // return $(img);
                // }
              // });
            // }

            /* DRAG DROP FEATURE - ON HOLD
            if(ZA.sUsername){
            var dropZone = document.getElementById("window_"+ZL.iComponentNo);
              $(dropZone).css({backgroundColor:"#000"});
              $( dropZone ).droppable({
                accept: "#dragImg",
                activeClass: "borderDragAccept",
                hoverClass: "bgDragAccept", 
                drop: function( event, ui ) {
                  alert("Buy me bitch");
                }
              });
            }
            */
            
            var infoBox = ZA.createDiv(itemBlock,"","","div");
            $(infoBox).css({ width:152,height:42,top:0,opacity:1,background:"url(_site/info_title_bg.png)" });
            var textBox = ZA.createDiv(itemBlock,"","","div");
            $(textBox).css({ paddingTop:5,paddingRight:6,fontSize:9,lineHeight:"8px",width:140,height:38,top:0,color:"#FFF",right:5,textAlign:"right" });          
            $(textBox).html("<span class=txtGreen>"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/desc")+"</span><br />"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/cards")+" cards in pack <br /> <span class='txtBlue'>"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/price")+" TCG</span>");
            var buyButton = ZA.createDiv(itemBlock,"","buyButton","div");
            $(buyButton).attr('alt',itemCount.toString());
            //$(buyButton).html('Buy');
            //$(buyButton).addClass('cmdButton');
            buyButton.onclick = (function() {
              var current_i = ZA.getXML(ZS.sXML,"pack_"+itemCount+"/id");
              return function() {
                  ZS.buyItem(current_i,$(this).attr('alt'));
              }
            });
            
            var imageBox = ZA.createDiv(itemBlock,"imageBlock","","div");
            $(imageBox).css({ width:150,height:"60%",bottom:10,backgroundColor:"#cccccc" });
            var sImgPath = ZA.getXML(ZS.sXML,"pack_"+itemCount+"/fullimageserver")+"products/"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/img")+".png";
            $(imageBox).css({ backgroundImage:"url("+sImgPath+")",backgroundPosition:ZA.getXML(ZS.sXML,"pack_"+itemCount+"/backgroundposition") }); 

            
            // var textBox = ZA.createDiv(itemBlock,"","","div");
            // $(textBox).css({ paddingTop:2,paddingLeft:6,width:92,height:38,bottom:10,color:"#FFF",textAlign:"left" });          
            // $(textBox).html("<span class='txtGreen'>"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/desc")+"</span><br />"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/cards")+" cards - TCG <span class='txtBlue'>"+ZA.getXML(ZS.sXML,"pack_"+itemCount+"/price")+"</span>");
            // var buyButton = ZA.createDiv(itemBlock,"","","div");
            // $(buyButton).css({ zIndex:4,bottom:15,left:107 });
            // $(buyButton).attr('alt',itemCount.toString());
            // $(buyButton).html('Buy');
            // $(buyButton).addClass('cmdButton');
            // buyButton.onclick = (function() {
              // var current_i = ZA.getXML(ZS.sXML,"pack_"+itemCount+"/id");
              // return function() {
                  // ZS.buyItem(current_i,$(this).attr('alt'));
              // }
            // });
            

            itemCount++;
          }
        }
        
      }
    };

    WORK_Shop.prototype.toggleMax=function(){
      var iSpeed = 300;
      if(!ZS.divListLarge){
      	ZS.buildListLarge();
      }
      if (ZA.aComponents[ZS.iComponentNo].iIsMaximized) {
        $(ZS.divList).animate({opacity:0},iSpeed,function(){ ZS.divList.style.display="none" });
        $(ZS.divScroll).animate({opacity:0},iSpeed,function(){
          ZS.divScroll.style.display="none";
          ZS.divListLarge.style.display="block";
        });
        $(ZS.divListLarge).animate({opacity:1},iSpeed,function(){ /*ZS.showMenuLeftScrollbar(ZS.divData,ZS.divListLarge)*/ ZS.activateProductsScrollbar(); });
      } else {
        $(ZS.divList).animate({opacity:1},iSpeed,function(){ ZS.divList.style.display="block" });
        $(ZS.divScroll).animate({opacity:1},iSpeed,function(){ ZS.divScroll.style.display="block" });
        $(ZS.divScrollBar).remove();
        $(ZS.divListLarge).animate({opacity:0},iSpeed,function(){ ZS.divListLarge.style.display="none" });
      }
    };

    WORK_Shop.prototype.buyItem=function(itemID,index){
      if (!ZA.sUsername) {
        ZA.callAjax("_app/shop/?buyItem="+itemID,function(xml){ ZS.buyResponse(xml); },2);
      }else{
        var bConfirm = confirm("Buy the "+ZA.getXML(ZS.sXML,"pack_"+index+"/desc")+" for "+ZA.getXML(ZS.sXML,"pack_"+index+"/price")+" TCG credits?");
        if(bConfirm){
          ZA.callAjax("_app/shop/?buyItem="+itemID,function(xml){ ZS.buyResponse(xml); },2);
        }
      }
    };

    WORK_Shop.prototype.buyResponse=function(xml){
      var response = "";
      var icon = "-697px -63px";
      var showMsg = true;
      switch(parseInt(ZA.getXML(xml,"value")))
      {
        case -1:
          response = "Insufficient credits to purchase item.";
        break;
        case 0:
          response = "You need to login before you can use the shop.";
        break;
        case 1:
          showMsg = false;
          //reload card comparison
          //ZA.callAjax(ZC.sURL+"?init=1",function(xml){ZC.init(xml);});
          //Booster Purchased Display
          ZA.createWindowPopup(-1,"",690,490,1,0);
          var divWindow=document.getElementById("window_-1");
          var divData=ZA.createDiv(divWindow);
          $(divData).css({
            width:"100%",
            height:"100%",
            padding:5
          });
          var divMemo=ZA.createDiv(divData);
          $(divMemo).css({textAlign:"left",position:"absolute",left:"10px",top:"10px"});
          $(divMemo).html('<strong>Booster Purchase Successful</strong><br />You have received the following cards...');
          var divCards = ZA.createDiv(divData);
          $(divCards).css({
          	background:"url(_site/line.gif) repeat",
          	top:45,
          	left:10,
          	width:380,
          	height:380,
          	border:"5px solid #999",
          	"-moz-border-radius":"5px"
          });
          var iCount = ZA.getXML(xml,"count");
          var iItemCount = 0;
          var iTop = 5;
          var iLeft = 5;
          for(i=0;i<iCount;i++){
            var qty = ZA.getXML(xml,"cards/card_"+i+"/qty");
            for(o=0;o<qty;o++){
              var description = ZA.getXML(xml,"cards/card_"+i+"/description");
              var cardBlock = ZA.createDiv(divCards,"cblock","albumthumbnail_"+i,"div");
              $(cardBlock).css({
              	position:"relative",
              	"float":"left",
              	width:64,
              	height:115,
              	marginTop:10,
              	marginLeft:10
              });
              $(cardBlock).attr('title',description);
              var imgBlock = ZA.createDiv(cardBlock,"","","img");
              $(imgBlock).css({ width:64,height:90,cursor:"pointer" });
              
              var imgPath = ZA.getXML(xml,"cards/card_"+i+"/path");
              var imgID = ZA.getXML(xml,"cards/card_"+i+"/img");
              imgBlock.src = imgPath+"cards/"+imgID+"_web.jpg";
              
              //click event
              $(imgBlock).click({p:imgPath,i:imgID,b:cardBlock},function(event){
                 var p = event.data.p;
                 var i = event.data.i;
             	 $("#imgFront").attr("src","").hide('fade',150,function(){
             	 	$(this).hide().css({width:250,marginLeft:0});
                 	$("#imgFront").attr("src",p+"cards/"+i+"_front.jpg").show();
             	 });
                 $("#imgBack").hide('fade',150,function(){
                 	$(this).hide();
                 	$("#imgBack").attr("src",p+"cards/"+i+"_back.jpg");
                 });
              });
              
              var cardName = ZA.createDiv(cardBlock);
              $(cardName).css({
              	width:"100%",
              	paddingTop:1,
              	height:24,
              	overflow:"hidden",
              	textAlign:"center",
              	color:"#FFF"
              });
              $(cardName).html(description);
            }
          }
          
          var divPreview = ZA.createDiv(divMemo,"","divBuyPreview","div");
          $(divPreview).css({ top:34,left:400,width:250,height:350,background:"url(img/cards/gc.jpg) no-repeat" });
          var imgFront = ZA.createDiv(divPreview,"imgPreview","imgFront","img");
          $(imgFront).css({ height:350,cursor:"pointer" });
          var imgFront = ZA.createDiv(divPreview,"imgPreview","imgBack","img");
          $(imgFront).css({ height:350,cursor:"pointer" }).hide();
          
          $(".imgPreview").click(function(){
          	var img1;
          	var img2;
            if($("#imgFront").is(":visible")){
            	img1 = $("#imgFront");
            	img2 = $("#imgBack");
            }
            else{
            	img2 = $("#imgFront");
            	img1 = $("#imgBack");
            }
            //flip card
			img1.animate({
				width:10,
				marginLeft:120
			},150,function(){
				img1.hide();
				img2.css({width:10,marginLeft:120}).show().animate({
					width:250,
					marginLeft:0
				},150);
			});
         });
          
          var divClose = ZA.createDiv(divData,"cmdButton","","div");
          $(divClose).html('Close');
          $(divClose).css({bottom:25,right:25});
          $(divClose).click(function(){
            ZA.callAjax("_app/album/?init=1",function(xml){ZL.init(xml);});
            $("#bodycloak_-1").remove();
            $("#windowcontainer_-1").remove();
            $("#window_-1").remove();
          });
          
         // ZA.oPlayerBar.update({credits:ZA.getXML(xml,"credits")});
        break;
        default:
          response = "Oops. Something went wrong. Try again in a while.";
      }
      if(showMsg){ ZS.showWindow(icon,response); }
    };

    WORK_Shop.prototype.buildScroller=function(){
      var iWindowWidth = parseInt(ZS.divData.style.width);
      
      var divArrowLeft = ZA.createDiv(ZS.divScroll,"","","div");
      $(divArrowLeft).css({ top:0,left:0,cursor:"pointer",width:20,height:20,backgroundImage:ZS.imgAll,backgroundPosition:"-180px -60px" });
      $(divArrowLeft).click(function(e){
        if(ZS.currentPage != 0){
          ZS.currentPage--;
          ZS.gotoPage(ZS.currentPage);
        }
      });
      
      var divPageCountList = ZA.createDiv(ZS.divScroll,"","","div");
      var offsetCount = (444-(ZS.pageCount*14))/2;
      var iLeft = offsetCount-14;
      $(divPageCountList).css({ top:0,left:20,width:444,height:20});


      for(i=0;i<ZS.pageCount;i++){
        var divPageIcon = ZA.createDiv(divPageCountList,"","","div");
        iLeft += 14;
        $(divPageIcon).css({ top:7,left:iLeft,cursor:"pointer",width:7,height:7,backgroundImage:ZS.imgAll,backgroundPosition:"-130px -80px"});
        divPageIcon.onclick = (function() {
            var current_i = i;
            return function() {
                ZS.currentPage=current_i;
                ZS.gotoPage(current_i);
            }
         });
      }
      
      ZS.gotoPage(0);
      var divArrowRight = ZA.createDiv(ZS.divScroll,"","","div");
      $(divArrowRight).css({ top:0,right:0,cursor:"pointer",width:20,height:20,backgroundImage:ZS.imgAll,backgroundPosition:"-260px -60px" });
      $(divArrowRight).click(function(e){
        if(ZS.currentPage != ZS.pageCount-1){
          ZS.currentPage++;
          ZS.gotoPage(ZS.currentPage);
        }
      });
    };
    
    WORK_Shop.prototype.gotoPage=function(page){
      for (i=0;i<ZS.pageCount;i++){
        $(ZS.divScroll.childNodes[1].childNodes[i]).css({ backgroundPosition:"-130px -80px" }); 
      }
      $(ZS.divScroll.childNodes[1].childNodes[page]).css({ backgroundPosition:"-120px -80px" });
      var newPos = page*-484;
      $(ZS.divList).animate({left:newPos},600);
    };

  }
	WORK_Shop._iInited=1;
};

var ZS = new WORK_Shop();
ZS.iComponentNo=2;
ZA.aComponents[ZS.iComponentNo].fMaximizeFunction=ZS.toggleMax;
ZA.callAjax("_app/shop/?init=1",function(xml){ ZS.init(xml); },2);
