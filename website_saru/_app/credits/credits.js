function WORK_Credits()
{
	this.iComponentNo = 10;
	this.divData = 0;
	this.sURL = "_app/credits/";
	this.sXML = "";
	this.selection = "";

	if(typeof WORK_Credits._iInited=="undefined") {

	WORK_Credits.prototype.init=function()
	{
		ZCR.divData = document.getElementById("window_"+ZCR.iComponentNo);
		$(ZCR.divData).empty();
		ZCR.buildWindow();
		ZCR.setTransactionLogsTable(true);
   };
	
	WORK_Credits.prototype.buildWindow=function()
	{
		var container = ZA.createDiv(ZCR.divData,"creditContainer");
		$(container).css({
			width:741,
			height:"100%"
		});
		
		//user credits
		
		var divCredits = ZA.createDiv(container,"","transactionLogs");
		$(divCredits).css({
			bottom:0,
			left:0,
			height:300,
			width:"100%",
		});
		
		//tcg credits icon with splash
		// var icon = ZA.createDiv(divCredits);
		// $(icon).css({
			// background:"url(_site/tcg.png) no-repeat",
			// width:300,
			// height:200,
			// top:5,
			// left:320
		// });
		
		//current balance
		
		var title = ZA.createDiv(container,"currentTitle");
		$(title).html('Current Balance');
		var balance = ZA.createDiv(container,"currentCredits");
		
		var div = ZA.createDiv(balance,"creditAmount");
		$(div).html(ZA.sUserCredits+' <span class="txtBlue" style="font-size:30px"><br />Credits</span>');

		//transaction logs
		var divTransactions = ZA.createDiv(divCredits);
		$(divTransactions).css({
			bottom:10,
			width:"100%",
		});
			
			//title
			var title = ZA.createDiv(divTransactions);
			$(title).css({
				left:10,
				top:-28,
				fontSize:16,
				fontWeight:"bold",
				textAlign:"left",
				"text-shadow":"1px 1px 1px #FFF"
			})
			.html('Transaction Logs');
			
			var all = ZA.createDiv(divTransactions,"cmdButton");
			$(all).css({
				right:15,
				top:-30,
			})
			.html('Load ALL transactions')
			.click(function(){
				ZA.addLoader($(ZCR.divData));
				$(this).hide();
				ZCR.setTransactionLogsTable();
			});
			
			//transactions table
			var data = ZA.createDiv(divTransactions,"","divTransactionLogs");
			$(data).css({
				position:"relative",
				width:"100%",
				height:348,
				overflow:"hidden",
			});
			
		//payment gateways
		
		var checkCheckout = function()
		{
			var sPackage = $("input[name='package']:checked").val();
			var sMethod = $("input[name='method']:checked").attr('id');
			//auto select package/method according to selection
			if(ZCR.selection == 'package')
			{
				if(sPackage == '1')
				{
					if(typeof(sMethod) == "undefined" || sMethod == 'paypal')
					{
						$("input[name='method'][id='creditcard']").attr('checked',true);
					}
				}
				else
				{
					$("input[name='method'][id='creditcard']").attr('checked',true);
				}
			}
			else if(ZCR.selection == 'method')
			{
				if(sMethod == 'paypal')
				{
					$("input[name='package']").attr('checked',false);
					$("#cmdCheckout").removeClass('cmdButtonDisabled');
				}
				else if(sMethod == 'sms')
				{
					$("input[name='package'][value='1']").attr('checked',true);
				}
				else if(sMethod == 'creditcard')
				{
					if(typeof(sPackage) == "undefined")
					{
						$("input[name='package'][id='TCG5']").attr('checked',true);
					}
				}
			}
			$("#cmdCheckout").removeClass('cmdButtonDisabled');
		}
		
		var divGateways = ZA.createDiv(container,"creditsPayments","paymentGateway");

			var title = ZA.createDiv(divGateways);
			$(title).css({
				top:-28,
				left:0,
				fontSize:16,
				fontWeight:"bold",
				textAlign:"left",
				lineHeight:"120%",
				"text-shadow":"1px 1px 1px #FFF"
			})
			.html('Buy TCG credits');
			
			var divInner = ZA.createDiv(divGateways,"optionsBlock","creditPackages");
			$(divInner).css({
				height:250
			});
			
			//select package	
			var title = ZA.createDiv(divInner,"title");
			$(title).html('Select Package')
			.click(function(){
				if($(this).hasClass('selectable'))
				{
					$(this).removeClass('selectable');
					$("#paymentMethods").animate({height:22},250,function(){
						$("input[name='method']").attr('checked',false);
						$("#cmdCheckout").hide().addClass('cmdButtonDisabled');
						$("#cmdSelectPackage").show('fast');
					});
				}
			});
			
			var options = ZA.createDiv(divInner,"packageOptions");
				
				//350TCG
				var option = ZA.createDiv(options,"packageOption","1");
				$(option).html(
					'<table>'+
						'<tr>'+
							'<td width="20">'+
								'<input type="radio" name="package" value="1" id="TCG5" alt="36012" />'+
								'<input type="hidden" class="cost" value="5" />'+
								'<input type="hidden" class="credits" value="350" />'+
							'</td>'+
							'<td class="credits"><label for="TCG5">350 <span class="txtBlue" style="font-size:12px"><br />TCG</span></label></td>'+
							'<td><label for="TCG5">R5.00</label></td>'+
						'</tr>'+
					'</table>'
				);
				
				//700TCG
				var option = ZA.createDiv(options,"packageOption","2");
				$(option).html(
					'<table>'+
						'<tr>'+
							'<td width="20">'+
								'<input type="radio" name="package" value="2" id="TCG10" alt="38021" />'+
								'<input type="hidden" class="cost" value="10" />'+
								'<input type="hidden" class="credits" value="700" />'+
							'</td>'+
							'<td class="credits"><label for="TCG10">700 <span class="txtBlue" style="font-size:12px"><br />TCG</span></label></td>'+
							'<td><label for="TCG10">R10.00</label></td>'+
						'</tr>'+
					'</table>'
				);
				
				//1050TCG
				var option = ZA.createDiv(options,"packageOption","3");
				$(option)
				.html(
					'<table>'+
						'<tr>'+
							'<td width="20">'+
								'<input type="radio" name="package" value="3" id="TCG15" alt="39051" />'+
								'<input type="hidden" class="cost" value="15" />'+
								'<input type="hidden" class="credits" value="1050" />'+
							'</td>'+
							'<td class="credits"><label for="TCG15">1050 <span class="txtBlue" style="font-size:12px"><br />TCG</span></label></td>'+
							'<td><label for="TCG15">R15.00</label></td>'+
						'</tr>'+
					'</table>'
				);
			
			//event handler
			$("input[name='package']").bind("click",function(){
				ZCR.selection = 'package';
				checkCheckout();
			});
			
			//payment methods
			var title = ZA.createDiv(divInner,"title");
			$(title).html('Select Payment Method');
			
			var options = ZA.createDiv(divInner,"methodOptions");
				
				//sms shortcode
				/*var option = ZA.createDiv(options,"methodOption");
				$(option).html(
					'<table>'+
						'<tr>'+
							'<td width="20"><input type="radio" name="method" id="sms" val="1" alt="SMS" /></td>'+
							'<td width="85"><label for="sms">SMS</label></td>'+
							'<td><div style="position:relative;width:30px;height:25px;background:url(_site/all.png) -196px -236px no-repeat;"></div></td>'+
						'</tr>'+
					'</table>'+
					'<div id="smsMethodInfo" style="position:relative;padding:10px;display:none;">'+
						'SMS "<span id="keyword">X</span>" and your username "<span>" to <span id="shortcode">Y</span>'+
					'</div>'
				);*/
				
				//paypal
				var option = ZA.createDiv(options,"methodOption");
				$(option).html(
					'<table>'+
						'<tr>'+
							'<td width="20"><input type="radio" name="method" id="paypal" val="2" alt="PayPal" /></td>'+
							'<td width="85"><label for="paypal">PayPal</label></td>'+
							'<td><div style="position:relative;width:30px;height:25px;background:url(_site/all.png) -231px -236px no-repeat;"></div></td>'+
						'</tr>'+
					'</table>'
				);
				
				//credit card
				/*var option = ZA.createDiv(options,"methodOption");
				$(option)
				.html(
					'<table>'+
						'<tr>'+
							'<td width="20"><input type="radio" name="method" id="creditcard" val="3" alt="Credit Card" /></td>'+
							'<td width="85"><label for="creditcard">Credit Card</label></td>'+
							'<td><div style="position:relative;width:30px;height:25px;background:url(_site/all.png) -260px -236px no-repeat;"></div></td>'+
						'</tr>'+
					'</table>'
				);*/
				
				//event handler
				$("input[name='method']").bind("click change",function(){
					$("#cmdCheckout").removeClass('cmdButtonDisabled');
				});
			
			//event handler
			$("input[name='method']").bind("click change",function(){
				ZCR.selection = 'method';
				checkCheckout();
			});
			
			//check out button
			var button = ZA.createDiv(divInner,"cmdButton","cmdCheckout");
			$(button).css({
				width:90,
				left:"50%",
				marginLeft:-55,
				bottom:18
			})
			.addClass('cmdButtonDisabled')
			.html('<nobr>Check out</nobr>')
			.click(function(){
				if(!$(this).hasClass('cmdButtonDisabled'))
				{
					ZCR.openCheckoutWindow();
				}
			});
		
/*******************************************************************************
* DEV *

var testers = ['jacoh', 'steynviljoen', 'theoldman', 'superman'];
testers = testers.join(',');
var dev = testers.indexOf(ZA.sUsername.toLowerCase()+',');
if(dev < 0)
{
	$(divGateways).empty()
	.html('<div style="padding-top:190px;position:relative;">PAYMENT GATEWAY<br /><br /><span style="color:#fff;font-size:16px;font-weight:bold;">COMING SOON!!!</span></div>');
}

*******************************************************************************/
	};
	
	WORK_Credits.prototype.setTransactionLogsTable=function(limit)
	{
		if(typeof(limit)=="undefined")
		{
			limit = '0';
		}
		else
		{
			limit = '1';
		}
		//get transaction logs
		ZA.callAjax(ZCR.sURL+"?init=1&limit="+limit,function(xml)
		{
			var transactions = '<div style="width:100%;">';
			var logsCount = parseInt(ZA.getXML(xml,"count"));
			if(logsCount > 0)
			{
				var tablebody = '';
				for(var i=0; i<logsCount; i++)
				{
					var rowclass = (i%2) ? 'even' : 'odd';
					var signcol = (parseInt(ZA.getXML(xml,"log_"+i+"/amount")) > 0) ? '#000000' : '#CC0000';
					tablebody+=
					'<tr class="'+rowclass+'">'+
						'<td style="width:110px;text-align:right;padding-right:15px;">'+ZA.getXML(xml,"log_"+i+"/date")+'</td>'+
						'<td style="text-align:left;">'+ZA.getXML(xml,"log_"+i+"/message")+'</td>'+
						'<td style="width:60px;padding-right:15px;text-align:right;font-weight:bold;color:'+signcol+';">'+ZA.getXML(xml,"log_"+i+"/amount")+' TCG</td>'+
					'</tr>';
				}
								
				transactions+=
				'<div class="logHeader">'+
					'<table class="grid transactions" cellspacing="0" cellpadding="0">'+
						'<thead>'+
							'<tr>'+
								'<th style="width:110px;">Transaction Date</th>'+
								'<th>Description</th>'+
								'<th style="width:60px;">Amount</th>'+
							'</tr>'+
						'</thead>'+
					'</table>'+
				'</div>'+
				'<div style="position:relative;height:320px;width:100%;overflow:hidden;" id="logsHolder">'+
					'<table class="grid transactions" cellspacing="0" cellpadding="0">'+
						'<tbody style="border:1px solid green;">'+
							tablebody+
						'</tbody>'+
					'</table>'+
				'</div>';
			}
			else
			{
				transactions+= 'No logs';
			}
			transactions+= '</div>';
			//set the data
			$("#divTransactionLogs").html(transactions);
			//set scrollbar
			if(logsCount > 10)
			{
				$("#logsHolder").jScrollPane({
					enableKeyboardNavigation:false,
					mouseWheelSpeed:32,
					trackClickSpeed:32,
					verticalGutter:2
				});
				ZA.removeLoader();
			}
		});
	};
	
	WORK_Credits.prototype.openCheckoutWindow=function()
	{
		var creditsPackage = $("input[name='package']:checked");
		var paymentMethod = $("input[name='method']:checked");
		var method = paymentMethod.attr('id');
		var description = paymentMethod.attr('alt');
		var keyword = creditsPackage.attr('id');
		var shortcode = creditsPackage.attr('alt');
		var credits = creditsPackage.parent().find(".credits").val();
		var cost = creditsPackage.parent().find(".cost").val();
		
    	//create checkout popup window
    	var windowTitle = ' '+description+' Checkout';
		ZA.createWindowPopup(1010,windowTitle,260,380,1,0);
		ZA.addLoader($("#window_1010"));
		
		var divData = document.getElementById("window_1010");
		$(divData).css({"-moz-user-select":"-moz-none"});
		
		var container = ZA.createDiv(divData);
		$(container).css({
			width:"100%",
			height:"100%"
		});
		
		//payment method
		switch(method)
		{
			case 'creditcard':
				
				$(divData).css({
					background:"url(_site/line.gif) repeat"
				});
				var form = ZA.createDiv(container);
				$(form).css({
					top:30,
					width:170,
					height:225,
					left:"50%",
					marginLeft:-96,
					padding:10,
					border:"1px solid #999",
					background:"#efefef"
				})
				.html
				(
'<form id="frmCreditCard" method="POST" action="https://www.vcs.co.za/vvonline/ccform.asp">'+
'<input type="hidden" name="p1" value="8043">'+
'<input type="hidden" name="p2" id="referenceNumber" value="">'+
'<input type="hidden" name="p3" value="'+credits+' TCG credits">'+
'<input type="hidden" name="p4" value="'+cost+'.00">'+
//'<input type="hidden" name="p5" value="e">'+
//'<input type="hidden" name="p6" value="f">'+
//'<input type="hidden" name="p7" value="g">'+
//'<input type="hidden" name="p8" value="h">'+
//'<input type="hidden" name="p9" value="i">'+
'<input type="hidden" name="p10" value="http://mytcg.net/vcs/cancel/">'+
//'<input type="hidden" name="p11" value="k">'+
//'<input type="hidden" name="p12" value="l">'+
'<input type="hidden" name="Budget" value="N">'+
//'<input type="hidden" name="NextOccurDate" value="n">'+
'<input type="hidden" name="m_1" value="'+credits+'">'+
//'<input type="hidden" name="m_2" value="z">'+
//'<input type="hidden" name="m_2" value="z">'+
//'<input type="hidden" name="m_3" value="z">'+
//'<input type="hidden" name="m_4" value="z">'+
//'<input type="hidden" name="m_6" value="z">'+
//'<input type="hidden" name="m_7" value="z">'+
//'<input type="hidden" name="m_8" value="z">'+
//'<input type="hidden" name="m_9" value="z">'+
//'<input type="hidden" name="m_10" value="z">'+
'<input type="button" id="cmdPayByCreditCard" class="cmdButton" value="Pay by Credit Card" style="left:30px;bottom:40px;">'+
'<div style="width:156px;height:15px;bottom:15px;left:15px;background:url(_site/all_1.png) -440px -180px no-repeat;"></div>'+
'</form>'
				);
				var icon = ZA.createDiv(form);
				$(icon).css({
					position:"relative",
					background:"url(_site/all_1.png) -270px -230px no-repeat",
					width:125,
					height:83,
					top:15,
					left:25
				});
				var div = ZA.createDiv(form);
				$(div).css({
					top:130,
					width:"100%",
					marginLeft:-10,
					fontSize:16,
					fontWeight:"bold"
				})
				.html(credits+' <span class="txtBlue">TCG</span>');
				var div = ZA.createDiv(form);
				$(div).css({
					top:150,
					width:"100%",
					marginLeft:-10
				})
				.html('R'+cost+'.00');
				//event handler
				$("#cmdPayByCreditCard").click(function(){
					if(!$(this).hasClass('cmdButtonDisabled'))
					{
						$(this).addClass('cmdButtonDisabled');
						ZA.callAjax(ZCR.sURL+'?payment=1&gateway='+method+'&amount='+credits+'&cost='+cost,function(xml){
							var result = ZA.getXML(xml,'result');
							if(result=='success')
							{
								var ref = ZA.getXML(xml,'reference');
								$("#referenceNumber").val(ref);
								$("#frmCreditCard").submit();
							}
							else
							{
								ZA.showMessage('Unexpected error. Please try again.','-');
								$("#closeCheckout").click();
							}
						});
					}
				});
				break;
			
			case 'paypal':
				
				$(divData).css({
					background:"url(_site/line.gif) repeat"
				});
				var form = ZA.createDiv(container);
				$(form).css({
					top:30,
					width:170,
					height:135,
					left:"50%",
					marginLeft:-96,
					padding:10,
					paddingTop:100,
					border:"1px solid #999",
					background:"#efefef"
				})
				.html
				(
/*
'<form id="frmPaypal" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_self">'+
'<input type="hidden" name="cmd" value="_s-xclick">'+
'<input type="hidden" name="hosted_button_id" value="CYRRUVYYRMENC">'+
'<table>'+
'<tr><td><input type="hidden" name="on0" value="TCG Credits">TCG Credits</td></tr><tr><td><select name="os0">'+
	'<option value="350 TCG Credits" alt="350" title="$1.00">350 TCG Credits $1.00</option>'+
	'<option value="700 TCG Credits" alt="700" title="$1.50">700 TCG Credits $1.50</span></option>'+
	'<option value="1050 TCG Credits" alt="1050" title="$2.00">1050 TCG Credits $2.00</option>'+
'</select> </td></tr>'+
'<input type="hidden" name="on1" value="reference"><input type="hidden" name="os1" id="referenceNumber">'+
'</table>'+
'<input type="hidden" name="currency_code" value="USD">'+
'<input type="image" id="cmdPayByPaypal" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">'+
'<img alt="" border="0" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">'+
'</form>'
*/
/*'<form id="frmPaypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_self">'+
'<input type="hidden" name="cmd" value="_s-xclick">'+
//'<input type="hidden" name="notify_url" value="http://mytcg.net/paypal/">'+
'<input type="hidden" name="hosted_button_id" value="ZT4SREPVEM5J8">'+
'<table style="margin-bottom:40px;"><tr><td>'+
'<table>'+
'<tr><td><input type="hidden" name="on0" value="TCG Credits">TCG Credits</td></tr><tr><td><select name="os0">'+
	'<option value="350 TCG Credits" alt="350" title="$1.00">350 TCG Credits $1.00</option>'+
	'<option value="700 TCG Credits" alt="700" title="$1.50">700 TCG Credits $1.50</span></option>'+
	'<option value="1050 TCG Credits" alt="1050" title="$2.00">1050 TCG Credits $2.00</option>'+
'</select> </td></tr>'+
'<input type="hidden" name="on1" value="reference"><input type="hidden" name="os1" id="referenceNumber">'+
'</table>'+
'<input type="hidden" name="currency_code" value="USD">'+
'<input type="image" id="cmdPayByPaypal" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">'+
'<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">'+
'</form>'
*/
'<form action="https://www.paypal.com/cgi-bin/webscr" method="post">'+
'<input type="hidden" name="cmd" value="_s-xclick">'+
'<input type="hidden" name="hosted_button_id" value="C3N4C4RT8JE5W">'+
'<table>'+
'<tr><td><input type="hidden" name="on0" value="TCG Credits">TCG Credits</td></tr><tr><td><select name="os0">'+
	'<option value="350 TCG @">350 TCG @$1.00 USD</option>'+
	'<option value="700 TCG @">700 TCG @$2.00 USD</option>'+
	'<option value="1400 TCG @">1400 TCG @$4.00 USD</option>'+
	'<option value="2800 TCG @">2800 TCG @$8.00 USD</option>'+
'</select> </td></tr>'+
'</table>'+
'<input type="hidden" name="currency_code" value="USD">'+
'<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">'+
'<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">'+
'</form>'



				);
				var icon = ZA.createDiv(form);
				$(icon).css({
					top:30,
					width:140,
					height:40,
					left:25,
					background:"url(_site/all_1.png) -450px -120px no-repeat"
				});
				//event handler
				$("#cmdPayByPaypal").click(function(){
					if(!$(this).hasClass('clicked'))
					{
						$(this).addClass('clicked');
						credits = $("select[name='os0']").find("option:selected").attr('alt');
						cost = $("select[name='os0']").find("option:selected").attr('title');
						ZA.callAjax(ZCR.sURL+'?payment=1&gateway='+method+'&amount='+credits+'&cost='+cost,function(xml){
							var result = ZA.getXML(xml,'result');
							if(result=='success')
							{
								var ref = ZA.getXML(xml,'reference');
								$("#referenceNumber").val(ref);
								$("#frmPaypal").submit();
							}
							else
							{
								ZA.showMessage('Unexpected error. Please try again.','-');
								$("#closeCheckout").click();
							}
						});
					}
					return false;
				});
				break;
			
			case 'sms':
				
				keyword = 'TopCar Cards';
				shortcode = '36262';
				
				$(divData).css({
					background:"url(_site/line.gif) repeat"
				});
				var info = ZA.createDiv(container);
				$(info).css({
					position:"relative",
					background:"url(_site/all.png) -660px -380px no-repeat",
					width:150,
					height:245,
					top:32,
					marginLeft:'auto',
					marginRight:'auto'
				});
				//keyword
				var div = ZA.createDiv(info);
				$(div).css({
					color:"#000",
					top:46,
					width:"100%",
					fontWeight:"bold"
				})
				.html(keyword+' '+ZA.sUsername);
				//shortcode
				var div = ZA.createDiv(info,'txtGreen');
				$(div).css({
					top:102,
					fontSize:28,
					width:"100%",
					fontWeight:"bold"
				})
				.html(shortcode);
				//credits
				var div = ZA.createDiv(info);
				$(div).css({
					color:"#444",
					top:160,
					width:"100%"
				})
				.html('<strong>'+credits+' <span class="txtBlue">TCG</span></strong><br />credits');
				//cost
				var div = ZA.createDiv(info);
				$(div).css({
					color:"#444",
					top:218,
					width:"100%"
				})
				.html('R'+cost+'.00');
				break;
		}
		
		//close button
		var command = ZA.createDiv(divData,"cmdButton","closeCheckout");
		$(command).css({
			bottom:10,
			right:10,
			width:40
		})
		.html('Close')
		.click(function(){
			$("#bodycloak_1010").remove();
			$("#windowcontainer_1010").remove();
			$("#window_1010").remove();
		});
		
		//remove window loader
		setTimeout("ZA.removeLoader()",750);
	};
	
   WORK_Credits.prototype.maximize=function()
   {
		if(ZA.aComponents[ZCR.iComponentNo].iIsMaximized)
		{
			//maximizing window
			ZCR.init();
		}
		else
		{
			//minimizing window
		}
	};
	
	}
	WORK_Credits._iInited=1;
};


var ZCR = new WORK_Credits();
ZA.aComponents[ZCR.iComponentNo].fMaximizeFunction=ZCR.maximize;
