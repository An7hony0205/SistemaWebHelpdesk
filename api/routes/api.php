<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\PriorityController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Módulo Preferencias de Usuario
    Route::get('preferences', [\App\Domains\Preferences\Controllers\UserPreferenceController::class, 'index']);
    Route::get('preferences/{category}', [\App\Domains\Preferences\Controllers\UserPreferenceController::class, 'show']);
    Route::patch('preferences/{category}', [\App\Domains\Preferences\Controllers\UserPreferenceController::class, 'update']);
    Route::delete('preferences/{category}', [\App\Domains\Preferences\Controllers\UserPreferenceController::class, 'reset']);

    Route::apiResource('tickets', TicketController::class)->only(['index', 'show', 'store', 'update']);
    Route::get('tickets/{ticket}/activities', [TicketController::class, 'activities']);
    Route::apiResource('tickets.comments', TicketCommentController::class)->only(['index', 'store']);
    Route::post('tickets/{ticket}/tags', [TicketController::class, 'syncTags']);

    Route::apiResource('macros', \App\Http\Controllers\MacroController::class);

    // Módulo SLA
    Route::apiResource('sla-policies', \App\Http\Controllers\SlaPolicyController::class);

    // Módulo Notificaciones
    Route::post('notification-templates/preview', [\App\Http\Controllers\NotificationTemplateController::class, 'preview']);
    Route::apiResource('notification-templates', \App\Http\Controllers\NotificationTemplateController::class);
    Route::get('notification-preferences', [\App\Http\Controllers\NotificationPreferenceController::class, 'index']);
    Route::post('notification-preferences', [\App\Http\Controllers\NotificationPreferenceController::class, 'store']);
    Route::get('notification-logs', [\App\Http\Controllers\NotificationLogController::class, 'index']);

    // Módulo Dashboard & KPIs
    Route::get('dashboard/layout', [\App\Http\Controllers\DashboardController::class, 'layout']);
    Route::put('dashboard/layout', [\App\Http\Controllers\DashboardController::class, 'updateLayout']);
    Route::post('dashboard/metrics', [\App\Http\Controllers\DashboardController::class, 'metrics']);

    // Módulo Knowledge Base
    Route::apiResource('kb-categories', \App\Domains\KnowledgeBase\Controllers\KbCategoryController::class);
    Route::apiResource('kb-articles', \App\Domains\KnowledgeBase\Controllers\KbArticleController::class);

    // Módulo Automations
    Route::get('automations/dictionary', [\App\Domains\Automations\Controllers\AutomationRuleController::class, 'dictionary']);
    Route::apiResource('automations', \App\Domains\Automations\Controllers\AutomationRuleController::class);

    // Módulo Integrations
    Route::apiResource('integrations/api-keys', \App\Domains\Integrations\Controllers\ApiKeyController::class)->only(['index', 'store', 'destroy']);
    Route::get('integrations/webhooks/{id}/logs', [\App\Domains\Integrations\Controllers\WebhookEndpointController::class, 'logs']);
    Route::apiResource('integrations/webhooks', \App\Domains\Integrations\Controllers\WebhookEndpointController::class)->only(['index', 'store', 'destroy']);

    // Módulo Inteligencia Artificial (IA)
    Route::get('ai/settings', [\App\Domains\Intelligence\Controllers\AiSettingsController::class, 'show']);
    Route::put('ai/settings', [\App\Domains\Intelligence\Controllers\AiSettingsController::class, 'update']);
    Route::post('ai/deflect', [\App\Domains\Intelligence\Controllers\AiSettingsController::class, 'deflectTicket']);

    // Administración
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('statuses', StatusController::class);
    Route::apiResource('priorities', PriorityController::class);
    Route::apiResource('custom-fields', \App\Http\Controllers\CustomFieldController::class);
});

// Rutas Públicas (Sin Autenticación JWT)
Route::post('webhooks/inbound/{tenantUuid}', [\App\Domains\Integrations\Controllers\InboundWebhookController::class, 'handle']);
