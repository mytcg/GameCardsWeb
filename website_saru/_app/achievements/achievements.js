function COMPONENT_Achievement()
{
	this.temp=0;
	this.sXML='';


    if (typeof COMPONENT_Achievement._iInited=="undefined")
    {
		COMPONENT_Achievement.prototype.init=function(sXML)
		{
			ZA.createWindowPopup(123,"achievements",800,800,1,1);
			var divData=document.getElementById("window_123");
			var divContainer=ZA.createDiv(divData,"container");
			$(divContainer).css({
				overflow:'hidden',
				height:750,
				width:785,
			});

			var divAchievements = ZA.createDiv(divContainer,"","achieve_holder");
			
			//title 
			var title = ZA.createDiv(divData,"txtBlue");
			$(title).css({
				marginBottom:10,
				paddingTop:10,
				width:"100%",
				height:20,
				fontSize:16,
				fontWeight:"bold",
				"text-shadow":"1px 1px 1px #000",
				position:"relative"
			})
			.html('Achievement List');
			
			var AchieveCount = parseInt(ZA.getXML(sXML,"count"));
			// var AchieveID = ZA.getXML(sXML,"achie/achi_1/name");
			var iCount=0;
			// var title = ZA.createDiv(divData,"achieveTitle");
			// $(title).html(AchieveID);
			if(AchieveCount > 0)
			{
				for (iCount=0; iCount < AchieveCount; iCount++)
				{
					var AchieveID = parseInt(ZA.getXML(sXML,"achie/achi_"+iCount+"/id"));
					// if (AchieveID)
					// {
						var divAchievementsCon = ZA.createDiv(divAchievements,"","achieve_con");
						var progress = ZA.getXML(sXML,"achie/achi_"+iCount+"/subachi_"+iCount+"/progress");
						var target = ZA.getXML(sXML,"achie/achi_"+iCount+"/subachi_"+iCount+"/target");
						
						var divAuctionDesc = ZA.createDiv(divAchievementsCon,"","","div");
						$(divAuctionDesc).css({
							fontSize:12,
							textAlign:"right",
							height:12,
							position:"relative",
						});
						$(divAuctionDesc).html('<b>'+progress+'/'+target+'</b>')
						
						var divAuctionImage=ZA.createDiv(divAchievementsCon,"","achievement_"+iCount);
						$(divAuctionImage).css({
							backgroundImage:"url("+ZA.getXML(sXML,"achie/achi_"+iCount+"/incomplete_image")+")",
							backgroundRepeat:"no-repeat",
							width:160,
							height:140,
							marginLeft: 25,
			    			marginTop: 10,
			    			position:"relative",
						});
						
						var divAuctionTitle = ZA.createDiv(divAchievementsCon,"txtGreen","","div");
						$(divAuctionTitle).css({
							fontSize:12,
							textAlign:"center",
							height:12,
							position:"relative",
						});
						$(divAuctionTitle).html('<b>'+ZA.getXML(sXML,"achie/achi_"+iCount+"/name")+'</b>');
						
						var divAuctionDesc = ZA.createDiv(divAchievementsCon,"","","div");
						$(divAuctionDesc).css({
							fontSize:12,
							textAlign:"center",
							height:12,
							position:"relative",
						});
						$(divAuctionDesc).html('<b>'+ZA.getXML(sXML,"achie/achi_"+iCount+"/description")+'</b>');
					// }
				}
	 		}
			else
			{
			 var noLog = ZA.createDiv(divAchievements);
			 $(noLog).html('No Logs');
			}
			
			var divButton = ZA.createDiv(divData,"cmdButton");
			$(divButton).css({
				bottom:5,
				right:15,
			});
			
			//reset vertical scrollbar
			$("#achieve_holder").css({
			}).jScrollPane({enableKeyboardNavigation:false});
			$(divButton).html('Close');
			$(divButton).click(function(){
				ART.clickCloseAchievement();
			});
		};
	};
	
	COMPONENT_Achievement.prototype.clickCloseAchievement=function(){
		var divBody=document.getElementsByTagName("body")[0];
		var divCloak=document.getElementById("bodycloak_123");
		var divNotification=document.getElementById("windowcontainer_123");
		var divData=document.getElementById("window_123");
		if (divNotification) {
			divBody.removeChild(divNotification);
			divBody.removeChild(divData);
		}
		if(divCloak) {
			divBody.removeChild(divCloak);
		}
	};
};

var ART = new COMPONENT_Achievement();
