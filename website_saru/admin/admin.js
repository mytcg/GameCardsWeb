//==============================================================================
//APP CLASS 
function WORK_App(){
	this.sBrowserName="";
	this.sURL="";
	this.iMainCategoryID=0;
	this.iSubCategoryID=0;
	this.iProductID=0;
	this.iUserID=0;
	this.iPackID=0;
	this.iCardID=0;
	this.iCardQualityID=0;
	this.iImageServerID=0;
	this.sXMLCategories="";
	this.sXMLProducts="";
	this.sXMLUsers="";
	this.sXMLImageServers="";
	this.sXMLCards="";
	this.sXMLCardsQuality="";
	
	if (typeof WORK_App._iInited=="undefined"){

//create body
WORK_App.prototype.createBody=function(){
	ZA.sBrowserName=navigator.appName;
	if (ZA.sBrowserName=="Microsoft Internet Explorer"){
		ZA.sBrowserName="MSIE";
	}
	ZA.callAjax("_app/?init=1",function(xml){ ZA.init(xml); });
};


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
  $.get(pageQuery, function(data,status){
  	if ((!iForceBusy)&&(divData)) {
  		divData.removeChild(divBusy);
  	}
    if(callback){ 
    	callback(data);
    }
  });
};


/*********** create html element */
WORK_App.prototype.createDiv=function(divParent,sClassName,sID,sType){
	var divA;
	if (!sType) sType="div";
	divA=document.createElement(sType);
	if (sClassName) divA.className=sClassName;
	if (sID) divA.setAttribute("id",sID);
	divParent.appendChild(divA);
	return divA;
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
    
    if (xData.selectSingleNode(sElement).attributes.getNamedItem("val"))
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
    return "";
  }
};


//initialize variables and build page
WORK_App.prototype.init=function(sXMLInit){
//	alert(sXMLInit)
	ZA.sURL=ZA.getXML(sXMLInit,"url");
	var divBody=document.getElementsByTagName("body")[0];
	var divHeader=ZA.createDiv(divBody,"","bodyheader");
	$(divHeader).css({opacity:0.8});
	var aMenuItems=ZA.getXML(sXMLInit,"menu").split("|");
	var iLeft=5;
	var iSize=aMenuItems.length;
	for (var iCount=0;iCount<iSize;iCount++) {
		var divItem=ZA.createDiv(divHeader,"menuitem");
		var sDesc=aMenuItems[iCount];
		$(divItem).html(sDesc);
		$(divItem).css({left:iLeft+"px"});
		divItem.onclick=ZA.clickMenu(iCount);
		iLeft+=100;
	}
	var divPage=ZA.createDiv(divBody,"","bodypage");
	var divData=ZA.createDiv(divPage,"","bodypagedata");
	$(divData).css({fontSize:"large"});
	$(divData).html("ADMINISTRATION: "+ZA.sURL);
};


//click menu item 
WORK_App.prototype.clickMenu=function(iMenuNo){
	return function() {
		switch (iMenuNo) {
			
			case 0:
				ZA.callAjax
					("_app/?categories=1",function(xml){ZCAT.createPage(xml);});				
			break;
			
			case 1:
				ZA.callAjax
					("_app/?products=1",function(xml){ZPRO.createPage(xml);});				
			break;
				
			case 2:
				ZA.callAjax
					("_app/?users=1",function(xml){ZUSE.createPage(xml);});				
			break;
					
			case 3:
				ZA.callAjax
					("_app/?packs=1",function(xml){ZPAC.createPage(xml);});				
			break;
					
			case 4:
				ZA.callAjax
					("_app/?imageservers=1",function(xml){ZIMA.createPage(xml);});				
			break;
						
			case 5:
				ZA.callAjax
					("_app/?cards=1",function(xml){ZCAR.createPage(xml);});				
			break;
					
			case 6:
				ZA.callAjax
					("_app/?cardquality=1",function(xml){ZCQU.createPage(xml);});				
			break;
						
			case 7:
				ZA.callAjax("_app/?logout=1",function(xml){ZA.logout(xml);});
			break;
				
		}
	};
};


//logout return
WORK_App.prototype.logout=function(sXML){
	window.open("../","_parent");
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


		WORK_App._iInited=1;
	}
};//END function WORK_App()




