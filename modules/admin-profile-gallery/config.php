<?php

return [
    '__name' => 'admin-profile-gallery',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/admin-profile-gallery.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'https://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-profile-gallery' => ['install','update','remove'],
        'theme/admin/profile/gallery' => ['install','update','remove'],
        'theme/admin/static/js/profile-gallery.js' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin-profile' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'profile' => NULL
            ],
            [
                'profile-gallery' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminProfileGallery\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-profile-gallery/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminProfileGallery' => [
                'path' => [
                    'value' => '/profile/(:id)/gallery',
                    'params' => [
                        'id' => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminProfileGallery\\Controller\\Gallery::index'
            ],
            'adminProfileGalleryEdit' => [
                'path' => [
                    'value' => '/profile/(:profile)/gallery/(:id)',
                    'params' => [
                        'id' => 'number',
                        'profile' => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminProfileGallery\\Controller\\Gallery::edit'
            ],
            'adminProfileGalleryRemove' => [
                'path' => [
                    'value' => '/profile/(:profile)/gallery/(:id)/remove',
                    'params' => [
                        'id' => 'number',
                        'profile' => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminProfileGallery\\Controller\\Gallery::remove'
            ],
        ]
    ],
    'adminProfile' => [
        'sidebar' => [
            'Gallery' => [null, 'adminProfileGallery']
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.profile-gallery.edit' => [
                'title' => [
                    'label' => 'Title',
                    'type' => 'text',
                    'rules' => [
                        'required' => TRUE
                    ]
                ],
                'images' => [
                    'label' => 'Image List',
                    'type' => 'textarea',
                    'rules' => [
                        'json' => TRUE
                    ]
                ]
            ],
            'admin.profile-gallery.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => TRUE,
                    'rules' => []
                ]
            ]
        ]
    ]
];