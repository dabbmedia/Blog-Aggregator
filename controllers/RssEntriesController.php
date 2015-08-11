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
		$strChannelName = craft()->request->getParam('channel');
		if(empty($strChannelName))
		{
			$strChannelName = 'jobs';
		}
		$intChannelId = craft()->sections->getSectionByHandle('jobs')->id;
		$intTypeId = craft()->request->getParam('type');

		if(empty($intTypeId))
		{
			$intTypeId = '14';
		}

    craft()->tasks->createTask('RssEntries', 'Update "'.$strChannelName.'" Entries', array('channel'=>$intChannelId,'type'=>$intTypeId));

		echo 'Fetching RSS!';
		craft()->end();
	}
}
