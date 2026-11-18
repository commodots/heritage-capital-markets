<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\Enquiry;
use App\Models\SiteSetting;
use App\Support\ChatAvailability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function availability(): JsonResponse
    {
        return response()->json([
            'is_open' => ChatAvailability::isOpen(),
            'offline_message' => SiteSetting::query()->where('key', 'chat_offline_message')->value('value')
                ?? 'Our office is currently closed. Please leave an enquiry and our team will respond.',
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        if (! ChatAvailability::isOpen()) {
            return response()->json([
                'message' => 'Live chat is currently unavailable. Please use the offline enquiry form.',
                'offline' => true,
            ], 409);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:3000'],
        ]);
        $token = Str::random(64);

        $conversation = DB::transaction(function () use ($data, $token) {
            $conversation = ChatConversation::create([
                'visitor_token' => $token,
                'visitor_name' => $data['name'],
                'visitor_email' => $data['email'],
                'visitor_phone' => $data['phone'] ?? null,
                'status' => 'waiting',
                'last_message_at' => now(),
            ]);
            $conversation->messages()->create([
                'sender_type' => 'visitor',
                'message' => $data['message'],
            ]);
            return $conversation;
        });

        $request->session()->put('live_chat_token', $token);

        $email = SiteSetting::query()->where('key', 'chat_notification_email')->value('value');
        if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::raw(
                    "New live chat conversation from {$conversation->visitor_name} ({$conversation->visitor_email}).",
                    fn ($message) => $message->to($email)->subject('New HCML live chat conversation')
                );
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return response()->json([
            'status' => $conversation->status,
            'messages' => $conversation->messages()->orderBy('id')->get(['id', 'sender_type', 'message', 'created_at']),
        ], 201);
    }

    public function message(Request $request): JsonResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:3000']]);
        $conversation = $this->visitorConversation($request);
        abort_unless($conversation, 404, 'Chat conversation not found.');
        abort_if($conversation->status === 'closed', 409, 'This conversation is closed.');
        $conversation->messages()->create(['sender_type' => 'visitor', 'message' => $data['message']]);
        $conversation->update(['last_message_at' => now()]);
        return response()->json(['status' => $conversation->status]);
    }

    public function messages(Request $request): JsonResponse
    {
        $conversation = $this->visitorConversation($request);
        abort_unless($conversation, 404, 'Chat conversation not found.');
        return response()->json([
            'status' => $conversation->status,
            'messages' => $conversation->messages()->orderBy('id')->get(['id', 'sender_type', 'message', 'created_at']),
        ]);
    }

    public function offlineEnquiry(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:3000'],
        ]);
        Enquiry::create([
            ...$data,
            'subject' => 'Live Chat Offline Enquiry',
            'source' => 'live_chat_offline',
            'status' => 'new',
        ]);
        return response()->json(['message' => 'Thank you. Your enquiry has been received and our team will contact you.'], 201);
    }

    private function visitorConversation(Request $request): ?ChatConversation
    {
        $token = $request->session()->get('live_chat_token');
        if (! is_string($token) || $token === '') return null;
        return ChatConversation::query()->where('visitor_token', $token)->first();
    }
}
