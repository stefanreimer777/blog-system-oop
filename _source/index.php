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


				#****************************************#
				#********** INITIALIZE SESSION **********#
				#****************************************#
				
				session_name('blogProject');
				
				if( session_start() === false ) {
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
					
					#********** USER IS NOT LOGGED IN **********#
					if( !isset($_SESSION['userID']) ) {
if(DEBUG)			echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: User ist nicht eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";				

						// delete empty session
						session_destroy();
						
						// set flag for login form instead of navigation links
						$showLoginForm = true;
					
					
					#**********  USER IS LOGGED IN **********#
					} else {
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: User ist eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
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
						
						// set flag for navigation links instead of login form
						$showLoginForm = false;					
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
				
				$loginError 			= NULL;
				$categoryFilterId		= NULL;


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
				
				// Gefundene Datensätze für spätere Verarbeitung in Zweidimensionales Array zwischenspeichern
				$allCategoriesArray = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);

/*
if(DEBUG_V)	echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($allCategoriesArray);					
if(DEBUG_V)	echo "</pre>";
*/
			
#***************************************************************************************#

			
				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#
				
				// Schritt 1 URL: Prüfen, ob Parameter übergeben wurde
				if( isset($_GET['action']) ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben. <i>(" . basename(__FILE__) . ")</i></p>\n";										
			
					// Schritt 2 URL: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$action = cleanString($_GET['action']);
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>";
		
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
						
						header('Location: index.php');
						exit();
						
						
					#********** FILTER BY CATEGORY **********#					
					} elseif( $action === 'filterByCategory' ) {
if(DEBUG)			echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Kategoriefilter aktiv... <i>(" . basename(__FILE__) . ")</i></p>";				
						
						// 2. URL-Parameter auslesen
						$categoryFilterId = cleanString($_GET['catId']);
if(DEBUG_V)			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryFilterId: $categoryFilterId <i>(" . basename(__FILE__) . ")</i></p>\n";			

						/*
							SICHERHEIT: Prüfen, ob zweiter Parameter 'catID' übergeben wurde und ob 'catId' eine gültige Kategorie-ID darstellt.
							Da $allCategoriesArray ein zweidimensionales Array ist, wird mittels in_array($needle, array_column($haystack, IndexName)) 
							geprüft, ob das in $allCategoriesArray enthaltene Array unter dem Key 'cat_id' einen korrespondierenden Wert aufweist.
							Die Funktion array_column() gibt ein Array mit allen Werten aus dem definierten Index zurück. In diesem konkreten Fall
							also ein Array mit allen cat_ids aus den einzelnen Category Arrays des zweidimensionalen $allCategoriesArray aus der DB.
						*/
						#********** INVALID CATEGORY ID **********#
						if( !isset($categoryFilterId) OR !in_array( $categoryFilterId, array_column($allCategoriesArray, 'catID') ) ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Gewählte Kategorie-ID ist ungültig! <i>(" . basename(__FILE__) . ")</i></p>\n";
							$categoryFilterId = NULL;
						
						#********** VALID CATEGORY ID **********#
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Gewählte Kategorie-ID ist gültig. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						}
						
					} // BRANCHING END
					
				} // PROCESS URL PARAMETERS END
			

