<?php
//Create A Card
//FREE
//PRO
//UPLOAD

//Validate edit script
$aCard = "";
if($_GET['edit']){
	$sql = "SELECT user_id FROM mytcg_card WHERE card_id = ".$_GET['edit'];
	$aValid = myqu($sql);
	$aUserID = $aValid[0]['user_id'];
	if($aUserID != $_SESSION['user']){
		$_GET['edit'] = null;
		$_GET['pro'] = null;
		$cardData = "";
		$_SESSION['edit']=null;
	}else{
		$sql = "SELECT C.card_id, C.cardtype, I.description AS path, C.image AS img, C.cardorientation_id AS portrait, C.cardtype, C.template
				FROM mytcg_card C
				INNER JOIN mytcg_imageserver I ON (C.front_imageserver_id = I.imageserver_id)
				WHERE card_id = ".$_GET['edit'];
		$aCard = myqu($sql);
		$aCard = $aCard[0];
		
		$_SESSION['edit'] = $aCard['card_id'];
		$_SESSION['front'] = $aCard['path']."cards/".$aCard['img']."_front.jpg";
		$_SESSION['back'] = $aCard['path']."cards/".$aCard['img']."_back.jpg";
	}
}else{
	$_SESSION['edit'] = null;
	$_SESSION['front'] = null;
	$_SESSION['back'] = null;
}
//Default if edit load in not active
//PORTRAIT OR LANDSCAPE
$portrait = "";
$landscape = "";
if($aCard['portrait']=="1"){
	$portrait = "class='active'";
	$sOrientation = "portrait";
}else{
	$landscape = "class='active'";
	$sOrientation = "landscape";
}

