<?php

namespace Craft;

/**
 * RSS Entries Variable provides access to database objects from templates
 */
class RssEntriesVariable
{
    /**
     * Get all available routes
     *
     * @return array
     */
    public function getAllRoutes()
    {
        return craft()->rssEntries->getAllRoutes();
    }

    /**
     * Get a specific route. If no route is found, returns null
     *
     * @param  int   $id
     * @return mixed
     */
    public function getRouteById($id)
    {
        return craft()->rssEntries->getRouteById($id);
    }
}
