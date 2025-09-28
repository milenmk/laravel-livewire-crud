<?php

declare(strict_types=1);

namespace Milenmk\LaravelCrud;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Trait to resolve model name.
 */
trait ModelResolver
{
    /**
     * Get the base namespace for models.
     */
    protected function getModelNamespace(): string
    {
        return 'App\\Models\\';
    }

    /**
     * @param  string  $modelName  Name of the model (e.g., 'User')
     */
    protected function resolveModel(string $modelName): string
    {

        $modelClass = $this->getModelNamespace().$modelName;

        if (! class_exists($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
            throw new InvalidArgumentException("Invalid model: $modelName");
        }

        return $modelClass;
    }
}
