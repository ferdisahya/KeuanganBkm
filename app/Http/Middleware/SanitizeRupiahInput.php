<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware: Sanitize Rupiah Input
 * 
 * Otomatis membersihkan titik pemisah ribuan (.) dari field-field nominal/harga
 * sebelum masuk ke controller, sehingga validasi 'numeric' tetap berjalan.
 * 
 * Contoh: "20.000.000" -> "20000000"
 */
class SanitizeRupiahInput
{
    /**
     * Field-field yang akan dibersihkan titiknya secara otomatis.
     */
    protected $fields = [
        'nominal',
        'total',
        'harga',
        'saldo_kas',
        'saldo_raab',
        'saldo',
        'debet',
        'kredit',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Hanya sanitasi pada request POST/PUT/PATCH
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $input = $request->all();
            foreach ($this->fields as $field) {
                if (isset($input[$field]) && is_string($input[$field])) {
                    // Hapus titik pemisah ribuan (format Indonesia)
                    $input[$field] = str_replace('.', '', $input[$field]);
                    // Ganti koma desimal menjadi titik (jika ada)
                    $input[$field] = str_replace(',', '.', $input[$field]);
                }
            }
            $request->replace($input);
        }

        return $next($request);
    }
}
