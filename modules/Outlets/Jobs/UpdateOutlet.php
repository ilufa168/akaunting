<?php

namespace Modules\Outlets\Jobs;

use App\Abstracts\Job;
use App\Interfaces\Job\ShouldUpdate;
use Modules\Outlets\Models\Outlet;

class UpdateOutlet extends Job implements ShouldUpdate
{
    public function handle(): Outlet
    {
        $this->model->update($this->request->all());

        return $this->model;
    }
}
