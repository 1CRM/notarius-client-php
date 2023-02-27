<?php

declare(strict_types=1);

namespace OneCRM\NotariusClient;

class Template extends WorkflowOrTemplate {

    public $duration;

    /**
     * @var TemplateOwner
     */
    public $owner;


    protected function objectProps(): array {
        return array_merge(
            parent::objectProps(),
            [
                'owner' => '\OneCRM\NotariusClient\TemplateOwner',
            ]
        );
    }
}
