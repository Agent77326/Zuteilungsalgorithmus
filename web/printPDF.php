<?php
	if (!file_exists("../LICENSE") || !file_exists("../VERSION")) {
		die("</head><body style='color: #000'>Bei diesem Produkt handelt es sich möglicherweise um eine illegale Kopie. Bitte beziehen Sie dieses Produkt nur von der offiziellen Github-Seite unter <a href='https://github.com/Agent77326/Zuteilungsalgorithmus'>https://github.com/Agent77326/Zuteilungsalgorithmus</a></body></html>");
	}
	session_start();
  (include "../data/config.php") OR die("</head><body style='color: #000'>Der Webserver wurde noch nicht konfiguriert, kontaktiere einen Admin damit dieser setup.sh ausführt.</body></html>");
	require "php/db.php";
	require "php/utils.php";
	require "php/setup.php";
  require "php/TCPDF-6.2.26/tcpdf.php";

  if (!isLogin() || $_SESSION["benutzer"]["typ"] != "admin" && $_SESSION["benutzer"]["typ"] != "teachers") {
    die("Zugriff verweigert");
  }

  class printPDF extends TCPDF {

		function printHeader($config) {
			// Logo-Bild-Größe anpassen, die Parameter sind: (pfad ,x ,y ,size)
			$pos = JSON_decode(file_get_contents("pictures/logo-position.json"), true);
			$this->Image("pictures/logo.jpg", $pos["x"], $pos["y"], $pos["size"]);
			$this->SetFont("freeserif", "B", 24);
			$this->Cell(30);
			$this->Cell(66, 10, ($config["wahlTyp"] == "ag" ? "AGs " : "Projektwoche ") . date("Y"));
			$this->ln(13);
		}


    // Breite: 297mm - 2cm margin => 277mm zum Arbeiten
    function printProjekt($config, $projekt) {
      $this->AddPage("L", "A4");
			$this->printHeader($config);
      $this->setCellHeightRatio(1.1);

      // Zeile 1
      $this->SetFont("freeserif", "B", 14);
      $this->Cell(180, 0, ($config["wahlTyp"] == "ag" ? "AG-T" : "Projektt") . "itel:", "LTR");
      $this->Cell(97, 0, "Betreuer:", "TR");
      $this->SetFont("freeserif", "", 10);
      $this->ln();
      $this->Cell(180, 0, $projekt["name"], "LBR");
      $this->Cell(97, 0, $projekt["betreuer"], "BR");
      $this->ln(6);

      // Zeile 2
      $this->SetFont("freeserif", "B", 14);
      $this->Cell(22, 0, "Stufen:", "LTR");
      $this->Cell(52, 0, "Teilnehmeranzahl:", "TR");
      $this->Cell(203, 0, "Kosten/Sonstiges:", "TR");
      $this->SetFont("freeserif", "", 10);
      $this->ln();
      $this->Cell(22, 0, $projekt["minKlasse"] . " - " . $projekt["maxKlasse"], "LBR");
      $this->Cell(52, 0, $projekt["minPlatz"] . " - " . $projekt["maxPlatz"], "BR");
      $this->Cell(203, 0, $projekt["sonstiges"], "BR");
      $this->ln(6);

      // Zeile 3
      $this->SetFont("freeserif", "B", 14);
      $this->Cell(277, 0, "Vorraussetzungen:", "LTR");
      $this->SetFont("freeserif", "", 10);
      $this->ln();
      $this->Cell(277, 0, $projekt["vorraussetzungen"], "LBR");
      $this->ln(6);

      // Zeile 4
      $this->SetFont("freeserif", "B", 14);
      $this->Cell(0, 0, "Beschreibung:", "LTR");
      $this->SetFont("freeserif", "", 10);
      $this->ln();
      $y = $this->getY();
      //$this->Write(5, newlineBack($projekt["beschreibung"]), "", 0, "L", false, 0, false, false, 0);
      //$this->MultiCell(0, 5, newlineBack($projekt["beschreibung"]), "LBR", "L");
      $this->writeHTMLCell(0, 0, 10, $y, $projekt["beschreibung"]);
      $this->Line(10, $y, 10, $y + 80);
      $this->Line(10, $y + 80, 287, $y + 80);
      $this->Line(287, $y, 287, $y + 80);
      $this->ln(82);

			if ($config["wahlTyp"] == "projektwoche") {
	      // Zeile 5
	      $this->SetFont("freeserif", "B", 14);
	      $this->Cell(277, 0, "Vorraussichtlicher Ablauf", 0, 0, "C");
	      $this->ln(6);

	      // Tabelle
	      // Zeile 6
	      $this->SetFont("freeserif", "B", 14);
	      $this->Cell(27, 0, "Tag", "LTRB");
	      $this->Cell(50, 0, "Montag", "TRB");
	      $this->Cell(50, 0, "Dienstag", "TRB");
	      $this->Cell(50, 0, "Mittwoch", "TRB");
	      $this->Cell(50, 0, "Donnerstag", "TRB");
	      $this->Cell(50, 0, "Freitag", "TRB");
	      $this->ln();

	      //Zeile 7 Vormittag
	      $y = $this->getY();
	      $h = 20;
	      $this->SetFont("freeserif", "B", 10);
	      $this->Cell(27, 0, "Vormittag");
	      $this->SetFont("freeserif", "", 10);
	      $this->writeHTMLCell(50, 0, 37, $y, $projekt["moVor"]);
	      $this->writeHTMLCell(50, 0, 87, $y, $projekt["diVor"]);
	      $this->writeHTMLCell(50, 0, 137, $y, $projekt["miVor"]);
	      $this->writeHTMLCell(50, 0, 187, $y, $projekt["doVor"]);
	      $this->writeHTMLCell(50, 0, 237, $y, $projekt["frVor"]);
				// Draw Borders
				$this->Rect(10, $y, 27, $h);
				$this->Rect(37, $y, 50, $h);
				$this->Rect(87, $y, 50, $h);
				$this->Rect(137, $y, 50, $h);
				$this->Rect(187, $y, 50, $h);
				$this->Rect(237, $y, 50, $h);
	      $this->ln($h);
	      //Zeile 7 Mensa
	      $y = $this->getY();
	      $h = 5;
	      $this->SetFont("freeserif", "B", 10);
	      $this->Cell(27, 0, "Mensa");
	      $this->SetFont("freeserif", "", 10);
	      $this->writeHTMLCell(50, 0, 37, $y, $projekt["moMensa"] == "true" ? "Ja" : "Nein");
	      $this->writeHTMLCell(50, 0, 87, $y, $projekt["diMensa"] == "true" ? "Ja" : "Nein");
	      $this->writeHTMLCell(50, 0, 137, $y, $projekt["miMensa"] == "true" ? "Ja" : "Nein");
	      $this->writeHTMLCell(50, 0, 187, $y, $projekt["doMensa"] == "true" ? "Ja" : "Nein");
	      $this->writeHTMLCell(50, 0, 237, $y, $projekt["frMensa"] == "true" ? "Ja" : "Nein");
				// Draw Borders
				$this->Rect(10, $y, 27, $h);
				$this->Rect(37, $y, 50, $h);
				$this->Rect(87, $y, 50, $h);
				$this->Rect(137, $y, 50, $h);
				$this->Rect(187, $y, 50, $h);
				$this->Rect(237, $y, 50, $h);
	      $this->ln($h);
	      //Zeile 7 Nachmittag
	      $y = $this->getY();
	      $h = 20;
	      $this->SetFont("freeserif", "B", 10);
	      $this->Cell(27, 0, "Nachmittag");
	      $this->SetFont("freeserif", "", 10);
	      $this->writeHTMLCell(50, 0, 37, $y, $projekt["moNach"]);
	      $this->writeHTMLCell(50, 0, 87, $y, $projekt["diNach"]);
	      $this->writeHTMLCell(50, 0, 137, $y, $projekt["miNach"]);
	      $this->writeHTMLCell(50, 0, 187, $y, $projekt["doNach"]);
	      $this->writeHTMLCell(50, 0, 237, $y, $projekt["frNach"]);
				// Draw Borders
				$this->Rect(10, $y, 27, $h);
				$this->Rect(37, $y, 50, $h);
				$this->Rect(87, $y, 50, $h);
				$this->Rect(137, $y, 50, $h);
				$this->Rect(187, $y, 50, $h);
				$this->Rect(237, $y, 50, $h);
	      $this->ln($h);
			}
			elseif ($config["wahlTyp"] == "ag") {
				$this->Cell(27, 0, "To be implemented");
			}
			else {
				die("Unknown Wahl-Typ");
			}
    }

		function printKlasse($config, $klasse, $studentlist, $projekte, $zwangszuteilung, $klassenliste = []) {
      $this->AddPage("P", "A4");
			$this->printHeader($config);
      $this->setCellHeightRatio(1.1);

      // Zeile 1
      $this->SetFont("freeserif", "B", 14);
      $this->Cell(0, 0, empty($klassenliste) ? $klasse : "Klasse " . $klasse);
      $this->ln(6);

			// Aufbereiten des Headers
			if (!empty($klassenliste)) {
				$header = [
					"Klasse",
					"Nachname",
					"Vorname",
					$_SESSION["benutzer"]["typ"] == "admin" ? "Ergebnis" : "Bereits gewählt"
				];
				$widths = [
					12, 39, 39, 100
				];
			}
			else {
				$header = [
					"Klasse",
					"Nachname",
					"Vorname"
				];
				$widths = [
					12, 89, 89
				];
			}
			// Aufbereiten der Daten für die Schülertabelle
			$dataToPrint = [];
			$dummyWertVorhanden = 0;
			foreach ($studentlist as $student) {
				// dummy-Wert
				if (empty($student["nachname"])) {
					$dummyWertVorhanden += 1;
					continue;
				}

				$zugeteilt = false;
				foreach ($zwangszuteilung as $key => $zuteilung) {
					if ($student["uid"] == $zuteilung["uid"]) {
						$zugeteilt = true;
						break;
					}
				}
				if (!empty($klassenliste)) {
					$string = "";
					if ($_SESSION["benutzer"]["typ"] == "admin") {
						$string = empty($student["projekt"]) ? ($config["Stage"] > 4 ? "Konnte nicht zugeteilt werden" : "N/A") : getProjektInfo($projekte, $student["projekt"])["name"];
					}
					else {
						$keineWahl = empty(dbSearch("../data/wahl.csv", "uid", $student["uid"])[0]["wahl"]);
						$string = $zugeteilt ? "Zugeteilt" : ($keineWahl ? "Nein" : "Ja");
					}
					array_push($dataToPrint, [
						$student["klasse"],
						$student["nachname"],
						$student["vorname"],
						$string
					]);
				}
				else {
					array_push($dataToPrint, [
						$student["klasse"],
						$student["nachname"],
						$student["vorname"]
					]);
				}
			}
			// Tabelle
			$this->ColoredTable($header, $dataToPrint, $widths);
			if (!empty($klassenliste)) {
				$anzahl = 0;
				foreach ($klassenliste as $listung) {
					if ($listung["klasse"] == $klasse) {
						$anzahl = $listung["anzahl"];
						break;
					}
				}
				if ($config["Stage"] > 4) {
					$ohneZuteilung = 0;
					foreach ($studentlist as $student) {
						if (empty($student["nachname"])) {
							continue;
						}
						if (empty($student["projekt"])) {
							$ohneZuteilung += 1;
						}
					}
					$this->Cell(0, 6, count($studentlist) - $dummyWertVorhanden - $ohneZuteilung. " / " . $anzahl . " Schüler wurden zugeteilt");
				}
				else {
					$this->Cell(0, 6, count($studentlist) - $dummyWertVorhanden . " / " . $anzahl . " Schüler-Einträgen gefunden");
				}
			}
			else {
				$this->Cell(0, 6, count($studentlist) - $dummyWertVorhanden . " Teilnehmer");
			}
		}

		// Colored table
    public function ColoredTable($header, $data, $widths) {
      // Colors, line width and bold font
      $this->SetFillColor(9, 140, 56);
      $this->SetTextColor(0);
      $this->SetDrawColor(0, 0, 0);
      $this->SetFont("freeserif", "B", 8);
      // Header
      //$w = array(40, 35, 40, 45);
      for ($i = 0; $i < count($header); ++$i) {
        $this->Cell($widths[$i], 7, $header[$i], 1, 0, "C", 1);
      }
      $this->Ln();
      // Color and font restoration
      $this->SetFillColor(224, 235, 255);
      $this->SetTextColor(0);
      $this->SetFont("freeserif", "", 8);
      // Data
      $fill = 0;
      foreach($data as $row) {
				for ($i = 0; $i < count($row); $i++) {
					if ($row[$i] == "Konnte nicht zugeteilt werden") {
						$this->SetTextColor(220, 53, 69);
					}
          $this->Cell($widths[$i], 6, $row[$i], "LR", 0, "L", $fill);
					$this->SetTextColor(0);
				}
        $this->Ln();
        $fill=!$fill;
      }
      $this->Cell(array_sum($widths), 0, "", "T");
			$this->ln();
    }
  }





  // Anfrage verarbeiten
	// Projekt(e) drucken
  if (!empty($_GET["print"]) && !empty($_GET["projekt"]) && $_GET["print"] == "projekt") {
	  $pdf = new printPDF("L", "mm", "A4");
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor("Lise-Meitner-Gymnasium Maxdorf G8GTS");

    if ($_GET["projekt"] == "all") {
			$pdf->SetTitle(($config["wahlTyp"] == "ag" ? "AGs" : "Projektwoche") . " " . date("Y") . " - " . ($config["wahlTyp"] == "ag" ? "AG-L" : "Projektl") . "iste");
			$pdf->SetSubject(($config["wahlTyp"] == "ag" ? "AG-L" : "Projektl") . "iste");
      foreach ($projekte as $projekt) {
        $pdf->printProjekt($config, $projekt);
				if (!empty($projekt["teilnehmer"]) && $_SESSION["benutzer"]["typ"] == "admin") {
					$pdf->printKlasse($config, "Teilnehmerliste " . $projekt["name"], $projekt["teilnehmer"], $projekte, $zwangszuteilung);
				}
      }
    }
    else {
      $projekt = getProjektInfo($projekte, $_GET["projekt"]);
			$pdf->SetTitle(($config["wahlTyp"] == "ag" ? "AGs" : "Projektwoche") . " " .  date("Y") . " - " . ($config["wahlTyp"] == "ag" ? "AG" : "Projekt") . " " . $projekt["name"]);
			$pdf->SetSubject("Projekt " . $projekt["name"]);
      $pdf->printProjekt($config, $projekt);
			if (!empty($projekt["teilnehmer"]) && $_SESSION["benutzer"]["typ"] == "admin") {
				$pdf->printKlasse($config, "Teilnehmerliste " . $projekt["name"], $projekt["teilnehmer"], $projekte, $zwangszuteilung);
			}
    }
  }
	// Schülerlisten drucken
  elseif (!empty($_GET["print"]) && !empty($_GET["klasse"]) && $_GET["print"] == "students") {
	  $pdf = new printPDF("P", "mm", "A4");
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor("Lise-Meitner-Gymnasium Maxdorf G8GTS");

    if ($_GET["klasse"] == "all") {
			$pdf->SetTitle(($config["wahlTyp"] == "ag" ? "AGs" : "Projektwoche") . " " . date("Y"));
			$pdf->SetSubject("Schülerlisten der Schule");
			foreach ($klassen as $key => $klasse) {
        $pdf->printKlasse($config, $key, $klasse, $projekte, $zwangszuteilung, $klassenliste);
      }
    }
    else {
			$pdf->SetTitle(($config["wahlTyp"] == "ag" ? "AGs" : "Projektwoche") . " " . date("Y") . " - Klasse " . $_GET["klasse"]);
			$pdf->SetSubject("Schülerliste der Klasse " . $_GET["klasse"]);
			if (empty($klassen[$_GET["klasse"]])) {
				error_log("Klasse '" . $_GET["klasse"] . "' konnte nicht gefunden werden.", 0, "../data/error.log");
			}
      $pdf->printKlasse($config, $_GET["klasse"], $klassen[$_GET["klasse"]], $projekte, $zwangszuteilung, $klassenliste);
    }
  }
  else {
    die("Ungültige Anfrage");
  }
  $pdf->Output();
?>
