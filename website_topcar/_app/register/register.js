function WORK_Register(){
	this.divData=0;
	this.iComponentNo=3;
	this.sXML = "";
	
	if (typeof WORK_Register._iInited=="undefined"){

    WORK_Register.prototype.init=function(){
      ZR.divData=document.getElementById("window_"+ZR.iComponentNo);
      var divData = ZR.divData;
      
      var divWin = ZA.createDiv(divData);
      $(divWin).css({
		width:"100%",
		textAlign:"left",
		padding:5
      });
      
      //Heading
      var spanHead = ZA.createDiv(divWin,"","","span");
      $(spanHead).css({lineHeight:"18px",fontSize:"16px",fontWeight:"bold",color:"#444"});
      $(spanHead).html("Become part of the Game Card trend");
      
      //Text
      $(divWin).append("<br /><br />It's quick, dirty and <b>REALLY EASY</b>.<br>Simply enter your email address, a username and password below then click the Register button and you're done.<br /><br />");
      
      //FORM
      var divForm = ZA.createDiv(divWin,"","divForm","div");
      var frmRegister = ZA.createDiv(divForm,"","frmForm","form");
      frmRegister.action = "_app/register/index.php?register=1";
      
      $(frmRegister).append(
      		'<table width="100%">'+
      			'<tr>'+
      				'<td align="right">Email address:</td>'+
      				'<td><input type="text" id="email" name="email" class="registerBox" /></td>'+
      			'</tr><tr>'+
      				'<td align="right">Username:</td>'+
      				'<td><input type="text" id="username" name="username" class="registerBox" /></td>'+
      			'</tr><tr>'+
      				'<td align="right">Password:</td>'+
      				'<td><input type="text" id="password" name="password" class="registerBox" /></td>'+
      			'</tr>'+
      		'</table>'
      	);
      $("#username, #email, #password").css({ width:"275px" });
      
      $("#username, #email, #password").keydown(function (e){
        if(e.keyCode == 13){
          ZR.clickRegister();
        }
      });
      
      //Response
      var spanResponse = ZA.createDiv(divWin,"","spanResponse");
      $(spanResponse).css({ top:"155px",width:270,textAlign:"center",color:"#cc0000",left:-5 });
      $(spanResponse).html("");
      
      var btnRegister = ZA.createDiv(divWin,"btnCreate","btnRegister","div");
      $(btnRegister).css({ top:"150px",right:"25px",width:"90px",height:"10px" });
      $(btnRegister).html("Register NOW!");
      $(btnRegister).click(function(){
        ZR.clickRegister();
      });
    };
    
    WORK_Register.prototype.clickRegister=function(){
        $("#spanResponse").html("Registration in progress...");
		  var sUsername = $.trim($("#username").val());
        var sEmail = $.trim($("#email").val());
        var sPassword = $.trim($("#password").val());
        if((sUsername!="")&&(sEmail!="")&&(sPassword!="")){
			ZA.callAjax("_app/register/?register=1&username="+sUsername+"&email="+sEmail+"&password="+sPassword,function(xml){
				ZA.sUserLogin = sUsername;
	         ZA.sUserPassword = sPassword;
				ZR.response(xml);
         });
        }else{
			var message = '';
          if(sEmail==""){
            $("#email").val('').focus();
				message = "Please enter your email address.";
          }
			 else if(sUsername==""){
				$("#username").val('').focus();
				message = "Please enter a username.";
			 }
          else if(sPassword==""){
            $("#password").val('').focus();
				message = "Please enter a password.";
          }
          $("#spanResponse").hide('fade',{opacity:0},150,function(){
            $(this).html(message).show('fade',{opactiy:100},150);
          });
        }
    };
    
    WORK_Register.prototype.response=function(sXML){
      var action = ZA.getXML(sXML,"action");
      if(action=="fail"){
    		ZA.sUserLogin = null;
    		ZA.sUserPassword = null;
          $("#spanResponse").hide('fade',{opacity:0},150,function(){
            $(this).html(ZA.getXML(sXML,"message")).show('fade',{opactiy:100},150);
          });
      }else{
          $("#divprocessd").animate({left:"+=65"},300);
          $("#divprocessf").animate({width:"+=65"},300);
          $("#divprocesst").css({backgroundPosition:"-418px -30px"});

          ZA.createWindowPopup(-1,"",480,160,1,0);
          var divWindow=document.getElementById("window_-1");
          var divData=ZA.createDiv(divWindow);
          $(divData).css({
            width:"100%",
				height:"100%",
            padding:5
          });
          var divMemo=ZA.createDiv(divData);
          $(divMemo).css({textAlign:"left",position:"absolute",left:"10px",top:"10px"});
          $(divMemo).html('<b>Registration Successful</b><br>Click below to login to your account.');
          
			 var button = ZA.createDiv(divData,"cmdButton");
			 $(button).css({
				bottom:20,
				right:20
			 })
			 .html('Login')
			 .click(function(){
            ZA.aWindowLogin=new WORK_Login();
            ZA.callAjax("_app/?login=1&username="+ZA.sUserLogin+"&password="+ZA.sUserPassword,function(xml){
              ZA.aWindowLogin.clickLoginReturn(xml);
            });
          });
      		
      }
    };
  }
	WORK_Register._iInited=1;
};


var ZR = new WORK_Register();
ZR.iComponentNo=3;
ZR.init();