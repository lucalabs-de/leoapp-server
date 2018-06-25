<?php

    require_once('../../apiEndpoint.php');

    class GetVotes extends ApiEndpoint {

        protected function getMethod() {
            return "GET";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $survey = $db->real_escape_string($_GET['p0']);

            parent::exitOnBadRequest($survey);

            $query = "SELECT recipient FROM Survey WHERE owner = $survey";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            $to = $result->fetch_assoc()['recipient'];

            $query = "SELECT COUNT(r.user) as count, a.content as ans FROM Result r RIGHT JOIN Answers a ON a.id = r.answer WHERE a.survey = $survey GROUP BY ans ORDER BY count DESC";
            $result = $db->query($query);

            switch ($to) {
                case '5':
                case '6':
                case '7':
                case '8':
                case '9':
                case 'EF':
                case 'Q1':
                case 'Q2':
                $query = "SELECT COUNT(*) as c FROM Users WHERE uklasse = '$to'";
                break;
                case 'Sek II':
                $query = "SELECT COUNT(*) as c FROM Users WHERE uklasse = 'Q1' OR uklasse = 'Q2' OR uklasse = 'EF'";
                break;
                case 'Sek I':
                $query = "SELECT COUNT(*) as c FROM Users WHERE uklasse <> 'Q1' AND uklasse <> 'Q2' AND uklasse <> 'EF'";
                default:
                $query = "SELECT COUNT(*) as c FROM Users";
                break;
            }

            $result2 = $db->query($query);

            if($result === false || $result2 === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            $json = array();
            $answers = array();

            while($row = $result->fetch_assoc()) {
                $answers[] = array(
                    "id" => $row['ans'],
                    "votes" => $row['count']
                );
            }

            $query = "SELECT title as t FROM Survey WHERE owner=".$survey;
            $result3 = $db->query($query);
            $array = $result3->fetch_assoc();

            $json["title"] = $array['t'];

            $query = "SELECT COUNT(DISTINCT r.user) as count FROM Answers a JOIN Survey s ON s.owner = a.survey JOIN Result r ON r.answer = a.id WHERE a.id IN (SELECT id FROM Answers WHERE survey = $survey)";
            $result4 = $db->query($query);
            $array = $result4->fetch_assoc();

            $json["audience_size"] = $result2->fetch_assoc()['c'];
            $json["registered_votes"] = $array['count'];
            $json["answers"] = $answers;

            parent::returnApiResponse($json);

            $db->close();
        }

    }

    new GetVotes();

?>