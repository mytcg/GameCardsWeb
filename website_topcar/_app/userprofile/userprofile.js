function COMPONENT_UserProfile(){
  this.temp=0;
  this.sXML = "";

  if (typeof COMPONENT_UserProfile._iInited=="undefined"){
    
    //Build screens
    COMPONENT_UserProfile.prototype.init=function(xml){
      var divData=document.getElementById("window_20");
      if(! divData){
        ZA.createWindowPopup(20,"profile",600,400,1,1);
        divData=document.getElementById("window_20");
      }
      var response = ZA.getXML(xml,"error");
      $(divData).html("");
      ZUP.sXML = xml;
      
      var frmDetails = ZA.createDiv(divData,"","frmDetails","form");
      $(frmDetails).attr({method:'POST',action:"?save=1"});
      
      var divLeft = ZA.createDiv(frmDetails,"","","div");
      $(divLeft).css({top:0,left:0,width:261,height:325,padding:15,textAlign:"left"});
      var divSeparater = ZA.createDiv(frmDetails,"","","div");
      $(divSeparater).css({top:6,left:291,width:2,height:305,backgroundColor:"#AAA"});
      var divRight = ZA.createDiv(frmDetails,"","","div");
      $(divRight).css({top:0,left:293,width:261,height:325,padding:15,textAlign:"left"});
      
      ZUP.drawLeft(divLeft);
      ZUP.drawRight(divRight);
      ZUP.loadData();
      
      var divResponse = ZA.createDiv(divData,"","zupResponse","div");
      $(divResponse).css({width:290,height:15,top:340,left:0,color:"#CC0000",textAlign:"right"});
      $(divResponse).html(response);
      
      var divSave = ZA.createDiv(divData,"cmdButton","","div");
      $(divSave).html('Save All');
      $(divSave).css({bottom:5,left:418});
      $(divSave).click(function(){
        var sendData = {
            //username:$("#zupUsername").attr('value'),
            password:$("#zupPassword").attr('value'),
            email:$("#zupEmail").attr('value'),
            msisdn:$("#zupCell").attr('value'),
            
            name:$("#zupName").attr('value'),
            age:$("#zupAge").attr('value'),
            owncar:$("#zupOwnCar").attr('value'),
            aspirationalcar:$("#zupAspirationalCar").attr('value'),
            gender:$("#zupGender").attr('value'),
            location:$("#zupLocation").attr('value'),
            cellnumber:$("#zupCellNumber").attr('value'),
          };
          $.post("_app/userprofile/?save=1",sendData,function(xml){
            var error = ZA.getXML(xml,"error");
            if(error == "Saved"){
              ZUP.init(xml);
            }else{
              $("#zupResponse").html(error);
            }
          });
      });

      var divClose = ZA.createDiv(divData,"cmdButton","","div");
      $(divClose).html('Close');
      $(divClose).css({bottom:5,left:493});
      $(divClose).click(function(){
        $("#bodycloak_20").remove();
        $("#windowcontainer_20").remove();
        $("#window_20").remove();
      });
      
    };
    
    COMPONENT_UserProfile.prototype.loadData=function(){
      var xml = ZUP.sXML;
      var sUsername = ZA.getXML(xml,"username");
      $("#zupUsername").val(sUsername);
      
      var sEmail = ZA.getXML(xml,"email");
      $("#zupEmail").val(sEmail);
      
      var sMSISDN = ZA.getXML(xml,"msisdn");
      $("#zupCell").val(sMSISDN);
      
      var sVerified = ZA.getXML(xml,"verified");
      if(sVerified=="1"){ $("#chkVerify").toggleClass("chkRed chkGreen") }
      
      var iCount = parseInt(ZA.getXML(xml,"answers/count"));
      if(iCount > 0){
        for(i=0;i<iCount;i++){
          var sType = ZA.getXML(xml,"answers/question_"+i+"/question");
          sType = sType.replace(" ","");
          var sAnswer = ZA.getXML(xml,"answers/question_"+i+"/answer");
          var sAnswered = ZA.getXML(xml,"answers/question_"+i+"/answered");
          $("#zup"+sType).val(sAnswer);
          if(sAnswered=="1"){
            $("#chk"+sType).toggleClass("chkRed chkGreen");
          }
        }
      }
    };
    
    COMPONENT_UserProfile.prototype.drawLeft=function(divLeft){
      $(divLeft).append("<b><span class='txtBlue'>Account Details</span></b><br />Need some creds, verify your email and update your username.<br /><br />");
      $(divLeft).append("<b>Username</b><br />");
      var inpUsername = ZA.createDiv(divLeft,"profileInput","zupUsername","input");
		$(inpUsername)
		.attr('readonly','readonly')
		.css({
		  background: "transparent"
		});
		
      
      $(divLeft).append("<br /><br /><b>Change Password</b><br />");
      var inpPassword = ZA.createDiv(divLeft,"profileInput","zupPassword","input");
      
      $(divLeft).append("<br /><br /><b>Email</b><br />");
      var inpEmail = ZA.createDiv(divLeft,"profileInput","zupEmail","input");
      
      $(divLeft).append("<br /><br /><b>Verify your email address</b><br />Click the send email button. Check your inbox. Get Verification code. Enter the code below and hit enter.<br />");
      var divVerify = ZA.createDiv(divLeft,"cmdButton","","div");
      $(divVerify).html('Send Email');
      $(divVerify).css({bottom:25,left:15});
      $(divVerify).click(function(){
        ZA.callAjax("_app/userprofile/?sendverificationemail=1",function(xml){
          $("#zupResponse").html("Verification Sent.");
        });
      });
      
      var btnVerify = ZA.createDiv(divLeft,"cmdButton","","div");
      $(btnVerify).html('Verify code');
      $(btnVerify).css({bottom:25,left:102});
      $(btnVerify).click(function(){
        ZA.callAjax("_app/userprofile/?verify=1",function(xml){
            var sVerified = ZA.getXML(xml,"verified");
            if(sVerified=="1"){ $("#chkVerify").toggleClass("chkRed chkGreen") }
          });
      });
      
      var inpVerify = ZA.createDiv(divLeft,"profileInput","zupVerify","input");
      $(inpVerify).keydown(function(e){
        if(e.keyCode==13){
          ZA.callAjax("_app/userprofile/?verify=1",function(xml){
            var sVerified = ZA.getXML(xml,"verified");
            if(sVerified=="1"){ $("#chkVerify").toggleClass("chkRed chkGreen") }
          });
        }
      });
      
      var chkVerify = ZA.createDiv(divLeft,"chkRed","chkVerify","div");
      $(chkVerify).css({top:288,left:262});
		
      $(divLeft).append("<br /><br /><b>Cell number</b><br />");
      var inpCell = ZA.createDiv(divLeft,"profileInput","zupCell","input");
      
    };
    
    COMPONENT_UserProfile.prototype.drawRight=function(divRight){
      $(divRight).append("<b><span class='txtBlue'>Personal Details</span></b><br />Need more creds, Get some for every one you enter and a bonus for completing all of them.<br /><br />");
      $(divRight).append("<b>Name</b><br />");
      var inpName = ZA.createDiv(divRight,"profileInput","zupName","input");
      var chkName = ZA.createDiv(divRight,"chkRed","chkName","div");
      $(chkName).css({top:89,left:262});
      
      $(divRight).append("<br /><br /><b>Age</b><br />");
      var inpAge = ZA.createDiv(divRight,"profileInput","zupAge","input");
      var chkAge = ZA.createDiv(divRight,"chkRed","chkAge","div");
      $(chkAge).css({top:133,left:262});
      
      $(divRight).append("<br /><br /><b>Own Car</b><br />");
      var inpOwnCar = ZA.createDiv(divRight,"profileInput","zupOwnCar","input");
      var chkOwnCar = ZA.createDiv(divRight,"chkRed","chkOwnCar","div");
      $(chkOwnCar).css({top:176,left:262});
      
      $(divRight).append("<br /><br /><b>Aspirational Car</b><br />");
      var inpAspirationalCar = ZA.createDiv(divRight,"profileInput","zupAspirationalCar","input");
      var chkAspirationalCar = ZA.createDiv(divRight,"chkRed","chkAspirationalCar","div");
      $(chkAspirationalCar).css({top:220,left:262});
      
      $(divRight).append("<br /><br /><b>Gender</b><br />");
      var inpGender = ZA.createDiv(divRight,"profileInput","zupGender","input");
      var chkGender = ZA.createDiv(divRight,"chkRed","chkGender","div");
      $(chkGender).css({top:263,left:262});
      
      $(divRight).append("<br /><br /><b>Location</b><br />");
      var inpLocation = ZA.createDiv(divRight,"profileInput","zupLocation","input");
      var chkLocation = ZA.createDiv(divRight,"chkRed","chkLocation","div");
      $(chkLocation).css({top:306,left:262});
      
      // $(divRight).append("<br /><br /><b>Cell Number</b><br />");
      // var inpLocation = ZA.createDiv(divRight,"profileInput","zupCell","input");
      // var chkLocation = ZA.createDiv(divRight,"chkRed","chkLocation","div");
      // $(chkLocation).css({top:306,left:262});
//       
      // $(divRight).append("<br /><br /><b>Email</b><br />");
      // var inpLocation = ZA.createDiv(divRight,"profileInput","zupEmail","input");
      // var chkLocation = ZA.createDiv(divRight,"chkRed","chkLocation","div");
      // $(chkLocation).css({top:306,left:262});
    };
  }
};
var ZUP = new COMPONENT_UserProfile();
