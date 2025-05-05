<?php

function addErrorsToArray($status, $name, $errors) {
    $errors[$name] = [
        "status" => $status,
        "errors" => $errors
    ];
    return $errors;
}