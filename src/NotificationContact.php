<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class NotificationContact extends ApiObject {
    public $firstName;
    public $lastName;
    public $email;
    public $phone;
    public $secretQuestion;
    public $secretAnswer;
    public $isSecretAnswerChanged;
    public $amr;
    public $lang;
}
