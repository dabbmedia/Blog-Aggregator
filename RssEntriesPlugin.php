<?php
namespace Craft;

class RssEntriesPlugin extends BasePlugin
{
    function getName()
    {
         return Craft::t('RSS Entries');
    }

    function getVersion()
    {
        return '1.0';
    }

    function getDeveloper()
    {
        return 'IMM';
    }

    function getDeveloperUrl()
    {
        return 'http://imm.com';
    }

    public function hasCpSection()
    {
        return true;
    }

    public function registerCpRoutes()
    {
        return array(
            'rssentries\/routes\/new' => 'rssentries/routes/_edit',
            'rssentries\/routes\/(?P<routeId>\d+)' => 'rssentries/routes/_edit',
       );
    }
}
