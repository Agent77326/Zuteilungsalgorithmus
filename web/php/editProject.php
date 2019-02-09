<?php
//authentication
if (isLogin() && ($_SESSION['benutzer']['typ'] == "teachers" && $config["Stage"] < 3 && $config["Stage"] > 0 || $_SESSION['benutzer']['typ'] == "admin")) {
	$error = false;
	if (empty($_POST["pName"]) ||
		empty($_POST["beschreibung"]) ||
		empty($_POST["betreuer"]) ||
		empty($_POST["minKlasse"]) ||
		empty($_POST["maxKlasse"]) ||
		empty($_POST["minPlatz"]) ||
		empty($_POST["maxPlatz"])) {
		alert("Fehlende Angaben");
		$error = true;
	}
	foreach ($_POST as $post) {
		if (strpos($post, CONFIG["dbLineSeperator"]) !== false || strpos($post, CONFIG["dbElementSeperator"]) !== false) {
			alert("Ungültige Zeichenkette: " . CONFIG["dbLineSeperator"] . " oder " . CONFIG["dbElementSeperator"] . " Bitte benutzen sie reguläre Zeichenketten!");
			$error = true;
		}
	}

	if ($_POST["minKlasse"] > $_POST["maxKlasse"]) {
		alert("Ungültige Angabe: die Mindeststufe ist größer wie die maximale Jahrgangsstufe");
		$error = true;
	}
	if ($_POST["minKlasse"] < 5) {
		alert("Ungültige Angabe: die Mindeststufe ist kleiner als der kleinstmögliche Jahrgangsstufe");
		$error = true;
	}
	if ($_POST["maxKlasse"] > 12) {
		alert("Ungültige Angabe: die Maximalstufe ist größer als die größtmögliche Jahrgangsstufe");
		$error = true;
	}
	if ($_POST["minPlatz"] > $_POST["maxPlatz"]) {
		alert("Ungültige Angabe: die Mindestanzahl an benötigten Teilnehmern ist größer wie die Maximale");
		$error = true;
	}

	if (empty($_GET['projekt']) || empty(getProjektInfo($_GET['projekt']))) {
    alert("Der zu editierende Eintrag konnte nicht gefunden werden");
    $error = true;
  }

	if (!$error) {
		dbSetRow("../data/projekte.csv", "id", $_GET['projekt'], [
				$_GET['projekt'],
				$_POST["pName"],
				newlineRemove($_POST["beschreibung"]),
				$_POST["betreuer"],
				$_POST["minKlasse"],
				$_POST["maxKlasse"],
				$_POST["minPlatz"],
				$_POST["maxPlatz"],
				$_POST["sonstiges"],
				$_POST["vorraussetzungen"],
				$_POST["raum"],
				$_POST["material"],
				newlineRemove($_POST["moVor"]),
				checkBox("moMensa"),
				newlineRemove($_POST["moNach"]),
				newlineRemove($_POST["diVor"]),
				checkBox("diMensa"),
				newlineRemove($_POST["diNach"]),
				newlineRemove($_POST["miVor"]),
				checkBox("miMensa"),
				newlineRemove($_POST["miNach"]),
				newlineRemove($_POST["doVor"]),
				checkBox("doMensa"),
				newlineRemove($_POST["doNach"]),
				newlineRemove($_POST["frVor"]),
				checkBox("frMensa"),
				newlineRemove($_POST["frNach"])
		]);
		?>
		<div class="modal fade" role="dialog" id="successModal">
	    <div class="modal-dialog" role="document">

	      <!-- Modal content-->
	      <div class="modal-content bg-dark">
	        <div class="modal-header">
	          <h4>Erfolgreich aktualisiert</h4>
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	        </div>

	        <div class="modal-body">
	          <p>
							Das Projekt <kbd><?php echo $_POST["pName"]; ?></kbd>
							mit dem Betreuer <kbd><?php echo $_POST["betreuer"]; ?></kbd>
							für <kbd><?php echo $_POST["minPlatz"]; ?></kbd>
							bis <kbd><?php echo $_POST["maxPlatz"]; ?></kbd>
							Schüler der Klassenstufe <kbd><?php echo $_POST["minPlatz"]; ?></kbd>
							bis <kbd><?php echo $_POST["maxPlatz"]; ?></kbd> wurde aktualisiert.
							Die Daten sind nun auf dem Server gespeichert.
						</p>
	        </div>

	        <div class="modal-footer">
	          <button type="button" class="btn btn-primary btn-default" onclick="window.location.href = '/';">Zurück zur Übersicht</button>
	          <button type="submit" class="btn btn-success btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>OK</button>
	        </div>
	      </div>

	    </div>
	  </div>
		<script>
			$("#successModal").modal("show");
		</script>
		<?php
	}
}
else die("Unzureichende Zugriffberechtigung");
?>