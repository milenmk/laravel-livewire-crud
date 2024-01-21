<?php

declare(strict_types = 1);

namespace Milenmk\LaravelCrud;

/**
 * Get or Set data for models
 */
trait GetSetData
{

    use CrudClass;

    /**
     * Get data for the model
     */
    protected function getData(string $modelName): array
    {

        return match ($modelName) {
            'User'         => [
                'username' => $this->username,
                'email'    => $this->email,
            ],
            'AnotherModel' => [
                'property1' => $this->property1,
                'property2' => $this->property2,
            ],
            default        => [],
        };
    }

    /**
     * Set data from the object to the properties
     */
    protected function setDataFromObject(string $modelName, object $object): void
    {

        switch ($modelName) {
            case 'User':
                $this->username = $object->username;
                $this->email = $object->email;
                break;
            case 'AnotherModel':
                $this->property1 = $object->property1;
                $this->property2 = $object->property2;
                break;
        }
    }

}