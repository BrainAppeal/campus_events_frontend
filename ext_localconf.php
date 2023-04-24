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

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    static function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'BrainAppeal.CampusEventsFrontend',
            'Event',
            [
                \BrainAppeal\CampusEventsFrontend\Controller\EventController::class => 'list, show'
            ]/*,
            // non-cacheable actions
            [
                \BrainAppeal\CampusEventsFrontend\Controller\EventController::class => 'list'
            ]*/
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
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'campus_events_frontend-plugin-event',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:campus_events_frontend/Resources/Public/Icons/calendar-days-solid.svg']
        );

    }
);
