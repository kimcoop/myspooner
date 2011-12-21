<?

	include('../functions/blog.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {
			
			$('header span').removeClass('active');
			$('#blog').addClass('active');
			
			$('#addNewPost').click(function() {
				window.location.href = 'blogpost.php';
			});
			
		$('.articleComments').live('click', function() {
			$(this).next().next('.comments').slideToggle();
		});
				
		$('.postComment').click(function() {
			var el = $(this);
			var contentArea = el.siblings('.commentContent');
			var content = contentArea.val();
			var commentForm = el.parent().parent();
			var commentSection = commentForm.parent().parent();
			
			if (content != '') {
				var articleID = el.siblings('.artID').val();
				
				var dataString = 'action=comment&content='+content
													+'&articleID='+articleID;
				$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/blog.php',
						 data: dataString,
						 success: function(data) {
							contentArea.val('');
							commentSection.replaceWith(data.comments);
						 }
				});
							
				return false;
			}
			
			contentArea.val('');
			commentForm.slideUp();
		
		});
		
			
		$('.addComment').live('click', function() {
			$(this).children('.commentForm').slideDown();
		});
			
});
</script>

<div id="container">


<h2>Blog</h2>

<div class='button_container'>
	Write a new blog post!
	<div id="addNewPost" class="addNew"></div>
</div>

<!--<div id="searchResults"></div>

<form id="searchBlog" action="" method="post">
<input id="search" type="search"><input type="submit" id="doSearch" value="Search">
</form>-->

<div id="articles">
<? echo getAllArticles(); ?>
</div>


<? include('../include/footer.php') ?>