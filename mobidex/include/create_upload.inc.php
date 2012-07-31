<?php

//Create A Card
//FREE
//PRO
//UPLOAD

?>
<div id="page-title">Upload your business card</div>

<!-- UPLOAD Card Creator -->

<div id="frmPro" style="position:relative;">

   <div id="frmUploadImage" style="display:none;">
      <div class="close"></div>
		<iframe style="width: 380px; height: 135px; border: 0px none;" src="ajaxfileupload.php" name="upload_target" id="upload_target" alt="front"></iframe>
   </div>
	
	<div id="1" class="frmStep" style="position:relative;">
	
		<div class="float-right">
			<div class="cardImagesHolder landscape">
				<p class="tab active" alt="tabFront">FRONT</p>
				<p class="tab" alt="tabBack">BACK</p>
				<div class="clear"></div>
				<div id="tabFront" style="width:530px; padding:15px; height:390px; background:#000;">
					<div class="cardImageHolder" id="front">
						<img id="front" class="ownImage float-left default" src="" />
					</div>
				</div>
				<div id="tabBack" style="width:530px; padding:15px; height:390px; background:#000; display:none;">
					<div class="cardImageHolder" id="back">
						<img id="back" class="ownImage float-left" src="" />
					</div>
				</div>
			</div>
			<div class="flip-card">Flip Card</div>
		</div>
		
		<?php
		
		//orientation block
		
		$contents = <<<STR
		<div style="position:relative;padding-bottom:5px;">
			<p style="padding:4px;">Orientation</p>
			<div class="orientation-holder">
				<div id="landscape" class="active" alt="portrait" title="Landscape"></div>
				<div id="portrait" alt="landscape" title="portrait"></div>
			</div>
		</div>
STR;
		buildBlock(310, 45, 'float-left', '', $contents);
		
		//front and back tabs
		
		$aColors = array
		(
			'Background colour'=>'-1',
			'White'=>'#FFFFFF',
			'Silver'=>'#C0C0C0',
			'Gray'=>'#808080',
			'Black'=>'#000000',
			'Red'=>'#FF0000',
			'Maroon'=>'#800000',
			'Yellow'=>'#FFFF00',
			'Olive'=>'#808000',
			'Lime'=>'#00FF00',
			'Green'=>'#008000',
			'Aqua'=>'#00FFFF',
			'Teal'=>'#008080',
			'Blue'=>'#0000FF',
			'Navy'=>'#000080',
			'Fuchsia'=>'#FF00FF',
			'Purple'=>'#800080'
		);
		$colorOptions = '';
		foreach($aColors as $color=>$hex)
		{
			$colorOptions.= '<option value="'.$hex.'">'.$color.'</option>';
		}
		
		$aLayouts = array
		(
			'1'=>'active',
			'2'=>'',
			'3'=>'',
			'4'=>'',
			'5'=>'',
			'6'=>''
		);
		$layoutOptions = '';
		foreach($aLayouts as $layout=>$active)
		{
			$layoutOptions.= '<div class="optLayout '.$active.'" id="layout'.$layout.'"></div>';
		}
		$layoutOptions.= '<div class="clear"></div>';
		
				<p class="tab active" alt="tabFront">FRONT</p>
				<p class="tab" alt="tabBack">BACK</p>
				<div class="clear"></div>
				<div id="tabFront" style="width:530px; padding:15px; height:390px; background:#000;">
					<div class="cardImageHolder" id="front">
						<img id="front" class="ownImage float-left default" src="" />
					</div>
				</div>
				<div id="tabBack" style="width:530px; padding:15px; height:390px; background:#000; display:none;">
					<div class="cardImageHolder" id="back">
						<img id="back" class="ownImage float-left" src="" />
					</div>
				</div>
		
		$contents = <<<STR
			<p class="tab active" alt="tabFront">FRONT</p>
			<p class="tab" alt="tabBack">BACK</p>
			<div style="position:relative;padding-bottom:0px;">
				<select name="cboBackgroundColor" style="margin-bottom:5px;">
					{$colorOptions}
				</select>
			</div>
			<hr />
			<div style="position:relative; padding-bottom:5px; display:none;">
				{$layoutOptions}
			</div>
			<div id="cmdUploadImage" class="button center" style="width:120px; margin-bottom:10px;">Upload Image</div>
			<hr />
			<div style="position:relative; margin-left:20px; margin-right:20px; padding-top:5px;">
				<div class="min-icon float-left"></div>
				<div class="pos-icon float-right"></div>
				<div style="position:relative; padding-top:6px; margin:0px 40px;">
					<div id="slider" style="position:relative; border:1px solid #666!important; background:transparent!important;"></div>
				</div>
				<div class="clear"></div>
				<br />
				<div style="position:relative; padding-left:50px;">
					<div id="cmdCenterImage" class="button-center float-left">Center</div>
					<div id="cmdRotateImage" class="button-rotate float-left">Rotate</div>
				</div>
			</div>
STR;

		buildBlock(310, 290, 'float-left', '', $contents);
		
		clear();
		
		?>

	</div>
	
</div>





