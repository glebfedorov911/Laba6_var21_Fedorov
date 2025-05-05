<?php

require_once '../database/Repository.php';

interface ValidatorInterface {
    public function validate();
}

class Validator implements ValidatorInterface {
    protected $errorMessages;

    function __construct() {
        $this->errorMessages = [];
    }

    public function validate() {}
}

class PasswordValidator extends Validator implements ValidatorInterface
{
    protected $password;

    function __construct($password)
    {
        parent::__construct();
        $this->password = $password;
    }

    public function validate() {
        return $this->verifyPassword();
    }

    protected function verifyPassword() {
        $isGoodLength = $this->lengthPassword();
        $hasNumsInPassword = $this->numInPassword();
        $hasSpecSymbolsInPassword = $this->specSymbolInPassword();
        $hasLettersInPassword = $this->lettersInPassword();
        if ($isGoodLength && $hasNumsInPassword && $hasSpecSymbolsInPassword && $hasLettersInPassword) {
            $hashPassword = password_hash($this->password, PASSWORD_DEFAULT);
            return [true, $hashPassword, null];
        }
        return [false, null, $this->errorMessages];
    }

    protected function lengthPassword() {
        if (strlen($this->password) < 8) {
            $this->errorMessages[] = "Пароль должен быть более 8 символов";
            return false;
        }
        return true;
    }

    protected function numInPassword() {
        if (!preg_match('/\d/', $this->password)) {
            $this->errorMessages[] = "Пароль должен включать в себя цифры: [0-9].";
            return false;
        }
        return true;
    }

    protected function specSymbolInPassword() {
       if  (!preg_match('/[#_?\$]/', $this->password)) {
           $this->errorMessages[] = "Пароль должен включать в себя спец. символы: [#, _, ?, $].";
           return false;
       }
       return true;
    }

    protected function lettersInPassword() {
        if (!preg_match('/[a-zA-Z]/', $this->password)) {
            $this->errorMessages[] = "Пароль должен включать в себя буквы: [a-z, A-Z].";
            return false;
        }
        return true;
    }
}

class EmailValidator extends Validator implements ValidatorInterface
{
    protected $email;

    function __construct($email) {
        parent::__construct();
        $this->email = $email;
    }

    public function validate()
    {
        return $this->checkCorrectEmail();
    }

    protected function checkCorrectEmail() {
        $isValidEmail = $this->emailValidation();
        if ($isValidEmail) {
            return [true, $this->email, null];
        }
        return [false, null, $this->errorMessages];
    }

    protected function emailValidation()
    {
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if (!preg_match($pattern, $this->email)) {
                $this->errorMessages[] = "Значение должно подходить под формат почты email@mail.ru.";
            return false;
        }
        return true;
    }
}

class PhoneValidator extends Validator implements ValidatorInterface
{

    protected $phone;

    function __construct($phone) {
        parent::__construct();
        $this->phone = $phone;
    }

    public function validate() {
        return $this->checkCorrectPhone();
    }

    protected function checkCorrectPhone() {
        $isGoodFormat = $this->checkPhoneOnFormat();
        $isRussianFormat = $this->checkPhoneOnRussianNumber();
        if ($isGoodFormat && $isRussianFormat) {
            return [true, $this->phone, null];
        }
        return [false, null, $this->errorMessages];
    }

    protected function checkPhoneOnRussianNumber() {
        if (!preg_match('/^\+7/', $this->phone)) {
            $this->errorMessages[] = "Телефон должен быть российского формата +7XXXXXXXXXX";
            return false;
        }
        return true;
    }

    protected function checkPhoneOnFormat() {
        $pattern = '/^\+\d{10,15}$/';
        if (!preg_match($pattern, $this->phone)) {
            $this->errorMessages[] = "Телефон должен быть в формате +XXXXXXXXXXX";
            return false;
        }
        return true;
    }
}

interface SignUpValidatorInterface {

    public function phoneValidate($phone);
    public function emailValidate($email);
    public function passwordValidate($password);
}

class SignUpValidator implements SignUpValidatorInterface
{
    public function phoneValidate($phone) {
        $phoneValidator = new PhoneValidator($phone);
        return $phoneValidator->validate();
    }
    public function emailValidate($email) {
        $emailValidator = new EmailValidator($email);
        return $emailValidator->validate();
    }
    public function passwordValidate($password) {
        $passwordValidator = new PasswordValidator($password);
        return $passwordValidator->validate();
    }
}

class NameSignValidator extends Validator implements ValidatorInterface {
    private $name;

    public function __construct($name)
    {
        parent::__construct();
        $this->name = $name;
        $this->repository = new JSONRepository(__DIR__ . '/../database/signs.json');
    }

    public function validate()
    {
        $isCorrectLength = $this->lengthSignName();
        if ($isCorrectLength) {
            return [true, $this->name, null];
        }
        return [false, null, $this->errorMessages];
    }

    private function lengthSignName() {
        if (strlen($this->name) < 3 || strlen($this->name) > 20) {
            $this->errorMessages[] = "Название заявки не может быть менее 3 и более 20 символов";
            return false;
        }
        return true;
    }
}

class SizeSignValidator extends Validator implements ValidatorInterface {
    private $size;

    public function __construct($size)
    {
        parent::__construct();
        $this->size = $size;
    }

    public function validate() {
        $isGoodSize = $this->checkSize();
        if ($isGoodSize) {
            return [true, $this->size, null];
        }
        return [false, null, $this->errorMessages];
    }

    private function checkSize() {
        if ($this->size < 0) {
            $this->errorMessages[] = "Размер не может быть меньше нуля";
            return false;
        }
        if ($this->size > 100000) {
            $this->errorMessages[] = "Размер не может быть больше ста тысяч";
            return false;
        }
        return true;
    }

}

interface SignValidatorInterface
{
    public function sizeValidate($size);
    public function nameValidate($name);
}

class SignValidator implements SignValidatorInterface {
    public function sizeValidate($size) {
        $validator = new SizeSignValidator($size);
        return $validator->validate();
    }

    public function nameValidate($name) {
        $validator = new NameSignValidator($name);
        return $validator->validate();
    }
}