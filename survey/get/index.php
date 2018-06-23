<?php

    require_once('../../apiEndpoint.php');

    class GetSurveys extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $query = "SELECT title, description, recipient, multiple, owner, UNIX_TIMESTAMP(createdate) as createdate FROM Survey ORDER BY createdate DESC";
            $result = $db->query($query);

            $surveys = array();

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            while ($row = $result->fetch_assoc()) {

                $id = $row['owner'];
                $query = "SELECT content, id FROM Answers WHERE survey = $id ORDER BY id ASC";
                $answers = $db->query($query);

                $query = "SELECT uname FROM Users WHERE uid = $id";
                $res = $db->query($query);

                if($res === false) {
                    parent::returnApiError("Internal Server Error", 500);
                }

                $survey = array(
                    "id" => $id,
                    "author" => $res->fetch_assoc()['uname'],
                    "title" => $row['title'],
                    "description" => $row['description'],
                    "recipient" => $row['recipient'],
                    "multiple" => $row['multiple'],
                    "createdate" => $row['createdate']
                );

                $answers = array();
                while ($ansArray = $answers->fetch_assoc()) {
                    $answer = array(
                        "id" => $ansArray['id'],
                        "content" => $ansArray['content']
                    );
                    $answers[] = $answer;
                }

                $survey["answers"] = $answers;
                $surveys[] = $survey;
            }

            parent::returnApiResponse($surveys);

            $db->close();
        }

    }

    new GetSurveys();

?>