<?php

namespace App\Domains\Intelligence\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Intelligence\Models\AiSetting;
use App\Domains\Intelligence\Services\OpenAiService;

class AiSettingsController extends Controller
{
    public function show(Request $request)
    {
        $settings = AiSetting::firstOrCreate(
            ['tenant_id' => $request->user()->tenant_id],
            [
                'is_triage_enabled' => false,
                'is_sentiment_enabled' => false
            ]
        );
        
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $request->validate([
            'openai_api_key' => 'nullable|string',
            'is_triage_enabled' => 'boolean',
            'is_sentiment_enabled' => 'boolean',
        ]);

        $settings = AiSetting::firstOrCreate(['tenant_id' => $request->user()->tenant_id]);

        if ($request->has('openai_api_key')) {
            $settings->openai_api_key = $request->openai_api_key;
        }

        $settings->is_triage_enabled = $request->is_triage_enabled ?? $settings->is_triage_enabled;
        $settings->is_sentiment_enabled = $request->is_sentiment_enabled ?? $settings->is_sentiment_enabled;
        $settings->save();

        return response()->json($settings);
    }

    public function deflectTicket(Request $request, OpenAiService $aiService)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $settings = AiSetting::where('tenant_id', $request->user()->tenant_id)->first();
        if (!$settings || empty($settings->openai_api_key)) {
            return response()->json(['articles' => []]);
        }

        $aiService->setApiKey($settings->openai_api_key);
        $keywords = $aiService->extractKeywords($request->text);

        if (empty($keywords)) {
            return response()->json(['articles' => []]);
        }

        // MVP: Full-Text search fallback using AI keywords
        $query = \App\Domains\KnowledgeBase\Models\KbArticle::where('tenant_id', $request->user()->tenant_id)
            ->where('status', 'published');

        foreach ($keywords as $keyword) {
            $query->orWhere('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%");
        }

        $articles = $query->take(3)->get(['id', 'title', 'slug', 'views_count']);

        return response()->json(['articles' => $articles]);
    }
}
