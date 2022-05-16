<?php
#*******************************************************************************************#


				#********************************#
				#********** CLASS USER **********#
				#********************************#

				/*
					Die Klasse ist quasi der Bauplan/die Vorlage für alle Objekte, die aus ihr erstellt werden.
					Sie gibt die Eigenschaften/Attribute eines späteren Objekts vor (Variablen) sowie 
					die "Fähigkeiten" (Methoden/Funktionen), über die das spätere Objekt besitzt.

					Jedes Objekt einer Klasse ist nach dem gleichen Schema aufgebaut (gleiche Eigenschaften und Methoden), 
					besitzt aber i.d.R. unterschiedliche Attributswerte.
				*/

				
#*******************************************************************************************#

				/**
				*
				*	Class represents a User
				*	Must be embedded in an Blog object
				*
				*/
				class User {
					
					#*******************************#
					#********** ATTRIBUTE **********#
					#*******************************#
					
					/* 
						Innerhalb der Klassendefinition müssen Attribute nicht zwingend initialisiert werden.
						Ein Weglassen der Initialisierung bewirkt das gleiche, wie eine Initialisierung mit NULL.
					*/
					private $userID;
					private $userFirstName;
					private $userLastName;
					private $userEmail;
					private $userCity;
					private $userPassword;
					

					#***********************************************************#
					
					
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#
					
					/*
						Der Constructor ist eine magische Methode und wird automatisch aufgerufen,
						sobald mittels des new-Befehls ein neues Objekt erstellt wird.
						Der Constructor erstellt eine neue Klasseninstanz/Objekt.
						Soll ein Objekt beim Erstellen bereits mit Attributwerten versehen werden,
						muss ein eigener Constructor geschrieben werden. Dieser nimmt die Werte in 
						Form von Parametern (genau wie bei Funktionen) entgegen und ruft seinerseits 
						die entsprechenden Setter auf, um die Werte zuzuweisen.					
					*/

					/**
					*
					*	@construct
					*	Creates a user object
					*	Setters are only called if parameter has other value than '' or NULL
					*	
					*	@param	String		$userFirstName=NULL			first name of user
					*	@param	String		$userLastName=NULL			last name of user
					*	@param	String		$userEmail=NULL				email of user
					*	@param	String		$userCity=NULL					city of user
					*	@param	String		$userPassword=NULL			password of user
					*	@param	Int|String	$userID=NULL					Record ID given by the database
					*	
					*	@return	Void
					*
					*/
					public function __construct( $userFirstName=NULL, $userLastName=NULL, $userEmail=NULL,
														  $userCity=NULL, $userPassword=NULL, $userID=NULL ) {
if(DEBUG_CC)		echo "<h3 class='debugClass'>🛠 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</h3>\n";						
						
						// Setter nur aufrufen, wenn der jeweilige Parameter keinen Leerstring und kein NULL enthält
						if( $userFirstName		!== '' 	AND $userFirstName		!== NULL )		$this->setUserFirstName($userFirstName);
						if( $userLastName			!== '' 	AND $userLastName			!== NULL )		$this->setUserLastName($userLastName);
						if( $userEmail				!== '' 	AND $userEmail				!== NULL )		$this->setUserEmail($userEmail);
						if( $userCity				!== '' 	AND $userCity				!== NULL )		$this->setUserCity($userCity);
						if( $userPassword			!== '' 	AND $userPassword			!== NULL )		$this->setUserPassword($userPassword);						
						if( $userID					!== '' 	AND $userID					!== NULL )		$this->setUserID($userID);						
/*						
if(DEBUG_CC)		echo "<pre class='debugClass value'><b>Line " . __LINE__ .  "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_CC)		print_r($this);					
if(DEBUG_CC)		echo "</pre>";
*/
					}
					
					
					#********** DESTRUCTOR **********#
					/*
						Der Destructor ist eine magische Methode und wird automatisch aufgerufen,
						sobald ein Objekt mittels unset() gelöscht wird, oder sobald das Skript beendet ist.
						Der Destructor gibt den vom gelöschten Objekt belegten Speicherplatz wieder frei.
					*/

					/**
					*
					*	@destruct
					*
					*	@return	Void
					*
					*/
					public function __destruct() {
if(DEBUG_CC)		echo "<h3 class='debugClass'>☠️  <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</h3>\n";						
											}
					
					
					
					#***********************************************************#

					
					#*************************************#
					#********** GETTER & SETTER **********#
					#*************************************#
				
					/* 
						Type Hinting
						Man kann den Datentyp einer Variablen bzw. eines Rückgabewertes
						mittels Type Hinting vorbestimmen. Für eine Variable wird der 
						Datentyp DAVOR notiert (string $variable), für einen Rückgabewert
						erfolgt die Notation hinter der Funktionsdeklaration und wird mittels 
						einem : ausgewiesen (function() : string {...})
						Um alternativ zum vorgegebenen Datentyp auch NULL zurückgeben zu können,
						wird vor den Datentyp ein ? (nullable return type) notiert. 
						'function xy() : ?array' bedeutet also 'return Datentyp Array oder NULL'
												
						Beim Ausführen der Funktion wird durch das Type Hinting nun der Datentyp
						geprüft UND im Zweifelsfall in den vorgegebenen Datentyp UMGEWANDELT! Dieses
						Verhalten ist insofern problematisch, als dass sich nicht alle Datentypen 
						verlustfrei umwandeln lassen: Float->Integer = Verlust der Nachkommastallen.
						Komplexe Datentypen lassen sich gar nicht umwandeln, weshalb es nur hier zu 
						einer entsprechenden Fehlermelung kommt.
						
						Daher ist Type Hinting für die sichere Datentypprüfung nur sehr bedingt geeignet.
					*/
					
					#********** USER ID **********#
					public function getUserID() : NULL|Int {
						return $this->userID;
					}
					public function setUserID(NULL|String|Int $value) : Void {
						if( filter_var($value, FILTER_VALIDATE_INT) === false ) {
							// Fehlerfall
if(DEBUG_C)				echo "<h3 class='debugClass err'><b>Line " . __LINE__ .  "</b>: " . __METHOD__ . "(): Muss eine ganze Zahl sein! (<i>" . basename(__FILE__) . "</i>)</h3>\n";						
						} else {
							// Erfolgsfall
							// Übergebenen Wert in einen Integer umwandeln
							$value = intval( cleanString($value) );
							$this->userID = $value;
						}	
					}
					
					#********** USER FIRST NAME **********#
					public function getUserFirstName() : NULL|String {
						return $this->userFirstName;
					}
					public function setUserFirstName(NULL|String $value) : Void {
						$this->userFirstName = cleanString($value);
					}
					
					
					#********** USER LAST NAME **********#
					public function getUserLastName() : NULL|String {
						return $this->userLastName;
					}
					public function setUserLastName(NULL|String $value) : Void {
						$this->userLastName = cleanString($value);
					}
					
					
					#********** USER EMAIL **********#
					public function getUserEmail() : NULL|String {
						return $this->userEmail;
					}
					public function setUserEmail(NULL|String $value) : Void {
						$this->userEmail = cleanString($value);
					}

					
					#********** USER CITY **********#
					public function getUserCity() : NULL|String {
						return $this->userCity;
					}
					public function setUserCity(NULL|String $value) : Void {
						$this->userCity = cleanString($value);
					}
					
					
					#********** USER PASSWORD **********#
					public function getUserPassword() : NULL|String {
						return $this->userPassword;
					}
					public function setUserPassword(NULL|String $value) : Void {
						$this->userPassword = cleanString($value);
					}

					
					#********** VIRTUAL ATTRIBUTES **********#
					
					public function getFullName() {
						return $this->getUserFirstName() . ' ' . $this->getUserLastName();
					}
							
					
					#***********************************************************#
					

					#******************************#
					#********** METHODEN **********#
					#******************************#
					
					
					#********** FETCH USER DATA FROM DATABASE **********#
					/**
					*
					*	Fetches user object's data 
					*	
					* 	@param	PDO		$PDO		DB connection via PDO
					*
					*	@return	Boolean				true on corresponding data set | false on no corresponding data set
					*
					*/
					public function fetchFromDB(PDO $PDO) {
if(DEBUG_C)			echo "<h3 class='debugClass'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</h3>\n";
												
						$sql 		= 'SELECT * FROM User
										 WHERE userEmail =? ';

						
						$params 	= array( $this->getUserEmail() );
						
						// Schritt 2 DB: SQL-Statement vorbereiten
						$PDOStatement = $PDO->prepare($sql);
						
						// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
						try {	
							$PDOStatement->execute($params);						
						} catch(PDOException $error) {
if(DEBUG_C)				echo "<p class='debugClass err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
						}
						
						// Schritt 4 DB: Daten weiterverarbeiten
						// Bei SELECT: Datensatz abholen
						$row = $PDOStatement->fetch(PDO::FETCH_ASSOC);

if(DEBUG_C)			echo "<pre class='debugClass value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_C)			print_r($row);					
if(DEBUG_C)			echo "</pre>";
					
						if( $row === false ) {
						// Fehlerfall (Kein passender Datensatz gefunden)
						return false;
	
						} else {
							// Erfolgsfall (Passender Datensatz zum Accountnamen wurde gefunden)
	
	
							#********** WRITE DATA INTO OBJECT **********#


							#********** USER OBJECT **********#
							if( $row['userID'] 				!== '' AND $row['userID'] 				!== NULL ) 	$this->setUserID( $row['userID'] );
							if( $row['userFirstName'] 		!== '' AND $row['userFirstName'] 	!== NULL ) 	$this->setUserFirstName( $row['userFirstName'] );
							if( $row['userLastName'] 		!== '' AND $row['userLastName'] 		!== NULL ) 	$this->setUserLastName( $row['userLastName'] );
							if( $row['userEmail'] 			!== '' AND $row['userEmail'] 			!== NULL ) 	$this->setUserEmail( $row['userEmail'] );
							if( $row['userCity'] 			!== '' AND $row['userCity'] 			!== NULL ) 	$this->setUserCity( $row['userCity'] );
							if( $row['userPassword'] 		!== '' AND $row['userPassword'] 		!== NULL ) 	$this->setUserPassword( $row['userPassword'] );
							
						
if(DEBUG_C)				echo "<pre class='debugClass value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_C)				print_r($this);					
if(DEBUG_C)				echo "</pre>";
					
							return true;

				
							#***********************************************************#
					}
				}
			}
			
				
				
#*******************************************************************************************#
?>


















