<?php

Kirby::plugin('microman/column-block', [
    'blueprints' => [
        'blocks/columns' => __DIR__ . '/blueprints/blocks/columns.yml'
    ],
    'snippets' => [
        'blocks/columns' => __DIR__ . '/snippets/blocks/columns.php'
    ]
]);
