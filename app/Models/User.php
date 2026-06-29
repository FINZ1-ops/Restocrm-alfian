<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model pengguna sistem (login).
 *
 * Tidak memakai BaseRestaurantModel karena tabel `users` bersifat global:
 * super_admin tidak punya restaurant_id, sedangkan staff restoran punya.
 */
class User extends Model
{
    protected $table          = 'users';
    protected $primaryKey     = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    protected $allowedFields  = [
        'email', 'password', 'name', 'role',
        'restaurant_id', 'is_active',
    ];

    protected $validationRules = [
        'email'    => 'required|valid_email|max_length[255]',
        'password' => 'required|min_length[8]',
        'name'     => 'required|min_length[3]|max_length[255]',
        'role'     => 'required|in_list[super_admin,sales,admin_resto,kasir,dapur,customer]',
    ];

    // ── Query Helpers ──────────────────────────────────────────────────────

    /**
     * Cari satu user by email — dipanggil dari Auth::login().
     */
    public function getUserByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Ambil semua user dalam satu restoran
     */
    public function getByRestaurant(int $restaurantId): array
    {
        return $this->where('restaurant_id', $restaurantId)->findAll();
    }

    /**
     * Ambil semua user berdasarkan role
     */
    public function getByRole(string $role): array
    {
        return $this->where('role', $role)->findAll();
    }

    // ── Password Helpers (bcrypt) ──────────────────────────────────────────

    /** Hash password sebelum disimpan ke database */
    public function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT);
    }

    /** Cocokkan password plain text dengan hash di database */
    public function verifyPassword(string $plain, string $hashed): bool
    {
        return password_verify($plain, $hashed);
    }
}
