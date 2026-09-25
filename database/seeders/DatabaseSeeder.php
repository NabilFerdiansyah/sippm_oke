<?php

namespace Database\Seeders;

use App\Models\Laporan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed akun demo & contoh laporan, mengikuti persis data yang tampil
     * pada mockup (nama, username, status, nomor laporan, dsb).
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedUsers();
            $this->seedLaporan();
        });
    }

    protected function seedUsers(): void
    {
        // ---- Manager ----
        User::updateOrCreate(['username' => 'sri.manager'], [
            'name' => 'Sri Handayani',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'phone' => '0813-2233-4455',
            'bagian' => 'Produksi',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        // ---- Operator ----
        User::updateOrCreate(['username' => 'andi.operator'], [
            'name' => 'Andi Wijaya',
            'password' => Hash::make('password123'),
            'role' => 'operator',
            'phone' => '0812-3456-7890',
            'bagian' => 'Gilingan',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['username' => 'slamet.operator'], [
            'name' => 'Slamet Riyadi',
            'password' => Hash::make('password123'),
            'role' => 'operator',
            'phone' => '0857-1122-3344',
            'bagian' => 'Boiler / Ketel',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['username' => 'eko.operator'], [
            'name' => 'Eko Purnomo',
            'password' => Hash::make('password123'),
            'role' => 'operator',
            'phone' => '0819-5566-7788',
            'bagian' => 'Stasiun Puteran',
            'is_active' => false,
            'must_change_password' => false,
        ]);

        // ---- Teknisi ----
        User::updateOrCreate(['username' => 'budi.teknisi'], [
            'name' => 'Budi Santoso',
            'password' => Hash::make('password123'),
            'role' => 'teknisi',
            'phone' => '0814-9988-7766',
            'bagian' => 'Mekanik',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['username' => 'rahmat.teknisi'], [
            'name' => 'Rahmat Hidayat',
            'password' => Hash::make('password123'),
            'role' => 'teknisi',
            'phone' => '0821-3344-5566',
            'bagian' => 'Elektrik',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['username' => 'dedi.teknisi'], [
            'name' => 'Dedi Kurniawan',
            'password' => Hash::make('password123'),
            'role' => 'teknisi',
            'phone' => '0838-2211-9900',
            'bagian' => 'Mekanik',
            'is_active' => false,
            'must_change_password' => false,
        ]);
    }

    protected function seedLaporan(): void
    {
        $andi = User::where('username', 'andi.operator')->first();
        $slamet = User::where('username', 'slamet.operator')->first();
        $sri = User::where('username', 'sri.manager')->first();
        $budi = User::where('username', 'budi.teknisi')->first();
        $rahmat = User::where('username', 'rahmat.teknisi')->first();
        $dedi = User::where('username', 'dedi.teknisi')->first();

        $rows = [
            // BR-2026-005 — selesai, riwayat teknisi Dedi
            [
                'kode' => 'BR-2026-005', 'operator_id' => $andi->id, 'station' => 'gilingan',
                'machine' => 'Gilingan 02', 'category' => 'mekanik', 'condition_text' => 'Baut atau sambungan kendor',
                'urgency' => 'rendah', 'incident_date' => '2026-08-02', 'incident_time' => '07:50',
                'description' => 'Baut dudukan gilingan terasa kendor, terdengar bunyi ketukan ringan.',
                'status' => 'selesai',
                'manager_id' => $sri->id, 'validated_at' => '2026-08-02 08:05:00',
                'technician_id' => $dedi->id, 'assignment_priority' => 'rendah',
                'assignment_note' => 'Cek dan kencangkan seluruh baut dudukan.',
                'work_start_time' => '08:10', 'assigned_at' => '2026-08-02 08:07:00',
                'started_at' => '2026-08-02 08:10:00',
                'inspection_result' => 'Ditemukan beberapa baut dudukan kendor pada sisi kiri gilingan.',
                'root_cause' => 'Baut kendor', 'action_taken' => 'Pengencangan baut',
                'components_text' => 'Baut M16 dudukan gilingan — 6 unit, dikencangkan ulang.',
                'work_end_time' => '08:35', 'additional_note' => null,
                'submitted_for_validation_at' => '2026-08-02 08:40:00',
                'final_manager_note' => 'Disetujui, kondisi sudah normal kembali.',
                'final_validated_at' => '2026-08-02 09:00:00',
            ],
            // BR-2026-008 — selesai, riwayat teknisi Budi
            [
                'kode' => 'BR-2026-008', 'operator_id' => $andi->id, 'station' => 'gilingan',
                'machine' => 'Gilingan 01', 'category' => 'mekanik', 'condition_text' => 'Bearing rusak/aus',
                'urgency' => 'sedang', 'incident_date' => '2026-08-05', 'incident_time' => '13:15',
                'description' => 'Terdengar suara berdecit pada bearing gilingan 01, disertai getaran ringan.',
                'status' => 'selesai',
                'manager_id' => $sri->id, 'validated_at' => '2026-08-05 13:25:00',
                'technician_id' => $budi->id, 'assignment_priority' => 'sedang',
                'assignment_note' => 'Periksa kondisi bearing secara menyeluruh.',
                'work_start_time' => '13:30', 'assigned_at' => '2026-08-05 13:28:00',
                'started_at' => '2026-08-05 13:30:00',
                'inspection_result' => 'Bearing mengalami keausan cukup parah pada sisi kiri poros.',
                'root_cause' => 'Keausan bearing', 'action_taken' => 'Penggantian bearing',
                'components_text' => 'Bearing 6206 sisi kiri poros — 1 unit, diganti baru.',
                'work_end_time' => '15:30', 'additional_note' => null,
                'submitted_for_validation_at' => '2026-08-05 15:35:00',
                'final_manager_note' => 'Disetujui, downtime sesuai standar penanganan bearing.',
                'final_validated_at' => '2026-08-05 16:00:00',
            ],
            // BR-2026-009 — ditolak
            [
                'kode' => 'BR-2026-009', 'operator_id' => $andi->id, 'station' => 'gilingan',
                'machine' => 'Gilingan 01', 'category' => 'mekanik', 'condition_text' => 'Getaran mesin berlebihan',
                'urgency' => 'sedang', 'incident_date' => '2026-08-08', 'incident_time' => '10:10',
                'description' => 'Getaran terasa lebih kuat dari biasanya namun belum diperiksa detail penyebabnya.',
                'status' => 'ditolak',
                'manager_id' => $sri->id, 'validated_at' => '2026-08-08 10:40:00',
                'rejection_reason' => 'Laporan belum memiliki informasi kondisi mesin yang cukup. Mohon lengkapi detail lokasi getaran dan sertakan foto kondisi mesin.',
            ],
            // BR-2026-011 — selesai (contoh downtime 40 menit)
            [
                'kode' => 'BR-2026-011', 'operator_id' => $andi->id, 'station' => 'gilingan',
                'machine' => 'Gilingan 02', 'category' => 'mekanik', 'condition_text' => 'Baut atau sambungan kendor',
                'urgency' => 'rendah', 'incident_date' => '2026-08-10', 'incident_time' => '09:00',
                'description' => 'Sensor level pada Gilingan 02 tampak kendor dari dudukannya.',
                'status' => 'selesai',
                'manager_id' => $sri->id, 'validated_at' => '2026-08-10 09:10:00',
                'technician_id' => $budi->id, 'assignment_priority' => 'rendah',
                'assignment_note' => 'Periksa kondisi sensor dan kencangkan bila perlu.',
                'work_start_time' => '09:00', 'assigned_at' => '2026-08-10 09:12:00',
                'started_at' => '2026-08-10 09:15:00',
                'inspection_result' => 'Sensor longgar pada dudukan, kabel dalam kondisi baik.',
                'root_cause' => 'Sensor longgar', 'action_taken' => 'Pengencangan sensor',
                'components_text' => 'Dudukan sensor level — dikencangkan ulang, tidak ada penggantian komponen.',
                'work_end_time' => '09:40', 'additional_note' => null,
                'submitted_for_validation_at' => '2026-08-10 09:45:00',
                'final_manager_note' => 'Disetujui, kondisi mesin sudah normal.',
                'final_validated_at' => '2026-08-10 10:00:00',
            ],
            // BR-2026-012 — menunggu validasi akhir (persis contoh di mockup)
            [
                'kode' => 'BR-2026-012', 'operator_id' => $andi->id, 'station' => 'gilingan',
                'machine' => 'Gilingan 02', 'category' => 'mekanik', 'condition_text' => 'Bearing rusak/aus',
                'urgency' => 'tinggi', 'incident_date' => '2026-08-13', 'incident_time' => '08:20',
                'description' => 'Bearing pada Gilingan 02 kembali menimbulkan suara kasar setelah beberapa hari beroperasi.',
                'status' => 'menunggu_validasi_akhir',
                'manager_id' => $sri->id, 'validated_at' => '2026-08-13 08:40:00',
                'technician_id' => $budi->id, 'assignment_priority' => 'tinggi',
                'assignment_note' => 'Periksa kondisi bearing dan sistem transmisi pada sisi kanan gilingan.',
                'work_start_time' => '10:05', 'assigned_at' => '2026-08-13 08:55:00',
                'started_at' => '2026-08-13 10:05:00',
                'inspection_result' => 'Ditemukan keausan pada bearing sisi kanan poros gilingan, menyebabkan getaran dan suara abnormal.',
                'root_cause' => 'Keausan bearing', 'action_taken' => 'Penggantian bearing',
                'components_text' => 'Bearing 6205 sisi kanan poros — 1 unit, diganti baru.',
                'work_end_time' => '11:40', 'additional_note' => null,
                'submitted_for_validation_at' => '2026-08-13 11:45:00',
            ],
            // BR-2026-013 — dalam penanganan (dikerjakan)
            [
                'kode' => 'BR-2026-013', 'operator_id' => $slamet->id, 'station' => 'gilingan',
                'machine' => 'Gilingan 01', 'category' => 'elektrik', 'condition_text' => 'Motor listrik tidak mau hidup',
                'urgency' => 'sedang', 'incident_date' => '2026-08-13', 'incident_time' => '14:10',
                'description' => 'Motor penggerak Gilingan 01 tidak mau menyala saat tombol start ditekan.',
                'status' => 'dikerjakan',
                'manager_id' => $sri->id, 'validated_at' => '2026-08-13 14:15:00',
                'technician_id' => $rahmat->id, 'assignment_priority' => 'sedang',
                'assignment_note' => 'Periksa kontaktor dan sambungan kabel motor.',
                'work_start_time' => '14:20', 'assigned_at' => '2026-08-13 14:18:00',
                'started_at' => '2026-08-13 14:20:00',
            ],
            // BR-2026-014 — menunggu validasi (persis contoh di mockup)
            [
                'kode' => 'BR-2026-014', 'operator_id' => $andi->id, 'station' => 'gilingan',
                'machine' => 'Gilingan 02', 'category' => 'mekanik', 'condition_text' => 'Bearing rusak/aus',
                'urgency' => 'tinggi', 'incident_date' => '2026-08-14', 'incident_time' => '09:40',
                'description' => 'Terdengar suara gesekan kasar sejak pukul 09.30 pada sisi kanan gilingan, disertai getaran yang lebih kuat dari biasanya. Produksi masih berjalan namun perlu segera diperiksa.',
                'status' => 'menunggu_validasi',
            ],
        ];

        foreach ($rows as $row) {
            Laporan::updateOrCreate(['kode' => $row['kode']], $row);
        }
    }
}
