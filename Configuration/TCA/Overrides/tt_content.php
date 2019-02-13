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
    'BrainAppeal.CampusEventsFrontend',
    $pluginName,
    'LLL:EXT:campus_events_frontend/Resources/Private/Language/locallang_db.xlf:tx_campus_events_frontend_event.name'
);

$pluginSignature = $prefix . '_' . strtolower($pluginName);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'recursive,select_key,pages';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:campus_events_frontend/Configuration/FlexForms/Event.xml');
