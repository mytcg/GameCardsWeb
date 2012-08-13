

/* Description
                 */
function COMPONENT_Paypal(){
	this.sBaseURL="_app/paypal/index.php";
	this.divView = 0;
  this.divWorkspace = 0;
	
	if (typeof COMPONENT_Paypal._iInited=="undefined"){

//Initializes component
    COMPONENT_Paypal.prototype.init=function(workspace){
        //// remove after implementation
       //alert(workspace[0]);
        CP.divWorkspace = workspace[0];
       // var tmpws = SF.createDiv(SD.divMain,"fbconnect","fbconnect","div");
        ///////////////////////////////

         ZA.callAjax(CP.sBaseURL+"?getcode=1",function(html){ CP.setcode(html); });
        
      
    };
    
    COMPONENT_Paypal.prototype.setcode=function(html){
     // html = html.replace('\r\n','');
     // SF.createDiv(FBC.divWorkspace,"","",html);
     var dud = ZA.createDiv(CP.divWorkspace,"dud","dud","div");
     dud.innerHTML = html;
      CP.divWorkspace.appendChild(dud);

    }



		COMPONENT_Paypal._iInited=1;
	}
};


var CP = new COMPONENT_Paypal();


