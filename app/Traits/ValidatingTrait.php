<?php

declare(strict_types=1);

namespace App\Traits;

use Watson\Validating\ValidatingTrait as BaseValidatingTrait;

trait ValidatingTrait
{
    use BaseValidatingTrait;

    protected $validationErrors;

    /**
     * Register a validating event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function validating($callback)
    {
        static::registerModelEvent('validating', $callback);
    }

    /**
     * Register a validated event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function validated($callback)
    {
        static::registerModelEvent('validated', $callback);
    }
}
