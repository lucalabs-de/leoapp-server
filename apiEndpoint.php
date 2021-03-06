<?php

require_once('dbconfig.php');
require_once('secure.php');
require_once('permissionLevels.php');

abstract class ApiEndpoint {

    function __construct() {

        header('Content-Type: application/json');

        $method = $_SERVER['REQUEST_METHOD'];

        if (strcmp(strtoupper($this->getMethod()), $method) !== 0) {
            header("Allow: ".$this->getMethod());
            $this->returnApiError("Method Not Allowed", 405);
        } 

        if (!$this->isAuthorized()) {
            $this->returnApiError("Not Authorized", 401);
        }

        if (strcmp($method, "POST") === 0) {
            $_POST = json_decode(file_get_contents('php://input'), true);
            if ($_POST == NULL) {
                $this->returnApiError("Bad Request", 400);
            }
        }
        $this->handleRequest();
    }

    //Must return the desired HTTP Method as a string
    abstract protected function getMethod();
    abstract protected function handleRequest();
    abstract protected function getPermissionLevel();

    protected function getConnection() {
        $db = new mysqli(dbhost, dbuser, dbpass, dbname);

        if ($db->connect_error) {
            $this->returnApiError("Internal Server Error", 500);
        }

        return $db;
    }

    protected function isAuthorized() {

        switch ($this->getPermissionLevel()) {
            case PermissionLevel::NONE:
                return true;
            case PermissionLevel::ONLY_AUTHENTICATION:
                return isSecure($_SERVER['HTTP_AUTHENTICATION'], $_SERVER['HTTP_DEVICE'], $this->getConnection());
            case PermissionLevel::ONLY_TEACHERS:
                $checksum = $_SERVER['HTTP_AUTHENTICATION'];
                $device = $_SERVER['HTTP_DEVICE'];
                return isSecure($checksum, $device, $this->getConnection()) && $this->getUserPermissionLevel($checksum) >= PermissionLevel::ONLY_TEACHERS;
            case PermissionLevel::ONLY_ADMINS:
                $checksum = $_SERVER['HTTP_AUTHENTICATION'];
                $device = $_SERVER['HTTP_DEVICE'];
                return isSecure($checksum, $device, $this->getConnection()) && $this->getUserPermissionLevel($checksum) >= PermissionLevel::ONLY_ADMINS;
        }

        return isSecure($_SERVER['HTTP_AUTHENTICATION'], $this->getConnection());
    }

    protected function exitOnBadRequest(...$params) {
        foreach ($params as $p) {
            if (empty($p) || strlen($p) === 0) {
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
        echo json_encode($json, JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);
    }

    protected function returnApiSuccess() {
        http_response_code(200);
        $json = array(
            "success" => true
        );
        echo json_encode($json, JSON_PRETTY_PRINT);
    }

    private function getUserPermissionLevel($checksum) {
        //TODO implement
        return 3;
    }

}

?>