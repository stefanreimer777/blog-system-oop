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

			
#**********************************************************************************#

				
				#**************************************#
				#********** OUTOUT BUFFERING **********#
				#**************************************#
				
				// ob_start();
				if( ob_start() === false ) {
					// Fehlerfall
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten des Output Bufferings! <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
					
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Output Buffering erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\r\n";									
				}


#**********************************************************************************#


				#*************************************#
				#********** TESTING CLASSES **********#
				#*************************************#

				
				// #********** CLASS CATEGORY **********#
				// $CategoryObject1 = new Category();
				// // $catLabel=NULL, $catID=NULL
				// $CategoryObject2 = new Category('Mobile', '3');
				
				
				// #********** CLASS USER **********#
				// $UserObject1 = new User();
				// $userFirstName=NULL, $userLastName=NULL, $userEmail=NULL,
				// $userCity=NULL, $userPassword=NULL, $userID=NULL
				// $UserObject2 = new User('John', 'Doe', 'johndoe@gmail.com', 'New York', '1234','6');
				
				
				// #********** BLOG OBJECT **********#
				// $Category, $User,
				// $blogHeadline=NULL, $blogImagePath=NULL,$blogImageAlignment=NULL,
				// $blogContent=NULL,$blogDate=NULL, $blogID=NULL
				// $BlogObject1 = new Blog( $CategoryObject1, $UserObject1);
				// $BlogObject2 = new Blog( $CategoryObject2, $UserObject2,
				// 									'Read this!','../../css/images/avatar_dummy.png','left',
				// 									'Very interesting blog', '2022-03-21', '1'
				// 								);
				
				
				
#***************************************************************************************#


				#****************************************#
				#********** INITIALIZE SESSION **********#
				#****************************************#
				
				session_name('blogProject_oop');
				
				if( session_start() === false ) {
					// Fehlerfall
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
										
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
				
if(DEBUG_V)		echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)		print_r($_SESSION);					
if(DEBUG_V)		echo "</pre>";
				
					
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
				$PDO = dbConnect('blog_v1_oop');


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


if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetching all categories from database... <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				$allCategoriesArray = Category::fetchAllFromDb($PDO);



if(DEBUG_V)	echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($allCategoriesArray);					
if(DEBUG_V)	echo "</pre>";

			
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
						if( isset($categoryFilterId) === false OR array_key_exists( $categoryFilterId, $allCategoriesArray ) === false ) {
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

/*
if(DEBUG_V)	echo "<pre class='debug value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";		
*/
						
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formLogin']) ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'Login' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	
					
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					// USEROBJECT	
					// $userFirstName=NULL, $userLastName=NULL, $userEmail=NULL,
					// $userCity=NULL, $userPassword=NULL, $userID=NULL
					$User		=  new User();
					$User->setUserEmail($_POST['loginName']);
					
					$loginPassword = cleanString($_POST['loginPassword']);
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$loginPassword: $loginPassword <i>(" . basename(__FILE__) . ")</i></p>";

					// Schritt 3 FORM: ggf. Werte validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$errorLoginName 		= checkEmail($User->getUserEmail());
					$errorLoginPassword 	= checkInputString($loginPassword);
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$errorLoginName: $errorLoginName <i>(" . basename(__FILE__) . ")</i></p>";
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$errorLoginPassword: $errorLoginPassword <i>(" . basename(__FILE__) . ")</i></p>";


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
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetching all media types from database... <i>(" . basename(__FILE__) . ")</i></p>\n";				


						#********** 1. VALIDATE LOGIN NAME **********#
						if( $User->fetchFromDB($PDO) === false ) {
							// Fehlerfall (Kein passender Datensatz gefunden)
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Der User '{$User->getUserEmail()}' wurde nicht in der Datenbank gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							$loginError = 'Die Logindaten sind ungültig!';
							
						} else {
							// Erfolgsfall (Passender Datensatz zum Usernamen wurde gefunden)
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Der User '{$User->getUserEmail()}' wurde in der Datenbank gefunden. <i>(" . basename(__FILE__) . ")</i></p>\n";				
							

							#********** 2. VALIDATE PASSWORD **********#
							// Wenn die Passworte nicht übereinstimmen, liefert password_verify() false zurück
							if( password_verify( $loginPassword, $User->getUserPassword() ) === false ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Passwort '$loginPassword' stimmt nicht mit dem Passwort aus der DB überein! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								$loginError = 'Die Logindaten sind ungültig!';
								
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Passwort '$loginPassword' stimmt mit dem Passwort aus der DB überein. <i>(" . basename(__FILE__) . ")</i></p>\n";				



								
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
								$_SESSION['ip_address'] 	= $_SERVER['REMOTE_ADDR'];
								$_SESSION['userID'] 			= $User->getUserID();
								$_SESSION['userFirstName'] = $User->getUserFirstName();
								$_SESSION['userLastName'] 	= $User->getUserLastName();
								
								#********** REDIRECT TO DASHBOARD **********#								
								header('Location: dashboard.php');
								exit();
								
								} // START SESSION END
								
							}
						
						}

					} 	// FINAL FORM VALIDATION END	

				} // PROCESS FORM LOGIN END