//==============================================================================
//CATEGORIES CLASS 
function WORK_Categories(){
	if (typeof WORK_Categories._iInited=="undefined"){

//categories page 
WORK_Categories.prototype.createPage=function(sXML){
	ZA.iMainCategoryID=0;
	ZA.sXMLCategories=sXML;
	var divPage=document.getElementById("bodypage");
	var divData=document.getElementById("bodypagedata");
	if (divData) {
		divPage.removeChild(divData);
	}
	var divData=ZA.createDiv(divPage,"","bodypagedata");
	var iTop=5;
	var divDesc=ZA.createDiv(divData);
	$(divDesc).css({top:iTop+"px",fontSize:"large"});
	$(divDesc).html("Main Categories");
	iTop+=20;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divSelect=ZA.createDiv(divBox,"","maincategory","select");
	divSelect.multiple="multiple";
	divSelect.size=10;
	$(divSelect).css({width:"350px"});
	var iCount=0;
	var iID=0;
	while (iID=parseInt(ZA.getXML(sXML,"category_"+iCount+"/id"))) {
		var divItem=ZA.createDiv(divSelect,"","","option");
		divItem.onclick=ZCAT.clickMain();
		divItem.value=iID;
		divItem.text=ZA.getXML(sXML,"category_"+iCount+"/description");
		iCount++;
	}
	iTop+=170;
	var divDesc=ZA.createDiv(divData);
	$(divDesc).css({top:iTop+"px"});
	$(divDesc).html("Description");	
	iTop+=15;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv(divBox,"inputtext","categorymaindescription","input");
	divInput.size=45;
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Save";
	divInput.onclick=ZCAT.clickMainSave();
	iTop+=30;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Clear";
	divInput.onclick=ZCAT.clickMainClear();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="New";
	divInput.onclick=ZCAT.clickMainNew();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Delete";
	divInput.onclick=ZCAT.clickMainDelete();

	var divSub=ZA.createDiv(divPage,"","bodypagedata2");
	var iTop=5;
	var divDesc=ZA.createDiv(divSub);
	$(divDesc).css({top:iTop+"px",fontSize:"large"});
	$(divDesc).html("Subcategories");
	iTop+=190;
	var divDesc=ZA.createDiv(divSub);
	$(divDesc).css({top:iTop+"px"});
	$(divDesc).html("Description");	
	iTop+=15;
	var divBox=ZA.createDiv(divSub);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv
		(divBox,"inputtext","categorysubdescription","input");
	divInput.size=45;
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Save";
	divInput.onclick=ZCAT.clickSubSave();
	iTop+=30;
	var divBox=ZA.createDiv(divSub);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Clear";
	divInput.onclick=ZCAT.clickSubClear();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="New";
	divInput.onclick=ZCAT.clickSubNew();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Delete";
	divInput.onclick=ZCAT.clickSubDelete();
};


//click main category 
WORK_Categories.prototype.clickMain=function(){
	return function() {
		var divInput=document.getElementById("categorysubdescription");
		if (divInput) {
			divInput.value="";
		}		
		var divSelect=document.getElementById("maincategory");
		var iSelected=divSelect.selectedIndex;
		var divInput=document.getElementById("categorymaindescription");
		divInput.value=divSelect.options[iSelected].text;
		var iID=divSelect.options[iSelected].value;
		ZA.iMainCategoryID=iID;
		var divPage2=document.getElementById("bodypagedata2");
		var divBox=document.getElementById("subcategorybox");
		if (divBox) {
			divPage2.removeChild(divBox);
		}
		var divBox=ZA.createDiv(divPage2,"","subcategorybox");
		$(divBox).css({top:"25px"});		
		var divSelect=ZA.createDiv(divBox,"","subcategory","select");
		divSelect.multiple="multiple";
		divSelect.size=10;
		$(divSelect).css({top:"20px",width:"350px"});
		var iCount=0;
		var iFound=0;
		while ((!iFound)&&
			(iTest=ZA.getXML(ZA.sXMLCategories,"category_"+iCount+"/id"))) {
			if (iID==iTest) {
				iFound=1;
			} else {
				iCount++;				
			}
		}
		if (iFound) {
			var iCountSub=0;
			var iID=0;
			while (iID=ZA.getXML(ZA.sXMLCategories
				,"category_"+iCount+"/subcategory_"+iCountSub+"/id")) {
				var divOption=ZA.createDiv(divSelect,"","","option");
				divOption.value=iID;
				divOption.text=ZA.getXML(ZA.sXMLCategories
						,"category_"+iCount+"/subcategory_"+iCountSub+"/description");
				divOption.onclick=ZCAT.clickSub();
				iCountSub++;
			}
		}
	};
};


//click main category save
WORK_Categories.prototype.clickMainSave=function(){
	return function() {
		var divInput=document.getElementById("categorymaindescription");
		if (!divInput.value) {
			return;
		}
		ZA.callAjax("_app/?categorymainsave=1&id="+ZA.iMainCategoryID
			+"&value="+divInput.value,function(xml){ZCAT.createPage(xml);});
	};
};


//click main category clear
WORK_Categories.prototype.clickMainClear=function(){
	return function() {
		var divInput=document.getElementById("categorymaindescription");
		divInput.value="";
		ZA.iMainCategory=0;
	};
};


//click main category new
WORK_Categories.prototype.clickMainNew=function(){
	return function() {
		var divInput=document.getElementById("categorymaindescription");
		if (!divInput.value) {
			return;
		}
		ZA.callAjax("_app/?categorymainnew=1&value="
			+divInput.value,function(xml){ZCAT.createPage(xml);});
	};
};


//click main category delete
WORK_Categories.prototype.clickMainDelete=function(){
	return function() {
		var divInput=document.getElementById("categorymaindescription");
		if (!divInput.value) {
			return;
		} else {
			var iAnswer=confirm("Click OK to delete this category.");
		}
		if (iAnswer) {
			ZA.callAjax("_app/?categorymaindelete=1&id="+ZA.iMainCategoryID
				,function(xml){ZCAT.createPage(xml);});
		}
	};
};


//click subcategory
WORK_Categories.prototype.clickSub=function(){
	return function() {
		var divSelect=document.getElementById("subcategory");
		var iSelected=divSelect.selectedIndex;
		var divInput=document.getElementById("categorysubdescription");
		ZA.iSubCategoryID=divSelect.options[iSelected].value;
		divInput.value=divSelect.options[iSelected].text;
	};
};


//click subcategory save
WORK_Categories.prototype.clickSubSave=function(){
	return function() {
		var divInput=document.getElementById("categorysubdescription");
		if (!divInput.value) {
			return;
		}
		ZA.callAjax("_app/?categorymainsave=1&id="+ZA.iSubCategoryID
			+"&value="+divInput.value,function(xml){ZCAT.createPage(xml);});
	};
};


//click subcategory clear
WORK_Categories.prototype.clickSubClear=function(){
	return function() {
		var divInput=document.getElementById("categorysubdescription");
		divInput.value="";
		ZA.iSubCategory=0;
	};
};


//click subcategory new
WORK_Categories.prototype.clickSubNew=function(){
	return function() {
		var divInput=document.getElementById("categorysubdescription");
		if (!divInput.value) {
			return;
		}
		ZA.callAjax("_app/?categorymainnew=1&value="
			+divInput.value,function(xml){ZCAT.createPage(xml);});
	};
};


//click subcategory delete
WORK_Categories.prototype.clickSubDelete=function(){
	return function() {
		var divInput=document.getElementById("categorysubdescription");
		if (!divInput.value) {
			return;
		} else {
			var iAnswer=confirm("Click OK to delete this category.");
		}
		if (iAnswer) {
			ZA.callAjax("_app/?categorymaindelete=1&id="+ZA.iMainCategoryID
				,function(xml){ZCAT.createPage(xml);});
		}
	};
};


		WORK_Categories._iInited=1;
	}
};//END function WORK_Categories()




