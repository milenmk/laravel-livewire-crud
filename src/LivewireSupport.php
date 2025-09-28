<?php

declare(strict_types=1);

namespace Milenmk\LaravelCrud;

/**
 * Trait for Livewire Support utilities.
 */
trait LivewireSupport
{
    /**
     * Reset input fields and validation if Livewire methods exist.
     */
    protected function cancelActionIfAvailable(): void
    {
        if (method_exists($this, 'reset')) {
            $this->reset();
        }

        if (method_exists($this, 'resetErrorBag')) {
            $this->resetErrorBag();
        }

        if (method_exists($this, 'resetValidation')) {
            $this->resetValidation();
        }
    }

    /**
     * Handle event dispatching.
     */
    protected function dispatchEvent(string $eventName, string $type = '', string|array|null $message = null): void
    {
        if (method_exists($this, 'dispatch')) {
            $this->dispatch($eventName, type: $type, title: $message); // Livewire
        } else {
            event($eventName, ['type' => $type, 'message' => $message]); // Laravel Controller
        }
    }

    /**
     * Validate only if the method exists.
     */
    protected function validateIfAvailable(): void
    {
        if (method_exists($this, 'validate')) {
            $this->validate();
        }
    }
}
