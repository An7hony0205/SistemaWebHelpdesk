<?php

namespace App\Domains\KnowledgeBase\Controllers;

use App\Domains\KnowledgeBase\Models\KbArticle;
use App\Domains\KnowledgeBase\Models\KbArticleVersion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KbArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = KbArticle::with(['category', 'author:id,name'])
            ->where('tenant_id', $request->user()->tenant_id);

        // Búsqueda simple en SQL para MVP
        if ($request->has('q') && ! empty($request->q)) {
            $searchTerm = '%'.$request->q.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', $searchTerm)
                    ->orWhere('content', 'LIKE', $searchTerm)
                    ->orWhere('excerpt', 'LIKE', $searchTerm);
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Si es público, solo ver 'published' (esto requeriría un flag en el request si lo abstraemos,
        // pero por ahora todo está detrás de auth)
        if (! $request->user()->hasPermissionTo('manage articles')) {
            $query->where('status', 'published');
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($articles);
    }

    public function show(Request $request, $slug)
    {
        $article = KbArticle::with(['category', 'author:id,name', 'tags'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->where('slug', $slug)
            ->firstOrFail();

        // Registrar métrica de visita
        $article->increment('views_count');

        return response()->json($article);
    }

    public function store(Request $request)
    {
        // En MVP requerimos permiso para crear.
        if (! $request->user()->hasPermissionTo('create articles')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:kb_categories,id',
            'excerpt' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // El usuario permisivo publica directo o lo deja en draft
            $status = $request->user()->hasPermissionTo('publish articles') ? 'published' : 'draft';
            $publishedAt = $status === 'published' ? now() : null;

            $article = KbArticle::create([
                'tenant_id' => $request->user()->tenant_id,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'excerpt' => $request->excerpt,
                'content' => $request->content,
                'status' => $status,
                'author_id' => $request->user()->id,
                'published_at' => $publishedAt,
            ]);

            // Crear Versión 1
            $this->createVersion($article, 1, $request->user()->id);

            DB::commit();

            return response()->json($article, 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        if (! $request->user()->hasPermissionTo('edit articles')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $article = KbArticle::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:kb_categories,id',
        ]);

        DB::beginTransaction();
        try {
            $article->update([
                'title' => $request->title,
                'content' => $request->content,
                'category_id' => $request->category_id,
                'excerpt' => $request->excerpt ?? $article->excerpt,
                'editor_id' => $request->user()->id,
            ]);

            // Versionado
            $nextVersion = KbArticleVersion::where('article_id', $article->id)->max('version_number') + 1;
            $this->createVersion($article, $nextVersion, $request->user()->id);

            DB::commit();

            return response()->json($article);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function createVersion(KbArticle $article, int $versionNumber, int $userId)
    {
        KbArticleVersion::create([
            'article_id' => $article->id,
            'version_number' => $versionNumber,
            'title' => $article->title,
            'content' => $article->content,
            'created_by' => $userId,
        ]);
    }
}
