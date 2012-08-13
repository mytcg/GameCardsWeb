<div id="page-title">browse your contacts and your cards</div>
<?php
//search
$contents =
<<<STR
	<div style="position:relative;">
		<input type="text" id="txtSearch" class="textbox" style="width:180px;" value="Search.." alt="Search.." />
		<div id="cmdSearch"></div>
	</div>
	<div class="button-small" id="cmdReset" style="width:95px; margin-top:20px; display:none;">View All Cards</div>
STR;

buildBlock(225, 60, 'float-left', '', $contents, '');   
?>
<div id="search-message" style="font-size:14px; position:relative; float:right; width:675px; padding:24px 0px 35px 0px; text-align:center; font-style:italic; display:none;">
</div>
<?php
   //user cards   
   printUserCards();
   
   //my cards   
   $aCards = getCards();
   $myCards = array();
   if(sizeof($aCards) > 0)
   {
      foreach($aCards as $card)
      {
		$description = truncateString($card['description'],32);
		$contents =
				'<input type="hidden" class="usercard_id" value="'.$card['usercard'].'" />'.
				'<input type="hidden" class="card_id" value="'.$card['card_id'].'" />'.
				'<input type="hidden" class="cardtype" value="'.$card['cardtype'].'" />'.
				'<input type="hidden" class="yours" value="1" />'.
				'<input type="hidden" class="cardstat" value="'.$card['description'].'" />'.
				'<input type="hidden" class="cardstat" value="'.$card['searchtags'].'" />';
				
        $myCards[] = '<span class="mycard-item" id="'.$card['card_id'].'" alt="'.$card['orientation'].'" title="'.$card['description'].'">'.$contents.$description.'</span><div class="del-button" id="'.$card['card_id'].'" alt="card"></div>';
      }
		if(sizeof($myCards) > 0)
		{
			$myCards = implode('<hr />', $myCards);
		}
      else
      {
         $myCards = '<p style="text-align:center; font-style:italic;">No Cards</p>';
      }
   }
	else
	{
		$myCards = '<div style="position:relative; font-style:italic; text-align:center;">No cards</div>';
	}
	$contents =
<<<STR
   <div id="cmdAddCard" class="add-button" style="top:-50px;" title="Create a new Card"></div>
	{$myCards}
STR;
	buildBlock(225, 150, 'userCardsLinks float-left', 'my cards', $contents, 'height:auto;');
   
   
	//my albums
   $albums = '<img src="site/loading51.gif" style="width:24px; height:24px;" />';
	$contents =
<<<STR
	<div id="cmdAddAlbum" class="add-button" style="top:-50px;" title="Create a new Album"></div>
   <div id="albums-holder" style="position:relative;">
      {$albums}
   </div>
STR;
	buildBlock(225, 150, 'float-left', 'albums', $contents, 'height:auto; clear:left;', 'blockAlbums');
	clear();
?>


	<!-- frmView -->
	<div id="frmView" style="display:none;">
		
		<div class="close"></div>
		
		<div id="frmStepX" style="position:relative;">
			<h1 class="card-name" style="text-align:center;padding:5px;"></h1>
			<div class="cards-holder"></div>
			<div class="clear"></div>
			<p class="float-left" style="padding:6px;padding-left:20%;">Front image</p>
			<p class="float-right" style="padding:6px;padding-right:20%;">Back image</p>
			<div class="clear"></div>
		</div>
		
		<div style="top:420px; width:100%;"><hr />
			<div id="cmdOk" class="button center" style="width:120px;">Close</div>
		</div>
		
	</div>
	
<script>
//FUNCTIONS -----

function showAllCards()
{
   $("#cards-holder").find(".card").each(function(){
      $(this).show();
   });
	checkForNoCards();
}

function hideAllCards()
{
   $("#cards-holder").find(".card").each(function(){
      $(this).hide();
   });
}

