<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Cari user menggunakan Query Builder
        $user = DB::table('users')->where('username', $request->username)->first();

        // Cek apakah user ada dan password cocok
        if ($user && Hash::check($request->password, $user->password)) {
            // Buat sesi manual yang aman
            $request->session()->put('user_id', $user->id);
            $request->session()->put('full_name', $user->full_name);
            $request->session()->put('role', $user->role);

            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Username atau password salah!');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }
}
