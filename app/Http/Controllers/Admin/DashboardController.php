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
            'homepage' => ['Hero & Banners', 'homepage_sections', 'admin.homepage.index'],
            'achievements' => ['Key Achievements', 'achievements', 'admin.achievements.index'],
            'tournaments' => ['Tournaments', 'tournaments', 'admin.tournaments.index'],
            'coaching' => ['Coaching & Tips', 'coaching_tips', 'admin.coaching.index'],
            'photos' => ['Gallery Photos', 'photos', 'admin.photos.index'],
            'videos' => ['Gallery Videos', 'videos', 'admin.videos.index'],
            'sponsors' => ['Sponsors', 'sponsors', 'admin.sponsors.index'],
            'messages' => ['Unread Messages', 'contact_messages', 'admin.messages.index'],
            'users' => ['Admin Users', 'users', 'admin.users.index'],
        ] as $key => [$label, $table, $route]) {
            $configured = Schema::hasTable($table);
            $query = $configured ? DB::table($table) : null;

            if ($key === 'messages' && $configured && Schema::hasColumn($table, 'read_at')) {
                $query->whereNull('read_at');
            }

            $metrics[] = [
                'label' => $label,
                'key' => $key,
                'route' => $route,
                'value' => $configured ? $query->count() : 0,
            ];
        }

        return view('admin.dashboard', ['metrics' => $metrics, 'title' => 'Dashboard']);
    }
}
