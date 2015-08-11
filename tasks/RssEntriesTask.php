<?php
namespace Craft;
/**
 * Power Nap task
 */
class RssEntriesTask extends BaseTask
{
	/**
	 * Defines the settings.
	 *
	 * @access protected
	 * @return array
	 */
	protected function defineSettings()
	{
		return array(
      'route'   => AttributeType::Number,
			'channel'	=> AttributeType::Number
		);
	}
	/**
	 * Returns the default description for this task.
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return 'Parsing RSS';
	}
	/**
	 * Gets the total number of steps for this task.
	 *
	 * @return int
	 */
	public function getTotalSteps()
	{
		return 1;
	}
	/**
	 * Runs a task step.
	 *
	 * @param int $step
	 * @return bool
	 */
	public function runStep($step)
	{
    if(isset($this->getSettings()->route)&&!empty($this->getSettings()->route))
    {
      RssEntriesPlugin::log('Loading Route: '.$this->getSettings()->route, LogLevel::Info);
      $routes[] = craft()->rssEntries->getRouteById($this->getSettings()->route);
    }
    else {
      RssEntriesPlugin::log('Loading Channel: '.$this->getSettings()->channel, LogLevel::Info);
      $routes = craft()->rssEntries->getAllRoutesForChannel($this->getSettings()->channel);
    }

		foreach($routes as $route)
		{
			RssEntriesPlugin::log('Fetching URL: '.$route->url, LogLevel::Info);
      $feed = implode(file($route->url));
      $xml = simplexml_load_string($feed);
      $json = json_encode($xml);
      $array = json_decode($json,TRUE);

			foreach( $array['channel']['item'] as $item )
			{
				$record = [];
        RssEntriesPlugin::log('Fetching URL: '.json_encode($item), LogLevel::Info);

        $record['title'] = $item['title'];
        $record['description'] = $item['description'];

				$records[] = $record;
			}
		}

		RssEntriesPlugin::log('Writing '.count($records).' records.', LogLevel::Info);

		foreach($records as $record) {

			 $entry = new EntryModel();
			 $entry->sectionId   = $route->channel; // Use 'id' from 'craft_sections' table
			 $entry->enabled     = true;

			 $entry->getContent()->setAttributes($record);

			 // Save entry
			 $success = craft()->entries->saveEntry($entry);

			 if (!$success)
			 {
					 $errors = $entry->getErrors();
					 foreach ($errors as $error) {
							 RssEntriesPlugin::log('Error:'.$error[0], LogLevel::Error);
					 }
					 return false;
			 }
			 else
			 {
					 RssEntriesPlugin::log('Successfully saved entry "'.$entry->id.'"', LogLevel::Info);
			 }
		}

		return true;
	}

}
