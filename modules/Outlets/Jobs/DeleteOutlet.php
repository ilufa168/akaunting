<?php

namespace Modules\Outlets\Jobs;

use App\Abstracts\Job;
use App\Interfaces\Job\ShouldDelete;
use Modules\Outlets\Models\Outlet;

class DeleteOutlet extends Job implements ShouldDelete
{
    public function handle(): bool
    {
        $this->model->delete();

        return true;
    }
}
