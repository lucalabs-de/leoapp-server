<?php

    require_once('../../apiEndpoint.php');

    class AddSurvey extends ApiEndpoint {

        protected function getMethod() {
            return "POST";
        }

        protected function handleRequest() {
            $db = parent::getConnection();

            $id = $db->real_escape_string($_POST['id']);
            $to = $db->real_escape_string($_POST['recipient']);
            $title = $db->real_escape_string($_POST['title']);
            $description = $db->real_escape_string($_POST['desc']);
            $multiple = $db->real_escape_string($_POST['mult']);
            $answers = $_POST['answers'];

            parent::exitOnBadRequest($id, $to, $title, $description);

            if (sizeOf($answers) === 0) {
                parent::returnApiError("Bad Request", 400);
            }

            if (empty($multiple) || strlen($multiple) === 0) {
                $multiple = 0;
            }

            if (preg_match("/(^[5-9]\$)|(^EF\$)|(^Q[1-2]\$)|(^Sek I{1,2}\$)|(^Alle\$)/", $to) == 0) {
                parent::returnApiError("$to is not a valid recipient", 400);    
            }

            //REMOVE EXISTING SURVEY

            $query = "SELECT uid FROM Users WHERE uid = $id";
            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            if ($result->num_rows == 0) {
                parent::returnApiError("user $id does not exist", 404);
            }

            $query = "DELETE FROM Survey WHERE owner = $id";
            $db->query($query);

            $query = "DELETE FROM Answers WHERE survey = $id";
            $db->query($query);

            $query = "INSERT INTO Survey VALUES ($id, '$title', '$description', '$to', '$multiple', '".date("Y-m-d H:i:s")."')";

            $result = $db->query($query);

            if ($result === false) {
                parent::returnApiError("Internal Server Error", 500);
            }

            //INSERT ANSWERS

            foreach ($answers as $answer) {
                $query = "INSERT INTO Answers VALUES (null, ".$id.", '".$answer."')";
                $result = $db->query($query);
                if ($result === false) {
                    parent::returnApiError("Internal Server Error", 500);
                }
            }

            parent::returnApiSuccess();

            $db->close();
        }

    }

    new AddSurvey();

?>