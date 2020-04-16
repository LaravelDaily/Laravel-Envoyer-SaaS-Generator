<?php

namespace App;

use DateTimeInterface;
use App\Traits\MultiTenantModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Project
 * @package App
 */
class Project extends Model
{
    use SoftDeletes, MultiTenantModelTrait;

    public $table = 'projects';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'created_by',
    ];

    /**
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @return BelongsTo
     */
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
