<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketTypeModel extends Model
{
    protected $table = 'ticket_types';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'event_id', 'name', 'description', 'price', 'capacity',
        'sort_order', 'is_active',
    ];
    protected $useTimestamps = true;
    protected $updatedField = '';

    public function getByEvent(int $eventId): array
    {
        return $this->where('event_id', $eventId)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    public function getActiveByEvent(int $eventId): array
    {
        return $this->where('event_id', $eventId)
                    ->where('is_active', true)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }
}