<?php

namespace App\Models;

use CodeIgniter\Model;

class GuestListModel extends Model
{
    protected $table = 'guest_lists';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'event_id', 'first_name', 'last_name', 'email',
        'is_registered', 'registration_id',
    ];
    protected $useTimestamps = true;
    protected $updatedField = '';

    public function getForEvent(int $eventId): array
    {
        return $this->where('event_id', $eventId)
            ->orderBy('last_name', 'ASC')
            ->findAll();
    }

    public function findByEmail(int $eventId, string $email)
    {
        return $this->where('event_id', $eventId)
            ->where('LOWER(email)', strtolower($email))
            ->first();
    }

    public function markRegistered(int $guestId, int $registrationId): bool
    {
        return $this->update($guestId, [
            'is_registered' => true,
            'registration_id' => $registrationId,
        ]);
    }
}
