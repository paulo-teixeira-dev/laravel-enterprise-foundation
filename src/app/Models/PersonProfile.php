<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PersonProfile extends Model
{
    use LogsActivity;

    protected $table = 'person_profiles';

    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'nationality',
        'address',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state_id',
        'postal_code',
        'file_id',
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
            ->useLogName('PersonProfile')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function personContact(): HasMany
    {
        return $this->hasMany(PersonContact::class, 'person_profile_id', 'id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'person_profile_id', 'id');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }
}
