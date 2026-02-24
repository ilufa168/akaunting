<?php

namespace Modules\Outlets\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;

class Event extends Provider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        \Modules\Outlets\Listeners\Report\AddOutlets::class,
    ];

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        return [
            __DIR__ . '/../Listeners',
        ];
    }
}
