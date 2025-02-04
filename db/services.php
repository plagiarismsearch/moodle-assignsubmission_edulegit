<?php

$functions = [
        'assignsubmission_edulegit_webhook_handler' => [
                'classname' => 'assignsubmission_edulegit\external\webhook_handler',
                'methodname' => 'execute',
                'classpath' => '',
                'description' => 'Handles EduLegit webhook requests.',
                'type' => 'write',
                'ajax' => true,
        ],
];

$services = [
        'EduLegit Webhook Service' => [
                'functions' => ['assignsubmission_edulegit_webhook_handler'],
                'restrictedusers' => 0,
                'enabled' => 1,
        ],
];