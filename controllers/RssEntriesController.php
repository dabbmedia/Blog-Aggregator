<?php
namespace Craft;

/**
 * RSS Entries controller
 */
class RssEntriesController extends BaseController
{
	/**
	 * Fetches Entries.
	 */
	public function actionStart()
	{
    $intRouteId = craft()->request->getParam('route');
		$intChannelId = craft()->request->getParam('channel');

    craft()->tasks->createTask('RssEntries', 'Update Entries from RSS', array('route'=>$intRouteId,'channel'=>$intChannelId));

		craft()->end();
	}
}