//LOAD UPLOADED IMAGE
$imgSrc = "";
if($aCard != ""){
	$front = /*$aCard['path'].*/"img/cards/".$aCard['img']."_front.jpg";
	$back = /*$aCard['path'].*/"img/cards/".$aCard['img']."_back.jpg";
}
$cardtype = $_GET['pro'];
if(!$cardtype){
	$cardtype = "0";
}
?>
<div id="page-title">Create a Card - Get started by selecting a template</div>
<?php

	//industries (categories)
	$aCats = getAllCategories();
	$industries = '';
	foreach($aCats as $cat)
	{
		$industries.= '<option value="'.$cat['category_id'].'">'.$cat['description'].'</option>';
	}
	
   //colors
   $aColors = array
   (
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
	$colours = '';
	foreach($aColors as $col=>$hex)
	{
		$colours.= '<option value="'.$hex.'">'.$col.'</option>';
	}
	
	//filter
	$contents = <<<STR
	<select id="cboIndustries" style="display:none;">
		<option>All Industries</option>
		{$industries}
	</select>
	<select id="cboStyles" style="display:none;">
		<option>All Styles</option>
		<option>&nbsp;</option>
		<option>&nbsp;</option>
		<option>&nbsp;</option>
		<option>&nbsp;</option>
	</select>
	<select id="cboColours" style="display:none;">
		<option>All Colours</option>
		{$colours}
	</select>
	<p>Narrow your results</p>
	<p>
		<label><input type="radio" name="optOrientation" value="both" checked="checked" />Both</label>
		<label><input type="radio" name="optOrientation" value="portrait" />Portrait</label>
		<label><input type="radio" name="optOrientation" value="landscape" />Landscape</label>
	</p>
	<hr />
	<div class="button-small" id="cmdResetFilters" style="display:none; width:110px; margin-left:auto; margin-right:auto;">RESET ALL FILTERS</div>
	<div style="position:relative;">
		<input type="text" id="txtSearch" class="textbox" style="width:180px;" value="Search.." alt="Search.." />
		<div id="cmdSearch"></div>
	</div>
	<div style="position:relative;">
		<div class="button" id="cmdOwnImages" style="margin-top:25px;display:none;" alt="1">CUSTOM PRO</div>
		<div class="button" id="cmdOwnCard" style="margin-top:25px;" alt="2">UPLOAD PRO</div>
	</div>
STR;
	
	buildBlock(225, 185, 'float-left', 'show designs by', $contents);

	//templates
	$filter = '';
	printTemplates($filter);

?>









<!-- PRO Card Creator -->

<div id="frmPro" style="display:none;">

	<div class="close"></div>
	
	
   <div id="frmUploadImage" style="display:none;">
      <div class="close"></div>
		<iframe style="width: 460px; height: 135px; border: 0px none;" src="ajaxfileupload.php" name="upload_target" id="upload_target" alt="front"></iframe>
   </div>
	
	<div id="1" class="frmStep" style="position:relative;">
	
		<div class="float-right">
			<div class="cardImagesHolder <?php echo($sOrientation); ?>">
				<div id="tabFront" style="width:530px; padding:0px; height:390px;">
					<div class="cardImageHolder" id="front">
						<img id="front" class="ownImage float-left default" src="<?php echo($front); ?>?123" />
					</div>
				</div>
				<div id="tabBack" style="width:530px; padding:0px; height:390px; display:none;">
					<div class="cardImageHolder" id="back">
						<img id="back" class="ownImage float-left default" src="<?php echo($back); ?>?123" />
					</div>
				</div>
			</div>
			<div class="flip-card hidden">Flip Card</div>
		</div>
		
		<?php
		
		//image settings
		
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
		
		$contents = <<<STR
			<div style="position:relative;padding-bottom:5px;">
				<p style="padding:4px;">Orientation</p>
				<div class="orientation-holder">
					<div id="landscape" alt="portrait" title="Landscape" {$landscape} ></div>
					<div id="portrait" alt="landscape" title="portrait" {$portrait} ></div>
				</div>
			</div>
STR;
		//card orientation
		buildBlock(310, 45, 'float-left', '', $contents);
		
		//image settings
		$contents = <<<STR
			<div class="tabs">
				<div class="tabz active" alt="tabFront">FRONT</div>
				<div class="tabz" alt="tabBack">BACK</div>
				<div class="clear"></div>
			</div>
			<hr />
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
				<div id="disable-tools"></div>
			</div>
STR;
		buildBlock(310, 290, 'float-left', '', $contents);
		clear();
		
		?>

	</div>
	
</div>





<div id="frmAll" style="display:none;">
	
	<div class="frmStep" id="2" style="position:relative; display:none;">
		<div class="center" style="position:relative;" id="changeforupload">
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
								<td class="middle"><input type="text" class="textbox" name="txtTags" title="Search Tags" value="Search Tags" alt="Search Tags" /> &nbsp; <span>separated by commas</span></td>
								<td class="right">&nbsp;</td>
							</tr>
							<tr class="hideforupload">
								<td>Display on</td>
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
								<td class="middle"><input type="text" class="textbox" name="txtCompany" title="Company" value="Company" alt="9" /> &nbsp; <span>e.g. Music Company</span></td>
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
		<div id="cmdDefault" class="button-small" style="width:120px; margin-top:0px; left:11px; top:10px; display:none;">Default Display</div>
	</div>
	
	<div class="frmStep" id="3" style="position:relative; height:415px; display:none;">
	<?php
		
		$fieldOptions = getFieldtypesCombo(true); //contentsOnly=true
		$colorOptions = str_replace("Background", "Highlight", $colorOptions);
		$contents = <<<STR
		<div class="showforupload" id="proFields" style="position:relative;">
			<select id="cboFieldOptions" style="float:left;">{$fieldOptions}</select>
			<br />
			<input type="text" id="txtFieldDescription" class="textbox" name="txtDescription" title="Enter Text" value="Enter Text" alt="Enter Text" style="width:268px;margin-bottom:10px;" />
			<div id="cmdDeleteField" class="float-right" style="width:75px; display:none;">DELETE</div>
			<select name="cboBorderColor" style="margin-bottom:5px;">
					{$colorOptions}
			</select>
			<div id="cmdAddField" class='button' style="margin-top:0px;">Confirm</div>
			<div id="disableFieldOptions"></div>
			<div class="clear"></div>
		</div>
STR;
		buildBlock(310, 245, 'float-left showforupload', '<span id="drawInfo" class="">Click and Drag Field on Image</span>', $contents);
		
		$sql = "SELECT font_id, name, file FROM mytcg_font";
		$aFonts = myqu($sql);
		$iSize = sizeof($aFonts);
		$fonts = '';
		$fontlist = "";
		for($i=0; $i < $iSize; $i++){
			$font_id = $aFonts[$i]['font_id'];
			$fontname = $aFonts[$i]['name'];
			$fontfile = $aFonts[$i]['file'];
			$fonts.= "<option id='{$font_id}' value='{$fontname}' alt='{$fontfile}'>{$fontname}</option>";
			$fontlist .= "|".$fontname."|".$font_id;
		}
		$fontlist = substr($fontlist, 1,strlen($fontlist));
		
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
				<option value="-1">Font Family</option>
				{$fonts}
			</select>
			<select id="cboFontSize">
				<option value="-1">Font Size</option>
				{$sizes}
			</select>
			<select id="cboFontColour">
				<option value="-1">Font Colour</option>
				{$colours}
			</select>
			<hr />
			<select id="cboFieldtypes" style="display:none;">
				{$fieldtypes}
			</select>
			<div class="button-small" id="cmdSelectAll" style="position:relative; width:120px;">Select All Fields</div>
STR;
		buildBlock(225, 210, 'float-left hideforupload', 'font', $contents);
		
		?>
		<div id="fieldsImages" class="float-right" style="width:540px; height:515px; margin-left: 20px;"></div>
		<?php
		
		echo '<div style="position:relative;clear:left;"></div>';
		
		$contents = <<<STR
		<div id="addedFields">
			<p style="font-style:italic;">No fields added. Click and drag on the card to create a field.</p>
			<ul class="front"></ul>
			<ul class="back"></ul>
		</div>
STR;
		buildBlock(310, 247, 'float-left showforupload', 'Information Fields', $contents);
		
		?>
		<div class="clear"></div>
	</div>
	
	<div class="frmStep" id="4" style="position:relative; display:none;">
		<div class="clear"></div>
		<div id="cardImages"></div>
		<div class="clear"></div>
	</div>
	
	<div style="top:515px; width:100%;">
		<hr />
		<div class="help-icon"><div><a href="" class="help" id="" alt="1">Help</a></div></div>
		<div class="buttons-holder">
			<div id="cmdPrev" alt="0" class="button button-disabled float-left">&lt; PREV STEP</div>
			<div id="cmdNext" alt="2" class="button float-left">NEXT STEP &gt;</div>
		</div>
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

var steps;
var gridSize;
var currentPage;
var searchString;
var pageCount;
var formLoaded;
var imagesLoaded;
var selectedField;
var iAddedCount;
var aFrontFields = [];
var aBackFields = [];
var sFrontImage;
var sBackImage;
var sFrontColor="#000000";
var sBackColor="#000000";
var sOrientation = "<?php echo($sOrientation); ?>";
var dontResize = false;
var loadImage;
var sImageURL;
var aReset = [];
var editLock = false;
var xmlData = null;
var cardtype = "<?php echo($cardtype); ?>";
var fontlist = "<?php echo($fontlist); ?>";
var template;

//Edit card data
editCard = "<?php echo($_SESSION['edit']); ?>";

var created = false;

//READY-----

$(document).ready(function(){

	var iPro = '<?php echo($_SESSION['pro']); ?>';
	var iPaid = '<?php echo($_SESSION['paid']); ?>';
	
	//INIT VARIABLES-----
	
	formLoaded = false;
	gridSize = parseInt($(".page-buttons").attr('alt'));
	
	if(editCard!=""){
	$.ajax({
		type: 'POST',
		url: sUrl+'user.php',
		data: {action: 'edit',card: editCard},
		success: function(xml){
			xmlData = xml;
			
			if(cardtype=="0"){
				steps = 4;
				pro = false;
		
				var currentStep = 1;
				var width = 900;
				var height = 640;
				var title = 'Free Card Creator: step '+currentStep+' of '+steps+' - select template';
				
				
				var templateName = getXML(xmlData,"description");
				var path = getXML(xmlData,"img");
				var front = "img/cards/"+path+"_front.jpg";
				var back = "img/cards/"+path+"_back.jpg";
				var templateImages = "<div style='background-image:url("+front+");cursor: pointer;' alt='"+front+"' id='front' class='templateImage'></div><div style='background-image:url("+back+"); cursor: pointer;' alt='"+back+"' id='back' class='templateImage'></div>";
				var contents = $("#frmFree").html()+$("#frmAll").html();
				var orientation = (getXML(xmlData,"orientation")=="1") ? 'portrait':'landscape';
				sOrientation = orientation;
				
				oPopup = buildPopup(width, height, title, contents, 707);
				
				//init
				oPopup.find("#templateImages").addClass(sOrientation);
				oPopup.find(".help-icon").hide().find("a").attr('id','0');
				
				//close button
				oPopup.find(".close").click(function(){
					destroyPopup(707);
				});
				
				var w;
				var h;
				if(orientation == 'portrait')
				{
					w = 250;
					h = 350;
				}
				else if(orientation == 'landscape')
				{
					w = 350;
					h = 250;
				}
				
				//SET BACK TO DEFAULT TEMPLATES
				front = "templates/"+orientation+"/"+getXML(xmlData,"template");
				front = front.replace("2","1");
				back = "templates/"+orientation+"/"+getXML(xmlData,"template");
				var fieldImages = "<div style='background-image:url("+front+");cursor: pointer;' alt='"+front+"' id='front' class='templateImage'></div><div style='background-image:url("+back+"); cursor: pointer;' alt='"+back+"' id='back' class='templateImage'></div>";
				
				//set images for fields
				oPopup.find("#fieldsImages").html(fieldImages);
				oPopup.find("#fieldsImages").find(".orientation-holder").remove(); 
				
				//fields images
				oPopup.find("#fieldsImages").find(".templateImage")
					.css({
						width:w,
						height:h,
						'float':'left',
						marginLeft:0,
						marginRight:10,
						marginTop:0,
						marginBottom:10,
						cursor:'default'
					})
					.removeClass('hidden')
					.show();
				
				var iFront = 1;
				var path = getXML(xmlData,"img");
				oPopup.find("#templateImages").find("#front").attr("alt","img/cards/"+path+"_front.jpg");
				oPopup.find("#templateImages").find("#back").attr("alt","img/cards/"+path+"_back.jpg");
				
				oPopup.find("#fieldsImages").find(".templateImage").each(function(){
					var alt = $(this).attr('alt');
					$(this).css('background-image','url('+alt+')').empty();
				});
				
				//set template name and images
				oPopup.find("h1").html(templateName);
				oPopup.find("#templateImages").html(templateImages);
				
				//orientation
				oPopup.find("#templateImages").find(".orientation-holder").remove();
				
				//template images
				
				oPopup.find("#templateImages").find(".templateImage").each(function(){
					var alt = $(this).attr('alt');
					$(this).html(sLoadingIcon)
						.css('background-image','')
						.removeClass('hidden')
						.show();
					$(this).smartBackgroundImage(alt, false);
				});
				
				//activate the default creator elements
				activateCardCreator(oPopup);
			}
			
			
		}
	});
}
	
	
	
	//FUNCTIONS -----
	
	function search(str)
	{
		alert(str);
	}

	function pointToPixel(pointSize)
	{
		var pix;
		switch(pointSize)
		{
			case "6":
			  pix = "8";
			break;
			case "8":
			  pix = "11";
			break;
			case "10":
			  pix = "13";
			break;
			case "11":
			  pix = "15";
			break;
			case "12":
			  pix = "16";
			break;
			case "14":
			  pix = "19";
			break;
			case "16":
			  pix = "22";
			break;
			case "18":
			  pix = "24";
			break;
			case "20":
			  pix = "26";
			break;
			case "22":
			  pix = "29";
			break;
			case "24":
			  pix = "32";
			break;
			case "28":
			  pix = "37";
			break;
			case "36":
			  pix = "48";
			break;
			default:
			  pix = "11";
		}
		return pix;
	}

	function findFontID(fontname)
	{
		var aFont = fontlist.split("|");
		var iCount = aFont.length;
		for(i=0;i<iCount;i++){
			if(aFont[i]==fontname){
				return aFont[i+1];
			}
		}
	}
	
	function findXmlOfStat(statID,fb){
		statID = parseInt(statID);
		var count = getXML(xmlData,"statCount");
		for(i=1;i<count;i++){
			var stat = parseInt(getXML(xmlData,"stats/stat_"+i+"/stat_id"));
			var frontorback = parseInt(getXML(xmlData,"stats/stat_"+i+"/frontorback"));
			if((stat == statID) && (fb == frontorback)){
				return i;
			}
		}
		return 0;
	}

	$.fn.smartBackgroundImage = function(url){
		var t = this;
		//create an img so the browser will download the image:
		$('<img />')
		.attr('src', url)
		.load(function(){ //attach onload to set background-image
			t.each(function(){ 
				$(this).css('backgroundImage', 'url('+url+')' ).empty();
			});
		});
		return this;
	}
	
	var sLoadingIcon = '<img class="loading" src="site/loading51.gif" />';
	
	function selectPage(page)
	{
		//teamplates
		var added = 0;
		var counter = 0;
		$(".templates-holder").find(".inplay").hide().each(function(){
			var id = $(this).attr('id');
			if( (counter >= ((gridSize*page) - gridSize)) && (counter < (gridSize*page)) )
			{
				//apply filter
				$(this).find(".templateImage").each(function(){
					var alt = $(this).attr('alt');
					$(this).smartBackgroundImage(alt);
				});
				$(this).show();
				added++;
			}
			else
			{
				if($(this).css('background-image') == '')
				{
					$(this).find(".templateImage").css('backgroundImage', '' ).html(sLoadingIcon);
				}
			}
			if(added >= gridSize)
			{
				//$(this).hide();
			}
			counter++;
		});
		currentPage = page;
		setCurrentPage(currentPage);
		
		//update page buttons
		$(".page-buttons").find(".button-small").each(function(){
			$(this).removeClass('button-small-active');
			var nr = parseInt($(this).attr('id'));
			if(nr <= Math.ceil(counter/gridSize))
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		});
		$(".page-buttons").find(".button-small[id='"+page+"']").addClass('button-small-active');
		
		//nav buttons
		if(currentPage == 1)
		{
			$("#prev").addClass('button-small-disabled');
		}
		else
		{
			if($("#prev").hasClass('button-small-disabled'))
			{
				$("#prev").removeClass('button-small-disabled');
			}
		}
		if(currentPage == getPageCount() || getPageCount() == 0)
		{
			$("#next").addClass('button-small-disabled');
		}
		else
		{
			if($("#next").hasClass('button-small-disabled'))
			{
				$("#next").removeClass('button-small-disabled');
			}
		}
		
	}

	function setCurrentPage(page)
	{
		currentPage = parseInt(page);
	}

	function getCurrentPage()
	{
		return parseInt(currentPage);
	}
	
	function getPageCount(sum)
	{
		if(typeof(sum) == "undefined")
		{
			sum = 0;
		}
		var pageCount = $(".page-buttons").find(".button-small:visible").size()
		return pageCount + (sum);
	}

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
			imgColor = sFrontColor;
			if(aFrontFields.length > 0)
			{
				fields = aFrontFields.join('^!^');
			}
		}
		else if(side == 'back')
		{
			imgColor = sBackColor;
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
			if(pro!='0' && proType=='2')
			{
				sGet += '&upload=1';
			}
		}
		sGet += "&b="+imgColor;
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
					sURL = window.location.href;
					
					//Piero
					//var url = 'http://localhost/mobidex/img/temp/';
					var url = 'http://mobidex.biz/img/temp/';
					
					//add image to step 3
					if(oPopup.find("#fieldsImages").find(".ownImage[id='"+side+"']").size())
					{
						oPopup.find("#fieldsImages").find(".ownImage[id='"+side+"']").css('background','url('+url+filename+') no-repeat');
					}
					else
					{
						oPopup.find("#fieldsImages").append('<div class="ownImage" id="'+side+'" style="position:relative; float:left; background:url('+url+filename+') no-repeat; margin:0px 10px 10px 0px;"></div>');
					}
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
			var sTitle = '';
			if(pro)
			{
				sTitle = (proType=='1') ? 'Custom Pro: ' : 'Upload Pro: ';
				oPopup.find(".help-icon").find("a").attr('id',proType);
			}
			else
			{
				sTitle = 'Free Card Creator: ';
				oPopup.find(".help-icon").find("a").attr('id','0');
			}
			sTitle += 'step '+step+' of '+steps+' - ';
			
			//help notes
			//id: 0 - free
			//id: 1 - pro
			//id: 2 - upload
			//alt: current step
			oPopup.find(".help-icon").find("a").attr('alt',step);
			
			switch(step)
			{
				
				case '1':
					
					if(pro!='0')
					{
						sTitle += 'upload image(s)';
					}
					else
					{
						sTitle += 'select template';
						oPopup.find(".help-icon").hide();
					}
					created = false;
					break;
					
				case '2':
					
					sTitle = sTitle+'enter details';
					oPopup.find(".help-icon").show();
					
					if(pro)
					{
						if(!formLoaded)
						{
							oPopup.find("#fieldsImages").addClass(sOrientation).empty();
						}
						if(!imagesLoaded)
						{
							//generate the front image
							var obj = oPopup.find(".cardImageHolder").find(".ownImage[id='front']");
							getImageSrc(obj, '1');
							
							//generate the back image
							var obj = oPopup.find(".cardImageHolder").find(".ownImage[id='back']");
							getImageSrc(obj, '1');
							
							imagesLoaded = true;
						}
					}
					
					//set default name of card
					var username = '<?php echo $_SESSION['username']; ?>';
					var sCardName = username+' ';
					if(pro)
					{
						sCardName += (proType=='1') ? 'custom pro' : 'upload pro';
					}
					else
					{
						sCardName += oPopup.find(".frmStep[id='1']").find("h1").html();
					}
					var sDate = new Date();
					sDate = sDate.getFullYear() +'-'+ sDate.getMonth() +'-'+ sDate.getDate();
					oPopup.find("input[name='txtCardName']").val( sCardName +' '+ sDate );
					
					if(editCard!=""){
						oPopup.find("input[name='txtCardName']").val( getXML(xmlData,"description") );
						oPopup.find("input[name='txtTags']").val( getXML(xmlData,"searchdata") );
						
						if(cardtype=='0'){
							iCount = parseInt(getXML(xmlData,"statCount"));
							for(i=1; i<iCount; i++){
								var id = getXML(xmlData,"stats/stat_"+i+"/stat_id");
								var obj = oPopup.find("input[alt='"+id+"']");
								var value = getXML(xmlData,"stats/stat_"+i+"/description");
								var type = getXML(xmlData,"stats/stat_"+i+"/type");
								
								if(obj.length == 0){
									var lastRow = $("#table-fields > tbody tr:last-child").html();
									$("#table-fields > tbody tr:last-child").remove();
									var str = '<tr><td class="left"></td>';
										str +='<td class="middle"><div class="checkbox x-icon" id="f"></div></td>';
										str +='<td class="middle"><div class="checkbox x-icon" id="b"></div></td>';
										str +='<td class="middle"><input type="text" alt="'+id+'" value="'+value+'" title="'+type+'" name="txt'+type+'" class="textbox"></td>';
										str +='<td class="right"><div title="Delete field" class="close-small"></div></td>';
										str +='</tr>';
									$("#table-fields > tbody").append(str);
									$("#table-fields > tbody").append("<tr>"+lastRow+"</tr>");
								}
								
								var objRow = oPopup.find("input[alt='"+id+"']").parent().parent();
								var frontorback = getXML(xmlData,"stats/stat_"+i+"/frontorback");
								if(frontorback=="1"){
									objRow.find("#f").removeClass("x-icon").addClass("check-icon");
								}
								if(frontorback=="2"){
									objRow.find("#b").removeClass("x-icon").addClass("check-icon");
								}
								var value = getXML(xmlData,"stats/stat_"+i+"/description");
								$("input[alt='"+id+"']").val(value);
							}
						}
					}
					
					if(!formLoaded)
					{
						//textbox behaviour
						activateTextboxes(oPopup.find(step2show).find("#frmDetails"));
					}
					if(pro && proType!='2' || !pro)
					{
						//show/hide fields
						oPopup.find("#cmdDefault").show();
							
						//show the parts not needed for upload pro
						oPopup.find(".hideforupload").show();
						
						//hide the parts needed for upload pro
						oPopup.find(".showforupload").hide();
						
						updateFieldTypesComboCustom(oPopup);
						//formLoaded = false;
					}
					else
					{
						//show/hide fields
						oPopup.find("#cmdDefault").hide();
													
						//hide the parts not needed for upload pro
						oPopup.find(".hideforupload").hide();
						
						//show the parts needed for upload pro
						oPopup.find(".showforupload").show();
						
						//update the parts for upload pro
						oPopup.find("#changeforupload").css('padding-top','175px');
					}
					
					break;
				
				
				case '3':
					sTitle += 'personalize information fields';
					oPopup.find(".help-icon").show();
					if(!formLoaded)
					{
						var aFront = [];
						var aBack = [];
						
						if(pro && proType == '2')
						{
							
							//Edit card details
							if(editCard!=""){
								//Build dem blocks
								var iCount = parseInt(getXML(xmlData,"statCount")); 
								for(i=1; i<iCount; i++){
									var frontorback = getXML(xmlData,"stats/stat_"+i+"/frontorback");
									if(frontorback=="1"){
										var objCard = $("#front.ownImage");
										var list = $(".front");
									}else{
										var objCard = $("#back.ownImage");
										var list = $(".back");
									}
									
									var t = getXML(xmlData,"stats/stat_"+i+"/top");
									var l = getXML(xmlData,"stats/stat_"+i+"/left");
									var w = getXML(xmlData,"stats/stat_"+i+"/width");
									var h = getXML(xmlData,"stats/stat_"+i+"/height");
									var c = getXML(xmlData,"stats/stat_"+i+"/bcolor");
									var type = getXML(xmlData,"stats/stat_"+i+"/type");
									var title = getXML(xmlData,"stats/stat_"+i+"/description");
									var alt = getXML(xmlData,"stats/stat_"+i+"/stat_id");
									objCard.append("<div id='c"+i+"' title='"+title+"' alt='"+alt+"' class='lblField' style='position:absolute;display:block;border:1px solid rgb("+c+");top:"+t+"px;left:"+l+"px;width:"+w+"px;height:"+h+"px;'></div>");
									//Add them to the left list
									
									list.append("<li alt='"+alt+"'><span>"+type+": "+title+"</span><div class='del-button'></div></li>")
									oPopup.find("#addedFields").find('p').hide();
								}
							}
							
							var cbo = '-1';
							var txt = '';
							oPopup.find("#cboFieldOptions").click(function(){
								var val = $(this).val();
								oPopup.find("#fieldsImages").find(".selected").attr('alt',val);
							});
							
							oPopup.find("#txtFieldDescription").change(function(){
								var val = $(this).val();
								oPopup.find("#fieldsImages").find(".selected").attr('title',val);
							});
							
				//Piero Mod			
				var offset = 0;
				var iLeft;
				var iTop;
				var x1,x2,y1,y2;
				oPopup.find("#fieldsImages").find(".ownImage").addClass('imageHover');
				oPopup.find("#fieldsImages").find(".ownImage").unbind().mousedown(function(e){
					var alt = oPopup.find("#cboFieldOptions").val();
					var title = oPopup.find("#txtFieldDescription").val();
					$("#current").attr({ id: '' });
					box = $('<div class="lblField selected" alt="0" title="'+title+'" style="position:fixed;">').hide();
					$(this).append(box);
					editLock = true;
					x1 = e.pageX;
					y1 = e.pageY;
					
					box.attr({id: 'current'}).css({
						top: e.pageY , //offsets
						left: e.pageX //offsets
					}).fadeIn();
				
				}).mousemove(function(e){
					
					$("#current").css({
						width:Math.abs(e.pageX - x1), //offsets
						height:Math.abs(e.pageY - y1) //offsets
					}).fadeIn();
					
				}).mouseup(function(){
					var id = $(this).attr('id');
					offset = $(this).offset();
					iLeft = offset.left+1;
					iTop = offset.top+1;
					$("#current").attr({ id: '' }).css({
						position: 'absolute',
						left: x1-iLeft,
						top: y1-iTop
					});
					oPopup.find("#fieldsImages").find(".ownImage").removeClass('imageHover').unbind();
					oPopup.find("#disableFieldOptions").removeClass().addClass('select-type');
					
					//box drawn - add field to list
					oPopup.find("#addedFields").find('p').hide();
					oPopup.find("#addedFields").find("ul[class='"+id+"']").append('<li alt="0"><span class="selected">Select Type and Enter Text</span><div class="del-button"></div></li>');
					
					activeField = '0';
					
					updateFieldTypesCombo(id);
				});
							
							activateTextboxes(oPopup.find(step2show).find("#proFields"));
							
							formLoaded = true;
						}
						else
						{
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
							
							//oPopup.find("#fieldsImages").find(".lblField").remove();
							
							//add front fields
							var iTop = 15;
							
							for(var i=0; i<aFront.length; i++)
							{
								var fieldname = aFront[i];
								var field = oPopup.find("#frmDetails").find(".textbox[name='"+fieldname+"']");
								var val = field.val();
								var alt = field.attr('alt');
								if( oPopup.find("#fieldsImages").find(image+"[id='front']").find(".lblField[alt='"+alt+"']").size() )
								{
									oPopup.find("#fieldsImages").find(image+"[id='front']").find(".lblField[alt='"+alt+"']").text(val).attr({
										'alt':alt,
										'id':fieldname
									});
								}
								else
								{
									oPopup.find("#fieldsImages").find(image+"[id='front']")
										.append('<div class="lblField" id="'+fieldname+'" alt="'+alt+'" style="left:25px; top:'+iTop.toString()+'px;">'+val+'</div>');
									iTop+= 25;
								}
							}
							//remove fields not added anymore
							oPopup.find("#fieldsImages").find(image+"[id='front']").find(".lblField").each(function(){
								var id = $(this).attr('id');
								if(aFront.indexOf(id) == -1)
								{
									$(this).remove();
								}
							});
							
							//add back fields
							var iTop = 15;
							for(var i=0; i<aBack.length; i++)
							{
								var fieldname = aBack[i];
								var field = oPopup.find("#frmDetails").find(".textbox[name='"+fieldname+"']");
								var val = field.val();
								var alt = field.attr('alt');
								if( oPopup.find("#fieldsImages").find(image+"[id='back']").find(".lblField[alt='"+alt+"']").size() )
								{
									oPopup.find("#fieldsImages").find(image+"[id='back']").find(".lblField[alt='"+alt+"']").text(val).attr({
										'alt':alt,
										'id':fieldname
									});
								}
								else
								{
									oPopup.find("#fieldsImages").find(image+"[id='back']")
										.append('<div class="lblField" id="'+fieldname+'" alt="'+alt+'" style="left:25px; top:'+iTop.toString()+'px;">'+val+'</div>');
									iTop+= 25;
								}
							}
							//remove fields not added anymore
							oPopup.find("#fieldsImages").find(image+"[id='back']").find(".lblField").each(function(){
								var id = $(this).attr('id');
								if(aBack.indexOf(id) == -1)
								{
									$(this).remove();
								}
							});
							
							if(editCard!=""){
								//UPDATE THE DATA ON THE EXISTING FIELDS - FRONT
								oPopup.find("#fieldsImages").find(image+"[id='front']").find(".lblField").each(function(){
									var statID = parseInt($(this).attr("alt"));
									var i = findXmlOfStat(statID,1);
									if(i > 0){
										sTop = getXML(xmlData,"stats/stat_"+i+"/top")+"px";
										sLeft = getXML(xmlData,"stats/stat_"+i+"/left")+"px";
										sWidth = getXML(xmlData,"stats/stat_"+i+"/width")+"px";
										sHeight = getXML(xmlData,"stats/stat_"+i+"/height")+"px";
										sFontSize = pointToPixel(getXML(xmlData,"stats/stat_"+i+"/size"))+"px";
										sFont = getXML(xmlData,"stats/stat_"+i+"/font_name");
										sFontColor = "#"+getXML(xmlData,"stats/stat_"+i+"/color");
										$(this).css({
											top:sTop,
											left:sLeft,
											color:sFontColor,
											fontSize:sFontSize,
											fontFamily:sFont
										})
									}
								});
								
								//UPDATE THE DATA ON THE EXISTING FIELDS - BACK
								oPopup.find("#fieldsImages").find(image+"[id='back']").find(".lblField").each(function(){
									var statID = parseInt($(this).attr("alt"));
									var i = findXmlOfStat(statID,2);
									if(i > 0){
										sTop = getXML(xmlData,"stats/stat_"+i+"/top")+"px";
										sLeft = getXML(xmlData,"stats/stat_"+i+"/left")+"px";
										sWidth = getXML(xmlData,"stats/stat_"+i+"/width")+"px";
										sHeight = getXML(xmlData,"stats/stat_"+i+"/height")+"px";
										sFontSize = pointToPixel(getXML(xmlData,"stats/stat_"+i+"/size"))+"px";
										sFont = getXML(xmlData,"stats/stat_"+i+"/font_name");
										sFontColor = "#"+getXML(xmlData,"stats/stat_"+i+"/color");
										$(this).css({
											top:sTop,
											left:sLeft,
											color:sFontColor,
											fontSize:sFontSize,
											fontFamily:sFont
										})
									}
								});
							}
							
							
						}
					}
					break;
				
				
				case '4':
					oPopup.find("#fieldsImages").find(".ownImage").removeClass('imageHover');
					oPopup.find("#fieldsImages").find(".ownImage").unbind();
					
					// oPopup.find(".lblField").each(function(){
						// var title = $(this).parent().parent().find(".textbox").attr('title');
						// var alt = $(this).parent().parent().find(".textbox").attr('alt');
						// var name = $(this).parent().parent().find(".textbox").attr('name');
						// if($(this).attr('id') == 'f')
						// {
							// aFront.push( name );
						// }
						// else
						// {
							// aBack.push( name );
						// }
					// });
					
					sTitle = sTitle+'preview and save card';
					oPopup.find(".help-icon").hide();
					
					//prepare card image holders
					var cardImages = oPopup.find("#fieldsImages").html();
					oPopup.find("#cardImages").addClass(sOrientation).html(cardImages);
					oPopup.find("#cardImages").find(".lblField").removeClass('selected');
					oPopup.find("#cardImages").find(".orientation-holder").remove();
					
					$.fn.smartImage = function(url){
						var t = this;
						//create an img so the browser will download the image:
						$('<img />')
						.attr('src', url)
						.load(function(){ //attach onload to set img src
							t.each(function(){ 
								//$(this).css('backgroundImage', 'url('+url+')' ).empty();
								//$(this).attr('src',url);
									$(this).empty().empty().append('<img src="'+src+'" />');
									
							});
						});
						return this;
					}
					
					if(pro)
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
						var src = getImageSrc(oPopup.find("#fieldsImages").find(".templateImage[id='front']"));
						oPopup.find("#cardImages").find(".templateImage[id='front']")
							.empty()
							.css('background','transparent')
							.append('<img src="'+src+'" />');
						//oPopup.find("#cardImages").find(".templateImage[id='front']").smartImage(src);
						//load back image
						var src = getImageSrc(oPopup.find("#fieldsImages").find(".templateImage[id='back']"));
						oPopup.find("#cardImages").find(".templateImage[id='back']")
							.empty()
							.css('background','transparent')
							.append('<img src="'+src+'" />');
						//oPopup.find("#cardImages").find(".templateImage[id='back']").smartImage(src);
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
			if(pro)
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
	
	//search
	$("#txtSearch")
	.bind('click focus',function(){
		if($(this).val() == 'Search..')
		{
			$(this).val('');
		}
	})
	.bind('blur',function(){
		//searchString = $(this).val();
		if($.trim($(this).val()) == '')
		{
			$(this).val('Search..');
		}
	});
	
	
	//reset filters button
	$("#cmdResetFilters").click(function(){
		alert('resetting all filters');
	});


	//search button
	$("#cmdSearch").click(function(){
		searchCards();
	});
	
	//search textbox
	$("#txtSearch").keypress(function(e){
		if(e.keyCode == 13)
		{
			searchCards();
		}
	});
   
	
	
	function searchCards()
	{
		var clearsearch = false;
		var orientation = $("input[name='optOrientation']:checked").val();
		var searchString = $.trim($("#txtSearch").val());
		if(searchString == $("#txtSearch").attr('alt')) searchString = '';
		
		$(".templates-holder").find(".block").hide().removeClass('inplay');
		if(searchString=='')
		{
			clearsearch = true;
			$(".templates-holder").find(".block").each(function(){
				var o = $(this).find(".orientation-holder").find(".orientation").attr('id');
				if(orientation == 'both' || orientation == o)
				{
					$(this).addClass('inplay');
				}
			});
		}
		else
		{
			$(".templates-holder").find(".block").each(function(){
				var tag = $(this).find(".searchtag").val();
				if(tag.indexOf(searchString) > -1)
				{	
					var o = $(this).find(".orientation-holder").find(".orientation").attr('id');
					if(orientation == 'both' || orientation == o)
					{
						$(this).addClass('inplay');
					}
				}
			});
		}
		
		selectPage(1);
		
		//results message
		if(clearsearch)
		{
			$("#search-message").hide();
		}
		else
		{
			var total = $(".templates-holder").find(".block").size();
			var found = $(".templates-holder").find(".inplay").size();
			if(found < 1)
			{
				$("#search-message").html('No cards found').show();
			}
			else
			{
				$("#search-message").html(found+'/'+total+' cards found').show();
			}
		}
		
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
	
	var disableClick = false;
	
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
					updateFieldTypesComboCustom(oPopup);
				});
			}
		});
		
		//remove field
		oPopup.find(".close-small").livequery('click',function(){
			$(this).parent().parent().hide('fast',function(){
				$(this).remove();
				updateFieldTypesComboCustom(oPopup);
				if( oPopup.find("#frmDetails").find("table tbody").find("tr:visible").size() == 9 )
				{
					oPopup.find("#frmDetails").find("table tbody").find("tr:hidden").show();
				}
			});
		});
		
		
		//select all fields
		oPopup.find("#cmdSelectAll").click(function(){
			disableClick = true;
			oPopup.find("#fieldsImages").find(".lblField").addClass('selected');
			oPopup.find("#cboFontFamily").val('-1');
			oPopup.find("#cboFontSize").val('-1');
			oPopup.find("#cboFontColour").val('-1');
		});
		
		
		//added fields behaviour
		oPopup.find("#addedFields").find("span").livequery('click',function(e){
			/*
			var col = oPopup.find("[name='cboBorderColor']").val();
			var text = oPopup.find("#txtFieldDescription").val();
			var sType = oPopup.find("#cboFieldOptions option:selected").val();
			
			var sClassName = $(".add-button-disabled").attr("id");
			var alt = $(this).parent().attr('alt');
			var id = $(this).parent().parent().attr('class');
			oPopup.find("#addedFields").find("span.selected").removeClass('selected');
			$(this).addClass('selected');
			//$(this).parent().parent().find("ul[class='"+id+"']").find(".lblField[alt='"+alt+"']").click();
			oPopup.find("#fieldsImages").find(".ownImage[id='"+id+"']").find(".lblField[alt='"+alt+"']").click();
			*/
		});
		
		//image fields behaviour
		oPopup.find("#fieldsImages").find(".lblField").livequery('click',function(e){
			
			//set selected field
			selectedField = $(this);
			
			//indicate selection
			var multi = false;
			if(e.ctrlKey) {
				//Ctrl+Click
				//leave previously selected field as selected
				multi = true;
			}
			else
			{
				oPopup.find("#fieldsImages").find(".lblField").removeClass('selected');
			}
			$(this).addClass('selected');
			
			//set draggable
			oPopup.find("#fieldsImages").find(".lblField").draggable("destroy");
			$(this).draggable({containment:"parent"});
			
			if(pro && proType == '2')
			{
				//set field data
				var cbo = $(this).attr('alt');
				var txt = $(this).attr('title');
				var id = $(this).parent().attr('id');
				oPopup.find("#cboFieldOptions").val(cbo);
				oPopup.find("#txtFieldDescription").val(txt);
				if(cbo != '0')
				{
					oPopup.find("#disableFieldOptions").removeClass().addClass('high-show');
				}
				else
				{
					oPopup.find("#disableFieldOptions").removeClass().addClass('select-type');
				}
				oPopup.find("#addedFields").find("ul").find(".selected").removeClass('selected');
				oPopup.find("#addedFields").find("ul[class='"+id+"']").find("li[alt='"+cbo+"']").find("span").addClass('selected');
				updateFieldTypesCombo(id);
			}
			else
			{
				//set font settings
				if(multi)
				{
					oPopup.find("#cboFontFamily").val('-1');
					oPopup.find("#cboFontSize").val('-1');
					oPopup.find("#cboFontColour").val('-1');
				}
				else
				{
					var fontFamily = $(this).css('font-family');
					var fontSize = parseInt($(this).css('font-size'),10).toString();
					var fontColour = rgb2hex($(this).css('color'));
					fontFamily = fontFamily.replace(/"/g,'');
					oPopup.find("#cboFontFamily").val(fontFamily);
					oPopup.find("#cboFontSize").val(fontSize);
					oPopup.find("#cboFontColour").val(fontColour);
				}
				oPopup.find("#disableFieldOptions").removeClass().addClass('enter-text');
			}
			
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
			$(this).find("option[value='-1']").hide();
		});
		oPopup.find("#cboFontSize").change(function(){
			var val = $(this).val()+'px';
			oPopup.find("#fieldsImages").find(".selected").each(function(){
				$(this).css('font-size',val);
			});
			$(this).find("option[value='-1']").hide();
		});
		oPopup.find("#cboFontColour").unbind().bind('click change',function(){
			if(!disableClick)
			{
				var val = $(this).val();
				oPopup.find("#fieldsImages").find(".selected").each(function(){
					$(this).css('color',val);
				});
			}
			disableClick = false;
			$(this).find("option[value='-1']").hide();
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
				//final step -> save the card
				
				var d = oPopup.find("input[name='txtCardName']").val();
				var o = (sOrientation == 'landscape') ? '2' : '1';
				var p = (pro) ? '1' : '0';
				var s = oPopup.find("input[name='txtTags']").val();
				if(s == oPopup.find("input[name='txtTags']").attr('title')) s = '';
				
				if(loggedIn == '1')
				{
					saveCard(d, o, sFrontImage, sBackImage, aFrontFields, aBackFields, s, pro, cardtype, template);
				}
				else
				{
					//show login popup with signup option
					var f = function(user){
						if(typeof(user)=="undefined")
						{
							user = '';
						}
						saveCard(d, o, sFrontImage, sBackImage, aFrontFields, aBackFields, s, pro, cardtype, template, user);
					}
					showLoginPopup('Save Card', 'Login and Save', 'You must log in to save the card.<br />If you don\'t have an account, sign up for FREE!', f);
				}
			}
			else
			{
				var step = $(this).attr('alt');
				var step2hide = (parseInt(step)-1).toString();
				if(step == 4)
				{
					var img = (pro) ? '.ownImage' : '.templateImage';
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
						var name = (proType=='2') ? 'txt' : $(this).attr('id');
						name = name.split(' ').join('');
						var value = (proType=='2') ? encodeURIComponent($(this).attr('title')) : encodeURIComponent($(this).html());
						var top = parseInt($(this).css('top'),10);
						var left = parseInt($(this).css('left'),10);
						var width = parseInt($(this).css('width'),10);
						var height = parseInt($(this).css('height'),10);
						var font = getFontFile($(this).css('font-family'));
						var font_id = findFontID($(this).css('font-family'));
						var size = aSizes[ parseInt($(this).css('font-size'),10) ];
						var color = rgb2hex($(this).css('color')).substring(1);
						var borderColor = rgb2hex($(this).css('borderTopColor')).substring(1);
						var field = [id, name, value, left.toString(), top.toString(), width.toString(), height.toString(), font, size, color, font_id, borderColor];
						aFrontFields.push( field.join('|') );
					});
					aBackFields = [];
					oPopup.find("#fieldsImages").find(img+"[id='back']").find(".lblField").each(function(){
						var id = $(this).attr('alt');
						var name = (proType=='2') ? 'txt' : $(this).attr('id');
						name = name.split(' ').join('');
						var value = (proType=='2') ? encodeURIComponent($(this).attr('title')) : encodeURIComponent($(this).html());
						var top = parseInt($(this).css('top'),10);
						var left = parseInt($(this).css('left'),10);
						var width = parseInt($(this).css('width'),10);
						var height = parseInt($(this).css('height'),10);
						var font = getFontFile($(this).css('font-family'));
						var font_id = findFontID($(this).css('font-family'));
						var size = aSizes[ parseInt($(this).css('font-size'),10) ];
						var color = rgb2hex($(this).css('color')).substring(1);
						var borderColor = rgb2hex($(this).css('borderTopColor')).substring(1);
						var field = [id, name, value, left.toString(), top.toString(), width.toString(), height.toString(), font, size, color, font_id, borderColor];
						aBackFields.push( field.join('|') );
					});
				}
				showStep('frmStep', step2hide, step);
			}
		});
		
	}
	
	
	
	//FREE card creator *********************************************************
	
	$("#page-contents").find(".templateImage").livequery('click',function()
	{
		steps = 4;
		pro = false;
		
		var currentStep = 1;
		var width = 900;
		var height = 640;
		var title = 'Free Card Creator: step '+currentStep+' of '+steps+' - select template';
		var templateName = $(this).parent().parent().find(".title").html();
		var templateImages = $(this).parent().html();
		
		template = $(this).css("background-image");
		
		var contents = $("#frmFree").html()+$("#frmAll").html();
		var orientation = $(this).parent().find(".orientation-holder").find(".orientation").attr('id');
		sOrientation = orientation;
		
		oPopup = buildPopup(width, height, title, contents, 707);
		
		//init
		oPopup.find("#templateImages").addClass(sOrientation);
		oPopup.find(".help-icon").hide().find("a").attr('id','0');
		
		//close button
		oPopup.find(".close").click(function(){
			destroyPopup(707);
		});
		
		var w;
		var h;
		if(orientation == 'portrait')
		{
			w = 250;
			h = 350;
		}
		else if(orientation == 'landscape')
		{
			w = 350;
			h = 250;
		}
		
		//set images for fields
		oPopup.find("#fieldsImages").html(templateImages);
		oPopup.find("#fieldsImages").find(".orientation-holder").remove();
		oPopup.find("#fieldsImages").find(".templateImage").each(function(){
			var alt = $(this).attr('alt');
			$(this).css('background-image','url('+alt+')').empty();
		});
		
		//fields images
		oPopup.find("#fieldsImages").find(".templateImage")
			.css({
				width:w,
				height:h,
				'float':'left',
				marginLeft:0,
				marginRight:10,
				marginTop:0,
				marginBottom:10,
				cursor:'default'
			})
			.removeClass('hidden')
			.show();
		
		//set template name and images
		oPopup.find("h1").html(templateName);
		oPopup.find("#templateImages").html(templateImages);
		
		//orientation
		oPopup.find("#templateImages").find(".orientation-holder").remove();
		
		//template images
		
		oPopup.find("#templateImages").find(".templateImage").each(function(){
			var alt = $(this).attr('alt');
			$(this).html(sLoadingIcon)
				.css('background-image','')
				.removeClass('hidden')
				.show();
			$(this).smartBackgroundImage(alt, false);
		});
		
		//activate the default creator elements
		activateCardCreator(oPopup);
		
	});
	
	//END OF: FREE card creator
	
	
	
	
	//PAID USER CARD CREATORS...

	var imgScale = [];
	var imgRotate = [];
	var imgWidth = [];
	var imgHeight = [];
	
	var pro = '<?php echo($_SESSION['pro']); ?>';
	var paid = '<?php echo($_SESSION['paid']); ?>';
	var proType;
	
	var updateFieldTypesCombo;
	
	function updateFieldTypesComboCustom(oPopup)
	{
		oPopup.find("#cboFieldtypes").find("option").removeClass('disabled').attr("disabled",false);
		oPopup.find("#table-fields").find(".textbox").each(function(){
			var alt = $(this).attr('alt');
			oPopup.find("#cboFieldtypes").find("option[value='"+alt+"']").addClass('disabled').attr("disabled",true);
		});
	};
	
	function loadProCreator(obj)
	{
		imgScale = [];
		imgRotate = [];
		imgWidth = [];
		imgHeight = [];
		
		proType = obj.attr('alt');

		if(iPro!='0' && iPaid=='1')
		{
			
			updateFieldTypesCombo = function(side)
			{
				if(typeof(side)=="undefined")
				{
					oPopup.find("#cboFieldOptions").find("option").removeClass('disabled').attr('disabled',false);
					oPopup.find("#addedFields").find('li').each(function(){
						var alt = $(this).attr('alt');
						var selOption = oPopup.find("#cboFieldOptions").find("option[value='"+alt+"']");
						if(selOption.html()!="Web Address"){
							selOption.addClass('disabled').attr('disabled',true);
						}
					});
				}
				else
				{
					oPopup.find("#cboFieldOptions").find("option").removeClass('disabled').attr('disabled',false);
					oPopup.find("#addedFields").find("ul."+side).find('li').each(function(){
						var alt = $(this).attr('alt');
						var selOption = oPopup.find("#cboFieldOptions").find("option[value='"+alt+"']");
						if(selOption.html()!="Web Address"){
							selOption.addClass('disabled').attr('disabled',true);
						}
					});
				}
			}
			
			
			
			steps = 4;
			var pro = true;
			
			var currentStep = 1;
			var width = 900;
			var height = 640;
			var templateName = obj.parent().parent().find(".title").html();
			var templateImages = obj.parent().html();
			var contents = $("#frmPro").html()+$("#frmAll").html();
			var activeSide = 'front';
			var title = (proType=='1') ? 'Custom Pro: ' : 'Upload Pro: ';
			var activeField = '0';
			
			title += 'step '+currentStep+' of '+steps+' - upload image(s)';
			
			oPopup = buildPopup(width, height, title, contents, 708);
			
			formLoaded = false;
			imagesLoaded = false;
			
			oPopup.find(".help-icon").show().find("a").attr('id',proType);
			
			//init
			if(proType=='2') //upload pro
			{
				//hide the parts not needed for upload pro
				oPopup.find(".hideforupload").hide();
				
				//show the parts needed for upload pro
				oPopup.find(".showforupload").show();
				
				//delete button
				oPopup.find(".del-button").livequery('click',function(){
					var alt = $(this).parent().attr('alt');
					var side = $(this).parent().parent().attr('class');
					oPopup.find("#txtFieldDescription").val(oPopup.find("#txtFieldDescription").attr('alt'));
					oPopup.find("#cboFieldOptions").val('-1');
					oPopup.find("#fieldsImages").find(".selected").removeClass('selected');
					oPopup.find("#fieldsImages").find("#"+side).find(".lblField[alt='"+alt+"']").remove();
					$(this).parent().remove();
					oPopup.find("#disableFieldOptions").removeClass().attr("style","");
					if(oPopup.find("#addedFields ul").find("li").size() < 1)
					{
						oPopup.find("#addedFields").find("p").show();
					}
					else
					{
						oPopup.find("#addedFields").find(".selected").removeClass('selected');
					}
					
					var offset = 0;
				var iLeft;
				var iTop;
				var x1,x2,y1,y2;
				oPopup.find("#fieldsImages").find(".ownImage").addClass('imageHover');
				oPopup.find("#fieldsImages").find(".ownImage").unbind().mousedown(function(e){
					var alt = oPopup.find("#cboFieldOptions").val();
					var title = oPopup.find("#txtFieldDescription").val();
					$("#current").attr({ id: '' });
					box = $('<div class="lblField selected" alt="0" title="'+title+'" style="position:fixed;">').hide();
					$(this).append(box);
					editLock = true;
					x1 = e.pageX;
					y1 = e.pageY;
					
					box.attr({id: 'current'}).css({
						top: e.pageY , //offsets
						left: e.pageX //offsets
					}).fadeIn();
				
				}).mousemove(function(e){
					
					$("#current").css({
						width:Math.abs(e.pageX - x1), //offsets
						height:Math.abs(e.pageY - y1) //offsets
					}).fadeIn();
					
				}).mouseup(function(){
					var id = $(this).attr('id');
					offset = $(this).offset();
					iLeft = offset.left+1;
					iTop = offset.top+1;
					$("#current").attr({ id: '' }).css({
						position: 'absolute',
						left: x1-iLeft,
						top: y1-iTop
					});
					oPopup.find("#fieldsImages").find(".ownImage").removeClass('imageHover').unbind();
					oPopup.find("#disableFieldOptions").removeClass().addClass('select-type');
					
					//box drawn - add field to list
					oPopup.find("#addedFields").find('p').hide();
					oPopup.find("#addedFields").find("ul[class='"+id+"']").append('<li alt="0"><span class="selected">Select Type and Enter Text</span><div class="del-button"></div></li>');
					
					activeField = '0';
					
					updateFieldTypesCombo(id);
				});
					
				});
				
				//select type
				oPopup.find("#cboFieldOptions").bind('change',function(){
					if(!$(this).hasClass('disabled'))
					{
						//selectedField
						var type = $(this).val();
						var text = $(this).find("option:selected").text() + ': ' + oPopup.find("#txtFieldDescription").val();
						oPopup.find("#addedFields").find("li[alt='"+activeField+"']").attr('alt',type).find("span").html(text);
						oPopup.find(".selected").attr("alt",type);
						oPopup.find("#disableFieldOptions").removeClass().addClass('enter-text');
						activeField = type;
						updateFieldTypesCombo();
					}
				});
				
				//enter text
				oPopup.find("#txtFieldDescription").bind('keyup',function(){
					var border = oPopup.find("[name='cboBorderColor']").val();
					var type = oPopup.find("#cboFieldOptions").val();
					var val = $(this).val();
					var id = oPopup.find("#fieldsImages").find(".selected").parent().attr('id');
					if(val=='') val = 'Enter Text';
					var text = oPopup.find("#cboFieldOptions").find("option:selected").text() + ': ' + val;
					oPopup.find("#addedFields").find("ul[class='"+id+"']").find("li[alt='"+type+"']").find("span").html(text);
					if(border != "-1"){
						oPopup.find("#disableFieldOptions").removeClass().css({height:0});
					}else{
						oPopup.find("#disableFieldOptions").removeClass().addClass("high-show");
					}
				});
				
				//select bordercolor
				oPopup.find("[name='cboBorderColor']").bind('change',function(){
					oPopup.find("#disableFieldOptions").removeClass().css({height:0});
				});
				
				//add new field
				oPopup.find("#cmdAddField").click(function(){
					if(!$(this).hasClass('add-button-disabled'))
					{
						var col = oPopup.find("[name='cboBorderColor']").val();
						var text = oPopup.find("#txtFieldDescription").val();
						var sType = oPopup.find("#cboFieldOptions option:selected").val();
						
						if(col=="-1"){
							col="#FFF";
						}					
						
						oPopup.find("[title='"+text+"']").css("border","1px solid "+col);
						oPopup.find("#fieldsImages").find(".selected").removeClass('selected');
						oPopup.find("#txtFieldDescription").val(oPopup.find("#txtFieldDescription").attr('alt'));
						oPopup.find("#cboFieldOptions").val('-1');
						oPopup.find("#disableFieldOptions").removeClass();
						oPopup.find("#disableFieldOptions").attr("style","");
						oPopup.find("#drawInfo").removeClass('disabled-text');
						oPopup.find("#addedFields").find("li span").removeClass('selected');
						oPopup.find("#fieldsImages").find(".ownImage").addClass('imageHover');
						
						// ////////////////////////////////////////////////////////
						// ////////////////////////////////////////////////////////
						// Enable DRAW INFO BOXES
						// ////////////////////////////////////////////////////////
						// ////////////////////////////////////////////////////////
						
						
							var offset = 0;
				var iLeft;
				var iTop;
				var x1,x2,y1,y2;
				oPopup.find("#fieldsImages").find(".ownImage").addClass('imageHover');
				oPopup.find("#fieldsImages").find(".ownImage").unbind().mousedown(function(e){
					var alt = oPopup.find("#cboFieldOptions").val();
					var title = oPopup.find("#txtFieldDescription").val();
					$("#current").attr({ id: '' });
					box = $('<div class="lblField selected" alt="0" title="'+title+'" style="position:fixed;">').hide();
					$(this).append(box);
					editLock = true;
					x1 = e.pageX;
					y1 = e.pageY;
					
					box.attr({id: 'current'}).css({
						top: e.pageY , //offsets
						left: e.pageX //offsets
					}).fadeIn();
				
				}).mousemove(function(e){
					
					$("#current").css({
						width:Math.abs(e.pageX - x1), //offsets
						height:Math.abs(e.pageY - y1) //offsets
					}).fadeIn();
					
				}).mouseup(function(){
					var id = $(this).attr('id');
					offset = $(this).offset();
					iLeft = offset.left+1;
					iTop = offset.top+1;
					$("#current").attr({ id: '' }).css({
						position: 'absolute',
						left: x1-iLeft,
						top: y1-iTop
					});
					oPopup.find("#fieldsImages").find(".ownImage").removeClass('imageHover').unbind();
					oPopup.find("#disableFieldOptions").removeClass().addClass('select-type');
					
					//box drawn - add field to list
					oPopup.find("#addedFields").find('p').hide();
					oPopup.find("#addedFields").find("ul[class='"+id+"']").append('<li alt="0"><span class="selected">Select Type and Enter Text</span><div class="del-button"></div></li>');
					
					activeField = '0';
					
					updateFieldTypesCombo(id);
				});
						
						
						// ////////////////////////////////////////////////////////
						// ////////////////////////////////////////////////////////
						// end of : DRAW INFO BOXES
						// ////////////////////////////////////////////////////////
						// ////////////////////////////////////////////////////////
						}
				});
			}
			else
			{
				//show the parts not needed for upload pro
				oPopup.find(".hideforupload").show();
				
				//hide the parts needed for upload pro
				oPopup.find(".showforupload").hide();
				
			}
			
			//close button
			oPopup.find(".close").click(function(){
				destroyPopup(708);
			});
			
			//upload image command button
			oPopup.find("#cmdUploadImage").click(function(){
				
				var width = 480;
				var height = 200;
				var title = 'upload image';
				
				//prepare contents
				$("#frmUploadImage").find("#upload_target").attr('alt', activeSide);
				var contents = $("#frmUploadImage").html();
				
				//build popup	
				var oPopupA = buildPopup(width, height, title, contents, 655, 'z-index:5;');
				
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
					
					
					//reset the slider
					dontResize = true;
					slider.slider("value",100);
					dontResize = false;
					
					//getImageDimensions();
					
					oPopup.find(".cardImageHolder[id='"+activeSide+"']").find("img").draggable();
					
					destroyPopup(655);
					
					oPopup.find("#disable-tools").hide();
					
					imagesLoaded = false;
					
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
				if(!oPopup.find(".cardImageHolder[id='"+activeSide+"']").find(".ownImage").hasClass('default'))
				{
					var w = parseInt(oPopup.find(".cardImageHolder[id='"+activeSide+"']").find(".ownImage").css('width'),10);
					var h = parseInt(oPopup.find(".cardImageHolder[id='"+activeSide+"']").find(".ownImage").css('height'),10);
					if(!dontResize)
					{
						imgWidth[activeSide] = w;
						imgHeight[activeSide] = h;
					}
				}
			}
			
			function showTab(tab)
			{
				//hide tabs
				oPopup.find(".tabs").find(".tabz").removeClass('active');
				oPopup.find(".cardImagesHolder").find("#tabFront").hide();
				oPopup.find(".cardImagesHolder").find("#tabBack").hide();
				
				//show the tab
				oPopup.find(".tabz[alt='"+tab+"']").addClass('active');
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
				
				//background color
				var col = rgb2hex(oPopup.find(".cardImageHolder[id='"+activeSide+"']").css('background-color'));
				oPopup.find("select[name='cboBackgroundColor']").val(col);
				
				//enable/disable controls
				if(oPopup.find(".cardImageHolder[id='"+activeSide+"']").find("img").hasClass("default"))
				{
					oPopup.find("#disable-tools").show();
				}
				else
				{
					oPopup.find("#disable-tools").hide();
				}
			}
			
			//getImageDimensions();
				
				//front/back tabs
				oPopup.find(".tabz").click(function(){
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
					if(activeSide=="front"){
						sFrontColor = col;
					}else{
						sBackColor = col;
					}
				});
				
				//scale
				var scale = 100;
				var slider = oPopup.find("#slider").slider({
					value: scale,
					min: 10,
					max: 100,
					step: 1,
					slide: function(event, ui) {
						if(!dontResize)
						{
							if(imgWidth[activeSide]==0 || imgHeight[activeSide]==0) getImageDimensions();
							resizeImage(ui.value);
						}
						imgScale[activeSide] = ui.value;
					},
					change: function(event, ui) {
						if(!dontResize)
						{
							if(imgWidth[activeSide]==0 || imgHeight[activeSide]==0) getImageDimensions();
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
			showLoginPopup('Pro Card Creator', 'Login', 'You must have a Pro Mobidex account to use the Pro Creator.<br />Please log in.');
		}
		
	}
	
	//PRO and UPLOAD card creator ***********************************************
	
	$("#cmdOwnImages, #cmdOwnCard").click(function(){
		//loadProCreator($(this));
		window.location.href = "?page=create&pro=2";
	});
	
	//END OF: Pro card creator
	//========================
	
	
	function getFontFile(fontname)
	{
		fontname = fontname.replace(/"/g,'');
		var file = $(".contents").find("#cboFontFamily").find("option[value='"+fontname+"']").attr('alt');
		return file;
	}
	
	
	//hover over template block
	$(".templateImage").mouseover(function(){
		$(this).parent().find("#front").addClass('hidden');
		$(this).parent().find("#back").removeClass('hidden');
	})
	.mouseout(function(){
		$(this).parent().find("#back").addClass('hidden');
		$(this).parent().find("#front").removeClass('hidden');
	});
	
	//pagination buttons
	$(".page-buttons").find('.button-small').click(function(){
		if(!$(this).hasClass('button-small-disabled') && !$(this).hasClass('button-small-active'))
		{
			selectPage(parseInt($(this).attr('id')));
		}
	});
	
	//prev page button
	$("#prev").click(function(){
		if(!$(this).hasClass('button-small-disabled'))
		{
			if(getCurrentPage() > 1)
			{
				selectPage(getCurrentPage()-1);
			}
			else
			{
				selectPage(getPageCount());
			}
		}
	});
	
	//next page button
	$("#next").click(function(){
		if(!$(this).hasClass('button-small-disabled'))
		{
			if(getCurrentPage() <= getPageCount(-1))
			{
				selectPage(getCurrentPage()+1);
			}
			else
			{
				selectPage(1);
			}
		}
	});
	
	//filter options
	$("input[name='optOrientation']").click(function(){		
		searchCards();
	});
	
	
	
	//INIT
	
	$("#page-help").find(".help-icon").hide();
	
	selectPage(1);
	//$(".templates-holder").find(".template").show();
	var loadpro = '<?php echo($_GET['pro']); ?>';
	if(loadpro == '1')
	{
		loadProCreator($("#cmdOwnImages"));
	}
	else if(loadpro == '2')
	{
		loadProCreator($("#cmdOwnCard"));
	}
	
});
</script>