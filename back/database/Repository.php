<?php

interface Repository
{
    public function create($data);
    public function update($id, $data);
    public function delete($id);
    public function getAll();
    public function getById($id);
}

class JSONRepository implements Repository {
    protected $currentId = 1;
    protected $databaseName;

    public function __construct($databaseName) {
        $this->databaseName = $databaseName;
        $this->currentId = $this->findCurrentId();
    }

    protected function findCurrentId() {
        $json = file_get_contents($this->databaseName);
        $data = json_decode($json, true);
        if (!$data) {
            return 1;
        }
        $getMaxIds = function($data) {
            $id = (int) $data[0]["id"];
            for ($i = 1; $i < count($data); $i++) {
                if ((int) $data[$i]["id"] > $id) {
                    $id = (int) $data[$i]["id"];
                }
            }
            return $id;
        };
        return $getMaxIds($data) + 1;
    }

    public function create($data) {
        $json = $this->readFile();
        $data["id"] = $this->currentId;
        $json[] = $data;
        $this->writeData($json);
        return $data;
    }
    public function update($id, $data) {
        $json = $this->readFile();
        $recordById = $this->getById($id);
        $json[$recordById[0]] = $data;
        $json[$recordById[0]]["id"] = $recordById[1]["id"];
        $this->writeData($json);
        return $data;
    }
    public function delete($id) {
        $json = $this->readFile();
        $recordById = $this->getById($id);
        unset($json[$recordById[0]]);
        $json = array_values($json);
        $this->writeData($json);
    }
    public function getAll() {
        return $this->readFile();
    }
    public function getById($id) {
        return $this->getBy($id, "id");
    }

    protected function readFile() {
        $database = file_get_contents($this->databaseName);
        return json_decode($database, true);
    }

    protected function writeData($json) {
        $json = json_encode($json);
        file_put_contents($this->databaseName, $json);
    }

    protected function getBy($value, $valueName) {
        $json = $this->readFile();
        for ($i = 0; $i < count($json); $i++) {
            if ((string) $json[$i][$valueName] == (string) $value) {
                return [$i, $json[$i]];
            }
        }
        throw new Exception("Не найдена запись с " . $valueName . " " . $value);
    }
}

interface UserRepository extends Repository {
    public function getByEmail($email);
    public function getByLogin($email);
    public function getByPhone($email);
}

class JSONUserRepository extends JSONRepository implements UserRepository {

    public function __construct ($databaseName)
    {
        parent::__construct($databaseName);
    }

    public function getByEmail($email) {
        return $this->getBy($email, "email");
    }

    public function getByPhone($phone) {
        return $this->getBy($phone, "phone");
    }

    public function getByLogin($login) {
        return $this->getBy($login, "login");
    }
}

class JSONSignsRepository extends JSONRepository implements Repository {
    public function __construct ($databaseName)
    {
        parent::__construct($databaseName);
    }

    public function getAllByUserId($userId) {
        $all = $this->getAll();
        $byUserId = [];
        for ($i = 0; $i < count($all); $i++) {
            if ((string) $all[$i]["userId"] == (string) $userId) {
                $byUserId[] = $all[$i];
            }
        }
        return $byUserId;
    }
}