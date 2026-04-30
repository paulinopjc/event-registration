<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomFieldModel extends Model
{
    protected $table = 'custom_fields';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'event_id', 'label', 'field_type', 'options',
        'is_required', 'sort_order',
    ];
    protected $useTimestamps = false;

    public function getByEvent(int $eventId): array
    {
        return $this->where('event_id', $eventId)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }
}