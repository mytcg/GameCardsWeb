<?php

//

?>
<div id="page-title">Where did your business card end up?</div>
<?php

   //for generating letter sub sections
   $aChars = array('#');
   for($chr='a'; $chr<='z'; $chr++)
   {
      $aChars[] = $chr;
      if($chr == 'z') break;
   }
   
   //user's cards and track window
   $trackWindows = '';
   $aCards = getCards();
	$totalCounter = 0;
   //echo '<pre>'.print_r($aCards,true).'</pre>';
	if(sizeof($aCards) > 0)
	{
      $cardThumbs = '';
		foreach($aCards as $card)
		{
			$trackCounter = 0;
         $aTracking = getCardTracking($card['card_id']);
         //echo '<pre>'.print_r($aTracking,true).'</pre>';
         $cardThumbs.= '<img src="'.$card['imageserver'].'cards/'.$card['image'].'_thumb.jpg" class="thumbnail" id="'.$card['card_id'].'" title="'.$card['description'].'" />';
         $trackWindows.= '<div id="'.$card['card_id'].'" class="trackingData"><div class="tracking-sub"></div>';
			foreach($aChars as $chr)
         {
            $trackingData = array();
				$i = 0;
				if(sizeof($aTracking) > 0)
				{
					foreach($aTracking as $track)
					{
						$detail = $track['detail'];
						if($track['username']!=NULL)
						{
							$detail = $track['username'].' ('.$track['detail'].')';
						}
						
						$title = 'Sent: '.$track['date'].' - Note: '.$track['note'];
						$c = strtolower(substr($detail,0,1));
						if($c == $chr || (is_numeric($c) && $chr=='#'))
						{
							//check for lower level sharing
							$subnumber = '';
							$user_id == '';
							if($track['receiver']!=NULL)
							{
								$aSubTracking = getCardSubTracking($card['card_id'],$track['receiver']);
								if(sizeof($aSubTracking) > 0)
								{
									$subnumber = '<div class="number">'.sizeof($aSubTracking).'</div>';
								}
								$user_id = $track['receiver'];
							}
							$statusMsg = ($track['status_id']==1) ? "<img src='site/check.png' align='absmiddle' />" : "<img src='site/delete.png' align='absmiddle' />" ;
							$thumb = $card['imageserver'].'cards/'.$card['image'].'_thumb.jpg';
							$trackingData[] = <<<STR
							<div class="track-item" id="{$track['tradecard_id']}" alt="{$card['card_id']}">
								<input type="hidden" class="user_id" value="{$user_id}" />
								<div class="thumb" style="background:url({$thumb}) center center no-repeat;"></div>
								<p title="{$title}">{$detail} - {$statusMsg}</p>
								{$subnumber}
							</div>
STR;
							$trackCounter++;
							$totalCounter++;
						}
					}
				}
				
            //tracking window
				if(sizeof($trackingData) > 0)
				{
					$trackingData = (sizeof($trackingData) > 0) ? implode('<hr />',$trackingData) : '';
					$trackWindows.= '<div class="trackBlock">';
						$trackWindows.= buildBlock(280, '', 'float-right', '', $trackingData, 'margin-right:580px; min-height:15px;', $chr, '', true);
						$trackWindows.= '<h1>'.strtoupper($chr).'</h1>';
						$trackWindows.= clear(true);
						$trackWindows.= '<div class="clear"></div>';
					$trackWindows.= '</div>';
				}
         }
			if($trackCounter == 0)
			{
				$trackWindows.= '<div style="font-size: 14px; position: relative; padding: 24px 0px 35px; text-align: center; font-style: italic;" id="search-message">You have not shared this card yet.</div>';
			}
         $trackWindows.= '</div>';
		}
	}
   
	if($totalCounter > 0)
	{
		$trackingContents.= $trackWindows;
	}
	else
	{
		$trackingContents = '<div style="font-size: 14px; position: relative; padding: 24px 0px 35px; text-align: center; font-style: italic;" id="search-message">You have not shared this card yet.</div>';
	}
	
	$contents =
<<<STR
   <div id="thumbnails-holder" class="float-right">
      {$cardThumbs}
   </div>
   <h2>MY CARDS</h2>
   <div class="clear"></div>
   <hr />
   <div class="tracking-holder">
		{$trackingContents}
   </div>
STR;
   
	buildBlock(920, 600, 'float-left', '', $contents);
	clear();

?>
<script>

//VARIABLES -----

var sLoadingIcon = '<img class="loading" src="site/loading51.gif" style="margin:0px; width:24px; height:24px;" />';

//FUNCTIONS -----

function selectCard(id)
{
   if(typeof(id) != "undefined" && id != '')
   {
      $("#thumbnails-holder").find(".thumbnail").removeClass('active');
      $("#thumbnails-holder").find(".thumbnail[id='"+id+"']").addClass('active');
      $(".trackingData").hide();
      $(".trackingData[id='"+id+"']").show('fast');
   }
}

function selectData(id)
{
   $(this).addClass('active');
}

//READY -----

$(document).ready(function(){
   
   //thumnails
   $("#thumbnails-holder").find(".thumbnail").click(function(){
      selectCard($(this).attr('id'));
   });
   
   //data items
   $(".track-item").click(function(){
		var alt = $(this).attr('alt');
		var offset = $(this).offset();
		var iTop = offset.top - 258;
		
		if(!$(this).hasClass('active'))
		{
			$(".track-item").removeClass('active');
			$(this).addClass('active');
			
			if($(this).find(".number").size())
			{
				var user_id = $(this).find(".user_id").val();
				var card_id = $(this).attr('alt');
				
				$(".trackingData[id='"+alt+"']").find(".tracking-sub").html(sLoadingIcon).css('top',iTop.toString()+'px').show();
				
				$.ajax({
					type: 'POST',
					url: sUrl+'user.php',
					data: {
						action: 'getsubtrack',
						card: card_id,
						user: user_id
					},
					success: function(result){
						
						$(".trackingData[id='"+alt+"']").find(".tracking-sub").html(result);
						
					}
				});
			}
			else
			{
				$(".trackingData[id='"+alt+"']").find(".tracking-sub").empty().hide();
			}
		}
   });
   
   //INIT -----
   
   //get first card
   var firstCard;
   $("#thumbnails-holder").find(".thumbnail").each(function(){
      firstCard = $(this).attr('id');
      return false;
   });
   //select first card
   selectCard(firstCard);
   
});
</script>