function getCardImages(id)
{
   var w = 350;
   var h = 350;
	var sImages =
      '<div class="editCardImage float-left" id="front" style="margin-left:10px; position:relative; width:'+w+'px; height:'+h+'px; background:url(img/cards/'+id+'_front.jpg) center center no-repeat;"></div>'+
      '<div class="editCardImage float-left" id="back" style="margin-left:10px; position:relative; width:'+w+'px; height:'+h+'px; background:url(img/cards/'+id+'_back.jpg) center center no-repeat;"></div>'
   ;
      
   return sImages;
}


function showCardEditor(obj)
{
	var width = 750;
	var height = 545;
	var title = 'edit card';
	var id = obj.attr('id');
	var cardName = obj.html();
   var orientation = obj.attr('alt');
	var cardImages = getCardImages(id);
   
	var contents =
		'<div class="close"></div>'+
		
		'<div id="" style="position:relative;">'+
			'<h1 style="text-align:center;padding:5px;">'+cardName+'</h1>'+
			'<p class="float-left" style="padding:6px;padding-left:20%;">Front image</p>'+
			'<p class="float-right" style="padding:6px;padding-right:20%;">Back image</p>'+
			'<div class="clear"></div>'+
			cardImages+
			'<div class="clear"></div>'+
		'</div>'+
		
		'<div style="top:420px; width:100%;">'+
			'<hr />'+
			'<div id="cmdOk" class="button center" style="width:120px;">Close</div>'+
		'</div>';
	
	//addLoader();
	
	var oPopup = buildPopup(width, height, title, contents, 808);
	
	//close button
	oPopup.find(".close").click(function(){
		destroyPopup(808);
	});
	
	//card images
	oPopup.find(".cardImage")
		.css({
			width:350,
			height:350,
			'float':'left',
			marginLeft:12,
			marginTop:10,
			cursor:'default'
		})
		.removeClass('hidden')
		.show();
		
	//setTimeout("removeLoader()", 750);
	
	//ok - close
	oPopup.find("#cmdOk").click(function(){
		destroyPopup(808);
	});

}

function getCardNotes(obj)
{
   var notes = '';
	obj.html('<img src="site/loading51.png class="loading" />');
   
   $.ajax({
      async: true,
      type: 'POST',
      url: 'ajax/user.php',
      data: {
         action: 'getnotes',
         usercard: id
      },
      success: function(response){
      
         notes = response;
			obj.html('<ul>'+notes+'</ul>');
         
      }
   });
   
}

function loadCardNotes(obj)
{
	var id = obj.attr("id");
	
	obj.html('<img src="site/loading51.gif" style="width:24px; height:24px; margin:10px;" />');
	
   $.ajax({
      async: true,
      type: 'POST',
      url: 'ajax/user.php',
      data: {
         action: 'getnotes',
         usercard: id
      },
      success: function(notes){
			
			if(notes != '')
			{
					
				obj.html('<ul>'+notes+'</ul>');
				
				$(".overlay[id='800']").find("#notes-holder").find(".del-button").unbind().click(function(){
					var alt = $(this).attr('alt');
					deleteCardNote(id, alt);
				});
				
			}
			else
			{
				obj.html('<p style="padding:10px;font-style:italic;font-size:12px;">This card has no notes</p>');
			}
		}
	});
}


function getAlbums()
{
   var albums = '';
   
   $.ajax({
      async: false,
      type: 'POST',
      url: 'ajax/user.php',
      data: {
         action: 'getalbums'
      },
      success: function(response){
      
         albums = response;
         
      }
   });
   
   return albums;
}

function loadAlbums(obj)
{
	var id = obj.attr('id');
	
	obj.html('<img src="site/loading51.gif" style="width:24px; height:24px; padding:0px;" />');
	
   $.ajax({
      async: true,
      type: 'POST',
      url: 'ajax/user.php',
      data: {
         action: 'getalbums'
      },
      success: function(albums){
			
         obj.empty().html( albums );
			
			obj.find(".album-item").find(".description").unbind().livequery('click',function(){
				//move card to category
				var deck = $(this).parent().attr('alt');
				moveCardToAlbum(id, deck);
			});
			
         obj.find(".del-button").remove();
			
      }
   });
   
}


