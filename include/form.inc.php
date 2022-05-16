<?php
#**************************************************************************************#

				
				/**
				*
				*	Ersetzt potentiell gefährliche Zeichen (< > " ' &) eines übergebenen Strings
				*	durch HTML-Entities und entfernt alle Whitespaces vor und nach dem String.
				*	Ersetzt einen übergebenen Leerstring durch NULL
				*
				*	@param	String	$value		Der zu übergebende String
				*
				*	@return	String					Der entschärfte und bereinigte String
				*
				*/
				function cleanString( $value ) {
if(DEBUG_F)		echo "<p class='debugCleanString'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value') <i>(" . basename(__FILE__) . ")</i></p>\n";	
					
					/*
						SICHERHEIT: Damit so etwas nicht passiert: <script>alert("HACK!")</script>
						muss der empfangene String ZWINGEND entschärft werden!
						htmlspecialchars() wandelt potentiell gefährliche Steuerzeichen wie
						< > " & in HTML-Code um (&lt; &gt; &quot; &amp;)
						Der Parameter ENT_QUOTES wandelt zusätzlich einfache ' in &apos; um
						Der Parameter ENT_HTML5 sorgt dafür, dass der generierte HTML-Code HTML5-konform ist
						Der 1. optionale Parameter regelt die zugrundeliegende Zeichencodierung 
						(NULL=Zeichencodierung wird vom Webserver übernommen)
						Der 2. optionale Parameter regelt, ob bereits vorhandene HTML-Entities erneut entschärft werden
						(false=keine doppelte Entschärfung)
					*/
					$value = htmlspecialchars( $value, ENT_QUOTES | ENT_HTML5, NULL, false );
					
					// trim() entfernt vor und nach einem String sämtliche Whitespaces 
					// (Leerzeichen, Tabs, Zeilenumbrüche)
					$value = trim($value);
					
					// Damit cleanString() nicht NULL-Werte in Leerstings verändert, wird 
					// ein eventueller Leerstring in $value mit NULL überschrieben 
					if( $value === '' ) {
						$value = NULL;
					}
					
					return $value;
				}
				

#**************************************************************************************#


				/**
				*
				*	Prüft einen übergebenen String auf Leerstring.
				*
				*	@param	String	$value		Der zu übergebende String
				*
				*	@return	String|NULL				Fehlermeldung bei Leerstring | NULL
				*
				*/
				function checkMandatory($value) {
if(DEBUG_F)		echo "<p class='debugCheckMandatory'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value') <i>(" . basename(__FILE__) . ")</i></p>\n";	
					
					/* von PHP als false interpretiert werden: '', '0', NULL, 0, 0.0, false
						WICHTIG: Die Prüfung auf Leerfeld muss zwingend den Datentyp Sting mitprüfen,
						da ansonsten bei einer Eingabe '0' (z.B. Anzahl der im Haushalt lebenden Kinder: 0)
						die '0' als false und somit als leeres Feld gewertet wird!
					*/	
					if( $value === '' OR $value === NULL ) {  // $value === NULL muss ergänzt werden, wenn cleanString auf NULL-Werte umgestellt wird.
						// Fehlerfall					
						return 'Dies ist ein Pflichtfeld!';
						
					} else {
						// Erfolgsfall
						return NULL;
					}
				}	


