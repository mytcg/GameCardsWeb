/**
 * Javscript
 * @name mobidex
 * @description User interface experience script
 */

//VARIABLES

var sUrl = 'ajax/';
var loggedIn = $("#loggedIn").val();
var currentPage = $("#currentPage").val();


// FUNCTIONS
function getXML(sData,sElement){
	sBrowserName=navigator.appName;
	if (sBrowserName=="Microsoft Internet Explorer"){
		sBrowserName="MSIE";
	}
  if (sBrowserName=="MSIE"){
    var xData=new ActiveXObject("Microsoft.XMLDOM");
    xData.async="false";
    xData.loadXML(sData);     
  } else {
    var xData=new DOMParser();  
    xData=xData.parseFromString(sData,"text/xml");
  }
  if (sBrowserName=="MSIE"){
    sElement="//"+sElement;
    xData.setProperty("SelectionLanguage","XPath");
    
    if ((xData.selectSingleNode(sElement))
    	&&(xData.selectSingleNode(sElement).attributes.getNamedItem("val")))
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
  }
};



function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

function rgb2hex(rgb) {
   if(rgb!='transparent')
   {
      var hexDigits = ["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F"];
      rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
      function hex(x) {
         return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
      }
      return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
   }
   return '-1';
}

var addOverlay=function(container, index, f)
{
   if(typeof(container) == "undefined" || $.trim(container) == '')
   {
      container = $("body");
   }
   if(typeof(index) == "undefined" || $.trim(index) == '')
   {
      index = '998';
   }
   
   $(container).append('<div class="overlay" id="'+index+'"><div class="overlay-background"></div></div>');
	
   /**
	 * click event handler
	 * 
   $(".overlay[id='"+index+"']")
   .click(
      function()
      {
         if(confirm('Click Ok to remove the overlay.'))
         {
            setTimeout("removeOverlay("+index+", "+f+")", 150);
         }
      }
   );
	*/
   
}

var removeOverlay=function(index, f)
{
   //set default index
   if(typeof(index) == "undefined")
   {
      index = '998';
   }
   
   //reset overlay
   $(".overlay")
      .attr('id','')
      .hide();
   
   //call-back function
   if(typeof(f) == "function")
   {
      f();
   }
   else
   {
      //alert('meh');
   }
}

var addLoader=function(container, index)
{
	if(typeof(container) == "undefined")
	{
		container = 'body';
	}
   if(typeof(index) == "undefined")
   {
      index = '999';
   }
   
   $(container).append
   (
      '<div class="loader" id="'+index+'">'+
         '<div class="overlay"></div>'+
         '<div class="animation">'+
            '<img src="site/loading51.gif" />'+
            '<br />'+
            '<p id="loader-text">&nbsp;&nbsp;&nbsp;Loading...</p>'+
         '</div>'+
      '</div>'
   );
   
   $(".loader[id='"+index+"']")
   .addClass('small')
   .show()
   .click(function(){
      //setTimeout("removeLoader("+index+")", 550);
   });
	
}
   
var removeLoader=function(index, f)
{
   if(typeof(index) == "undefined")
   {
      index = '999';
   }
   
   $(".loader")
   .attr('id','')
   .hide();
   
   //call-back function if provided
   if(typeof(f) == "function")
   {
      f();
   }
}

function buildBlock(container, width, height, title, contents, style)
{
	var notitle = ($.trim(title) == '') ? ' no-title' : '';
	
	container.append
	(
		'<div class="block'+notitle+'" style="width:'+width+'px; height:'+height+'px; left:50%; top:50%; margin-left:-'+(width/2)+'px; margin-top:-'+(height/2)+'px; '+style+'">'+
			'<div class="title">'+title+'</div>'+
			'<div class="contents">'+
				contents+
			'</div>'+
		'</div>'
	);
	
	resetBlockStyling();
}

var popupindex = 777;

function buildPopup(width, height, title, contents, id, style)
{
	if(typeof(id) == "undefined")
	{
		id = popupindex;
	}
	addOverlay('', id);
	var container = $(".overlay[id='"+id+"']");
	buildBlock(container, width, height, title, contents, style);
	
   //disable the main page
   $("body").addClass('body-disabled');
   
	//initialise graphics for buttons
	setButtonsStyle();
   
	return container;
}

