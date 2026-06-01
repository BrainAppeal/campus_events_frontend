<?php

use BrainAppeal\CampusEventsFrontend\Controller\EventController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

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
defined('TYPO3') || die();

call_user_func(
    static function () {

        ExtensionUtility::configurePlugin(
            'CampusEventsFrontend',
            'EventList',
            [
                EventController::class => 'list',
            ],
            [],
            ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        ExtensionUtility::configurePlugin(
            'CampusEventsFrontend',
            'EventShow',
            [
                EventController::class => 'show',
            ],
            [],
            ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );
        ExtensionManagementUtility::addTypoScriptSetup(trim('
    plugin {
        tx_campuseventsfrontend_eventlist.view.pluginNamespace = tx_campuseventsfrontend_event
        tx_campuseventsfrontend_eventshow.view.pluginNamespace = tx_campuseventsfrontend_event
    }
'));
    }
);
