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
defined('TYPO3') or die();

call_user_func(
    static function () {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'CampusEventsFrontend',
            'EventList',
            [
                \BrainAppeal\CampusEventsFrontend\Controller\EventController::class => 'list',
            ],
            [],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'CampusEventsFrontend',
            'EventShow',
            [
                \BrainAppeal\CampusEventsFrontend\Controller\EventController::class => 'show',
            ],
            [],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['txCampusEventsFrontendPluginUpdater'] = \BrainAppeal\CampusEventsFrontend\Updates\PluginUpdater::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['txCampusEventsFrontendPluginPermissionUpdater'] = \BrainAppeal\CampusEventsFrontend\Updates\PluginPermissionUpdater::class;
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(trim('
    plugin {
        tx_campuseventsfrontend_eventlist.view.pluginNamespace = tx_campuseventsfrontend_event
        tx_campuseventsfrontend_eventshow.view.pluginNamespace = tx_campuseventsfrontend_event
    }
'));
    }
);
