
plugin.tx_campuseventsfrontend_event {
    view {
        templateRootPaths.0 = EXT:campus_events_frontend/Resources/Private/Templates/
        templateRootPaths.1 = {$plugin.tx_campuseventsfrontend_event.view.templateRootPath}
        partialRootPaths.0 = EXT:campus_events_frontend/Resources/Private/Partials/
        partialRootPaths.1 = {$plugin.tx_campuseventsfrontend_event.view.partialRootPath}
        layoutRootPaths.0 = EXT:campus_events_frontend/Resources/Private/Layouts/
        layoutRootPaths.1 = {$plugin.tx_campuseventsfrontend_event.view.layoutRootPath}
    }
    persistence {
        storagePid = {$plugin.tx_campuseventsfrontend_event.persistence.storagePid}
        #recursive = 1
    }
    features {
        #skipDefaultArguments = 1
        # if set to 1, the enable fields are ignored in BE context
        ignoreAllEnableFieldsInBe = 0
        # Should be on by default, but can be disabled if all action in the plugin are uncached
        requireCHashArgumentForActionArguments = 1
    }
    mvc {
        #callDefaultActionIfActionCantBeResolved = 1
    }
    settings {

        list {
            # Paginate configuration.
            paginate {
                itemsPerPage = {$plugin.tx_dhbwbachelorthesissearch_thesis.settings.list.paginate.itemsPerPage}
                insertAbove = 0
                insertBelow = 1
                lessPages = {$plugin.tx_dhbwbachelorthesissearch_thesis.settings.list.paginate.lessPages}
                morePages = {$plugin.tx_dhbwbachelorthesissearch_thesis.settings.list.paginate.morePages}
                #forcedNumberOfLinks = 5
                maximumNumberOfLinks = {$plugin.tx_dhbwbachelorthesissearch_thesis.settings.list.paginate.maximumNumberOfLinks}
                pagesBefore = {$plugin.tx_dhbwbachelorthesissearch_thesis.settings.list.paginate.pagesBefore}
                pagesAfter = {$plugin.tx_dhbwbachelorthesissearch_thesis.settings.list.paginate.pagesAfter}
            }
        }
    }
}