<?php
#***************************************************************************************#


				#***********************************#
				#********** CONFIGURATION **********#
				#***********************************#
				
				require_once('include/config.inc.php');
				require_once('../include/db.inc.php');
				require_once('../include/form.inc.php');
				include_once('../include/dateTime.inc.php');


#***************************************************************************************#

			
				#******************************************#
				#********** VALIDATE PAGE ACCESS **********#
				#******************************************#
				
				#********** INITIALIZE SESSION **********#
				session_name('blogProject');
				if( !session_start() ) {
					// Fehlerfall
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
										
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
/*					
if(DEBUG_V)		echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)		print_r($_SESSION);					
if(DEBUG_V)		echo "</pre>";
*/					
				
					#********** INVALID LOGIN **********#
					/*
						SICHERHEIT: Um Session Hijacking und ähnliche Identitätsdiebstähle zu verhindern,
						wird die IP-Adresse des sich einloggenden Users geloggt und mit der beim Loginvorgang
						in die Session gespeicherten IP-Adresse abgeglichen.
						Eine IP-Adresse zu fälschen ist nahezu unmöglich. Wenn sich also der Dieb von einer
						anderen IP-Adresse aus einloggen will, wird ihm hier der Zutritt verweigert.
					*/
					if( !isset($_SESSION['userID']) OR $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] ) {
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Login ist nicht valide! <i>(" . basename(__FILE__) . ")</i></p>\n";				

						session_destroy();
						header('Location: index.php');
						exit();
					

					#********** VALID LOGIN **********#
					} else {
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Login ist valide. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						// SICHERHEIT: Neue Session-ID vergeben (Umbenennen der Sessiondatei und des Anpassen des Cookiewertes)
						/*
							SICHERHEIT: Um Cookiediebstahl oder Session Hijacking vorzubeugen, wird nach erfolgreicher
							Authentifizierung eine neue Session-ID vergeben. Ein Hacker, der zuvor ein Cookie mit einer 
							gültigen Session-ID erbeutet hat, kann dieses nun nicht mehr benutzen.
							Die Session-ID muss bei jedem erfolgreichem Login und bei jedem Logout erneuert werden, um
							einen effektiven Schutz zu bieten.
							
							Um die alte Session mit der alten (abgelaufenen) gleich zu löschen, muss
							session_regenerate_id() den optionalen Parameter delete_old_session=true erhalten.
						*/
						session_regenerate_id(true);
						
						// fetch user data from session
						$userID 			= $_SESSION['userID'];
						$userFirstName = $_SESSION['userFirstName'];
						$userLastName	= $_SESSION['userLastName'];
					}
					
				} // INITIALIZE SESSION END
			
			
#***************************************************************************************#	

			
				#***********************************#
				#********** DB CONNECTION **********#
				#***********************************#
				
				// Schritt 1 DB: DB-Verbindung herstellen
				$PDO = dbConnect();


#***************************************************************************************#

			
				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#
				
				$catId 					= NULL;
				$blogHeadline 			= NULL;
				$blogContent 			= NULL;
				$blogImageAlignment 	= NULL;
				$catLabel 				= NULL;
				$blogImagePath 		= NULL;
				
				$errorCatLabel			= NULL;
				$errorHeadline 		= NULL;
				$errorImageUpload 	= NULL;
				$errorContent 			= NULL;
				
				$dbError					= NULL;
				$dbSuccess				= NULL;


#***************************************************************************************#

	
				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#
				
				// Schritt 1 URL: Prüfen, ob Parameter übergeben wurde
				if( isset($_GET['action']) ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben... <i>(" . basename(__FILE__) . ")</i></p>";	
			
					// Schritt 2 URL: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$action = cleanString($_GET['action']);
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$action = $action <i>(" . basename(__FILE__) . ")</i></p>";
		
					// Schritt 3 URL: ggf. Verzweigung
					
					
					#********** LOGOUT **********#
					if( $_GET['action'] === 'logout' ) {
if(DEBUG)			echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: 'Logout' wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>";	
						
						// SICHERHEIT: Neue Session-ID vergeben (Umbenennen der Sessiondatei und des Anpassen des Cookiewertes)
						/*
							SICHERHEIT: Um Cookiediebstahl oder Session Hijacking vorzubeugen, wird nach erfolgreicher
							Authentifizierung eine neue Session-ID vergeben. Ein Hacker, der zuvor ein Cookie mit einer 
							gültigen Session-ID erbeutet hat, kann dieses nun nicht mehr benutzen.
							Die Session-ID muss bei jedem erfolgreichem Login und bei jedem Logout erneuert werden, um
							einen effektiven Schutz zu bieten.
							
							Um die alte Session mit der alten (abgelaufenen) gleich zu löschen, muss
							session_regenerate_id() den optionalen Parameter delete_old_session=true erhalten.
						*/
						session_regenerate_id(true);			
						session_destroy();
						
						header("Location: index.php");
						exit();
					}
					
				} // PROCESS URL PARAMETERS END

		
