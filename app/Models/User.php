<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Akun pengguna SIPPM: Operator, Manager, atau Teknisi.
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $role            operator|manager|teknisi
 * @property string|null $phone
 * @property string|null $bagian     Area/Stasiun (operator) atau Bagian/Keahlian (teknisi), "Produksi" untuk manager
 * @property bool $is_active
 * @property bool $must_change_password
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'phone',
        'bagian',
        'is_active',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
    ];

    // ------------------------------------------------------------------
    // Relasi
    // ------------------------------------------------------------------

    public function laporanDibuat(): HasMany
    {
        return $this->hasMany(Laporan::class, 'operator_id');
    }

    public function laporanDitugaskan(): HasMany
    {
        return $this->hasMany(Laporan::class, 'technician_id');
    }

    public function laporanDivalidasi(): HasMany
    {
        return $this->hasMany(Laporan::class, 'manager_id');
    }

    // ------------------------------------------------------------------
    // Helper peran
    // ------------------------------------------------------------------

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isTeknisi(): bool
    {
        return $this->role === 'teknisi';
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'manager' => 'Manager',
            'teknisi' => 'Teknisi',
            default => 'Operator',
        };
    }

    /**
     * Sub-label pada topbar/sidebar, mis. "Operator · Gilingan".
     */
    public function roleSubLabel(): string
    {
        $bagian = $this->bagian ?: match ($this->role) {
            'manager' => 'Produksi',
            'teknisi' => 'Maintenance',
            default => 'Gilingan',
        };

        return $this->roleLabel().' · '.$bagian;
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $letters = array_map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_slice($parts, 0, 2));

        return implode('', $letters) ?: 'U';
    }
}