<div id="frmAll" style="display:none;">
	
	<div class="frmStep" id="2" style="position:relative; display:none;">
		<div class="center" style="position:relative;">
			<div class="form" id="frmDetails">
				<div class="table-container">
					
					<table class="center-all" cellpadding="0" cellspacing="0">
						<tbody>
							<tr>
								<td class="left">&nbsp;</td>
								<td class="middle" style="width:50px;">&nbsp;</td>
								<td class="middle" style="width:50px;">&nbsp;</td>
								<td class="middle"><input type="text" class="textbox" name="txtCardName" title="Card Name" value="Card Name" alt="0" /> &nbsp; <span>Card Name</span></td>
								<td class="right">&nbsp;</td>
							</tr>
							<tr>
								<td class="left">&nbsp;</td>
								<td class="middle">&nbsp;</td>
								<td class="middle">&nbsp;</td>
								<td class="middle">
									<select id="cboCategories" style="margin-top:-1px;">
									<?php
									
										$cats = getAllCategories();
										//echo '<pre>'.print_r($cats,true).'</pre>';
										echo '<option value="-1">Select Category...</option>';
										if(sizeof($cats) > 0)
										{
											foreach($cats as $cat)
											{
												echo '<option value="'.$cat['category_id'].'">'.$cat['description'].'</option>';
											}
										}
									
									?>
									</select> &nbsp; <span>Card Category</span>
								</td>
								<td class="right">&nbsp;</td>
							</tr>
							<tr>
								<td class="left">&nbsp;</td>
								<td class="middle">&nbsp;</td>
								<td class="middle">&nbsp;</td>
								<td class="middle"><input type="text" class="textbox" name="txtTags" title="Search tags" value="" alt="" /> &nbsp; <span>Search Tags</span></td>
								<td class="right">&nbsp;</td>
							</tr>
							<tr class="hideforupload">
								<td>&nbsp;</td>
								<td style="text-align:center;font-weight:bold;">FRONT</td>
								<td style="text-align:center;font-weight:bold;">BACK</td>
								<td style="text-align:center;font-weight:bold;"><span style="padding-right:110px;">Information Fields</span></td>
								<td>&nbsp;</td>
							</tr>
						</tbody>
					</table>
					
					<div class="hideforupload" style="position:relative; height:326px!important; overflow-y:scroll; padding-bottom:4px;">
					<table id="table-fields" class="center-all" cellpadding="0" cellspacing="0">
						<tbody
							<tr>
								<td class="left"></td>
								<td class="middle"><div id="f" class="checkbox x-icon"></div></td>
								<td class="middle"><div id="b" class="checkbox x-icon"></div></td>
								<td class="middle"><input type="text" class="textbox" name="txtName" title="Name" value="Name" alt="7" /> &nbsp; <span>e.g. Bob Dylan</span></td>
								<td class="right"><div class="close-small" title="Delete field"></div></td>
							</tr>
							<tr>
								<td class="left"></td>
								<td class="middle"><div id="f" class="checkbox x-icon"></div></td>
								<td class="middle"><div id="b" class="checkbox x-icon"></div></td>
								<td class="middle"><input type="text" class="textbox" name="txtCompany" title="Company" value="Company" alt="9" /> &nbsp; <span>e.g. Free Spirit</span></td>
								<td class="right"><div class="close-small" title="Delete field"></div></td>
							</tr>
							<tr>
								<td class="left"></td>
								<td class="middle"><div id="f" class="checkbox x-icon"></div></td>
								<td class="middle"><div id="b" class="checkbox x-icon"></div></td>
								<td class="middle"><input type="text" class="textbox" name="txtDesignation" title="Designation" value="Designation" alt="10" /> &nbsp; <span>e.g. Singer / Songwriter</span></td>
								<td class="right"><div class="close-small" title="Delete field"></div></td>
							</tr>
							<tr>
								<td class="left"></td>
								<td class="middle"><div id="f" class="checkbox x-icon"></div></td>
								<td class="middle"><div id="b" class="checkbox x-icon"></div></td>
								<td class="middle"><input type="text" class="textbox" name="txtMobile" title="Mobile Number" value="Mobile Number" alt="12" /> &nbsp; <span>e.g. 082 555 1234</span></td>
								<td class="right"><div class="close-small" title="Delete field"></div></td>
							</tr>
							<tr>
								<td class="left"></td>
								<td class="middle"><div id="f" class="checkbox x-icon"></div></td>
								<td class="middle"><div id="b" class="checkbox x-icon"></div></td>
								<td class="middle"><input type="text" class="textbox" name="txtEmail" title="Email" value="Email" alt="14" /> &nbsp; <span>e.g. bob@email.com</span></td>
								<td class="right"><div class="close-small" title="Delete field"></div></td>
							</tr>
							<tr>
								<td class="left"></td>
								<td class="middle"><div id="f" class="checkbox x-icon"></div></td>
								<td class="middle"><div id="b" class="checkbox x-icon"></div></td>
								<td class="middle"><input type="text" class="textbox" name="txtAddress" title="Address" value="Address" alt="17" /> &nbsp; <span>e.g. 21 Long Street</span></td>
								<td class="right"><div class="close-small" title="Delete field"></div></td>
							</tr>
							<tr>
								<td class="left"></td>
								<td class="middle" style="width:50px;"><div id="f" class="checkbox x-icon" style="display:none;"></div></td>
								<td class="middle" style="width:50px;"><div id="b" class="checkbox x-icon" style="display:none;"></div></td>
								<td class="middle">
									<input type="text" class="textbox hidden" name="" title="" />
									<select id="cboFieldtypes" style="float:left;">
									<?php
										
										echo getFieldtypesCombo(true); //contentsOnly=true
										
									?>
									</select>
									<div id="cmdAdd"></div>
								</td>
								<td class="right"><div class="close-small hidden" title="Delete field"></div></td>
							</tr>
						</tbody>
					</table>
					</div>
					
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
	<div class="frmStep" id="3" style="position:relative; height:415px; display:none;">
	<?php
		
		$aFonts = array
		(
			'Arial'=>'arial',
			'Verdana'=>'verdana',
			'Times New Roman'=>'times',
			'Georgia'=>'georgia',
			'Courier New'=>'courier',
			'Lucida Console'=>'lucida',
		);
		$fonts = '';
		foreach($aFonts as $font=>$file)
		{
			$fonts.= '<option value="'.$font.'" alt="'.$file.'">'.$font.'</option>';
		}
		
