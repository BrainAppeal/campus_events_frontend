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

$prefix = 'campuseventsfrontend';
$pluginName = 'Event';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'CampusEventsFrontend',
    $pluginName,
    'LLL:EXT:campus_events_frontend/Resources/Private/Language/locallang_db.xlf:tx_campus_events_frontend_event.name'
);

$pluginSignature = $prefix . '_' . strtolower($pluginName);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'recursive,select_key,pages';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:campus_events_frontend/Configuration/FlexForms/Event.xml');




defined('TYPO3') or die();

$pluginConfig = ['event_list', 'event_show'];
foreach ($pluginConfig as $pluginName) {
    $pluginNameForLabel = $pluginName;
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'CampusEventsFrontend',
        \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($pluginName),
        'LLL:EXT:campus_events_frontend/Resources/Private/Language/locallang_be.xlf:plugin.' . $pluginName . '.title',
        null,
        //'news'
    );

    $contentTypeName = 'campuseventsfrontend_' . str_replace('_', '', $pluginName);
    $flexformFileName = $pluginNameForLabel;

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:campus_events_frontend/Configuration/FlexForms/flexform_' . $flexformFileName . '.xml',
        $contentTypeName
    );
    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$contentTypeName] = 'campus_events_frontend-plugin-event';

    $GLOBALS['TCA']['tt_content']['types'][$contentTypeName]['showitem'] = '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            --palette--;;headers,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
            pi_flexform,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ';
}