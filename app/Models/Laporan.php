<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Laporan Kerusakan / Abnormalitas Mesin Giling.
 *
 * Alur status:
 *  menunggu_validasi -> (Manager tolak) -> ditolak [selesai]
 *  menunggu_validasi -> (Manager terima) -> menunggu_penugasan
 *  menunggu_penugasan -> (Manager tugaskan teknisi) -> ditugaskan
 *  ditugaskan -> (Teknisi mulai pemeriksaan) -> dikerjakan
 *  dikerjakan -> (Teknisi kirim hasil) -> menunggu_validasi_akhir
 *  menunggu_validasi_akhir -> (Manager setujui) -> selesai [selesai]
 *  menunggu_validasi_akhir -> (Manager kembalikan) -> ditugaskan
 */
class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporans';

    protected $fillable = [
        'kode',
        'operator_id',
        'station',
        'machine',
        'category',
        'condition_text',
        'urgency',
        'incident_date',
        'incident_time',
        'description',
        'photo_before',
        'status',
        'rejection_reason',
        'manager_id',
        'validated_at',
        'technician_id',
        'assignment_priority',
        'assignment_note',
        'work_start_time',
        'assigned_at',
        'started_at',
        'inspection_result',
        'root_cause',
        'action_taken',
        'components_text',
        'work_end_time',
        'additional_note',
        'photo_after',
        'submitted_for_validation_at',
        'final_manager_note',
        'final_validated_at',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'validated_at' => 'datetime',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'submitted_for_validation_at' => 'datetime',
        'final_validated_at' => 'datetime',
    ];

    // ------------------------------------------------------------------
    // Relasi
    // ------------------------------------------------------------------

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LaporanActivity::class)->latest();
    }

    public function recordActivity(?User $user, string $action, ?string $fromStatus = null, ?string $toStatus = null, ?string $description = null, array $metadata = []): LaporanActivity
    {
        return $this->activities()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'description' => $description,
            'metadata' => $metadata ?: null,
        ]);
    }

    // ------------------------------------------------------------------
    // Label & badge (disamakan persis dengan kelas badge pada mockup)
    // ------------------------------------------------------------------

    public static function statusOptions(): array
    {
        return [
            'menunggu_validasi' => 'Menunggu Validasi',
            'ditolak' => 'Ditolak',
            'menunggu_penugasan' => 'Diterima',
            'ditugaskan' => 'Ditugaskan',
            'dikerjakan' => 'Dalam Penanganan',
            'menunggu_validasi_akhir' => 'Menunggu Validasi Akhir',
            'selesai' => 'Selesai',
        ];
    }

    /**
     * Label status detail (dipakai di layar Detail Laporan / Detail Tugas).
     */
    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    /**
     * Label status "ringkas" seperti pada daftar/tabel di mockup, dimana
     * ditugaskan & dikerjakan sama-sama tampil sebagai "Dalam Penanganan".
     */
    public function statusLabelRingkas(): string
    {
        if (in_array($this->status, ['ditugaskan', 'dikerjakan'], true)) {
            return 'Dalam Penanganan';
        }

        return $this->statusLabel();
    }

    /**
     * Kelas badge (b-amber/b-blue/b-green/b-red/b-mustard/b-gray) — identik
     * dengan kelas CSS pada mockup.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'menunggu_validasi' => 'b-amber',
            'ditolak' => 'b-red',
            'menunggu_penugasan' => 'b-green',
            'ditugaskan', 'dikerjakan' => 'b-blue',
            'menunggu_validasi_akhir' => 'b-mustard',
            'selesai' => 'b-green',
            default => 'b-gray',
        };
    }

    public function urgencyClass(): string
    {
        return match ($this->urgency) {
            'tinggi' => 'tinggi',
            'sedang' => 'sedang',
            default => 'rendah',
        };
    }

    public function urgencyLabel(): string
    {
        return match ($this->urgency) {
            'tinggi' => 'Tinggi',
            'sedang' => 'Sedang',
            default => 'Rendah',
        };
    }

    public function categoryLabel(): string
    {
        return config('sippm.category_labels')[$this->category] ?? ucfirst($this->category);
    }

    public function stationLabel(): string
    {
        return config('sippm.station_labels')[$this->station] ?? ucfirst($this->station);
    }

    /**
     * Downtime (menit) = Waktu Selesai Penanganan − Waktu Pengerjaan Dimulai,
     * persis seperti rumus pada mockup ("Downtime akan dihitung otomatis").
     */
    public function downtimeMinutes(): ?int
    {
        if (! $this->work_start_time || ! $this->work_end_time) {
            return null;
        }

        $start = Carbon::createFromFormat('H:i', substr($this->work_start_time, 0, 5));
        $end = Carbon::createFromFormat('H:i', substr($this->work_end_time, 0, 5));

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        return $start->diffInMinutes($end);
    }

    public function downtimeLabel(): ?string
    {
        $minutes = $this->downtimeMinutes();

        return $minutes === null ? null : $minutes.' menit';
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->operator_id === $user->id;
    }

    public function isAssignedTo(User $user): bool
    {
        return $this->technician_id === $user->id;
    }

    /**
     * Generate nomor laporan otomatis dengan format BR-{tahun}-{urut}, sesuai
     * pola contoh pada mockup (mis. BR-2026-014).
     */
    public static function generateKode(): string
    {
        $year = now()->year;
        $prefix = "BR-{$year}-";

        $last = static::where('kode', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('kode');

        $next = 1;
        if ($last) {
            $next = ((int) substr($last, strlen($prefix))) + 1;
        }

        return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