/*		
Points 	Pixels 	Ems 	Percent
6pt 		8px 	0.5em 	50%
7pt 		9px 	0.55em 	55%
7.5pt 	10px 	0.625em 	62.5%
8pt 		11px 	0.7em 	70%
9pt 		12px 	0.75em 	75%
10pt 		13px 	0.8em 	80%
10.5pt 	14px 	0.875em 	87.5%
11pt 		15px 	0.95em 	95%
12pt 		16px 	1em 		100%
13pt 		17px 	1.05em 	105%
13.5pt 	18px 	1.125em 	112.5%
14pt 		19px 	1.2em 	120%
14.5pt 	20px 	1.25em 	125%
15pt 		21px 	1.3em 	130%
16pt 		22px 	1.4em 	140%
17pt 		23px 	1.45em 	145%
18pt 		24px 	1.5em 	150%
20pt 		26px 	1.6em 	160%
22pt 		29px 	1.8em 	180%
24pt 		32px 	2em 		200%
26pt 		35px 	2.2em 	220%
27pt 		36px 	2.25em 	225%
28pt 		37px 	2.3em 	230%
29pt 		38px 	2.35em 	235%
30pt 		40px 	2.45em 	245%
32pt 		42px 	2.55em 	255%
34pt 		45px 	2.75em 	275%
36pt 		48px 	3em 	300%
*/	
		$aSizes = array
		(
			'6'=>'8',
			'8'=>'11',
			'9'=>'12',
			'10'=>'13',
			'11'=>'15',
			'12'=>'16',
			'14'=>'19',
			'16'=>'22',
			'18'=>'24',
			'20'=>'26',
			'22'=>'29',
			'24'=>'32',
			'28'=>'37',
			'36'=>'48'
		);
		$sizes = '';
		foreach($aSizes as $pt=>$px)
		{
			$sizes.= '<option value="'.$px.'">'.$pt.' pt</option>';
		}
		
		//$aFieldtypes = getFiletypesCombo(true);
		$fieldtypes = '';
		
		//font settings
		$contents = <<<STR
			<select id="cboFontFamily">
				{$fonts}
			</select>
			<select id="cboFontSize">
				{$sizes}
			</select>
			<select id="cboFontColour">
				{$colours}
			</select>
			<hr />
			<select id="cboFieldtypes" style="display:none;">
				{$fieldtypes}
			</select>
			<div class="button-small" id="cmdSelectAll" style="position:relative; width:120px;">Select All Fields</div>
STR;
		buildBlock(225, 210, 'float-left', 'font', $contents);
		
		?>
		<div id="fieldsImages" class="float-left" style="width:540px; height:515px; margin-left: 20px;">
			IMAGES PLACEHOLDER
		</div>
		<div class="clear"></div>
	</div>
	
	<div class="frmStep" id="4" style="position:relative; display:none;">
		<div class="clear"></div>
		<div id="cardImages"></div>
		<div class="clear"></div>
	</div>
	
	<div style="top:515px; width:100%;">
		<hr />
		<div class="buttons-holder">
			<div id="cmdPrev" alt="0" class="button button-disabled float-left">&lt; PREV STEP</div>
			<div id="cmdNext" alt="2" class="button float-left">NEXT STEP &gt;</div>
		</div>
		<div id="cmdDefault" class="button-small" style="width:120px; margin-top:0px; left:5px; top:-40px; display:none;">Default Display</div>
	</div>
	
</div>






<!-- FREE Card Creator -->

<div id="frmFree" style="display:none;">
	
	<div class="close"></div>
	
	<div class="frmStep" id="1" style="position:relative;">
		<h1 style="text-align:center;padding:5px;"></h1>
		<p class="float-left" style="padding:25px 0px 20px 26%;">Front image</p>
		<p class="float-right" style="padding:25px 26% 20px 0px;">Back image</p>
		<div class="clear"></div>
		<div id="templateImages" style="position:relative; width:725px; padding-right:5px; margin-left:auto; margin-right:auto;"></div>
		<div class="clear"></div>
	</div>
	
</div>















<script>

//GLOBAL VARIABLES-----	

	var iPro = '<?=$_SESSION['pro']?>';
	var iPaid = '<?=$_SESSION['paid']?>';
	
var aFrontFields = [];
var aBackFields = [];
var sFrontImage;
var sBackImage;
var sOrientation = 'landscape';
var dontResize = false;
var loadImage;
var sImageURL;

//READY-----

