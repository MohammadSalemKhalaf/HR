<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Resume extends Model
{
    use HasFactory, Notifiable,HasUuids,SoftDeletes;

    protected $table = "resumes";
      protected $keyType="string";
    public $incrementing = false;

    protected $fillable = [
        'filename',
        'fileUrl',
        'contactDetails',
        'education',
        'summary',
        'skills',
        'experience',
        'userId'
    ];

      protected $dates = [
        'deleted_at'
    ];

        protected function casts(): array
    {
        return [
            'deleted_at'=> 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}
