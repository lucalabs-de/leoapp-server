<?php

    require_once('../../apiEndpoint.php');

    class UpdateKlasse extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {

            $db = parent::getConnection();

            $uid = $db->real_escape_string($_POST['id']);
            $uklasse = $db->real_escape_string($_POST['grade']);

            parent::exitOnBadRequest($uid, $uklasse);

            if (preg_match("/(^[5-9]\$)|(^EF\$)|(^TEA\$)|(^Q[1-2]\$)/", $uklasse) !== 1) {
                parent::returnApiError("$uklasse is not a valid grade", 400);
            }

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