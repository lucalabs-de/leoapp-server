<?php

    require_once('../../apiEndpoint.php');

    new AddVote();

    class AddVote extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = getConnection();

            $user = $db->real_escape_string($_POST['user']);
            $answer = $db->real_escape_string($_POST['answer']);

            exitOnBadRequest($user, $answer);

            $query = "INSERT INTO Result VALUES (null, $user, $answer)";
            $result = $db->query($query);

            if($result === false) {
                returnApiError("Internal Server Error", 500);
            }

            returnApiSuccess();

            $db->close();
        }

    }

?>