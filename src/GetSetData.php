<?php

declare(strict_types=1);

namespace Milenmk\LaravelCrud;

use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Get or Set data for models
 */
trait GetSetData
{

    use CrudClass;

    /**
     * Get data for the model
     */
    protected function getData(Model $modelName): array
    {

        try {
            $model = app($modelName);

            // Use custom logic if the model defines it
            if (method_exists($model, 'getCrudData')) {
                return $model->getCrudData($this);
            }

            // Automatically map form properties based on fillable fields
            return array_intersect_key(
                $this->toArray(),
                array_flip($model->getFillable())
            );
        } catch (Exception $e) {
            $this->logError($e, __FUNCTION__);

            return [];
        }
    }

    /**
     * Set data from the object to the properties
     */
    protected function setDataFromObject(Model $modelName, object $object): void
    {

        try {
            $model = app($modelName);

            // Use custom logic if the model defines it
            if (method_exists($model, 'setCrudData')) {
                $model->setCrudData($this, $object);

                return;
            }

            // Automatic property assignment
            foreach ($object->getAttributes() as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        } catch (Exception $e) {
            $this->logError($e, __FUNCTION__);
        }
    }

}