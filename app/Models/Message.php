<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'message_type_id',
        'content',
        'attachment_path',
        'attachment_name',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Relacionamento com tipo de mensagem
     */
    public function messageType()
    {
        return $this->belongsTo(MessageType::class);
    }

    /**
     * Criptografar conteúdo ao salvar
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Crypt::encryptString($value);
    }

    /**
     * Descriptografar conteúdo ao ler
     */
    public function getContentAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Criptografar nome do anexo ao salvar
     */
    public function setAttachmentNameAttribute($value)
    {
        $this->attributes['attachment_name'] = $value
            ? Str::limit($value, 255, '')
            : null;
    }

    /**
     * Descriptografar nome do anexo ao ler
     */
    public function getAttachmentNameAttribute($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }
}