function destroyPopup(id)
{
	if(typeof(id) == "undefined")
	{
		id = popupindex;
	}
	$(".overlay[id='"+id+"']").remove();
   if($(".overlay").length == 1)
   {
      $("body").removeClass('body-disabled');
   }
}

function validateInput()
{
	return false;
}

function activateTextboxes(container)
{
	container.find(".textbox").bind('click focus',function(){
		var alt = $(this).attr('title');
		var val = $.trim($(this).val());
		if(val == alt)
		{
			$(this).val('');
		}
	})
	.blur(function(){
		var alt = $(this).attr('title');
		var val = $.trim($(this).val());
		if(val == '')
		{
			$(this).val(alt);
		}
	});
}


function userLogin(u, p, r, savecard)
{
   if(typeof(savecard)=="undefined")
   {
      savecard = false;
   }
   
   //validate input
   if(u == 'Username' || u == '' || p == '')
   {
      //invalid login credentials - ignore the user!!!
   }
   else
   {  
      //authenticate the user
      $.ajax({
         type: 'POST',
         url: sUrl+'user.php',
         data: {
            action: 'login',
            username: u,
            password: p,
            remember: r
         },
         success: function(result){
            
            if(result == '1')
            {
               var width = 220;
               var height = 160;
               var title = 'Login Successful';
               var contents = '<div style="text-align:center;position:relative;padding:20px;"><img src="site/loading51.gif" width="24" height="24" /><p style="padding:5px;">Loading...</p></div>';
               
               //login success
               var oPopupA = buildPopup(width, height, title, contents, 744);
               
               //ok button
               oPopupA.find("#cmdOk").click(function(){
                  destroyPopup(744);
               });
               
               var remember = (r=='1') ? '&r=1' : '&r=0';
               
               if(!savecard)
               {
                  setTimeout("window.location='?page=browse"+remember+"'", 250);
               }
               else
               {
                  //setTimeout("destroyPopup(744)", 150);
                  destroyPopup(770);
                  savecard();
               }
            }
            else
            {
               var width = 220;
               var height = 160;
               var title = 'Login Failed';
               var contents =
                  '<p style="text-align:center;">'+result+'</p>'+
                  '<div class="button center" id="cmdOk" style="width:50px;">Ok</div>';
               
               //login failed
               var oPopupA = buildPopup(width, height, title, contents, 744);
               
               //ok button
               oPopupA.find("#cmdOk").click(function(){
                  destroyPopup(744);
               });
               
               oPopupA.find("#cmdOk").focus();
            }
         }
      });
   }
}

function saveCard(sDescription, iOrientation, sFrontImage, sBackImage, aFrontFields, aBackFields, sTags, pro, cardtype, template, username)
{
   var failed = false;
   
   if(iOrientation == 'landscape')
   {
      iOrientation = '2';
   }
   else if(iOrientation == 'portrait')
   {
      iOrientation = '1';
   }
   if(typeof(username)=="undefined")
   {
      username = '';
   }
   
   $.ajax({
      async: false,
      type: 'POST',
      url: sUrl+'user.php',
      data: {
         action: 'savecard',
         description: sDescription,
         orientation: iOrientation,
         imagefront: sFrontImage,
         imageback: sBackImage,
         fieldsfront: aFrontFields,
         fieldsback: aBackFields,
         searchtags: sTags,
         cardtype: cardtype,
         template: template,
         user: username
      },
      success: function(result){
         if(result == '1')
         {
            //saved
         }
         else
         {
            failed = true;
         }
      }
   });
   
   if(failed)
   {
      //failed popup
      var width = 240;
      var height = 160;
      var title = 'card creator';
      var contents =
         '<div class="close"></div>'+
         '<p style="font-size:13px; text-align:center; margin-left:25px; margin-right:25px;">Error! Your card was not saved.</p>'+
         '<div id="cmdOk" class="button-small center" style="width:50px;">Ok</div>'
      ;
      
      var oPopupA = buildPopup(width, height, title, contents, 775);
      
      //close button
      oPopupA.find(".close").click(function(){
         destroyPopup(775);
      });
      
      //ok button
      oPopupA.find("#cmdOk").click(function(){
         destroyPopup(775);
      });
      
      setTimeout("removeLoader()", 750);
      
      destroyPopup(707);
      
      return false;
   }
   
   //saved popup
   var width = 240;
   var height = 160;
   var title = 'card creator';
   var contents =
      '<div class="close"></div>'+
      '<p style="font-size:13px; text-align:center; margin-left:25px; margin-right:25px;">Congratulations! Your card was saved.</p>'+
      '<div id="cmdOk" class="button-small center" style="width:50px;">Ok</div>'
   ;
   
   var oPopupA = buildPopup(width, height, title, contents, 778);
   
   //close button
   oPopupA.find(".close").click(function(){
      destroyPopup(778);
   });
   
   //ok button
   oPopupA.find("#cmdOk").click(function(){
      window.location = '?page=browse';
   });
   
   if(pro=='1')
   {
      destroyPopup(708);
   }
   else
   {
      destroyPopup(707);
   }
}

