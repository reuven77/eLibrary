<?php

namespace App\Models;

use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'book_id',
    'borrowed_at',
    'due_at',
    'returned_at',
    'status',
    'fine_amount',
    'rejection_reason',
    'reviewed_by',
    'borrower_phone',
    'borrower_address',
    'id_card_path',
    'borrower_notes',
])]
class Loan extends Model
{
    /** @use HasFactory<LoanFactory> */
    use HasFactory, HasUuids;

    public const STATUS_PENDING = 'menunggu_persetujuan';

    public const STATUS_APPROVED = 'disetujui';

    public const STATUS_REJECTED = 'ditolak';

    public const STATUS_RETURNED = 'dikembalikan';

    public const STATUS_OVERDUE = 'terlambat';

    /** Status yang menghitung kuota pinjaman aktif / pending. */
    public const QUOTA_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_OVERDUE,
    ];

    /** Status buku sedang dipegang member (stok sudah berkurang). */
    public const ON_HAND_STATUSES = [
        self::STATUS_APPROVED,
        self::STATUS_OVERDUE,
    ];

    protected function casts(): array
    {
        return [
            'borrowed_at' => 'datetime',
            'due_at' => 'datetime',
            'returned_at' => 'datetime',
            'fine_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canPrintReceipt(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_OVERDUE], true);
    }

    public function isOverdue(): bool
    {
        return in_array($this->status, self::ON_HAND_STATUSES, true)
            && $this->due_at !== null
            && $this->due_at->isPast()
            && $this->returned_at === null;
    }
}
