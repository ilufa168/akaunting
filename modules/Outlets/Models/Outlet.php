<?php

namespace Modules\Outlets\Models;

use App\Abstracts\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Outlet extends Model
{
    use HasFactory;

    protected $table = 'outlets';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'address',
        'phone',
        'email',
        'enabled',
        'created_from',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'enabled'    => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['name', 'enabled'];

    /**
     * Get the transactions for the outlet.
     */
    public function transactions()
    {
        return $this->hasMany('App\Models\Banking\Transaction', 'outlet_id');
    }

    /**
     * Get the documents for the outlet.
     */
    public function documents()
    {
        return $this->hasMany('App\Models\Document\Document', 'outlet_id');
    }

    /**
     * Scope to only include enabled outlets.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }

    /**
     * Get the line actions.
     *
     * @return array
     */
    public function getLineActionsAttribute()
    {
        $actions = [];

        $actions[] = [
            'title' => trans('general.show'),
            'icon' => 'visibility',
            'url' => route('outlets.show', $this->id),
            'permission' => 'read-outlets-main',
            'attributes' => [
                'id' => 'index-line-actions-show-outlet-' . $this->id,
            ],
        ];

        $actions[] = [
            'title' => trans('general.edit'),
            'icon' => 'edit',
            'url' => route('outlets.edit', $this->id),
            'permission' => 'update-outlets-main',
            'attributes' => [
                'id' => 'index-line-actions-edit-outlet-' . $this->id,
            ],
        ];

        $actions[] = [
            'type' => 'delete',
            'icon' => 'delete',
            'route' => 'outlets.destroy',
            'permission' => 'delete-outlets-main',
            'model' => $this,
            'model-name' => 'name',
            'attributes' => [
                'id' => 'index-line-actions-delete-outlet-' . $this->id,
            ],
        ];

        return $actions;
    }
}
