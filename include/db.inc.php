<?php
#******************************************************************************************************#


				/**
				*
				*	Stellt eine Verbindung zu einer Datenbank mittels PDO her
				*	Die Konfiguration und Zugangsdaten erfolgen über eine externe Konfigurationsdatei
				*
				*	@param [String $dbname=DB_NAME]		Name der zu verbindenden Datenbank
				*
				*	@return Object								DB-Verbindungsobjekt
				*
				*/
				function dbConnect($dbname=DB_NAME) {
if(DEBUG_DB)	echo "<p class='debugDb'>📑 <b>Line " . __LINE__ . ":</b> Versuche mit der DB '<b>$dbname</b>' zu verbinden... <i>(" . basename(__FILE__) . ")</i></p>\r\n";					
					
					// EXCEPTION-HANDLING (Umgang mit Fehlern)
					// Versuche, eine DB-Verbindung aufzubauen
					try {
						// wirft, falls fehlgeschlagen, eine Fehlermeldung "in den leeren Raum"
						
						// $pdo = new PDO("mysql:host=localhost; dbname=market; charset=utf8mb4", "root", "");
						$pdo = new PDO(DB_SYSTEM . ":host=" . DB_HOST . "; dbname=$dbname; charset=utf8mb4", DB_USER, DB_PWD);
					
					// falls eine Fehlermeldung geworfen wurde, wird sie hier aufgefangen					
					} catch(PDOException $error) {
						// Ausgabe der Fehlermeldung
if(DEBUG_DB)		echo "<p class='error'><b>Line " . __LINE__ . ":</b> <i>FEHLER: " . $error->GetMessage() . " </i> <i>(" . basename(__FILE__) . ")</i></p>\r\n";
						// Skript abbrechen
						exit;
					}
					// Falls das Skript nicht abgebrochen wurde (kein Fehler), geht es hier weiter
if(DEBUG_DB)	echo "<p class='debugDb ok'><b>Line " . __LINE__ . ":</b> Erfolgreich mit der DB '<b>$dbname</b>' verbunden. <i>(" . basename(__FILE__) . ")</i></p>\r\n";

					// DB-Verbindungsobjekt zurückgeben
					return $pdo;
				}
				
				
#******************************************************************************************************#
?>