function COMPONENT_GameChooser(){
 this.temp=0;


 
  
if (typeof COMPONENT_GameChooser._iInited=="undefined"){

    COMPONENT_GameChooser.prototype.init=function(xml){
      
      ZA.createWindowPopup(616,"Choose Game",300,200,1,0);
      
      var divData=document.getElementById("window_616");
      
      var divMemo=ZA.createDiv(divData);
      
      $(divMemo).css({
        overflow:'scroll',
        width:'300px',
        height:'120px'
      });
      
      var gamecount = ZA.getXML(xml,"game_count");
      gamecount = parseInt(gamecount);
      
      for(var i = 0; i < gamecount;i++){
        var gameid = ZA.getXML(xml,"id_"+i);
        var gamename = ZA.getXML(xml,"name_"+i);
        var gamecode = ZA.getXML(xml,"code_"+i);
        var gameimage = ZA.getXML(xml,"image_"+i);
        
        
        var gameoption = ZA.createDiv(divMemo,'','','img');
        
        $(gameoption).css({
          'float':'left',
          'margin-left':'10px',
        });
        
        var tmpfunction = "function(){ ZA.callAjax(\"_app/gamechooser/?getdecks=1&catid="+gameid+"\",function(xml){ CGC.choosedeck(xml); }); }";
        

        $(gameoption).attr('alt',gamename);
        $(gameoption).attr('src',gameimage);
        eval("$(gameoption).click("+tmpfunction+");");

        
      }

        var divButton=ZE.createButton(divData,200,125,80,"Cancel","CGC.clickClose()");
    };
    
    
    
    
    COMPONENT_GameChooser.prototype.choosedeck=function(xml){
      
      ZA.createWindowPopup(845,"Choose Deck",500,300,1,0);
      
      var divData=document.getElementById("window_845");
      
      var divMemo=ZA.createDiv(divData);
      
      $(divMemo).css({
        overflow:'scroll',
        width:'500px',
        height:'220px'
      });
      
      var gamecount = ZA.getXML(xml,"deck_count");
      gamecount = parseInt(gamecount);
      
      for(var i = 0; i < gamecount;i++){
        var deckid = ZA.getXML(xml,"id_"+i);
        var deckname = ZA.getXML(xml,"name_"+i);
        var deckimage = ZA.getXML(xml,"image_"+i);
        
        
        var deckoption = ZA.createDiv(divMemo,'','','img');
        
        $(deckoption).css({
          'float':'left',
          'margin-left':'10px',
        });
        
        var tmpfunction = "";//function(){ "+gamecode+"; }";
        
        $(deckoption).attr('alt',deckname);
        $(deckoption).attr('src',deckimage);
        eval("$(deckoption).click("+tmpfunction+");");

        
      }
        
        
        var divButton=ZE.createButton(divData,400,225,80,"Cancel","CGC.clickCloseDeckChooser()");
    };
    
    COMPONENT_GameChooser.prototype.clickCloseDeckChooser=function(){
      return function() {
        var divBody=document.getElementsByTagName("body")[0];
        var divCloak=document.getElementById("bodycloak_845");
        var divRegister=document.getElementById("windowcontainer_845");
        var divData=document.getElementById("window_845");
        if (divRegister) {
          divBody.removeChild(divRegister);
          divBody.removeChild(divData);
        }
        if (divCloak) {
          divBody.removeChild(divCloak);
        }
      };
    
    };

  
    
    COMPONENT_GameChooser.prototype.clickClose=function(){
      return function() {
        var divBody=document.getElementsByTagName("body")[0];
        var divCloak=document.getElementById("bodycloak_616");
        var divRegister=document.getElementById("windowcontainer_616");
        var divData=document.getElementById("window_616");
        if (divRegister) {
          divBody.removeChild(divRegister);
          divBody.removeChild(divData);
        }
        if (divCloak) {
          divBody.removeChild(divCloak);
        }
      };
    
    };

  }
  
  this._iInited=1;

};



var CGC = new COMPONENT_GameChooser();
//ZA.callAjax("_app/gamechooser/?init=1",function(xml){ CGC.init(xml); });