//==============================================================================
//PRODUCTS CLASS 
function WORK_Products(){
	if (typeof WORK_Products._iInited=="undefined"){

//categories page 
WORK_Products.prototype.createPage=function(sXML){
//	alert(sXML)
	ZA.sXMLProducts=sXML;
	ZA.iMainCategoryID=0;
	ZA.sXMLCategories=sXML;
	var divPage=document.getElementById("bodypage");
	var divData=document.getElementById("bodypagedata");
	var divData2=document.getElementById("bodypagedata2");
	if (divData) {
		divPage.removeChild(divData);
	}
	var divData=ZA.createDiv(divPage,"","bodypagedata");
	if (divData2) {
		divPage.removeChild(divData2);
	}
	var divData2=ZA.createDiv(divPage,"","bodypagedata2");
	var iTop=5;
	var divDesc=ZA.createDiv(divData);
	$(divDesc).css({top:iTop+"px",fontSize:"large"});
	$(divDesc).html("Products");
	iTop+=20;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divSelect=ZA.createDiv(divBox,"","productsselect","select");
	$(divSelect).css({width:"290px"});
	divSelect.onchange=ZPRO.clickSelect();
	var divOption=ZA.createDiv(divSelect,"","","option");
	var iCount=0;
	var iID=0;
	while (iID=ZA.getXML(sXML,"products/product_"+iCount+"/product_id")) {
		var divOption=ZA.createDiv(divSelect,"","","option");
		divOption.value=iCount;
		divOption.text=ZA.getXML
			(sXML,"products/product_"+iCount+"/description");
		iCount++;
	}
	iTop+=30;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	$(divBox).html("Description");
	iTop+=15;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divDesc=ZA.createDiv(divBox,"inputtext","productdescription","input");
	divDesc.size=45;
	iTop+=30;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	$(divBox).html("Price");
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px",left:"80px"});
	$(divBox).html("In Stock");
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px",left:"160px"});
	$(divBox).html("Image");
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px",left:"260px"});
	$(divBox).html("Is Active");
	iTop+=15;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divPrice=ZA.createDiv(divBox,"inputtext","productprice","input");
	divPrice.size=6;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px",left:"80px"});
	var divInStock=ZA.createDiv(divBox,"inputtext","productinstock","input");
	divInStock.size=6;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px",left:"160px"});
	var divImage=ZA.createDiv(divBox,"inputtext","productimage","input");
	divImage.size=10;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px",left:"260px"});
	var divIsActive=ZA.createDiv(divBox,"","productisactive","input");
	divIsActive.type="checkbox";
	
	iTop+=30;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	$(divBox).html("Full Image Server");
	iTop+=15;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divSelect=ZA.createDiv(divBox,"","productsfullimageserver","select");
	$(divSelect).css({width:"290px"});
	iTop+=30;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	$(divBox).html("Thumbnail Image Server");
	iTop+=15;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divSelect1=ZA.createDiv
		(divBox,"","productsthumbnailimageserver","select");
	$(divSelect1).css({width:"290px"});
	var divOption=ZA.createDiv(divSelect,"","","option");
	divOption.value=-1;
	var divOption=ZA.createDiv(divSelect1,"","","option");
	divOption.value=-1;
	var iCount=0;
	var iID=0;
	while (iID=ZA.getXML
		(sXML,"imageservers/imageserver_"+iCount+"/imageserver_id")) {
		var divOption=ZA.createDiv(divSelect,"","","option");
		divOption.value=iID;
		var sDesc=ZA.getXML
			(sXML,"imageservers/imageserver_"+iCount+"/description");
		divOption.text=sDesc;
		var divOption=ZA.createDiv(divSelect1,"","","option");
		divOption.value=iID;
		divOption.text=sDesc;
		iCount++;
	}
	iTop+=70;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Save";
	divInput.onclick=ZPRO.clickSave();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Clear";
	divInput.onclick=ZPRO.clickClear();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="New";
	divInput.onclick=ZPRO.clickNew();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Delete";
	divInput.onclick=ZPRO.clickDelete();
};


//click select box
WORK_Products.prototype.clickSelect=function(){
	return function() {
		ZPRO.clickSelectA();
	};
};


//click select box
WORK_Products.prototype.clickSelectA=function(){
	var divSelect=document.getElementById("productsselect");
	var iSelected=divSelect.selectedIndex;
	var iID=divSelect.options[iSelected].value;
	ZA.iProductID=ZA.getXML
		(ZA.sXMLProducts,"products/product_"+iID+"/product_id");
	var divInput=document.getElementById("productdescription");
	divInput.value=divSelect.options[iSelected].text;
	var divInput=document.getElementById("productprice");
	divInput.value=ZA.getXML
		(ZA.sXMLProducts,"products/product_"+iID+"/price");
	var divInput=document.getElementById("productinstock");
	divInput.value=ZA.getXML
		(ZA.sXMLProducts,"products/product_"+iID+"/in_stock");
	var divInput=document.getElementById("productimage");
	divInput.value=ZA.getXML
		(ZA.sXMLProducts,"products/product_"+iID+"/image");
	var divInput=document.getElementById("productisactive");
	var iIsActive=ZA.getXML
		(ZA.sXMLProducts,"products/product_"+iID+"/is_active");
	if (iIsActive=="1") {
		iIsActive=true;
	} else {
		iIsActive=false;
	}
	divInput.checked=iIsActive;
	var iServerID=ZA.getXML
		(ZA.sXMLProducts,"products/product_"+iID+"/full_imageserver_id");
	var divSelectFull=document.getElementById("productsfullimageserver");
	var iCount=0;
	var iFound=0;
	var iTestID=0;
	while ((!iFound)&&(iCount<divSelectFull.length)) {
		iTestID=divSelectFull.options[iCount].value;
		if (iTestID==ZA.getXML
			(ZA.sXMLProducts,"products/product_"+iID+"/full_imageserver_id")) {
			iFound=1;
		} else {
			iCount++;
		}
	}
	if (iFound) {
		divSelectFull.selectedIndex=iCount;
	} else {
		divSelectFull.selectedIndex=0;		
	}
	var iServerID=ZA.getXML
		(ZA.sXMLProducts,"products/product_"+iID+"/thumbnail_imageserver_id");
	var divSelectThumb=document.getElementById("productsthumbnailimageserver");
	var iCount=0;
	var iFound=0;
	var iTestID=0;
	while ((!iFound)&&(iCount<divSelectThumb.length)) {
		iTestID=divSelectThumb.options[iCount].value;
		if (iTestID==ZA.getXML
			(ZA.sXMLProducts,"products/product_"
			+iID+"/thumbnail_imageserver_id")) {
		iFound=1;
		} else {
			iCount++;
		}
	}
	if (iFound) {
		divSelectThumb.selectedIndex=iCount;
	} else {
		divSelectThumb.selectedIndex=0;		
	}
	var divData=document.getElementById("bodypagedata2");
	var divImg=ZA.createDiv(divData);
	$(divImg).css({width:"250px",height:"350px"});
	divImg.style.backgroundImage
		="url("+divSelectThumb[divSelectThumb.selectedIndex].text
		+"products/"
		+ZA.getXML(ZA.sXMLProducts,"products/product_"+iID+"/image")
		+".png)";
	var divImg=ZA.createDiv(divData);
	$(divImg).css({left:"270px",width:"64px",height:"90px"});
	divImg.style.backgroundImage
		="url("+divSelectThumb[divSelectThumb.selectedIndex].text
		+"products/"
		+ZA.getXML(ZA.sXMLProducts,"products/product_"+iID+"/image")
		+"_thumb.png)";
};


//click product save
WORK_Products.prototype.clickSave=function(){
	return function() {
		var divSelect=document.getElementById("productsselect");
		var iID=divSelect[divSelect.selectedIndex].value;
		iID=ZA.getXML(ZA.sXMLProducts,"products/product_"+iID+"/product_id");
		var divDesc=document.getElementById("productdescription");
		var divPrice=document.getElementById("productprice");
		var divInStock=document.getElementById("productinstock");
		var divImage=document.getElementById("productimage");
		var divIsActive=document.getElementById("productisactive");
		var iIsActive=divIsActive.checked;
		if (iIsActive) {
			iIsActive=1;
		} else {
			iIsActive=0;
		}
		if (divDesc.value=="") {
			return;
		}
		ZA.callAjax("_app/?productsave=1"
			+"&id="+iID
			+"&description="+divDesc.value
			+"&price="+divPrice.value
			+"&instock="+divInStock.value
			+"&image="+divImage.value
			+"&isactive="+iIsActive
			,function(xml){ZPRO.createPage(xml);});
	};
};


//click new
WORK_Products.prototype.clickNew=function(){
	return function() {
		var divDesc=document.getElementById("productdescription");
		var divPrice=document.getElementById("productprice");
		var divInStock=document.getElementById("productinstock");
		var divImage=document.getElementById("productimage");
		var divIsActive=document.getElementById("productisactive");
		var iIsActive=divIsActive.checked;
		if (iIsActive) {
			iIsActive=1;
		} else {
			iIsActive=0;
		}
		if (divDesc.value=="") {
			return;
		}
		ZA.callAjax("_app/?productnew=1"
			+"&description="+divDesc.value
			+"&price="+divPrice.value
			+"&instock="+divInStock.value
			+"&image="+divImage.value
			+"&isactive="+iIsActive
			,function(xml){ZPRO.createPage(xml);});
	};
};


//click clear
WORK_Products.prototype.clickClear=function(){
	return function() {
		var divSelect=document.getElementById("productsselect");
		divSelect.selectedIndex=0;
		ZPRO.clickSelectA();
	};
};


//click delete
WORK_Products.prototype.clickDelete=function(){
	return function() {
		var divSelect=document.getElementById("productsselect");
		var iID=divSelect[divSelect.selectedIndex].value;
		iID=ZA.getXML(ZA.sXMLProducts,"products/product_"+iID+"/product_id");
		if (!iID) {
			return;
		}
		ZA.callAjax("_app/?productdelete=1"
			+"&id="+iID
			,function(xml){ZPRO.createPage(xml);});
	};
};



		WORK_Products._iInited=1;
	}
};//END function WORK_Products()







