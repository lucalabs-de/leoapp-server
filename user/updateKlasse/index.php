<?php

    require_once('../../apiEndpoint.php');

    class UpdateKlasse extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {

            $db = parent::getConnection();

            $uid = $db->real_escape_string($_POST['uid']);
            $uklasse = $db->real_escape_string($_POST['uklasse']);

            parent::exitOnBadRequest($uid, $uklasse);

            $query = "UPDATE Users SET uklasse = '$uklasse' WHERE uid = $uid";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            parent::returnApiSuccess();

            $db->close();
        }

    }

    new UpdateKlasse();

?>