<?php

namespace App\Models;

use CodeIgniter\Model;

class CaptchaModel extends Model
{
    protected $table            = 'captchas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['captcha_hash', 'ip_address', 'is_used', 'expires_at', 'created_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function generateCode(int $length = 6): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $maxIndex = strlen($characters) - 1;
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, $maxIndex)];
        }

        return $code;
    }

    public function createCaptcha(string $code, string $ipAddress, int $ttlSeconds = 300): int
    {
        $this->deleteExpired();

        $now = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', time() + $ttlSeconds);

        $this->insert([
            'captcha_hash' => password_hash($code, PASSWORD_DEFAULT),
            'ip_address'   => $ipAddress,
            'is_used'      => 0,
            'expires_at'   => $expiresAt,
            'created_at'   => $now,
        ]);

        return (int) $this->getInsertID();
    }

    public function validateCaptcha(int $captchaId, string $inputCode, string $ipAddress): bool
    {
        $captcha = $this->where('id', $captchaId)
            ->where('ip_address', $ipAddress)
            ->where('is_used', 0)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();

        if (!$captcha) {
            return false;
        }

        $input = strtoupper(trim($inputCode));
        $isValid = password_verify($input, $captcha['captcha_hash']);

        if ($isValid) {
            $this->update($captchaId, ['is_used' => 1]);
            return true;
        }

        return false;
    }

    public function deleteExpired(): void
    {
        $this->builder()
            ->groupStart()
                ->where('expires_at <', date('Y-m-d H:i:s'))
                ->orWhere('is_used', 1)
            ->groupEnd()
            ->delete();
    }
}
