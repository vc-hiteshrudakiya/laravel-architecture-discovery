<?php

namespace Viitorcloud\LaravelArchitectureDiscovery\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Viitorcloud\LaravelArchitectureDiscovery\AI\AIManager;
use Viitorcloud\LaravelArchitectureDiscovery\ArchitectureDiscovery;

class AIController extends Controller
{
    private const DOC_TYPES = ['architecture', 'models', 'controllers', 'routes', 'services', 'modules'];

    public function analyze(ArchitectureDiscovery $discovery, AIManager $ai): JsonResponse
    {
        if (!$ai->isEnabled()) {
            return response()->json([
                'error' => 'AI is disabled. Set ai.enabled = true and configure GEMINI_API_KEY in your .env.',
            ], 503);
        }

        try {
            $report   = $discovery->discover();
            $response = $ai->analyze($report->getReport());
            return response()->json($response->toArray());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function chat(Request $request, AIManager $ai): JsonResponse
    {
        if (!$ai->isEnabled()) {
            return response()->json(['error' => 'AI is disabled.'], 503);
        }

        $message = trim($request->input('message', ''));
        if (empty($message)) {
            return response()->json(['error' => 'Message is required.'], 422);
        }

        try {
            $context = $request->input('context', []);
            $reply   = $ai->chat($message, $context);
            return response()->json(['reply' => $reply]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function documentation(Request $request, ArchitectureDiscovery $discovery, AIManager $ai): JsonResponse
    {
        if (!$ai->isEnabled()) {
            return response()->json(['error' => 'AI is disabled.'], 503);
        }

        $type = $request->input('type', 'architecture');
        if (!in_array($type, self::DOC_TYPES, true)) {
            return response()->json(['error' => 'Invalid doc type. Valid: ' . implode(', ', self::DOC_TYPES)], 422);
        }

        try {
            $report  = $discovery->discover();
            $content = $ai->generateDocumentation($report->getReport(), $type);
            return response()->json(['content' => $content, 'type' => $type, 'filename' => ucfirst($type) . '.md']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
