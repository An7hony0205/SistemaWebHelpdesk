<?php

namespace App\Domains\Intelligence\Jobs;

use App\Domains\Intelligence\Models\AiSetting;
use App\Domains\Intelligence\Services\OpenAiService;
use App\Domains\Support\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiClassifyTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function handle(OpenAiService $aiService)
    {
        $settings = AiSetting::where('tenant_id', $this->ticket->tenant_id)->first();

        if (! $settings || ! $settings->is_triage_enabled || empty($settings->openai_api_key)) {
            return;
        }

        $aiService->setApiKey($settings->openai_api_key);

        // Fetch available categories and priorities for context
        $categories = DB::table('categories')->pluck('name', 'id')->toArray();
        $priorities = DB::table('priorities')->pluck('name', 'id')->toArray();

        $classification = $aiService->classifyTicket(
            $this->ticket->title,
            $this->ticket->content,
            $categories,
            $priorities
        );

        if (! empty($classification)) {
            $updated = false;

            if (isset($classification['category_id']) && array_key_exists($classification['category_id'], $categories)) {
                $this->ticket->category_id = $classification['category_id'];
                $updated = true;
            }

            if (isset($classification['priority_id']) && array_key_exists($classification['priority_id'], $priorities)) {
                $this->ticket->priority_id = $classification['priority_id'];
                $updated = true;
            }

            if ($updated) {
                $this->ticket->save();

                // Add internal note
                $this->ticket->comments()->create([
                    'tenant_id' => $this->ticket->tenant_id,
                    'author_id' => null, // null means System/AI
                    'content' => '🤖 [Triage AI] '.($classification['reason'] ?? 'Clasificado automáticamente.'),
                    'is_internal' => true,
                ]);

                Log::info("Ticket {$this->ticket->id} automatically classified by AI.");
            }
        }
    }
}
