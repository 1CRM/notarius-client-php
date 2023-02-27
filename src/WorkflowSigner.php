<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class WorkflowSigner extends ApiObject {
    public $firstName;
    public $lastName;
    public $email;
    public $phone;
    public $secretQuestion;
    public $secretAnswer;
    public $isSecretAnswerChanged;
    public $amr;
    public $lang;
    public $type;
    public $contactOwner;
    public $placeHolder;
    public $generated;
    public $iv;
    public $birthDate;
    public $ivSigner;

    protected function objectProps(): array {
        return [
            "ivSigner" => '\OneCRM\NotariusClient\IVSigner',
        ];
    }
}
