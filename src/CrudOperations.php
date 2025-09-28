<?php

declare(strict_types=1);

namespace Milenmk\LaravelCrud;

use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Trait for CRUD Operations.
 */
trait CrudOperations
{
    use ModelResolver;

    /**
     * Get data for the model.
     *
     * @param  string  $modelClass  Model class
     */
    abstract protected function getData(string $modelClass): array;

    /**
     * Set model properties values from object.
     *
     * @param  string  $modelClass  Model class
     * @param  object  $object  Object
     */
    abstract protected function setDataFromObject(string $modelClass, object $object): void;

    /**
     * Show delete confirmation/modal.
     *
     * @param  int  $recordId  Record ID
     */
    public function commonDeleteData(string $modelName, int $recordId): void
    {
        $this->loadRecordForAction($modelName, $recordId);
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
                $idToUse = $this->getRecordId($recordId);

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
        $this->loadRecordForAction($modelName, $recordId);
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
                $idToUse = $this->getRecordId($recordId);

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
     * Get the record ID to use.
     */
    private function getRecordId(?int $recordId = null): int
    {
        $idToUse = $this->id ?? $recordId;

        if ($idToUse === null || $idToUse === 0) {
            throw new Exception('ID is not specified for the operation.');
        }

        return $idToUse;
    }

    /**
     * Load record for action and set data.
     */
    private function loadRecordForAction(string $modelName, int $recordId): void
    {
        $modelClass = $this->resolveModel($modelName);

        try {
            $object = $modelClass::findOrFail($recordId);

            if (property_exists($this, 'id') && is_null($this->id)) {
                $this->id = $object->id;
            }

            $this->setDataFromObject($modelClass, $object);
        } catch (Exception $e) {
            $this->logError($e, __FUNCTION__);
            $this->dispatchEvent('error', 'error', $e->getMessage());
        }
    }
}
