<?php

//

?>
<div id="page-title">Get Started by Signing Up! You can upgrade to Pro at any time.</div>

<div class="inline-window center" style="width: 60%;">
	<div class="table-container">
		<table class="center-all" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th><div class="mobidex-free center" alt="free"></div></th>
					<th></th>
					<th><div class="mobidex-pro disabled center" alt="pro"></div></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="left"><div class="check-icon"></div></td>
					<td class="middle" style="text-align:center;">Unlimited Card Sending</td>
					<td class="right"><div class="check-icon"></div></td>
				</tr>
				<tr>
					<td class="left"><div class="check-icon"></div></td>
					<td class="middle" style="text-align:center;">Collect, Store &amp; Organize Cards</td>
					<td class="right"><div class="check-icon"></div></td>
				</tr>
				<tr style="cursor: pointer;" title="Never have an out of date business card again. Every change you make is automatically pushed to all your contacts who already have your card.">
					<td class="left"><div class="check-icon"></div></td>
					<td class="middle" style="text-align:center;">Automatic Updates</td>
					<td class="right"><div class="check-icon"></div></td>
				</tr>
				<tr>
					<td class="left"><div class="x-icon"></div></td>
					<td class="middle" style="text-align:center;">View Card Distribution Tree</td>
					<td class="right"><div class="check-icon"></div></td>
				</tr>
				<tr>
					<td class="left"><div class="x-icon"></div></td>
					<td class="middle" style="text-align:center;">Use Custom Graphics</td>
					<td class="right"><div class="check-icon"></div></td>
				</tr>
				<tr>
					<td class="left"><div class="x-icon"></div></td>
					<td class="middle" style="text-align:center;">Card Uploader</td>
					<td class="right"><div class="check-icon"></div></td>
				</tr>
				<tr>
					<td class="left"><strong>Free</strong></td>
					<td class="middle" style="text-align:center;">Annual Cost</td>
					<td class="right"><strong>$19.99</strong></td>
				</tr>
				<tr>
					<td><div id="cmdSignUpFree" class="button center" style="width: 95px;">Free Sign Up</div></td>
					<td>&nbsp;</td>
					<td><div id="cmdSignUpPro" class="button center" style="width: 95px;">Pro Sign Up</div></td>
				</tr>
				<tr class="spacer">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<script>
$(document).ready(function(){
	
	$("#cmdSignUpFree").click(function(){
		showRegisterPopup();
	});
	
	$("#cmdSignUpPro").click(function(){
		showRegisterPopup('1');
	});
	
});
</script>

<?php

//

?>