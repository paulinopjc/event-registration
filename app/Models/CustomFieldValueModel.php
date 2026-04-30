<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomFieldValueModel extends Model
{
    protected $table = 'custom_field_values';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'registration_id', 'custom_field_id', 'value',
    ];
    protected $useTimestamps = false;

    public function getByRegistration(int $registrationId): array
    {
        return $this->select('custom_field_values.*, custom_fields.label, custom_fields.field_type')
                    ->join('custom_fields', 'custom_fields.id = custom_field_values.custom_field_id')
                    ->where('registration_id', $registrationId)
                    ->findAll();
    }
}