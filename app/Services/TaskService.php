<?php 

namespace App\Services;

use App\Events\TaskEvent;
use App\Http\Requests\TaskRequest;
use App\Jobs\EmailJobUsers;
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
    public function createTask(TaskRequest $request)
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
            'number' => $request->number, // <-- SHU JOYNI TEKSHIRING
            'reward' => $request->reward,
        ]);

        Cache::forget('tasks'); // Cache ni yangilash
  
        return $task;
    }

    /**
     * Update an existing task
     */
    public function updateTask(TaskRequest $request, string $id)
    {
        $task = Task::findOrFail($id);
    
        $imagePath = $task->image; // eski rasm saqlanadi
    
        if ($request->hasFile('image')) {
            // eski rasmni o'chirish (agar mavjud bo'lsa)
            if ($imagePath && file_exists(storage_path('app/public/' . $imagePath))) {
                unlink(storage_path('app/public/' . $imagePath));
            }
    
            $file = $request->file('image');
            $destinationPath = storage_path('app/public/tasks');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);
            $imagePath = 'tasks/' . $fileName;
        }
    
        $task->update([
            'image' => $imagePath,
            'telegram' => $request->telegram,
            'instagram' => $request->instagram,
            'youtube' => $request->youtube,
            'twitter' => $request->twitter,
            'text' => $request->text,
            'number' => $request->number,
            'reward' => $request->reward,
        ]);
    
        Cache::forget('tasks'); // Cache yangilanadi
    
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
