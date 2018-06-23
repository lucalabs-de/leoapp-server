<?php

    require_once('../../../apiEndpoint.php');

    new AddViewInstance();

    class AddViewInstance extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = getConnection();

            $id = $db->real_escape_string($_GET['id']);

            exitOnBadRequest($id);

            $query = "UPDATE Eintraege SET Gelesen = Gelesen + 1 WHERE EintragID = $id";
            
            if ($db->query($query) !== true) {
                returnApiError("Internal Server Error", 500);
            }

            returnApiSuccess();

            $db->close();
        }

    }

?>