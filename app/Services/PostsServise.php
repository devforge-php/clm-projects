<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostsServise
{
    // Ro'yxat olish + paginatsiya + redis cache
    public function index(int $page = 1): array
    {
        return Cache::remember("posts_page_{$page}", 600, function () use ($page) {
            $perPage = 5;
            $offset = ($page - 1) * $perPage;

            $posts = DB::select("SELECT * FROM posts ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}");
            $total = DB::selectOne("SELECT COUNT(*) as count FROM posts")->count;

            return [
                'data' => array_map(fn($post) => $this->formatPost($post), $posts),
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => (int)$total,
                    'last_page' => ceil((int)$total / $perPage),
                ]
            ];
        });
    }

    // Bitta postni olish
    public function show(int $id): ?array
    {
        return Cache::remember("post_{$id}", 300, function () use ($id) {
            $post = DB::selectOne("SELECT * FROM posts WHERE id = ?", [$id]);
            return $post ? $this->formatPost($post) : null;
        });
    }

    // Yaratish
    public function store(array $data): int
    {
        $image = $this->saveImage($data['image'] ?? null);

        DB::insert("
            INSERT INTO posts (user_id, image, name, description, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ", [
            $data['user_id'],
            $image,
            $data['name'],
            $data['description']
        ]);

        $id = DB::getPdo()->lastInsertId();

        // Cache tozalash
        Cache::forget("posts_page_1");
        return (int)$id;
    }

    // Yangilash
    public function update(int $id, array $data): void
    {
        if (!empty($data['image'])) {
            $old = $this->show($id);
            if ($old && $old['image']) Storage::disk('public')->delete($old['image']);
            $data['image'] = $this->saveImage($data['image']);
        }

        $params = [];
        $values = [];

        foreach ($data as $key => $value) {
            if (in_array($key, ['user_id', 'image', 'name', 'description'])) {
                $params[] = "$key = ?";
                $values[] = $value;
            }
        }

        $values[] = $id;
        DB::update("UPDATE posts SET " . implode(', ', $params) . ", updated_at = NOW() WHERE id = ?", $values);

        // Cache tozalash
        Cache::forget("post_{$id}");
        Cache::forget("posts_page_1");
    }

    // O'chirish
    public function delete(int $id): void
    {
        $post = $this->show($id);
        if ($post && $post['image']) Storage::disk('public')->delete($post['image']);

        DB::delete("DELETE FROM posts WHERE id = ?", [$id]);

        // Cache tozalash
        Cache::forget("post_{$id}");
        Cache::forget("posts_page_1");
    }

    // Rasm saqlash (public papkaga)
    private function saveImage(?UploadedFile $file): ?string
    {
        if (!$file || !$file->isValid()) return null;
        return $file->store('images', 'public');
    }

    // Postni formatlab qaytarish (URL ham bor)
    private function formatPost($post): array
    {
        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'name' => $post->name,
            'description' => $post->description,
            'image' => $post->image,
            'image_url' => $post->image ? asset("storage/{$post->image}") : null,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
        ];
    }
}