<?php

/**
 * campus_events_frontend comes with ABSOLUTELY NO WARRANTY
 * See the GNU GeneralPublic License for more details.
 * https://www.gnu.org/licenses/gpl-2.0
 *
 * Copyright (C) 2026 Brain Appeal GmbH
 *
 * @copyright 2019 Brain Appeal GmbH (www.brain-appeal.com)
 * @license   GPL-2 (www.gnu.org/licenses/gpl-2.0)
 * @link      https://www.campus-events.com/
 */
$EM_CONF['campus_events_frontend'] = [
    'title' => 'Campus Events Frontend',
    'description' => 'Frontend plugins for Campus Events',
    'category' => 'plugin',
    'author' => 'Brain Appeal DEV Team',
    'author_company' => 'Brain Appeal GmbH',
    'author_email' => 'info@brain-appeal.com',
    'state' => 'stable',
    'version' => '6.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
            'campus_events_connector' => '6.0.0-6.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
