<?php
$prefix = 'braineventfrontend';
$pluginName = 'Event';
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'BrainAppeal.BrainEventFrontend',
    $pluginName,
    'LLL:EXT:brain_event_frontend/Resources/Private/Language/locallang_db.xlf:tx_brain_event_frontend_event.name'
);

$pluginSignature = $prefix . '_' . strtolower($pluginName);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'recursive,select_key,pages';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:brain_event_frontend/Configuration/FlexForms/Event.xml');