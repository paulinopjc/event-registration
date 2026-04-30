<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'name', 'slug', 'description', 'venue',
        'event_date', 'event_end_date', 'banner_image',
        'status', 'max_registrations',
    ];
    protected $useTimestamps = true;
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[500]',
        'slug' => 'required|alpha_dash|max_length[500]',
        'event_date' => 'required|valid_date',
    ];

    public function getBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    public function getPublished()
    {
        return $this->where('status', 'published')
            ->orderBy('event_date', 'ASC')
            ->findAll();
    }
}