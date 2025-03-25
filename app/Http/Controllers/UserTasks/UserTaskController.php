<?php

namespace App\Http\Controllers\UserTasks;

use App\Http\Controllers\Controller;
use App\Jobs\VerifyUserTask;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserTaskController extends Controller
{
    public function index()
    {
        $task = Cache::remember('task', 60, function () {
            return Task::select('id', 'image', 'telegram', 'instagram', 'youtube', 'twitter', 'text', 'reward')
                ->inRandomOrder()
                ->first();
        });

        return response()->json(['data' => $task]);
    }

    public function verifyTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'code' => 'required|string',
        ]);

        $user = Auth::user();
        $task = Task::findOrFail($request->task_id);

        // 1️⃣ Foydalanuvchi bu taskni bajarganini cache orqali tekshiramiz
        $cacheKey = "task_completed_{$user->id}";
        $completedTasks = Cache::get($cacheKey, []);

        if (in_array($task->id, $completedTasks)) {
            return response()->json([
                'message' => 'Siz bu taskni allaqachon bajargansiz! Yangi taskni 24 soatdan keyin bajarishingiz mumkin.',
                'status' => 'failed',
            ], 403);
        }

        // 2️⃣ Kodni tekshiramiz
        if ($task->number !== $request->code) {
            return response()->json([
                'message' => 'Xato kod! Iltimos, to‘g‘ri kodni kiriting.',
                'status' => 'error',
            ], 400);
        }

        // 3️⃣ Taskni bajarilganlar ro‘yxatiga qo‘shamiz va 24 soatga cache qilamiz
        $completedTasks[] = $task->id;
        Cache::put($cacheKey, $completedTasks, now()->addDay());

        // 4️⃣ Taskni orqa fonda (queue) bajarish uchun Job yuboramiz
        VerifyUserTask::dispatch($user, $task);

        return response()->json([
            'message' => 'Task qabul qilindi, tez orada hisobga qo‘shiladi!',
            'status' => 'success',
        ]);
    }
}
