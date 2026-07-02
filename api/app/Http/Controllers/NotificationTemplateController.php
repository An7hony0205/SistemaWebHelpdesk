<?php

namespace App\Http\Controllers;

use App\Domains\Notifications\Models\NotificationTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $templates = NotificationTemplate::where('tenant_id', $request->user()->tenant_id)->get();

        return response()->json($templates);
    }

    public function store(Request $request)
    {
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'event_name' => 'required|string',
            'channel' => 'required|string',
            'locale' => 'required|string',
            'subject_template' => 'nullable|string',
            'body_template' => 'required|string',
        ]);

        $template = NotificationTemplate::create([
            'tenant_id' => $request->user()->tenant_id,
            'event_name' => $request->event_name,
            'channel' => $request->channel,
            'locale' => $request->locale,
            'subject_template' => $request->subject_template,
            'body_template' => $request->body_template,
            'version' => 1,
        ]);

        return response()->json($template, 201);
    }

    public function update(Request $request, $id)
    {
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'subject_template' => 'nullable|string',
            'body_template' => 'required|string',
        ]);

        $template = NotificationTemplate::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);

        $template->update([
            'subject_template' => $request->subject_template,
            'body_template' => $request->body_template,
            'version' => $template->version + 1,
        ]);

        return response()->json($template);
    }

    public function destroy(Request $request, $id)
    {
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $template = NotificationTemplate::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $template->delete();

        return response()->json(null, 204);
    }

    public function preview(Request $request)
    {
        if (! $request->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'subject_template' => 'nullable|string',
            'body_template' => 'required|string',
            'payload' => 'required|array',
        ]);

        $subject = $this->renderTemplate($request->subject_template ?? '', $request->payload);
        $body = $this->renderTemplate($request->body_template, $request->payload);

        return response()->json([
            'subject' => $subject,
            'body' => $body,
        ]);
    }

    private function renderTemplate(string $template, array $data): string
    {
        $rendered = $template;
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $rendered = str_replace('{{ '.$key.' }}', (string) $value, $rendered);
                $rendered = str_replace('{{'.$key.'}}', (string) $value, $rendered);
            }
        }

        return $rendered;
    }
}
