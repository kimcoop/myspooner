<?

	include('../functions/functions.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#blog').addClass('active');
	
	$('#postBlog').click(function(e) {
				e.preventDefault();
				var title = $('#title').val();
				var content = $('#content').val();
				
				var dataString = 'action=createArticle&title='+title
													+'&content='+content;
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
								//$('#successfulPost').text('Your post was added!').fadeIn().delay(2000).fadeOut();
								//alert(data.msg);
								//$('#articles').fadeOut();
								//$('#articles').text(data.articles).fadeIn();
							}
						 }
				});
				
				
				return false;
			});
			
	$('#backToBlog').click(function() {
		window.location.href = 'blog.php';
	});

});

</script>

<div id="container">

<p id="backToBlog" class="clickable" style="text-align:left">&laquo; Back to Blog</p>

<h2>New Blog Post</h2>

<div id="error" class="errorText" style="display:none;"></div>

<div id="newPost">
	<form method="post" action="">
		<input type="text" maxlength="255" id="title" placeholder="Enter title here"><br>
		<textarea id="content" maxlength="4000" placeholder="Enter article content here"></textarea><br>
		<input type="submit" id="postBlog" value="Publish Post">
	</form>
</div>

<? include('../include/footer.php') ?>