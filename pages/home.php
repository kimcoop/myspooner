<?

	include('../functions/latest_activity.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#home').addClass('active');

});

</script>

<div id="container">


<h2>Latest Activity</h2>

<? echo formatLatestActivities() ?>

<? include('../include/footer.php') ?>