//==============================================================================
//USERS CLASS 
function WORK_Users(){
	if (typeof WORK_Users._iInited=="undefined"){

//users page 
WORK_Users.prototype.createPage=function(sXML){
	ZA.iUserID=0;
	ZA.sXMLUsers=sXML;
	var divPage=document.getElementById("bodypage");
	var divData=document.getElementById("bodypagedata");
	if (divData) {
		divPage.removeChild(divData);
	}
	var divData=ZA.createDiv(divPage,"","bodypagedata");
	var iTop=5;
	var divDesc=ZA.createDiv(divData);
	$(divDesc).css({top:iTop+"px",fontSize:"large"});
	$(divDesc).html("Users");
	iTop+=20;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divSelect=ZA.createDiv(divBox,"","usersselect","select");
	$(divSelect).css({width:"290px"});
	divSelect.onchange=ZUSE.clickSelect();
	var divOption=ZA.createDiv(divSelect,"","","option");
	var iCount=0;
	var iID=0;
	while (iID=ZA.getXML(sXML,"user_"+iCount+"/user_id")) {
		var divOption=ZA.createDiv(divSelect,"","","option");
		divOption.value=iCount;
		divOption.text=ZA.getXML
			(sXML,"user_"+iCount+"/username");
		iCount++;
	}
	iTop+=30;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	$(divBox).html("Username");
	iTop+=15;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv(divBox,"inputtext","userusername","input");
	divInput.size=45;
	iTop+=30;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	$(divBox).html("Email Address");
	iTop+=15;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv(divBox,"inputtext","useremailaddress","input");
	divInput.size=45;
	iTop+=30;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	$(divBox).html("Real Name");
	iTop+=15;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv(divBox,"inputtext","userrealname","input");
	divInput.size=45;
	iTop+=30;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	$(divBox).html("Credits");
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px",left:"200px"});
	$(divBox).html("Active");
	iTop+=15;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv(divBox,"inputtext","usercredits","input");
	divInput.size=20;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px",left:"200px"});
	var divInput=ZA.createDiv(divBox,"","userisactive","input");
	divInput.type="checkbox";
	iTop+=70;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Save";
	divInput.onclick=ZUSE.clickSave();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Clear";
	divInput.onclick=ZUSE.clickClear();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="New";
	divInput.onclick=ZUSE.clickNew();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Delete";
	divInput.onclick=ZUSE.clickDelete();
};


//click select box
WORK_Users.prototype.clickSelect=function(){
	return function() {
		ZUSE.clickSelectA();
	};
};


//click select box
WORK_Users.prototype.clickSelectA=function(){
	var divSelect=document.getElementById("usersselect");
	var iSelected=divSelect.selectedIndex;
	var iID=divSelect.options[iSelected].value;
	ZA.iUserID=ZA.getXML(ZA.sXMLUsers,"user_"+iID+"/user_id");
	var divInput=document.getElementById("userusername");
	divInput.value=divSelect.options[iSelected].text;
	var divInput=document.getElementById("useremailaddress");
	divInput.value=ZA.getXML(ZA.sXMLUsers,"user_"+iID+"/email_address");
	var divInput=document.getElementById("userrealname");
	divInput.value=ZA.getXML(ZA.sXMLUsers,"user_"+iID+"/name");
	var divInput=document.getElementById("usercredits");
	divInput.value=ZA.getXML(ZA.sXMLUsers,"user_"+iID+"/credits");
	var divInput=document.getElementById("userisactive");
	var iIsActive=ZA.getXML(ZA.sXMLUsers,"user_"+iID+"/is_active");
	if (iIsActive=="1") {
		iIsActive=true;
	} else {
		iIsActive=false;
	}
	divInput.checked=iIsActive;
};


//click save
WORK_Users.prototype.clickSave=function(){
	return function() {
		var divSelect=document.getElementById("productsselect");
		var iID=divSelect[divSelect.selectedIndex].value;
		iID=ZA.getXML(ZA.sXMLProducts,"products/product_"+iID+"/product_id");
		var divDesc=document.getElementById("productdescription");
		var divPrice=document.getElementById("productprice");
		var divInStock=document.getElementById("productinstock");
		var divImage=document.getElementById("productimage");
		var divIsActive=document.getElementById("productisactive");
		var iIsActive=divIsActive.checked;
		if (iIsActive) {
			iIsActive=1;
		} else {
			iIsActive=0;
		}
		if (divDesc.value=="") {
			return;
		}
		ZA.callAjax("_app/?productsave=1"
			+"&id="+iID
			+"&description="+divDesc.value
			+"&price="+divPrice.value
			+"&instock="+divInStock.value
			+"&image="+divImage.value
			+"&isactive="+iIsActive
			,function(xml){ZPRO.createPage(xml);});
	};
};


//click new
WORK_Users.prototype.clickNew=function(){
	return function() {
		var divUsername=document.getElementById("userusername");
		var divEmailAddress=document.getElementById("useremailaddress");
		var divRealName=document.getElementById("userrealname");
		var divCredits=document.getElementById("usercredits");
		var divIsActive=document.getElementById("userisactive");
		var iIsActive=divIsActive.checked;
		if (iIsActive) {
			iIsActive=1;
		} else {
			iIsActive=0;
		}
		if (divDesc.value=="") {
			return;
		}
		ZA.callAjax("_app/?usernew=1"
			+"&username="+divUsername.value
			+"&emailaddress="+divEmailAddress.value
			+"&realname="+divRealName.value
			+"&credits="+divCredits.value
			+"&isactive="+iIsActive
			,function(xml){ZUSE.createPage(xml);});
	};
};


//click clear
WORK_Users.prototype.clickClear=function(){
	return function() {
		var divSelect=document.getElementById("usersselect");
		divSelect.selectedIndex=0;
		ZUSE.clickSelectA();
	};
};


//click delete
WORK_Users.prototype.clickDelete=function(){
	return function() {
		var divSelect=document.getElementById("productsselect");
		var iID=divSelect[divSelect.selectedIndex].value;
		iID=ZA.getXML(ZA.sXMLProducts,"products/product_"+iID+"/product_id");
		if (!iID) {
			return;
		}
		ZA.callAjax("_app/?productdelete=1"
			+"&id="+iID
			,function(xml){ZPRO.createPage(xml);});
	};
};




		WORK_Users._iInited=1;
	}
};//END function WORK_Users()





