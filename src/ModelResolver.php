<?php

declare(strict_types=1);

namespace Milenmk\LaravelCrud;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Trait to resolve model name
 */
trait ModelResolver
{

    /**
     * @param  string  $modelName  Name of the model (e.g., 'User')
     *
     * @return string
     */
    protected function resolveModel(string $modelName): string
    {

        $modelClass = "App\\Models\\$modelName";

        if (!class_exists($modelClass) || !is_subclass_of($modelClass, Model::class)) {
            throw new InvalidArgumentException("Invalid model: $modelName");
        }

        return $modelClass;
    }

}
