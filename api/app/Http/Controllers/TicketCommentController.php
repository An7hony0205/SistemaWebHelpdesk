<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domains\Support\TicketComment;
use App\Domains\Support\Ticket;

class TicketCommentController extends Controller
{
    public function index(Request $request, $ticketId)
    {
        $comments = TicketComment::with('user')
            ->where('ticket_id', $ticketId);
            
        // Si no es soporte/admin, solo ve comentarios públicos
        if (!$request->user()->hasRole(['Soporte', 'Admin'])) {
            $comments->where('is_internal', false);
        }
        
        return response()->json($comments->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'description' => 'required|string',
            'is_internal' => 'boolean'
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        // Si el usuario no tiene permisos de admin/soporte, verificar que el ticket es suyo
        if (!$request->user()->hasRole(['Soporte', 'Admin']) && $ticket->user_id != $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Validar si el usuario puede añadir un comentario interno (solo soporte)
        $isInternal = $request->input('is_internal', false);
        if ($isInternal && !$request->user()->hasRole(['Soporte', 'Admin'])) {
            $isInternal = false;
        }

        $comment = TicketComment::create([
            'tenant_id' => $request->user()->tenant_id,
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'description' => strip_tags($request->description, '<p><br><b><i><ul><li><img>'),
            'is_internal' => $isInternal,
        ]);

        return response()->json($comment->load('user'), 201);
    }
}
