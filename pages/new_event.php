<?

	include_once('../functions/functions.php');
	include('../include/header.php');
	include_once('../functions/blog.php');
	include_once('../functions/calendar.php');
	/*CREATE TABLE `event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;*/
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#calendar').addClass('active');
			
			$('#createNewEvent').click(function() {
				var el = $('#newTag');
				var tag = el.val();
				
				if (tag != '') {
				
					var title = $('#title').val();
					var content = $('#content').val();
					var tags = $('input.tagForArticle:checked');				
					
					var dataString = 'action=createArticle&title='+title
														+'&content='+content;
														
					$.each(tags, function() {
						dataString += '&tags[]=' + $(this).val();
					});
				
				
					$.ajax({ 
								 type: 'post',
								 dataType: 'json',
								 url: '../functions/calendar_functions.php',
								 data: dataString,
								 success: function(data) {
								 	if (data.error) {
										$('#notice').text(data.error).addClass('errorText').css({
											'visibility':'visible',
											'opacity':0
										}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
									} else {
										$('#tagsAsCheckboxes').fadeTo('slow',0, function() {
											$(this).html( data.tagsAsCheckboxes ).fadeTo('slow',1);
											});
										}
								 }
						});
						
					}
					el.val('');
					return false;
				});	//end click

});

</script>

<div id="container">

<a class="back" href="calendar.php">&laquo;&nbsp;Back to calendar</a>

<h2>New Event</h2>

<div id="notice" style="visibility:hidden;"></div>

		
		<div id="tagsAsCheckboxes">
			<? echo getTagsAsCheckbox(); ?>
		</div>
		
		
		<div style='margin-left:3em'>
		<span class='addNew' id='createNewTag'></span>
		<input type="text" id="newTag" placeholder="New tag" style='width: 5em'>
		</div>

<? include('../include/footer.php'); ?>