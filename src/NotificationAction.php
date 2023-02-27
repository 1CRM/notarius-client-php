<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class NotificationAction extends ApiObject {
    public $contact;

    protected function objectProps(): array {
        return [
            "contact" => '\OneCRM\NotariusClient\NotificationContact',
        ];
    }
}
