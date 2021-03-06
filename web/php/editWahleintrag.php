<?php
if (isLogin() && $_SESSION["benutzer"]["typ"] == "admin") {
  if (!empty($_POST["uid"]) && !empty($_POST["stufe"])
    && !empty($_POST["klasse"]) && !empty($_POST["vorname"])
    && !empty($_POST["nachname"]) && isset($_POST["ergebnis"])) { //ergebnis kann empty sein
    if ($_POST["stufe"] < CONFIG["minStufe"] || $_POST["stufe"] > CONFIG["maxStufe"]) {
      alert("Der Schüler " . $_POST["vorname"] . " " . $_POST["nachname"] . " aus der Klasse " . $_POST["klasse"] . " hat eine ungültige Stufe " . $_POST["stufe"]);
    }
    else {
      $gewaehlt = "";
      foreach ($wahlen as $wahl) {
        if ($wahl["uid"] == $_POST["uid"]) {
          $gewaehlt = implode("§", $wahl["wahl"]);
          break;
        }
      }
      $zugeteilt = false;
      foreach ($zwangszuteilung as $zuteilung) {
        if ($zuteilung["uid"] == $_POST["uid"]) {
          $zugeteilt = true;
          break;
        }
      }
      if (!empty($gewaehlt) || $zugeteilt) {
        if ($zugeteilt) {
          $data = [
            $_POST["uid"],
            $_POST["stufe"],
            $_POST["klasse"],
            $_POST["vorname"],
            $_POST["nachname"],
            $_POST["ergebnis"]
          ];
        }
        else {
          $data = [
            $_POST["uid"],
            $_POST["vorname"],
            $_POST["nachname"],
            $_POST["stufe"],
            $_POST["klasse"],
            $gewaehlt,
            $_POST["ergebnis"]
          ];
        }
        if ((dbSetRow("../data/" . ($zugeteilt ? "zwangszuteilung.csv" : "wahl.csv"), "uid", $_POST["uid"], $data)) === false) {
          alert("Die Daten konnten nicht gespeichert werden: '" . json_encode($data) . "'");
        }
        else {
          alert("Änderung erfolgreich gespeichert");
        }
      }
      else {
        alert("Der Schüler mit der ID " . $_POST["studentID"] . " konnte nicht gefunden werden und wurde dementsprechend nicht gelöscht werden");
      }
    }
  }
  else {
    alert("Ungültige Angaben: Es fehlend Angaben");
  }
}
?>
