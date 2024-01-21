## About

This package handles the logic for all CRUD operations you may need for your Laravel controllers and/or Livewire components

## Requirements

min. PHP version: 8.2

## Install

1. Run ```composer require milenmk/laravel-livewire-crud``` to install the package

## Usage

1. Create a new Trait, for example named GetSetData, and insert ``use CrudClass;`` in the beginning
2. The GetSetData file will be used to set the values for the properties when doing create or edit. You can see an example file in ``src/GetSetData.php``
3. In your controller/component insert ``use GetSetData;`` in the beginning
4. Reference the crud methods to the corresponding methods in CrudClass and pass the model name as parameter. For example, if you have a ``Client`` component and `Client` model,
   the Livewire component may look like this:

```
<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use App\Livewire\CommonComponent;
use App\Models\Client;
use App\Traits\GetSetData;

final class Clients extends CommonComponent
{
    use GetSetData;
    
    public function storeClient(): void
    {
        $this->rules = [
            'company' => 'required|min:3',
            'country' => 'nullable',
            'city' => 'nullable',
            'zip' => 'nullable',
            'address' => 'nullable',
            'phone' => 'nullable',
            'fax' => 'nullable',
            'mobile' => 'nullable',
            'email' => 'nullable',
        ];

        $this->commonStoreData('Client');
    }
    
    public function editClient(int $clientId): void
    {
        $this->commonEditData('Client', $clientId);
    }

    public function updateClient(): void
    {
        $this->rules = [
            'company' => 'required|min:3',
            'country' => 'nullable',
            'city' => 'nullable',
            'zip' => 'nullable',
            'address' => 'nullable',
            'phone' => 'nullable',
            'fax' => 'nullable',
            'mobile' => 'nullable',
            'email' => 'nullable',
            'status' => 'required',
        ];

        $this->commonUpdateData('Client');
    }
    
    public function deleteClient(int $clientId): void
    {
        $this->commonDeleteData('Client', $clientId);
    }
    
    public function destroyClient(): void
    {
        $this->commonDestroyData('Client');
    }

    public function bulkDestroyClients(): void
    {
        $this->commonBulkDestroyData('Client');
    }
    
}
```

5. To use the BulkDelete option, you must have a property named ``selectedItems`` in your component:

```
/**
 * Array of selected items for bulk action
 *
 */
public array $selectedItems = [];
```