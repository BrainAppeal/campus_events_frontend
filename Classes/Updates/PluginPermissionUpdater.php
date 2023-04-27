<?php
/**
 * campus_events_frontend comes with ABSOLUTELY NO WARRANTY
 * See the GNU GeneralPublic License for more details.
 * https://www.gnu.org/licenses/gpl-2.0
 *
 * Copyright (C) 2023 Brain Appeal GmbH
 *
 * @copyright 2023 Brain Appeal GmbH (www.brain-appeal.com)
 * @license   GPL-2 (www.gnu.org/licenses/gpl-2.0)
 * @link      https://www.campus-events.com/
 */


namespace BrainAppeal\CampusEventsFrontend\Updates;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class PluginPermissionUpdater implements UpgradeWizardInterface
{
    public function getIdentifier(): string
    {
        return 'txCampusEventsFrontendPluginPermissionUpdater';
    }

    public function getTitle(): string
    {
        return 'EXT:campus_events_frontend: Migrate plugin permissions';
    }

    public function getDescription(): string
    {
        $description = 'This update wizard updates all permissions and allows **all** CE frontend plugins instead of the previous single plugin.';
        $description .= ' Count of affected groups: ' . count($this->getMigrationRecords());
        return $description;
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    public function updateNecessary(): bool
    {
        return $this->checkIfWizardIsRequired();
    }

    public function executeUpdate(): bool
    {
        return $this->performMigration();
    }

    public function checkIfWizardIsRequired(): bool
    {
        return count($this->getMigrationRecords()) > 0;
    }

    public function performMigration(): bool
    {
        $records = $this->getMigrationRecords();

        foreach ($records as $record) {
            $this->updateRow($record);
        }

        return true;
    }

    protected function getMigrationRecords(): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('be_groups');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $listType = \BrainAppeal\CampusEventsFrontend\Updates\PluginUpdater::DEPRECATED_PLUGIN_LIST_TYPE;
        return $queryBuilder
            ->select('uid', 'explicit_allowdeny')
            ->from('be_groups')
            ->where(
                $queryBuilder->expr()->like(
                    'explicit_allowdeny',
                    $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards('tt_content:list_type:'.$listType) . '%')
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    protected function updateRow(array $row): void
    {
        $listType = \BrainAppeal\CampusEventsFrontend\Updates\PluginUpdater::DEPRECATED_PLUGIN_LIST_TYPE;
        $cTypePermissionLines = [];
        foreach (\BrainAppeal\CampusEventsFrontend\Updates\PluginUpdater::MIGRATION_SETTINGS as $migrateConf) {
            $cTypePermissionLines[] = 'tt_content:CType:' . $migrateConf['targetCType'];
        }
        $default = implode(',', $cTypePermissionLines);
        if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() >= 12) {
            $searchReplace = [
                'tt_content:list_type:'.$listType.':ALLOW' => $default,
                'tt_content:list_type:'.$listType.':DENY' => '',
                'tt_content:list_type:'.$listType => $default,
            ];
        } else {
            $default .= ',';
            $default = str_replace(',', ':ALLOW,', $default);
            $searchReplace = [
                'tt_content:list_type:'.$listType.':ALLOW' => $default,
                'tt_content:list_type:'.$listType.':DENY' => str_replace($default, 'ALLOW', 'DENY'),
            ];
        }

        $newList = str_replace(array_keys($searchReplace), array_values($searchReplace), $row['explicit_allowdeny']);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_groups');
        $queryBuilder->update('be_groups')
            ->set('explicit_allowdeny', $newList)
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter($row['uid'], Connection::PARAM_INT)
                )
            )
            ->executeStatement();
    }
}
