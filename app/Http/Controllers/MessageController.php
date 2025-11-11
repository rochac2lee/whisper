<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function create()
    {
        $messageTypes = MessageType::where('active', true)->get();

        return Inertia::render('Public/SendMessage', [
            'messageTypes' => $messageTypes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message_type_id' => 'required|exists:message_types,id',
            'content' => 'required|string|max:5000',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $data = $request->only(['message_type_id', 'content']);

        $uploadedPath = null;

        try {
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = now()->timestamp . '_' . Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                $uploadedPath = $file->storeAs('attachments', $filename, 'attachments');

                $data['attachment_path'] = $uploadedPath;
                $data['attachment_name'] = $file->getClientOriginalName();
            }

            Message::create($data);
        } catch (\Throwable $e) {
            if ($uploadedPath) {
                Storage::disk('attachments')->delete($uploadedPath);
            }
            throw $e;
        }

        return redirect()->route('home')->with('success', 'Mensagem enviada com sucesso!');
    }

    public function index(Request $request)
    {
        $query = Message::with('messageType');

        if ($request->message_type_id) {
            $query->where('message_type_id', $request->message_type_id);
        }

        if ($request->is_read !== null) {
            $query->where('is_read', $request->is_read);
        }

        $messages = $query->latest()->paginate(20);
        $messageTypes = MessageType::all();

        return Inertia::render('Admin/Messages/Index', [
            'messages' => $messages,
            'messageTypes' => $messageTypes,
            'filters' => $request->only(['message_type_id', 'is_read', 'search']),
        ]);
    }

    public function show(Message $message)
    {
        if (!$message->is_read) {
            $message->is_read = true;
            $message->save();
        }

        return Inertia::render('Admin/Messages/Show', [
            'message' => $message->load('messageType'),
        ]);
    }

    public function destroy(Message $message)
    {
        if ($message->attachment_path) {
            $attachmentsDisk = Storage::disk('attachments');

            if ($attachmentsDisk->exists($message->attachment_path)) {
                $attachmentsDisk->delete($message->attachment_path);
            }
        }

        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Mensagem excluída com sucesso!');
    }

    public function downloadAttachment(Message $message)
    {
        if (!$message->attachment_path) {
            abort(404);
        }

        $disk = Storage::disk('attachments');

        if (!$disk->exists($message->attachment_path)) {
            abort(404);
        }

        return response()->streamDownload(
            function () use ($disk, $message) {
                $stream = $disk->readStream($message->attachment_path);
                if ($stream === false) {
                    abort(404);
                }
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            },
            $message->attachment_name,
            [
                'Content-Type' => 'application/octet-stream',
            ]
        );
    }

    public function toggleRead(Message $message)
    {
        $message->is_read = !$message->is_read;
        $message->save();

        return back()->with('success', 'Status atualizado com sucesso!');
    }
}