//==============================================================================
//PACKS CLASS 
function WORK_Packs(){
if (typeof WORK_Packs._iInited=="undefined"){

//packs page 
WORK_Packs.prototype.createPage=function(sXML){
	ZA.iUserID=0;
	ZA.sXMLUsers=sXML;
	var divPage=document.getElementById("bodypage");
	var divData=document.getElementById("bodypagedata");
	if (divData) {
		divPage.removeChild(divData);
	}
	var divData=ZA.createDiv(divPage,"","bodypagedata");
	var iTop=5;
	var divDesc=ZA.createDiv(divData);
	$(divDesc).css({top:iTop+"px",fontSize:"large"});
	$(divDesc).html("Packs");
	iTop+=20;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
};


		WORK_Packs._iInited=1;
	}
};//END function WORK_Packs()





//==============================================================================
//CARDS CLASS 
function WORK_Cards(){
if (typeof WORK_Cards._iInited=="undefined"){

//cards page 
WORK_Cards.prototype.createPage=function(sXML){
	ZA.iCardID=0;
	ZA.sXMLCards=sXML;
	var divPage=document.getElementById("bodypage");
	var divData=document.getElementById("bodypagedata");
	if (divData) {
		divPage.removeChild(divData);
	}
	var divData=ZA.createDiv(divPage,"","bodypagedata");
	var iTop=5;
	var divDesc=ZA.createDiv(divData);
	$(divDesc).css({top:iTop+"px",fontSize:"large"});
	$(divDesc).html("Cards");
	iTop+=20;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
};


		WORK_Cards._iInited=1;
	}
};//END function WORK_Cards()





