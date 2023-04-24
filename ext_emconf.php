<?php
/**
 * campus_events_frontend comes with ABSOLUTELY NO WARRANTY
 * See the GNU GeneralPublic License for more details.
 * https://www.gnu.org/licenses/gpl-2.0
 *
 * Copyright (C) 2019 Brain Appeal GmbH
 *
 * @copyright 2019 Brain Appeal GmbH (www.brain-appeal.com)
 * @license   GPL-2 (www.gnu.org/licenses/gpl-2.0)
 * @link      https://www.campus-events.com/
 */

/** @var string $_EXTKEY */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Campus Events Frontend',
    'description' => 'Frontend plugins for Campus Events',
    'category' => 'plugin',
    'author' => 'Gert Hammes',
    'author_company' => 'Brain Appeal GmbH',
    'author_email' => 'info@brain-appeal.com',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '3.0.3',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.13-11.5.99',
            'campus_events_connector' => '3.0.0-3.999.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
