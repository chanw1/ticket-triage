<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClassifyTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Ticket $ticket;

    // We pass in the ticket when creating the job
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    // This is what runs when the job is processed
    public function handle(TicketClassifier $classifier): void
    {
        $result = $classifier->classify($this->ticket->subject, $this->ticket->body);

        // Don't overwrite manual categories
        if (!$this->ticket->category) {
            $this->ticket->category = $result['category'];
        }

        $this->ticket->explanation = $result['explanation'];
        $this->ticket->confidence  = $result['confidence'];
        $this->ticket->save();
    }
}