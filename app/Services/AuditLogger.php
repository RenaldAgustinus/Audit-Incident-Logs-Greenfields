<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth; <-- Boleh dihapus karena nggak dipakai lagi

class AuditLogger
{
    public static function log($incidentId, $action, $description)
    {
        DB::table('audit_trails')->insert([
            'user_id'     => session('user_id'), // <--- UBAH BAGIAN INI JUGA
            'incident_id' => $incidentId,
            'action'      => $action,
            'description' => $description,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}