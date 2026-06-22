<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::withCount('issues')->orderBy('name')->get();

        return view('tags.index', compact('tags'));
    }

    public function store(StoreTagRequest $request): JsonResponse|RedirectResponse
    {
        $tag = Tag::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json(['tag' => $tag], 201);
        }

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag created.');
    }
}
