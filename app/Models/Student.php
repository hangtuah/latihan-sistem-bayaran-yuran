<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['programme_id', 'matric', 'name', 'email'];

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** Akaun log masuk pelajar ini (users.student_id). */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