#**************************************************************************************#

				
				/**
				*
				*	Prüft einen übergebenen String auf Länge und optional zusätzlich auf Leerstring
				*	Generiert Fehlermeldung bei Leerstring oder ungültiger Länge
				*
				*	@param	String		$value									Der übergebene String
				*	@param	Integer		$minLength=INPUT_MIN_LENGTH		Die zu prüfende Mindestlänge
				*	@param	Integer		$maxLength=INPUT_MAX_LENGTH		Die zu prüfende Maximallänge
				*
				*	@return	String|NULL												Fehlermeldung | ansonsten NULL
				*
				*/
				function checkInputString($value, $minLength=INPUT_MIN_LENGTH, $maxLength=INPUT_MAX_LENGTH, $checkEmptyString=true) {
if(DEBUG_F)		echo "<p class='debugCheckInputString'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value' [$minLength | $maxLength]) <i>(" . basename(__FILE__) . ")</i></p>\n";	
					
					// Optional (wenn $checkEmptyString=true):
					// Prüfen auf Leerstring
					if( $checkEmptyString === true AND $errorMessage = checkMandatory($value) ) {
						// Fehlerfall
						return $errorMessage;
					
					// Prüfen auf Mindestlänge
					} elseif( mb_strlen($value) < $minLength  ) {
						// Fehlerfall
						return "Muss mindestens $minLength Zeichen lang sein!";
						
					// Prüfen auf Maximallänge	
					} elseif( mb_strlen($value) > $maxLength ) {
						// Fehlerfall
						return "Darf maximal $maxLength Zeichen lang sein!";
						
					} else {
						// Erfolgsfall
						return NULL;
					}
				}


#**************************************************************************************#

				
				/**
				*
				*	Prüft einen übergebenen String auf Leerstring und eine valide Email-Adresse
				*	Generiert Fehlermeldung bei Leerstring oder ungültiger Email-Adresse
				*
				*	@param	String	$value		Der übergebene String
				*
				*	@return	String|NULL				Fehlermeldung | ansonsten NULL
				*
				*/
				function checkEmail($value) {
if(DEBUG_F)		echo "<p class='debugCheckEmail'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value') <i>(" . basename(__FILE__) . ")</i></p>\n";	
					
					// Prüfen auf Leerstring
					if( $errorMessage = checkMandatory($value) ) {
						// Fehlerfall
						return $errorMessage;
					
					// Prüfen, auf Validität
					} elseif( filter_var( $value, FILTER_VALIDATE_EMAIL ) === false ) {
						// Fehlerfall
						return 'Dies ist keine gültige Email-Adresse!';
						
					} else {
						// Erfolgsfall
						return NULL;
					}
				}


