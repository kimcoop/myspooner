<?
	session_start();
	include('../functions/functions.php');
	include('../include/header.php');
	include('../functions/wall.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#wall').addClass('active');

});

</script>

<div id="container">

<div id="mywall">
	<h2>Wall</h2>
	<? echo formatWall($_SESSION['user_id']) ?>
</div>


<? include('../include/footer.php') ?>