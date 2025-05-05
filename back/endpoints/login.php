<?php

header("Content-Type: application/json");
require_once '../database/Repository.php';

function checkPasswordToCorrect($psw, $hash) {
    return password_verify($psw, $hash);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $mainError = [];
    $repository = new JSONUserRepository(__DIR__ . '/../database/users.json');
    try {
        $user = $repository->getByLogin($data["login"])[1];
    } catch (Exception $e) {
        $mainError[] = $e->getMessage();
    }
    if (!$mainError) {
        $isCorrectPassword = checkPasswordToCorrect($data["password"], $user["password"]);
        if ($isCorrectPassword) {
            $user["visit"] = (int) $user["visit"]+1;
            $repository->update($user["id"], $user);
            $user["password"] = "";
            echo json_encode(["success" => True, "data" => $user, "errors" => [], "mainError" => []]);
        } else {
            echo json_encode(["success" => False, "data" => [], "errors" => [], "mainError" => ["Неверный пароль"]]);
        }
    } else {
        echo json_encode(["success" => False, "data" => [], "errors" => [], "mainError" => $mainError]);
    }
}