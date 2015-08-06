<?php

namespace Craft;

/**
 * RSS Entries Service
 *
 * Provides a consistent API for our plugin to access the database
 */
class RssEntriesService extends BaseApplicationComponent
{
    protected $routeRecord;

    /**
     * Create a new instance of the RSS Entries Service.
     * Constructor allows RouteRecord dependency to be injected to assist with unit testing.
     *
     * @param @routeRecord RouteRecord The route record to access the database
     */
    public function __construct($routeRecord = null)
    {
        $this->routeRecord = $routeRecord;
        if (is_null($this->routeRecord)) {
            $this->routeRecord = RssEntries_RouteRecord::model();
        }
    }

    /**
     * Get a new blank route
     *
     * @param  array                           $attributes
     * @return RssEntries_RouteModel
     */
    public function newRoute($attributes = array())
    {
        $model = new RssEntries_RouteModel();
        $model->setAttributes($attributes);

        return $model;
    }

    /**
     * Get all routes from the database.
     *
     * @return array
     */
    public function getAllRoutes()
    {
        $records = $this->routeRecord->findAll(array('order'=>'t.id'));

        return RssEntries_RouteModel::populateModels($records, 'id');
    }

    /**
     * Get a specific route from the database based on ID. If no route exists, null is returned.
     *
     * @param  int   $id
     * @return mixed
     */
    public function getRouteById($id)
    {
        if ($record = $this->routeRecord->findByPk($id)) {
            return RssEntries_RouteModel::populateModel($record);
        }
    }

    /**
     * Save a new or existing route back to the database.
     *
     * @param  RssEntries_RouteModel $model
     * @return bool
     */
    public function saveRoute(RssEntries_RouteModel &$model)
    {
        if ($id = $model->getAttribute('id')) {
            if (null === ($record = $this->routeRecord->findByPk($id))) {
                throw new Exception(Craft::t('Can\'t find route with ID "{id}"', array('id' => $id)));
            }
        } else {
            $record = $this->routeRecord->create();
        }

        $record->setAttributes($model->getAttributes());
        if ($record->save()) {
            // update id on model (for new records)
            $model->setAttribute('id', $record->getAttribute('id'));

            return true;
        } else {
            $model->addErrors($record->getErrors());

            return false;
        }
    }

    /**
     * Delete an route from the database.
     *
     * @param  int $id
     * @return int The number of rows affected
     */
    public function deleteRouteById($id)
    {
        return $this->routeRecord->deleteByPk($id);
    }
}
