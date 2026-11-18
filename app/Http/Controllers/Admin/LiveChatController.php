<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LiveChatController extends Controller
{
    public function index()
    {
        return $this->renderInbox();
    }

    public function show(ChatConversation $conversation)
    {
        if ($conversation->status === 'waiting') $conversation->update(['status' => 'active']);
        $this->markRead($conversation);
        return $this->renderInbox($conversation);
    }

    public function poll(ChatConversation $conversation)
    {
        $this->markRead($conversation);
        $conversation->load(['messages' => fn ($q) => $q->orderBy('id'), 'assignee:id,name']);
        return response()->json([
            'conversation' => $this->conversationData($conversation, true),
            'unreadCount' => $this->unreadCount(),
        ]);
    }

    public function unreadCount()
    {
        return response()->json(['count' => $this->unreadCount()]);
    }

    public function reply(Request $request, ChatConversation $conversation)
    {
        abort_if($conversation->status === 'closed', 409, 'This conversation is closed.');
        $data = $request->validate(['message' => ['required', 'string', 'max:3000']]);
        DB::transaction(function () use ($request, $conversation, $data) {
            $conversation->messages()->create([
                'sender_type' => 'admin',
                'sender_id' => $request->user()->id,
                'message' => $data['message'],
            ]);
            $conversation->update(['status' => 'active', 'last_message_at' => now()]);
        });
        return back()->with('success', 'Reply sent.');
    }

    public function assign(Request $request, ChatConversation $conversation)
    {
        $data = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);
        $admin = User::with('adminRole')->whereKey($data['user_id'])->where('is_admin', true)->first();
        abort_unless($admin && $admin->hasFeature('live_chat'), 422, 'Select an active Live Chat administrator.');
        $conversation->update([
            'assigned_to' => $admin->id,
            'assignment_read_at' => $admin->is($request->user()) ? now() : null,
            'status' => $conversation->status === 'waiting' ? 'active' : $conversation->status,
        ]);
        return back()->with('success', 'Conversation assigned.');
    }

    public function close(ChatConversation $conversation)
    {
        $conversation->update(['status' => 'closed']);
        return back()->with('success', 'Conversation closed.');
    }

    private function renderInbox(?ChatConversation $selected = null)
    {
        $conversations = ChatConversation::with(['assignee:id,name', 'latestMessage'])->orderByDesc('last_message_at')->orderByDesc('id')->get()
            ->map(fn ($conversation) => $this->conversationData($conversation));
        $admins = User::with('adminRole')->where('is_admin', true)->get()
            ->filter(fn ($admin) => $admin->hasFeature('live_chat'))
            ->map(fn ($admin) => ['id' => $admin->id, 'name' => $admin->name])->values();
        return Inertia::render('Admin/LiveChat/Index', [
            'conversations' => $conversations,
            'conversation' => $selected ? $this->conversationData($selected->load(['messages' => fn ($q) => $q->orderBy('id'), 'assignee:id,name']), true) : null,
            'admins' => $admins,
            'unreadCount' => $this->unreadCount(),
        ]);
    }

    private function markRead(ChatConversation $conversation): void
    {
        $conversation->messages()->where('sender_type', 'visitor')->whereNull('read_at')->update(['read_at' => now()]);
        if ($conversation->assigned_to === auth()->id() && $conversation->assignment_read_at === null) {
            $conversation->update(['assignment_read_at' => now()]);
        }
    }

    private function unreadCount(): int
    {
        $userId = auth()->id();
        return ChatConversation::where(function ($q) use ($userId) {
            $q->whereHas('messages', fn ($m) => $m->where('sender_type', 'visitor')->whereNull('read_at'))
                ->orWhere(fn ($a) => $a->where('assigned_to', $userId)->whereNull('assignment_read_at'));
        })->count();
    }

    private function conversationData(ChatConversation $conversation, bool $includeMessages = false): array
    {
        $data = [
            'id' => $conversation->id,
            'visitor_name' => $conversation->visitor_name,
            'visitor_email' => $conversation->visitor_email,
            'visitor_phone' => $conversation->visitor_phone,
            'status' => $conversation->status,
            'assigned_to' => $conversation->assigned_to,
            'assignee' => $conversation->assignee ? ['id' => $conversation->assignee->id, 'name' => $conversation->assignee->name] : null,
            'last_message_at' => $conversation->last_message_at,
            'last_message' => $conversation->latestMessage?->message,
            'unread' => $conversation->messages()->where('sender_type', 'visitor')->whereNull('read_at')->exists(),
        ];
        if ($includeMessages) {
            $data['messages'] = $conversation->messages->map(fn ($message) => [
                'id' => $message->id,
                'sender_type' => $message->sender_type,
                'message' => $message->message,
                'created_at' => $message->created_at,
            ]);
        }
        return $data;
    }
}
