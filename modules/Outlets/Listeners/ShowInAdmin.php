<?php

namespace Modules\Outlets\Listeners;

use App\Events\Menu\AdminCreated as Event;
use App\Traits\Modules;
use App\Traits\Permissions;

class ShowInAdmin
{
    use Modules, Permissions;

    /**
     * Handle the event.
     *
     * @param  Event $event
     * @return void
     */
    public function handle(Event $event)
    {
        if (!$this->moduleIsEnabled('outlets')) {
            return;
        }

        $title = trans_choice('outlets::general.outlets', 2);

        if ($this->canAccessMenuItem($title, 'read-outlets-main')) {
            $event->menu->route('outlets.index', $title, [], 55, ['icon' => 'store', 'search_keywords' => trans('outlets::general.outlets')]);
        }
    }
}
