<?php

session_start();

?>
<html>
<head>
	<title>Ajax File Uploader Plugin For Jquery</title>
	<link rel="stylesheet" type="text/css" href="styles/style.css" />
	<script type="text/javascript" src="common/uploader/jquery.js"></script>
	<script type="text/javascript" src="common/uploader/ajaxfileupload.js"></script>
	<script type="text/javascript">
	
		var side;
	
		function ajaxFileUpload()
		{
			$("#loading")
			.ajaxStart(function(){
				$(this).show();
			})
			.ajaxComplete(function(){
				$(this).hide();
			});
			
			$.ajaxFileUpload
			(
				{
					url:'doajaxfileupload.php',
					secureuri:false,
					fileElementId:'fileToUpload',
					data:{name:'logan', id:'id', file:$("#fileToUpload").value()},
					success: function (result)
					{
						alert(result);
					},
					error: function()
					{
						alert('e');
					}
				}
			)
			
			return false;
			
		}
		
		$(document).ready(function(){
			
			side = parent.$("#upload_target").attr('alt');
			$("#cardSide").val(side);
			
			$("#submitUpload").click(function(){
				if(!$(this).hasClass("button-disabled"))
				{
					$(this).addClass("button-disabled");
					$("#frmUpload").hide();
					$("#loading").show();
					$("#frmUpload").submit();
				}
				return false;
			});
			
			$("#fileToUpload").change(function(){
				if($.trim($(this).val())!='')
				{
					$("#submitUpload").removeClass('button-disabled');
				}
			});
			
			if($("#cmdLoadImage").size())
			{
				$("#cmdLoadImage").click(function(){
					
					parent.loadImage( $("#filename").val() );
					
				});
			}
			
			
		});
		
	</script>
	
</head>
<body style="background:transparent;">
<?php

	
	//upload the file
	
	if(isset($_FILES) && sizeof($_FILES) > 0)
	{
		$cardSide = $_POST['side'];
		$file_id = 'fileToUpload';
		
		//Get file extension
		$file_title = $_FILES[$file_id]['name'];
		$ext_arr = split("\.",basename($file_title));
		$ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension
		
		//set the destination directory
		$dir = 'img/uploads';
		if(!file_exists($dir)) mkdir($dir);
		$dir.= '/';
		
		//set the destination path and filename
		//$filename = $_SESSION['user'] . date('YmdHis') . '.' . $ext;
		$filename = $_SESSION['user'] . '_' . $cardSide . '.' . $ext;
		$uploadfile = $dir . $filename;
		
		if (!move_uploaded_file($_FILES[$file_id]['tmp_name'], $uploadfile))
		{
			$result = "Cannot upload the file '".$_FILES[$file_id]['name']."'"; //Show error if any.
			if(!file_exists($folder)) {
				 $result .= " : Folder don't exist.";
			} elseif(!is_writable($folder)) {
				 $result .= " : Folder not writable.";
			} elseif(!is_writable($uploadfile)) {
				 $result .= " : File not writable.";
			}
			$file_name = '';
		}
		else
		{
			//show the image
			//echo '<img id="imgUploaded" src="img/uploads/'.$_SESSION['user'].'/'.$filename.'" />';
			$result =
			'<p style="text-align:center; padding:20px;">Image successfully uploaded.</p>'.
			'<div id="cmdLoadImage" class="button center" style="width:60px;">Ok</div>'.
			'<input type="hidden" id="filename" value="'.$uploadfile.'" />';
			
			if(!$_FILES[$file_id]['size']) { //Check if the file is made
				 //@unlink($uploadfile);//Delete the Empty file
				 $file_name = '';
				 $result = "Empty file found. Please select a valid file."; //Show the error message
			}
			else
			{
				chmod($uploadfile,0777);//Make it universally writable.
			}
		}
		
		echo $result;
		
		//echo '<pre>'.print_r($_SESSION,true).'</pre>';
		//echo '<pre>'.print_r($_FILES,true).'</pre>';
		
		exit;
	}


?>	
	<div style="position:relative;">
		
		<img id="loading" src="site/loading51.gif" style="display:none; width:32px; height:32px; margin-left:46%; margin-top:50px;">
		
		<form name="form" id="frmUpload" action="" method="POST" enctype="multipart/form-data" style="width:450px; margin-left:10px;">
			
			<p style="text-align:center;">
				Select a PNG or JPG file and click the <strong>Upload</strong> button (Max: 2MB)<br />
				<input id="fileToUpload" type="file" size="50" name="fileToUpload" class="input" style="margin-left:auto;margin-right:auto;margin-top:10px;" />
			</p>
				
			<input type="hidden" id="cardSide" name="side" value="11" />
			
			<div class="button button-disabled center" id="submitUpload" style="width:100px;margin-top:10px;">Upload</div>
			
		</form>
		
	</div>
   
</body>
</html>