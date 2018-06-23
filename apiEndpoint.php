<?php

require_once('dbconfig.php');

abstract class ApiEndpoint {

    function __construct() {

        header('Content-Type: application/json');

        if (strcmp(strtoupper($this->getMethod()), $_SERVER['REQUEST_METHOD']) !== 0) {
            $this->returnApiError("Method Not Allowed", 405); ///TODO add Allowed
        } 

        if (!$this->isAuthorized()) {
            $this->returnApiError("Not Authorized", 401); ///TODO add WWW-Authenticate
        }

        $this->handleRequest();
    }

    //Must return the desired HTTP Method as a string
    abstract protected function getMethod();
    abstract protected function handleRequest();

    protected function getConnection() {
        $db = new mysqli(dbhost, dbuser, dbpass, dbname);

        if ($db->connect_error) {
            $this->returnApiError("Internal Server Error", 500);
        }

        return $db;
    }

    protected function isAuthorized() {
        //TODO implement
        return true;
    }

    protected function exitOnBadRequest(...$params) {
        foreach ($params as $p) {
            if (!isset($param)) {
                $this->returnApiError("Bad Request", 400);
            }
        }
    }

    protected function returnApiError($message, $statuscode) {
        http_response_code($statuscode);

        $json = array(
            "success" => false,
            "error" => array(
                "code" => $statuscode,
                "message" => $message
            )
        );

        echo json_encode($json, JSON_PRETTY_PRINT);
        exit;
    }

    protected function returnApiResponse($response) {
        http_response_code(200);
        $json = array(
            "success" => true,
            "data" => $response
        );
        echo json_encode($json, JSON_PRETTY_PRINT);
    }

    protected function returnApiSuccess() {
        http_response_code(200);
        $json = array(
            "success" => true
        );

    }

}

?>