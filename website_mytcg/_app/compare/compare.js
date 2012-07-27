function WORK_Compare()
{
	this.iComponentNo=9;
	this.sURL="_app/compare/";
	this.sXML=null;
	this.divData=null;
	this.cards=[];
	this.active={'left':false,'right':false};
	this.scrollPos=[['left',0],['right',0]];
	this.sides={0:'left',1:'right'};
	this.other={'left':'right','right':'left'};
	this.statscount=0;
	
	
    if (typeof WORK_Compare._iInited=="undefined"){
    
	
	WORK_Compare.prototype.init=function(xml)
	{
		ZC.sXML = xml;
		ZC.divData = document.getElementById("window_"+ZC.iComponentNo);
		$(ZC.divData).empty();
		ZC.buildScreen();
	};
	
	
	WORK_Compare.prototype.buildScreen=function()
	{
		var divContainer = ZA.createDiv(ZC.divData);
		$(divContainer).css({
			width:"100%",
			height:"100%"
		});
		
		//----------------------------------------
		//left panel
		//----------------------------------------
		var divLeftPanel = ZA.createDiv(divContainer,"","leftPanel");
		$(divLeftPanel).css({
			background:"url(_site/line.gif) repeat",
			top:15,
			left:15,
			width:465,
			height:465,
			border:"2px solid #999",
			"-moz-border-radius":"5px"
		});
		var leftCardsHolder = ZA.createDiv(divLeftPanel,"","leftCardsHolder");
		$(leftCardsHolder).css({
			width:465,
			height:465
		});
		var leftInner = ZA.createDiv(leftCardsHolder,"compareCards","leftCards");
		$(leftInner).css({
			position:"relative",
			width:445,
			padding:10,
			paddingBottom:0
		});
		//left card
		var leftCardHolder = ZA.createDiv(divLeftPanel,"","leftCard");
		$(leftCardHolder).css({
			width:250,
			height:350,
			background:"url(_site/back.jpg) no-repeat center center",
			top:55,
			right:35,
			"z-index":998
		}).hide();
		$(leftCardHolder).attr('alt','-1');
		var back = ZA.createDiv(leftCardHolder,"","leftBack","img");
		$(back).css({
			width:250,
			height:350,
			cursor:"pointer"
		}).hide();
		$(back).attr('alt','left');
		var front = ZA.createDiv(leftCardHolder,"","leftFront","img");
		$(front).css({
			width:250,
			height:350,
			cursor:"pointer"
		});
		$(front).attr('alt','left');
		$("#leftBack, #leftFront").attr('title','Flip').click(function(){
			ZC.flipCards();
		});
		var info = ZA.createDiv(leftCardHolder);
		$(info).css({
			color:"#000",
			left:-160,
			width:135,
			height:300,
			top:15,
			textAlign:"left"
		});
		$(info).html(
			'<p class="description" style="margin-top:0px;font-size:16px;font-weight:bold;line-height:120%;text-shadow: 1px 1px 1px #DBDBDB;">CARD NAME</p>'+
			'<div style="top:50px;left:0px;">'+
				'<p>Quality:</p>'+
				'<p class="quality" style="font-size:16px;font-weight:bold;margin-top:5px;">X</p>'+
				'<p>Ranking:</p>'+
				'<p class="ranking" style="font-size:16px;font-weight:bold;margin-top:5px;">X</p>'+
				'<p >Average Ranking:</p>'+
				'<p class="avgranking" style="font-size:16px;font-weight:bold;margin-top:5px;">X</p>'+
				'<p>Value:</p>'+
				'<p class="value" style="font-size:16px;font-weight:bold;margin-top:5px;">X</p>'+
				'<p style="margin-bottom:0px;font-weight:bold;">Found in Shop:</p>'+
				'<p class="packs" style="margin-top:0px;">X</p>'+
			'</div>'
		);
		//button
		var button = ZA.createDiv(divContainer,"cmdButton","leftButton");
		$(button).css({
			//left:175,
			left:17,
			bottom:15,
			width:125
		});
		$(button).click(function(){
			ZC.closeCard('left');
		});
		$(button).html('Choose another card').hide();
		//label
		var label = ZA.createDiv(divContainer,"","leftLabel");
		$(label).css({
			left:170,
			bottom:15,
			width:125,
			padding:5
		});
		$(label).html('Choose a card').hide();
		//indicator blocks
		var blocksholder = ZA.createDiv(leftCardHolder,"compareBlocks","left");
		$(blocksholder).css({
			width:20,
			height:300,
			right:-20,
			top:0
		});
		ZC.statscount = parseInt(ZA.getXML(ZC.sXML,"allstats/statscount"));
		for(var i=0; i<ZC.statscount; i++){
			var iTop = ZA.getXML(ZC.sXML,"allstats/top_"+i);
			var indicator = ZA.createDiv(blocksholder,"statIndicator",i.toString());
			$(indicator).css('top',iTop+'px');
			var indicator = ZA.createDiv(blocksholder,"statSelectIndicator",i.toString());
			$(indicator).css({top:iTop+'px',cursor:"default"});
			var indicator = ZA.createDiv(blocksholder,"statSelectedIndicator",i.toString());
			$(indicator).css('top',iTop+'px');
		}
		//score
		var score = ZA.createDiv(divContainer,"compareScore","left");
		$(score).css({
			bottom:20,
			left:277,
			height:60,
			width:90,
			background:"url(_site/all.png) -205px -161px no-repeat"
		}).hide();
		$(score).html('<div id="leftScore" style="color:#97CC29;font-size:36px;font-weight:bold;padding:22px;position:relative;">X</div>');
		
		//----------------------------------------
		//right panel
		//----------------------------------------
		var divRightPanel = ZA.createDiv(divContainer,"","rightPanel");
		$(divRightPanel).css({
			background:"url(_site/line.gif) repeat",
			top:15,
			right:15,
			width:465,
			height:465,
			border:"2px solid #999",
			"-moz-border-radius":"5px"
		});
		var rightCardsHolder = ZA.createDiv(divRightPanel,"","rightCardsHolder");
		$(rightCardsHolder).css({
			width:465,
			height:465
		});
		var rightInner = ZA.createDiv(rightCardsHolder,"compareCards","rightCards");
		$(rightInner).css({
			position:"relative",
			width:445,
			padding:10,
			paddingBottom:0
		});
		//right card
		var rightCardHolder = ZA.createDiv(divRightPanel,"","rightCard");
		$(rightCardHolder).css({
			width:250,
			height:350,
			background:"url(_site/back.jpg) no-repeat right",
			top:55,
			left:35,
			"z-index":998
		}).hide();
		$(rightCardHolder).attr('alt','-1');
		var back = ZA.createDiv(rightCardHolder,"","rightBack","img");
		$(back).css({
			width:250,
			height:350,
			cursor:"pointer"
		}).hide();
		var front = ZA.createDiv(rightCardHolder,"","rightFront","img");
		$(front).css({
			width:250,
			height:350,
			cursor:"pointer"
		});
		$("#rightBack, #rightFront").attr('title','Flip').click(function(){
			ZC.flipCards();
		});
		var info = ZA.createDiv(rightCardHolder);
		$(info).css({
			color:"#000",
			left:270,
			width:135,
			height:300,
			top:15,
			textAlign:"right"
		});
		$(info).html(
			'<p class="description" style="margin-top:0px;font-size:16px;font-weight:bold;line-height:120%;text-shadow: 1px 1px 1px #FFF;">CARD NAME</p>'+
			'<div style="top:50px;right:0px;">'+
				'<p>Quality:</p>'+
				'<p class="quality" style="font-size:16px;font-weight:bold;margin-top:5px;">X</p>'+
				'<p>Ranking:</p>'+
				'<p class="ranking" style="font-size:16px;font-weight:bold;margin-top:5px;">X</p>'+
				'<p>Average Ranking:</p>'+
				'<p class="avgranking" style="font-size:16px;font-weight:bold;margin-top:5px;">X</p>'+
				'<p>Value:</p>'+
				'<p class="value" style="font-size:16px;font-weight:bold;margin-top:5px;">X</p>'+
				'<p style="margin-bottom:0px;font-weight:bold;">Found in Shop:</p>'+
				'<p class="packs" style="margin-top:0px;">X</p>'+
			'</div>'
		);
		//button
		var button = ZA.createDiv(divContainer,"cmdButton","rightButton");
		$(button).css({
			//right:175,
			right:17,
			bottom:15,
			width:125
		});
		$(button).click(function(){
			ZC.closeCard('right');
		});
		$(button).html('Choose another card').hide();
		//label
		var label = ZA.createDiv(divContainer,"","rightLabel");
		$(label).css({
			right:170,
			bottom:15,
			width:125,
			padding:5
		});
		$(label).html('Choose a card').hide();
		//indicator blocks
		var blocksholder = ZA.createDiv(rightCardHolder,"compareBlocks","right");
		$(blocksholder).css({
			width:20,
			height:300,
			left:-20,
			top:0
		});
		for(var i=0; i<ZC.statscount; i++){
			var iTop = ZA.getXML(ZC.sXML,"allstats/top_"+i);
			var indicator = ZA.createDiv(blocksholder,"statIndicator",i.toString());
			$(indicator).css('top',iTop+'px');
			var indicator = ZA.createDiv(blocksholder,"statSelectIndicator",i.toString());
			$(indicator).css({top:iTop+'px',cursor:"default"});
			var indicator = ZA.createDiv(blocksholder,"statSelectedIndicator",i.toString());
			$(indicator).css('top',iTop+'px');
		}
		//score
		var score = ZA.createDiv(divContainer,"compareScore","right");
		$(score).css({
			bottom:20,
			right:277,
			height:60,
			width:90,
			background:"url(_site/all.png) -205px -161px no-repeat"
		}).hide();
		$(score).html('<div id="rightScore" style="color:#97CC29;font-size:36px;font-weight:bold;padding:22px;position:relative;">X</div>');
		
		//swap cards button
		var swap = ZA.createDiv(divContainer,"","cmdSwap");
		$(swap).css({
			bottom:10,
			left:460,
			width:76,
			height:59,
			background:"url(_site/all.png) no-repeat -731px -220px",
			cursor:"pointer"
		});
		$(swap).hover(function(){
			$(this).css({
				"background-position":"-650px -220px",
				bottom:9,
				left:461
			});
		},function(){
			$(this).css({
				"background-position":"-731px -220px",
				bottom:10,
				left:460
			});
		});
		$(swap).attr('title','Swap');
		$(swap).click(function(){
			ZC.swapPanels();
		}).hide();
		
		//--------------------------------------------------------------------
		//all card thumbnails
		//--------------------------------------------------------------------
		var count = parseInt(ZA.getXML(ZC.sXML,"count"));
		for(var i=0; i<count; i++){
			var description = ZA.getXML(ZC.sXML,"cards/card_"+i+"/description");
			var image = ZA.getXML(ZC.sXML,"cards/card_"+i+"/path")+'cards/'+ZA.getXML(ZC.sXML,"cards/card_"+i+"/image");
			var quality = ZA.getXML(ZC.sXML,"cards/card_"+i+"/quality");
			var ranking = ZA.getXML(ZC.sXML,"cards/card_"+i+"/ranking");
			var avgranking = ZA.getXML(ZC.sXML,"cards/card_"+i+"/avgranking");
			var value = ZA.getXML(ZC.sXML,"cards/card_"+i+"/value");
			var packs = ZA.getXML(ZC.sXML,"cards/card_"+i+"/packs");
			var possess = ZA.getXML(ZC.sXML,"cards/card_"+i+"/possess");
			var cardid = ZA.getXML(ZC.sXML,"cards/card_"+i+"/card_id");
			ZC.cards[cardid] = i.toString();
			//left panel
			var thumb = ZA.createDiv(leftInner,"cardThumb",i.toString());
			$(thumb).html(
				'<img src="'+image+'_web.jpg" alt="'+description+'" />'+
				'<div class="cardname">'+description+'</div>'+
				'<input type="hidden" class="quality" value="'+quality+'" />'+
				'<input type="hidden" class="ranking" value="'+ranking+'" />'+
				'<input type="hidden" class="avgranking" value="'+avgranking+'" />'+
				'<input type="hidden" class="value" value="'+value+'" />'+
				'<input type="hidden" class="packs" value="'+packs+'" />'
			);
			$(thumb).attr('title',description);
			if(possess=='0'){
				$(thumb).addClass('noclick');
			}
			//right panel
			var thumb = ZA.createDiv(rightInner,"cardThumb",i.toString());
			$(thumb).html(
				'<img src="'+image+'_web.jpg" alt="'+description+'" />'+
				'<div class="cardname">'+description+'</div>'+
				'<input type="hidden" class="quality" value="'+quality+'" />'+
				'<input type="hidden" class="ranking" value="'+ranking+'" />'+
				'<input type="hidden" class="avgranking" value="'+avgranking+'" />'+
				'<input type="hidden" class="value" value="'+value+'" />'+
				'<input type="hidden" class="packs" value="'+packs+'" />'
			);
			$(thumb).attr('title',description);
			if(possess=='0'){
				$(thumb).addClass('noclick');
			}
		}
		$(leftInner).find(".cardThumb").click(function(){
			if(!$(this).hasClass('noclick')){
				ZC.openCard('left',$(this).attr('id'));
			}
		});
		$(rightInner).find(".cardThumb").click(function(){
			if(!$(this).hasClass('noclick')){
				ZC.openCard('right',$(this).attr('id'));
			}
		});
		var div = ZA.createDiv(leftInner);
		$(div).css({
			position:"relative",
			clear:"left"
		});
		var div = ZA.createDiv(rightInner);
		$(div).css({
			position:"relative",
			clear:"left"
		});
		//initialise the scrollbar
		$("#leftCardsHolder").jScrollPane({enableKeyboardNavigation:false});
		//initialise the scrollbar
		$("#rightCardsHolder").jScrollPane({enableKeyboardNavigation:false});
		
		//left cloak
		var cloak = ZA.createDiv(divLeftPanel,"","leftCloak");
		$(cloak).css({
			width:"100%",
			height:"100%",
			top:0,
			background:"#999",
			opacity:0.75
		});
		var btn = ZA.createDiv(divLeftPanel,"cmdButton","leftHide");
		$(btn).css({
			width:100,
			top:230,
			left:170
		});
		$(btn).html('Choose a card');
		$(btn).click(function(){
			$(this).hide('fast',function(){
				$("#leftCloak").hide('fast');
			});
			$("#leftLabel").show('fast');
			//initialise the scrollbar
			$("#leftCardsHolder").jScrollPane({enableKeyboardNavigation:false});
			ZC.active['left'] = true;
			ZC.checkSwap();
		});
		
		//right cloak
		var cloak = ZA.createDiv(divRightPanel,"","rightCloak");
		$(cloak).css({
			width:"100%",
			height:"100%",
			top:0,
			background:"#999",
			opacity:0.75
		});
		var btn = ZA.createDiv(divRightPanel,"cmdButton","rightHide");
		$(btn).css({
			width:100,
			top:230,
			left:170
		});
		$(btn).html('Choose a card');
		$(btn).click(function(){
			$(this).hide('fast',function(){
				$("#rightCloak").hide('fast');
			});
			$("#rightLabel").show('fast');
			//initialise the scrollbar
			$("#rightCardsHolder").jScrollPane({enableKeyboardNavigation:false});
			ZC.active['right'] = true;
			ZC.checkSwap();
		});
	};
	
	
	WORK_Compare.prototype.loadCard=function(cardid)
	{
		//load and show left card
		var id = ZC.cards[cardid];
		var panel = 'left';
		//set the card images and info
		$("#"+panel+"Card").attr('alt',id);
		$("#"+panel+"Front").attr('src','');
		$("#"+panel+"Back").attr('src','');
		ZC.setInfo(panel,id);
		//show and hide elements
		$("#"+panel+"Hide").hide();
		$("#"+panel+"Cloak").hide();
		$("#"+panel+"CardsHolder").hide();
		$("#"+panel+"Card").show();
		$("#"+panel+"Label").hide();
		$("#"+panel+"Button").show();
		if($("#"+ZC.other[panel]+"Card").attr('alt') != '-1'){
			if($("#"+ZC.other[panel]+"Back").css('display')!='none'){
				$("#"+panel+"Front").hide();
				$("#"+panel+"Back").show();
			}
		}
		ZC.checkSwap();
		ZC.checkScores();
		ZC.scrollPos[panel] = 0;
		ZC.active[panel] = true;
	}
	
	
	WORK_Compare.prototype.resetPanel=function(panel)
	{
		var panels = [];
		if(typeof(panel)=="undefined"){
			//reset both panels
			panels.push('left');
			panels.push('right');
			this.active['left']=false;
			this.active['right']=false;
			
		}
		else{
			panels.push(panel);
			this.active[panel]=false;
		}
		alert(panels);
		for(var i=0; i<panels.length; i++){
			panel = panels[i];
			$("#"+panel+"Card").attr('alt','-1').hide();
			$("#"+panel+"CardsHolder").show();
			$("#"+panel+"Button").hide();
			$("#"+panel+"Label").hide();
			$("#"+panel+"Cloak").show();
			$("#"+panel+"Hide").show();
			ZC.scrollPos[panel] = 0;
			$("#"+panel+"CardsHolder").jScrollPane({enableKeyboardNavigation:false});
			$("#"+panel+"CardsHolder").data('jsp').scrollToY(ZC.scrollPos[panel]);
		}
		
		$("#cmdSwap").hide();
		$(".compareScore").hide();
		
	};
	
	
	WORK_Compare.prototype.swapPanels=function()
	{
		var leftid = $("#leftCard").attr('alt');
		var rightid = $("#rightCard").attr('alt');
		var panel;
		
		//swap right panel to left
		panel = 'left';
		$("#"+panel+"Front").attr('src','');
		$("#"+panel+"Back").attr('src','');
		if(rightid == '-1'){
			//other panel is showing all cards
			//therefore this panel is showing a card
			$("#"+panel+"Card").hide();
			$("#"+panel+"CardsHolder").show();
			$("#"+panel+"Button").hide();
			$("#"+panel+"Label").show();
		}
		else{
			//other panel is showing a card
			if(leftid == '-1'){
				//this panel is not showing a card
				ZC.scrollPos[panel] = Math.abs(parseInt($("#"+panel+"Panel").find('.jspPane').css('top'),10));
				$("#"+panel+"CardsHolder").hide();
				ZC.setInfo(panel,rightid,true);
				$("#"+panel+"Card").show();
				$("#"+panel+"Label").hide();
				$("#"+panel+"Button").show();
			}
			else{
				//this panel is also showing a card
				ZC.setInfo(panel,rightid,true);
			}
		}
		
		//swap left panel to right
		panel = 'right';
		$("#"+panel+"Front").attr('src','');
		$("#"+panel+"Back").attr('src','');
		if(leftid == '-1'){
			//other panel is showing all cards
			//therefore this panel is showing a card
			$("#"+panel+"Card").hide();
			$("#"+panel+"CardsHolder").show();
			$("#"+panel+"Button").hide();
			$("#"+panel+"Label").show();
		}
		else{
			//other panel is showing a card
			if(rightid == '-1'){
				//this panel is not showing a card
				ZC.scrollPos[panel] = Math.abs(parseInt($("#"+panel+"Panel").find('.jspPane').css('top'),10));
				$("#"+panel+"CardsHolder").hide();
				ZC.setInfo(panel,leftid,true);
				$("#"+panel+"Card").show();
				$("#"+panel+"Label").hide();
				$("#"+panel+"Button").show();
			}
			else{
				//this panel is also showing a card
				ZC.setInfo(panel,leftid,true);
			}
		}
		
		//swap scores
		var id;
		var indicator;
		var tmp;
		var obj;
		$(".compareBlocks[id='left']").find("[class^='stat']").each(function(){
			id = $(this).attr('id');
			indicator = $(this).attr('class');
			obj = $(".compareBlocks[id='right']").find("."+indicator+"[id='"+id+"']");
			//swap stat values
			tmp = $(this).attr('alt');
			$(this).attr('alt',obj.attr('alt'));
			obj.attr('alt',tmp);
			//swap block states
			tmp = $(this).css('display');
			$(this).css('display',obj.css('display'));
			obj.css('display',tmp);
		});
		var tempScore = $("#leftScore").html();
		$("#leftScore").html($("#rightScore").html());
		$("#rightScore").html(tempScore);
		
		//swap scoller positions
		var tempPos = ZC.scrollPos['left'];
		ZC.scrollPos['left'] = ZC.scrollPos['right'];
		ZC.scrollPos['right'] = tempPos;
		
		//reinitialise the left scrollbar and set position
		$("#leftCardsHolder").jScrollPane({enableKeyboardNavigation:false});
		$("#leftCardsHolder").data('jsp').scrollToY(ZC.scrollPos['left']);
		//reinitialise the right scrollbar and set position
		$("#rightCardsHolder").jScrollPane({enableKeyboardNavigation:false});
		$("#rightCardsHolder").data('jsp').scrollToY(ZC.scrollPos['right']);
		
		//set current card status
		var tempid = leftid;
		leftid = rightid;
		rightid = tempid;
		$("#leftCard").attr('alt',leftid);
		$("#rightCard").attr('alt',rightid);
	};
	
	
	WORK_Compare.prototype.checkSwap=function()
	{
		if(ZC.active['left'] && ZC.active['right']){
			if($("#leftCard").is(':visible') || $("#rightCard").is(':visible')){
				$("#cmdSwap").show('fast');
			}
			else{
				$("#cmdSwap").hide('fast');
			}
		}
	};
	
	
	WORK_Compare.prototype.checkScores=function()
	{
		if($("#leftCard").css('display')!='none' && $("#rightCard").css('display')!='none'){
			ZC.compareScores();
			$(".compareScore").show('fast');
			if($("#leftBack").css('display')!='none' && $("#rightBack").css('display')!='none'){
				$(".compareBlocks").show();
			}
			else{
				$(".compareBlocks").hide();
			}
		}
		else{
			$(".compareBlocks").hide();
			$(".compareScore").hide('fast',function(){$(this).hide();});
		}
	};
	
	
	WORK_Compare.prototype.compareScores=function()
	{
		if($("#leftCard").attr('alt')!='-1' && $("#rightCard").attr('alt')!='-1'){
			var leftstat;
			var rightstat;
			var leftscore = 0;
			var rightscore = 0;
			$(".compareBlocks[id='left']").find(".statIndicator").each(function(){
				var id = $(this).attr('id');
				leftstat = parseFloat($(this).attr('alt'));
				rightstat = parseFloat($(".compareBlocks[id='right']").find(".statIndicator[id='"+id+"']").attr('alt'));
				if(leftstat == rightstat){
					//draw
					$(".compareBlocks[id='left']").find(".statIndicator[id='"+id+"']").hide();
					$(".compareBlocks[id='left']").find(".statSelectIndicator[id='"+id+"']").show();
					$(".compareBlocks[id='left']").find(".statSelectedIndicator[id='"+id+"']").hide();
					$(".compareBlocks[id='right']").find(".statIndicator[id='"+id+"']").hide();
					$(".compareBlocks[id='right']").find(".statSelectIndicator[id='"+id+"']").show();
					$(".compareBlocks[id='right']").find(".statSelectedIndicator[id='"+id+"']").hide();
				}
				else{
					if(leftstat > rightstat){
						leftscore++;
						$(".compareBlocks[id='left']").find(".statIndicator[id='"+id+"']").hide();
						$(".compareBlocks[id='left']").find(".statSelectIndicator[id='"+id+"']").hide();
						$(".compareBlocks[id='left']").find(".statSelectedIndicator[id='"+id+"']").show();
						$(".compareBlocks[id='right']").find(".statIndicator[id='"+id+"']").show();
						$(".compareBlocks[id='right']").find(".statSelectIndicator[id='"+id+"']").hide();
						$(".compareBlocks[id='right']").find(".statSelectedIndicator[id='"+id+"']").hide();
					}
					else{
						rightscore++;
						$(".compareBlocks[id='left']").find(".statIndicator[id='"+id+"']").show();
						$(".compareBlocks[id='left']").find(".statSelectIndicator[id='"+id+"']").hide();
						$(".compareBlocks[id='left']").find(".statSelectedIndicator[id='"+id+"']").hide();
						$(".compareBlocks[id='right']").find(".statIndicator[id='"+id+"']").hide();
						$(".compareBlocks[id='right']").find(".statSelectIndicator[id='"+id+"']").hide();
						$(".compareBlocks[id='right']").find(".statSelectedIndicator[id='"+id+"']").show();
					}
				}
			});
			$("#leftScore").html(leftscore.toString());
			$("#rightScore").html(rightscore.toString());
		}
	};
	
	
	WORK_Compare.prototype.flipCards=function(side)
	{
		var flipLeft = false;
		var flipRight = false;
		if(typeof(side)=='undefined'){
			flipLeft = true;
			flipRight = true;
		}
		else if(side=='left'){
			flipLeft = true;
		}
		else if(side=='right'){
			flipRight = true;
		}
		
		//left
		if($("#leftCard").is(":visible") && flipLeft){
			var index = i;
			if($("#leftFront").is(":visible")){
				$("#leftFront").animate({
					width:10
				},
				150,
				function(){
					$(this).hide();
					$("#leftBack").css({width:10}).show().animate({
						width:250
					},
					150,
					function(){
						ZC.checkScores();
					});
				});
			}
			else if($("#leftBack").is(":visible")){
				$(".compareBlocks").hide();
				$("#leftBack").animate({
					width:10
				},
				150,
				function(){
					$(this).hide();
					$("#leftFront").css({width:10}).show().animate({
						width:250
					},
					150,
					function(){
						ZC.checkScores();
					});
				});
			}
		}
		//right
		if($("#rightCard").is(":visible") && flipRight){
			var index = i;
			if($("#rightFront").is(":visible")){
				$("#rightFront").animate({
					width:10
				},
				150,
				function(){
					$(this).hide();
					$("#rightBack").css({width:10}).show().animate({
						width:250
					},
					150,
					function(){
						ZC.checkScores();
					});
				});
			}
			else if($("#rightBack").is(":visible")){
				$(".compareBlocks").hide();
				$("#rightBack").animate({
					width:10
				},
				150,
				function(){
					$(this).hide();
					$("#rightFront").css({width:10}).show().animate({
						width:250
					},
					150,
					function(){
						ZC.checkScores();
					});
				});
			}
		}
	};
	
	
	WORK_Compare.prototype.setInfo=function(panel,id,swap)
	{
		//display info
		$("#"+panel+"Back").attr('src',ZA.getXML(ZC.sXML,"cards/card_"+id+"/path")+'cards/'+ZA.getXML(ZC.sXML,"cards/card_"+id+"/image")+'_back..jpg');
		$("#"+panel+"Front").attr('src',ZA.getXML(ZC.sXML,"cards/card_"+id+"/path")+'cards/'+ZA.getXML(ZC.sXML,"cards/card_"+id+"/image")+'_front.jpg');
		$("#"+panel+"Card").find(".description").html($("#"+panel+"CardsHolder").find(".cardThumb[id='"+id+"']").find("img").attr('alt'));
		$("#"+panel+"Card").find(".quality").html($("#"+panel+"CardsHolder").find(".cardThumb[id='"+id+"']").find(".quality").val());
		$("#"+panel+"Card").find(".ranking").html($("#"+panel+"CardsHolder").find(".cardThumb[id='"+id+"']").find(".ranking").val());
		$("#"+panel+"Card").find(".avgranking").html($("#"+panel+"CardsHolder").find(".cardThumb[id='"+id+"']").find(".avgranking").val());
		$("#"+panel+"Card").find(".value").html($("#"+panel+"CardsHolder").find(".cardThumb[id='"+id+"']").find(".value").val()+' TCG');
		var packs = $("#"+panel+"CardsHolder").find(".cardThumb[id='"+id+"']").find(".packs").val();
		packs = packs.split(',');
		packs = packs.join('<br />');
		$("#"+panel+"Card").find(".packs").html(packs);
		if(typeof(swap)=="undefined"){
			if($("#"+ZC.other[panel]+"Back").is(":visible")){
				$("#"+panel+"Front").hide();
				$("#"+panel+"Back").css({width:250}).show();
			}
			else{
				$("#"+panel+"Back").hide();
				$("#"+panel+"Front").css({width:250}).show();
			}
			//stat info
			var statvalue;
			for(var i=0; i<ZC.statscount; i++){
				statvalue = ZA.getXML(ZC.sXML,"cards/card_"+id+"/stats/stat_"+i);
				$(".compareBlocks[id='"+panel+"']").find(".statIndicator[id='"+i+"']").attr('alt',statvalue).hide();
				$(".compareBlocks[id='"+panel+"']").find(".statSelectIndicator[id='"+i+"']").attr('alt',statvalue).hide();
				$(".compareBlocks[id='"+panel+"']").find(".statSelectedIndicator[id='"+i+"']").attr('alt',statvalue).hide();
			}
		}
	};
	
	
	WORK_Compare.prototype.openCard=function(panel,id)
	{
		$("#"+panel+"Card").attr('alt',id);
		//save current scroller position
		ZC.scrollPos[panel] = Math.abs(parseInt($("#"+panel+"Panel").find('.jspPane').css('top'),10));
		//set the card images and info
		$("#"+panel+"Back").attr('src','');
		$("#"+panel+"Front").attr('src','');
		ZC.setInfo(panel,id);
		//show and hide elements
		$("#"+panel+"CardsHolder").hide('fast',function(){
			//reinitialise the scrollbar
			$("#"+panel+"CardsHolder").jScrollPane({enableKeyboardNavigation:false});
			$("#"+panel+"Card").show('fast',function(){
				ZC.checkSwap();
				ZC.checkScores();
			});
		});
		$("#"+panel+"Label").hide('fast',function(){
			$("#"+panel+"Button").show('fast');
		});
	};
	
	
	WORK_Compare.prototype.closeCard=function(panel)
	{
		$("#"+panel+"Card").attr('alt','-1');
		//hide scores
		$(".compareBlocks").hide();
		$(".compareScore").hide('fast');
		//show and hide elements
		$("#"+panel+"Card").hide('fast',function(){
			$("#"+panel+"Back").attr('src','');
			$("#"+panel+"Front").attr('src','');
			$("#"+panel+"CardsHolder").show('fast',function(){
				ZC.checkSwap();
				//reinitialise the scrollbar
				$("#"+panel+"CardsHolder").jScrollPane({enableKeyboardNavigation:false});
				//restore scroller position
				$("#"+panel+"CardsHolder").data('jsp').scrollToY(ZC.scrollPos[panel]);
			});
		});
		$("#"+panel+"Button").hide('fast',function(){
			$("#"+panel+"Label").show('fast');
		});
	};
	
	
	WORK_Compare.prototype.maximize=function()
	{
		if (ZA.aComponents[ZC.iComponentNo].iIsMaximized) {
			//maximze window
			alert('maxi');
		} else {
			//minimize window
		}
	};

	
	}
	WORK_Compare._iInited=1;
};


var ZC = new WORK_Compare();
ZA.callAjax(ZC.sURL+"?init=1",function(xml){ ZC.init(xml); });
