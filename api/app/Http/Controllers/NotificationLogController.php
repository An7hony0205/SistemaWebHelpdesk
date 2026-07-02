<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domains\Notifications\Models\NotificationLog;

class NotificationLogController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $logs = NotificationLog::with('user:id,name,email')
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json($logs);
    }
}
