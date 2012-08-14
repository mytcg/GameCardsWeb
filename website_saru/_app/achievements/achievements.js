function COMPONENT_Achievement()
{
	this.temp=0;
	this.sXML='';


    if (typeof COMPONENT_Achievement._iInited=="undefined")
    {
		COMPONENT_Achievement.prototype.init=function(sXML)
		{
			ZA.createWindowPopup(123,"achievements",800,765,1,1);
			var divData=document.getElementById("window_123");
			var divContainer=ZA.createDiv(divData,"container");
			$(divContainer).css({
				overflow:'hidden',
				height:713,
				width:785,
			});

			var divAchievements = ZA.createDiv(divContainer,"","achieve_holder");
			$(divAchievements).css({
				// width:760,
				// height:377,
			});
			
			//title
			var title = ZA.createDiv(divAchievements,"achieveTitle");
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
			var iCount=0;
			if(AchieveCount > 0){
				
				while (iCount < AchieveCount)
				{
					var divAchievementsCon = ZA.createDiv(divAchievements,"achieve_con");
					$(divAchievementsCon).css({
						"float":"left",
						marginLeft:15,
						marginBottom:15,
						width:205,
						position:"relative",
						height:210,
						border:"1px solid #333",
					});
					// var some = ZA.getXML(sXML,"achieve_"+iCount+"/name");
					// $(divAchievementsCon).html(some);
					
					var divAuctionImage=ZA.createDiv(divAchievementsCon,"","achievement_"+iCount);
					$(divAuctionImage).css({
						backgroundImage:"url("+ZA.getXML(sXML,"achieve_"+iCount+"/incomplete_image")+")",
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
					$(divAuctionTitle).html('<b>'+ZA.getXML(sXML,"achieve_"+iCount+"/name")+'</b>');
					var divAuctionDesc = ZA.createDiv(divAchievementsCon,"","","div");
					$(divAuctionDesc).css({
						fontSize:12,
						textAlign:"center",
						height:12,
						position:"relative",
					});
					$(divAuctionDesc).html('<b>'+ZA.getXML(sXML,"achieve_"+iCount+"/description")+'</b>');
				iCount++;
				}
	 		}
			else
			{
			 var noLog = ZA.createDiv(divMain);
			 $(noLog).html('No Logs');
			}
			
			var divButton = ZA.createDiv(divContainer,"cmdButton");
			$(divButton).css({
				bottom:1,
				right:15,
			});
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
