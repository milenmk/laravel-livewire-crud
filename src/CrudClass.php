<?php

declare(strict_types=1);

namespace Milenmk\LaravelCrud;

/**
 * CRUD class for Livewire components.
 */
trait CrudClass
{
    use BulkActions;
    use CrudOperations;
    use ErrorHandling;
    use LivewireSupport;
}
