<?php
	//Calling DB in order to get page working
	require_once('db.php');

	//Delete the object with the according ID when the delete button is clicked
	if(isset($_POST['delete'])){
		$stmt=$conn->prepare("DELETE FROM 
		urls WHERE ID = :id");
		$stmt->bindParam(":id", $_POST['delete']);
		$stmt->execute();
	}

	//If your link is active, you can click on the button "deactivate" to deactive the file 
	//=> get 404 page not found when you click on the shortlink
	elseif(isset($_POST['active'])){
		$stmt=$conn->prepare("UPDATE urls SET status = 0 WHERE ID = :id");
		$stmt->bindParam(":id", $_POST['active']);
		$stmt->execute();
	}
	//Or if your link is inactive, you can click on the button "activate" to activate the file 
	//=> get you redirected to the correct original link when you click on the shortlink
	elseif(isset($_POST['inactive'])){

		$stmt=$conn->prepare("UPDATE urls SET status = 1 WHERE ID = :id");
		$stmt->bindParam(":id", $_POST['inactive']);
        $stmt->execute();
	}

	if(isset($_COOKIE['userID'])){
		//get to display all objects from DB table "urls" WHERE userID = userID get in the cookie
		$stmt=$conn->prepare("SELECT * FROM urls WHERE userID =" . $_COOKIE['userID']);
        $stmt->execute();
		$urls = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	else
	{
		header("location: homepage.php");
	}
	
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Shortner'Up | Compte</title>
	<link rel="stylesheet" href="main.css"/>
	<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@700&family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
	<!--Header-->
	<header class="header">
        <a href="#" class="logo">
            <img src="images/logo.svg" alt="Website logo">
            <span>Shortner'Up</span>
        </a>
        <div class="header-buttons">
			<!--Log out Link-->
            <a href="logout.php" class="signout-button">Se déconnecter</a>
        </div>
    </header>
	<!--Main page-->
	<main>
		<!--Submit a link to get it shortened by the addLink.php-->
		<section class="form">
			<form action="addLink.php" method="post">
				<input type="text" name="original_url" placeholder="e.g. https://example.com" />
				<input type="submit" value="SHORTEN"/>
			</form>
		</section>
		
		<!--Table with all the urls displayed in it-->
		<form method="post">
		<table class="urls"> 

			<!--Table labels-->
			<tr class="url">
				<th class="short_url_header">Lien court</th>
				<th class="original_url_header">Lien original</th>
				<th class="clicks_header">Nombre de clics</th>
				<th class="status_header">Statut</th>
				<th class="delete_header">Supprimer le lien</th>
			</tr>

			<!--Table entries-->
			<!--Loop inside the table "urls" to get all entries displayed-->
			<?php foreach ($urls as $url) : ?> 
			<tr class="url">

				<!--Display shortlink-->
				<td class="short_url">
					<a href=
					<?php
						//If "status" column == 1 => get us to the original link when the shortlink is clicked
						if($url['status']==1)
						{
							echo 'http://localhost/url_shortener/getOrigin?link='.$url['uniqID'];
						}
						//Elseif "status" column == 0 => get us to the "disabled link - 404" page
						elseif($url['status']==0)
						{
							echo 'disabled.php';
						}
					?>
					>
						<!-- Display the shortlink in page, deactivated or not.-->
						http://localhost/url_shortener/getOrigin?link=<?= $url['uniqID']; ?> 
					</a>
				</td>

				<!-- Display the original link (clickable)-->
				<td class="original_url">
					<a href="<?= $url['original_url']; ?>" target="_blank"><?= $url['original_url']; ?>
					</a>
				</td>

				<!-- Display the number of clicks on your shortlink-->
				<td class="clicks">
					<a href="<?= $url['clicks']; ?>" target="_blank"><?= $url['clicks']; ?>
					</a>
				</td>

				<!-- Display the status of your shortlink and allows you to change it-->
				<td class="status">
					<?php
						//If "status" column == 1 => show you "your link is active" and the "deactivate" button
						if($url['status']==1)
						{
							echo '<div>&#10004; Lien activé</div><button value="'.$url['ID'].'" type="submit" name="active" class="active">désactiver</button>';
						}
						//Elseif "status" column == 0 => show you "your link is inactive" and the "activate" button
						elseif($url['status']==0)
						{
							echo '<div>&#x274C; Lien désactivé</div><button value="'.$url['ID'].'" type="submit" name="inactive" class="inactive">activer</button>';
						}
					?>
				</td>

				<!-- Display the delete button ; allows you to delete row from DB -->
				<td class="delete">

					<button value=<?= $url['ID']?> type="submit" name="delete" class="delete">Supprimer</button>
						
				</td>
			</tr>
			<!--End of the foreach loop-->
			<?php endforeach; ?>
		</table>
		</form>
	</main>
	<!--Footer-->
	<footer class="footer">
        <span>Made with &#x1F90D; by H2G2</span>
        <span>&#x1F98A; Audrey Pont – &#x1F428; Paul Gasselin – &#x1F984; Théo Sciancalepore</span>
    </footer>
</body>
</html>