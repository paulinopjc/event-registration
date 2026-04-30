<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistrationModel extends Model
{
    protected $table = 'registrations';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'event_id', 'ticket_type_id', 'confirmation_code',
        'first_name', 'last_name', 'email', 'phone', 'company',
        'status', 'checked_in_at', 'qr_code_path',
    ];
    protected $useTimestamps = true;

    public function getByCode(string $code)
    {
        return $this->where('confirmation_code', $code)->first();
    }

    public function getForEvent(int $eventId, array $filters = [])
    {
        $builder = $this->select('registrations.*, ticket_types.name as ticket_type_name')
            ->join('ticket_types', 'ticket_types.id = registrations.ticket_type_id')
            ->where('registrations.event_id', $eventId);

        if (!empty($filters['status'])) {
            $builder->where('registrations.status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('registrations.first_name', $filters['search'])
                ->orLike('registrations.last_name', $filters['search'])
                ->orLike('registrations.email', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('registrations.created_at', 'DESC')->findAll();
    }

    public function countByTicketType(int $eventId): array
    {
        return $this->select('ticket_type_id, COUNT(*) as count')
            ->where('event_id', $eventId)
            ->where('status !=', 'cancelled')
            ->groupBy('ticket_type_id')
            ->findAll();
    }

    public static function generateCode(): string
    {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $code = 'EVT-';
        for ($i = 0; $i < 6; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }
}