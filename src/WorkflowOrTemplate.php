<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class WorkflowOrTemplate extends ApiObject {

    public $id;
    public $name;
    public $status;
    public $messageToSigners;
    public $customInitiatorName;
    public $customInitiatorEmail;
    public $hasOwnerDownloadedFinalDocuments;
    public $createdOn;
    public $createdBy;
    public $modifiedOn;
    public $modifiedBy;
    public $createdInVersion;
    public $parallelActions;
    public $sequentialDocuments;
    public $version;
    public $isLocked;
    public $locked;
    public $modifierFirstName;
    public $modifierLastName;
    public $labels;
    public $workflowOwnerEmailsEnabled;
    public $groupId;

    /**
     * @var WorkflowSigner[]
     */
    public $personWithUndownloadedFinalDocuments;

    /**
     * @var WorkflowSigner[]
     */
    public $personWithDownloadedFinalDocuments;

    /**
     * @var WorkflowSigner[]
     */
    public $personWithDownloadedAuditTrail;

    /**
     * @var Document[]
     */
    public $documents;
    /**
     * @var WorkflowAction[]
     */
    public $actions;
    /**
     * @var NotificationAction[]
     */
    public $notifications;
    /**
     * @var Webhook[]
     */
    public $webhooks;

    protected function listProps(): array {
        return [
            "documents" => "\OneCRM\NotariusClient\Document",
            "actions" => "\OneCRM\NotariusClient\WorkflowAction",
            "notifications" => "\OneCRM\NotariusClient\NotificationAction",
            "webhooks" => "\OneCRM\NotariusClient\Webhook",
            "personWithUndownloadedFinalDocuments" => "\OneCRM\NotariusClient\WorkflowSigner",
            "personWithDownloadedFinalDocuments" => "\OneCRM\NotariusClient\WorkflowSigner",
            "personWithDownloadedAuditTrail" => "\OneCRM\NotariusClient\WorkflowSigner",
        ];
    }

    public function cloneForUpdate() {
        $copy = new Workflow([]);
        $copy->name = $this->name;
        $copy->status = $this->status;
        foreach ($this->actions as $a) {
            $copy->actions[] = $a->cloneForUpdate();
        }
        foreach ($this->documents as $d) {
            $copy->documents[] = $d->cloneForUpdate();
        }
        return $copy;
    }

    protected function decodeField($fieldName, $value) {
        if ($fieldName === 'messageToSigners' && $value !== null) {
            return \urldecode($value);
        }
        return parent::decodeField($fieldName, $value);
    }

    protected function encodeField($fieldName, $value) {
        if ($fieldName === 'messageToSigners' && $value !== null) {
            return \urlencode($value);
        }
        return parent::encodeField($fieldName, $value);
    }
}
