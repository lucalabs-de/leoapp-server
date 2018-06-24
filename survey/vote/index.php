<?php

    require_once('../../apiEndpoint.php');

    class AddVote extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $user = $db->real_escape_string($_POST['user']);
            $answer = $db->real_escape_string($_POST['answer']);

            parent::exitOnBadRequest($user, $answer);

            $query = "SELECT id FROM Result WHERE answer = $answer AND user = $user";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            if ($result->num_rows === 0) {

                $query = "SELECT s.multiple as m, s.owner as i FROM Survey s JOIN Answers a ON s.owner = a.survey WHERE a.id = $answer";
                $result = $db->query($query);

                if ($result === false) {
                    parent::returnApiError("Internal Server Error", 500);
                }

                if ($result->num_rows === 0) {
                    parent::returnApiError("no answer with id $answer", 404);
                }

                $array = $result->fetch_assoc();
                $surveyId = $array['i'];
                $multiple = $array['m'];

                $query = "SELECT a.id FROM Answers a JOIN Result r ON r.answer = a.id WHERE a.survey = $surveyId AND r.user = $user";
                $result = $db->query($query);

                if ($result === false) {
                    parent::returnApiError("Internal Server Error", 500);
                }

                if ($result->num_rows > 0) {
                    if ($multiple == 0) {
                        parent::returnApiError("trying to register second vote on single vote survey $surveyId", 400); //TODO optional replace with vote switch -> unlikely
                    }
                }

                $query = "INSERT INTO Result VALUES (null, $user, $answer)";
                $result = $db->query($query);

                if($result === false) {
                    parent::returnApiError("Internal Server Error", 500);
                }

            }

            parent::returnApiSuccess();

            $db->close();
        }

    }

    new AddVote();

?>