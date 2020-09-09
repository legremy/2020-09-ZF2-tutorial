<?php

return [
    'controllers' => [
        'invokables' => [
            'Album\Controller\Album' => 'Album\Controller\AlbumController',
        ],
    ],
    'router' => [
        'routes' => [
            'album' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/album[/:action][/:id]',
                    'constaints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Album\Controller\Album',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'album' => __DIR__ . '/../view',
        ],
    ],
];