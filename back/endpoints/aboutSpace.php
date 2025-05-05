<?php

header("Content-Type: application/json");
require_once '../database/Repository.php';
require_once '../utils/Validator.php';
require_once '../endpoints/utils.php';

$repository = new JSONSignsRepository(__DIR__ . '/../database/signs.json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $mainError = [];
    $currentSignIndex = -1;

    $name = $data["name"] ?? null;
    $width = $data["width"] ?? null;
    $height = $data["height"] ?? null;
    $length = $data["length"] ?? null;
    $category = $data["category"] ?? null;
    $typeSign = $data["typeSign"] ?? null;
    $currentId = $data["currentId"] ?? null;
    $userId = $data["userId"] ?? null;

    $signs = $repository->getAll();
    for ($i = 0; $i < count($signs); $i++) {
        if ($signs[$i]["name"] == $name) {
            if  ($signs[$i]["id"] != $currentId) {
                $mainError[] = "Такое имя заявки уже используется id=" . $signs[$i]["id"];
            }
        }

        if ($signs[$i]["id"] == $currentId) {
            $currentSignIndex = $signs[$i]["id"];
        }
    }

    if ($typeSign == "delete") {
        $repository->delete($data["id"]);
        echo json_encode(["id"]);
        exit;
    }

    $validator = new SignValidator();
    [$isCorrectName, $name, $nameErrors] = $validator->nameValidate($name);
    [$isCorrectWidth, $width, $widthErrors] = $validator->sizeValidate($width);
    [$isCorrectHeight, $height, $heightErrors] = $validator->sizeValidate($height);
    [$isCorrectLength, $length, $lengthErrors] = $validator->sizeValidate($length);

    if (!$mainError && $isCorrectName && $isCorrectWidth && $isCorrectHeight && $isCorrectLength) {
        $created = [];
        if ($typeSign == "create") {
            $created = $repository->create(
                [
                    "name" => $name,
                    "width" => $width,
                    "length" => $length,
                    "height" => $height,
                    "category" => $category,
                    "userId" => $userId,
                ]
            );
        }

        if ($typeSign == "update") {
            $created = $repository->update(
                $currentSignIndex,
                [
                    "name" => $name,
                    "width" => $width,
                    "length" => $length,
                    "height" => $height,
                    "category" => $category,
                    "userId" => $userId,
                ]
            );
        }

        echo json_encode(["success" => True, "data" => $created, "errors" => [], "mainError" => []]);
    } else {
        $errors = [];
        $errors[] = addErrorsToArray($isCorrectName, "name", $nameErrors);
        $errors[] = addErrorsToArray($isCorrectWidth, "width", $widthErrors);
        $errors[] = addErrorsToArray($isCorrectHeight, "height", $heightErrors);
        $errors[] = addErrorsToArray($isCorrectLength, "length", $lengthErrors);
        echo json_encode(["success" => False, "data" => [], "errors" => $errors, "mainError" => $mainError]);
    }
} else {
    $signs = $repository->getAllByUserId($_GET["userId"]);
    echo json_encode(["success" => True, "data" => $signs, "errors" => [], "mainError" => []]);
}