function showLoginPopup(title, button, message, savecard)
{
   
	var width = 320;
	var height = 225;
	if(typeof(title) == "undefined")
	{
		title = 'user login form';
	}
	if(typeof(button) != "undefined")
	{
		buttonWidth = 100;
	}
	else
	{
		buttonWidth = 50;
		button = 'Login';
	}
	if(typeof(message) != "undefined")
	{
		height+= 60;
		message = '<p>'+message+'</p><hr />';
	}
	else
	{
		message = '';
	}
   if(typeof(savecard)=='undefined')
   {
      savecard = false;
   }
   
   var txtUsername = 'Username';
   var txtPassword = '';
   var hidden = '';
   var chkVal = '0';
   
   //set the username and password if saved
   if($.cookie("mobidex_username") != null)
   {
      txtUsername = $.cookie("mobidex_username");
      chkVal = '1';
   }
   if($.cookie("mobidex_password") != null)
   {
      txtPassword = $.cookie("mobidex_password");
      hidden = ' style="display:none;"';
   }
	var contents =
		'<div class="close"></div>'+
		message+
		'<input type="text" class="textbox" name="txtUsername" alt="Username" value="'+txtUsername+'" style="margin:0px 10px 10px 5px; width:267px;" />'+
			'<br />'+
		'<input type="password" class="textbox" name="txtPassword" alt="Password" value="'+txtPassword+'" style="margin:0px 10px 0px 5px; width:267px;" />'+
		'<div class="label" id="password"'+hidden+'>Password</div>'+
			'<br />'+
		'<div class="button-small float-right" id="cmdLogin" style="width:'+buttonWidth+'px;">'+button+'</div>'+
		'<p style="float:left; margin-top:8px;"><label><input name="remember" type="checkbox" val="'+chkVal+'"> Remember me</label></p>'+
		'<div class="clear"></div>'+
		'<p><a href="" id="signup-from-popup" style="float:right; font-style:italic; font-size:14px;">Sign up</a></p>';
	
	//user login popup
	var oPopup = buildPopup(width, height, title, contents, 770);
	
	//close button
	oPopup.find(".close").click(function(){
		destroyPopup(770);
	});
	
	//sign up link
	oPopup.find("#signup-from-popup").click(function(){
		showRegisterPopup(false, savecard);
		return false;
	});
	
	//login button
	oPopup.find("#cmdLogin").click(function(){
		var u = $.trim(oPopup.find("input[name='txtUsername']").val());
		var p = $.trim(oPopup.find("input[name='txtPassword']").val());
      var r = oPopup.find("input[name='remember']").val();
      userLogin(u, p, r, savecard);
	});
	
	//username text field
	var keyword = 'Username';
	oPopup.find("input[name='txtUsername']")
	.click(function(){
		if($(this).val() == keyword)
		{
			$(this).val('');
		}
	})
	.blur(function(){
		if($.trim($(this).val()) == '')
		{
			$(this).val(keyword);
		}
	});
	
	//password text field
	oPopup.find(".label[id='password']").click(function(){
		$(this).hide();
		$("input[name='txtPassword']").focus();
	});
	oPopup.find("input[name='txtPassword']")
	.bind('click focus', function(){
		$(".label[id='password']").hide();
	})
	.blur(function(){
		if($.trim($(this).val()) == '')
		{
			$(this).val('');
			$(".label[id='password']").show();
		}
	});
   
   //remember me checkbox
	oPopup.find("input[name='remember']").click(function(){
      if($(this).val() == '1')
      {
         $(this).val('0');
      }
      else
      {
         $(this).val('1');
      }
   });
   
	//login on enter
	oPopup.find("input[name='txtPassword'], input[name='txtUsername']").keypress(function(e){
		if(e.keyCode == 13)
		{
         $(this).blur();
			oPopup.find("#cmdLogin").click();
		}
	});
   
   function validateFields()
   {
      var u = $.trim(oPopup.find("input[name='txtUsername']").val());
      var p = $.trim(oPopup.find("input[name='txtPassword']").val());
      //validate input
      if(u == 'Username' || u == '' || p == '')
      {
         if( ! oPopup.find("#cmdLogin").hasClass('button-small-disabled') )
         {
            oPopup.find("#cmdLogin").addClass('button-small-disabled');
         }
      }
      else
      {
         oPopup.find("#cmdLogin").removeClass('button-small-disabled');
      }
   }
	
   
   //init
   
   validateFields();
   
   if( oPopup.find("input[name='txtUsername']").val() != oPopup.find("input[name='txtUsername']").attr('alt') )
   {
      oPopup.find("input[name='remember']").attr('checked',true);
      oPopup.find("input[name='remember']").val('1');
   }
   else
   {
      oPopup.find("input[name='remember']").attr('checked',false);
      oPopup.find("input[name='remember']").val('0');
   }
   
}

