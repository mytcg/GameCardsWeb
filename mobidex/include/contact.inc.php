<?php

//

?>
<div id="page-title">We appreciate your feedback. What's on your mind?</div>
<?php

	//contact form
	$contents = <<<STR
	
	<div class="form" id="contact">
		
		<div class="field-holder">
			<div class="check-icon float-left hidden"></div>
			<div class="x-icon float-left"></div>
			<input type="text" class="textbox" name="txtFullname" alt="Full Name" value="Full Name" />
		</div>
		<div class="field-holder">
			<div class="check-icon float-left hidden"></div>
			<div class="x-icon float-left"></div>
			<input type="text" class="textbox" name="txtEmail" alt="Email" value="Email" />
		</div>
		<div class="field-holder">
			<div class="check-icon float-left hidden"></div>
			<div class="x-icon float-left"></div>
		<input type="text" class="textbox" name="txtSubject" alt="Subject" value="Subject" />
		</div>
		<div class="field-holder">
			<div class="check-icon float-left hidden"></div>
			<div class="x-icon float-left"></div>
			<textarea class="textbox" name="txtMessage" alt="Message" style="height:140px; resize:none;">Message</textarea>
		</div>
		
		<div class="clear"></div>
		
		<div class="error-message" style="padding:5px 90px 10px 10px;"></div>
		<div class="button float-right" id="cmdSend" style="width:50px; margin-right:7px;">Send</div>
		
	</div>
	
STR;
	buildBlock(580, 420, '', 'send us your message', $contents, 'position:relative; margin-left:auto; margin-right:auto;');
	
?>
<p style="text-align:center;">
	Or you can contact Mobidex on <a href="mailto:info@mobidex.co.za" style="font-size:14px;">info@mobidex.co.za</a>
</p>

<script>

function resetForm()
{
	//clear textbox fields
	$(".textbox").each(function(){
		$(this).val($(this).attr('alt')).keyup(function(){
			validateForm();
		});
	});
	
	//disable send button
	$("#cmdSend").addClass('button-disabled');
	
	//reset check and x-icons
	$(".form[id='contact']").find(".check-icon").hide();
	$(".form[id='contact']").find(".x-icon").show();
}

function validateForm()
{
	//Single trigger. If any one of the fields cross the line, it is game over!!!
	var invalid = false;
	
	$(".form[id='contact']").find(".field-holder").each(function(){
		var val = $.trim($(this).find(".textbox").val());
		var alt = $.trim($(this).find(".textbox").attr('alt'));
		if(val == '' || val == alt)
		{
			$(this).find(".check-icon").hide();
			$(this).find(".x-icon").show();
			invalid = true;
		}
		else
		{
			if(alt=='Email')
			{
				if(!validateEmail(val))
				{
					$(this).find(".check-icon").hide();
					$(this).find(".x-icon").show();
					invalid = true;
				}
				else
				{
					$(this).find(".x-icon").hide();
					$(this).find(".check-icon").show();
				}
			}
			else
			{
				$(this).find(".x-icon").hide();
				$(this).find(".check-icon").show();
			}
		}
	});
	
	if(invalid)
	{
		$("#cmdSend").addClass('button-disabled');
	}
	else
	{
		$("#cmdSend").removeClass('button-disabled');
	}
}

$(document).ready(function(){
	
	//form fields behaviour
	$(".textbox").bind('click focus',function(){
		var alt = $(this).attr('alt');
		var val = $.trim($(this).val());
		if(val == alt)
		{
			$(this).val('');
		}
	})
	.blur(function(){
		var alt = $(this).attr('alt');
		var val = $.trim($(this).val());
		if(val == '')
		{
			$(this).val(alt);
		}
	});
	
	//send button
	$("#cmdSend").click(function(){
		if(!$(this).hasClass('button-disabled'))
		{
			var f = $.trim($("input[name='txtFullname']").val());
			var e = $.trim($("input[name='txtEmail']").val());
			var s = $.trim($("input[name='txtSubject']").val());
			var m = $.trim($("textarea[name='txtMessage']").val());
			
			addLoader();
			
			$.ajax({
				type: 'POST',
				url: sUrl+'user.php',
				data: {
					action: 'send',
					fullname: f,
					email: e,
					subject: s,
					message: m
				},
				success: function(result){
					if(result == '1')
					{
						//success
					}
					else
					{
						//alert('Input -> '+result);
					}
					//reponse popup
					var width = 300;
					var height = 160;
					var title = 'Message Sent';
					var contents =
						'<p style="font-size:13px; text-align:center; margin-left:25px; margin-right:25px;">Thanks for the feedback.<br />We\'ll reply to you as soon as possible.</p>'+
						'<div id="cmdOk" class="button-small center" style="width:50px;">Ok</div>'
					;
					
					//album delete confirmation popup
					oPopup = buildPopup(width, height, title, contents);
					
					//delete button
					oPopup.find("#cmdOk").click(function(){
						destroyPopup();
						resetForm();
					});
					
					setTimeout("removeLoader()", 750);
				}
			});
		}
	});
	
	
	//INIT
	
	resetForm();
	
});
</script>