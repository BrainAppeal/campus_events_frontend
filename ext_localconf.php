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
    static function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'CampusEventsFrontend',
            'Event',
            [
                \BrainAppeal\CampusEventsFrontend\Controller\EventController::class => 'list, show'
            ]/*,
            // non-cacheable actions
            [
                \BrainAppeal\CampusEventsFrontend\Controller\EventController::class => 'list'
            ]*/
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'CampusEventsFrontend',
            'EventList',
            [
                \BrainAppeal\CampusEventsFrontend\Controller\EventController::class => 'list'
            ],
            [],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'CampusEventsFrontend',
            'EventShow',
            [
                \BrainAppeal\CampusEventsFrontend\Controller\EventController::class => 'show'
            ],
            [],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        // wizards
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod {
                wizards.newContentElement.wizardItems.plugins {
                    elements {
                        event {
                            iconIdentifier = campus_events_frontend-plugin-event
                            title = LLL:EXT:campus_events_frontend/Resources/Private/Language/locallang_db.xlf:tx_campus_events_frontend_event.name
                            description = LLL:EXT:campus_events_frontend/Resources/Private/Language/locallang_db.xlf:tx_campus_events_frontend_event.description
                            tt_content_defValues {
                                CType = list
                                list_type = campuseventsfrontend_event
                            }
                        }
                    }
                    show = *
                }
           }'
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
