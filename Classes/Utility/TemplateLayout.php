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

namespace BrainAppeal\CampusEventsFrontend\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TemplateLayout utility class
 */
class TemplateLayout implements SingletonInterface
{
    /**
     * Get available template layouts for a certain page
     *
     * @param int $pageUid
     * @return array<int<0,max>, array<string, string>>
     */
    public function getAvailableTemplateLayouts(int $pageUid): array
    {
        $templateLayouts = [];

        // Check if the layouts are extended by ext_tables
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['campus_events_frontend']['templateLayouts'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['campus_events_frontend']['templateLayouts'])
        ) {
            $templateLayouts = $GLOBALS['TYPO3_CONF_VARS']['EXT']['campus_events_frontend']['templateLayouts'];
            foreach ($templateLayouts as &$layout) {
                if (!isset($layout['label']) && isset($layout[0])) {
                    $layout['label'] = $layout[0];
                    unset($layout[0]);
                }
                if (!isset($layout['value']) && isset($layout[1])) {
                    $layout['value'] = $layout[1];
                    unset($layout[1]);
                }
            }
            unset($layout);
        }

        // Add TsConfig values
        foreach ($this->getTemplateLayoutsFromTsConfig($pageUid) as $templateKey => $title) {
            if (str_starts_with($title, '--div--')) {
                $optGroupParts = GeneralUtility::trimExplode(',', $title, true, 2);
                $title = $optGroupParts[1];
                $templateKey = $optGroupParts[0];
            }
            $templateLayouts[] = ['label' => $title, 'value' => $templateKey];
        }

        return $templateLayouts;
    }

    /**
     * Get template layouts defined in TsConfig
     *
     * @param int $pageUid
     * @return array<string, string>
     */
    protected function getTemplateLayoutsFromTsConfig(int $pageUid): array
    {
        $templateLayouts = [];
        $pagesTsConfig = BackendUtility::getPagesTSconfig($pageUid);
        if (isset($pagesTsConfig['tx_campuseventsfrontend.']['templateLayouts.'])
            && is_array($pagesTsConfig['tx_campuseventsfrontend.']['templateLayouts.'])) {
            $templateLayouts = $pagesTsConfig['tx_campuseventsfrontend.']['templateLayouts.'];
        }
        return $templateLayouts;
    }
}
