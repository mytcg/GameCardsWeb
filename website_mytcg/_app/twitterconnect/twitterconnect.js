


function COMPONENT_TConnect(){
	this.sBaseURL="_app/twitterconnect/index.php";
	this.divView = 0;
  this.divWorkspace = 0;
	
	if (typeof COMPONENT_TConnect._iInited=="undefined"){

    COMPONENT_TConnect.prototype.init=function(workspace){
         TC.divWorkspace = workspace[0];

         var sToken = getURLParameter("oauth_token");
         //alert(sToken);
         
         if(sToken){
           ZA.callAjax(TC.sBaseURL+"?oauth_token="+sToken,function(xml){ TC.oauth_tokenstatus(xml); });
         }else{
           ZA.callAjax(TC.sBaseURL+"?loginstatus=1",function(html){ TC.loginstatus(html); });
         }
         
         
    };
    
    COMPONENT_TConnect.prototype.loginstatus=function(html){
     // html = html.replace('\r\n','');
     // SF.createDiv(FBC.divWorkspace,"","",html);
     var dud = ZA.createDiv(TC.divWorkspace,"dud","dud","div");
     dud.innerHTML = html;
     TC.divWorkspace.appendChild(dud);
    }
    
    COMPONENT_TConnect.prototype.oauth_tokenstatus=function(xml){
      //alert(xml);
      
      ZA.callAjax(TC.sBaseURL+"?loginstatus=1",function(html){ TC.loginstatus(html); });
    }
    
		COMPONENT_TConnect._iInited=1;
	}
};

//helper
function getURLParameter(name) {
    return (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1];
}




var TC = new COMPONENT_TConnect();
//init code: TC.init($("#twarea"));

