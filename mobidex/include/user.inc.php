<span>
<?php

if(isUserLoggedIn())
{
   $userPro = ($_SESSION['pro']>'0' && $_SESSION['paid']=='1') ? 'PRO' : '<a href="" style="text-decoration:none;" onclick="showPaymentGateway();return false;" title="Activate Pro Subscription">FREE</a>';
   echo '<span>'.$_SESSION['username'].' ('.$userPro.') is logged in</span>&mdash; <a href="#" class="logout">Logout</a>';
}
else
{
   echo '<a href="#" class="login">Login</a>';
}

?>
</span>