<?php

namespace App\Domains\Preferences\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Preferences\Requests\UpdatePreferenceRequest;
use App\Domains\Preferences\Repositories\UserPreferenceRepositoryInterface;
use App\Domains\Preferences\DTOs\PreferenceUpdateDTO;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function __construct(
        private UserPreferenceRepositoryInterface $repository
    ) {}

    public function index(Request $request)
    {
        $preferences = $this->repository->getAll($request->user()->id);
        return response()->json($preferences);
    }

    public function show(Request $request, string $category)
    {
        $categoryPrefs = $this->repository->getByCategory($request->user()->id, $category);
        return response()->json($categoryPrefs);
    }

    public function update(UpdatePreferenceRequest $request, string $category)
    {
        $dto = PreferenceUpdateDTO::fromRequest($request->validated(), $request->user()->id, $category);
        
        $updated = $this->repository->updateCategory($dto->userId, $dto->category, $dto->settings);

        // Optionally dispatch an event here like UserPreferencesUpdated
        // event(new UserPreferencesUpdated($updated));

        // Return the updated merged category settings
        return response()->json($this->repository->getByCategory($dto->userId, $dto->category));
    }

    public function reset(Request $request, string $category)
    {
        $this->repository->resetCategory($request->user()->id, $category);
        return response()->json($this->repository->getByCategory($request->user()->id, $category));
    }
}
