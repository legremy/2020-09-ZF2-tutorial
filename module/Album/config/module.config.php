<?php

return [
    'controllers' => [
        'invokables' => [
            'Album\Controller\Album' => 'Album\Controller\AlbumController',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'album' => __DIR__ . '/../view',
        ],
    ],
];