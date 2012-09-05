

/* Description
   This components creates a
   
   jquery framework which
   
   developers can use to connect
   
   users to their websites via
   
   facebook                      */
function COMPONENT_FBConnect(){
	this.sBaseURL="_app/fbconnect/index.php";
	this.divView = 0;
  this.divWorkspace = 0;
	
	if (typeof COMPONENT_FBConnect._iInited=="undefined"){

//Initializes component
    COMPONENT_FBConnect.prototype.init=function(workspace){
        //// remove after implementation
       //alert(workspace[0]);
        
       // var tmpws = SF.createDiv(SD.divMain,"fbconnect","fbconnect","div");
        ///////////////////////////////
        
         FBC.divWorkspace = workspace[0];
         ZA.createDiv(FBC.divWorkspace,"fb-root","fb-root","div");
         ZA.callAjax(FBC.sBaseURL+"?getfbconnecthtml=1",function(html){ FBC.getfbloginstatus(html); });
        
      
    };
    
    COMPONENT_FBConnect.prototype.getfbloginstatus=function(html){
     // html = html.replace('\r\n','');
     // SF.createDiv(FBC.divWorkspace,"","",html);
     var dud = ZA.createDiv(FBC.divWorkspace,"dud","dud","div");
     dud.innerHTML = html;
      FBC.divWorkspace.appendChild(dud);
      var script = document.createElement('script');    
          script.src = FBC.sBaseURL+"?javascriptinit=1";
          script.type = 'text/javascript';
          
          FBC.divWorkspace.appendChild(script);


    }
    
    //this function is called after a user has connected with facebook. xml
    //will contain data on the user and wether or not the user has an existing account
    COMPONENT_FBConnect.prototype.checklocalaccountcallback=function(xml){
     
     
     var hasaccount = ZA.getXML(xml,'hasaccount');
     var loggedin = ZA.getXML(xml,'loggedin');
     var fbuid = ZA.getXML(xml,'fbuid');
     var name = ZA.getXML(xml,'name');
     var first_name = ZA.getXML(xml,'first_name');
     var last_name = ZA.getXML(xml,'last_name');
     var gender = ZA.getXML(xml,'gender');
     var email = ZA.getXML(xml,'email');
      
      if(hasaccount == 0){
         $('#registerusername').val(first_name);
         $('#fbuid').val(fbuid);
         $('#registeremailaddress').val(email);
         $('#registerfullname').val(name);
      }
      
      if(loggedin == 1){
        
        window.location = window.location;
        
      }
    }


		COMPONENT_FBConnect._iInited=1;
	}
};

/* Description
   This is the instance of the
   
   COMPONENT_FBConnect class   */
var FBC = new COMPONENT_FBConnect();
//init code: FBC.init($("#fbarea"));

