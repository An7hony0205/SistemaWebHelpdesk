<?php

namespace App\Http\Controllers;

use App\Domains\Identity\User;
use App\Domains\Notifications\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function index(Request $request)
    {
        $preferences = NotificationPreference::where('configurable_type', User::class)
            ->where('configurable_id', $request->user()->id)
            ->get();

        return response()->json($preferences);
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string',
            'channels' => 'required|array',
            'channels.*' => 'string',
        ]);

        $preference = NotificationPreference::updateOrCreate(
            [
                'configurable_type' => User::class,
                'configurable_id' => $request->user()->id,
                'event_name' => $request->event_name,
            ],
            [
                'channels' => $request->channels,
            ]
        );

        return response()->json($preference);
    }
}
