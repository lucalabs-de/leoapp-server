<?php

    require_once('../../apiEndpoint.php');

    class RemoveDevice extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $userid = $db->real_escape_string($_POST['user_id']);
            $device = $db->real_escape_string($_POST['device']);

            parent::exitOnBadRequest($userid, $device);

            $query = "DELETE FROM Devices WHERE device ='$device' AND user = $userid";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            parent::returnApiSuccess();

            $db->close();
        }

        protected function getPermissionLevel() {
            return PermissionLevel::ONLY_AUTHENTICATION;
        }

    }

    new RemoveDevice();

?>