<?php

    require_once('../../apiEndpoint.php');

    class AddEntry extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $heute = date("Y-m-d H:i:s");
            $adressat = $db->real_escape_string($_POST['recipient']);
            $titel = $db->real_escape_string($_POST['title']);
            $inhalt = $db->real_escape_string($_POST['content']);
            $ablaufdatum = $db->real_escape_string($_POST['date']);

            parent::exitOnBadRequest($titel, $inhalt, $ablaufdatum, $adressat);

            if (preg_match("/(^[5-9]\$)|(^EF\$)|(^Q[1-2]\$)|(^Sek I{1,2}\$)|(^Alle\$)/", $adressat) == 0) {
                parent::returnApiError("$to is not a valid recipient", 400);    
            }

            $query = "INSERT INTO Eintraege VALUES (null, 0, '".$adressat."', '".$titel."', '".$inhalt."', 'null', '".$heute."', '".$ablaufdatum."')";
            $result = $db->query($query);
            
            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            parent::returnApiSuccess();

            $db->close();
        }

        protected function getPermissionLevel() {
            return PermissionLevel::ONLY_TEACHERS;
        }

    }

    new AddEntry();

?>