#***************************************************************************************#			

	
				#*************************************************#
				#********** PROCESS FORM 'NEW CATEGORY' **********#
				#*************************************************#
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formNewCategory']) ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'New Category' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	
		
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$catLabel = cleanString($_POST['catLabel']);
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$catLabel: $catLabel <i>(" . basename(__FILE__) . ")</i></p>";
				
					// Schritt 3 FORM: Werte ggf. validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$errorCatLabel = checkInputString($catLabel);
					
					
					#********** FINAL FORM VALIDATION **********#
					if( $errorCatLabel ) {
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";						
						
					} else {
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";						
						
						// Schritt 4 FORM: Daten weiterverarbeiten

						
						#********** CHECK IF CATEGORY NAME ALREADY EXISTS **********#
						$sql 		= 'SELECT COUNT(catLabel) FROM categories WHERE catLabel = :ph_catLabel';
						
						$params 	= array( 'ph_catLabel' => $catLabel );
						
						// Schritt 2 DB: SQL-Statement vorbereiten
						$PDOStatement = $PDO->prepare($sql);
						
						// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
						try {	
							$PDOStatement->execute($params);								
						} catch(PDOException $error) {
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
						}
						
						$categoryExists = $PDOStatement->fetchColumn();
if(DEBUG_V)			echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$categoryExists: $categoryExists <i>(" . basename(__FILE__) . ")</i></p>";
						
						if( $categoryExists ) {
							// Fehlerfall
							echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Kategorie <b>'$catLabel'</b> existiert bereits! <i>(" . basename(__FILE__) . ")</i></p>";
							$errorCatLabel = 'Es existiert bereits eine Kategorie mit diesem Namen!'; 
						
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug'>Line <b>" . __LINE__ . "</b>: Neue Kategorie <b>$catLabel</b> wird gespeichert... <i>(" . basename(__FILE__) . ")</i></p>";	


							#********** SAVE CATEGORY INTO DB **********#
							$sql 		= 'INSERT INTO categories (catLabel) VALUES (:ph_catLabel)';
							$params 	= array( 'ph_catLabel' => $catLabel );
							
							// Schritt 2 DB: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
							try {	
								$PDOStatement->execute($params);								
							} catch(PDOException $error) {
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
								$dbError= 'Fehler beim Zugriff auf die Datenbank!';
							}
							
							// Schritt 4 DB: Schreiberfolg prüfen
							$rowCount = $PDOStatement->rowCount();
if(DEBUG_V)				echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>";
							
							if( !$rowCount ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FEHLER beim Speichern der neuen Kategorie! <i>(" . basename(__FILE__) . ")</i></p>";
								$dbError = 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
																	
							} else {
								// Erfolgsfall								
								$newCatID = $PDO->lastInsertId();
								
if(DEBUG)					echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Kategorie <b>'$catLabel'</b> wurde erfolgreich unter der ID$newCatID in der DB gespeichert. <i>(" . basename(__FILE__) . ")</i></p>";								
								$dbSuccess = "Die neue Kategorie mit dem Namen <b>'$catLabel'</b> wurde erfolgreich gespeichert.";
									
								// Felder aus Formular wieder leeren
								$catLabel = NULL;
								
							} // SAVE CATEGORY INTO DB END
							 
						} // CHECK IF CATEGORY NAME ALREADY EXISTS END
						
					} // FINAL FORM VALIDATION END

				} // PROCESS FORM 'NEW CATEGORY' END

			
