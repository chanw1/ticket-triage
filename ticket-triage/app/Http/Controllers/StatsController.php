<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $statusCounts = Ticket::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $categoryCounts = Ticket::select('category', DB::raw('count(*) as total'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('total', 'category');

        return response()->json([
            'status_counts'   => $statusCounts,
            'category_counts' => $categoryCounts,
        ]);
    }
}