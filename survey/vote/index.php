<?php

    require_once('../../apiEndpoint.php');

    new AddVote();

    class AddVote extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $user = $db->real_escape_string($_POST['user']);
            $answer = $db->real_escape_string($_POST['answer']);

            parent::exitOnBadRequest($user, $answer);

            $query = "INSERT INTO Result VALUES (null, $user, $answer)";
            $result = $db->query($query);

            if($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            parent::returnApiSuccess();

            $db->close();
        }

    }

?>