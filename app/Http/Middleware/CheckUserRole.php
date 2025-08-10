<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use App\Models\Roles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }


        // Ambil role_id pengguna yang login
        $roleId = Auth::user()->userRoleId;


        // Ambil menu_id yang diizinkan untuk role ini
        $roleMenu = Roles::where('roleId', $roleId)->first();
        $allowedMenuIds = json_decode($roleMenu->roleMenuId ?? '[]', true);

        // Ambil menu yang cocok dengan URL saat ini
        $currentRouteName = $request->route()->getName();
        // dd($currentRouteName);
        $menu = Menu::where('menuRoute', $currentRouteName)->first();

        // Validasi apakah pengguna memiliki akses ke menu ini
        if ($menu && !in_array($menu->menuId, $allowedMenuIds)) {
            // Redirect jika tidak diizinkan
            return redirect()->route('unauthorized'); // Buat halaman unauthorized
        }

        return $next($request);
    }
}
