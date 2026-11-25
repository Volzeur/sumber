<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'amount_needed',
        'interest_rate',
        'collateral_description',
        'repayment_period',
        'repayment_unit',
        'status',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_needed' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'closed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount_needed, 0, ',', '.');
    }

    public function getTotalWithInterestAttribute(): float
    {
        return $this->amount_needed * (1 + $this->interest_rate / 100);
    }

    public function getFormattedRepaymentAttribute(): string
    {
        $unit = $this->repayment_period === 1
            ? rtrim($this->repayment_unit, 's')
            : $this->repayment_unit;

        return $this->repayment_period . ' ' . $unit;
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_with_interest, 0, ',', '.');
    }

    public function getWhatsappLinkAttribute(): string
    {
        $phone = $this->user->formatted_whatsapp;
        $message = urlencode(
            "Hi {$this->user->name}, I'm interested in your loan request: \"{$this->title}\" " .
            "for {$this->formatted_amount}. Let's discuss!"
        );
        return "https://wa.me/{$phone}?text={$message}";
    }
}