<?php

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'number',
        'seller_name',
        'seller_address',
        'seller_nip',
        'buyer_name',
        'buyer_address',
        'buyer_nip',
        'total_net_grosze',
        'total_vat_grosze',
        'total_gross_grosze',
        'pdf_path',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'total_net_grosze' => 'integer',
            'total_vat_grosze' => 'integer',
            'total_gross_grosze' => 'integer',
            'issued_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function generateNumber(Order $order): string
    {
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        $month = now()->format('m');

        return sprintf('%03d/%s/%d', $count, $month, now()->year);
    }

    public function displayTotalNet(): string
    {
        return number_format($this->total_net_grosze / 100, 2, ',', '').' zł';
    }

    public function displayTotalVat(): string
    {
        return number_format($this->total_vat_grosze / 100, 2, ',', '').' zł';
    }

    public function displayTotalGross(): string
    {
        return number_format($this->total_gross_grosze / 100, 2, ',', '').' zł';
    }
}
