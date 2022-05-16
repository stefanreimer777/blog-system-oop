<?php
#**********************************************************************************#
			
			
				#***********************************#
				#********** CONFIGURATION **********#
				#***********************************#
				
				
				require_once('include/config.inc.php');
				require_once('include/db.inc.php');
				require_once('include/form.inc.php');
				require_once('include/dateTime.inc.php');
				
				
				#********** INCLUDE CLASSES **********#
				require_once('Class/User.class.php');
				require_once('Class/Category.class.php');
				require_once('Class/Blog.class.php');

			
#***************************************************************************************#

			
				#******************************************#
				#********** VALIDATE PAGE ACCESS **********#
				#******************************************#
				
				#********** INITIALIZE SESSION **********#
				session_name('blogProject_oop');
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
						$User = new User();
						$User->setUserID( $_SESSION['userID']);
						$User->setUserFirstName( $_SESSION['userFirstName']);
						$User->setUserLastName( $_SESSION['userLastName']);
					
						
					}
					
				} // INITIALIZE SESSION END
			
			
#**********************************************************************************#


if(DEBUG_V)	echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_SESSION);					
if(DEBUG_V)	echo "</pre>";


#**********************************************************************************#

			
				#***********************************#
				#********** DB CONNECTION **********#
				#***********************************#
				
				// Schritt 1 DB: DB-Verbindung herstellen
				$PDO = dbConnect('blog_v1_oop');



#***************************************************************************************#

			
				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#
				
				$Category = NULL;
				$Blog = NULL;

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

if(DEBUG_V)	echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formNewCategory']) ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'New Category' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	
							
				// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$Category = new Category($_POST['catLabel']);
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: Kategorie: '{$Category->getCatLabel()}' <i>(" . basename(__FILE__) . ")</i></p>";
			
				// Schritt 3 FORM: Werte ggf. validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$errorCatLabel = checkInputString($Category->getCatLabel());


					#********** FINAL FORM VALIDATION **********#
					if( $errorCatLabel ) {
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";						
							
					} else {
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";						
							
						// Schritt 4 FORM: Daten weiterverarbeiten

						#********** CHECK IF CATEGORY NAME ALREADY EXISTS **********#
	
						if ($Category->checkIfCategoryExistsInDB($PDO) !== '0') {
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Die Kategorie <b> '{$Category->getCatLabel()}'</b></B>existiert bereits in der DB <i>(" . basename(__FILE__) . ")</i></p>";						
							$errorCatLabel = 'Es existiert bereits eine Kategorie mit diesem Namen!'; 

						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug'>Line <b>" . __LINE__ . "</b>: Neue Kategorie <b>{$Category->getCatLabel()}l</b> wird in der Datenbank gespeichert... <i>(" . basename(__FILE__) . ")</i></p>";	


							#********** SAVE CATEGORY INTO DB **********#
							if($Category->saveToDb($PDO) === false) {
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim speichern der Kategorie .<i>(" . basename(__FILE__) . ")</i></p>\n";										
								$dbError = 'Es ist ein Fehler aufgetreten!';
							} else {
if(DEBUG) 				echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Kategorie <b>'{$Category->getCatLabel()}'</b> wurde erfolgreich unter der ID {$Category->getCatID()} in der DB gespeichert. <i>(" . basename(__FILE__) . ")</i></p>"; 
								$dbSuccess = "Die neue Kategorie mit dem Namen <b>'{$Category->getCatLabel()}'</b> wurde erfolgreich in der Datenbank gespeichert.";

								// Felder aus Formular wieder leeren
								$Category = NULL;

							} // SAVE CATEGORY INTO DB END
							 
						} // CHECK IF CATEGORY NAME ALREADY EXISTS END
						
					} // FINAL FORM VALIDATION END

				} // PROCESS FORM 'NEW CATEGORY' END




#***************************************************************************************#


					#********** FETCH ALL CATEGORIES FROM DB **********#
if(DEBUG)	echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Lade Kategorien aus DB. <i>(" . basename(__FILE__) . ")</i></p>\n";
					// Fetch all Categories from DB
					$allCategoriesArray = Category::fetchAllFromDb( $PDO);

/*
if(DEBUG_V)	echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($allCategoriesArray);					
if(DEBUG_V)	echo "</pre>";
*/

#***************************************************************************************#

	
					#***************************************************#
					#********** PROCESS FORM 'NEW BLOG ENTRY' **********#
					#***************************************************#

					
