<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Fungsi ajaib untuk mencatat log dengan 1 baris kode!
     */
    public static function log($incidentId, $action, $description)
    {
        DB::table('audit_trails')->insert([
            'user_id'     => Auth::id(),
            'incident_id' => $incidentId,
            'action'      => $action,
            'description' => $description, 
            'created_at'  => now(), 
            'updated_at'  => now(),
        ]);
    }
}