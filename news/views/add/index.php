<?php

    require_once('../../../apiEndpoint.php');

    class AddViewInstance extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $id = $db->real_escape_string($_POST['id']);

            parent::exitOnBadRequest($id);

            $query = "UPDATE Eintraege SET Gelesen = Gelesen + 1 WHERE EintragID = $id";
            
            if ($db->query($query) !== true) {
                parent::returnApiError("Internal Server Error", 500);
            }

            parent::returnApiSuccess();

            $db->close();
        }

        protected function getPermissionLevel() {
            return PermissionLevel::ONLY_AUTHENTICATION;
        }

    }

    new AddViewInstance();

?>