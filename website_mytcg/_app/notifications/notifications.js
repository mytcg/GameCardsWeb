function COMPONENT_Notification()
{
	this.temp=0;
	this.sXML='';


    if (typeof COMPONENT_Notification._iInited=="undefined")
    {
		COMPONENT_Notification.prototype.init=function(sXML)
		{
			ZA.createWindowPopup(9110,"notifications",800,460,1,0);
			var divData=document.getElementById("window_9110");
			var divContainer=ZA.createDiv(divData,"container");
			$(divContainer).css({
				overflow:'hidden',
				height:415,
				width:784,
			});
			
			var divNotifications = ZA.createDiv(divContainer);
			$(divNotifications).css({
				top:10,
				left:10,
				width:760,
				height:362,
				background:"#999",
			});
			
			//title
			var title = ZA.createDiv(divNotifications);
			$(title).css({
				top:10,
				left:10,
				fontSize:16,
				fontWeight:"bold",
				"text-shadow":"1px 1px 1px #FFF"
			})
			.html('Notification Logs');
			 
			 var logCount = parseInt(ZA.getXML(sXML,"count"));
			 if(logCount > 0){
			 		
			 	  var divMain = ZA.createDiv(divNotifications,"","");
			 	  $(divMain).css({
			 	  	 top:20,
					 position:"relative",
					 width:"100%",
					 overflow:"hidden",
					});
			 	  var tblGrid = ZA.createDiv(divMain,"grid transactions","","table");
			 	  var tblRow = ZA.createDiv(tblGrid,"","","tr");
			 	  var tblCell = ZA.createDiv(tblRow,"","","td");
			 	  $(tblCell).css({
					color:'#FFF',
					fontWeight:"bold",
				  }).html("Date");
			 	  var tblCell = ZA.createDiv(tblRow,"","","td");
			 	  $(tblCell).css({
			 	  	color:'#FFF',
					fontWeight:"bold",
				  }).html("Description");
			 	
			 	for(var i=0; i<logCount; i++){
					var rowclass = (i%2) ? 'even' : 'odd';
 					var tblRow = ZA.createDiv(tblGrid,rowclass,"","tr");
 					$(tblRow).css({
						textAlign:"left",
				  	});
			 	  	var tblCell = ZA.createDiv(tblRow,"","","td");
			 	  	$(tblCell).html(ZA.getXML(sXML,"log_"+i+"/date"));
			 	  	var tblCell = ZA.createDiv(tblRow,"","","td");
			 	  	$(tblCell).html(ZA.getXML(sXML,"log_"+i+"/message"));
 					 
	 			}
	 		}
			 else
			 {
				 var noLog = ZA.createDiv(divMain);
				 $(noLog).html('No Logs');
			 }
			
			var divButton = ZA.createDiv(divContainer,"cmdButton");
			$(divButton).css({
				bottom:10,
				right:15,
			});
			$(divButton).html('Close');
			$(divButton).click(function(){
				CRT.clickCloseNotifications();
			});
			 
		};
	};
	
	COMPONENT_Notification.prototype.clickCloseNotifications=function(){
		var divBody=document.getElementsByTagName("body")[0];
		var divCloak=document.getElementById("bodycloak_9110");
		var divNotification=document.getElementById("windowcontainer_9110");
		var divData=document.getElementById("window_9110");
		if (divNotification) {
			divBody.removeChild(divNotification);
			divBody.removeChild(divData);
		}
		if(divCloak) {
			divBody.removeChild(divCloak);
		}
	};
      

};



var CRT = new COMPONENT_Notification();
