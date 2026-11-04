<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $metrics = [];
        foreach ([
            'news' => ['Total News', 'articles'],
            'tournaments' => ['Total Tournaments', 'tournaments'],
            'photos' => ['Total Photos', 'photos'],
            'videos' => ['Total Videos', 'videos'],
            'sponsors' => ['Total Sponsors', 'sponsors'],
            'messages' => ['Unread Contact Messages', 'contact_messages'],
            'social-works' => ['Social Projects', 'social_works'],
            'equipment' => ['Equipment Records', 'equipment'],
            'coaching' => ['Coaching Tips', 'coaching_tips'],
            'users' => ['User Accounts', 'users'],
        ] as $module => [$label, $table]) {
            $configured = Schema::hasTable($table);
            $query = $configured ? DB::table($table) : null;

            if ($module === 'messages' && $configured) {
                $configured = Schema::hasColumn($table, 'read_at');
                if ($configured) {
                    $query->whereNull('read_at');
                }
            }

            $metrics[] = [
                'label' => $label,
                'module' => $module,
                'value' => $configured ? $query->count() : null,
            ];
        }

        return view('admin.dashboard', ['metrics' => $metrics, 'title' => 'Dashboard']);
    }
}
