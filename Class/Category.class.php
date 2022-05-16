<?php
#*******************************************************************************************#


				#*************************************#
				#********** CLASS CATEGORIE **********#
				#*************************************#

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
				*	Class represents a category
				*	Must be embedded in the blog object
				*
				*/
				class Category {
					
					#*******************************#
					#********** ATTRIBUTE **********#
					#*******************************#
					
					/* 
						Innerhalb der Klassendefinition müssen Attribute nicht zwingend initialisiert werden.
						Ein Weglassen der Initialisierung bewirkt das gleiche, wie eine Initialisierung mit NULL.
					*/
					private $catID;
					private $catLabel;
					
					
					

					
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
					*	Creates a category object
					*	Setters are only called if parameter has other value than '' or NULL
					*	
					*	@param	String		$catLabel=NULL			label of catgeory
					*	@param	Int|String	$catID=NULL				Record ID given by the database
					*	@return	Void
					*
					*/
					public function __construct( $catLabel=NULL, $catID=NULL ) {
if(DEBUG_CC)		echo "<h3 class='debugClass'>🛠 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</h3>\n";						
												
						// Setter nur aufrufen, wenn der jeweilige Parameter keinen Leerstring und kein NULL enthält
						if( $catLabel	!== '' 	AND $catLabel 	!== NULL )		$this->setCatLabel($catLabel);
						if( $catID		!== '' 	AND $catID 		!== NULL )		$this->setCatID($catID);
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
				
					#********** CAT ID **********#
					public function getCatID() : NULL|Int {
						return $this->catID;
					}
					public function setCatID(NULL|String|Int $value) : Void {
						if( filter_var($value, FILTER_VALIDATE_INT) === false ) {
							// Fehlerfall
if(DEBUG_C)				echo "<h3 class='debugClass err'><b>Line " . __LINE__ .  "</b>: " . __METHOD__ . "(): Muss eine ganze Zahl sein! (<i>" . basename(__FILE__) . "</i>)</h3>\n";						
						} else {
							// Erfolgsfall
							// Übergebenen Wert in einen Integer umwandeln
							$value = intval( cleanString($value) );
							$this->catID = $value;
						}	
					}

					#********** CAT LABEL **********#
					public function getCatLabel() : NULL|String {
						return $this->catLabel;
					}
					public function setCatLabel(NULL|String $value) : Void {
						$this->catLabel = cleanString($value);
					}
					
					
					#***********************************************************#
					

					#******************************#
					#********** METHODEN **********#
					#******************************#


					#********** SAVE CATEGORY NAME INTO DB **********#
					/**
					*
					*	Saves a new category into database
					*	
					*	Writes the database's last insert id on success into calling object
					*
					*	@param	PDO	$PDO		DB connection via PDO
					*
					*	@return	Boolean			true on success | false on error					
					*
					*/
					public function saveToDb(PDO $PDO) {
if(DEBUG_C)			echo "<h3 class='debugClass'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</h3>\n";
												
						$sql		= 'INSERT INTO Category
										( catLabel)
										VALUES
										(?)';
										
						$params	= array($this->getCatLabel() );	

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
						// Bei schreibendem Zugriff: Schreiberfolg prüfen (Number of affected rows)
						$rowCount = $PDOStatement->rowCount();
if(DEBUG_C)			echo "<p class='debugClass value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";

						if( $rowCount === 0 ) {
							// Fehlerfall
							return false;
							
						} else {
							// Erfolgsfall
							
							// lastInsertId ins Objekt schreiben
							$this->setCatID( $PDO->lastInsertId() );							
							return true;
						}
					}

				

			
					#***********************************************************#

					#********** CHECK IF CATEGORY NAME ALREADY EXISTS IN DB **********#
					/**
					*
					*	Checks if category name already exists in database 
					*
					*	@param	PDO					$PDO		DB connection via PDO
					*
					*	@return	String|Boolean					Number of matching DB entries or false
					*
					*/
					public function checkIfCategoryExistsInDB(PDO $PDO) {
if(DEBUG_C)			echo "<h3 class='debugClass'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</h3>\n";
												
						$sql 		= 'SELECT COUNT(catLabel) FROM Category
										WHERE catLabel = ?';

						$params 	= array( $this->getCatLabel() );

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
						// Bei SELECT COUNT() Rückgabewert von COUNT() auslesen
						$count = $PDOStatement->fetchColumn();
if(DEBUG_C)			echo "<p class='debugClass value'><b>Line " . __LINE__ . "</b>: \$count: $count <i>(" . basename(__FILE__) . ")</i></p>\n";

						return $count;						
					}


					#***********************************************************#
					
					
					#********** FETCH ALL CATEGORIES FROM DB **********#
					/**
					*
					*	Fetches calling categories object's data from DB 
					*
					*	@param	PDO		$PDO		DB connection via PDO
					*
					*	@return	String				true on corresponding data set | false on no corresponding data set
					*
					*/
					public static function fetchAllFromDb(PDO $PDO) {
if(DEBUG_C)			echo "<h3 class='debugClass'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</h3>\n";						
                                    
                  // Schritt 1 DB: DB-Verbindung herstellen
                  // ist bereits geschehen
                  
                  $sql 		= 'SELECT * FROM Category';
                  
						$params	= array();
						
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
						// Bei lesendem Zugriff: Datensätze abholen
						
						$allCategoriesObjectArray = array();
						
						#********** LOOP START **********#
						while( $row = $PDOStatement->fetch(PDO::FETCH_ASSOC) ) {

							$allCategoriesObjectArray[$row['catID']] = new Category( $row['catLabel'], $row['catID']);
/*						
if(DEBUG_C)				echo "<pre class='debugClass value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_C)				print_r($row);					
if(DEBUG_C)				echo "</pre>";
*/

               }
					return $allCategoriesObjectArray;
                                 
			}   
		}                 

				
				
#*******************************************************************************************#
?>


















