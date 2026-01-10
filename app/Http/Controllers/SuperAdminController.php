<?php
namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function index()
    {
        // Contoh data yang ditampilkan di dashboard Superadmin
        $totalTenants = Tenant::count();
        $totalUsers   = User::count();
        // Anda bisa menambahkan logic lain, misal: Tenant yang baru daftar bulan ini

        return view('superadmin.dashboard', compact('totalTenants', 'totalUsers'));
    }
}
