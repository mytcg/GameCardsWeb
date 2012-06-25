function COMPONENT_Leaderboard()
{
	this.temp=0;
	this.sXML='';


    if (typeof COMPONENT_Leaderboard._iInited=="undefined"){
    	
	
	COMPONENT_Leaderboard.prototype.init=function()
	{

		ZA.createWindowPopup(9988,"leaderboard",516,350,1,1);
		var divData=document.getElementById("window_9988");

		var divContainer=ZA.createDiv(divData,"container");
		$(divContainer).css({
			overflow:'hidden',
			height:305,
			width:500
		});
		var divLeaderboards = ZA.createDiv(divContainer);
		$(divLeaderboards).css({
			top:10,
			left:160,
			width:326,
			height:252,
			background:"#999",
			border:"2px solid #999",
			"-moz-border-radius":"5px"
		});
		var divButtons = ZA.createDiv(divContainer);
		$(divButtons).css({
			width:140,
			top:10,
			left:10,
		});
		
		ZA.callAjax("_app/leaderboard/?init=1",function(xml)
		{
			CLB.sXML = xml;
			var leaderBoardsCount = ZA.getXML(xml,"count");
			var i;
			for(i=0; i<leaderBoardsCount; i++){
				var divLeaderboard = ZA.createDiv(divLeaderboards,"leaderboardholder",i.toString());
				$(divLeaderboard).css({
					position:"relative"
				}).hide();
				var button = ZA.createDiv(divButtons,"leaderboardlink cmdButton",i.toString());
				$(button).css({
					position:"relative",
					marginBottom:11,
					width:120,
					"-moz-user-select":"none"
				});
				$(button).html(ZA.getXML(xml,"leaderboards/leaderboard_"+i+"/description"));
				
				//leaderboard table
				var leadersCount = ZA.getXML(xml,"leaderboards/leaderboard_"+i+"/count");
				var tablebody = '';
				var rowclass;
				var numberone;
				var username;
				for(var k=0; k<leadersCount; k++){
					rowclass = (k%2) ? 'even' : 'odd';
					numberone = (k==0) ? ' numberone' : '';
					username = ZA.getXML(xml,"leaderboards/leaderboard_"+i+"/leaders/leader_"+k+"/username");
					username = (username.indexOf("@")>-1) ? username.substring(0,username.indexOf("@")) : username;
					tablebody+= '<tr class="'+rowclass+numberone+'">';
					tablebody+= '<td>'+(k+1).toString()+'</td>';
					tablebody+= '<td class="name">'+username+'</td>';
					tablebody+= '<td class="score">'+ZA.getXML(xml,"leaderboards/leaderboard_"+i+"/leaders/leader_"+k+"/value")+'</td>';
					tablebody+= '</tr>';
				}
				var table = ZA.createDiv(divLeaderboard);
				$(table).css({
					position:"relative",
					width:"100%"
				});
				$(table).html(
					'<table class="grid" cellspacing="0" cellpadding="0">'+
						'<thead>'+
							'<tr style="background:#999;color:#FFF;">'+
								'<th width="12%" style="padding-top:3px;">Rank</th>'+
								'<th style="padding-top:3px;">Name</th>'+
								'<th width="18%" style="padding-top:3px;">Score</th>'+
							'</tr>'+
						'</thead>'+
						'<tbody>'+
							tablebody+
						'</tbody>'+
					'</table>'
				);
				
			}
			$(".leaderboardlink").click(function(){
				CLB.showLeaderboard($(this).attr('id'));
			});
			//show last leaderboard by default
			CLB.showLeaderboard(0);
		});

		var divButton = ZA.createDiv(divContainer,"cmdButton");
		$(divButton).css({
			bottom:10,
			right:10
		});
		$(divButton).html('Close');
		$(divButton).click(function(){
			CLB.clickCloseLeaderboard();
		});
	};
	
	
	COMPONENT_Leaderboard.prototype.showLeaderboard=function(id){
		var obj = $(".leaderboardlink[id='"+id+"']");
		if(!obj.hasClass('leaderboardlinkSelected')){
			$(".leaderboardlink").removeClass('leaderboardlinkSelected');
			obj.addClass('leaderboardlinkSelected');
			$(".leaderboardholder").hide();
			$(".leaderboardholder[id='"+id+"']").show('blind',500);
		}
	};
	
	
	COMPONENT_Leaderboard.prototype.clickCloseLeaderboard=function(){
		var divBody=document.getElementsByTagName("body")[0];
		var divCloak=document.getElementById("bodycloak_9988");
		var divLeaderboard=document.getElementById("windowcontainer_9988");
		var divData=document.getElementById("window_9988");
		if (divLeaderboard) {
			divBody.removeChild(divLeaderboard);
			divBody.removeChild(divData);
		}
		if(divCloak) {
			divBody.removeChild(divCloak);
		}
	};
      

	}
	
};


var CLB = new COMPONENT_Leaderboard();
