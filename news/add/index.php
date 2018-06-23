<?php

    require_once('../../apiEndpoint.php');

    class AddEntry extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $heute = date("Y-m-d H:i:s");
            $adressat = real_escape_string($_GET['to']);
            $titel = $db->real_escape_string($_GET['title']);
            $inhalt = $db->real_escape_string($_GET['content']);
            $ablaufdatum = $db->real_escape_string($_GET['date']);

            parent::exitOnBadRequest($title, $inhalt, $ablaufdatum, $adressat);

            $query = "INSERT INTO Eintraege VALUES (null, 0, '".$adressat."', '".$titel."', '".$inhalt."', 'null', '".$heute."', '".$ablaufdatum."')";
            $result = $db->query($query);
            
            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            parent::returnApiSuccess();

            $db->close();
        }

    }

    new AddEntry();

?>