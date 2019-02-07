<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'BrainAppeal.CampusEventsFrontend',
            'Event',
            [
                'Event' => 'list, show'
            ]/*,
            // non-cacheable actions
            [
                'Event' => 'list'
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
            \TYPO3\CMS\Core\Imaging\IconProvider\FontawesomeIconProvider::class,
            ['name' => 'calendar']
        );
		
    }
);
