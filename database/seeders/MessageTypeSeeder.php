<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageTypeSeeder extends Seeder
{
    /**
     * Seed dos tipos de mensagens padrão
     */
    public function run(): void
    {
        $messageTypes = [
            [
                'name' => 'Sugestão',
                'color' => '#4CAF50',
                'active' => true,
            ],
            [
                'name' => 'Dúvida',
                'color' => '#2196F3',
                'active' => true,
            ],
            [
                'name' => 'Reclamação',
                'color' => '#F44336',
                'active' => true,
            ],
            [
                'name' => 'Ideia',
                'color' => '#FF9800',
                'active' => true,
            ],
            [
                'name' => 'Elogio',
                'color' => '#9C27B0',
                'active' => true,
            ],
        ];

        foreach ($messageTypes as $type) {
            \App\Models\MessageType::create($type);
        }
    }
}
