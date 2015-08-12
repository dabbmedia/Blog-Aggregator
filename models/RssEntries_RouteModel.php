<?php

namespace Craft;

/**
 * Routes Model
 *
 * Provides a read-only object representing a route, which is returned
 * by our service class and can be used in our templates and controllers.
 */
class RssEntries_RouteModel extends BaseModel
{
    /**
     * Defines what is returned when someone puts {{ route }} directly
     * in their template.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->url;
    }

    public function getInterval() {

        switch($this->refresh){
          case '2':
            $return = '6hrs';
            break;
          case '1':
            $return = '12hrs';
            break;
          default:
            $return = '24hrs';
            break;
        }

        return $return;
    }

    /**
     * Define the attributes this model will have.
     *
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'id'      => AttributeType::Number,
            'url'     => AttributeType::String,
            'channel' => AttributeType::Number,
            'refresh' => AttributeType::Number,
        );
    }
}
