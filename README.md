## About

This package provides a comprehensive solution for CRUD operations within your Laravel applications, offering a 
streamlined and reusable approach for managing data.
Whether you're working with Laravel controllers or Livewire components, this package allows you to:

- Simplify common CRUD operations (create, read, update, delete) by abstracting and consolidating logic 
and reducing boilerplate code.
- Enhance productivity by providing out-of-the-box methods for storing, updating, and deleting records.
- Integrate seamlessly with both Laravel and Livewire, making it easy to use in traditional controllers 
or real-time components.
- Ensure consistency across your application with standardized CRUD methods for various models.
- Support bulk actions, enabling efficient mass deletion or updates of records.
- Reduce development time by leveraging reusable methods for model interactions, validation and error handling.

This package is ideal for developers who want to focus on business logic rather than repetitive CRUD operations, 
saving time and minimizing errors.

Since version 1.3.0, the package also support Livewire Forms. Just move the corresponding logic (validation rules and
CRUD methods from the component to the form)

## Requirements

- PHP 8.1 or higher
- Laravel 9.x or higher
- Livewire 3.x or higher

## Install

Run ```composer require milenmk/laravel-livewire-crud``` to install the package

## Usage

1. In your controller or Livewire component, include the `GetSetData` trait:

```
use Milenmk\LaravelCrud\GetSetData;
```

2. Reference the CRUD methods from the CrudClass trait, passing the model name as a parameter. Here's an example with a Client model and a Livewire component:

```
<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use App\Livewire\CommonComponent;
use App\Models\Client;
use Milenmk\LaravelCrud\GetSetData;

final class Clients extends CommonComponent
{
    use GetSetData;

    // Store new client
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

    // Edit existing client
    public function editClient(int $clientId): void
    {
        $this->commonEditData('Client', $clientId);
    }

    // Update client information
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

    // Delete client
    public function deleteClient(int $clientId): void
    {
        $this->commonDeleteData('Client', $clientId);
    }

    // Destroy client (permanent delete)
    public function destroyClient(): void
    {
        $this->commonDestroyData('Client');
    }

    // Bulk delete clients
    public function bulkDestroyClients(): void
    {
        $this->commonBulkDestroyData('Client', $this->selectedItems);
    }

    /**
     * Array of selected items for bulk action
     */
    public array $selectedItems = [];
}

```

3. To use the Bulk Delete option, define a `selectedItems` property in your Livewire component, which should be an array of selected record IDs:

```
/**
 * Array of selected items for bulk action
 *
 */
public array $selectedItems = [];
```

## Methods Overview

- `commonStoreData('Model')` stores a new record in the database.
- `commonEditData('Model', $recordId)` retrieves data for editing a specific record by ID.
- `commonUpdateData('Model')` updates a specific record in the database.
- `commonDeleteData('Model', $recordId)` marks a specific record for deletion (soft delete).
- `commonDestroyData('Model')` permanently deletes a specific record from the database.
- `commonBulkDestroyData('Model', $selectedItems)` bulk deletes records based on an array of IDs.

## Additional Information

### Livewire Considerations

- If you are using Livewire, ensure that you are using dispatch for event handling as it integrates with Livewire's event system.
- For Livewire components, methods like reset(), resetErrorBag(), and resetValidation() will be triggered if they exist in the component.

### Laravel Controller Considerations

For regular Laravel controllers, the event() function is used for an event dispatching, as Livewire-specific methods like dispatch() are not available.

## Example of a Laravel Controller

```
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Client;
use Milenmk\LaravelCrud\GetSetData;

class ClientController extends Controller
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

    public function bulkDestroyClients(array $clientIds): void
    {
        $this->commonBulkDestroyData('Client', $clientIds);
    }
}

```

## DISCLAIMER

This package is provided ”as is”, without warranty of any kind, either express or implied, including but not limited to the warranties of merchantability, fitness for a particular
purpose, or noninfringement.

The author(s) make no representations or warranties regarding the accuracy, reliability or completeness of the code or its suitability for any specific use case. It is recommended
that you thoroughly test this package in your environment before deploying it to production.

By using this package, you acknowledge and agree that the author(s) shall not be held liable for any damages, losses or other issues arising from the use of this software.

## Contributing

You can review the source code, report bugs, or contribute to the project by visiting the GitHub repository:

[GitHub Repository](https://github.com/milenmk/laravel-livewire-crud)

Feel free to open issues or submit pull requests. Contributions are welcome!

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
