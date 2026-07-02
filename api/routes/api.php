<?php

use App\Domains\Automations\Controllers\AutomationRuleController;
use App\Domains\Integrations\Controllers\ApiKeyController;
use App\Domains\Integrations\Controllers\InboundWebhookController;
use App\Domains\Integrations\Controllers\WebhookEndpointController;
use App\Domains\Intelligence\Controllers\AiSettingsController;
use App\Domains\KnowledgeBase\Controllers\KbArticleController;
use App\Domains\KnowledgeBase\Controllers\KbCategoryController;
use App\Domains\Preferences\Controllers\UserPreferenceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MacroController;
use App\Http\Controllers\NotificationLogController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\NotificationTemplateController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\SlaPolicyController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Módulo Preferencias de Usuario
    Route::get('preferences', [UserPreferenceController::class, 'index']);
    Route::get('preferences/{category}', [UserPreferenceController::class, 'show']);
    Route::patch('preferences/{category}', [UserPreferenceController::class, 'update']);
    Route::delete('preferences/{category}', [UserPreferenceController::class, 'reset']);

    Route::apiResource('tickets', TicketController::class)->only(['index', 'show', 'store', 'update']);
    Route::get('tickets/{ticket}/activities', [TicketController::class, 'activities']);
    Route::apiResource('tickets.comments', TicketCommentController::class)->only(['index', 'store']);
    Route::post('tickets/{ticket}/tags', [TicketController::class, 'syncTags']);

    Route::apiResource('macros', MacroController::class);

    // Módulo SLA
    Route::apiResource('sla-policies', SlaPolicyController::class);

    // Módulo Notificaciones
    Route::post('notification-templates/preview', [NotificationTemplateController::class, 'preview']);
    Route::apiResource('notification-templates', NotificationTemplateController::class);
    Route::get('notification-preferences', [NotificationPreferenceController::class, 'index']);
    Route::post('notification-preferences', [NotificationPreferenceController::class, 'store']);
    Route::get('notification-logs', [NotificationLogController::class, 'index']);

    // Módulo Dashboard & KPIs
    Route::get('dashboard/layout', [DashboardController::class, 'layout']);
    Route::put('dashboard/layout', [DashboardController::class, 'updateLayout']);
    Route::post('dashboard/metrics', [DashboardController::class, 'metrics']);

    // Módulo Knowledge Base
    Route::apiResource('kb-categories', KbCategoryController::class);
    Route::apiResource('kb-articles', KbArticleController::class);

    // Módulo Automations
    Route::get('automations/dictionary', [AutomationRuleController::class, 'dictionary']);
    Route::apiResource('automations', AutomationRuleController::class);

    // Módulo Integrations
    Route::apiResource('integrations/api-keys', ApiKeyController::class)->only(['index', 'store', 'destroy']);
    Route::get('integrations/webhooks/{id}/logs', [WebhookEndpointController::class, 'logs']);
    Route::apiResource('integrations/webhooks', WebhookEndpointController::class)->only(['index', 'store', 'destroy']);

    // Módulo Inteligencia Artificial (IA)
    Route::get('ai/settings', [AiSettingsController::class, 'show']);
    Route::put('ai/settings', [AiSettingsController::class, 'update']);
    Route::post('ai/deflect', [AiSettingsController::class, 'deflectTicket']);

    // Administración
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('statuses', StatusController::class);
    Route::apiResource('priorities', PriorityController::class);
    Route::apiResource('custom-fields', CustomFieldController::class);
});

// Rutas Públicas (Sin Autenticación JWT)
Route::post('webhooks/inbound/{tenantUuid}', [InboundWebhookController::class, 'handle']);
