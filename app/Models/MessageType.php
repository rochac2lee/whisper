<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'color',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relacionamento com mensagens
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