function getTruncatedString(str, max, separator)
{
   var stringval = str;
   var trimmed = false;
   if(str.length > max)
   {
      stringval = str.split(separator);
      stringval.pop();
      stringval = stringval.join(separator);
      stringval = this.getLimitedString(stringval, max, separator);
      trimmed = true;
   }
   return stringval;
}

function viewTermsPopup()
{
   var title = 'Mobidex Terms and Conditions';
	var width = 800;
	var height = 600;
	var contents = '<div class="close"></div>'+$("#frmTerms").html();
   

	
   var oPopup = buildPopup(width, height, title, contents, 555);
	
	//close button
	oPopup.find(".close").click(function(){
		destroyPopup(555);
	});
   
}


function getFontFile(fontname)
{
   fontname = fontname.replace(/"/g,'');
   var file = fontname;
   switch(fontname)
   {
      case 'Times New Roman':
         file = 'times';
         break;
      case 'Lucida Console':
         file = 'lucida';
         break;
      case 'Courier New':
         file = 'courier';
         break;
   }
   return file.toLowerCase();
}


function validateEmail(email)
{ 
   var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
   return re.test(email);
}

function showRegisterPopup(pro, savecard)
{
	if(typeof(pro) == "undefined")
	{
      pro = false;
	}
   if(typeof(savecard)=="undefined")
   {
      savecard = false;
   }
   
   var title = (pro) ? 'Pro user registration' : 'Free user registration';
	var form = ".form[id='signup']";
	var width = 300;
	var height = 490;
	var contents = $("#frmSignUp").html();
	
   var oPopup = buildPopup(width, height, title, contents, 776);
	
	//close button
	oPopup.find(".close").click(function(){
		destroyPopup(776);
	});
   
	//i agree check button
	oPopup.find("input[name='chkAgree']").click(function(){
		validateForm();
	});
	
	//Change dropdown list
	oPopup.find("select[name='selCountry']").change(function(){
		validateForm();
	});
   
   //view terms and conditions
   oPopup.find("#viewTerms").click(function(){
      viewTermsPopup();
   });
	
   function validateForm()
   {
      //validation
      var invalid = false;
      
      oPopup.find(".form[id='signup']").find(".field-holder").each(function(){
         var val = $.trim($(this).find(".textbox").val());
         var alt = $(this).find(".textbox").attr('alt');
         if(val == alt || val == '')
         {
            $(this).find(".check-icon").addClass('hidden');
            $(this).find(".x-icon").removeClass('hidden');
            invalid = true;
         }
         else
         {
            $(this).find(".x-icon").addClass('hidden');
            $(this).find(".check-icon").removeClass('hidden');
         }
      });
      
      //Update code based on Country selection
      var ccode = oPopup.find("select[name='selCountry'] option:selected").attr("alt");
      oPopup.find("input[name='txtCode']").val(ccode);
      
      //confirm password
		var p = $.trim(oPopup.find("input[name='txtPassword']").val());
		var c = $.trim(oPopup.find("input[name='txtConfirm']").val());
      if(p != c)
      {
         oPopup.find(".form[id='signup']").find(".field-holder").find("[name='txtPassword']").parent().find(".check-icon").addClass('hidden');
         oPopup.find(".form[id='signup']").find(".field-holder").find("[name='txtPassword']").parent().find(".x-icon").removeClass('hidden');
         oPopup.find(".form[id='signup']").find(".field-holder").find("[name='txtConfirm']").parent().find(".check-icon").addClass('hidden');
         oPopup.find(".form[id='signup']").find(".field-holder").find("[name='txtConfirm']").parent().find(".x-icon").removeClass('hidden');
         invalid = true;
      }
      
      //agree to t's and c's
      if( oPopup.find("input[name='chkAgree']").attr('checked') != "checked" )
      {
         invalid = true;
      }
      
      //validate email address
		var e = $.trim(oPopup.find("input[name='txtEmail']").val());
      if(!validateEmail(e))
      {
         oPopup.find(".form[id='signup']").find(".field-holder").find("[name='txtEmail']").parent().find(".check-icon").addClass('hidden');
         oPopup.find(".form[id='signup']").find(".field-holder").find("[name='txtEmail']").parent().find(".x-icon").removeClass('hidden');
         invalid = true;
      }
      
      //validate mobile number
      var m = $.trim(oPopup.find("input[name='txtMobile']").val());
      if(m.length < 9 || isNaN(m) || m.substring(0,1)=='0' || m.substring(0,1)=='-' || m.substring(0,1)=='+')
      {
         oPopup.find(".form[id='signup']").find(".field-holder").find("[name='txtMobile']").parent().find(".check-icon").addClass('hidden');
         oPopup.find(".form[id='signup']").find(".field-holder").find("[name='txtMobile']").parent().find(".x-icon").removeClass('hidden');
         invalid = true;
      }
      
      //update button status
      if(invalid)
      {
         oPopup.find(".form[id='signup']").find("#signup").addClass('button-disabled');
      }
      else
      {
         oPopup.find(".form[id='signup']").find("#signup").removeClass('button-disabled');
      }
      
   }
   
	//sign up button
	$(".form[id='signup']").find("#signup").click(function()
	{
		if(!$(this).hasClass('button-disabled'))
		{
			var obj = $(this);
			obj.addClass('button-disabled');
			var n = $.trim(oPopup.find("input[name='txtName']").val());
            var s = $.trim(oPopup.find("input[name='txtSurname']").val());
			var p = $.trim(oPopup.find("input[name='txtPassword']").val());
			var c = $.trim(oPopup.find("input[name='txtConfirm']").val());
			var e = $.trim(oPopup.find("input[name='txtEmail']").val());
			var m = $.trim(oPopup.find("input[name='txtMobile']").val());
         	var country = oPopup.find("select[name='selCountry'] option:selected").val();
            var ccode = $.trim(oPopup.find("input[name='txtCode']").val());
         
         var proUser = (pro) ? '1' : '0';
         
         $.ajax({
            type: 'POST',
            url: sUrl+'user.php',
            data: {
               action: 'register',
               name: n,
               surname: s,
               password: p,
               confirm: c,
               email: e,
               ccode: ccode,
               mobile: m,
               country: country,
               pro: proUser
            },
            success: function(result){
               
               var width = 240;
               var height = 180;
               
               if(result.indexOf(' ') > -1)
               {
                  //registration failed
                  var title = 'Registration Failed';
                  var contents =
                     '<p style="text-align:center;"><br />'+result+'</p>'+
                     '<div class="button center" id="cmdOk" style="width:100px;">Try again</div>';
                  
                  var oPopup = buildPopup(width, height, title, contents, 749);
                  
                  //ok button
                  oPopup.find("#cmdOk").click(function(){
                     destroyPopup(749);
                     obj.removeClass('button-disabled');
                  });
               
               }
               else
               {
                  //success : result = username
                  var title = 'Registration Successful';
                  var contents = '<p style="text-align:center;"><br />Thank you for registering.<br />Your username is '+result+'.</p>';
                  
                  if(!savecard)
                  {
                     contents += '<div class="button center" id="cmdOk" style="width:120px;">Click to log in</div>';
                     var oPopup = buildPopup(width, height, title, contents, 749);
                     //ok button
                     oPopup.find("#cmdOk").click(function(){
                        window.location = '?page=browse';
                     });
                  }
                  else
                  {
                     contents += '<p style="text-align:center;"><br />Saving card...</p>';
                     var oPopup = buildPopup(width, height, title, contents, 749);
                     savecard(result);
                  }
                  
                  
                  
               }
               
            }
         });
		}
	});
	
	//text fields
	$(".form[id='signup']").find(".textbox").each(function(){
		if($(this).attr('type') == "text")
		{
			//text fields
			$(this)
			.bind('click focus',function(){
				if($(this).val() == $(this).attr('alt'))
				{
					$(this).val('');
				}
			})
			.blur(function(){
				if($.trim($(this).val()) == '')
				{
					$(this).val($(this).attr('alt'));
				}
			});
		}
		else if($(this).attr('type') == "password")
		{
			var id = $(this).attr('alt');
			id = id.split(' ');
			id = id.join('');
			id = id.toLowerCase();
			//password fields
			$(this)
			.bind('click focus',function(){
				if($(this).val() == '')
				{
					$(this).val('');
					$(".label[id='"+id+"']").hide();
				}
			})
			.blur(function(){
				if($.trim($(this).val()) == '')
				{
					$(this).val('');
					$(".label[id='"+id+"']").show();
				}
			});
		}
      //validation indicators
      $(this).keyup(function(){
			validateForm();
		});
	});
}


function setButtonsStyle()
{
	$(".button, .button-small, .button-small-active").each(function(){
      if(!$(this).find(".button-left-panel").size())
      {
         $(this).append
         (
            '<div class="button-left-panel"></div>'+
            '<div class="button-right-panel"></div>'
         );
      }
	});
}



// WINDOWS

var resetInlineWindowStyling = function()
{
	$(".inline-window").each(function(){
		var thHeight = parseInt($(this).find('thead').css('height'),10);
		var iHeight = parseInt($(".inline-window").css('height'),10) - thHeight;
		var iWidth = parseInt($(".inline-window").css('width'),10) + 1;
		$(this).append
		(
			'<div class="top-left"></div>'+
			'<div class="top-right"></div>'+
			'<div class="bottom-left"></div>'+
			'<div class="bottom-right"></div>'+
			'<div class="th-left-panel" style="height:'+thHeight+'px"></div>'+
			'<div class="th-right-panel" style="height:'+thHeight+'px"></div>'+
			'<div class="top-panel" style="width:'+iWidth+'px"></div>'+
			'<div class="left-panel" style="height:'+iHeight+'px"></div>'+
			'<div class="right-panel" style="height:'+iHeight+'px"></div>'+
			'<div class="bottom-panel" style="width:'+iWidth+'px"></div>'
		);
	});
};

var resetBlockStyling = function(id)
{
    if(typeof(id)=="undefined")
    {
        $(".block").each(function(){
            if(!$(this).hasClass('no-style'))
            {
               var offsetY = 40;
               if($(this).hasClass('no-title'))
               {
                  offsetY = 0;
               }
               var iWidth = parseInt($(this).css('width'),10);
               var iHeight = parseInt($(this).css('height'),10);
               $(this).append
               (
                  '<div class="top" style="width:'+(iWidth-10)+'px;"></div>'+
                  '<div class="top-left"></div>'+
                  '<div class="top-right"></div>'+
                  '<div class="left" style="height:'+(iHeight-14-offsetY)+'px;"></div>'+
                  '<div class="right" style="height:'+(iHeight-14-offsetY)+'px;"></div>'+
                  '<div class="bottom" style="width:'+(iWidth-10)+'px;"></div>'+
                  '<div class="bottom-left"></div>'+
                  '<div class="bottom-right"></div>'+
                  '<div class="title-left"></div>'+
                  '<div class="title-right"></div>'
               );
            }
        });
    }
    else
    {
        var block = $(".block[id='"+id+"']");
        if(!block.hasClass('no-style'))
        {
           var offsetY = 40;
           if(block.hasClass('no-title'))
           {
              offsetY = 0;
           }
           var iWidth = parseInt(block.css('width'),10);
           var iHeight = parseInt(block.css('height'),10);
           
           block.append
           (
              '<div class="top" style="width:'+(iWidth-10)+'px;"></div>'+
              '<div class="top-left"></div>'+
              '<div class="top-right"></div>'+
              '<div class="left" style="height:'+(iHeight-14-offsetY)+'px;"></div>'+
              '<div class="right" style="height:'+(iHeight-14-offsetY)+'px;"></div>'+
              '<div class="bottom" style="width:'+(iWidth-10)+'px;"></div>'+
              '<div class="bottom-left"></div>'+
              '<div class="bottom-right"></div>'+
              '<div class="title-left"></div>'+
              '<div class="title-right"></div>'
           );
        }
    }
};


//activate pro via payment gateway
function showPaymentGateway()
{
   var width = 480;
   var height = 405;
   var title = 'Activate Pro Subscription';
   var id = 555;
   var contents = $("#frmPaymentGateway").html();
   
   var oPopup = buildPopup(width, height, title, contents, id);
   
   //cancel button
   oPopup.find("#cmdCancel").click(function(){
      destroyPopup(id);
   });
   /*
   oPopup.find("#cmdNext").click(function(){
      var step = $(this).attr('alt');
      var step2hide = (parseInt(step)-1).toString();
      showStep('frmOwnImage', step, step2hide);
   });
   */
}

	
	
// -----------------------------------------------------------------------------
// READY
// -----------------------------------------------------------------------------

$(document).ready(function(){
	
// GENERAL -----

	//catch all anchor a tag clicks
	$("a").livequery('click',function()
	{
		var sId = $(this).attr('id');
		var sClass = $(this).attr('class');
		var sAlt = $(this).attr('alt');
		
		switch(sClass)
		{
			case 'login':
				showLoginPopup();
				break;
			
			case 'logout':
				
				//log the user out
				$.ajax({
					type: 'POST',
					url: sUrl+'user.php',
					data: {action: 'logout'},
					success: function(result){
						if(result == '1')
						{
							window.location = '?page=home';
						}
					}
				});
				break;
			
			case 'help':
            
            var notes = '';
				var title = 'help - ';
            
            if(typeof(sId) == "undefined")
            {
               notes = $("#help-notes").find("#"+sAlt).html();
               title += $("#help-notes").find("#"+sAlt).find(".help-title").html();
            }
            else
            {
               
               //use the same help notes for pro step 2 as for free step 2
               if(sAlt == '2' && sId == '1')
               {
                  sId = '0';
               }
               
               //use the same help notes for upload step 1 as for pro step 1
               if(sAlt == '1' && sId == '2')
               {
                  sId = '1';
               }
               
               //use the same help notes for upload step 1 as for pro step 1
               if(sAlt == '3' && sId == '1')
               {
                  sId = '0';
               }
               
               notes = $("#help-notes").find("#"+sId+"[alt='"+sAlt+"']").html();
               title += $("#help-notes").find("#"+sId+"[alt='"+sAlt+"']").find(".help-title").html();
            }
				
				//contextual page help notes
				var width = 680;
				var height = 640;
				var contents = '<div class="close"></div><div id="help-notes">'+notes+'</div>';
            
				//help popup
				buildPopup(width, height, title, contents, 100, 'z-index:100;');
				
				//close button
				$(".close").click(function(){
					destroyPopup(100);
				});
            
            return false;
				
				break;
			
			default:
				//alert('id='+sId+'\nclass='+sClass+'\nalt='+sAlt);
            if($(this).hasClass('disabled'))
            {
               return false;
            }
            break;
		}
		//return false;
	});
	
	//catch all button clicks
	
	$(".button-small").livequery('click',function()
	{
		var id = $(this).attr('id');
		
		var exceptions = [
			'cmdResetFilters',
			'cmdOwnImage',
			'prev',
			'next'
		];
		
		if(!inArray(id, exceptions))
		{
			//
		}
	});
	
	$(".button").livequery('click',function()
	{
		var id = $(this).attr('id');
		
		//will not be caught by the catch all buttons
		var exceptions = [
			'cmdCreate',
			'cmdResetFilters',
			'cmdOwnImage',
			'cmdNext',
			'cmdOk'
		];
		
		if(!inArray(id, exceptions))
		{
			if(!$(this).hasClass('button-disabled'))
			{
				switch(id)
				{
					default:
						//alert('button clicked -> '+id);
				}
			}
		}
	});
	
   
// MENU -----
	
   $(".submenu-item").click(function()
   {
      var id = $(this).attr('id');
      if(id == '0')
      {
         window.location = '?page=create';
      }
      else
      {
         if(loggedIn == '0')
         {
            showLoginPopup('Pro Card Creator', 'Login', 'You must have a Pro Mobidex account to use the Pro Creator.<br />Please log in.');
         }
         else
         {
            //load page
            window.location = '?page=create&pro='+id;
         }
      }
   });
   
	$(".menu-item").click(function()
	{
		if(!$(this).hasClass('active') && $(this).attr('id') != 'create')
		{
			//add page content loader
			addLoader($("#page-center"));
			
			//if(!confirm('go')){return false;};
			
			var contents;
			var page;
			var id = $(this).attr('id');
			
			$("#page-contents").empty().html('Loading...');
			$(".menu-item").removeClass('active');
			
			$(this).addClass('active');
			
			if(loggedIn == '0')
			{
				switch(id)
				{
					case 'home':
						//load home page
						page = 'home';
						
						break;
					
					case 'signup':
						//load signup page
						page = 'signup';
						
						break;
					
					case 'create':
						//load create a card
						page = 'create';
						
						break;
					
					case 'contact':
						//load contact us page
						page = 'contact';
						
						break;
				}
			}
			else
			{
				switch(id)
				{
					case 'browse':
						//load browse page
						page = 'browse';
						
						break;
					
					case 'create':
						//load create a card page
						page = 'create';
						
						break;
					
					case 'track':
						//load track
						page = 'track';
						
						break;
					
					case 'contact':
						//load contact us page
						page = 'contact';
						
						break;
				}
			}
			
			//load page
			window.location = '?page='+page;
         
		}
	});
   
   //menu create dropdown
   $(".menu-item[id='create']").hover(function(){
      $("#submenu-items").show();
   },
   function(){
      $("#submenu-items").hide();
   });
   
   $("#submenu-items").hover(function(){
      $(this).show();
   },
   function(){
      $(this).hide();
   });
   
	
// INIT
	
	loggedIn = $("#loggedIn").val();
	currentPage = $("#currentPage").val();
	
   $("#page-help").find(".help").attr('alt',currentPage);
	if(loggedIn == '1')
	{
		//user logged in
		if(true)
		{
			$("#user").show();
			$("#page-help").show();
		}
	}
	else
	{
		//user not logged in
		if(currentPage != 'home')
		{
			$("#user").show();
			$("#page-help").show();
		}
	}
   
   //default page settings
   switch(currentPage)
   {
      case 'contact':
         $("#page-help").hide();
         break;
      default:
         break;
   }
	
	//dynamic styling
	resetInlineWindowStyling();
	resetBlockStyling();
	setButtonsStyle();
   
});