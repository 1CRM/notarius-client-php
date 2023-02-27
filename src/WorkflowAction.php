<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class WorkflowAction extends ApiObject {

    public const NOT_STARTED = 'NOT_STARTED';
    public const STARTED = 'STARTED';
    public const COMPLETED = 'COMPLETED';
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const DECLINED = 'DECLINED';

    public $mode;
    public $returnUrl;
    public $zoneLabel;
    public $step;
    public $ref;
    public $signer;
    public $messageToSigner;
    public $id;
    public $type;
    public $status;
    public $secondFactorUsed;
    public $declineReason;

    protected function objectProps(): array {
        return [
            "signer" => '\OneCRM\NotariusClient\WorkflowSigner',
        ];
    }

    protected function listProps(): array {
        return [
            "signers" => '\OneCRM\NotariusClient\WorkflowSigner',
        ];
    }

    public function cloneForUpdate() {
        $copy = new WorkflowAction([]);
        $copy->mode = $this->mode;
        $copy->returnUrl = $this->returnUrl;
        $copy->zoneLabel = $this->zoneLabel;
        $copy->step = $this->step;
        $copy->ref = $this->ref;
        $copy->signer = clone $this->signer;
        $copy->messageToSigner = $this->messageToSigner;
        return $copy;
    }


    protected function decodeField($fieldName, $value) {
        if ($fieldName === 'messageToSigner' && $value !== null) {
            return \urldecode($value);
        }
        return parent::encodeField($fieldName, $value);
    }

    protected function encodeField($fieldName, $value) {
        if ($fieldName === 'messageToSigner' && $value !== null) {
            return \urlencode($value);
        }
        return parent::encodeField($fieldName, $value);
    }
}
