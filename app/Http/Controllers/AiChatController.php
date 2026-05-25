<?php

namespace App\Http\Controllers;

use App\Services\DeepseekService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Cache;

class AiChatController extends Controller
{
    public function ask(Request $request, DeepseekService $deepseek)
    {
        if (!Cache::get('deepseek_ai_enabled', false)) {
            return Response::json([
                'success' => false,
                'message' => 'AI support is currently disabled by the administrator.',
            ], 503);
        }

        $request->validate([
            'question' => 'required|string|max:1000',
        ]);

        $user = $request->user();
        $roles = [];
        if ($user && isset($user->roles)) {
            if (is_array($user->roles)) {
                $roles = array_map(fn($role) => is_array($role) ? ($role['name'] ?? '') : (is_object($role) ? ($role->name ?? '') : ''), $user->roles);
            } else {
                try {
                    $roles = $user->roles->pluck('name')->toArray();
                } catch (\Throwable $e) {
                    $roles = [];
                }
            }
        }

        $isAdmin = in_array('admin', $roles, true);

        $systemPrompt = $isAdmin
            ? 'You are an AI assistant for ClicksIntel. The user is an admin. Answer platform questions with admin-level context when appropriate, but stay factual and do not invent credentials or access data that is not in the system. If the question is unrelated to this platform, politely redirect to platform usage.'
            : 'You are an AI assistant for ClicksIntel. Answer only questions related to affiliate and advertiser use of the platform. Do not reveal or discuss admin-only controls. If the question is about admin functions, respond that admin-level guidance is not available here.';

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $request->question],
        ];

        $result = $deepseek->chat($messages);

        if (!$result['success']) {
            Log::error('AI chat failed', ['message' => $result['message'] ?? 'Unknown error']);
            return Response::json([
                'success' => false,
                'message' => $result['message'] ?? 'DeepSeek AI returned an error.',
            ], 500);
        }

        return Response::json([
            'success' => true,
            'message' => trim($result['content'] ?? $result['data']['choices'][0]['message']['content'] ?? ''),
        ]);
    }
}
