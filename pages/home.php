<?

	include('../functions/latest_activity.php');
	include('../include/header.php');
	include_once('../functions/blog.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#home').addClass('active');
	
			$('input.notification').click(function() {
				var el = $(this);
				var tagID = el.val();
					$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/latest_activity.php',
						 data: {
						 			'action': 'markAsReceived',
						 			'tagID': tagID
						 },
						 success: function(data) {
								el.replaceWith( $("<span class='notificationMarked'></span").text(data.msg) );
							}
					});// end ajax
			});// end click

});

</script>

<div id="container">

<h2>Notifications</h2>

<div id="notifications">
<? echo getNotifications('count') ?>

<? echo formatNotifications() ?>
</div>

<h2>Latest Activity</h2>

<? echo formatJoiners();
	 echo formatLatestActivities() ?>

<? include('../include/footer.php') ?>