function reloadAlbums()
{
   var sAlbums = getAlbums();
   $("#albums-holder").empty().html( sAlbums );
   resetBlockStyling('blockAlbums');
   
   //delete album button
   $("#albums-holder").find(".del-button").unbind().click(function(){
      showDeleteWindow($(this));
   });
   
	//my albums
	$("#albums-holder").find(".album-item").find("span").click(function(){
		showCategory( $(this).html() );
	});
   
   //if the popup is open
   if($(".overlay[id='800']").size())
   {
      $(".overlay[id='800']").find("#categories-holder").find(".categories").html( sAlbums );
      $(".overlay[id='800']").find("#categories-holder").find(".del-button").unbind().click(function(){
         showDeleteWindow($(this));
      });
   }
   
}

function addNewCategory(category)
{
   category = $.trim(category);
   if(category == '')
   {
      //empty
      return false;
   }
   else
   {
      $.ajax({
         async: false,
         type: 'POST',
         url: 'ajax/user.php',
         data: {
            action: 'addalbum',
            description: category
         },
         success: function(){
            
            reloadAlbums();
            $("#txtNewCategory").val('').focus();
            
         }
      });
   }
}


function reloadCardNotes(id)
{
   loadCardNotes( $(".overlay[id='800']").find("#notes-holder").find(".notes") );
}

function deleteAlbum(id)
{
   $.ajax({
      async: false,
      type: 'POST',
      url: 'ajax/user.php',
      data: {
         action: 'deletealbum',
         id: id
      },
      success: function(response){
      
         reloadAlbums();
      
      }
   });
}

function deleteCardNote(id, note)
{
   if(confirm("Are you sure you want to delete this note?"))
   {
      $.ajax({
         async: false,
         type: 'POST',
         url: 'ajax/user.php',
         data: {
            action: 'deletenote',
            noteid: note
         },
         success: function(response){
         
            reloadCardNotes(id);
            
         }
      });
   }
}

function addNoteToCard(id, note, f)
{
   note = $.trim(note);
   if(note == '')
   {
      //empty
   }
   else
   {
      $.ajax({
         async: false,
         type: 'POST',
         url: 'ajax/user.php',
         data: {
            action: 'addnote',
            usercard: id,
            note: note
         },
         success: function(response){
         
            reloadCardNotes(id);
            
            if(typeof(f) == "function")
            {
               f;
            }
            
         }
      });
   }
}


function moveCardToAlbum(card, deck)
{
   $.ajax({
      async: false,
      type: 'POST',
      url: 'ajax/user.php',
      data: {
         action: 'movecard',
         card: card,
         deck: deck
      },
      success: function(){
         
         $(".overlay[id='800']").find("#cmdMoveLink").click();
         
         var albumname = $(".overlay[id='800']").find("#categories-holder").find(".album-item[alt='"+deck+"']").find(".description").html();
         
         $("#cards-holder").find(".card[id='"+card+"']").attr('alt',albumname);
         
         //resetUsercards();
         
      }
   });
}


function resetUsercards()
{
   $.ajax({
      async: false,
      type: 'POST',
      url: 'ajax/user.php',
      data: {
         action: 'getusercards'
      },
      success: function(html){
      
         //$("#blocks-holder").empty().html( html );
         
         //$("#blocks-holder").find(".card").show();
         
      }
   });
}