#***************************************************************************************#


				#****************************************#
				#********** PROCESS FORM LOGIN **********#
				#****************************************#				
						
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formLogin']) ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'Login' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	

					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$loginName 		= cleanString($_POST['loginName']);
					$loginPassword = cleanString($_POST['loginPassword']);
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$loginName: $loginName <i>(" . basename(__FILE__) . ")</i></p>";
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$loginPassword: $loginPassword <i>(" . basename(__FILE__) . ")</i></p>";

					// Schritt 3 FORM: ggf. Werte validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$errorLoginName 		= checkEmail($loginName);
					$errorLoginPassword 	= checkInputString($loginPassword);
					
					
					#********** FINAL FORM VALIDATION **********#					
					if( $errorLoginName OR $errorLoginPassword ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";						
						$loginError = 'Benutzername oder Passwort falsch!';
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";						
									
						// Schritt 4 FORM: Daten weiterverarbeiten
						
						
						#********** FETCH USER DATA FROM DB BY LOGIN NAME **********#						
						$sql 		= 	'SELECT userID, userFirstName, userLastName, userPassword FROM users 
										 WHERE userEmail = :ph_userEmail';
						
						$params 	= array( 'ph_userEmail' => $loginName );
						
						// Schritt 2 DB: SQL-Statement vorbereiten
						$PDOStatement = $PDO->prepare($sql);
						
						// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
						try {	
							$PDOStatement->execute($params);								
						} catch(PDOException $error) {
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
						}
						
						
						#********** VERIFY LOGIN NAME **********#						
						if( !$row = $PDOStatement->fetch(PDO::FETCH_ASSOC) ) {
							// Fehlerfall:
if(DEBUG)				echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FEHLER: Benutzername wurde nicht in DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>";
							$loginError = 'Benutzername oder Passwort falsch!';
						
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Benutzername wurde in DB gefunden. <i>(" . basename(__FILE__) . ")</i></p>";
						
						
							#********** VERIFY PASSWORD **********#							
							if( !password_verify( $loginPassword, $row['userPassword']) ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FEHLER: Passwort stimmt nicht mit DB überein! <i>(" . basename(__FILE__) . ")</i></p>";
								$loginError = 'Benutzername oder Passwort falsch!';
							
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Passwort stimmt mit DB überein. LOGIN OK. <i>(" . basename(__FILE__) . ")</i></p>";
							
																
								#********** START SESSION **********#
								if( !session_start() ) {
									// Fehlerfall
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									$loginError = 'Der Loginvorgang konnte nicht durchgeführt werden!<br>
														Bitte überprüfen Sie die Sicherheitseinstellungen Ihres Browsers und 
														aktivieren Sie die Annahme von Cookies für diese Seite.';
									
									// TODO: Eintrag in ErrorLogFile
									
								} else {
									// Erfolgsfall
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
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
									
									
									#********** SAVE USER DATA INTO SESSION **********#
if(DEBUG)						echo "<p class='debug'>Line <b>" . __LINE__ . "</b>: Schreibe Userdaten in Session... <i>(" . basename(__FILE__) . ")</i></p>";
									#********** SAVE USER DATA INTO SESSION **********#
									/*
										SICHERHEIT: Um Session Hijacking und ähnliche Identitätsdiebstähle zu verhindern,
										wird die IP-Adresse des sich einloggenden Users geloggt und in die Session gespeichert.
										Eine IP-Adresse zu fälschen ist nahezu unmöglich. Wenn sich also der Dieb von einer
										anderen IP-Adresse aus einloggen will, wird ihm auf der Folgeseite der Zutritt verweigert.
										
										Diese Methode ist sehr sicher, allerdings verhindert sie, dass ein User permanent eingeloggt 
										bleiben kann. Spätestens, wenn ein User nach 24 Stunden eine frische IP-Adresse von seinem
										Internetprovider erhält, muss er sich neu einloggen.
										Hier sollte allerdings das Thema Sicherheit Vorrang haben vor der Bequemlichkeit des Users.
									*/
									$_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
									$_SESSION['userID'] 			= $row['userID'];
									$_SESSION['userFirstName'] = $row['userFirstName'];
									$_SESSION['userLastName'] 	= $row['userLastName'];
									
									
									#********** REDIRECT TO DASHBOARD **********#								
									header('Location: dashboard.php');
									exit();
									
								} // START SESSION END
							
							} // VERIFY PASSWORD END
							
						} // VERIFY LOGIN NAME END
						
					} // FINAL FORM VALIDATION END

				} // PROCESS FORM LOGIN END

			
#***************************************************************************************#


				#************************************************#
				#********** FETCH BLOG ENTRIES FORM DB **********#
				#************************************************#				
				
				
				#********** PREPARE SQL STATEMENT AND PLACEHOLDERS **********#
				// for both cases generate sql base statement
				$sql 		= 	'SELECT * FROM blogs
								 INNER JOIN users USING(userID)
								 INNER JOIN categories USING(catID)';
								 
				// no placeholder for case a)
				$params 	= 	NULL;					 
				
				
				#********** A) FETCH ALL BLOG ENTRIES **********#
				if( !isset( $categoryFilterId ) ) {
if(DEBUG)		echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Lade alle Blog-Einträge... <i>(" . basename(__FILE__) . ")</i></p>";

				#********** B) FILTER BLOG ENTRIES BY CATEGORY ID **********#				
				} else {
if(DEBUG)		echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Filtere Blog-Einträge nach Kategorie-ID$categoryFilterId... <i>(" . basename(__FILE__) . ")</i></p>";					
					
					// add condition for category filter
					$sql		.=	' WHERE catID = :ph_catID';
					// assign placeholder
					$params 	 = array( 'ph_catID' => $categoryFilterId );
				}					 
				
				
				// for both cases add 'order by' condition
				$sql		.= ' ORDER BY blogDate DESC';				
				#**************************************************************#


				// Schritt 2 DB: SQL-Statement vorbereiten
				$PDOStatement = $PDO->prepare($sql);
				
				// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
				try {	
					$PDOStatement->execute($params);								
				} catch(PDOException $error) {
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
					$dbError = 'Fehler beim Zugriff auf die Datenbank!';
				}
				
				// Gefundene Datensätze für spätere Verarbeitung in Zweidimensionales Array zwischenspeichern
				$allBlogArticlesArray = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
