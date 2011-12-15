<? 	include('include/header.php');


?>

<script type="text/javascript">

$(function() {
		
		$('#loginButton').click(function(e) {
			e.preventDefault();
			var email = $('#email1').val();
			var password = $('#password1').val();
			
			var dataString = 'action=login&email='+email
												+'&password='+password;		
			$.ajax({ 
					 type: 'post',
					 dataType: 'json',
					 url: 'functions/functions.php',
					 data: dataString,
					 success: function(data) {
						if (data.error != '') {
						 	alert(data.msg);
						} else {
							$('#error').text(data.error).addClass('error').fadeIn();
						}
					 }
			});
			$('form input').val('');
			return false;
		});
		
		$('#registerButton').click(function(e) {
			e.preventDefault();
			var email = $('#email').val();
			var password = $('#password').val();
			var fname = $('#fname').val();
			var lname= $('#lname').val();
			
			var dataString = 'action=register&email='+email
												+'&password='+password
												+'&fname='+fname
												+'&lname='+lname;		
			$.ajax({ 
					 type: 'post',
					 dataType: 'json',
					 url: 'functions/functions.php',
					 data: dataString,
					 success: function(data) {
						if (data.error != '') {
							alert(data.msg);
						} else {
							$('#error').text(data.error).addClass('error').fadeIn();
						}
					}
			});
			$('form input').val('');
			return false;
		});
		
		
});

</script>

<div id="container">

<h1>MySpooner</h1>

<div id="error"></div>

<form method="post" id="loginForm" action="<? echo $form ?>">
	<table>
		<tr><th><label for=email1>Email:&nbsp;</label></th><td><input required autofocus name="email1" id="email1" type="email"/></td></tr>
		<tr><th><label for=password1>Password:&nbsp;</label></th><td><input required name="password1" id="password1" type="password"/></td></tr>
		<tr><td colspan="2" style="text-align:center"><input type="submit" name="login" id="loginButton" value="Login"/></td></tr>
	</table>
</form>
<br/>
<form method="post" id="registerForm" action="<? echo $form ?>">
	<table>
		<tr><th><label for=email>Email:&nbsp;</label></th><td><input required name="email" id="email" type="email"/></td></tr>
		<tr><th><label for=password>Password:&nbsp;</label></th><td><input required name="password" id="password" type="password"/></td></tr>
		<tr><th><label for=fname>First name:&nbsp;</label></th><td><input required name="fname" id="fname" type="text"/></td></tr>
		<tr><th><label for=lname>Last name:&nbsp;</label></th><td><input required name="lname" id="lname" type="text"/></td></tr>
		<tr><td colspan="2" style="text-align:center"><input type="submit" name="register" id="registerButton" value="Register"/></td></tr>
	</table>
</form>

</div><!-- end container -->


<? include('include/footer.php'); ?>