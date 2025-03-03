<?php

declare(strict_types=1);

namespace Milenmk\LaravelCrud;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * CRUD class for Livewire components
 */
trait CrudClass
{

    use BulkActions;
    use ModelResolver;

    /**
     * Store new data in the database
     * @throws Throwable
     */
    public function commonStoreData(string $modelName): void
    {

        $this->validateIfAvailable();

        $modelClass = $this->resolveModel($modelName);

        DB::transaction(function () use ($modelClass) {

            try {
                $modelClass::create($this->getData($modelClass));
                $this->dispatchEvent('created', 'success', __('Record created'));
            } catch (Exception $e) {
                $this->logError($e, __FUNCTION__);
                $this->dispatchEvent('error', 'error', $e->getMessage());
                throw $e;
            }
        });

        $this->cancelActionIfAvailable();
    }

    /**
     * Validate only if the method exists
     */
    protected function validateIfAvailable(): void
    {

        if (method_exists($this, 'validate')) {
            $this->validate();
        }
    }

    /**
     * Get data for the model
     *
     * @param  Model  $modelName  Mode name
     */
    abstract protected function getData(Model $modelName): array;

    /**
     * Handle event dispatching
     */
    protected function dispatchEvent(string $eventName, string $type = '', string|array $message = null): void
    {

        if (method_exists($this, 'dispatch')) {
            $this->dispatch($eventName, type: $type, title: $message); // Livewire
        } else {
            event($eventName, ['type' => $type, 'message' => $message]); // Laravel Controller
        }
    }

    /**
     * Insert error(s) message into the app .log file
     *
     * @param  Exception  $e
     * @param  string  $method
     *
     * @return void
     */
    protected function logError(Exception $e, string $method): void
    {

        Log::error(
            "[$method] Error: {$e->getMessage()}",
            ['class' => __CLASS__, 'user_id' => auth()->id()]
        );
    }

    /**
     * Reset input fields and validation if Livewire methods exist
     */
    protected function cancelActionIfAvailable(): void
    {

        if (method_exists($this, 'reset')) {
            $this->reset();
        }

        if (method_exists($this, 'resetErrorBag')) {
            $this->resetErrorBag();
        }

        if (method_exists($this, 'resetValidation')) {
            $this->resetValidation();
        }
    }

    /**
     * Get data for an edit form
     *
     * @param  int  $recordId  Record ID
     */
    public function commonEditData(string $modelName, int $recordId): void
    {

        $modelClass = $this->resolveModel($modelName);

        try {
            $object = $modelClass::findOrFail($recordId);

            if (property_exists($this, 'id') && is_null($this->id)) {
                $this->id = $object->id;  // Set the ID if it is not set already
            }

            $this->setDataFromObject($modelClass, $object);
        } catch (Exception $e) {
            $this->logError($e, __FUNCTION__);
            $this->dispatchEvent('error', 'error', $e->getMessage());
        }
    }

    /**
     * Set model properties values from object
     *
     * @param  Model  $modelName  Model name
     * @param  object  $object  Object
     */
    abstract protected function setDataFromObject(Model $modelName, object $object): void;

    /**
     * Update data in the database
     *
     * @param  string  $modelName
     * @param  int|null  $recordId  Record ID
     * @throws Throwable
     */
    public function commonUpdateData(string $modelName, int $recordId = null): void
    {

        $this->validateIfAvailable();

        $modelClass = $this->resolveModel($modelName);

        DB::transaction(function () use ($recordId, $modelClass) {

            try {
                // Check if $id is set and use it, otherwise fallback to $recordId.
                $idToUse = $this->id ?? $recordId;

                // Ensure that either $this->id or $recordId is available for findOrFail
                if (!$idToUse) {
                    throw new Exception('ID is not specified for updating the record.');
                }

                $object = $modelClass::findOrFail($idToUse);
                $object->update($this->getData($modelClass));
                $this->dispatchEvent('updated', 'success', __('Record updated'));
            } catch (Exception $e) {
                $this->logError($e, __FUNCTION__);
                $this->dispatchEvent('error', 'error', $e->getMessage());
                throw $e;
            }
        });

        $this->cancelActionIfAvailable();
    }

    /**
     * Show delete confirmation/modal
     *
     * @param  int  $recordId  Record ID
     */
    public function commonDeleteData(string $modelName, int $recordId): void
    {

        $modelClass = $this->resolveModel($modelName);

        try {
            $object = $modelClass::findOrFail($recordId);

            if (property_exists($this, 'id') && is_null($this->id)) {
                $this->id = $object->id;  // Set the ID if it is not set already
            }

            $this->setDataFromObject($modelClass, $object);
        } catch (Exception $e) {
            $this->logError($e, __FUNCTION__);
            $this->dispatchEvent('error', 'error', $e->getMessage());
        }
    }

    /**
     * Delete data from database
     *
     * @param  string  $modelName
     * @param  int|null  $recordId  Record ID
     * @throws Throwable
     */
    public function commonDestroyData(string $modelName, int $recordId = null): void
    {

        $modelClass = $this->resolveModel($modelName);

        DB::transaction(function () use ($recordId, $modelClass) {

            try {
                // Check if $id is set and use it, otherwise fallback to $recordId.
                $idToUse = $this->id ?? $recordId;

                // Ensure that either $this->id or $recordId is available for findOrFail
                if (!$idToUse) {
                    throw new Exception('ID is not specified for updating the record.');
                }

                $object = $modelClass::findOrFail($idToUse);
                $object->delete();
                $this->dispatchEvent('deleted', 'success', __('Record deleted'));
            } catch (Exception $e) {
                $this->logError($e, __FUNCTION__);
                $this->dispatchEvent('error', 'error', $e->getMessage());
                throw $e;
            }
        });

        $this->cancelActionIfAvailable();
    }

}