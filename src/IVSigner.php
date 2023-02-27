<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class IVSigner extends ApiObject {
    public $firstName;
    public $lastName;
    public $birthDateVerificationStatus;
    public $nameVerificationStatus;
    public $verificationStatus;
    public $nameComparisonScore;
    public $birthDate;
}