#**************************************************************************************#

				
				/**
				*
				*	Validiert ein auf den Server geladenes Bild, generiert einen unique Dateinamen
				*	sowie eine sichere Dateiendung und verschiebt es in ein anzugebendes Zielverzeichnis.
				*	Validiert werden der aus dem Dateiheader ausgelesene MIME-Type, die aus dem Dateiheader
				*	ausgelesene Bildgröße und die ermittelte Dateigröße. Der Dateiheader wird außerdem auf
				*	Plausibilität geprüft.
				*
				*	@param	String	$fileTemp														Der temporäre Pfad zum hochgeladenen Bild im Quarantäneverzeichnis
				*	@param	Integer	$imageMaxWidth=IMAGE_MAX_WIDTH							Die maximal erlaubte Bildbreite in Pixeln
				*	@param	Integer	$imageMaxHeight=IMAGE_MAX_HEIGHT							Die maximal erlaubte Bildhöhe in Pixeln
				*	@param	Integer	$imageMaxSize=IMAGE_MAX_SIZE								Die maximal erlaubte Dateigröße in Bytes
				*	@param	String	$imageUploadPath=IMAGE_UPLOAD_PATH						Das Zielverzeichnis
				*	@param	Array		$imageAllowedMimeTypes=IMAGE_ALLOWED_MIME_TYPES		Whitelist der zulässigen MIME-Types mit den zugehörigen Dateiendungen
				*	@param	Integer	$imageMinSize=IMAGE_MIN_SIZE								Die minimal erlaubte Dateigröße in Bytes
				*
				*	@return	Array		{'imagePath'=>String|NULL, 								Bei Erfolg der Speicherpfad zur Datei im Zielverzeichnis | bei Fehler NULL
				*							 'imageError'=>String|NULL}								Bei Erfolg NULL | Bei Fehler Fehlermeldung
				*
				*/
				function imageUpload( 	$fileTemp,
												$imageMaxWidth 			= IMAGE_MAX_WIDTH,
												$imageMaxHeight 			= IMAGE_MAX_HEIGHT,
												$imageMaxSize 				= IMAGE_MAX_SIZE,
												$imageUploadPath 			= IMAGE_UPLOAD_PATH,
												$imageAllowedMimeTypes 	= IMAGE_ALLOWED_MIME_TYPES,
												$imageMinSize 				= IMAGE_MIN_SIZE
											) {
if(DEBUG_F)		echo "<p class='debugImageUpload'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$fileTemp') <i>(" . basename(__FILE__) . ")</i></p>\n";	
					
					
					#***************************************************************************#
					#********** GATHER INFORMATION FOR IMAGE FILE VIA THE FILE HEADER **********#
					#***************************************************************************#
					
					/*
						Die Funktion getimagesize() liest den Dateiheader einern Bilddatei aus und 
						liefert bei gültigem MIME Type ('image/...') ein gemischtes Array zurück:
						
						[0] 				Bildbreite in PX 
						[1] 				Bildhöhe in PX 
						[3] 				Einen für das HTML <img>-Tag vorbereiteten String (width="480" height="532") 
						['bits']			Anzahl der Bits pro Kanal 
						['channels']	Anzahl der Farbkanäle (somit auch das Farbmodell: RGB=3, CMYK=4) 
						['mime'] 		MIME Type
						
						Bei ungültigem MIME Type (also nicht 'image/...') liefert getimagesize() NULL zurück
					*/
					$imageDataArray = getimagesize($fileTemp);
/*					
if(DEBUG_F)		echo "<pre class='debugImageUpload value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_F)		print_r($imageDataArray);					
if(DEBUG_F)		echo "</pre>";					
*/					
					
					#********** CHECK FOR VALID MIME TYPE **********#
					if( $imageDataArray === false ) {
						// Fehlerfall (mime type is not valid)
						/*
							Bildwerte auf NULL setzen, damit die Variablen für die nachfolgenden
							Validierungen exitieren und zu korrekten Fehlermeldungen führen
						*/
						$imageWidth = $imageHeight = $imageMimeType = $fileSize = NULL;
						
					} elseif( is_array($imageDataArray) ) {						
						// Erfolgsfall (mime type is valid)
						
						$imageWidth 	= $imageDataArray[0];			// image width in px via getimagesize()
						$imageHeight 	= $imageDataArray[1];			// image height in px via getimagesize()
						$imageMimeType = $imageDataArray['mime'];		// image height in px via getimagesize()
						$fileSize		= filesize($fileTemp);			// file size in bytes via filesize()					
					}
if(DEBUG_F)		echo "<p class='debugImageUpload value'><b>Line " . __LINE__ . "</b>: \$imageWidth: $imageWidth px<i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)		echo "<p class='debugImageUpload value'><b>Line " . __LINE__ . "</b>: \$imageHeight: $imageHeight px<i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)		echo "<p class='debugImageUpload value'><b>Line " . __LINE__ . "</b>: \$imageMimeType: $imageMimeType <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_F)		echo "<p class='debugImageUpload value'><b>Line " . __LINE__ . "</b>: \$fileSize: " . round($fileSize/1024, 2) . " kB<i>(" . basename(__FILE__) . ")</i></p>\n";
					
					#**********************************************************************#
					
					
					#**************************************#
					#********** IMAGE VALIDATION **********#
					#**************************************#
					
					// Whitelist mit erlaubten MIME TYPES und Dateiendungen
					// $imageAllowedMimeTypes = array('image/jpg'=>'.jpg', 'image/jpeg'=>'.jpg', 'image/png'=>'.png', 'image/gif'=>'.gif');
					
					/*
						Da Schadcode häufig nur wenige Zeilen lang ist, ist eine zu kleine
						Dateigröße per se verdächtig. Brauchbare Bilddateien beginnen bei
						etwa 1kB Dateigröße (ca. 80-100Bytes für Icons).
						Außerdem wird gleich geprüft, ob ein Hacker womöglich den MIME Type
						im Dateiheader manipuliert hat. Bilder verfügen immer über eine Größenangabe
						in Pixeln, die vom Hacker manchmal vergessen wird, ebenfalls in den manipulierten
						Header einzufügen. Wenn die Bildgrößenangaben keinen Wert besitzen, muss von einem
						manipulierten Dateiheader ausgegangen werden.
						
						Sollte getimagesize() aufgrund eines falschen MIME Types false zurückgeliefert haben,
						sind alle Variablenwerte NULL und führen hier automatisch zum Fehlerfall
					*/
					#********** CHECK IF FILE HEADER IS PLAUSIBLE **********#
					if( $fileSize < $imageMinSize OR $imageWidth === NULL OR $imageHeight === NULL ) {
						// Fehlerfall 1: Potentiell verdächtiger Dateiheader
						$errorMessage = 'Potentielles Schadskript entdeckt!';
					
					
					#********** CHECK FOR VALID IMAGE/MIME TYPE **********#
					/*
						Der optionale 3. Parameter der in_array()-Funktion erzwingt einen strikten Wertevergleich, 
						damit '0' und '' nicht als gleich interpretiert werden.
						Er sollte aus Sicherheitsgründen immer gesetzt werden.
						in_array() liefert true zurück, wenn die Needle im Array gefunden wurde, ansonsten false
					*/
					} elseif( in_array($imageMimeType, array_keys($imageAllowedMimeTypes), true) === false ) {
						// Fehlerfall 2: Unerlaubter MIME TYPE
						$errorMessage = 'Dies ist kein erlaubter Bildtyp!';
						
					
					#********** VALIDATE IMAGE WIDTH **********#
					} elseif( $imageWidth > $imageMaxWidth ) {
						// Fehlerfall 3: Bildbreite zu groß
						$errorMessage = 'Die Bildbreite darf maximal ' . $imageMaxWidth . 'px betragen!';
						
						
					#********** VALIDATE IMAGE HEIGHT **********#
					} elseif( $imageHeight > $imageMaxHeight ) {
						// Fehlerfall 4: Bildhöhe zu groß
						$errorMessage = 'Die Bildhöhe darf maximal ' . $imageMaxHeight . 'px betragen!';
						
					
					#********** VALIDATE FILE SIZE **********#
					} elseif( $fileSize > $imageMaxSize ) {
						// Fehlerfall 5: Datei zu groß
						$errorMessage = 'Die Dateigröße darf maximal ' . $imageMaxSize/1024 . 'kB betragen!';
						
						
					#********** ALL CHECKS ARE PASSED **********#
					} else {
						// Erfolgsfall
						$errorMessage = NULL;
					}
					
					
					#**********************************************************************#
					
					
					#********** FINAL IMAGE VALIDATION **********#
					if( $errorMessage !== NULL ) {
						// Fehlerfall
if(DEBUG_F)			echo "<p class='debugImageUpload err'><b>Line " . __LINE__ . "</b>: $errorMessage <i>(" . basename(__FILE__) . ")</i></p>\n";				
						// Initialize $fileTarget
						$fileTarget = NULL;
						
					} else {
						// Erfolgsfall
if(DEBUG_F)			echo "<p class='debugImageUpload ok'><b>Line " . __LINE__ . "</b>: Die Bildvalidierung ergab keine Probleme. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						
						#**********************************************************#
						#********** PREPARE IMAGE FOR PERSISTANT STORING **********#
						#**********************************************************#
						
						/*
							Da der Dateiname selbst Schadcode in Form von ungültigen oder versteckten Zeichen,
							doppelte Dateiendungen (dateiname.exe.jpg) etc. beinhalten kann, darüberhinaus ohnehin 
							sämtliche, nicht in einer URL erlaubten Sonderzeichen und Umlaute entfernt werden müssten 
							sollte der Dateiname aus Sicherheitsgründen komplett neu generiert werden.
							
							Hierbei muss außerdem bedacht werden, dass die jeweils generierten Dateinamen unique
							sein müssen, damit die Dateien sich bei gleichem Dateinamen nicht gegenseitig überschreiben.
						*/
						
						#********** GENERATE UNIQUE FILE NAME **********#
						/*
							- 	mt_rand() stellt die verbesserte Version der Funktion rand() dar und generiert 
								Zufallszahlen mit einer gleichmäßigeren Verteilung über das Wertesprektrum. Ohne zusätzliche
								Parameter werden Zahlenwerte zwischen 0 und dem höchstmöglichem von mt_rand() verarbeitbaren 
								Zahlenwert erzeugt.							
							- 	str_shuffle mischt die Zeichen eines übergebenen Strings zufällig durcheinander.
							- 	microtime() liefert einen Timestamp mit Millionstel Sekunden zurück (z.B. '0.57914300 163433596'),
								aus dem für eine URL-konforme Darstellung der Dezimaltrenner und das Leerzeichen entfernt werden.
						*/
						$fileName = mt_rand() . '_' . str_shuffle('abcdefghijklmnopqrstuvwxyz_-0123456789') . '_' . str_replace( array('.', ' '), '', microtime());
						
						
						#********** GENERATE FILE EXTENSION **********#
						/*
							Aus Sicherheitsgründen wird nicht die ursprüngliche Dateinamenerweiterung aus dem
							Dateinamen verwendet, sondern eine vorgenerierte Dateiendung aus dem Array der 
							erlaubten MIME Types.
							Die Dateiendung wird anhand des ausgelesenen MIME Types [key] ausgewählt.
						*/
						$fileExtension = $imageAllowedMimeTypes[$imageMimeType];
						
						
						#********** GENERATE FILE TARGET **********#
						// Endgültigen Speicherpfad auf dem Server generieren
						// DestinationPath/FileName + FileExtension
						$fileTarget = $imageUploadPath . $fileName . $fileExtension;
// if(DEBUG_F)			echo "<p class='debugImageUpload value'><b>Line " . __LINE__ . "</b>: \$fileTarget: $fileTarget <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						
						#**********************************************************************#
						
						
						#*****************************************************#
						#********** MOVE IMAGE TO FINAL DESTINATION **********#
						#*****************************************************#
						
						/*
							move_uploaded_file() verschiebt eine hochgeladene Datei an einen 
							neuen Speicherort und benennt die Datei um
						*/
						if( move_uploaded_file($fileTemp, $fileTarget) === false ) {
							// Fehlerfall
if(DEBUG_F)				echo "<p class='debugImageUpload err'><b>Line " . __LINE__ . "</b>: FEHLER beim Verschieben der Datei nach <i>'$fileTarget'</i>! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							$errorMessage = 'Beim Hochladen des Bildes ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
							// Lösche FileTarget
							$fileTarget = NULL;
							
						} else {
							// Erfolgsfall
if(DEBUG_F)				echo "<p class='debugImageUpload ok'><b>Line " . __LINE__ . "</b>: Datei erfolgreich nach <i>'$fileTarget'</i> verschoben. <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
						} // MOVE IMAGE TO FINAL DESTINATION END
						
						
						#**********************************************************************#
						
						
					} // FINAL IMAGE VALIDATION END
					
					
					#**********************************************************************#
					
					
					#********** RETURN ARRAY CONTAINING EITHER IMAGE PATH OR ERROR MESSAGE **********#
					return array( 'imagePath' => $fileTarget, 'imageError' => $errorMessage );
					
				}


#**************************************************************************************#
?>























