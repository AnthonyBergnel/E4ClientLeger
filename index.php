<?php
session_start ();

//Fichier de connexion requis afin de pouvoir se connecter à la BDD
//MySql
require 'utilitaires/connection/connection_mysql.php';

//Vérifie l'existance des entrées au chargement de la page (check du login et du password)
//Si les champs d'entrée sont remplis, la condition est vérifiée
if(isset($_POST['loggin']) AND isset($_POST['mdpL']))
{
	//Destruction des espaces inutiles au début et à la fin des entrées
	$loggin=trim($_POST['loggin']);
	$mdpL=trim($_POST['mdpL']);

	//Vérifie si les chaines obtenues sont vides
	//Si les chaines ne sont pas vides, la condition est vérifiée
	if($loggin!="" AND $mdpL!="")
	{
		//Prépartation de la requête permettant de vérifier si un enregistrement avec le loggin et password précédents existe
		$verifCompte=  "SELECT 
							emplLoggin,
							emplMdp
						FROM
							employe
						WHERE
							emplLoggin='".$loggin."'
							AND
							emplMdp='".$mdpL."'";
		//Vérifie l'existance de l'enregistrement
		//Si l'enregistrement existe, la condition est validée
		if(compteSQL($verifCompte)!=0)
		{
			//Prépartation de la requête permettant de récupérer les informations nécessaires par rapport aux loggin
			$compteUtil=   "SELECT 
								emplId,
								emplNom,
								emplPrenom,
								emplCat
							FROM
								employe
							WHERE
								emplLoggin='".$loggin."'
								AND
								emplMdp='".$mdpL."'";
			//Stockage des données
			$compteUtil=tableSQL($compteUtil);

			$_SESSION['emplId']=$compteUtil[0]['emplId'];
			$_SESSION['emplNom']=$compteUtil[0]['emplNom'];
			$_SESSION['emplPrenom']=$compteUtil[0]['emplPrenom'];
			$_SESSION['emplCat']=$compteUtil[0]['emplCat'];

			//redirection vers l'accueil
			header("location:accueil.php");
		}
		//Message en cas d'échec de connexion
		echo "<meta http-equiv='refresh' content='0;url=index.php?message=<font color=red>Identifiant ou mot de passe incorrects</font>'>";
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Mesguen</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel='stylesheet' type='text/css' href='bootstrap/css/bootstrap.css'/>
		<script type='text/javascript' src='bootstrap/js/bootstrap.min.js'></script>
	</head>
	<body>
		<div class='container'>
			<div class='row'>
				<div class='col-lg-6 col-lg-offset-3'>
					<img class='col-lg-10 col-lg-offset-1' src='images/Mesguen.jpg'/>		
				</div>
			</div>
			<div class='row'>
				<div class='row'>
					<label class='label-control'></label>
				</div>
				<div class='row'>
					<div class='col-lg-8 col-lg-offset-2'>
						<div class="thumbnail">
							<div class="caption">
								<h4>Informations de connexion: </h4>
								<form class='connexion' action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
									<br/>
									<div class='row'>
										<div class='col-lg-offset-3'>
											<div class='col-lg-3'>
												<label class='identifiant'>Identifiant: </label>
											</div>
											<div class='col-lg-3'>
												<input name="loggin" id="loggin" type="text" class='identifiant' required/>
											</div>
										</div>
									</div>
									<br/>
									<div class='row'>
										<div class='col-lg-offset-3'>
											<div class='col-lg-3'>
												<label class='mdp'>Mot de passe: </label>
											</div>
											<div class='col-lg-3'>
												<input name="mdpL" id="mdpL" type="password" class='mdp' required/>
											</div>
										</div>
									</div>
									<br/>
									<button type='submit' class="btn btn-success btn-block"><span class='glyphicon glyphicon-user'></span> Connexion</button>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- Le formulaire envois les données sur cette page en methode POST -->
					<?php
					//Récupération du message d'erreur
					if(isset($_GET['message']))
					{
					?>
					<div class="alert alert-warning alert-dismissible fade show" role="alert">
					  	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					   		<span aria-hidden="true">&times;</span>
					  	</button>
					  	<strong>Erreur connexion !</strong> L'identifiant et le mot de passe ne correspondent pas.
					</div>
					<?php 
					}
					?>
			</div>
		</div>
	</body>
</html>