//==============================================================================
//CARDS CLASS 
function WORK_CardsQuality(){
if (typeof WORK_CardsQuality._iInited=="undefined"){

//cardsquality page 
WORK_CardsQuality.prototype.createPage=function(sXML){
	ZA.iCardQualityID=0;
	ZA.sXMLCardsQuality=sXML;
	var divPage=document.getElementById("bodypage");
	var divData=document.getElementById("bodypagedata");
	if (divData) {
		divPage.removeChild(divData);
	}
	var divData=ZA.createDiv(divPage,"","bodypagedata");
	var iTop=5;
	var divDesc=ZA.createDiv(divData);
	$(divDesc).css({top:iTop+"px",fontSize:"large"});
	$(divDesc).html("Cards Quality");
	iTop+=20;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
};


		WORK_CardsQuality._iInited=1;
	}
};//END function WORK_CardsQuality()





//==============================================================================
//IMAGESERVERS CLASS 
function WORK_ImageServers(){
if (typeof WORK_ImageServers._iInited=="undefined"){

//imageservers page 
WORK_ImageServers.prototype.createPage=function(sXML){
	ZA.iUserID=0;
	ZA.sXMLImageServers=sXML;
	var divPage=document.getElementById("bodypage");
	var divData=document.getElementById("bodypagedata");
	if (divData) {
		divPage.removeChild(divData);
	}
	var divData=ZA.createDiv(divPage,"","bodypagedata");
	var iTop=5;
	var divDesc=ZA.createDiv(divData);
	$(divDesc).css({top:iTop+"px",fontSize:"large"});
	$(divDesc).html("Image Servers");
	iTop+=20;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divSelect=ZA.createDiv(divBox,"","imageserversselect","select");
	$(divSelect).css({width:"290px"});
	divSelect.onchange=ZIMA.clickSelect();
	var divOption=ZA.createDiv(divSelect,"","","option");
	var iCount=0;
	var iID=0;
	while (iID=ZA.getXML(sXML,"imageserver_"+iCount+"/imageserver_id")) {
		var divOption=ZA.createDiv(divSelect,"","","option");
		divOption.value=iCount;
		divOption.text=ZA.getXML
			(sXML,"imageserver_"+iCount+"/description");
		iCount++;
	}
	iTop+=30;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	$(divBox).html("Description");
	iTop+=15;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv
		(divBox,"inputtext","imageserverdescription","input");
	divInput.size=45;
	iTop+=70;
	var divBox=ZA.createDiv(divData);
	$(divBox).css({top:iTop+"px"});
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Save";
	divInput.onclick=ZIMA.clickSave();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Clear";
	divInput.onclick=ZIMA.clickClear();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="New";
	divInput.onclick=ZIMA.clickNew();
	var divInput=ZA.createDiv(divBox,"inputbutton","","input");
	divInput.type="button";
	divInput.value="Delete";
	divInput.onclick=ZIMA.clickDelete();
};


//click select box
WORK_ImageServers.prototype.clickSelect=function(){
	return function() {
		ZIMA.clickSelectA();
	};
};


//click select box
WORK_ImageServers.prototype.clickSelectA=function(){
	var divSelect=document.getElementById("imageserversselect");
	var iSelected=divSelect.selectedIndex;
	var iID=divSelect.options[iSelected].value;
	ZA.iImageServerID=ZA.getXML(ZA.sXMLUsers,"user_"+iID+"/user_id");
	var divInput=document.getElementById("imageserverdescription");
	divInput.value=divSelect.options[iSelected].text;
};


//click imageserver save
WORK_ImageServers.prototype.clickSave=function(){
	return function() {
		var divSelect=document.getElementById("imageserversselect");
		var iID=divSelect[divSelect.selectedIndex].value;
		alert(iID)
		iID=ZA.getXML(ZA.sXMLImageServers,"imageserver_"+iID+"/imageserver_id");
		alert(iID)
		var divDesc=document.getElementById("imageserverdescription");
		if (divDesc.value=="") {
			return;
		}
		ZA.callAjax("_app/?imageserversave=1"
			+"&id="+iID
			+"&description="+divDesc.value
			,function(xml){ZIMA.createPage(xml);});
	};
};


//click new
WORK_ImageServers.prototype.clickNew=function(){
	return function() {
		var divDesc=document.getElementById("imageserverdescription");
		if (divDesc.value=="") {
			return;
		}
		ZA.callAjax("_app/?imageservernew=1"
			+"&description="+divDesc.value
			,function(xml){ZUSE.createPage(xml);});
	};
};


//click clear
WORK_ImageServers.prototype.clickClear=function(){
	return function() {
		var divSelect=document.getElementById("imageserversselect");
		divSelect.selectedIndex=0;
		ZIMA.clickSelectA();
	};
};


//click delete
WORK_ImageServers.prototype.clickDelete=function(){
	return function() {
		var divSelect=document.getElementById("imageserversselect");
		var iID=divSelect[divSelect.selectedIndex].value;
		iID=ZA.getXML(ZA.sXMLProducts,"imageserver_"+iID+"/imageserver_id");
		if (!iID) {
			return;
		}
		ZA.callAjax("_app/?imageserverdelete=1"
			+"&id="+iID
			,function(xml){ZIMA.createPage(xml);});
	};
};


		WORK_ImageServers._iInited=1;
	}
};//END function WORK_ImageServers()





var ZA=new WORK_App();
var ZCAT=new WORK_Categories();
var ZPRO=new WORK_Products();
var ZUSE=new WORK_Users();
var ZPAC=new WORK_Packs();
var ZIMA=new WORK_ImageServers();
var ZCAR=new WORK_Cards();
var ZCQU=new WORK_CardsQuality();


$(document).ready (function(){ ZA.createBody();});
