<?php

declare(strict_types=1);

namespace Milenmk\LaravelCrud;

use Exception;

/**
 * Get or Set data for models.
 */
trait GetSetData
{
    use CrudClass;

    /**
     * Get data for the model.
     */
    protected function getData(string $modelClass): array
    {

        try {
            $model = app($modelClass);

            // Check if Livewire Forms is being used
            $data = property_exists($this, 'form') && method_exists($this->form, 'all') ? $this->form->all() : $this->toArray();

            // Use custom logic if the model defines it
            if (method_exists($model, 'getCrudData')) {
                return $model->getCrudData($this);
            }

            // Automatically map form properties based on fillable fields
            return array_intersect_key(
                $data,
                array_flip($model->getFillable())
            );
        } catch (Exception $e) {
            $this->logError($e, __FUNCTION__);

            return [];
        }
    }

    /**
     * Set data from the object to the properties.
     */
    protected function setDataFromObject(string $modelClass, object $object): void
    {

        try {
            $model = app($modelClass);

            // Use custom logic if the model defines it
            if (method_exists($model, 'setCrudData')) {
                $model->setCrudData($this, $object);

                return;
            }

            // Check if Livewire Forms is being used
            if (property_exists($this, 'form') && method_exists($this->form, 'fill')) {
                $this->form->fill($object->getAttributes());
            } else {
                // Automatic property assignment for standard Livewire components
                foreach ($object->getAttributes() as $key => $value) {
                    if (property_exists($this, $key)) {
                        $this->{$key} = $value;
                    }
                }
            }
        } catch (Exception $e) {
            $this->logError($e, __FUNCTION__);
        }
    }
}
