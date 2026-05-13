<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PersonContact extends Model
{
    use LogsActivity;

    protected $table = 'person_contacts';

    protected $fillable = [
        'phone',
        'email',
        'type',
        'person_profile_id',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('PersonContact')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function personProfile(): BelongsTo
    {
        return $this->belongsTo(PersonProfile::class, 'person_profile_id', 'id');
    }
}
