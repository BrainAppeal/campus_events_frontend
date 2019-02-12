<?php

/** @var string $_EXTKEY */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Campus Events Frontend',
    'description' => 'Frontend plugins for Campus Events',
    'category' => 'plugin',
    'author' => 'Gert Hammes',
    'author_company' => 'Brain Appeal GmbH',
    'author_email' => 'info@brain-appeal.com',
    'state' => 'beta',
    'clearCacheOnLoad' => 1,
    'version' => '0.9.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
            'campus_events_connector' => '>=0.9.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
