<?php

    require_once('../../apiEndpoint.php');

    class AddViewInstance extends ApiEndpoint {

        protected function getMethod() {
            return "DELETE"; //TODO mod_rewrite
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $id = $db->real_escape_string($_GET['p0']);

            parent::exitOnBadRequest($id);

            $sql = "DELETE FROM Eintraege WHERE EintragID = $id";

            $result = $db->query($sql);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            parent::returnApiSuccess();
        }

        protected function getPermissionLevel() {
            return PermissionLevel::ONLY_TEACHERS;
        }

    }

    new AddViewInstance();

?>