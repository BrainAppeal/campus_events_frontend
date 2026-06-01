<?php

declare(strict_types=1);

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

namespace BrainAppeal\CampusEventsFrontend\Hooks;

use BrainAppeal\CampusEventsFrontend\Utility\TemplateLayout;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Userfunc to render alternative label for media elements
 */
#[Autoconfigure(public: true)]
class ItemsProcFunc
{
    public function __construct(
        private LanguageServiceFactory $languageServiceFactory,
        private TemplateLayout         $templateLayoutsUtility
    )
    {
    }

    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     *
     * @param array &$config configuration array
     */
    public function user_templateLayout(array &$config): void
    {
        $currentColPos = $config['flexParentDatabaseRow']['colPos'];
        $pageId = $this->getPageId((int)($config['flexParentDatabaseRow']['pid'] ?? 0));

        if ($pageId > 0) {
            $templateLayouts = $this->templateLayoutsUtility->getAvailableTemplateLayouts($pageId);

            $templateLayouts = $this->reduceTemplateLayouts($templateLayouts, $currentColPos);
            foreach ($templateLayouts as $layout) {
                $label = $layout['label'];
                if (str_starts_with((string)$layout['label'], 'LLL')) {
                    $label = $this->getLanguageService()->sL($label);
                }
                $additionalLayout = [
                    'label' => htmlspecialchars((string)$label),
                    'value' => $layout['value'],
                ];
                $config['items'][] = $additionalLayout;
            }
        }
    }

    /**
     * Reduce the template layouts by the ones that are not allowed in given colPos
     *
     * @param array<int<0,max>, array<string, string>> $templateLayouts
     * @param int $currentColPos
     * @return array
     */
    protected function reduceTemplateLayouts(array $templateLayouts, int $currentColPos): array
    {
        $currentColPos = (int)$currentColPos;
        $restrictions = [];
        $allLayouts = [];
        foreach ($templateLayouts as $key => $layout) {
            if (isset($layout['allowedColPos']) && str_ends_with($layout['value'] ?? '', '.')) {
                $layoutKey = substr((string)$layout['value'], 0, -1);
                $restrictions[$layoutKey] = GeneralUtility::intExplode(',', $layout['allowedColPos'], true);
            } else {
                $allLayouts[$key] = $layout;
            }
        }
        if ($restrictions !== []) {
            foreach ($restrictions as $restrictedIdentifier => $restrictedColPosList) {
                if (!in_array($currentColPos, $restrictedColPosList, true)) {
                    unset($allLayouts[$restrictedIdentifier]);
                }
            }
        }

        return $allLayouts;
    }

    /**
     * Get page id, if negative, then it is an "after record"
     *
     * @param int $pid
     * @return int
     */
    protected function getPageId(int $pid): int
    {
        if ($pid > 0) {
            return $pid;
        }

        $row = BackendUtilityCore::getRecord('tt_content', abs($pid), 'uid,pid');
        return (int)($row['pid'] ?? 0);
    }


    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'] ?? $this->languageServiceFactory->createFromUserPreferences($this->getBackendUser());
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
