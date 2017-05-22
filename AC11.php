<?php
session_start();

//Autorisation de regard par rapport au statut
if($_SESSION['emplCat']=="Exploitant")
{
	//Fichier de connexion requis afin de pouvoir se connecter à la BDD
	//MySql
	require 'utilitaires/connection/connection_mysql.php';
	//Encodage des sorties de la BDD en utf8
	//mysql_set_charset("utf8");
	$connexion->exec("SET CHARACTER SET utf8");

	if(isset($_SESSION['trnNumInfo']))
	{
		unset($_SESSION['trnNumInfo']);
	}

	if(isset($_POST['trnNumSup']))
	{
		$trnNum=$_POST['trnNumSup'];

		$sqlSupprimerTournee=  "DELETE FROM 
									tournee
								WHERE
									trnNum=".$trnNum;
		executeSQL($sqlSupprimerTournee);

		echo "<meta http-equiv='refresh' content='0;url=AC11.php?message=<font color=green>Tournée supprimée</font>'>";
	}

	$sqlTournees=  "SELECT
						tournee.trnNum,
						emplNom,
						emplPrenom,
						vehMat,
						trnDepChf
					FROM
						employe,
						tournee
					WHERE
						emplId=chfId
					ORDER BY
						trnNum
					ASC";
	$sqlTournees=tableSQL($sqlTournees);
	?>
	<!DOCTYPE html>
	<html>
		<head>
	        <title>Tourn&eacute;es</title>
	        <meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
			<script type='text/javascript' src='jquery-3.2.1.min.js'></script>
			<link rel='stylesheet' type='text/css' href='bootstrap/css/bootstrap.css'/>
			<script type='text/javascript' src='bootstrap/js/bootstrap.min.js'></script>
			<script type='text/javascript' src='datatables/media/js/dataTables.bootstrap.min.js'></script>
			<link rel='stylesheet' type='text/css' href='dataTables.bootstrap.min.css'/>
			<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/r/bs-3.3.5/jqc-1.11.3,dt-1.10.8/datatables.min.js"></script>
		</head>
		<body>
			<div clas='container-fluid'>
			<div class='row'>
				<div style='text-align:center;' class='col-lg-12 bg-success'>
					<h3>AC11 - Organiser les tourn&eacute;es - Liste des tourn&eacute;es</h3>
				</div>
			</div>
			<div class='row'>
				</p>
			</div>
			<div class='row'>
			<div class='col-lg-10 col-lg-offset-1'>
				<table id="tbltrn" class='table table-bordered table-striped'>
						<?php
						$couleur="";
						?>
						<thead>
						<tr class="tournee<?php echo $couleur; ?>">
							<th>
								Tourn&eacute;e
							</th>
							<th>
								Date
							</th>
							<th>
								Chauffeur
							</th>
							<th>
								V&eacute;hicule
							</th>
							<th>
								D&eacute;part
							</th>
							<th>
								Arriv&eacute;e
							</th>
							<th>
								Supprimer
							</th>
							<th>
								Modifier
							</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach($sqlTournees As $donnees)
						{
							if($couleur="")
							{
								$couleur="success";
							}
							else
							{
								$couleur="";
							}
							?>
							
							<tr class="tournee<?php echo $couleur; ?>">
								<td>
									<?php
									echo $donnees['trnNum'];
									?>
								</td>
								<td>
									<?php
									echo date("d/m/Y", strtotime($donnees['trnDepChf']));
									?>
								</td>
								<td>
									<?php
									echo $donnees['emplNom']." ".$donnees['emplPrenom'];
									?>
								</td>
								<td>
									<?php
									echo $donnees['vehMat'];
									?>
								</td>
								<td>
									<?php
									$sqlMinEtape=  "SELECT
														lieuNom,
														comNom
													FROM
														commune,
														lieu,
														etape
													WHERE
														commune.comId=lieu.comId
														AND
														lieu.lieuId=etape.lieuId
														AND
														trnNum=".$donnees['trnNum']."
		                                            ORDER BY
		                                            	etpRDV
		                                            ASC";
		
									if(compteSQL($sqlMinEtape)!=0)
									{
										$sqlMinEtape=tableSQL($sqlMinEtape);
										echo $sqlMinEtape[0]['lieuNom']." ".$sqlMinEtape[0]['comNom'];
									}
									else
									{
										echo "Aucune Etape disponible";
									}
									?>
								</td>
								<td>
									<?php
									$sqlMaxEtape=  "SELECT
														lieuNom,
														comNom
													FROM
														commune,
														lieu,
														etape
													WHERE
														commune.comId=lieu.comId
														AND
														lieu.lieuId=etape.lieuId
														AND
														trnNum=".$donnees['trnNum']."
		                                            ORDER BY
		                                            	etpRDV
		                                            DESC";
		
									if(compteSQL($sqlMaxEtape)!=0)
									{
										$sqlMaxEtape=tableSQL($sqlMaxEtape);
										echo $sqlMaxEtape[0]['lieuNom']." ".$sqlMaxEtape[0]['comNom'];
									}
									else
									{
										echo "Aucune Etape disponible";
									}
									?>
								</td>
								<td>
									<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
										<input name="trnNumSup" type="hidden" value="<?php echo $donnees['trnNum']; ?>"/>
										<button class='btn btn-xs btn-danger'name="suppr_bout_<?php echo $donnees['trnNum']; ?>" id="suppr_bout_<?php echo $donnees['trnNum']; ?>" class="suppr_form" type="submit" onclick="if(window.confirm('Voulez-vous vraiment supprimer ?')){return true;}else{return false;}"><span class='glyphicon glyphicon-remove'></span> Supprimer</button>
									</form>
								</td>
								<td>
									<form action="AC12.php" method="POST">
										<input name="trnNumInfo" type="hidden" value="<?php echo $donnees['trnNum']; ?>"/>
										<button class='btn btn-xs btn-info' name="modif_bout_<?php echo $donnees['trnNum']; ?>" id="modif_bout_<?php echo $donnees['trnNum']; ?>" class="modif_form" type="submit"><span class='glyphicon glyphicon-pencil'></span> Modifier</button>
									</form>
								</td>
								<script type="text/javascript">
									function griser()
									{
										var now = new Date();
										var annee=now.getFullYear();
										var mois=now.getMonth()+1;
										if(mois<10)
										{
											mois='0'+mois;
										}
										var jour=now.getDate();
										if(jour<10)
										{
											jour='0'+jour;
										}
										var heure=now.getHours();
										if(heure<10)
										{
											heure='0'+heure;
										}
										var minute=now.getMinutes();
										if(minute<10)
										{
											minute='0'+minute;
										}
										var seconde=now.getSeconds();
										if(seconde<10)
										{
											seconde='0'+seconde;
										}
										date=annee+'-'+mois+'-'+jour+' '+heure+':'+minute+':'+seconde;
										if("<?php echo $donnees['trnDepChf']; ?>"<date)
										{
											document.getElementById('suppr_bout_<?php echo $donnees["trnNum"]; ?>').setAttribute('disabled', 'disabled');
											document.getElementById('suppr_bout_<?php echo $donnees["trnNum"]; ?>').style.opacity=0.5;
											document.getElementById('modif_bout_<?php echo $donnees["trnNum"]; ?>').setAttribute('disabled', 'disabled');
											document.getElementById('modif_bout_<?php echo $donnees["trnNum"]; ?>').style.opacity=0.5;
										}
									}
		
									griser();
								</script>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				</div>
			</div>
			<div class='row'>
				<div class='col-lg-4 col-lg-offset-5'>
					<button type='button' class='btn btn-default' onclick="location='AC12.php'"><span class='glyphicon glyphicon-plus'></span> Ajouter</button>
					<button type='button' class='btn btn-default' onclick="location='accueil.php'"><span class='glyphicon glyphicon-share-alt'></span> Retour</button>
				</div>
			</div>
				
				<?php
				if(isset($_GET['message']))
				{
				?>
				<div class='row'>
					<div class='col-lg-10 col-lg-offset-1 alert alert-info'>
				<?php 
					echo $_GET['message'];
				?>
					</div>
				</div>
				<?php 
				}
				?>
			</div>
			<script>
			$(document).ready(function() {
			    $('#tbltrn').DataTable();
			} );
			</script>
		</body>
	</html>
	<?php
}
else
{
	header("location:accueil.php");
}
?>