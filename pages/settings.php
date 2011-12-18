<?

	include('../functions/settings.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#settings').addClass('active');
	
	$('#edit').click(function() {
		$('.editable').each(function() {
			text = $(this).text();
			id = $(this).attr('id');
			$(this).replaceWith( $('<input id='+id+' type="text">').val(text) );
		});
	});
	
	$('#save').click(function() {
		var fname = $('#fname').val();
		var lname = $('#lname').val();
		var email = $('#email').val();	
		
		var dataString = 'action=updateUser&fname='+fname+
											'&lname='+lname+
											'&email='+email;
		$.ajax({ 
					 type: 'post',
					 dataType: 'json',
					 url: '../functions/settings.php',
					 data: dataString,
					 success: function() {
							alert('Your profile was updated!');
					 }
			});
			return false;
	
	});

});

</script>

<div id="container">


<h2>Settings</h2>

<div class='settings'>
	<? 
	
		$id = $_SESSION['user_id'];
		echo "<div class='settingType'><span class='type'>First name:</span><span id='fname' class='editable'>".getUsername($id)."</span></div>";
		echo "<br><br>";
		echo "<div class='settingType'><span class='type'>Last name:</span><span id='lname' class='editable'>".getLastName($id)."</span></div>";
		echo "<br><br>";
		echo "<div class='settingType'><span class='type'>Email:</span><span id='email' class='editable'>".getEmail($id)."</span></div>";
		echo "<br><br>";
		echo "<div class='settingType'><span class='type'>Joined on:</span><span>".toDate(getJoinDate($id))."</span></div>";
	
	?>
		<br>
		<input type='button' value='Edit' id='edit'>&nbsp;<input type='submit' id='save' value='Save'>
	
</div>

<? include('../include/footer.php') ?>