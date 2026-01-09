<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $table      = 'satuan';
    protected $primaryKey = 'id_satuan';
    public $timestamps    = false;

    protected $fillable = [
        'id_tenant',
        'nama_satuan',
    ];
}
