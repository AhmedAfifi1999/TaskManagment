<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AIChatController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        // Rate limiting
        $key      = 'ai_chat_' . $request->ip();
        $attempts = cache()->get($key, 0);

        if ($attempts >= 30) {
            return response()->json([
                'reply' => 'تجاوزت الحد المسموح به. حاول بعد 10 دقائق.',
            ], 429);
        }

        cache()->put($key, $attempts + 1, now()->addMinutes(10));

        // ✅ ID المستخدم الحالي فقط
        $userId = $request->user()?->id;

        $ai    = new AIService();
        $reply = $ai->ask($request->message, $userId);

        return response()->json(['reply' => $reply]);
    }
}