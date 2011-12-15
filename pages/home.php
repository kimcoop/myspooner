<? //index 

	include('../functions/functions.php');
	include('../include/header.php');

	if (!$loggedIn) {
		echo "How did you end up here? You're not logged in.";
	} else {
		echo "Welcome, $form!";
	
?>



<h1>Logged in!</h1>
<? echo "Hello, $username" ?>



<? include('../functions/footer.php') ?>

<? } ?>