function showCardViewer(obj)
{
    var id = obj.find('.usercard_id').val();
	var cardid = obj.find('.card_id').val();
	var yours = obj.find('.yours').val();
	var cardtype = obj.find('.cardtype').val();
	var width = 750;
	var height = 545;
	var title = 'view card';
	var cardName = obj.find('.cardstat').val();
	var cardImages = obj.html();
	
	isYours = "";
	if(yours=="1"){
		isYours = '<div id="cmdEditLink" alt="'+cardid+'" class="float-left">EDIT</div>';
	}
	var contents =
		'<div class="close"></div>'+
		
		'<div id="" style="position:relative;">'+
			'<h1 style="text-align:center;padding:5px;">'+cardName+'</h1>'+
			'<div class="clear"></div>'+
			'<p class="float-left" style="padding:6px;padding-left:21%;">Front image</p>'+
			'<p class="float-right" style="padding:6px;padding-right:22%;">Back image</p>'+
			'<div class="clear"></div>'+
			'<div style="position:relative; height:360px;">'+
				'<div class="cardImage" id="front" style="cursor:cursor; background:url(img/cards/'+cardid+'_front.jpg) center center no-repeat;"></div>'+
				'<div class="cardImage" id="back" style="cursor:cursor; background:url(img/cards/'+cardid+'_back.jpg) center center no-repeat;"></div>'+
			'</div>'+
		'</div>'+
		
		'<div style="top:420px; width:100%;">'+
			'<hr style="margin-bottom:15px;" />'+
			'<div id="cmdDeleteLink" alt="'+id+'" class="float-left">DELETE</div>'+
			isYours+
			'<div id="cmdMoveLink" alt="'+id+'" class="float-right">MOVE TO ALBUM</div>'+
			'<div id="cmdNotesLink" alt="'+id+'" class="float-right">NOTES</div>'+
		'</div>'+
		
      '<div id="categories-holder" style="bottom:-223px; left:-11px; padding:0px 10px; height:150px; background:black; width:731px; display:none;'+
         'border-left:1px solid #333;'+
         'border-right:1px solid #333;'+
         'border-bottom:1px solid #333;'+
         '-moz-border-radius: 0px 0px 5px 5px;'+'">'+
         
            '<div class="categories" id="'+id+'" style="position:relative; background:#000; height:90px; overflow-y:scroll; font-size:12px; padding:10px 5px 0px 10px;"></div>'+
               
            '<div style="background:#222222; bottom:0px; left:0px; width:740px; padding:5px; -moz-border-radius: 0px 0px 5px 5px;">'+
               '<input type="text" id="txtNewCategory" class="textbox" style="margin-bottom:1px; width:707px; margin-left:3px;" alt="New album name" title="New album name" value="New album name" />'+
               '<div id="cmdAddCategory" class="add-button" style="top:7px; right:10px;" title="Add a note to the card"></div>'+
            '</div>'+
         
      '</div>'+
      
      '<div id="notes-holder" style="bottom:-223px; left:-11px; padding:0px 10px; height:150px; background:black; width:731px; display:none;'+
         'border-left:1px solid #333;'+
         'border-right:1px solid #333;'+
         'border-bottom:1px solid #333;'+
         '-moz-border-radius: 0px 0px 5px 5px; ">'+
         
            '<div class="notes" id="'+id+'" style="position:relative; background:#000; height:100px; overflow-y:scroll; font-size:12px;"></div>'+
               
            '<div style="background:#222222; bottom:0px; left:0px; width:740px; padding:5px; -moz-border-radius: 0px 0px 5px 5px;">'+
               '<input type="text" id="txtNewNote" class="textbox" style="margin-bottom:1px; width:707px; margin-left:3px;" alt="Enter note here" title="Enter note here" value="Enter note here" />'+
               '<div id="cmdAddNote" class="add-button" style="top:7px; right:10px;" title="Add a note to the card"></div>'+
            '</div>'+
         
      '</div>';
	
	
	// build the popup
	oPopup = buildPopup(width, height, title, contents, 800);
	
	
	//init
	activateTextboxes(oPopup);
	
   var iMarginTop = parseInt(oPopup.find('.block').css('margin-top'),10);
   oPopup.find('.block').css('margin-top',iMarginTop-60)
   
	//orientation
	oPopup.find(".orientation-holder").remove();
	
	//close button
	oPopup.find(".close").click(function(){
		destroyPopup(800);
	});
	
	function shrinkPopup()
	{
		//shrink the popup window
		$(".overlay[id='800']").find(".block").each(function(){
			var height = 545;
			$(this).css({
				'height': height.toString()+'px',
				'margin-top': '-332px'
			});
			height = 491;
			$(this).find(".left").css('height', height.toString()+'px');
			$(this).find(".right").css('height', height.toString()+'px');
			return false;
		});
	}
	
	function expandPopup()
	{
		//expand the popup window
		$(".overlay[id='800']").find(".block").each(function(){
			var height = 695;
			$(this).css({
				'height': height.toString()+'px',
				'margin-top': '-390px'
			});
			height = 641;
			$(this).find(".left").css('height', height.toString()+'px');
			$(this).find(".right").css('height', height.toString()+'px');
			return false;
		});
	}
		
      //view notes
      oPopup.find("#cmdNotesLink").click(function(){
         
         oPopup.find("#categories-holder").hide();
         
         if(oPopup.find("#notes-holder").is(":visible"))
         {
            oPopup.find("#notes-holder").hide();
				shrinkPopup();
         }
         else
         {
            oPopup.find("#notes-holder").show();
				expandPopup();
				var sNotes = oPopup.find("#notes-holder").find(".notes").html();
				if(sNotes=='')
				{
					//load card notes
					loadCardNotes( oPopup.find("#notes-holder").find(".notes") );
				}
         }
      });
   
      //add note
      oPopup.find("#cmdAddNote").click(function(){
			var alt = oPopup.find("#txtNewNote").attr('alt');
         var sNote = oPopup.find("#txtNewNote").val();
			if(sNote!=alt)
			{
				var f = oPopup.find("#txtNewNote").val(alt);
				addNoteToCard(id, sNote, f);
			}
      });
      
      
   //move to category
   oPopup.find("#categories-holder").hide();
   
      //view categories
      oPopup.find("#cmdMoveLink").click(function(){
         
         oPopup.find("#notes-holder").hide();
         
         if(oPopup.find("#categories-holder").is(":visible"))
         {
            oPopup.find("#categories-holder").hide();
				shrinkPopup();
         }
         else
         {
				expandPopup();
				var sAlbums = oPopup.find("#categories-holder").find(".categories").html();
				if(sAlbums=='')
				{
					//load albums
					loadAlbums( oPopup.find("#categories-holder").find(".categories") );
				}
            oPopup.find("#categories-holder").show();
         }
      });
      
      //add category
      oPopup.find("#cmdAddCategory").click(function(){
			var alt = oPopup.find("#txtNewCategory").attr('alt');
         var sCategory = oPopup.find("#txtNewCategory").val();
			if(alt!=sCategory)
			{
				addNewCategory(sCategory);
			}
      });
      
   //delete
   oPopup.find("#cmdDeleteLink").click(function(){
		showDeleteWindow($(this),true);
   });
	
	oPopup.find("#cmdEditLink").click(function(){
		window.location = '?page=create&pro='+cardtype+'&edit='+cardid;
   });
	
	//card images
	oPopup.find(".cardImage")
		.css({
			width:350,
			height:350,
			'float':'left',
			marginLeft:12,
			marginTop:10,
			cursor:'default'
		})
		.removeClass('hidden')
		.show();
		
	//setTimeout("removeLoader()", 750);
	
	//ok - close
	oPopup.find("#cmdOk").click(function(){
		destroyPopup(800);
	});

}

