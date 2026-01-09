<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTokoAccess extends Model
{
    /**
     * =========================================
     * KONFIGURASI DASAR
     * =========================================
     */
    protected $table      = 'user_toko_access';
    protected $primaryKey = 'id_access';
    public $timestamps    = false;

    protected $fillable = [
        'id_user',
        'id_toko',
    ];

    /**
     * =========================================
     * RELASI
     * =========================================
     */

    /**
     * User pemilik akses
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Toko yang bisa diakses
     */
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'id_toko');
    }

    /**
     * =========================================
     * HELPER / BUSINESS LOGIC (OPSIONAL)
     * =========================================
     */

    /**
     * Cek apakah user punya akses ke toko tertentu
     */
    public static function userHasAccessToToko($idUser, $idToko)
    {
        return self::where('id_user', $idUser)
            ->where('id_toko', $idToko)
            ->exists();
    }

    /**
     * Ambil semua toko yang bisa diakses user
     */
    public static function getTokoByUser($idUser)
    {
        return self::with('toko')
            ->where('id_user', $idUser)
            ->get();
    }
}
