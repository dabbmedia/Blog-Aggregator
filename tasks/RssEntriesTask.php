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
		require_once craft()->path->getPluginsPath().'rssentries/vendor/SimpleHTMLDom/simple_html_dom.php';

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

			foreach( $array['channel']['item'] as $position )
			{
				$record = [];

				RssEntriesPlugin::log('Monks '.json_encode($position), LogLevel::Info);

				$record['pubDate'] = $position['pubDate'];

				RssEntriesPlugin::log('Fetching URL: '.$position['link'], LogLevel::Info);

				$html = file_get_html($position['link']);
				$objJob = $html->find('table[id=JobDescription]', 0);

				foreach ($objJob->find('td[class=smallheader]') as $details) {

						$sibling = @strip_tags($details->next_sibling()->innertext, '<h1');
						$sibling = str_replace('&bull;', "<li>", $sibling);


						$strKey = trim(strip_tags($details->innertext));

						if($strKey == 'Category')
						{
							$criteria = craft()->elements->getCriteria(ElementType::Entry);
							$criteria->title = trim(str_replace('&nbsp;','',$sibling));
							$categories = craft()->elements->findElements($criteria);
							foreach( $categories as $category ){
								$categoryId = $category->id;
							}
							if(isset($categoryId)&&!empty($categoryId))
							{
								$mixValue = array($categoryId);
							}
						}
						else
						{
							$mixValue = trim(str_replace('&nbsp;','',$sibling));
						}

						$record[$this->keyFormatter($strKey)] = $mixValue;

				}

				$objLink = $html->find('form',0);
				$record['jobApplicationUrl'] = 'https://recruiting.myapps.paychex.com/appone/'.$objLink->action;

				$records[] = $record;
				sleep(1);
			}
		}

		// Delete all Entries in this channel
		$criteria = craft()->elements->getCriteria(ElementType::Entry);
		$criteria->sectionId = $route->channel;
		$criteria->limit = null;
		$entries = $criteria->find();
		foreach($entries as $entry){
			craft()->entries->deleteEntry($entry);
		}
		RssEntriesPlugin::log('Entries Count: '.count($entries), LogLevel::Info);

		RssEntriesPlugin::log('Writing '.count($records).' records.', LogLevel::Info);

		foreach($records as $record) {

			 $entry = new EntryModel();
			 $entry->sectionId   = $route->channel; // Use 'id' from 'craft_sections' table
			 $entry->enabled     = true;
			 $entry->postDate		 = date("Y-m-d H:i:s", strtotime($record['pubDate']));
			 /*
			 $entry->setContentFromPost(array(
    		'tagFieldHandle' => $myTagIds,
			));*/

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

	private function keyFormatter($strKey){

		switch($strKey){
			case 'Title':
				$return = 'title';
				break;
			case 'About the Organization':
				$return = 'jobOrgDescription';
				break;
			case 'Category':
				$return = 'jobDepartment';
				break;
			case 'Description':
				$return = 'jobDescription';
				break;
			case 'Position Requirements':
				$return = 'jobPositionRequirements';
				break;
			case 'Location':
				$return = 'jobLocation';
				break;
			case 'Full-Time/Part-Time':
				$return = 'jobFullTimePartTime';
				break;
			case 'Travel':
				$return = 'jobTravel';
				break;
			case 'Req Number':
				$return = 'jobReqNumber';
				break;
			case 'EOE Statement':
				$return = 'jobEoeStatement';
				break;
			default:
				$return = 'trash';
				break;
		}

		return $return;
	}

}
