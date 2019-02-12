<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Campus Events Frontend',
    'description' => 'Frontend plugins for Campus Events',
    'category' => 'plugin',
    'author' => 'Gert Hammes',
    'author_email' => 'gert.hammes@brain-appeal.com',
    'author_company' => 'Brain Appeal GmbH',
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
            'campus_events_connector' => '>=0.4.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
