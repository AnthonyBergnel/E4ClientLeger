<?php
session_start();

if($_SESSION['emplCat']=="Exploitant")
{
	require 'utilitaires/connection/connection_mysql.php';
	//mysql_set_charset("UTF8");
	@mysql_set_charset("UTF8");
	if(isset($_POST['etpIdSup']) AND isset($_POST['trnNumSup']))
	{
		$etpId=$_POST['etpIdSup'];
		$trnNum=$_POST['trnNumSup'];

		$sqlSupprimerEtape="DELETE FROM 
								etape
							WHERE
								etpId=".$etpId."
								AND
								trnNum=".$trnNum;
		executeSQL($sqlSupprimerEtape);

		$_SESSION['trnNumInfo']=$trnNum;

		echo "<meta http-equiv='refresh' content='0;url=AC12.php?message=<font color=green>Etape suppriée</font>'>";
	}

	if(isset($_POST['maj']))
	{
		$trnDepChf=$_POST['trnDepChf'];
		$chauffeur=$_POST['chauffeur'];
		$vehicule=$_POST['vehicule'];
		$commentaire=$_POST['commentaire'];
		$trnNum=$_POST['trnNumInfo'];

		$sqlUpdate="UPDATE
						tournee
					SET
						trnDepChf='".$trnDepChf."',
						chfId=".$chauffeur.",
						vehMat='".$vehicule."',
						trnCommentaire='".$commentaire."'
					WHERE
						trnNum=".$trnNum;
		executeSQL($sqlUpdate);
	}

	if(isset($_POST['creer']))
	{
		if($_POST['chauffeur']!="NAN" AND $_POST['vehicule']!="NAN" AND $_POST['date']!="")
		{
			$trnDepChf=$_POST['date'];
			$chauffeur=$_POST['chauffeur'];
			$vehicule=$_POST['vehicule'];
			$sqlControl=   "SELECT
								trnNum
							FROM
								tournee
							WHERE
								trndepchf='".$trnDepChf."'
								AND
								chfId='".$chauffeur."'
								AND
								vehMat='".$vehicule."'";
			if(compteSQL($sqlControl)==0)
			{
				if(isset($_POST['commentaire']))
				{
					$commentaire=$_POST['commentaire'];
					$sqlInsert="INSERT INTO tournee
									(trndepchf,
									chfId,
									vehMat,
									trnCommentaire)
								VALUES
									('".$trnDepChf."',
									'".$chauffeur."',
									'".$vehicule."',
									'".$commentaire."')";
				}
				else
				{
					$sqlInsert="INSERT INTO tournee
									(trndepchf,
									chfId,
									vehMat)
								VALUES
									('".$trnDepChf."',
									'".$chauffeur."',
									'".$vehicule."')";
				}
				$result=executeSQL($sqlInsert);
				$result=tableSQL($sqlControl);

				$_SESSION['trnNumInfo']=$result[0]['trnNum'];
				header("location:AC12.php");
			}
			else
			{
				echo "<meta http-equiv='refresh' content='0;url=AC12.php?message=<font color=red>Tournée déjà  éxistante</font>'>";
			}
		}
		else
		{
			echo "<meta http-equiv='refresh' content='0;url=AC12.php?message=<font color=red>Données manquantes</font>'>";
		}
	}
	?>
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8" />
	        <title>Ajouter une tourn&eacute;e</title>
	        <meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
			<link rel='stylesheet' type='text/css' href='bootstrap/css/bootstrap.css'/>
			<script type='text/javascript' src='bootstrap/js/bootstrap.min.js'></script>
			<script type='text/javascript' src='datatables/js/jquery.js'></script>
	        <script type="text/javascript">
		        <?php
		        require 'javascript/controle.js';
		        ?>
	        </script>
		</head>
		<body>
			<?php
			if(isset($_POST['trnNumInfo']) OR isset($_SESSION['trnNumInfo']))
			{
				if(isset($_POST['trnNumInfo']))
				{
					$trnNum=$_POST['trnNumInfo'];
					$_SESSION['trnNumInfo']=$trnNum;
				}
				else
				{
					$trnNum=$_SESSION['trnNumInfo'];
					if(isset($_SESSION['etpIdInfo']))
					{
						unset($_SESSION['etpIdInfo']);
					}
				}
				$sqlTournee=   "SELECT
									chfId,
									emplNom,
									emplPrenom,
									vehicule.vehMat,
									trnDepChf,
									trnCommentaire
								FROM
									employe,
									vehicule,
									tournee
								WHERE
									emplId=chfId
									AND
									vehicule.vehMat=tournee.vehMat
									AND
									trnNum=".$trnNum;
				$sqlTournee=tableSQL($sqlTournee);
				?>
				<div class='container-fluid'>
					<div class='row'>
						<div class='col-lg-12 bg-success' style='text-align:center;'> 
							<h3 id="header_Organiser_tournee">AC12 - Organiser les tournees - Liste des etapes de la tournee <?php echo $trnNum; ?></h3>
						</div>
					</div>
					<div class='row'>
						<p/>
					</div>
					<div class='col-lg-5 col-lg-offset-1'>
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onSubmit="return isValidFormTourneeUpdate();" id="form_tournee">
						<div id="label_float">
							<div>
								<label for="trnDepChf">Date: </label>
								<input class='form-control' required name="trnDepChf" id="trnDepChf" type="text" value="<?php echo date("d/m/Y", strtotime($sqlTournee[0]['trnDepChf'])); ?>" onKeyPress="return isDateKey(event);"/>
							</div>
							<div>
								<label class='label-control'>Chauffeur: </label>
								<select class='form-control' name="chauffeur">
									<?php
									$sqlChauffeurs="SELECT
														emplId,
														emplNom,
														emplPrenom
													FROM
														employe
													ORDER BY
														emplNom,
														emplPrenom
													ASC";
									$sqlChauffeurs=tableSQL($sqlChauffeurs);
	
									foreach($sqlChauffeurs As $donnees)
									{
										if($sqlTournee[0]['chfId']==$donnees['emplId'])
										{
											?>
											<option value="<?php echo $donnees['emplId']; ?>" selected="true"><?php echo $donnees['emplNom'].' '.$donnees['emplPrenom']; ?></option>
											<?php
										}
										else
										{
											?>
											<option value="<?php echo $donnees['emplId']; ?>"><?php echo $donnees['emplNom'].' '.$donnees['emplPrenom']; ?></option>
											<?php
										}
									}
									?>
								</select>
							</div>
							<div>
								<label class='label-control'>Vehicule: </label>
								<select required class='form-control' name="vehicule">
									<?php
									$sqlVehicules= "SELECT
														vehMat
													FROM
														vehicule
													ORDER BY
														vehMat
													ASC";
									$sqlVehicules=tableSQL($sqlVehicules);
	
									foreach($sqlVehicules As $donnees)
									{
										if($sqlTournee[0]['vehMat']==$donnees['vehMat'])
										{
											?>
											<option value="<?php echo $donnees['vehMat']; ?>" selected="true"><?php echo $donnees['vehMat']; ?></option>
											<?php
										}
										else
										{
											?>
											<option value="<?php echo $donnees['vehMat']; ?>"><?php echo $donnees['vehMat']; ?></option>
											<?php
										}
									}
									?>
								</select>
							</div>
							<div>
								<label class='label-control'>Pris en charge le : </label>
								<input name="trnDepChfHor"  class='form-control' type="text" value="<?php echo date("d/m/y H:i", strtotime($sqlTournee[0]['trnDepChf'])); ?>" disabled="disabled"/>
							</div>
							<div>
								<label class='label-control'>Commentaire :</label>
								<textarea name="commentaire" class='form-control' type="text"><?php echo $sqlTournee[0]['trnCommentaire']; ?></textarea>
							</div>
						</div>
						<div class='row'>
							<p/>
						</div>
						<div class='row'>
							<div class='col-lg-offset-4'>
								<input required name="trnNumInfo" id="trnNumInfo" type="hidden" value="<?php echo $trnNum; ?>"/>
								<button type='submit' class='btn btn-default'><span class='glyphicon glyphicon-ok'></span> Valider</button>
								<button type='button' id='retour' class='btn btn-default'  onClick="location='AC11.php'"><span class='glyphicon glyphicon-share-alt'></span> Retour</button>
							</div>
							<script type="text/javascript">
								function griser()
								{
									<?php
									$nbEtapes= "SELECT
													etpId
												FROM
													etape
												WHERE
													trnNum=".$trnNum;
									$nbEtapes=compteSQL($nbEtapes);
									?>
									if(<?php echo $nbEtapes; ?>==0)
									{
										document.getElementById('retour').setAttribute('disabled', 'disabled');
										document.getElementById('retour').style.opacity=0.5;
									}
								}
								griser();
							</script>
						</div>
					</form>
					</div>
					<div id="etapes_tournee">
						<?php
						$sqlEtapes="SELECT
										etpId,
										comNom,
										lieuNom,
										trnNum
									FROM
										commune,
										lieu,
										etape
									WHERE
										commune.comId=lieu.comId
										AND
										lieu.lieuId=etape.lieuId
										AND
										trnNum=".$trnNum."
									ORDER BY
										etpRDV
									ASC";
						?>
						<table class='col-lg-5 col-lg-offset-1' id="etapes_tournee">
							<tr>
								<th>
									Numero de l'etape
								</th>
								<th>
									Etapes
								</th>
							</tr>
							<?php
							$sqlEtapes=tableSQL($sqlEtapes);
							$compteur=1;
							foreach($sqlEtapes As $donnees)
							{
								?>
								<tr>
									<td>
										<?php
										echo $compteur++;
										?>
									</td>
									<td>
										<?php
										echo $donnees['lieuNom']." ".$donnees['comNom'];
										?>
									</td>
									<td>
										<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
											<input name="etpIdSup" type="hidden" value="<?php echo $donnees['etpId']; ?>"/>
											<input name="trnNumSup" type="hidden" value="<?php echo $donnees['trnNum']; ?>"/>
											<button class='btn btn-xs btn-danger' name="suppr_bout_<?php echo $donnees['etpId']; ?>" id="suppr_bout_<?php echo $donnees['etpId']; ?>" class="suppr_form" type="submit" onclick="if(window.confirm('Voulez-vous vraiment supprimer ?')){return true;}else{return false;}"><span class='glyphicon glyphicon-remove'></span> </button>
										</form>
									</td>
									<td>
										<form action="AC13.php" method="POST">
											<input name="etpIdInfo" type="hidden" value="<?php echo $donnees['etpId']; ?>"/>
											<input name="trnNumInfo" type="hidden" value="<?php echo $donnees['trnNum']; ?>"/>
											<button class='btn btn-xs btn-info' name="modif_bout_<?php echo $donnees['etpId']; ?>" id="modif_bout_<?php echo $donnees['etpId']; ?>" class="modif_form" type="submit"><span class='glyphicon glyphicon-pencil'></span></button>
										</form>
									</td>
								</tr>
								<?php
							}
	
							if(isset($_GET['message']))
							{
								echo utf8_decode($_GET['message']);
							}
							?>
						</table>
						<br/>
						<div class='col-lg-offset-9'>
							<form action="AC13.php" method="POST">
								<input type="hidden" name="trnNumInfo" value="<?php echo $trnNum; ?>"/>
								<button class="btn btn-success btn-circle" id="ajouter" type="submit"><span class='glyphicon glyphicon-plus'></span></button>
							</form>
						</div>
					</div>
					</div>
					<?php
					}
					else
					{
						?>
						<div class='container-fluid'>
							<div class='row'>
								<div class='col-lg-12 bg-success' style='text-align:center;'> 
									<h3>AC12 - Organiser les tournees - Ajouter une tournee</h3>
								</div>
							</div>
							<div class='row'>
								</p>	
							</div>
							<div class='row'>
								<div class='col-lg-5 col-lg-offset-1'>
									<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onSubmit="return isValidFormTourneeCreate();">
											<label class='label-control'>Chauffeur:</label>
											<select class='form-control' name='chauffeur'>
											<option selected value="NAN">Aucun chauffeur</option>
											<?php
											$sqlChauffeur= "SELECT
																emplId,
																emplNom,
																emplPrenom
															FROM
																employe
															WHERE
																emplcat='chauffeur'";
											$sqlChauffeur=tableSQL($sqlChauffeur);
											foreach($sqlChauffeur As $row)
											{
												echo "<option value='$row[0]'>".$row[1]." ".$row[2]."</option>";
											}
											?>
										</select>
									<div class='row'>
										<p/>
									</div>
									<div class='row'>
										<label class='label-control'>V&eacute;hicule: </label>
											<select class='form-control' name="vehicule">
											<option selected value="NAN">Aucun v&eacute;hicule</option>
											<?php
											$sqlPlaque="SELECT
															vehMat
														FROM
															vehicule";
											$sqlPlaque=tableSQL($sqlPlaque);
											foreach($sqlPlaque As $row)
											{
												echo "<option value='".$row[0]."'>".$row[0]."</option>";
											}
											?>
										</select>
									</div>
									<div class='row'>
										<p/>
									</div>
									<div class='row'>
										<label class='label-control'>Pris en charge le :</label>
										<input  class='form-control' type='date' name='date'/>
									</div>
									<div class='row'
										<p/>
									</div>
									<div class='row'>
										<p/>
									</div>
									<div class='row'>
										<label class='label-control'>Commentaire :</label>
										<div class='row'>
											<div class='col-lg-12'>
												<textarea class='form-control' name="commentaire"></textarea>
											</div>
										</div>
									</div>
									<div class='row'>
										<p/>
									</div>
									<div class='row'>
										<div class='col-lg-4 col-lg-offset-5'>
											<input type='hidden' name='creer' value='yolo'/>
											<button type='submit' class='btn btn-default'><span class='glyphicon glyphicon-ok'></span> Valider</button>
											<button type='button' class='btn btn-default' onclick="location='AC11.php'"><span class='glyphicon glyphicon-share-alt'></span> Retour</button>
										</div>
									</div>
								<?php
								if(isset($_GET['message']))
								{
									echo utf8_decode($_GET['message']);
								}
								?>
								</form>
							</div>
							</div>
						</div>
					</div>
					<?php
				}
				?>
						</div>
		</body>
	</html>
	<?php
}
else
{
	header("location:accueil.php");
}
?>