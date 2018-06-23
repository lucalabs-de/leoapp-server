<?php

    require_once('../../apiEndpoint.php');

    new AddViewInstance();

    class AddViewInstance extends ApiEndpoint {

        protected function getMethod() {
            return "DELETE"; //TODO mod_rewrite
        }

        protected function handleRequest() {
            $db = getConnection();

            $id = $db->real_escape_string($_GET['id']);

            exitOnBadRequest($id);

            $sql = "DELETE FROM Eintraege WHERE EintragID=".$id;

            $result = $db->query($sql);

            if ($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            returnApiSuccess();
        }

    }

?>