<?

	include('../functions/functions.php');
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
			
			$('.tag').click(function() {
				var tag = $(this).text();
				$('#container').load('filterblog.php', function() {
					$('#filterOn').text('Displaying blog posts tagged as '+tag);
					$.ajax({ 
						 type: 'post',
						 dataType: 'string',
						 url: '../functions/blog.php',
						 data: {
						 			'action': 'getArticlesByTag',
						 			'tag': tag
						 },
						 success: function(data) {
							if (data.error) {
								alert(data.error);
							} else {
								$('#posts').text(data.articles);
							}
						 }
					});
				});				
			});
			
		$('.addComment').click(function() {
			$(this).children('.commentForm').slideDown();
		});
		
		$('.postComment').click(function() {
			var el = $(this);
			var contentArea = el.siblings('.commentContent');
			var content = contentArea.val();
			
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
							el.parent().parent().slideUp();
						 }
				});
							
				return false;
			}
			
			contentArea.val('');
			el.parent().parent().slideUp();
		
		});
			
});
</script>

<div id="container">


<h2>Blog<span id="addNewPost" class="addNew"></span></h2>

<!--<div id="searchResults"></div>

<form id="searchBlog" action="" method="post">
<input id="search" type="search"><input type="submit" id="doSearch" value="Search">
</form>-->

<div id="articles">
<? echo getAllArticles(); ?>
</div>


<? include('../include/footer.php') ?>