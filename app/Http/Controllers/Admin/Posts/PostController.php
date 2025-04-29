<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Http\Controllers\Controller;
use App\Services\PostsServise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    protected PostsServise $service;

    public function __construct(PostsServise $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $page = max(1, $request->query('page', 1));
        $result = $this->service->index($page);
        return response()->json($result, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Kirgan user ID-sini qo'shish
        $data['user_id'] = Auth::id();

        $id = $this->service->store($data);
        return response()->json(['id' => $id], 201);
    }

    public function show(string $id)
    {
        $post = $this->service->show((int)$id);
        return $post ? response()->json($post, 200) : response()->json(['error' => 'Not Found'], 404);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $this->service->update((int)$id, $data);
        return response()->json(['message' => 'Updated']);
    }

    public function destroy(string $id)
    {
        $this->service->delete((int)$id);
        return response()->json(['message' => 'Deleted']);
    }
}