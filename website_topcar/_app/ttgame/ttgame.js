function COMPONENT_TTGame(){
  this.workspace = 0; //area used for the playboard
  this.animating = 0; //check to see if animations are in progress
  this.p1c = []; //player one cards
  this.p2c = []; //player two cards
  this.baseurl = "_app/ttgame/"; //component url : relative
  this.p1cardonboard = 0; //card currently on the board : player1
  this.p2cardonboard = 0; //card currently on the board : player2
  this.whosturn = 1; // player1 or player2's turn: used in validation
  this.statboard = 0; //the area where the stats are displayed
  this.gameid = 0;// unique id of the current game
  this.buttoncontinue = 0; //the continue button
  this.facefront = 1; // is the players cards currently turned over or not
  this.resultdisplay = 0; //feedback from the server is displayed here
  this.statsids = new Array();//array of the stat id's
  this.statnames = new Array();//respective array of stat texts
  this.chosenstat = -1;//keeps track of which stat was chosen
  this.player1score = 0;//player1 score
  this.player2score = 0;//player2 score
  
 
  
  if (typeof COMPONENT_TTGame._iInited=="undefined"){


//setup the empty playing board
    COMPONENT_TTGame.prototype.init=function(){
      CTTG.workspace = ZA.createDiv(document.getElementById('bodypage'),'CTTG_Board','CTTG_Board','div');
      
      var wwidth = $(window).width();
      var nleft = (wwidth/2) - (762 / 2);
      
      $(CTTG.workspace).css({
        'width' : '0px',
        'height' : '0px',
        'background' : 'url("img/ttgame/mainboard.png")',
        'position' : 'absolute',
        'overflow' : 'hidden',
        'top' : '20px',
        'left' : nleft+'px',
        'z-index' : '1000'
      });
      
      
      
      
    };
    
//setup the board, getting it ready for a game
    COMPONENT_TTGame.prototype.showboard=function(){
            
      $(CTTG.workspace).animate({
        'width' : '762px',
        'height' : '803px'
        }, 500 );
        
        var divButton =  ZA.createDiv(this.workspace,"","idButton","div");
        $(divButton).css({backgroundColor:"#FFF",top:633,left:597,zIndex:50});
        $(divButton).html("Flip Cards");
        $(divButton).click(function(e) { CTTG.flipcards() });
        
        CTTG.buttoncontinue =  ZA.createDiv(this.workspace,"","idButton","div");
        $(CTTG.buttoncontinue).css({backgroundColor:"#F0F",top:'249px',left:'329px',height:'20px',zIndex:50,overflow:'hidden',height:'0px'});
        $(CTTG.buttoncontinue).html("Continue");
        $(CTTG.buttoncontinue).click(function(e) { CTTG.continuegame() });
        
        CTTG.resultdisplay = ZA.createDiv(this.workspace,"","resultdisplay","div");
        $(CTTG.resultdisplay).css({backgroundColor:"#F0F",top:'201px',left:'277px',height:'40px',width:'200px',zIndex:50});
        $(CTTG.resultdisplay).html("Choose your card.");
        
        ZA.callAjax(CTTG.baseurl+"?init=1&oponentid=0&deckid=1",function(xml){CTTG.loadgame(xml);});
        

    };
    
//readies the board for the next round
     COMPONENT_TTGame.prototype.continuegame=function(){
        $(CTTG.buttoncontinue).animate({
                'height' : '0px',
              },500
              );
              
       $(CTTG.p1cardonboard.cardlayer).animate({
        'left' : '-500px',
      },500
      );
      
      $(CTTG.p2cardonboard.cardlayer).animate({
        'left' : '2000px',
      },500
      );
      
      
      CTTG.p1c.splice(parseInt($(CTTG.p1cardonboard.cardlayer).attr('cardid')),1);
      CTTG.p2c.splice(parseInt($(CTTG.p2cardonboard.cardlayer).attr('cardid')),1);
      
      CTTG.p1cardonboard = 0;
      CTTG.p2cardonboard = 0;
      
      for(var i = 0;i < CTTG.p1c.length ; i++){


        CTTG.p1c[i].setindex(i);
        CTTG.p1c[i].setHover();
        CTTG.p1c[i].setClick();

      }
      
      for(var i = 0;i < CTTG.p2c.length ; i++){

        CTTG.p2c[i].setindex(i);

      }
      $(CTTG.resultdisplay).html("Choose your card");
      
      if( CTTG.p1c.length == 0){
        
        var endresult = "";
        if(this.player1score == this.player2score){
          endresult = "This game is a draw.";
        }else{
          if(this.player1score > this.player2score){
            endresult = "You have won this game.";
          }else{
            endresult = "You have lost this game."
          }
        }
        $(CTTG.resultdisplay).html("Game Over<br />"+endresult);
      }
      
     }
     
     
//flips all visible cards
    COMPONENT_TTGame.prototype.flipcards=function(){
      
      for(var i =0; i < this.p1c.length;i++ ){
     
        this.p1c[i].flip();
      }
      if(this.p2cardonboard != 0){
        this.p2cardonboard.flip();
      }
      
      
    }

//this is purely for animation, to simulate a card picked my the cpu
    COMPONENT_TTGame.prototype.chooseCPUCard=function(){
      
      var numRand = Math.floor(Math.random()*CTTG.p2c.length);
      //alert(numRand);
      CTTG.p2c[numRand].putonboard();
      
    }

//handles the comparison and actions taken when the player and cpu's cards are compared
    COMPONENT_TTGame.prototype.comparecards=function(xml){
      
     
      var resultoutcome = "";
      var results = parseInt(ZA.getXML(xml,"result"));
      var cid = ZA.getXML(xml,"cardid");
      
      var score = ZA.getXML(xml,"score").split('/');
      
      this.player1score = parseInt(score[0]);
      this.player2score = parseInt(score[1]);
      this.p2cardonboard.imageurlfront = 'img/cards/'+cid+'_front.png';
      this.p2cardonboard.imageurlback = 'img/cards/'+cid+'_back.png';
      
      if(this.facefront == 0){
        $(this.p2cardonboard.canvas).attr("src",this.p2cardonboard.imageurlback);

      }else{
        $(this.p2cardonboard.canvas).attr("src",this.p2cardonboard.imageurlfront);

      }
      
       switch(results){
        case 0:
        resultoutcome = "You Lose the round";
        break;
        case 1:
        resultoutcome = "You Win the round";
        break;
        case 2:
        resultoutcome = "This round is a Draw";
        break;
        
      }
      var stattext = "";
      
      var who = "";
      if(CTTG.whosturn == 1){
        who = "CPU";
      }else{
        who = "You";
      }
      if(CTTG.chosenstat >= 0){
        stattext = who+" chose "+CTTG.statnames[CTTG.chosenstat]+"<br />";
      }
      
      $(CTTG.resultdisplay).html(stattext+resultoutcome+"<br /><b>Score</b>: You "+ZA.getXML(xml,"score")+" CPU");
      
      $(CTTG.buttoncontinue).animate({
                'height' : '20px',
              },500
              );
      
      
    }
    
//places the cards on the board, sets up necesary variables aswell
    COMPONENT_TTGame.prototype.loadgame=function(xml){
      

      var cardcount = parseInt(ZA.getXML(xml,"gamecard_count"));
      //load gameid
      CTTG.gameid = ZA.getXML(xml,"game_id");
      
      for(var i = 0;i < cardcount ; i++){
        
        var card = new TTCard();
        
        var cid = ZA.getXML(xml,"card_id_"+i);
        var gid = ZA.getXML(xml,"gamecard_id_"+i);
        
        card.init(25,625,'img/cards/'+cid+'_front.png','img/cards/'+cid+'_back.png',CTTG.workspace,i,gid);
        CTTG.p1c.push(card);
        
      }
      
      for(var i = 0;i < cardcount ; i++){
        
        var card = new TTCPUCard();
        
       // var cid = ZA.getXML(xml,"card_id_"+i);
        
        card.init(215,115,'img/ttgame/cardback.png','img/ttgame/cardback.png',CTTG.workspace,i,i);
        CTTG.p2c.push(card);
        
      }
      
      var attribcount = parseInt(ZA.getXML(xml,"attribute_count"));
      CTTG.statboard = ZA.createDiv(CTTG.workspace,'CTTG_stats','CTTG_stats','div');
      $(CTTG.statboard).css({padding:"5px",width:'100px',overflow:'hidden',height:'0px',top:'249px',left:'329px',zIndex:50});
      
      for(var i = 0;i < attribcount ; i++){
          var statid = ZA.getXML(xml,"categorystat_id_"+i);
          CTTG.statsids.push(statid);
          var statname = ZA.getXML(xml,"description_"+i);
          CTTG.statnames.push(statname);
          var divButton = ZA.createDiv(CTTG.statboard,"","idButton","div");
          $(divButton).css({backgroundColor:"#FF0",padding:"5px",width:'80px',height:'15px',top:10+(30*i)+'px',left:'10px',zIndex:50});
          $(divButton).html(statname);
          $(divButton).attr('statid',statid);
          $(divButton).attr('orderid',i);
          
              
         $(divButton).click(function(){
            $(CTTG.statboard).animate({
                'height' : '0px',
              },500
              );
               
               CTTG.chosenstat = $(this).attr('orderid');
              ZA.callAjax(CTTG.baseurl+"?compare=1&gameid="+CTTG.gameid+"&gamecardid="+CTTG.p1cardonboard.card_id1+"&statid="+$(this).attr('statid'),function(xml){CTTG.comparecards(xml);});
             
              
              
              
           
         });
         
              
          $(divButton).hover(function(){
            $(this).animate({
                'backgroundColor':'#0FF',
              },200);
          },
          function(){
            $(this).animate({
                'backgroundColor':'#FF0',
              },200);
            
          });
          
          
          

      }
      
      

    };
    
  }

//separate object for the cards (player). I found it difficult to not use objects for these
  COMPONENT_TTGame._iInited=1;
  
  function TTCard(){
    this.posid; //position the card is in the deck. should never change
    this.id; //system id of the card
    this.card_id;//unique id for the card in the current game... used in game validation
    this.x;//draw coordinated for card
    this.y;//draw coordinated for card
    this.imageurlfront;//url to the front image of the card
    this.imageurlback;//url to the back image of the card
    this.cardlayer;//this is the main display element for the cards
    this.canvas;//the actual card drawing surface. an image element
    this.board;//this is the element on which the card wil be drawn
    
    
    TTCard.prototype.init = function(x1,y1,imageurl1,imageurl2,board1,id1,card_id1){
      //set initial status of objects
      this.id = id1;
      this.posid = id1;
      this.card_id1 = card_id1;
      this.x = x1;
      this.y = y1;
      this.imageurlfront = imageurl1;
      this.imageurlback = imageurl2;
      this.board = board1;
      this.cardlayer = ZA.createDiv(this.board,'','','div');
      
      this.canvas = ZA.createDiv(this.cardlayer,'','','img');
      
      //set object properties
      $(this.cardlayer).attr('cardid',id1);
      
      $(this.canvas).attr("src", this.imageurlfront);
      $(this.canvas).css({
          'width' : '99%',
          'height' : '99%'
      });

      $(this.cardlayer).css({
          'width' : '50px',
          'height' : '70px',
          'top': this.y+'px',
          'left':((25*this.posid)+this.x)+'px'
       });
       
       //Due to the inheretance structure of javascript, the following might get 
       //conviluting. Please try to keep your head....
       this.setHover();
       this.setClick();
        
        //initial click attribute

      
    }
    
//resets the card index, prevents out of bound indexes
      TTCard.prototype.setindex = function(ind){
        $(this.cardlayer).attr('cardid',ind);
      }

//flips the card arround
     TTCard.prototype.flip = function(){
       
                      var tmpvarh = $(this.cardlayer).css('height');
                      tmpvarh = tmpvarh.replace("px","");
                      
                      var tmpvar = $(this.cardlayer).css('left');
                      tmpvar = tmpvar.replace("px","");
                      
                      var delta = 25;
                      if(parseInt(tmpvarh) > 100){
                        delta = 100;
                      }
                      
                      $(this.cardlayer).animate({
                        'width' : '0px',
                        'left' : (parseInt(tmpvar) + delta)+'px'
                      },200,function(){
                      
              
                            var currentimage = $(CTTG.p1c[$(this).attr('cardid')].canvas).attr("src");
                            if(currentimage == CTTG.p1c[$(this).attr('cardid')].imageurlfront){
                              $(CTTG.p1c[$(this).attr('cardid')].canvas).attr("src",CTTG.p1c[$(this).attr('cardid')].imageurlback);
                              CTTG.facefront = 0;
                            }else{
                              $(CTTG.p1c[$(this).attr('cardid')].canvas).attr("src",CTTG.p1c[$(this).attr('cardid')].imageurlfront);
                              CTTG.facefront = 1;
                            }
                            
                            var tmpvarh = $(this).css('height');
                            tmpvarh = tmpvarh.replace("px","");
                            
                            var tmpvar = $(this).css('left');
                            tmpvar = tmpvar.replace("px","");
                            
                             var delta = 25;
                              if(parseInt(tmpvarh) > 100){
                                delta = 100;
                              }
                      
                      
                            $(this).animate({
                              'width' : (delta*2)+'px',
                              'left' : (parseInt(tmpvar) - delta)+'px'
                            },200
                            );
                        
                       }); 
       
     }
    
 //on hover animations
    TTCard.prototype.setHover = function(){
      //initial hover attributes zooms in and out as the user moves over cards
       $(this.cardlayer).hover(
         function () {
              
              $(this).animate({
                'width' : '100px',
                'height' : '140px',
                'top':'555px',
                'left':((25*CTTG.p1c[$(this).attr('cardid')].posid)+25)+'px'
                
              },500);
            
          }, 
          function () {
            $(this).animate({
              'width' : '50px',
              'height' : '70px',
              'top':'625px',
              'left':((25* CTTG.p1c[$(this).attr('cardid')].posid)+25)+'px'
            },0
            );
        
          }
        );
    }
    
 //on click card animations and actions
    TTCard.prototype.setClick = function(){
      //initial hover attributes zooms in and out as the user moves over cards
        $(this.cardlayer).click(
            function(){
              $(this).unbind();
              
              CTTG.p1cardonboard = CTTG.p1c[$(this).attr('cardid')];
              
              
              $(this).animate({
                'width' : '200px',
                'height' : '280px',
                'top':'269px',
                'left':'105px'
              },200
              );
            
              for(var i =0; i < CTTG.p1c.length;i++ ){
     
                $(CTTG.p1c[i].cardlayer).unbind();
                
              }
              
              CTTG.chooseCPUCard();
              
              if(CTTG.whosturn == 1){
                $(CTTG.statboard).animate({
                  'height' : '320px',
                },500
                );
                
                $(CTTG.resultdisplay).html("Choose a stat");
                CTTG.whosturn = 2;
               
              }else{
                 var numRand = Math.floor(Math.random()*CTTG.statsids.length);
                 CTTG.chosenstat = numRand;
                 ZA.callAjax(CTTG.baseurl+"?compare=1&gameid="+CTTG.gameid+"&gamecardid="+CTTG.p1cardonboard.card_id1+"&statid="+numRand,function(xml){CTTG.comparecards(xml);});
                 CTTG.whosturn = 1;
              }
          }
        );
    }
    
    
    
    
  };
  
 //separate object for the cards (CPU). I found it difficult to not use objects for these
  function TTCPUCard(){
      
    this.id;
    this.x;
    this.y;
    this.imageurlfront;
    this.imageurlback;
    this.cardlayer;
    this.canvas;
    this.board;
    
    TTCPUCard.prototype.init = function(x1,y1,imageurl1,imageurl2,board1,id1){
      //set initial status of objects
      this.id = id1;
      this.x = x1;
      this.y = y1;
      this.imageurlfront = imageurl1;
      this.imageurlback = imageurl2;
      this.board = board1;
      this.cardlayer = ZA.createDiv(this.board,'','','div');
      
      this.canvas = ZA.createDiv(this.cardlayer,'','','img');
       $(this.cardlayer).attr('cardid',id1);
      //set object properties
      $(this.canvas).attr("src", this.imageurlfront);
      $(this.canvas).css({
          'width' : '99%',
          'height' : '99%'
      });

      $(this.cardlayer).css({
          'width' : '50px',
          'height' : '70px',
          'top': this.y+'px',
          'left':((25*this.id)+this.x)+'px'
       });
       
     
    }
    
    //flips the card
    TTCPUCard.prototype.flip = function(){
       
                      var tmpvarh = $(this.cardlayer).css('height');
                      tmpvarh = tmpvarh.replace("px","");
                      
                      var tmpvar = $(this.cardlayer).css('left');
                      tmpvar = tmpvar.replace("px","");
                      
                      var delta = 25;
                      if(parseInt(tmpvarh) > 100){
                        delta = 100;
                      }
                      
                      $(this.cardlayer).animate({
                        'width' : '0px',
                        'left' : (parseInt(tmpvar) + delta)+'px'
                      },200,function(){
                      
              
                            var currentimage = $(CTTG.p2c[$(this).attr('cardid')].canvas).attr("src");
                            if(currentimage == CTTG.p2c[$(this).attr('cardid')].imageurlfront){
                              $(CTTG.p2c[$(this).attr('cardid')].canvas).attr("src",CTTG.p2c[$(this).attr('cardid')].imageurlback);
                            }else{
                              $(CTTG.p2c[$(this).attr('cardid')].canvas).attr("src",CTTG.p2c[$(this).attr('cardid')].imageurlfront);
                            }
                            
                            var tmpvarh = $(this).css('height');
                            tmpvarh = tmpvarh.replace("px","");
                            
                            var tmpvar = $(this).css('left');
                            tmpvar = tmpvar.replace("px","");
                            
                             var delta = 25;
                              if(parseInt(tmpvarh) > 100){
                                delta = 100;
                              }
                      
                      
                            $(this).animate({
                              'width' : (delta*2)+'px',
                              'left' : (parseInt(tmpvar) - delta)+'px'
                            },200
                            );
                        
                       }); 
       
     }
    
    //resets the card index, prevents out of bound indexes
      TTCPUCard.prototype.setindex = function(ind){
        $(this.cardlayer).attr('cardid',ind);
      }
      
     TTCPUCard.prototype.putonboard = function(){
       
       $(this.cardlayer).animate({
          'width' : '200px',
          'height' : '280px',
          'top':'270px',
          'left':'464px'
        },200
        );
        
         CTTG.p2cardonboard = this;
       
       
     }
  
  };
  
};

var CTTG = new COMPONENT_TTGame();
CTTG.init();
CTTG.showboard();