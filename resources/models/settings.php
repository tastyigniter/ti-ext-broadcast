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
            'provider' => [
                'label' => 'lang:igniter.broadcast::default.label_provider',
                'type' => 'radiotoggle',
                'default' => 'pusher',
                'options' => [
                    'pusher' => 'lang:igniter.broadcast::default.text_pusher',
                    'reverb' => 'lang:igniter.broadcast::default.text_reverb',
                    'ably' => 'lang:igniter.broadcast::default.text_ably',
                ],
            ],
            'app_id' => [
                'label' => 'lang:igniter.broadcast::default.label_app_id',
                'span' => 'left',
                'type' => 'text',
                'trigger' => [
                    'action' => 'hide',
                    'field' => 'provider',
                    'condition' => 'value[ably]',
                ],
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
                'trigger' => [
                    'action' => 'hide',
                    'field' => 'provider',
                    'condition' => 'value[ably]',
                ],
            ],
            'cluster' => [
                'label' => 'lang:igniter.broadcast::default.label_cluster',
                'span' => 'right',
                'default' => 'mt1',
                'type' => 'text',
                'trigger' => [
                    'action' => 'show',
                    'field' => 'provider',
                    'condition' => 'value[pusher]',
                ],
            ],
            'encrypted' => [
                'label' => 'lang:igniter.broadcast::default.label_encrypted',
                'span' => 'left',
                'type' => 'switch',
                'default' => true,
                'trigger' => [
                    'action' => 'show',
                    'field' => 'provider',
                    'condition' => 'value[pusher]',
                ],
            ],
        ],
        'rules' => [
            ['provider', 'lang:igniter.broadcast::default.label_provider', 'required|string|in:pusher,reverb,ably'],
            ['app_id', 'lang:igniter.broadcast::default.label_app_id', 'nullable|required_if:provider,pusher,reverb|integer'],
            ['key', 'lang:igniter.broadcast::default.label_key', 'nullable|required_if:provider,pusher,reverb|string'],
            ['secret', 'lang:igniter.broadcast::default.label_secret', 'nullable|required_if:provider,pusher,reverb|string'],
            ['cluster', 'lang:igniter.broadcast::default.label_cluster', 'nullable|required_if:provider,pusher|string'],
            ['encrypted', 'lang:igniter.broadcast::default.label_encrypted', 'nullable|required_if:provider,pusher|boolean'],
        ],
    ],
];