#***************************************************************************************#


				#************************************************#
				#********** FETCH BLOG ENTRIES FROM DB **********#
				#************************************************#


if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetching all blog entries from database... <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				$allBlogArticlesArray = Blog::fetchAllFromDb($PDO, $categoryFilterId);


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
		
			<link rel='stylesheet' href='css/main.css'>
			<link rel='stylesheet' href='css/debug.css'>
			
			<meta charset="utf-8">
			<title>PHP-Projekt Blog OOP</title>
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



			<h1>PHP-Projekt Blog OOP</h1>
			<p><a href='index.php'>:: Alle Einträge anzeigen ::</a></p>



			<!-- ---------- BLOG ENTRIES START ---------- -->
			
			<main class="blogs fleft">
				
				<?php if( !$allBlogArticlesArray ): ?>
					<p class="info">Noch keine Blogeinträge vorhanden.</p>
				
				<?php else: ?>
				
					<?php foreach( $allBlogArticlesArray AS $singleBlogItemObject): ?>
						<?php $dateTimeArray = isoToEuDateTime($singleBlogItemObject->getBlogDate()) ?>
						
						<article class='blogEntry'>
						
							<a name='entry<?= $singleBlogItemObject->getBlogID() ?>'></a>
							
							<p class='fright'><a href='?action=filterByCategory&catId=<?= $singleBlogItemObject->getCategory()->getCatID() ?>'>Kategorie: <?= $singleBlogItemObject->getCategory()->getCatLabel() ?></a></p>
							<h2 class='clearer'><?= $singleBlogItemObject->getBlogHeadline() ?></h2>

							<p class='author'><?= $singleBlogItemObject->getUser()->getUserFirstName() ?> <?= $singleBlogItemObject->getUser()->getUserLastName() ?> (<?= $singleBlogItemObject->getUser()->getUserCity() ?>) schrieb am <?= $dateTimeArray['date'] ?> um <?= $dateTimeArray['time'] ?> Uhr:</p>
							
							<p class='blogContent'>
							
								<?php if($singleBlogItemObject): ?>
									<img class='<?= $singleBlogItemObject->getBlogImageAlignment() ?>' src='<?= $singleBlogItemObject->getBlogImagePath() ?>' alt='' title=''>
								<?php endif ?>
								
								<?= nl2br( $singleBlogItemObject->getBlogContent() ) ?>
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

					<?php foreach( $allCategoriesArray AS $CategoryObjectDB ): ?>
						<p><a href="?action=filterByCategory&catId=<?= $CategoryObjectDB->getCatID()?>" <?php if(  $CategoryObjectDB->getCatID() == $categoryFilterId) echo 'class="active"' ?>><?= $CategoryObjectDB->getCatLabel() ?></a></p>
					<?php endforeach ?>

				<?php endif ?>
				</nav>

				<div class="clearer"></div>

				<!-- ---------- CATEGORY FILTER LINKS END ---------- -->

</body>

</html>

			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			
		</body>
		
	</html>