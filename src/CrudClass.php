<?php

declare(strict_types=1);

namespace Milenmk\LaravelCrud;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * CRUD class for Livewire components.
 */
trait CrudClass
{
    use BulkActions;
    use ModelResolver;

    /**
     * Get data for the model.
     *
     * @param  string  $modelName  Mode name
     */
    abstract protected function getData(string $modelName): array;

    /**
     * Set model properties values from object.
     *
     * @param  string  $modelName  Model name
     * @param  object  $object  Object
     */
    abstract protected function setDataFromObject(string $modelName, object $object): void;

    /**
     * Show delete confirmation/modal.
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
     * Delete data from database.
     *
     * @param  int|null  $recordId  Record ID
     *
     * @throws Throwable
     */
    public function commonDestroyData(string $modelName, ?int $recordId = null): void
    {

        $modelClass = $this->resolveModel($modelName);

        DB::transaction(function () use ($recordId, $modelClass): void {

            try {
                // Check if $id is set and use it, otherwise fallback to $recordId.
                $idToUse = $this->id ?? $recordId;

                // Ensure that either $this->id or $recordId is available for findOrFail
                if ($idToUse === null || $idToUse === 0) {
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

    /**
     * Get data for an edit form.
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
     * Store new data in the database.
     *
     * @throws Throwable
     */
    public function commonStoreData(string $modelName): void
    {

        $this->validateIfAvailable();

        $modelClass = $this->resolveModel($modelName);

        $data = $this->getData($modelClass);

        if (empty($data)) {
            $this->dispatchEvent('error', 'error', __('No data to store.'));

            return;
        }

        DB::transaction(function () use ($modelClass, $data): void {

            try {
                $modelClass::create($data);
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
     * Update data in the database.
     *
     * @param  int|null  $recordId  Record ID
     *
     * @throws Throwable
     */
    public function commonUpdateData(string $modelName, ?int $recordId = null): void
    {

        $this->validateIfAvailable();

        $modelClass = $this->resolveModel($modelName);

        $data = $this->getData($modelClass);

        if (empty($data)) {
            $this->dispatchEvent('error', 'error', __('No data to update.'));

            return;
        }

        DB::transaction(function () use ($recordId, $modelClass, $data): void {

            try {
                // Check if $id is set and use it, otherwise fallback to $recordId.
                $idToUse = $this->id ?? $recordId;

                // Ensure that either $this->id or $recordId is available for findOrFail
                if ($idToUse === null || $idToUse === 0) {
                    throw new Exception('ID is not specified for updating the record.');
                }

                $object = $modelClass::findOrFail($idToUse);
                $object->update($data);
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
     * Reset input fields and validation if Livewire methods exist.
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
     * Handle event dispatching.
     */
    protected function dispatchEvent(string $eventName, string $type = '', string|array|null $message = null): void
    {

        if (method_exists($this, 'dispatch')) {
            $this->dispatch($eventName, type: $type, title: $message); // Livewire
        } else {
            event($eventName, ['type' => $type, 'message' => $message]); // Laravel Controller
        }
    }

    /**
     * Insert error(s) message into the app .log file.
     */
    protected function logError(Exception $e, string $method): void
    {

        Log::error(
            "[$method] Error: {$e->getMessage()}",
            ['class' => self::class, 'user_id' => auth()->id()]
        );
    }

    /**
     * Validate only if the method exists.
     */
    protected function validateIfAvailable(): void
    {

        if (method_exists($this, 'validate')) {
            $this->validate();
        }
    }
}
