function COMPONENT_UserProfile(){
  this.temp=0;
  this.sXML = "";

  if (typeof COMPONENT_UserProfile._iInited=="undefined"){
    
    //Build screens
    COMPONENT_UserProfile.prototype.init=function(xml){
      var divData=document.getElementById("window_20");
      if(! divData){
        ZA.createWindowPopup(20,"profile",600,505,1,1);
        divData=document.getElementById("window_20");
        
      }
      var response = ZA.getXML(xml,"error");
      $(divData).html("");
      ZUP.sXML = xml;
      
      var frmDetails = ZA.createDiv(divData,"","frmDetails","form");
      $(frmDetails).attr({method:'POST',action:"?save=1"});
      var divLeft = ZA.createDiv(frmDetails,"divLeft","","div");
      $(divLeft).css({top:0,left:0,width:261,height:425,padding:15,textAlign:"left"});
      var divSeparater = ZA.createDiv(frmDetails,"","","div");
      $(divSeparater).css({top:6,left:291,width:2,height:305,backgroundColor:"#AAA"});
      var divRight = ZA.createDiv(frmDetails,"divRight","","div");
      $(divRight).css({top:0,left:293,width:261,height:425,padding:15,textAlign:"left"});
      
      ZUP.drawLeft(divLeft);
      ZUP.drawRight(divRight);
      ZUP.loadData();
      
      var divResponse = ZA.createDiv(divData,"","zupResponse","div");
      $(divResponse).css({width:290,height:15,bottom:55,left:0,color:"#F2C126",textAlign:"center"});
      $(divResponse).html(response);
      
      var divSave = ZA.createDiv(divData,"cmdButton","","div");
      $(divSave).html('Save All');
      $(divSave).css({bottom:10,left:418});
      $(divSave).click(function(){
        var sendData = {
            //username:$("#zupUsername").attr('value'),
            password:$("#zupPassword").attr('value'),
            email:$("#zupEmail").attr('value'),
            msisdn:$("#zupCell").attr('value'),
            name:$("#zupName").attr('value'),
            age:$("#zupAge").attr('value'),
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
      $(divClose).css({bottom:10,left:493});
      $(divClose).click(function(){
        $("#bodycloak_20").remove();
        $("#windowcontainer_20").remove();
        $("#window_20").remove();
      });
      
    };
    
    COMPONENT_UserProfile.prototype.loadData=function(){
      var xml = ZUP.sXML;
      // var sUsername = ZA.getXML(xml,"username");
      // $("#zupUsername").val(sUsername);
      
      var sEmail = ZA.getXML(xml,"email");
      $("#zupEmail").val(sEmail);
      
      var sMSISDN = ZA.getXML(xml,"msisdn");
      $("#zupCell").val(sMSISDN);
      
      var sName = ZA.getXML(xml,"name");
      $("#zupName").val(sName);
      
      var sSurname = ZA.getXML(xml,"surname");
      $("#zupSurname").val(sSurname);
      
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
      $(divLeft).append("<span class='txtBlue'>Account Details</span><br />Need some creds, verify your email and update your profile.<br /><br />");
      
      $(divLeft).append("<span>E</span>mail<br />");
      var inpEmail = ZA.createDiv(divLeft,"profileInput","zupEmail","input");
		$(inpEmail).attr('readonly','readonly');
      
      $(divLeft).append("<br /><br /><span>Change</span> Password<br />");
      var inpPassword = ZA.createDiv(divLeft,"profileInput","zupPassword","input");
      
      $(divLeft).append("<br /><br /><span>Cell</span> number<br />");
      var inpCell = ZA.createDiv(divLeft,"profileInput","zupCell","input");
      
      $(divLeft).append("<br /><br /><span>Name</span><br />");
      var inpName = ZA.createDiv(divLeft,"profileInput","zupName","input");
      
      $(divLeft).append("<br /><br /><span>Surname</span><br />");
      var inpSurname = ZA.createDiv(divLeft,"profileInput","zupSurname","input");
      
      $(divLeft).append("<br /><br /><span>Verify your email address</span><br />Click the send email button. Check your inbox. Get Verification code. Enter the code below and hit enter.<br />");
      var divVerify = ZA.createDiv(divLeft,"cmdButton","","div");
      $(divVerify).html('Send Email');
      $(divVerify).css({bottom:5,left:15});
      $(divVerify).click(function(){
        ZA.callAjax("_app/userprofile/?sendverificationemail=1",function(xml){
          $("#zupResponse").html("Verification Sent.");
        });
      });
      
      var btnVerify = ZA.createDiv(divLeft,"cmdButton","","div");
      $(btnVerify).html('Verify code');
      $(btnVerify).css({bottom:5,left:102});
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
      $(chkVerify).css({bottom:90,right:5});
      
    };
    
    COMPONENT_UserProfile.prototype.drawRight=function(divRight){
      	var xml = ZUP.sXML;
		$(divRight).append("<span class='txtBlue'>Personal Details</span><br />Completing these all these details will earn you some free creds.<br /><br />");
   		iTop = 80;
   		iLeft = 265;
		var iCount = parseInt(ZA.getXML(xml,"answers/count"));
		for(i=0;i<iCount;i++){
			if (i==0){
				var sType = ZA.getXML(xml,"answers/question_"+i+"/question");
				$(divRight).append("<span>"+sType+"</span><br />");
				sType = sType.replace(" ","");
			    var inpName = ZA.createDiv(divRight,"profileInput","zup"+sType+"","input");
			    var chkName = ZA.createDiv(divRight,"chkRed","chk"+sType+"","div");
			    $(chkName).css({top:iTop,left:iLeft});
			}else{
				var sType = ZA.getXML(xml,"answers/question_"+i+"/question");
				$(divRight).append("<br /><br /><span>"+sType+"</span><br />");
				sType = sType.replace(" ","");
			    var inpName = ZA.createDiv(divRight,"profileInput","zup"+sType+"","input");
			    var chkName = ZA.createDiv(divRight,"chkRed","chk"+sType+"","div");
			    $(chkName).css({top:iTop,left:iLeft});
			}
		    iTop += 49;
    	}
    };
    
  }
};
var ZUP = new COMPONENT_UserProfile();
