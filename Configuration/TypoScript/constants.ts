
plugin.tx_campuseventsfrontend_event {
    view {
        # cat=plugin.tx_campuseventsfrontend_event/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:campus_events_frontend/Resources/Private/Templates/
        # cat=plugin.tx_campuseventsfrontend_event/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:campus_events_frontend/Resources/Private/Partials/
        # cat=plugin.tx_campuseventsfrontend_event/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:campus_events_frontend/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.tx_campuseventsfrontend_event//a; type=string; label=Default storage PID
        storagePid =
    }
    settings {

        list {
            # Paginate configuration.
            paginate {
                itemsPerPage = 100
                insertAbove = 0
                insertBelow = 1
                lessPages = 1
                morePages = 1
                #forcedNumberOfLinks = 5
                maximumNumberOfLinks = 10
                pagesBefore = 3
                pagesAfter = 3
            }
        }
    }
}