#***************************************************************************************#


				#***************************************************#
				#********** PROCESS FORM 'NEW BLOG ENTRY' **********#
				#***************************************************#
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formNewBlogEntry']) ) {			
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'New Blog Entry' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	

					// Schritt 2 FORM: Daten auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$catID 					= cleanString($_POST['catID']);
					$blogHeadline 			= cleanString($_POST['blogHeadline']);
					$blogContent 			= cleanString($_POST['blogContent']);
					$blogImageAlignment 	= cleanString($_POST['blogImageAlignment']);
if(DEBUG_V) 	echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$catID: $catID <i>(" . basename(__FILE__) . ")</i></p>";
if(DEBUG_V) 	echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$blogHeadline: $blogHeadline <i>(" . basename(__FILE__) . ")</i></p>";
if(DEBUG_V) 	echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$blogImageAlignment: $blogImageAlignment <i>(" . basename(__FILE__) . ")</i></p>";
if(DEBUG_V) 	echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$blogContent: $blogContent <i>(" . basename(__FILE__) . ")</i></p>";

					// Schritt 3 FORM: ggf. Werte validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$errorHeadline = checkInputString($blogHeadline);
					$errorContent 	= checkInputString($blogContent, 5, 64000);


					#********** FINAL FORM VALIDATION PART I (FIELDS VALIDATION) **********#					
					if( $errorHeadline OR $errorContent) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FINAL FORM VALIDATION PART I: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";
						
					} else {
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: FINAL FORM VALIDATION PART I: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";


						#********** OPTIONAL: FILE UPLOAD **********#
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Prüfe auf Bildupload... <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						// Prüfen, ob eine Datei hochgeladen wurde
						if( !$_FILES['blogImage']['tmp_name'] ) {
if(DEBUG)				echo "<p class='debug hint'>Line <b>" . __LINE__ . "</b>: Bildupload ist nicht aktiv. <i>(" . basename(__FILE__) . ")</i></p>";
						
						} else {
if(DEBUG)				echo "<p class='debug hint'>Line <b>" . __LINE__ . "</b>: Bild Upload ist aktiv... <i>(" . basename(__FILE__) . ")</i></p>";

							// imageUpload() liefert ein Array zurück, das eine Fehlermeldung (String oder NULL) enthält
							// sowie den Pfad zum gespeicherten Bild (String oder NULL)
							$imageUploadResultArray = imageUpload($_FILES['blogImage']['tmp_name']);
					
							
							#********** VALIDATE IMAGE UPLOAD RESULTS **********#
							if( $imageUploadResultArray['imageError'] ) {
								// Fehlerfall
								$errorImageUpload = $imageUploadResultArray['imageError'];
								
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Bild wurde erfolgreich unter <i>'" . $imageUploadResultArray['imagePath'] . "'</i> gespeichert. <i>(" . basename(__FILE__) . ")</i></p>";
								// Pfad zum Bild speichern
								$blogImagePath = $imageUploadResultArray['imagePath'];
							}
							#****************************************************#
							

						} // OPTIONAL: FILE UPLOAD END
						#*************************************************************************#
						
						
						#********** FINAL FORM VALIDATION PART II (IMAGE UPLOAD) **********#					
						if( $errorImageUpload ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FINAL FORM VALIDATION PART II: Bilduploadfehler! <i>(" . basename(__FILE__) . ")</i></p>";
							
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: FINAL FORM VALIDATION PART II: Kein Bilduploadfehler. <i>(" . basename(__FILE__) . ")</i></p>";


							#********** SAVE BLOG ENTRY DATA INTO DB **********#
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Speichere Blogeintrag in DB... <i>(" . basename(__FILE__) . ")</i></p>\n";

							$sql 		= 	'INSERT INTO blogs (blogHeadline, blogImagePath, blogImageAlignment, blogContent, catID, userID)
											 VALUES (:ph_blogHeadline, :ph_blogImagePath, :ph_blogImageAlignment, :ph_blogContent, :ph_catID, :ph_userID) ';
							
							$params 	= array( 'ph_blogHeadline'				=>	$blogHeadline,
													'ph_blogImagePath'			=>	$blogImagePath,
													'ph_blogImageAlignment'		=>	$blogImageAlignment,
													'ph_blogContent'				=>	$blogContent,
													'ph_catID'						=>	$catID,
													'ph_userID'						=>	$userID);
							
							// Schritt 2 DB: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
							try {	
								$PDOStatement->execute($params);								
							} catch(PDOException $error) {
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
								$dbError = 'Fehler beim Zugriff auf die Datenbank!';
							}
							
							// Schritt 4 DB: Schreiberfolg prüfen
							$rowCount = $PDOStatement->rowCount();							
if(DEBUG_V)				echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>";						
							
							if( !$rowCount ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FEHLER beim Speichern des Blogbeitrags! <i>(" . basename(__FILE__) . ")</i></p>";
								$dbError = 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
							
							} else {
								// Erfolgsfall
								$newBlogID = $PDO->lastInsertId();
								
if(DEBUG)					echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Blogbeitrag erfolgreich mit der ID$newBlogID gespeichert. <i>(" . basename(__FILE__) . ")</i></p>";
								$dbSuccess = 'Der Blogbeitrag wurde erfolgreich gespeichert.';
								
								// Felder aus Formular wieder leeren
								$catID 					= NULL;
								$blogHeadline 			= NULL;
								$blogImageAlignment 	= NULL;
								$blogContent 			= NULL;
								
							} // SAVE BLOG ENTRY INTO DB END
							
						} // FINAL FORM VALIDATION PART II (IMAGE UPLOAD) END
							
					} // FINAL FORM VALIDATION PART I (FIELDS VALIDATION) END
					
				} // PROCESS FORM 'NEW BLOG ENTRY' END
			

