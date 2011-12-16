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
					$('#filterOn').text('Displaying posts tagged as '+tag);
				});
			});
			
});
</script>

<div id="container">


<h2>Blog<span id="addNewPost" class="addNew"></span></h2>

<div id="searchResults"></div>

<form id="searchBlog" action="" method="post">
<input id="search" type="search"><input type="submit" id="doSearch" value="Search">
</form>

<div id="articles">
<? echo getAllArticles(); ?>
</div>


<? include('../include/footer.php') ?>