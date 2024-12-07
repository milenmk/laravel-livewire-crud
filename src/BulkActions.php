<?php

declare(strict_types = 1);

namespace Milenmk\LaravelCrud;

use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Trait for Bulk Actions
 */
trait BulkActions
{

    /**
     * Bulk delete data from database
     *
     * @param string $modelName
     * @param array  $recordsIds
     *
     * @return void
     */
    public function commonBulkDestroyData(string $modelName, array $recordsIds): void
    {

        $modelClass = $this->resolveModel($modelName);
        $errors = 0;

        DB::transaction(function () use ($modelClass, $recordsIds, &$errors) {

            foreach ($recordsIds as $recordId) {
                try {
                    $modelClass::findOrFail($recordId)->delete();
                } catch (Exception $e) {
                    $this->logError($e, __FUNCTION__);
                    $errors++;
                }
            }
        });

        $this->dispatchEvent($errors ? 'bulk-delete-failed' : 'bulk-deleted');
        $this->cancelActionIfAvailable();
    }

}