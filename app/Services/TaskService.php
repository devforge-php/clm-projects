<?php 

namespace App\Services;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    /**
     * Get all tasks from cache or database
     */
    public function getAllTasks()
    {
        return Cache::remember('tasks', 60, function () {
            return Task::all();
        });
    }

    /**
     * Get a single task by ID
     */
    public function getTaskById(string $id)
    {
        return Task::findOrFail($id);
    }

    /**
     * Create a new task
     */
    public function createTask(Request $request)
    {
        $imagePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $destinationPath = storage_path('app/public/tasks');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);
            $imagePath = 'tasks/' . $fileName;
        }

        $task = Task::create([
            'image' => $imagePath,
            'telegram' => $request->telegram,
            'instagram' => $request->instagram,
            'youtube' => $request->youtube,
            'twitter' => $request->twitter,
            'text' => $request->text,
            'reward' => $request->reward,
        ]);

        Cache::forget('tasks'); // Cache ni yangilash

        return $task;
    }

    /**
     * Update an existing task
     */
    public function updateTask(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $task->update($request->all());

        Cache::forget('tasks'); // Cache ni yangilash

        return $task;
    }

    /**
     * Delete a task
     */
    public function deleteTask(string $id)
    {
        Task::destroy($id);
        Cache::forget('tasks'); // Cache ni yangilash
    }
}
