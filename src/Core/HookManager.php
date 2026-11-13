<?php

declare(strict_types=1);

namespace Crm\Core;

final class HookManager
{
    private array $actions = [];
    private array $filters = [];
    private int $sequence = 0;

    public function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        $this->actions[$hook][] = [
            'callback' => $callback,
            'priority' => $priority,
            'sequence' => $this->sequence++,
        ];
    }

    public function doAction(string $hook, mixed ...$arguments): void
    {
        foreach ($this->callbacks($this->actions, $hook) as $callback) {
            $callback(...$arguments);
        }
    }

    public function listen(string $event, callable $listener, int $priority = 10): void
    {
        $this->addAction($event, $listener, $priority);
    }

    public function dispatch(string $event, mixed ...$payload): void
    {
        $this->doAction($event, ...$payload);
    }

    public function addFilter(string $hook, callable $callback, int $priority = 10): void
    {
        $this->filters[$hook][] = [
            'callback' => $callback,
            'priority' => $priority,
            'sequence' => $this->sequence++,
        ];
    }

    public function applyFilters(string $hook, mixed $value, mixed ...$arguments): mixed
    {
        foreach ($this->callbacks($this->filters, $hook) as $callback) {
            $value = $callback($value, ...$arguments);
        }

        return $value;
    }

    private function callbacks(array $registry, string $hook): array
    {
        $callbacks = $registry[$hook] ?? [];
        usort($callbacks, static fn (array $a, array $b): int =>
            [$a['priority'], $a['sequence']] <=> [$b['priority'], $b['sequence']]
        );

        return array_column($callbacks, 'callback');
    }
}
