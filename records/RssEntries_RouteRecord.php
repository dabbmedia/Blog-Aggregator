<?php

namespace Craft;

/**
 * Route Record
 *
 * Provides a definition of the database tables required by our plugin,
 * and methods for updating the database. This class should only be called
 * by our service layer, to ensure a consistent API for the rest of the
 * application to use.
 */
class RssEntries_RouteRecord extends BaseRecord
{
    /**
     * Gets the database table name
     *
     * @return string
     */
    public function getTableName()
    {
        return 'rssentries_routes';
    }

    /**
     * Define columns for our database table
     *
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'url' => array(AttributeType::String, 'required' => true, 'unique' => true),
            'channel' => array(AttributeType::Number, 'required' => true, 'unique' => false),
            'refresh' => array(AttributeType::Number, 'required' => true, 'unique' => false),
        );
    }

    /**
     * Create a new instance of the current class. This allows us to
     * properly unit test our service layer.
     *
     * @return BaseRecord
     */
    public function create()
    {
        $class = get_class($this);
        $record = new $class();

        return $record;
    }
}
