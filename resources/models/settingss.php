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
                'default' => true,
            ],
        ],
        'rules' => [
            ['app_id', 'lang:igniter.broadcast::default.label_app_id', 'required|integer'],
            ['key', 'lang:igniter.broadcast::default.label_key', 'required|string'],
            ['secret', 'lang:igniter.broadcast::default.label_secret', 'required|string'],
            ['cluster', 'lang:igniter.broadcast::default.label_cluster', 'required|string'],
            ['encrypted', 'lang:igniter.broadcast::default.label_encrypted', 'required|string'],
        ],
    ],
];
