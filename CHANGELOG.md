## v1.4.1

#### Published at: 2025-09-28

- Added configurable model namespace in ModelResolver trait with fallback to App\Models\
- Improved data retrieval in GetSetData trait to support form->data fallback
- CRUD methods now handle UUID for model's ID
- Fix: Allow string IDs and improve form data handling

## v1.4.0

#### Published at: 2025-09-28

- feat: Refactor CrudClass and related traits for improved modularity and error handling
- feat: Update README with new features and contribution guidelines
- feat: Enhance codebase and update dependencies

## v1.3.2

#### Published at: 2025-03-04

- Updated README to include sample code when using Livewire Forms

## v1.3.1

#### Published at: 2025-03-04

- Fix bug in Model Resolver

## v1.3.0

#### Published at: 2025-03-03

- Add support for LivewireForms

## v1.2.0

#### Published at: 2025-03-03

- Improved event messaging. `dispatchEvent()` now also dispatches event type and event message

## v1.1.0

#### Published at: 2024-12-07

- Improved License and README
- [NEW] Introducing trait for Bulk actions
- [NEW] Class to resolve the model instance
- `GetSetData` now uses the model `$fillable` properties to get/set data for the CRUD class methods.

## v1.0.1

#### Published at: 2024-01-21

- [BUG FIX] Method `cancelAction()` is now public

## v1.0.0

#### Published at: 2024-01-21

- Initial release