function showCards(searchstring)
{
	if(typeof(searchstring)=="undefined")
	{
		searchstring = '';
	}
	else if(searchstring == $("#txtSearch").attr('alt'))
	{
		searchstring = '';
	}
	//clean searchstring	
	searchstring = $.trim(searchstring).toLowerCase();
	
	//check for valid value
	
	if(searchstring=='' || searchstring==$("#txtSearch").attr('alt'))
	{
		showAllCards();
	}
	else
	{
		//prepare for search results
		hideAllCards();
		
		//search for cards containing search string
		$("#cards-holder").find(".block").each(function(){
			var show = false;
			$(this).find(".cardstat").each(function(){
				var cardstat = $.trim($(this).val()).toLowerCase();
				if( cardstat.indexOf(searchstring) > -1 )
				{
					show = true;
				}
			});
			if(show)
			{
				$(this).show();
			}
		});
		
	}
	
	checkForNoCards(true);
}


function checkForNoCards(searched)
{
	if(typeof(searched)=="undefined")
	{
		searched = false;
	}
	
	var msg = '';
	var show = false;
	var found = 0;
	var total = 0;
	
	$("#cards-holder").find(".block").each(function(){
		if($(this).is(":visible"))
		{
			found++;
		}
		total++;
	});
	
	if(searched)
	{
		show = true;
		if(found == 0)
		{
			msg = 'No cards found with that description';
		}
		else
		{
			msg = found.toString()+' / '+total.toString()+' cards found';
		}
	}
	else
	{
		if(found == 0)
		{
			msg = 'No cards found';
		}
	}
	
	if(found == total)
	{
		show = false;
	}
	
	if(total == 0)
	{
		show = true;
		msg = 'No cards here. Create a card to get started.<div style="width:320px; height:60px; border-right:1px solid #fff; border-bottom:1px solid #fff;"></div>';
	}
	
	if(show)
	{
		$("#search-message").html(msg).show();
	}
	else
	{
		$("#search-message").hide();
	}
}


