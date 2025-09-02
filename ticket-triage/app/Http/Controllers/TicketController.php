<?php

namespace App\Http\Controllers;
use App\Jobs\ClassifyTicket;


use App\Models\Ticket;        // ✅ import the model
use Illuminate\Http\Request;

class TicketController extends Controller
{

 
    
    // GET /api/tickets
    public function index(Request $request)
    {
        $query = Ticket::query();

        // Filter by status (?status=open)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category (?category=billing)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search subject/body (?search=login)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Default paginate 10 per page, allow ?per_page=25
        $perPage = $request->get('per_page', 10);

        return $query->orderByDesc('created_at')->paginate($perPage);
    }



public function stats()
{
    $byStatus = Ticket::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status');

    $byCategory = Ticket::selectRaw('COALESCE(category, "uncategorized") as category, COUNT(*) as count')
        ->groupBy('category')
        ->pluck('count', 'category');

    return response()->json([
        'by_status' => $byStatus,
        'by_category' => $byCategory,
    ]);
}


    public function classify(Ticket $ticket)
{
    ClassifyTicket::dispatch($ticket);

    return response()->json([
        'message' => 'Classification job queued',
        'ticket_id' => $ticket->id
    ]);
}


 public function exportCsv()
{
    $tickets = \App\Models\Ticket::all();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=tickets.csv",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = [
        'id', 'subject', 'body', 'status',
        'category', 'confidence', 'explanation',
        'note', 'created_at', 'updated_at'
    ];

    $callback = function() use ($tickets, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($tickets as $ticket) {
            $row = [];
            foreach ($columns as $col) {
                $row[] = $ticket->{$col};
            }
            fputcsv($file, $row);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

    // POST /api/tickets
    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $ticket = Ticket::create($data);

        return response()->json($ticket, 201);
    }

    // GET /api/tickets/{ticket}
    public function show(Ticket $ticket)
    {
        return $ticket;
    }

    // PATCH /api/tickets/{ticket}
    public function update(Request $request, Ticket $ticket)
    {
         $ticket->update($request->only(['subject', 'status', 'category', 'note']));
        return $ticket;
    }

    // If you keep this, include it in routes; otherwise remove it.
    public function destroy(string $id)
    {
        // Not used for this task
        abort(405); // Method Not Allowed
    }
}