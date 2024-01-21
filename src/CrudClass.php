<?php

declare(strict_types = 1);

namespace Milenmk\LaravelCrud;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * CRUD class for Livewire components
 */
trait CrudClass
{

    /**
     * Store new data in the database
     */
    public function commonStoreData(string $modelName): void
    {

        $this->validate();

        try {
            $modelClass = 'App\Models\\' . $modelName;
            $object = $modelClass::create($this->getData(ucfirst($modelName)));
            $this->dispatch('created');
        } catch (Exception $exc) {
            Log::error($exc->getMessage() . ' for ' . __CLASS__ . '::' . __FUNCTION__);
            $this->dispatch('error');
        }

        $this->cancelAction();
    }

    /**
     * Get data for the model
     *
     * @param string $modelName Mode name
     */
    abstract protected function getData(string $modelName): array;

    /**
     * Reset input fields, errors and validations
     *
     * @return void
     */
    public function cancelAction(): void
    {

        $this->reset();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Get data for an edit form
     *
     * @param int $recordId Record ID
     */
    public function commonEditData(string $modelName, int $recordId): void
    {

        try {
            $modelClass = 'App\Models\\' . $modelName;
            $object = $modelClass::findOrFail($recordId);
            if ($object) {
                $this->id = $object->id;
                $this->setDataFromObject(ucfirst($modelName), $object);
            } else {
                $this->dispatch('warning');
                $this->cancelAction();
            }
        } catch (Exception $exc) {
            Log::error($exc->getMessage() . ' for ' . __CLASS__ . '::' . __FUNCTION__);
            $this->dispatch('error');
        }
    }

    /**
     * Set model properties values from object
     *
     * @param string $modelName Model name
     * @param object $object    Object
     */
    abstract protected function setDataFromObject(string $modelName, object $object): void;

    /**
     * Update data in the database
     */
    public function commonUpdateData(string $modelName): void
    {

        $this->validate();

        try {
            $modelClass = 'App\Models\\' . $modelName;
            $object = $modelClass::findOrFail($this->id);
            $update = $object->update($this->getData(ucfirst($modelName)));
            $this->dispatch('updated');
        } catch (Exception $exc) {
            Log::error($exc->getMessage() . ' for ' . __CLASS__ . '::' . __FUNCTION__);
            $this->dispatch('error');
        }

        $this->cancelAction();
    }

    /**
     * Show delete confirmation/modal
     *
     * @param int $recordId Record ID
     */
    public function commonDeleteData(string $modelName, int $recordId): void
    {

        $this->useCachedRows();

        try {
            $modelClass = 'App\Models\\' . $modelName;
            $object = $modelClass::findOrFail($recordId);
            if ($object) {
                $this->id = $object->id;
                $this->setDataFromObject(ucfirst($modelName), $object);
            } else {
                $this->dispatch('warning');
                $this->cancelAction();
            }
        } catch (Exception $exc) {
            Log::error($exc->getMessage() . ' for ' . __CLASS__ . '::' . __FUNCTION__);
            $this->dispatch('error');
        }
    }

    /**
     * Delete data from database
     *
     */
    public function commonDestroyData(string $modelName): void
    {

        try {
            $modelClass = 'App\Models\\' . $modelName;
            $object = $modelClass::findOrFail($this->id);
            $delete = $object->delete();
            $this->dispatch('deleted');
        } catch (Exception $exc) {
            Log::error($exc->getMessage() . ' for ' . __CLASS__ . '::' . __FUNCTION__);
            $this->dispatch('error');
        }

        $this->cancelAction();
    }

    /**
     * Bulk delete data from database
     *
     */
    public function commonBulkDestroyData(string $modelName): void
    {

        $object = null;
        $errors = [];
        $error = 0;

        foreach ($this->selectedItems as $item) {
            try {
                $modelClass = 'App\Models\\' . $modelName;
                $object = $modelClass::find($item);
                $object->delete();
            } catch (Exception $exc) {
                Log::error($exc->getMessage() . ' for ' . __CLASS__ . '::' . __FUNCTION__);
                $errors[] = $exc;
                $error++;
            }

            if ($error || $object === null || $object === false) {
                $this->dispatch('error');
            }
        }

        if (!$error || $object !== null) {
            $this->dispatch('bulk-deleted');
        }

        $this->cancelAction();
    }

}