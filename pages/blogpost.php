<?

	include('../functions/functions.php');
	include('../include/header.php');
	include('../functions/blog.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#blog').addClass('active');
	
	$('#postBlog').live('click', function(e) {
				e.preventDefault();
				var title = $('#title').val();
				var content = $('#content').val();
				var tags = $('input.tagForArticle:checked');
				var userTags = $('input.userTag:checked');				
				
				var dataString = 'action=createArticle&title='+title
													+'&content='+content;
													
				$.each(tags, function() {
					dataString += '&tags[]=' + $(this).val();
				});
				
				$.each(userTags, function() {
					dataString += '&userTags[]=' + $(this).val();
				});
				
				$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/blog.php',
						 data: dataString,
						 success: function(data) {
							if (data.error) {
								$('#error').text(data.error).fadeIn().delay(2000).fadeOut();
							} else {
								window.location.href = 'blog.php';
							}
						 }
				});
				
				return false;
			});

});

</script>

<div id="container">
<br>
<a class="back" href="blog.php">&laquo;&nbsp;Back to blog</a>

<h2>New Blog Post</h2>

<div id="error" class="errorText" style="display:none;"></div>

<div id="newPost">
	<form method="post" action="">
		<input type="text" maxlength="255" id="title" placeholder="Enter title here"><br>
		<textarea id="content" maxlength="4000" placeholder="Enter article content here"></textarea><br>	
		
		<div id="tagsAsCheckboxes">
			<? echo getTagsAsCheckbox(); ?>
		</div>
		
		
		<div style='margin-left:3em'>
		<span class='addNew' id='createNewTag'></span>
		<input type="text" id="newTag" placeholder="New tag" style='width: 5em'>
		</div>
		
		<? echo getUserTagsAsCheckbox(); ?>
		<br>
		<input type="submit" id="postBlog" value="Publish Post">
	</form>
</div>


<? include('../include/footer.php'); ?>