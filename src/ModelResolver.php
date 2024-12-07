<?php

declare(strict_types = 1);

namespace Milenmk\LaravelCrud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;

/**
 * Trait to resolve model name
 */
trait ModelResolver
{

    /**
     * @param string $modelName Name of the model (e.g., 'User')
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function resolveModel(string $modelName): Model
    {

        $modelClass = App::make("App\\Models\\$modelName");

        if (!is_subclass_of($modelClass, Model::class)) {
            throw new InvalidArgumentException("Invalid model: $modelName");
        }

        return $modelClass;
    }

}