/*
if(DEBUG_V)	echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($allBlogArticlesArray);					
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

	<body>
		
		<!-- ---------- PAGE HEADER START ---------- -->
		<header class="fright">
						
			<?php if( $showLoginForm == true ): ?>
				<?php if($loginError): ?>
				<p class="error"><b><?= $loginError ?></b></p>
				<?php endif ?>
				
				<!-- -------- Login Form START -------- -->
				<form action="" method="POST">
					<input type="hidden" name="formLogin">
					<input type="text" name="loginName" placeholder="Email">
					<input type="password" name="loginPassword" placeholder="Password">
					<input type="submit" value="Login">
				</form>
				<!-- -------- Login Form END -------- -->
				
			<?php else: ?>
				<!-- -------- PAGE LINKS START -------- -->
				<a href="?action=logout">Logout</a><br>
				<a href='dashboard.php'>zum Dashboard >></a>
				<!-- -------- PAGE LINKS END -------- -->
			<?php endif ?>
		
		</header>
		
		<div class="clearer"></div>
				
		<br>
		<hr>
		<br>
		
		<!-- ---------- PAGE HEADER END ---------- -->
		
		
		
		<h1>PHP-Projekt Blog</h1>
		<p><a href='index.php'>:: Alle Einträge anzeigen ::</a></p>
		
		
		
		<!-- ---------- BLOG ENTRIES START ---------- -->
		
		<main class="blogs fleft">
			
			<?php if( !$allBlogArticlesArray ): ?>
				<p class="info">Noch keine Blogeinträge vorhanden.</p>
			
			<?php else: ?>
			
				<?php foreach( $allBlogArticlesArray AS $singleBlogItemArray ): ?>
					<?php $dateTimeArray = isoToEuDateTime($singleBlogItemArray['blogDate']) ?>
					
					<article class='blogEntry'>
					
						<a name='entry<?= $singleBlogItemArray['blogID'] ?>'></a>
						
						<p class='fright'><a href='?action=filterByCategory&catId=<?= $singleBlogItemArray['catID'] ?>'>Kategorie: <?= $singleBlogItemArray['catLabel'] ?></a></p>
						<h2 class='clearer'><?= $singleBlogItemArray['blogHeadline'] ?></h2>

						<p class='author'><?= $singleBlogItemArray['userFirstName'] ?> <?= $singleBlogItemArray['userLastName'] ?> (<?= $singleBlogItemArray['userCity'] ?>) schrieb am <?= $dateTimeArray['date'] ?> um <?= $dateTimeArray['time'] ?> Uhr:</p>
						
						<p class='blogContent'>
						
							<?php if($singleBlogItemArray['blogImagePath']): ?>
								<img class='<?= $singleBlogItemArray['blogImageAlignment'] ?>' src='<?= $singleBlogItemArray['blogImagePath'] ?>' alt='' title=''>
							<?php endif ?>
							
							<?= nl2br( $singleBlogItemArray['blogContent'] ) ?>
						</p>
						
						<div class='clearer'></div>
						
						<br>
						<hr>
						
					</article>
					
				<?php endforeach ?>
			<?php endif ?>
			
		</main>
		
		<!-- ---------- BLOG ENTRIES END ---------- -->
		
		
		
		<!-- ---------- CATEGORY FILTER LINKS START ---------- -->
		
		<nav class="categories fright">

			<?php if( !$allCategoriesArray ): ?>
				<p class="info">Noch keine Kategorien vorhanden.</p>
			
			<?php else: ?>
			
				<?php foreach( $allCategoriesArray AS $categorySingleItemArray ): ?>
					<p><a href="?action=filterByCategory&catId=<?= $categorySingleItemArray['catID']?>" <?php if( $categorySingleItemArray['catID'] == $categoryFilterId ) echo 'class="active"' ?>><?= $categorySingleItemArray['catLabel'] ?></a></p>
				<?php endforeach ?>

			<?php endif ?>
		</nav>

		<div class="clearer"></div>

		<!-- ---------- CATEGORY FILTER LINKS END ---------- -->
		
	</body>

</html>







