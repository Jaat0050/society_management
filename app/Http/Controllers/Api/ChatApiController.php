<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatMessageSent;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatApiController extends Controller
{
    public function rooms(Request $request)
    {
        $rooms = ChatRoom::where('society_id', $this->societyId())
            ->where('is_active', true)
            ->orderBy('type')
            ->get(['id', 'name', 'type', 'flat_id']);

        return $this->ok($rooms);
    }

    public function messages(Request $request, int $roomId)
    {
        $room = $this->roomForUser($request, $roomId);

        $after = (int) $request->query('after_id', 0);

        $query = ChatMessage::where('chat_room_id', $room->id)
            ->with('user:id,name,role')
            ->orderBy('id');

        if ($after > 0) {
            $query->where('id', '>', $after);
        } else {
            $query->latest('id')->limit(50);
        }

        $messages = ($after > 0 ? $query->get() : $query->get()->reverse()->values());

        return $this->ok([
            'room' => $room->only(['id', 'name', 'type']),
            'messages' => $messages->map(fn ($m) => $this->messagePayload($m)),
        ]);
    }

    public function send(Request $request, int $roomId)
    {
        $room = $this->roomForUser($request, $roomId);

        $data = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = ChatMessage::create([
            'chat_room_id' => $room->id,
            'user_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);

        $message->load('user:id,name,role');

        broadcast(new ChatMessageSent($message))->toOthers();

        return $this->ok($this->messagePayload($message), 'Message sent', 201);
    }

    /** SSE stream for live chat (Chrome / web). Poll fallback on mobile. */
    public function stream(Request $request, int $roomId): StreamedResponse
    {
        $room = $this->roomForUser($request, $roomId);
        $after = (int) $request->query('after_id', 0);

        return response()->stream(function () use ($room, $after) {
            $lastId = $after;
            $iterations = 0;
            while ($iterations < 120 && ! connection_aborted()) {
                $new = ChatMessage::where('chat_room_id', $room->id)
                    ->where('id', '>', $lastId)
                    ->with('user:id,name,role')
                    ->orderBy('id')
                    ->get();

                if ($new->isNotEmpty()) {
                    $payload = $new->map(fn ($m) => $this->messagePayload($m))->values();
                    echo 'data: '.json_encode($payload)."\n\n";
                    $lastId = $new->last()->id;
                    ob_flush();
                    flush();
                } else {
                    echo ": heartbeat\n\n";
                    ob_flush();
                    flush();
                }
                sleep(2);
                $iterations++;
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    protected function roomForUser(Request $request, int $roomId): ChatRoom
    {
        return ChatRoom::where('society_id', $request->user()->society_id)
            ->where('is_active', true)
            ->findOrFail($roomId);
    }

    protected function messagePayload(ChatMessage $m): array
    {
        return [
            'id' => $m->id,
            'chat_room_id' => $m->chat_room_id,
            'body' => $m->body,
            'user' => [
                'id' => $m->user->id,
                'name' => $m->user->name,
                'role' => $m->user->role,
            ],
            'created_at' => $m->created_at?->toIso8601String(),
        ];
    }
}