#***************************************************************************************#
			
			
				#**********************************************#
				#********** FETCH CATEGORIES FROM DB **********#
				#**********************************************#

if(DEBUG)	echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Lade Kategorien aus DB... <i>(" . basename(__FILE__) . ")</i></p>";
			
				$sql 		= 'SELECT * FROM categories';
				
				$params 	= NULL;
				
				// Schritt 2 DB: SQL-Statement vorbereiten
				$PDOStatement = $PDO->prepare($sql);
				
				// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
				try {	
					$PDOStatement->execute($params);								
				} catch(PDOException $error) {
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
					$dbError = 'Fehler beim Zugriff auf die Datenbank!';
				}
				
				// Kategorien aus DB zur späteren Verwendung in Array speichern
				$allCategoriesArray = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);

/*
if(DEBUG_V)	echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($allCategoriesArray);					
if(DEBUG_V)	echo "</pre>";
*/

#***************************************************************************************#			
?>

<!doctype html>

<html>

	<head>
		<meta charset="utf-8">
		<title>PHP-Projekt Blog</title>
		<link rel="stylesheet" href="css/main.css">
		<link rel="stylesheet" href="../css/debug.css">
	</head>

	<body class="dashboard">

		<!-- ---------- PAGE HEADER START ---------- -->
	
		<header class="fright">
			<a href="?action=logout">Logout</a><br>
			<a href="index.php"><< zum Frontend</a>
		</header>
		<div class="clearer"></div>

		<br>
		<hr>
		<br>
		
		<!-- ---------- PAGE HEADER END ---------- -->
		
		<h1 class="dashboard">PHP-Projekt Blog - Dashboard</h1>
		<p class="name">Aktiver Benutzer: <?= "$userFirstName $userLastName" ?></p>
		
		
		<!-- ---------- POPUP MESSAGE START ---------- -->
		<?php if( $dbError OR $dbSuccess ): ?>
		<popupBox>
			<?php if($dbError): ?>
			<h3 class="error"><?= $dbError ?></h3>
			<?php elseif($dbSuccess): ?>
			<h3 class="success"><?= $dbSuccess ?></h3>
			<?php endif ?>
			<a class="button" onclick="document.getElementsByTagName('popupBox')[0].style.display = 'none'">Schließen</a>
		</popupBox>		
		<?php endif ?>
		<!-- ---------- POPUP MESSAGE END ---------- -->
		
		
		<!-- ---------- LEFT PAGE COLUMN START ---------- -->
		<main class="forms fleft">			
						
			<h2 class="dashboard">Neuen Blog-Eintrag verfassen</h2>
			<p class="small">
				Um einen Blogeintrag zu verfassen, muss dieser einer Kategorie zugeordnet werden.<br>
				Sollte noch keine Kategorie vorhanden sein, erstellen Sie diese bitte zunächst.
			</p> 
			
			
			<!-- ---------- FORM 'NEW BLOG ENTRY' START ---------- -->
			<form action="" method="POST" enctype="multipart/form-data">
				<input class="dashboard" type="hidden" name="formNewBlogEntry">
				
				<br>
				<label>Kategorie:</label>
				<select class="dashboard bold" name="catID">			
				<?php foreach($allCategoriesArray AS $categorySingleItemArray): ?>
					<option value='<?= $categorySingleItemArray['catID'] ?>' <?php if($catId == $categorySingleItemArray['catID']) echo 'selected'?>><?= $categorySingleItemArray['catLabel'] ?></option>
				<?php endforeach ?>
				</select>
				
				<br>
				
				<label>Überschrift:</label>
				<span class="error"><?= $errorHeadline ?></span><br>
				<input class="dashboard" type="text" name="blogHeadline" placeholder="..." value="<?= $blogHeadline ?>"><br>
				
				
				<!-- ---------- IMAGE UPLOAD START ---------- -->
				<label>[Optional] Bild veröffentlichen:</label>
				<span class="error"><?= $errorImageUpload ?></span>
				<imageUpload>					
					
					<!-- -------- INFOTEXT FOR IMAGE UPLOAD START -------- -->
					<p class="small">
						Erlaubt sind Bilder des Typs 
						<?php $allowedMimetypes = implode( ', ', array_keys(IMAGE_ALLOWED_MIME_TYPES) ) ?>
						<?= strtoupper( str_replace( array(', image/jpeg', 'image/'), '', $allowedMimetypes) ) ?>.
						<br>
						Die Bildbreite darf <?= IMAGE_MAX_WIDTH ?> Pixel nicht übersteigen.<br>
						Die Bildhöhe darf <?= IMAGE_MAX_HEIGHT ?> Pixel nicht übersteigen.<br>
						Die Dateigröße darf <?= IMAGE_MAX_SIZE/1024 ?>kB nicht übersteigen.
					</p>
					<!-- -------- INFOTEXT FOR IMAGE UPLOAD END -------- -->
					
					<input type="file" name="blogImage">
					<select class="alignment fright" name="blogImageAlignment">
						<option value="fleft" 	<?php if($blogImageAlignment == 'fleft') echo 'selected'?>>align left</option>
						<option value="fright" 	<?php if($blogImageAlignment == 'fright') echo 'selected'?>>align right</option>
					</select>
				</imageUpload>
				<br>	
				<!-- ---------- IMAGE UPLOAD END ---------- -->
				
				
				<label>Inhalt des Blogeintrags:</label>
				<span class="error"><?= $errorContent ?></span><br>
				<textarea class="dashboard" name="blogContent" placeholder="..."><?= $blogContent ?></textarea><br>
				
				<div class="clearer"></div>
				
				<input class="dashboard" type="submit" value="Veröffentlichen">
			</form>
			<!-- ---------- FORM 'NEW BLOG ENTRY' END ---------- -->
			
		</main>
		<!-- ---------- LEFT PAGE COLUMN END ---------- -->
		
		
		
		<!-- ---------- RIGHT PAGE COLUMN START ---------- -->
		<aside class="forms fright">
		
			<h2 class="dashboard">Neue Kategorie anlegen</h2>
			
			
			<!-- ---------- FORM 'NEW CATEGORY' START ---------- -->			
			<form class="dashboard" action="" method="POST">
			
				<input class="dashboard" type="hidden" name="formNewCategory">
				
				<label>Name der neuen Kategorie:</label>
				<span class="error"><?= $errorCatLabel ?></span><br>
				<input class="dashboard" type="text" name="catLabel" placeholder="..." value="<?= $catLabel ?>"><br>

				<input class="dashboard" type="submit" value="Neue Kategorie anlegen">
			</form>
			<!-- ---------- FORM 'NEW CATEGORY' END ---------- -->
			
		
		</aside>

		<div class="clearer"></div>
		<!-- ---------- RIGHT PAGE COLUMN END ---------- -->
		
		
	</body>
</html>






