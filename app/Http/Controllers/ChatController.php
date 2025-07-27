<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    /**
     * Display the chat interface
     */
    public function index(): Response
    {
        return Inertia::render('chat/Index');
    }

    /**
     * Send message to CatéGPT API
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:4000',
            'conversation_history' => 'array',
        ]);

        try {
            $apiKey = config('services.categpt.key');
            
            if (!$apiKey) {
                Log::error('CatéGPT API key not configured');
                return response()->json([
                    'error' => 'CatéGPT API key not configured. Please add CATEGPT_API_KEY to your .env file.'
                ], 500);
            }

            // Prepare the request for CatéGPT API
            $requestData = [
                'question' => $request->message,
                'lang' => 'en', // Default to English, can be made configurable
                'modechat' => 1, // Use conversational mode for better chat experience
            ];

            // Add conversation context if available
            $conversationHistory = $request->input('conversation_history', []);
            if (!empty($conversationHistory) && is_array($conversationHistory)) {
                $lastMessage = end($conversationHistory);
                if (isset($lastMessage['uniqueID'])) {
                    $requestData['reply_to_message_id'] = $lastMessage['uniqueID'];
                }
            }

            Log::info('Sending request to CatéGPT', [
                'question' => $request->message,
                'lang' => $requestData['lang'],
                'modechat' => $requestData['modechat']
            ]);

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://categpt.chat/api/question', $requestData);

            Log::info('CatéGPT API Response', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['stat'] === 'ok') {
                    // Format the response based on modechat setting
                    $responseContent = '';
                    
                    if ($requestData['modechat'] == 1) {
                        // Conversational mode - single string response
                        $responseContent = $data['reponse'];
                    } else {
                        // Structured mode - format the array response
                        $responseContent = $this->formatStructuredResponse($data['reponse']);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => $responseContent,
                        'uniqueID' => $data['uniqueID'] ?? null,
                        'references' => $data['references'] ?? [],
                        'keywords' => $data['keywords'] ?? [],
                        'others' => $data['others'] ?? [],
                        'all_tokens' => $data['all_tokens'] ?? null,
                        'stat' => $data['stat'],
                    ]);
                } else {
                    Log::error('CatéGPT API returned error status', ['data' => $data]);
                    return response()->json([
                        'error' => 'Failed to get response from CatéGPT service'
                    ], 500);
                }
            } else {
                $errorData = $response->json();
                Log::error('CatéGPT API Error', [
                    'status' => $response->status(),
                    'response' => $errorData,
                    'headers' => $response->headers(),
                ]);

                $errorMessage = 'Failed to get response from CatéGPT service';
                if (isset($errorData['error'])) {
                    $errorMessage = $errorData['error'];
                }

                return response()->json([
                    'error' => $errorMessage
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Chat API Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An error occurred while processing your request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format structured response from CatéGPT
     */
    private function formatStructuredResponse($response): string
    {
        if (!is_array($response)) {
            return $response;
        }

        $formattedResponse = '';
        
        foreach ($response as $section) {
            if (isset($section['type']) && isset($section['text'])) {
                $type = ucfirst($section['type']);
                $formattedResponse .= "**{$type}:**\n{$section['text']}\n\n";
            }
        }

        return trim($formattedResponse);
    }

    /**
     * Get chat history (if you want to implement persistence)
     */
    public function getHistory(): JsonResponse
    {
        // This could be implemented to store/retrieve chat history from database
        return response()->json([
            'history' => []
        ]);
    }

    /**
     * Retrieve a specific answer by uniqueID
     */
    public function getSpecificAnswer(Request $request): JsonResponse
    {
        $request->validate([
            'uniqueID' => 'required|string',
        ]);

        try {
            $apiKey = config('services.categpt.key');
            
            if (!$apiKey) {
                return response()->json([
                    'error' => 'CatéGPT API key not configured'
                ], 500);
            }

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->get("https://categpt.chat/api/post/{$request->uniqueID}");

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['stat'] === 'ok') {
                    return response()->json([
                        'success' => true,
                        'data' => $data,
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Failed to retrieve the specific answer'
                    ], 500);
                }
            } else {
                return response()->json([
                    'error' => 'Failed to retrieve the specific answer'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Get Specific Answer Error', [
                'message' => $e->getMessage(),
                'uniqueID' => $request->uniqueID,
            ]);

            return response()->json([
                'error' => 'An error occurred while retrieving the answer'
            ], 500);
        }
    }
} 