<?php

namespace Modules\Outlets\Jobs;

use App\Abstracts\Job;
use App\Interfaces\Job\HasOwner;
use App\Interfaces\Job\HasSource;
use App\Interfaces\Job\ShouldCreate;
use Modules\Outlets\Models\Outlet;

class CreateOutlet extends Job implements HasOwner, HasSource, ShouldCreate
{
    public function handle(): Outlet
    {
        $data = $this->request->all();

        if (!isset($data['company_id'])) {
            $data['company_id'] = company_id();
        }

        $this->model = Outlet::create($data);

        return $this->model;
    }
}
