<html>
<head>
	<title>Welcome</title>
</head>
<body>


<p>You will be redirected in <span id="counter">5</span> second(s).</p>
<script type="text/javascript">
function countdown() {
    var i = document.getElementById('counter');
    if (parseInt(i.innerHTML)<=0) {
        location.href = 'login.php';
    }
    i.innerHTML = parseInt(i.innerHTML)-1;
}
setInterval(function(){ countdown(); },1000);
</script>
<?php
	
	echo " <center>You have successfully signed up!</center>";
	echo "<center><h1>welcome to Trendyz</h1><center>";
	header( "Refresh: 5;index.php", true, 303);



?>
</body>