function showCategory(cardcat)
{
	if(typeof(cardcat)=="undefined" || cardcat.toLowerCase()=='all')
	{
		showAllCards();
	}
	else
	{
		//prepare for search results
		hideAllCards();
		cardcat = $.trim(cardcat).toLowerCase();
		
		//search for cards of category
		$("#cards-holder").find(".block").each(function(){
			var cat = $.trim($(this).attr('alt')).toLowerCase();
			if( cat == cardcat )
			{
				$(this).show();
			}
		});
	}

}


//READY -----

$(document).ready(function(){
   
	//search
	$("#txtSearch").keydown(function(e){
		var code = (e.keyCode ? e.keyCode : e.which);
		if(code == 13) {
			//Enter keycode
			$("#cmdSearch").click();
		}
	});
	
   $("#cmdSearch").click(function(){
      var searchstring = $("#txtSearch").val();
      showCards(searchstring);
   });
   
	
	//reset
	$("#cmdReset").click(function(){
		showAllCards();
		checkForNoCards();
		$("#txtSearch").val( $("#txtSearch").attr('alt') );
	});
	
	
   //albums
   
	//card blocks
	$("#page-contents").find(".cardImage").livequery('click',function()
	{
		showCardViewer($(this));
	});
	
	//hover over card image
	$(".cardImage").mouseover(function(){
		$(this).parent().find("#front").addClass('hidden');
		$(this).parent().find("#back").removeClass('hidden');
	})
	.mouseout(function(){
		$(this).parent().find("#back").addClass('hidden');
		$(this).parent().find("#front").removeClass('hidden');
	});

	
	//search field
	$("#txtSearch")
	.bind('click focus',function(){
		if($(this).val() == $(this).attr('alt'))
		{
			$(this).val('');
		}
	})
	.bind('blur',function(){
		if($.trim($(this).val()) == '')
		{
			$(this).val( $(this).attr('alt') );
		}
	});
   
   //add card button
   $("#cmdAddCard").click(function(){
      var width = 240;
	  var height = 150;
      var title = 'create a card';
		var contents =
			'<div class="close"></div>'+
         '<div id="free" class="button center" style="margin-left:5px; margin-right:5px;">Free Template</div>'+
         '<div id="custom" class="button center" style="margin-left:5px; margin-right:5px;display:none;">Custom Pro</div>'+
         '<div id="upload" class="button center" style="margin-left:5px; margin-right:5px;">Upload Pro</div>'
      ;

		//addLoader();
		
		//album delete confirmation popup
		oPopup = buildPopup(width, height, title, contents);
		
		//close button
		oPopup.find(".close").click(function(){
			destroyPopup();
		});
      
      //buttons
      oPopup.find(".button").click(function(){
         var id = $(this).attr('id');
			var url = '';
         switch(id)
         {
            case 'free':
               destroyPopup();
               addLoader();
               window.location = '?page=create';
               break;
            
            case 'custom':
					url = '?page=create&pro=1';
            case 'upload':
					if(url == '') url = '?page=create&pro=2';
					var pro = '<?php echo($_SESSION['pro']); ?>';
					var paid = '<?php echo($_SESSION['paid']); ?>';
					if(pro!='0' && paid=='1')
					{
						destroyPopup();
						addLoader();
						window.location = url;
					}
					else
					{
						showPaymentGateway();
					}
               break;
         }
      });
      
		//setTimeout("removeLoader()", 750);
		
   });
   
	
   //add album button
   $("#cmdAddAlbum").click(function(){
      var width = 240;
		var height = 160;
      var title = 'create a new album';
		var contents =
			'<div class="close"></div>'+
         '<p><input type="text" id="txtFolder" class="textbox" value="Album Name" alt="Album Name" style="width:195px;"/></p>'+
         '<div id="cmdAddLink" class="button-small center" style="width:50px;">Done</div>'
      ;
      
		//addLoader();
		
		//album add popup
		oPopup = buildPopup(width, height, title, contents);
		
		//close button
		oPopup.find(".close").click(function(){
			destroyPopup();
		});
      
		//add button
		oPopup.find("#cmdAddLink").click(function(){
         var alt = oPopup.find("#txtFolder").attr('alt');
         var foldername = $.trim(oPopup.find("#txtFolder").val());
         oPopup.find("#txtFolder").val(foldername);
         if(alt == foldername || foldername=='')
         {
            oPopup.find("#txtFolder").focus();
         }
         else
         {
            addNewCategory(foldername);
            destroyPopup();
         }
		});
		
      //setTimeout("removeLoader()", 750);
      
      //folder name
      oPopup.find("#txtFolder")
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
		
   });
   
   
   //delete card button
   $(".del-button[alt='card']").click(function(){
      showDeleteWindow($(this));
   });
	
	
   
	//my cards
	
	$(".mycard-item").click(function(){
		showCardViewer( $(this) );
	});
	

	//INIT -----
   
   showAllCards();
   reloadAlbums();
	
});





