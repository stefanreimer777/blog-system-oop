<?php
#*******************************************************************************************#


				#********************************#
				#********** CLASS BLOG **********#
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
				*	Class represents a blog
				*	Must embed User Category object
				*
				*/
				class Blog {
					
					#*******************************#
					#********** ATTRIBUTE **********#
					#*******************************#
					
					/* 
						Innerhalb der Klassendefinition müssen Attribute nicht zwingend initialisiert werden.
						Ein Weglassen der Initialisierung bewirkt das gleiche, wie eine Initialisierung mit NULL.
					*/
					private $blogID;
					private $blogHeadline;
					private $blogImagePath;
					private $blogImageAlignment;
					private $blogContent;
					private $blogDate;
					
					
					#********** CATEGORY OBJECT **********#
					private $Category;

					#********** USER OBJECT **********#
					private $User;
					

					
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
					*	Creates a blog object
					*	Must embed Category and User object
					*	Setters for non object data types are only called if parameter has other value than '' or NULL
					*
					*	@param	Category 	$Category				Category object defining category of account
					*	@param	User 			$User						User object corresponding to blog object
					
					*	@param	String 		$blogHeadline=NULL			Blog headline 
					*	@param	String 		$blogImagePath=NULL			Path to blog image
					*	@param	String 		$blogImageAlignment=NULL	Blog image alignment left or right
					*	@param	String 		$blogContent=NULL				Blog content text
					*	@param	String 		$blogDate=NULL					Blog date creation
					*	@param	Int|String 	$blogID=NULL					Record ID given by database
					*
					*	@return	Void
					*
					*/
					public function __construct( $Category, $User,
														  $blogHeadline=NULL, $blogImagePath=NULL,$blogImageAlignment=NULL,
														  $blogContent=NULL,$blogDate=NULL, $blogID=NULL ) {
if(DEBUG_CC)		echo "<h3 class='debugClass'>🛠 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</h3>\n";			

						// Das Accountobjekt muss immer die eingebetteten Objekte beinhalten
						$this->setCategory($Category);
						$this->setUser($User);
						
						
												
						// Setter nur aufrufen, wenn der jeweilige Parameter keinen Leerstring und kein NULL enthält
						if( $blogHeadline			!== '' 	AND $blogHeadline			!== NULL )		$this->setBlogHeadline($blogHeadline);
						if( $blogImagePath		!== '' 	AND $blogImagePath		!== NULL )		$this->setBlogImagePath($blogImagePath);
						if( $blogImageAlignment	!== '' 	AND $blogImageAlignment	!== NULL )		$this->setBlogImageAlignment($blogImageAlignment);
						if( $blogContent			!== '' 	AND $blogContent			!== NULL )		$this->setBlogContent($blogContent);
						if( $blogDate				!== '' 	AND $blogDate				!== NULL )		$this->setBlogDate($blogDate);
						if( $blogID					!== '' 	AND $blogID					!== NULL )		$this->setBlogID($blogID);
				
if(DEBUG_CC)		echo "<pre class='debugClass value'><b>Line " . __LINE__ .  "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_CC)		print_r($this);					
if(DEBUG_CC)		echo "</pre>";

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
				
					#********** BLOG ID **********#
					public function getBlogID() : NULL|Int {
						return $this->blogID;
					}
					public function setBlogID(NULL|String|Int $value) : Void {
						if( filter_var($value, FILTER_VALIDATE_INT) === false ) {
							// Fehlerfall
if(DEBUG_C)				echo "<h3 class='debugClass err'><b>Line " . __LINE__ .  "</b>: " . __METHOD__ . "(): Muss eine ganze Zahl sein! (<i>" . basename(__FILE__) . "</i>)</h3>\n";						
						} else {
							// Erfolgsfall
							// Übergebenen Wert in einen Integer umwandeln
							$value = intval( cleanString($value) );
							$this->blogID = $value;
						}	
					}
					
					#********** BLOG HEADLINE **********#
					public function getBlogHeadline() : NULL|String {
						return $this->blogHeadline;
					}
					public function setBlogHeadline(NULL|String $value) : Void {
						$this->blogHeadline = cleanString($value);
					}


					#********** BLOG IMAGE PATH **********#
					public function getBlogImagePath() : NULL|String {
						return $this->blogImagePath;
					}
					public function setBlogImagePath(NULL|String $value) : Void {
						$this->blogImagePath = cleanString($value);
					}


					#********** BLOG IMAGE ALIGNMENT **********#
					public function getBlogImageAlignment() : NULL|String {
						return $this->blogImageAlignment;
					}
					public function setBlogImageAlignment(NULL|String $value) : Void {
						$this->blogImageAlignment = cleanString($value);
					}


					#********** BLOG CONTENT **********#
					public function getBlogContent() : NULL|String {
						return $this->blogContent;
					}
					public function setBlogContent(NULL|String $value) : Void {
						$this->blogContent = cleanString($value);
					}


					#********** BLOG DATE **********#
					public function getBlogDate() : NULL|String {
						return $this->blogDate;
					}
					public function setBlogDate(NULL|String $value) : Void {
						$this->blogDate = cleanString($value);
					}


					#********** CATEGORY OBJECT **********#
					public function getCategory() : Category {
						return $this->Category;
					}
					public function setCategory(Category $value) : Void {
						$this->Category = $value;
					}


					#********** USER OBJECT **********#
					public function getUser() : User {
						return $this->User;
					}
					public function setUser(User $value) : Void {
						$this->User = $value;
					}
					
					
					
					#***********************************************************#
					

					#******************************#
					#********** METHODEN **********#
					#******************************#


					#********** FETCH BLOG DATA FROM DATABASE **********#
					/**
					*
					*	Fetches calling blog object's data and embedded object's data from DB 
					*	by either user id or category id via INNER JOIN
					*	Calling blog object must contain User object and Categorye object
					*
					*	@param	PDO		$PDO		DB connection via PDO
					*
					*
					*/
					public static function fetchAllFromDb(PDO $PDO, NULL|String|Int $categoryID=NULL ) {
if(DEBUG_C)			echo "<h3 class='debugClass'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</h3>\n";
												
						$sql		= 'SELECT * FROM Blog
										INNER JOIN User USING (userID)
										INNER JOIN Category USING (catID)' ;
						
						$params	= array();



						#********** FETCH BLOG ENTRIES BY ID **********#
						if( $categoryID !== NULL ) {
if(DEBUG) echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Filter Blog Einträge nach Category ID: $categoryID... <i>(" . basename(__FILE__) . ")</i></p>";
						
							$sql   .= ' WHERE catID = ?';

							$params = array($categoryID);


						}


						
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

						$BlogEntriesArray = array();

						#********** LOOP START **********#
						while( $row = $PDOStatement->fetch(PDO::FETCH_ASSOC) ) {
/*				
if(DEBUG_C)				echo "<pre class='debugClass value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_C)				print_r($row);					
if(DEBUG_C)				echo "</pre>";
*/

							$Category = new Category( $row['catLabel'], $row['catID']);
							$User = new User( $row['userFirstName'], $row['userLastName'], $row['userEmail'],
													$row['userCity'], $row['userPassword'], $row['userID'] );

							// $Category, $User,
							// $blogHeadline=NULL, $blogImagePath=NULL,$blogImageAlignment=NULL,
							// $blogContent=NULL,$blogDate=NULL, $blogID=NULL 
							$BlogEntriesArray[$row['blogID']] = new Blog( $Category, $User, 
																						$row['blogHeadline'],$row['blogImagePath'], $row['blogImageAlignment'], $row['blogContent'],  
																						$row['blogDate'],$row['blogID']
																					);



						}
						return $BlogEntriesArray;

					}
					
					#***********************************************************#


					#********** SAVE BLOG ENTRY INTO DB **********#
					/**
					*
					*	Saves new data set of calling object's attributes data into database
					*	Writes the database's last insert id on success into calling object
					*
					*	@param	PDO	$PDO		DB connection via PDO
					*
					*	@return	Boolean			true on success | false on error					
					*
					*/
					public function saveToDb(PDO $PDO) {
if(DEBUG_C)			echo "<h3 class='debugClass'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</h3>\n";
																		
						$sql		= 'INSERT INTO Blog
										(blogHeadline, blogImagePath, blogImageAlignment, blogContent, catID, userID)
										
										VALUES

										(?,?,?,?,?,?)';
										
						$params 	= array( 
												$this->getBlogHeadline(),
												$this->getBlogImagePath() ,
												$this->getBlogImageAlignment() ,
												$this->getBlogContent() ,
												$this->getCategory()->getCatID() ,
												$this->getUser()->getUserID() );
												

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
							$this->setBlogID( $PDO->lastInsertId() );							
							return true;
						}
					}
						
										
						
									
				#***********************************************************#
					
				}

				
				
#*******************************************************************************************#
?>


















