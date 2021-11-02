<?php

return [
    'form' => [
        'toolbar' => [
            'buttons' => [
                'save' => ['label' => 'lang:admin::lang.button_save', 'class' => 'btn btn-primary', 'data-request' => 'onSave'],
                'saveClose' => [
                    'label' => 'lang:admin::lang.button_save_close',
                    'class' => 'btn btn-default',
                    'data-request' => 'onSave',
                    'data-request-data' => 'close:1',
                ],
            ],
        ],
        'fields' => [
            'app_id' => [
                'label' => 'lang:igniter.broadcast::default.label_app_id',
                'span' => 'left',
                'type' => 'text',
            ],
            'key' => [
                'label' => 'lang:igniter.broadcast::default.label_key',
                'span' => 'right',
                'type' => 'text',
            ],
            'secret' => [
                'label' => 'lang:igniter.broadcast::default.label_secret',
                'span' => 'left',
                'type' => 'text',
            ],
            'cluster' => [
                'label' => 'lang:igniter.broadcast::default.label_cluster',
                'span' => 'right',
                'type' => 'text',
            ],
            'encrypted' => [
                'label' => 'lang:igniter.broadcast::default.label_encrypted',
                'span' => 'left',
                'type' => 'switch',
                'default' => TRUE,
            ],
            'use_websockets' => [
                'label' => 'lang:igniter.broadcast::default.label_use_websockets',
                'span' => 'right',
                'type' => 'switch',
                'default' => FALSE,
            ],
            'host' => [
                'label' => 'lang:igniter.broadcast::default.label_host',
                'span' => 'left',
                'type' => 'text',
                'default' => '127.0.0.1',
                'trigger' => [
                    'action' => 'enable',
                    'field' => 'use_websockets',
                    'condition' => 'checked',
                ],
            ],
            'port' => [
                'label' => 'lang:igniter.broadcast::default.label_port',
                'span' => 'right',
                'type' => 'number',
                'default' => 6001,
                'trigger' => [
                    'action' => 'enable',
                    'field' => 'use_websockets',
                    'condition' => 'checked',
                ],
            ],
            'scheme' => [
                'label' => 'lang:igniter.broadcast::default.label_scheme',
                'span' => 'left',
                'type' => 'text',
                'default' => 'http',
                'trigger' => [
                    'action' => 'enable',
                    'field' => 'use_websockets',
                    'condition' => 'checked',
                ],
            ],
        ],
        'rules' => [
            ['app_id', 'lang:igniter.broadcast::default.label_app_id', 'required|integer'],
            ['key', 'lang:igniter.broadcast::default.label_key', 'required|string'],
            ['secret', 'lang:igniter.broadcast::default.label_secret', 'required|string'],
            ['cluster', 'lang:igniter.broadcast::default.label_cluster', 'required|string'],
            ['encrypted', 'lang:igniter.broadcast::default.label_encrypted', 'required|string'],
            ['use_websockets', 'lang:igniter.broadcast::default.label_use_websockets', 'required|boolean'],
            ['host', 'lang:igniter.broadcast::default.label_host', 'required_if:use_websockets,1|string'],
            ['port', 'lang:igniter.broadcast::default.label_port', 'required_if:use_websockets,1|integer'],
            ['scheme', 'lang:igniter.broadcast::default.label_scheme', 'required_if:use_websockets,1|alpha'],
        ],
    ],
];
