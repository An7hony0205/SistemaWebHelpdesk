<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domains\Support\Ticket;
use App\Domains\Support\UseCases\CreateTicketUseCase;
use App\Domains\Support\UseCases\AssignTicketUseCase;
use App\DTOs\TicketCreateDTO;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CreateTicketUseCase $createTicketUseCase,
        private AssignTicketUseCase $assignTicketUseCase
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);
        $user = $request->user();
        
        $query = Ticket::with(['category', 'user', 'assignedUser', 'tags']);
            
        if (!$user->hasRole(['Soporte', 'Admin'])) {
            $query->where('user_id', $user->id);
        }
        
        // Filtros avanzados básicos
        if ($request->has('status_id')) {
            $query->where('status_id', $request->status_id);
        }
        if ($request->has('priority_id')) {
            $query->where('priority_id', $request->priority_id);
        }
        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $this->authorize('create', Ticket::class);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority_id' => 'nullable|exists:priorities,id',
        ]);

        $dto = TicketCreateDTO::fromRequest($request->all(), $request->user()->id);
        $ticket = $this->createTicketUseCase->execute($dto);

        return response()->json($ticket, 201);
    }

    public function show(Request $request, $id)
    {
        $ticket = Ticket::with(['category', 'user', 'comments.user', 'assignedUser', 'tags', 'attachments', 'sla.policy'])->findOrFail($id);
        $this->authorize('view', $ticket);

        return response()->json($ticket);
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('update', $ticket);
        
        $oldStatusId = $ticket->status_id;
            
        $ticket->update([
            'status_id' => $request->input('status_id', $ticket->status_id),
            'priority_id' => $request->input('priority_id', $ticket->priority_id),
        ]);
        
        if ($ticket->wasChanged('status_id')) {
            $newStatus = \App\Domains\Administration\Status::find($ticket->status_id);
            if ($newStatus) {
                // Notificar cambio de estado para posibles pausas/reanudaciones de SLA
                \Illuminate\Support\Facades\Event::dispatch(new \App\Events\TicketStatusUpdated($ticket, $newStatus->name));
                
                if (in_array($newStatus->name, ['Resuelto', 'Cerrado'])) {
                    \Illuminate\Support\Facades\Event::dispatch(new \App\Events\TicketResolved($ticket));
                } elseif ($newStatus->name === 'Abierto' && $oldStatusId != $ticket->status_id) {
                    \Illuminate\Support\Facades\Event::dispatch(new \App\Events\TicketReopened($ticket));
                }
            }
        }
        
        if ($request->has('assigned_to') && $request->user()->hasRole(['Soporte', 'Admin'])) {
            $this->assignTicketUseCase->execute($ticket, $request->assigned_to);
        }
        
        return response()->json($ticket);
    }

    public function activities(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $this->authorize('view', $ticket);

        // Fetch activity logs using Spatie Activitylog
        $activities = \Spatie\Activitylog\Models\Activity::forSubject($ticket)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($activities);
    }
}
