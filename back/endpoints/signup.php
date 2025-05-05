<?php

header("Content-Type: application/json");
require_once '../database/Repository.php';
require_once '../utils/Validator.php';
require_once '../endpoints/utils.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $mainError = [];

    $email = $data["email"];
    $phone = $data["phone"];
    $password = $data["password"];
    $login = $data["login"];
    $station = $data["station"];

    $validator = new SignUpValidator();
    [$isCorrectPhone, $phone, $errorsPhone] = $validator->phoneValidate($phone);
    [$isCorrectEmail, $email, $errorsEmail] = $validator->emailValidate($email);
    [$isCorrectPassword, $password, $errorsPassword] = $validator->passwordValidate($password);

    $repository = new JSONUserRepository(__DIR__ . '/../database/users.json');

    try {
        $byEmail = $repository->getByEmail($email);
        if ($byEmail) $mainError[] = "Пользователь с такой почтой уже существует [" . $email . "]";
    } catch (Exception $ex) {}

    try {
        $byPhone = $repository->getByPhone($phone);
        if ($byPhone) $mainError[] = "Пользователь с таким номером телефона уже существует [" . $phone . "]";
    } catch (Exception $ex) {}

    try {
        $byLogin = $repository->getByLogin($login);
        if ($byLogin) $mainError[] = "Пользователь с таким логином уже существует [" . $login . "]";
    } catch (Exception $ex) {}

    if ($isCorrectPhone && $isCorrectPassword && $isCorrectEmail && !$mainError) {
        $created = $repository->create(
            [
                "login" => $login,
                "phone" => $phone,
                "password" => $password,
                "station" => $station,
                "email" => $email,
                "visit" => 1,
            ]
        );
        echo json_encode(["success" => True, "data" => $created, "errors" => [], "mainError" => $mainError]);
    } else {
        $errors = [];
        $errors[] = addErrorsToArray($isCorrectPhone, "phone", $errorsPhone);
        $errors[] = addErrorsToArray($isCorrectPassword, "password", $errorsPassword);
        $errors[] = addErrorsToArray($isCorrectEmail, "email", $errorsEmail);
        echo json_encode(["success" => False, "data" => [], "errors" => $errors, "mainError" => $mainError]);
    }
}