function WORK_Album(){
	this.iComponentNo=0;
	this.divData=0;
	this.sURL="_app/album/";
	this.sXML="";
	this.iCurrentAlbumNo=0;
	this.iCellCount=0;
	this.iCellWidth=148;
	this.iCellHeight=100;
	this.iCellMargin=7;
	this.iCellsPerPage=4;
	this.iMaxRows=2;
	this.iCurrentPage=0;
	this.iAlbumPageIsActive=1;
	
	if (typeof WORK_Album._iInited=="undefined"){


WORK_Album.prototype.init=function(sXML){
  ZL.divData=document.getElementById("window_"+ZL.iComponentNo);
  ZL.sXML=sXML;
  
	var divControlsLeft=ZA.createDiv(ZL.divData,"controlspageleft");
	divControlsLeft.onclick=ZA.clickPageLeft(ZL);
	
	var divControlsRight=ZA.createDiv(ZL.divData,"controlspageright");
	divControlsRight.onclick=ZA.clickPageRight(ZL);

	ZL.showAlbums();
};


WORK_Album.prototype.maximize=function(){
	if (ZL.iAlbumPageIsActive){
		ZL.showAlbums();
	} else {
		ZL.showCards(ZL.iCurrentAlbumNo);	
	}
};

		
WORK_Album.prototype.clickGoToAlbums=function(){
	return function(){
		ZL.showAlbums();
	};
};

		
WORK_Album.prototype.clickShowCards=function(iAlbumNo){
	return function(){
		ZL.iCurrentAlbumNo=iAlbumNo;
		ZL.showCards(iAlbumNo);
	};
};

		
WORK_Album.prototype.showCards=function(iAlbumNo){
	ZL.iCellWidth=64;
	ZL.iCellHeight=90;
	if (ZA.aComponents[ZL.iComponentNo].iIsMaximized) {
		ZL.iCellMargin=14;
		ZL.iCellsPerPage=24;
		ZL.iMaxRows=4;
	} else {
		ZL.iCellMargin=12;
		ZL.iCellsPerPage=8;
		ZL.iMaxRows=2;
	}
	ZL.iCurrentPage=0;
	ZL.iAlbumPageIsActive=0;
	var divCards=document.getElementById("pagebg_"+ZL.iComponentNo);
	if (divCards){
		ZL.divData.removeChild(divCards);
	}
	var divCards=ZA.createDiv(ZL.divData,"pagebg","pagebg_"+ZL.iComponentNo);
	var divAlbum=document.getElementById("albumgotoalbum");
	$(divAlbum).css({display:"block"});
	divAlbum.onclick=ZL.clickGoToAlbums();
	if (ZA.aComponents[ZL.iComponentNo].iIsMaximized) {
		var iHeight=ZA.iHeightContainer-195;
	} else {
		var iHeight=parseInt(ZA.aComponents[ZL.iComponentNo].aPos[3])-80;		
	}
	var iCategoryID=ZA.getXML(ZL.sXML,"albums/album_"+iAlbumNo+"/category_id");
	var iNumCards=parseInt(ZA.getXML(ZL.sXML,"albumcards/no_of_cards"));
	var aCards=[];
	for (var iCount=0;iCount<iNumCards;iCount++){
		if (ZA.getXML(ZL.sXML,"albumcards/albumcard_"+iCount+"/category_id")
			==iCategoryID){
			aCards.push(iCount);
		}
	}
	var iSize=aCards.length;
	ZL.iCellCount=iSize;
	$(divCards).css({
		height:iHeight+"px",
		width:(ZL.iCellCount*(ZL.iCellWidth+ZL.iCellMargin))+"px"
	});

	var iLeft=ZL.iCellMargin;
	var iTop=ZL.iCellMargin;
	for (var iCount=0;iCount<iSize;iCount++){
		var iCardNo=aCards[iCount];
		var divCard=ZA.createDiv(divCards,"pagebox");
		divCard.innerHTML=ZA.getXML
			(ZL.sXML,"albumcards/albumcard_"+iCardNo+"/description");
		$(divCard).css({
			backgroundImage:"url("
			+ZA.getXML(ZL.sXML,"albumcards/albumcard_"+iCardNo+"/thumbnail_imageserver")
			+"cards/"
			+ZA.getXML(ZL.sXML,"albumcards/albumcard_"+iCardNo+"/image")
			+"_thumb.png)",
			left:iLeft+"px",
			top:iTop+"px",
			width:ZL.iCellWidth+"px",
			height:ZL.iCellHeight+"px"
		});
		iTop+=ZL.iCellHeight+ZL.iCellMargin;
		if (iTop>(ZL.iMaxRows*(ZL.iCellHeight+ZL.iCellMargin))) {
			iTop=ZL.iCellMargin;
			iLeft+=ZL.iCellWidth+ZL.iCellMargin;
		}
	}
	ZA.showPageDots(ZL);
};





		
WORK_Album.prototype.showAlbums=function(){
	ZL.iCellWidth=148;
	ZL.iCellHeight=100;
	if (ZA.aComponents[ZL.iComponentNo].iIsMaximized) {
		ZL.iMaxRows=4;
		ZL.iCellMargin=14;
		ZL.iCellsPerPage=24;
	} else {
		ZL.iMaxRows=2;
		ZL.iCellMargin=7;
		ZL.iCellsPerPage=4;
	}
	ZL.iCurrentPage=0;
	ZL.iAlbumPageIsActive=1;
	ZL.iCellCount=parseInt(ZA.getXML(ZL.sXML,"albumcount"));

	var divAlbums=document.getElementById("pagebg_"+ZL.iComponentNo);
	if (divAlbums) {
		ZL.divData.removeChild(divAlbums);
	}
	var divAlbums=ZA.createDiv(ZL.divData,"pagebg","pagebg_"+ZL.iComponentNo);
	if (ZA.aComponents[ZL.iComponentNo].iIsMaximized) {
		var iHeight=ZA.iHeightContainer-195;
	} else {
		var iHeight=parseInt(ZA.aComponents[ZL.iComponentNo].aPos[3])-80;		
	}
	$(divAlbums).css({
		height:iHeight+"px",
		width:(ZL.iCellCount*(ZL.iCellWidth+ZL.iCellMargin))+"px"
	});
	var iCount=0;
	var iLeft=ZL.iCellMargin;
	var iTop=ZL.iCellMargin;
	while (iCount<ZL.iCellCount) {
		var divAlbum=ZA.createDiv(divAlbums,"pagebox");
		var divDesc=ZA.createDiv(divAlbum,"pageboxdesc");
		$(divDesc).css({
			opacity:0.8,
			width:(ZL.iCellWidth-4)+"px"
		});
		$(divDesc).html(
				"<span class='txtGreen'>"
				+ZA.getXML(ZL.sXML,"albums/album_"+iCount+"/description")
				+"</span>"
				+"<br />"
				+"<span class='txtBlue'>"
				+ZA.getXML(ZL.sXML,"albums/album_"+iCount+"/no_of_cards")
				+"</span><span class='txtWhite'>"
				+" cards total."
				+"<br />"
				+"You own "
				+"</span><span class='txtBlue'>"
				+ZA.getXML(ZL.sXML,"albums/album_"+iCount+"/cards_own")				
				+"</span><span class='txtWhite'> cards."
				+"</span>"
		);
		var divView=ZA.createDiv(divAlbum,"buttonviewalbum");
		divView.onclick=ZL.clickShowCards(iCount);
		$(divAlbum).css({
			backgroundImage:"url("
			+ZA.getXML(ZL.sXML,"albums/album_"+iCount+"/imageserver")
			+"albums/"
			+ZA.getXML(ZL.sXML,"albums/album_"+iCount+"/image")
			+".png)",
			left:iLeft+"px",
			top:iTop+"px",
			width:ZL.iCellWidth+"px",
			height:ZL.iCellHeight+"px"
		});
		iTop+=ZL.iCellHeight+ZL.iCellMargin;
		if (iTop>(ZL.iMaxRows*(ZL.iCellHeight+ZL.iCellMargin))) {
			iTop=ZL.iCellMargin;
			iLeft+=ZL.iCellWidth+ZL.iCellMargin;
		}
		iCount++;
	}
	ZA.showPageDots(ZL);
};


		}
	WORK_Album._iInited=1;
};



var ZL = new WORK_Album();
ZL.iComponentNo=4;
ZA.aComponents[ZL.iComponentNo].fMaximizeFunction=ZL.maximize;
ZA.callAjax(ZL.sURL+"?init=1",function(xml){ZL.init(xml);});
