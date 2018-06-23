<?php

    require_once('../../apiEndpoint.php');

    new AddEntry();

    class AddEntry extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = getConnection();

            $heute = date("Y-m-d H:i:s");
            $adressat = real_escape_string($_GET['to']);
            $titel = $db->real_escape_string($_GET['title']);
            $inhalt = $db->real_escape_string($_GET['content']);
            $ablaufdatum = $db->real_escape_string($_GET['date']);

            exitOnBadRequest($title, $inhalt, $ablaufdatum, $adressat);

            $query = "INSERT INTO Eintraege VALUES (null, 0, '".$adressat."', '".$titel."', '".$inhalt."', 'null', '".$heute."', '".$ablaufdatum."')";
            $result = $db->query($query);
            
            if ($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            returnApiSuccess();

            $db->close();
        }

    }

?>