function showDeleteWindow(obj, usercard)
{
   var id = obj.attr('id');
   var width = 320;
   var height = 200;
   var alt = obj.attr('alt');
	
	if(typeof(usercard)=="undefined")
	{
		usercard = false;
	}
	else
	{
		alt = "card";
		id = obj.attr('alt');
	}
   
   var title = 'Confirm';
   var name = (alt=="card")
      ? obj.parent().find(".mycard-item[id='"+id+"']").html()
      : obj.parent().find(".description").text();
	if(usercard)
	{
		name = $(".overlay[id='800']").find('h1').html();
	}
   
   var message = 'Are you sure you want to delete this '+alt+'?<br /><br /><strong>'+name+'</strong>';
   
   var contents =
      '<div class="close"></div>'+
      '<p style="font-size:13px; text-align:center; margin-left:25px; margin-right:25px;">'+message+'</p>'+
      '<div id="cmdDelete" class="button-small center" style="width:50px;">Delete</div>'
   ;
   
   //addLoader();
   
   //album delete confirmation popup
   oPopup = buildPopup(width, height, title, contents);
   
   //close button
   oPopup.find(".close").click(function(){
      destroyPopup();
   });
   
   //cancel button
   oPopup.find("#cmdCancel").click(function(){
      destroyPopup();
   });
      
   //setTimeout("removeLoader()", 750);
   
   //delete button
   oPopup.find("#cmdDelete").click(function(){
      
      if(alt == "album")
      {
         
         deleteAlbum(id);
         
         destroyPopup();
         
      }
      else
      {
			if(usercard)
			{
				//delete usercard from database
				$.ajax({
					type: 'POST',
					url: 'ajax/user.php',
					data: {
						action: 'deleteusercard',
						id: id
					},
					success: function(){
						
						destroyPopup();
						
						//addLoader();
						
						window.location.reload();
						
					}
				});
			}
			else
			{
				//delete card from database
				$.ajax({
					type: 'POST',
					url: 'ajax/user.php',
					data: {
						action: 'delete'+alt,
						id: id
					},
					success: function(response){
						
						destroyPopup();
						
						//addLoader();
						
						window.location.reload();
						
					}
				});
			}
      }
   });
   
}

</script>