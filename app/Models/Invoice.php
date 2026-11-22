<?php

namespace App\Models;

use App\Enums\FeeType;
use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(InvoiceObserver::class)]
class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'number', 'type', 'amount', 'due_date', 'paid_at'];

    protected $casts = ['type' => FeeType::class, 'due_date' => 'date', 'paid_at' => 'datetime', 'amount' => 'decimal:2'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    #[Scope]
    protected function outstanding(Builder $query): void
    {
        $query->whereNull('paid_at');
    }

    #[Scope]
    protected function overdue(Builder $query): void
    {
        $query->outstanding()
            ->where('due_date', '<', today('Asia/Kuala_Lumpur'));
    }

    #[Scope]
    protected function ofType(Builder $query, FeeType $type): void
    {
        $query->where('type', $type);
    }
}
