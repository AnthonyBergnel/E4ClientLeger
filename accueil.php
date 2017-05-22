<?php
session_start();
if($_SESSION['emplCat']=="Exploitant" OR $_SESSION['emplCat']=="Chauffeur")
{
	?>
	<!DOCTYPE html>
	<html>
		<head>
			<title>PPE</title>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
			<link rel='stylesheet' type='text/css' href='bootstrap/css/bootstrap.css'/>
			<script type='text/javascript' src='bootstrap/js/bootstrap.min.js'></script>
		</head>
		<body>
			<div class='container'>
				<div class='row'>
					<div class=''>
						<p>Bonjour <?php echo $_SESSION['emplNom']." ".$_SESSION['emplPrenom']; ?></p>
					</div>
				</div>
				<h1>Vos pages</h1>
				<?php
				if($_SESSION['emplCat']=='Exploitant')
				{
					?>
					<a href="AC11.php" id='orgat'>Organiser les tourn&eacutees</a>
					<a href='AC11.php'><div class='lienimg'></div></a><img src='images/tournee.png' class='tournee'/>
					<?php
				}
				else
				{
					?>
					<a href="" id='orgat'>Liste des tourn&eacutees</a>
					<?php
				}
				?>
				<a href="deco.php" id='deconnexion'>D&eacuteconnexion</a>
			</div>
		</body>
</html>
<?php
}
else
{
	header("location:index.php");
}
?>
