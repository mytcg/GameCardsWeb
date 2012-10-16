<?php
$iUserID = $user['user_id'];
$iDeckID = $_GET['deck'];

// $sql = "SELECT UC.card_id, COUNT(UC.card_id) AS 'avail', CONCAT(I.description,'cards/',C.image,'_web.jpg') AS 'thumbnail', C.description, C.image
		// FROM mytcg_usercard UC 
		// JOIN mytcg_card C USING(card_id)
		// JOIN mytcg_imageserver I ON C.thumbnail_imageserver_id = I.imageserver_id
		// WHERE UC.deck_id IS NULL
		// AND UC.usercardstatus_id = 1
		// AND UC.user_id = ".$iUserID."
		// GROUP BY UC.card_id
		// ORDER BY UC.card_id ASC";
// $cards = myqu($sql);
// for($i=0;$i<sizeof($cards);$i++){
	// $card = $cards[$i];
// }

$query = "SELECT CD.active
		  FROM mytcg_deck D
		  INNER JOIN mytcg_competitiondeck CD ON (D.competitiondeck_id = CD.competitiondeck_id)
		  WHERE D.deck_id = ".$iDeckID;
$response = myqu($query);
$active = $response[0]['active'];

function hasCard($positionID,$deckID){
	$sql = "SELECT CONCAT(I.description,'cards/',C.image,'_web.jpg') AS 'thumbnail', C.description, C.image, C.card_id
			FROM mytcg_deckcard DC 
			INNER JOIN mytcg_card C ON (DC.card_id = C.card_id)
			JOIN mytcg_imageserver I ON (C.front_imageserver_id = I.imageserver_id)
			WHERE DC.deck_id = ".$deckID."
			AND DC.position_id = ".$positionID;
	$card = myqu($sql);
	return $card[0];
}
?>
<div class="headTitle">
		<div class="headDeck">
			<span>DECK EDITOR</span>
		</div>
</div>

<div id="deckPlate">
	<div class="yourDeck">Your Deck</div>
	<div class="yourDeckCard">Your Cards</div>
	<?php if($active=="1"){ ?><div class="saveDeckButton">Save your deck</div><?php } ?>
	<div class="backDeckButton">Back to Deck screen</div>
	<div class="hideOverflow">
		<div id="deckCards">
			<?php
				$sql = "SELECT description, position_id
						FROM mytcg_position";
				$positions = myqu($sql);
				for($i=0;$i<sizeof($positions);$i++){
					$card = hasCard($positions[$i]['position_id'],$iDeckID);
					if(sizeof($card) > 0){
						echo("<div id='{$positions[$i]['position_id']}' alt='{$positions[$i]['description']}' class='deckcardholder'><img id='{$card['card_id']}' alt='deck' src='{$card['thumbnail']}' border=0 /></div>");
					}else{
						echo("<div id='{$positions[$i]['position_id']}' alt='{$positions[$i]['description']}' class='deckcardholder'>{$positions[$i]['description']}</div>");
					}
					if(sizeof($cards[$i]) > 0){
						$card = $cards[$i];
					}
				}		
			?>
		</div>
		<div id="playerCards">
			<?php
			$sql = "SELECT UC.card_id, COUNT(UC.card_id) AS 'avail', CONCAT(I.description,'cards/',C.image,'_web.jpg') AS 'thumbnail', C.description, C.image
					FROM mytcg_usercard UC
					JOIN mytcg_card C USING(card_id)
					JOIN mytcg_imageserver I ON C.thumbnail_imageserver_id = I.imageserver_id
					WHERE UC.usercardstatus_id = 1
					AND UC.user_id = ".$iUserID."
					GROUP BY UC.card_id
					ORDER BY C.description ASC";
			$cards = myqu($sql);
			for($i=0;$i<sizeof($cards);$i++){
				$card = $cards[$i];
				echo("<div id='card_{$card['card_id']}' class='cardholder'><img id='{$card['card_id']}' alt='card' src='{$card['thumbnail']}' border=0 /></div>");
			}
			?>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	var deck_id = <?php echo($iDeckID); ?>;
	var saveButton = null;
	$("#playerCards").jScrollPane();
	$(".jspContainer").css("overflow","visible");
	$(".jspScrollable").css("overflow","visible");
	$("#playerCards").css("overflow","visible");
	//var card_id = $(this).parent().find(".card_id").val();
	
	$('.backDeckButton').click(function(){
		window.location = "index.php?page=deck";
	});
	
	$('.cardholder img, .deckcardholder img').click(function(){
		var cardID = parseInt($(this).attr('id'));
		if(cardID > 0){
			App.showCardModal(cardID,true);
		}
	});
	
	<?php if($active=="1"){ ?>
	$(".saveDeckButton").click(function(){
		saveButton = $(this);
		saveButton.html("Saving");
		var sString = "";
		$(".deckcardholder img").each(function(){
			var pos = $(this).parent().attr("id");
			var card_id = $(this).attr("id");
			
			sString += pos + "||" + card_id + "@";
		});
		
		App.callAjax('_app/deckbuild.php?deck='+deck_id+'&list='+sString,function(response){
			saveButton.html("Saved");
		});
	});
	
	var obj = null;
	var sType = null;
	var startID = null;
	
	function setDraggables(){
		$(".cardholder img, .deckcardholder img").draggable({
			stack:".cardholder img",
			opacity: 1,
			helper: "clone",
			start: function(event,ui){
				obj = $(this);
				$(this).css({zIndex:100});
				startID = $(this).parent().attr("id");
			}
		});
	}
	setDraggables();
	
	$(".deckcardholder").droppable({
		tolerance: 'pointer',
		hoverClass: "drop",
		drop: function(event,ui){
			//duplicate
			var duplicate = false;
			var dropID = $(this).attr("id");
			var dragID = parseInt(ui.draggable.attr("id"));
			var sSource = ui.draggable.attr("src");
			
			//Look for duplicate card
			var d = parseInt($(".deckcardholder").find("#"+dragID).parent().attr("id"));
			var e = ui.draggable.attr("alt");
			if((d > 0)&&(e=="card")){
				duplicate = true;
			}
			
			if((startID=="23")&&(e=="deck")){
				if(d!=23){
					duplicate = true;
				}
			}
			
			//skip if Man of the Match
			if(dropID=="23"){
				duplicate = false;
			}
			
			if(duplicate){
				var divBody = document.body;
				var divErrorWindow = App.createDiv(divBody,"modal-window","notice-modal-window");
				$(divErrorWindow).css({color:"#000",fontWeight:"bold",top:380,left:80,height:"20px",width:"150px"});
	       		$(divErrorWindow).html("Card already in deck!");
		       	$(divErrorWindow).delay(2000).fadeOut('slow',function(){ $(divErrorWindow).remove(); });
			}else{
				$(this).html(obj);
				$(this).find("img").attr("alt","deck");
				$("#card_"+dragID).html("<img alt='card' id='"+dragID+"' border='0' src='"+sSource+"' />");
				setDraggables();
			}
		}
	});
	$("#playerCards").droppable({
		drop: function(event,ui){
			if(ui.draggable.attr("alt")=="deck"){
				var thisOBJ = $("#deckCards #"+startID);
				thisOBJ.html(thisOBJ.attr("alt"));
			}
		}
	});
	<?php } ?>
});		
</script>








