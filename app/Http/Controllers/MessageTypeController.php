<?php

namespace App\Http\Controllers;

use App\Models\MessageType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageTypeController extends Controller
{
    public function index()
    {
        $messageTypes = MessageType::withCount('messages')
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Admin/MessageTypes/Index', [
            'messageTypes' => $messageTypes,
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.message-types.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'active' => 'boolean',
        ]);

        MessageType::create($request->all());

        return redirect()->route('admin.message-types.index')->with('success', 'Tipo de mensagem criado com sucesso!');
    }

    public function edit(MessageType $messageType)
    {
        return redirect()->route('admin.message-types.index');
    }

    public function update(Request $request, MessageType $messageType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'active' => 'boolean',
        ]);

        $messageType->update($request->all());

        return redirect()->route('admin.message-types.index')->with('success', 'Tipo de mensagem atualizado com sucesso!');
    }

    public function destroy(MessageType $messageType)
    {
        if ($messageType->messages()->count() > 0) {
            return back()->withErrors(['error' => 'Não é possível deletar um tipo de mensagem que possui mensagens associadas.']);
        }

        $messageType->delete();

        return redirect()->route('admin.message-types.index')->with('success', 'Tipo de mensagem deletado com sucesso!');
    }
}
