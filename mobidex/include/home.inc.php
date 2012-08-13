<?php
/** |<
 * php script
 * mobidex 2011
 * developer: J. Horn
 * date: 11.10.2011
 * description: home page
 * user not logged in
 **/

//home

?>
<div id="page-title">Welcome</div>
<?php

	//featured
	$contents = '<img src="site/home.png" width="692" height="495" style="margin-top:-14px; margin-left:-14px;" />';
	buildBlock(680, 480, 'float-left no-style', '', $contents, '');

	//login
   $txtUsername = 'Username';
   $txtPassword = '';
   $chkValue = '0';
   if(isset($_COOKIE['mobidex_username']) && $_COOKIE['mobidex_username']!='')
   {
      $txtUsername = $_COOKIE['mobidex_username'];
      $chkValue = '1';
   }
   if(isset($_COOKIE['mobidex_password']) && $_COOKIE['mobidex_password']!='')
   {
      $txtPassword = $_COOKIE['mobidex_password'];
      $hidden = ' style="display:none;"';
   }
	$contents =
<<<STR
	<input type="text" class="textbox" name="txtUsername" alt="Username" value="{$txtUsername}" style="margin:0px 10px 10px 5px; width:167px;" />
		<br />
	<input type="password" class="textbox" name="txtPassword" alt="Password" value="{$txtPassword}" style="margin:0px 10px 0px 5px; width:167px;" />
	<div class="label" id="password" {$hidden}>Password</div>
	<div class="button-small float-right" id="cmdLogin" style="width:50px;">LOGIN</div>
	<p style="float:left; margin-top:8px;"><label><input name="remember" type="checkbox" value="{$chkValue}"> Remember me</label></p>
	<div class="clear"></div>
	<p><a href="" id="signup-from-popup" style="float:right; font-style:italic; font-size:14px; margin-top: -5px;">Sign up</a></p>
STR;
	buildBlock(220, 215, 'float-right', 'login', $contents, '', 'frmLogin');

	//create
	$contents =
<<<STR
	<div style="position:relative; width:195px; height:195px; margin-left:10px; margin-right:auto; margin-bottom:20px; top:0px;">
		<img src="site/create.png" />
	</div>
	<div style="position:relative; text-align:center; margin-top:-20px;">
		<div id="cmdCreate" class="button">CREATE YOUR CARD</div>
	</div>
STR;
	buildBlock(220, 250, 'float-right', '', $contents);

	clear();

	//video
	$contents = <<< STR
	<div id="video"></div>
STR;
	buildBlock(680, 400, 'float-left', '', $contents);

	//press
	$contents = <<< STR
	<div style="width:203px; height:383px; background:url(site/temp.jpg) center center no-repeat;"></div>
STR;
	buildBlock(220, 400, 'float-right', '', $contents);
   
	clear();
   
	//testimonials
	$contents = <<< STR
   <div style="width:900px; height:80px;">
      <p style="font-size:16px;">Changing jobs or getting a new phone number doesn't have to mean you lose your business card network. Updating your mobidex card once with our easy to use editor means that everybody that has it, automatically gets your new details and is told when something changes.</p>
      <div style="bottom:0px; right:0px;"> &mdash; John Doe </div>
   </div>
STR;
	buildBlock(920, 100, 'float-left', '', $contents);
   
   clear();
//END OF: home

?>
<script>
$(document).ready(function(){
	
	//sign up link
	$("#signup-from-popup").click(function(){
		window.location = '?page=signup';
		return false;
	});
	
	//login button
	$("#cmdLogin").addClass('button-small-disabled').click(function(){
      if(!$(this).hasClass('button-small-disabled'))
      {
         var u = $.trim($("#frmLogin").find("input[name='txtUsername']").val());
         var p = $.trim($("#frmLogin").find("input[name='txtPassword']").val());   
         //var r = ($("#frmLogin").find("input[name='remember']").attr('checked')) ? '1' : '0';
         var r = $("#frmLogin").find("input[name='remember']").val();
         userLogin(u, p, r);
      }
	});
   
   function validateFields()
   {
      var u = $.trim($("#frmLogin").find("input[name='txtUsername']").val());
      var p = $.trim($("#frmLogin").find("input[name='txtPassword']").val());
      //validate input
      if(u == 'Username' || u == '' || p == '')
      {
         if( ! $("#cmdLogin").hasClass('button-small-disabled') )
         {
            $("#cmdLogin").addClass('button-small-disabled');
         }
      }
      else
      {
         $("#cmdLogin").removeClass('button-small-disabled');
      }
   }
	
	//username text field
	var keyword = 'Username';
	$("input[name='txtUsername']")
   .change(function(){
      validateFields();
   })
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
	$(".label[id='password']").click(function(){
		$(this).hide();
		$("input[name='txtPassword']").focus();
	});
	$("input[name='txtPassword']")
   .change(function(){
      validateFields();
   })
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
   $("input[name='remember']").click(function(){
      if($(this).val() == '1')
      {
         $(this).val('0');
      }
      else
      {
         $(this).val('1');
      }
   });
   
	//enter on fields
	$("input[name='txtPassword'], input[name='txtUsername']").keypress(function(e){
		if(e.keyCode == 13)
		{
         $(this).blur();
			$("#cmdLogin").click();
		}
	});
   
	//create your card
	$("#cmdCreate").click(function(){
		addLoader();
		window.location = '?page=create';
	});
   
   validateFields();
   
   //init
   if( $("input[name='txtUsername']").val() != $("input[name='txtUsername']").attr('alt') )
   {
      $("input[name='remember']").attr('checked',true);
   }
   else
   {
      $("input[name='remember']").attr('checked',false);
   }
   
});
</script>