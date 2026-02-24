<?php

namespace Modules\Outlets\Listeners\Report;

use App\Abstracts\Listeners\Report as Listener;
use App\Events\Report\FilterShowing;
use App\Events\Report\GroupApplying;
use App\Events\Report\GroupShowing;
use App\Events\Report\RowsShowing;
use Modules\Outlets\Models\Outlet;

class AddOutlets extends Listener
{
    protected $classes = [
        'App\Reports\IncomeSummary',
        'App\Reports\ExpenseSummary',
        'App\Reports\IncomeExpenseSummary',
        'Modules\Outlets\Reports\OutletSummary',
    ];

    /**
     * Handle filter showing event.
     *
     * @param  \App\Events\Report\FilterShowing  $event
     * @return void
     */
    public function handleFilterShowing(FilterShowing $event)
    {
        if (!module_is_enabled('outlets') || $this->skipThisClass($event)) {
            return;
        }

        $event->class->filters['outlets'] = $this->getOutlets(true);
        $event->class->filters['keys']['outlets'] = 'outlet_id';
        $event->class->filters['names']['outlets'] = trans_choice('outlets::general.outlets', 2);
        $event->class->filters['routes']['outlets'] = ['outlets.index', 'search=enabled:1'];
        $event->class->filters['multiple']['outlets'] = true;
    }

    /**
     * Handle group showing event.
     *
     * @param  \App\Events\Report\GroupShowing  $event
     * @return void
     */
    public function handleGroupShowing(GroupShowing $event)
    {
        if (!module_is_enabled('outlets') || $this->skipThisClass($event)) {
            return;
        }

        $event->class->groups['outlet'] = trans_choice('outlets::general.outlets', 1);
    }

    /**
     * Handle group applying event.
     *
     * @param  \App\Events\Report\GroupApplying  $event
     * @return void
     */
    public function handleGroupApplying(GroupApplying $event)
    {
        if (!module_is_enabled('outlets') || $this->skipThisClass($event)) {
            return;
        }

        $this->applyOutletGroup($event);
    }

    /**
     * Handle rows showing event.
     *
     * @param  \App\Events\Report\RowsShowing  $event
     * @return void
     */
    public function handleRowsShowing(RowsShowing $event)
    {
        if (!module_is_enabled('outlets') || $this->skipRowsShowing($event, 'outlet')) {
            return;
        }

        $all_outlets = $this->getOutlets();

        // Add Unallocated for items with null outlet_id
        $rows = [0 => trans('outlets::general.unallocated')] + $all_outlets;

        if ($outlet_ids = $this->getSearchStringValue('outlet_id')) {
            $outlet_ids = is_array($outlet_ids) ? $outlet_ids : explode(',', $outlet_ids);
            $outlet_ids = array_map('strval', (array) $outlet_ids);

            $rows = collect($rows)->filter(function ($value, $key) use ($outlet_ids) {
                return in_array((string) $key, $outlet_ids);
            })->all();
        }

        $this->setRowNamesAndValues($event, $rows);
    }

    /**
     * Apply outlet group - set outlet_id to 0 (Unallocated) when null.
     *
     * @param  \App\Events\Report\GroupApplying  $event
     * @return void
     */
    public function applyOutletGroup($event)
    {
        $item = $event->model;

        if (!isset($item->outlet_id) || $item->outlet_id === null) {
            $item->outlet_id = 0;
        }
    }

    /**
     * Get outlets for filter/rows.
     *
     * @param  bool  $limit
     * @return array
     */
    public function getOutlets($limit = false)
    {
        $model = Outlet::enabled()->orderBy('name');

        if ($limit !== false) {
            $model->take(setting('default.select_limit'));
        }

        return $model->pluck('name', 'id')->toArray();
    }
}