/*
if(DEBUG_V)	echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";
*/

				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formNewBlogEntry']) ) {		
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'New Category' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	
							
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					// CATEGORYOBJECT
					// $catLabel=NULL, $catID=NULL
					$Category2 = new Category();
					$Category2->setCatID($_POST['catID']);

					// USEROBJECT
					// $userFirstName=NULL, $userLastName=NULL, $userEmail=NULL,
					// $userCity=NULL, $userPassword=NULL, $userID=NULL

					// BLOGOBJECT
					// $Category, $User,
					// $blogHeadline=NULL, $blogImagePath=NULL,$blogImageAlignment=NULL,
					// $blogContent=NULL,$blogDate=NULL, $blogID=NULL
					$Blog = new Blog(
											$Category2,
											$User,
											$_POST['blogHeadline']
											
											);
					$Blog->setBlogImageAlignment($_POST['blogImageAlignment']);
					$Blog->setBlogContent($_POST['blogContent']);

					// Schritt 3 FORM: Werte ggf. validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$errorCatLabel = checkInputString($Blog->getCategory()->getCatID());


					#********** FINAL FORM VALIDATION **********#
					if( $errorCatLabel ) {
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";						
							
						} else {
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";						
							
					// Schritt 4 FORM: Daten weiterverarbeiten


					#********** CHECK IF CATEGORY NAME ALREADY EXISTS IN DB **********#
if(DEBUG)	   echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Prüfe, ob die Kategorie bereits existiert ... <i>(" . basename(__FILE__) . ")</i></p>\n";

					if( $Category2->checkIfCategoryExistsInDB($PDO) ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Die Kategorie '{$Category2->getCatLabel()}' existiert bereits in der DB! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						$errorCatLabel = 'Diese Kategorie ist bereits in de Datenbank vorhanden!';

					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die Kategorie '{$Category2->getCatLabel()}' existiert noch nicht in der DB. <i>(" . basename(__FILE__) . ")</i></p>\n";				

							// Schritt 3 FORM: ggf. Werte validieren
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
							$errorHeadline = checkInputString($Blog->getBlogHeadline());
							$errorContent 	= checkInputString($Blog->getBlogContent(), 5, 64000);


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
								$Blog->setBlogImagePath( $imageUploadResultArray['imagePath']);
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


							#********** SAVE BLOG OBJECT INTO DB **********#
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Saving Category data into database... <i>(" . basename(__FILE__) . ")</i></p>\n";
						
							if( $Blog->saveToDb($PDO) === false ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Error saving Category into database! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							$dbError 	= 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
							
							} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Category saved successfully into database with ID   <i>(" . basename(__FILE__) . ")</i></p>\n";				
							$dbSuccess 	= 'Die Kategorie wurde erfolgreich in der Datenbank gespeichert.';

							$Blog = NULL;
							}



							}

						} // SAVE CATEGORY INTO DB END

					} // FINAL FORM VALIDATION PART II (IMAGE UPLOAD) END

				} // CHECK IF CATEGORY NAME ALREADY EXISTS END

			} // PROCESS FORM 'NEW CATEGORY' END

			
#***************************************************************************************#	
?>

<!doctype html>

	<html>
		
		<head>	
			<meta charset="utf-8">
			<title>PHP-Projekt Blog OOP | Dashboard</title>
			
			<link rel='stylesheet' href='css/main.css'>
			<link rel='stylesheet' href='css/debug.css'>
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

			<h1 class="dashboard">PHP-Projekt Blog OOP - Dashboard</h1>
			<p class="name">Aktiver Benutzer: <?= $User?->getFullName() ?></p>


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
					<?php foreach($allCategoriesArray AS $categoryObjectDB): ?>
						<option value='<?= $categoryObjectDB->getCatID()?>' <?php if( $Blog?->getCategory()->getCatID() === $categoryObjectDB->getCatID()) echo 'selected'?>>
						<?= $categoryObjectDB->getCatLabel() ?></option>
					<?php endforeach ?>
					</select>
					
					<br>
					
					<label>Überschrift:</label>
					<span class="error"><?= $errorHeadline ?></span><br>
					<input class="dashboard" type="text" name="blogHeadline" placeholder="..." value=""><br>
				

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
					<textarea class="dashboard" name="blogContent" placeholder="..."><?= $Blog?->getBlogContent() ?></textarea><br>
				
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
					<input class="dashboard" type="text" name="catLabel" placeholder="..." value=""><br>

					<input class="dashboard" type="submit" value="Neue Kategorie anlegen">
				</form>
				<!-- ---------- FORM 'NEW CATEGORY' END ---------- -->
				
			
			</aside>

			<div class="clearer"></div>
			<!-- ---------- RIGHT PAGE COLUMN END ---------- -->
	
	
		</body>
</html>

			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			