$(document).ready(function(){

	
	//INIT VARIABLES-----
	
	
	
	
	//FUNCTIONS -----
	
	function getImageSrc(card, pro)
	{
		var sUrl;
		var sGet;
		var sImage;
		var fields = '';
		var side = card.attr('id');
		
		if(typeof(pro)=="undefined")
		{
			pro = '0';
		}
		
		if(side == 'front')
		{
			if(aFrontFields.length > 0)
			{
				fields = aFrontFields.join('^!^');
			}
		}
		else if(side == 'back')
		{
			if(aBackFields.length > 0)
			{
				fields = aBackFields.join('^!^');
			}
		}
		
		if(pro == '1')
		{
			sImage = card.attr('src');
			var t = sImage.split('?');
			if(t.length > 0)
			{
				sImage = t[0];
			}
			sUrl = 'generate_image_pro.php';
			sGet = '?i='+sImage;
			sGet += '&o='+sOrientation;
			sGet += '&t='+parseInt(card.css('top'),10);
			sGet += '&l='+parseInt(card.css('left'),10);
			sGet += '&s='+imgScale[side];
			sGet += '&r='+imgRotate[side];
		}
		else
		{
			var orientation;
			if(sOrientation=='portrait')
			{
				orientation = '1';
			}
			else
			{
				orientation = '2';
			}
			
			sImage = card.css('background-image');
			sImage = sImage.replace('url("', '');
			sImage = sImage.replace('url(', '');
			sImage = sImage.replace('")', '');
			sImage = sImage.replace(')', '');
			var t = sImage.split("/");
			
			if(pro == '2')
			{
				sImage = 'img/temp/'+t[t.length-1];
			}
			else
			{
				sImage = 'templates/'+ t[t.length-2]+ '/' +t[t.length-1];
			}
			
			sUrl = 'generate_image.php';
			sGet = '?file='+sImage+'&orientation='+orientation+'&fields='+fields;
		}
		//alert(sUrl+sGet);
		//save the image to server
		$.ajax({
			async: false,
			type: 'POST',
			url: 'ajax/user.php',
			data: {
				action: 'saveimage',
				side: side,
				getstring: sGet,
				image: sImage,
				pro: pro
			},
			success: function(filename){
				
				if(pro=='1')
				{
					var url = 'http://127.0.0.1/mobidex/img/temp/';
					
					//add image to step 3
					oPopup.find("#fieldsImages").append
					(
						'<div class="ownImage" id="'+side+'" style="position:relative; float:left; background:url('+url+filename+') no-repeat; margin:0px 10px 10px 0px;"></div>'
					);
				}
				else
				{
					if(side == 'front')
					{
						sFrontImage = filename;
					}
					else
					{
						sBackImage = filename;
					}
				}
			}
		});
		
		return sUrl + sGet;
	}

	
	function showStep(form, step2hide, step2show)
	{
		var step = step2show;
		step2hide = "."+form+"#"+step2hide;
		step2show = "."+form+"#"+step2show;
		
		//hide current step
		oPopup.find(step2hide).hide('fast',function(){
			
			//prepare step to show
			oPopup.find("#cmdDefault").hide();
			var sTitle = (iPro != '0') ? 'Pro Card Creator: ' : 'Free Card Creator: ';
			sTitle += 'step '+step+' of '+steps+' - ';
			var prepareSave = false;
			
			switch(step)
			{
				
				case '1':
					
					sTitle += (iPro!='0') ? 'upload image(s)' : 'select template';
					
					created = false;
					
					break;
					
					
				case '2':
					
					sTitle = sTitle+'enter details';
					
					if(iPro!='0')
					{
						oPopup.find("#fieldsImages").addClass(sOrientation).empty();
						
						//generate the front image
						var obj = oPopup.find(".cardImageHolder").find(".ownImage[id='front']");
						getImageSrc(obj, '1');
						
						//generate the back image
						var obj = oPopup.find(".cardImageHolder").find(".ownImage[id='back']");
						getImageSrc(obj, '1');
						
					}
					
					//set default name of card
					var username = '<?php echo $_SESSION['username']; ?>';
					if(username.length > 0) username += ' ';
					oPopup.find("input[name='txtCardName']").val( username + oPopup.find(".frmStep[id='1']").find("h1").html());
					
					if(!formLoaded)
					{
						//textbox behaviour
						activateTextboxes(oPopup.find(step2show).find("#frmDetails"));
					}
					//show/hide fields
					oPopup.find("#cmdDefault").show();
					formLoaded = false;
					
					
					
					break;
				
				
				case '3':
					
					sTitle += 'personalize information fields';
					
					if(!formLoaded)
					{
						var aFront = [];
						var aBack = [];
						
						//create fields arrays
						oPopup.find("#frmDetails").find(".textbox").each(function(){
							if(!$(this).hasClass('hidden'))
							{
								$(this).parent().parent().find(".check-icon").each(function(){
									var title = $(this).parent().parent().find(".textbox").attr('title');
									var alt = $(this).parent().parent().find(".textbox").attr('alt');
									var name = $(this).parent().parent().find(".textbox").attr('name');
									if($(this).attr('id') == 'f')
									{
										aFront.push( name );
									}
									else
									{
										aBack.push( name );
									}
								});
							}
						});
						
						var image = (pro) ? ".ownImage" : ".templateImage";
						
						oPopup.find("#fieldsImages").find(".lblField").remove();
						
						//add front fields
						var iTop = 15;
						
						for(var i=0; i<aFront.length; i++)
						{
							var fieldname = aFront[i];
							var field = oPopup.find("#frmDetails").find(".textbox[name='"+fieldname+"']");
							var val = field.val();
							var alt = field.attr('alt');
							if(val == alt)
							{
								//val = '';
								//oPopup.find("#fieldsImages").find(".lblField[id='"+fieldname+"']").hide();
							}
							else
							{
								//oPopup.find("#fieldsImages").find(".lblField[id='"+fieldname+"']").html(val).show();
							}
							oPopup.find("#fieldsImages").find(image+"[id='front']")
								.append('<div class="lblField" id="'+fieldname+'" alt="'+alt+'" style="left:25px; top:'+iTop.toString()+'px;">'+val+'</div>');
							iTop+= 25;
						}
						
						//add back fields
						var iTop = 15;
						for(var i=0; i<aBack.length; i++)
						{
							var fieldname = aBack[i];
							var field = oPopup.find("#frmDetails").find(".textbox[name='"+fieldname+"']");
							var val = field.val();
							var alt = field.attr('alt');
							if(val == alt)
							{
								//val = '';
								//oPopup.find("#fieldsImages").find(".lblField[id='"+fieldname+"']").hide();
							}
							else
							{
								//oPopup.find("#fieldsImages").find(".lblField[id='"+fieldname+"']").html(val).show();
							}
							oPopup.find("#fieldsImages").find(image+"[id='back']")
								.append('<div class="lblField" id="'+fieldname+'" alt="'+alt+'" style="left:25px; top:'+iTop.toString()+'px;">'+val+'</div>');
							iTop+= 25;
						}
						formLoaded = true;
					}
					break;
				
				
				case '4':
					
					sTitle = sTitle+'preview and save card';
					
					//prepare card image holders
					var cardImages = oPopup.find("#fieldsImages").html();
					oPopup.find("#cardImages").addClass(sOrientation).html(cardImages);
					oPopup.find("#cardImages").find(".lblField").removeClass('selected');
					oPopup.find("#cardImages").find(".orientation-holder").remove();
					
					if(iPro!='0')
					{
						//load front image
						var src = getImageSrc(oPopup.find("#cardImages").find(".ownImage[id='front']"),'2');
						oPopup.find("#cardImages").find(".ownImage[id='front']")
							.empty()
							.css('background','transparent')
							.append('<img src="'+src+'" />');
						
						//load back image
						var src = getImageSrc(oPopup.find("#cardImages").find(".ownImage[id='back']"),'2');
						oPopup.find("#cardImages").find(".ownImage[id='back']")
							.empty()
							.css('background','transparent')
							.append('<img src="'+src+'" />');
						
					}
					else
					{
						//load front image
						var src = getImageSrc(oPopup.find("#cardImages").find(".templateImage[id='front']"));
						oPopup.find("#cardImages").find(".templateImage[id='front']")
							.empty()
							.css('background','transparent')
							.append('<img src="'+src+'" />');
						
						//load back image
						var src = getImageSrc(oPopup.find("#cardImages").find(".templateImage[id='back']"));
						oPopup.find("#cardImages").find(".templateImage[id='back']")
							.empty()
							.css('background','transparent')
							.append('<img src="'+src+'" />');
					}
					
					prepareSave = true;
					
					if(sOrientation == 'portrait')
					{
						oPopup.find("#cardImages").css({
							paddingTop:80,
							paddingLeft:50,
							width:550
						});
					}
					else if(sOrientation == 'landscape')
					{
						oPopup.find("#cardImages").css({
							paddingTop:120,
							paddingLeft:15,
							width:725
						});
					}
					
					break;
			}
			
			//update title
			if(iPro!='0')
			{
				$("#708").find(".block").find(".title").each(function(){
					$(this).html(sTitle);
					return false;
				});
			}
			else
			{
				$("#707").find(".block").find(".title").each(function(){
					$(this).html(sTitle);
					return false;
				});
			}
			
			//show next step
			oPopup.find(step2show).show('fast');
			
			//update prev button
			if(parseInt(step) > 1)
			{
				oPopup.find("#cmdPrev").removeClass('button-disabled');
				oPopup.find("#cmdPrev").attr('alt',(parseInt(step)-1).toString());
			}
			else
			{
				oPopup.find("#cmdPrev").addClass('button-disabled');
			}
			//update next button
			if(parseInt(step) < steps)
			{
				oPopup.find("#cmdNext").html('NEXT STEP &gt;');
				oPopup.find("#cmdNext").attr('alt',(parseInt(step)+1).toString());
			}
			else
			{
				oPopup.find("#cmdNext").html('SAVE');
			}
			
		});
	}
	
	
	

	function initFields(oPopup)
	{
		oPopup.find(".textbox").each(function(){
			if($(this).is(":visible"))
			{
				var alt = $(this).attr('alt');
				var val = $.trim($(this).val());
				if(val == '')
				{
					$(this).val(alt);
				}
			}
		});
	}
	
	
	
	function activateCardCreator(oPopup)
	{
		//fields front and/or back
		oPopup.find(".checkbox").livequery('click',function(){
			if($(this).hasClass('x-icon'))
			{
				$(this).removeClass('x-icon');
				$(this).addClass('check-icon');
			}
			else if($(this).hasClass('check-icon'))
			{
				$(this).removeClass('check-icon');
				$(this).addClass('x-icon');
			}
		});
		
		//load default values
		oPopup.find("#cmdDefault").click(function(){
			oPopup.find("#frmDetails").find(".textbox[name='txtName']").parent().parent().find(".checkbox[id='f']").removeClass('x-icon').addClass('check-icon');
			oPopup.find("#frmDetails").find(".textbox[name='txtCompany']").parent().parent().find(".checkbox[id='f']").removeClass('x-icon').addClass('check-icon');
			oPopup.find("#frmDetails").find(".textbox[name='txtDesignation']").parent().parent().find(".checkbox[id='f']").removeClass('x-icon').addClass('check-icon');
			oPopup.find("#frmDetails").find(".textbox[name='txtMobile']").parent().parent().find(".checkbox[id='f']").removeClass('x-icon').addClass('check-icon');
			oPopup.find("#frmDetails").find(".textbox[name='txtName']").parent().parent().find(".checkbox[id='b']").removeClass('x-icon').addClass('check-icon');
			oPopup.find("#frmDetails").find(".textbox[name='txtCompany']").parent().parent().find(".checkbox[id='b']").removeClass('x-icon').addClass('check-icon');
			oPopup.find("#frmDetails").find(".textbox[name='txtDesignation']").parent().parent().find(".checkbox[id='b']").removeClass('x-icon').addClass('check-icon');
			oPopup.find("#frmDetails").find(".textbox[name='txtMobile']").parent().parent().find(".checkbox[id='b']").removeClass('x-icon').addClass('check-icon');
			oPopup.find("#frmDetails").find(".textbox[name='txtEmail']").parent().parent().find(".checkbox[id='b']").removeClass('x-icon').addClass('check-icon');
			oPopup.find("#frmDetails").find(".textbox[name='txtAddress']").parent().parent().find(".checkbox[id='b']").removeClass('x-icon').addClass('check-icon');
		});
		
		//add field
		oPopup.find("#cmdAdd").livequery('click',function(){
			var alt = oPopup.find("#cboFieldtypes").find("option:selected").html();
			var id = oPopup.find("#cboFieldtypes").find("option:selected").val();
			if(id != '-1')
			{
				$(this).hide('fast',function(){
					$(this).parent().find(".textbox").val(alt);
					$(this).parent().find(".textbox").attr('title',alt);
					$(this).parent().find(".textbox").attr('name','txt'+alt);
					$(this).parent().find(".textbox").attr('alt',id.toString());
					var row = $(this).parent().parent();
					var html = row.html();
					//prepare field row
					row.find("#cmdAdd").remove();
					row.find(".close-small").removeClass('hidden');
					row.find("#cboFieldtypes").remove();
					row.find(".textbox").removeClass('hidden');
					row.find(".checkbox").show('fast');
					//prepare and add new field row
					oPopup.find("#table-fields").find("tbody").append('<tr>'+html+'</tr>');
					oPopup.find("#cmdAdd").show('fast');
					//init fields
					activateTextboxes(oPopup.find("#frmDetails"));
				});
			}
		});
		
		//remove field
		oPopup.find(".close-small").livequery('click',function(){
			$(this).parent().parent().hide('fast',function(){
				$(this).remove();
				if( oPopup.find("#frmDetails").find("table tbody").find("tr:visible").size() == 9 )
				{
					oPopup.find("#frmDetails").find("table tbody").find("tr:hidden").show();
				}
			});
		});
		
		
		//select all fields
		oPopup.find("#cmdSelectAll").click(function(){
			
			oPopup.find("#fieldsImages").find(".lblField").addClass('selected');
			
		});
		
		
		//fields behaviour
		oPopup.find("#fieldsImages").find(".lblField").livequery('click',function(e){
			
			//set selected field
			selectedField = $(this);
			
			//indicate selection
			if(e.ctrlKey) {
				//Ctrl+Click
				//leave previously selected
			}
			else
			{
				oPopup.find("#fieldsImages").find(".lblField").removeClass('selected');
			}
			$(this).addClass('selected');
			
			//set draggable
			oPopup.find("#fieldsImages").find(".lblField").draggable("destroy");
			$(this).draggable({containment:"parent"});
			
			//set font settings
			var fontFamily = $(this).css('font-family');
			var fontSize = parseInt($(this).css('font-size'),10).toString();
			var fontColour = rgb2hex($(this).css('color'));
			
			oPopup.find("#cboFontFamily").val(fontFamily);
			oPopup.find("#cboFontSize").val(fontSize);
			oPopup.find("#cboFontColour").val(fontColour);
			
		});
		
		//deselect information field
		/*
		oPopup.find("#fieldsImages").find(".templateImage").click(function(){
			selectedField.removeClass('selected');
			selectedField.draggable("destroy");
			selectedField = null;
		});
		*/
		
		//information fields personalization
		oPopup.find("#cboFontFamily").change(function(){
			var val = $(this).val();
			oPopup.find("#fieldsImages").find(".selected").each(function(){
				$(this).css('font-family',val);
			});
		});
		oPopup.find("#cboFontSize").change(function(){
			var val = $(this).val()+'px';
			oPopup.find("#fieldsImages").find(".selected").each(function(){
				$(this).css('font-size',val);
			});
		});
		oPopup.find("#cboFontColour").change(function(){
			var val = $(this).val();
			oPopup.find("#fieldsImages").find(".selected").each(function(){
				$(this).css('color',val);
			});
		});
		
		//prev step
		oPopup.find("#cmdPrev").click(function(){
			if(!$(this).hasClass('button-disabled'))
			{
				var step = $(this).attr('alt');
				var step2hide = (parseInt(step)+1).toString();
				showStep('frmStep', step2hide, step);
			}
		});
		
		
		//next step
		oPopup.find("#cmdNext").unbind().click(function(){
			
			if($(this).html() == 'SAVE')
			{
				//final step -> save
				
				var failed = false;
				
				//alert('saving..\nFRONT = '+sFrontImage+'\nBACK = '+sBackImage);
				
				var sDescription = oPopup.find("input[name='txtCardName']").val();
				var o = (sOrientation == 'landscape') ? '2' : '1';
				
				$.ajax({
					async: false,
					type: 'POST',
					url: sUrl+'user.php',
					data: {
						action: 'savecard',
						description: sDescription,
						orientation: o,
						imagefront: sFrontImage,
						imageback: sBackImage,
						fieldsfront: aFrontFields,
						fieldsback: aBackFields,
						pro: pro
					},
					success: function(result){
						if(result == '1')
						{
							//saved
						}
						else
						{
							failed = true;
						}
					}
				});
				
				if(failed)
				{
					//failed popup
					var width = 240;
					var height = 150;
					var title = 'card creator';
					var contents =
						'<div class="close"></div>'+
						'<p style="font-size:13px; text-align:center; margin-left:25px; margin-right:25px;">Error! Your card was not saved.</p>'+
						'<div id="cmdOk" class="button-small center" style="width:50px;">Ok</div>'
					;
					
					var oPopupA = buildPopup(width, height, title, contents, 775);
					
					//close button
					oPopupA.find(".close").click(function(){
						destroyPopup(775);
					});
					
					//ok button
					oPopupA.find("#cmdOk").click(function(){
						destroyPopup(775);
					});
					
					setTimeout("removeLoader()", 750);
					
					destroyPopup(707);
					
					return false;
				}
				
				
				if(loggedIn == '1')
				{
					//saved popup
					var width = 240;
					var height = 150;
					var title = 'card creator';
					var contents =
						'<div class="close"></div>'+
						'<p style="font-size:13px; text-align:center; margin-left:25px; margin-right:25px;">Congratulations! Your card was saved successfully.</p>'+
						'<div id="cmdOk" class="button-small center" style="width:50px;">Ok</div>'
					;
					
					var oPopupA = buildPopup(width, height, title, contents, 778);
					
					//close button
					oPopupA.find(".close").click(function(){
						destroyPopup(778);
					});
					
					//ok button
					oPopupA.find("#cmdOk").click(function(){
						window.location = '?page=browse';
					});
					
					if(iPro!='0')
					{
						destroyPopup(708);
					}
					else
					{
						destroyPopup(707);
					}
					
				}
				else
				{
					//saved popup
					
					showLoginPopup('Save Card', 'Login and Save', 'You must log in save the card.<br />If you don\'t have an account, sign up for FREE!');
					
				}
			}
			else
			{
				var step = $(this).attr('alt');
				var step2hide = (parseInt(step)-1).toString();
				if(step == 4)
				{
					var img = (iPro != '0') ? '.ownImage' : '.templateImage';
					var aSizes = [];
					aSizes[8] = '6';
					aSizes[11] = '8';
					aSizes[12] = '9';
					aSizes[13] = '10';
					aSizes[15] = '11';
					aSizes[16] = '12';
					aSizes[19] = '14';
					aSizes[22] = '16';
					aSizes[24] = '18';
					aSizes[26] = '20';
					aSizes[29] = '22';
					aSizes[32] = '24';
					aSizes[37] = '28';
					aSizes[48] = '36';
					//create fields arrays
					aFrontFields = [];
					oPopup.find("#fieldsImages").find(img+"[id='front']").find(".lblField").each(function(){
						var id = $(this).attr('alt');
						var name = $(this).attr('id');
						var value = encodeURIComponent($(this).html());
						var top = parseInt($(this).css('top'),10);
						var left = parseInt($(this).css('left'),10);
						var width = parseInt($(this).css('width'),10);
						var height = parseInt($(this).css('height'),10);
						var font = getFontFile($(this).css('font-family'));
						var size = aSizes[ parseInt($(this).css('font-size'),10) ];
						var color = rgb2hex($(this).css('color')).substring(1);
						var field = [id, name, value, left.toString(), top.toString(), width.toString(), height.toString(), font, size, color];
						aFrontFields.push( field.join('|') );
					});
					aBackFields = [];
					oPopup.find("#fieldsImages").find(img+"[id='back']").find(".lblField").each(function(){
						var id = $(this).attr('alt');
						var name = $(this).attr('id');
						var value = encodeURIComponent($(this).html());
						var top = parseInt($(this).css('top'),10);
						var left = parseInt($(this).css('left'),10);
						var width = parseInt($(this).css('width'),10);
						var height = parseInt($(this).css('height'),10);
						var font = getFontFile($(this).css('font-family'));
						var size = aSizes[ parseInt($(this).css('font-size'),10) ];
						var color = rgb2hex($(this).css('color')).substring(1);
						var field = [id, name, value, left.toString(), top.toString(), width.toString(), height.toString(), font, size, color];
						aBackFields.push( field.join('|') );
					});
				}
				showStep('frmStep', step2hide, step);
			}
		});
		
	}
	
	
	
	
	
	
	//PAID USER CARD CREATORS...

	var imgScale = [];
	var imgRotate = [];
	var imgWidth = [];
	var imgHeight = [];
	
	var pro = '<?=$_SESSION['pro']?>';
	var paid = '<?=$_SESSION['paid']?>';
	
	function loadProCreator(obj)
	{
		imgScale = [];
		imgRotate = [];
		imgWidth = [];
		imgHeight = [];
		
		var proType = obj.attr('alt');
		
		if(iPro!='0' && iPaid=='1')
		{
			steps = 4;
			var pro = true;
			
			var currentStep = 1;
			var width = 900;
			var height = 640;
			var templateName = obj.parent().parent().find(".title").html();
			var templateImages = obj.parent().html();
			var contents = $("#frmPro").html()+$("#frmAll").html();
			var activeSide = 'front';
			var title = (proType=='1') ? 'Pro' : 'Upload';
			title += ' Card Creator: step '+currentStep+' of '+steps+' - upload image(s)';
			
			oPopup = buildPopup(width, height, title, contents, 708);
			
			//init
			
			//close button
			oPopup.find(".close").click(function(){
				destroyPopup(708);
			});
			
			//upload image command button
			oPopup.find("#cmdUploadImage").click(function(){
				
				var width = 400;
				var height = 200;
				var title = 'upload image';
				
				//prepare contents
				$("#frmUploadImage").find("#upload_target").attr('alt', activeSide);
				var contents = $("#frmUploadImage").html();
				
				//build popup	
				var oPopupA = buildPopup(width, height, title, contents, 655);
				
				//callback function
				loadImage=function(imgsrc){
					
					sImageURL = imgsrc;
					
					//reset rotate (to be sure to get correct width and height)
					if(imgRotate[activeSide] != 0)
					{
						imgRotate[activeSide] = 0;
						oPopup.find(".ownImage[id='"+activeSide+"']").animate({
							rotate:imgRotate[activeSide]+'deg'
						});
					}	
					
					//insert uploaded image src
					oPopup.find(".cardImageHolder[id='"+activeSide+"']")
						.empty()
						.html('<img src="'+imgsrc + '?' + (new Date().getTime()) +'" class="ownImage float-left" id="'+activeSide+'" />');
					
					var img = new Image();
					img.src = 'http://127.0.0.1/mobidex/'+imgsrc;
					var w = img.width;
					var h = img.height;
					
					//reset the slider
					dontResize = true;
					slider.slider("value",100);
					dontResize = false;
					
					//getImageDimensions();
					
					oPopup.find(".cardImageHolder[id='"+activeSide+"']").find("img").draggable();
					
					destroyPopup(655);
					
				};
				
				//close button
				oPopupA.find(".close").click(function(){
					destroyPopup(655);
				});
				
			});
			
			
			//image settings
			
			imgScale['front'] = 100;
			imgScale['back'] = 100;
			imgWidth['front'] = 0;
			imgWidth['back'] = 0;
			imgHeight['front'] = 0;
			imgHeight['back'] = 0;
			imgRotate['front'] = 0;
			imgRotate['back'] = 0;
			
			function getImageDimensions()
			{
				var w = parseInt(oPopup.find(".cardImageHolder[id='"+activeSide+"']").find(".ownImage").css('width'),10);
				var h = parseInt(oPopup.find(".cardImageHolder[id='"+activeSide+"']").find(".ownImage").css('height'),10);
				if(!dontResize)
				{
					imgWidth[activeSide] = w;
					imgHeight[activeSide] = h;
				}
				//alert(w+'x'+h);
			}
			
			function showTab(tab)
			{
				//hide tabs
				oPopup.find(".cardImagesHolder").find("p[alt='tabFront']").removeClass('active');
				oPopup.find(".cardImagesHolder").find("#tabFront").hide();
				oPopup.find(".cardImagesHolder").find("p[alt='tabBack']").removeClass('active');
				oPopup.find(".cardImagesHolder").find("#tabBack").hide();
				
				//show this tab
				oPopup.find(".tab[alt='"+tab+"']").addClass('active');
				oPopup.find(".cardImagesHolder").find("#"+tab).show();
				activeSide = tab.substring(3).toLowerCase();
				if(imgWidth[activeSide]==0 || imgHeight[activeSide]==0)
				{
					getImageDimensions();
				}
				
				//set controls
				dontResize = true;
				slider.slider("value",imgScale[activeSide]);
				dontResize = false;
			}
			
			getImageDimensions();
				
				//front/back tabs
				oPopup.find(".tab").click(function(){
					showTab($(this).attr('alt'));
				});
				
				//orienation
				oPopup.find(".orientation-holder").find("div").click(function(){
					var alt = $(this).attr('alt');
					var id = $(this).attr('id');
					if(!$(this).hasClass('active'))
					{
						$(this).parent().find("#"+alt).removeClass('active');
						$(this).addClass('active');
						oPopup.find(".cardImagesHolder").removeClass(alt).addClass(id);
						sOrientation = id;
					}
				});
				
				//background color
				oPopup.find("[name='cboBackgroundColor']").change(function(){
					var col = $(this).val();
					oPopup.find(".cardImageHolder[id='"+activeSide+"']").css('background',col);
				});
				
				//scale
				var scale = 100;
				var slider = oPopup.find("#slider").slider({
					value: scale,
					min: 10,
					max: 100,
					step: 10,
					slide: function(event, ui) {
						if(imgWidth[activeSide]==0 || imgHeight[activeSide]==0) getImageDimensions();
						if(!dontResize)
						{
							resizeImage(ui.value);
						}
						imgScale[activeSide] = ui.value;
					},
					change: function(event, ui) {
						if(imgWidth[activeSide]==0 || imgHeight[activeSide]==0) getImageDimensions();
						if(!dontResize)
						{
							resizeImage(ui.value);
						}
						imgScale[activeSide] = ui.value;
					}
				});
				
				function resizeImage(scale)
				{
					// scale: 10 - 100
					//getImageDimensions();
					var w = imgWidth[activeSide];
					var h = imgHeight[activeSide];
					var iWidth = w * scale / 100;
					var iHeight = h * scale / 100;
					oPopup.find(".ownImage[id='"+activeSide+"']").css('width',iWidth.toString()+'px');
					oPopup.find(".ownImage[id='"+activeSide+"']").css('height',iHeight.toString()+'px');
				}
				
				//minus button
				oPopup.find(".min-icon").click(function(){
					//reduce slider with 10
					var val = parseInt(slider.slider("value"));
					if(val > 0)
					{
						val = val - 10;
					}
					dontResize = false;
					slider.slider( "value", val );
				});
				
				//plus button
				oPopup.find(".pos-icon").click(function(){
					//increase slider with 10
					var val = parseInt(slider.slider("value"));
					if(val < 100)
					{
						val = val + 10;
					}
					dontResize = false;
					slider.slider( "value", val );
				});
				
				//center button
				oPopup.find("#cmdCenterImage").click(function(){
					var img = oPopup.find(".ownImage[id='"+activeSide+"']");
					var card = oPopup.find(".cardImageHolder[id='"+activeSide+"']");
					var w = parseInt(img.css('width'),10);
					var h = parseInt(img.css('height'),10);
					var width = parseInt(card.css('width'),10);
					var height = parseInt(card.css('height'),10);
					var left = (width - w) / 2;
					var top = (height - h) / 2;
					img.css('left',left.toString()+'px');
					img.css('top',top.toString()+'px');
				});
				
				//rotate button (right)
				oPopup.find("#cmdRotateImage").click(function(){
					imgRotate[activeSide] += 90;
					if(imgRotate[activeSide] > 270)
					{
						imgRotate[activeSide] = 0;
					}
					oPopup.find(".ownImage[id='"+activeSide+"']").animate({
						rotate:imgRotate[activeSide]+'deg'
					});
				});
				
			//flip card
			oPopup.find(".flip-card").click(function(){
				
				var alt = $(this).attr('alt');
				if(typeof(alt)=="undefined" || alt=="")
				{
					alt = 'tabBack';
				}
				
				switch(alt)
				{
					case 'tabBack':
						$(this).attr('alt','tabFront');
						break;
					
					case 'tabFront':
						$(this).attr('alt','tabBack');
						break;
				}
				
				showTab(alt);
				
			});
			
			//next button
			oPopup.find("#cmdNext").unbind().click(function(){
				var step = $(this).attr('alt');
				var step2hide = (parseInt(step)-1).toString();
				showStep('frmStep', step, step2hide);
			});
			
			//activate the default creator elements
			activateCardCreator(oPopup);
			
		}
		else
		{
			//show payment gateway
			showPaymentGateway();
		}
		
	}
	
	//PRO and UPLOAD card creator ***********************************************
	
	$("#cmdOwnImages, #cmdOwnCard").click(function(){
		loadProCreator($(this));
	});
	
	//END OF: Pro card creator
	//========================
	
	
	
	//INIT
	
	
	
	
});
</script>