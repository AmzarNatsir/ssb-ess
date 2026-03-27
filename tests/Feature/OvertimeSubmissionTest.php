<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Overtime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class OvertimeSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful overtime submission with valid data.
     */
    public function test_employee_can_submit_valid_overtime_request(): void
    {
        Storage::fake('public');

        // Create user and associated karyawan with matching NIK
        $nik = 'EMP' . rand(1000, 9999);
        $user = User::factory()->create(['nik' => $nik]);
        $karyawan = Karyawan::factory()->create(['nik' => $nik]);

        // Prepare valid overtime data
        $overtimeData = [
            'tgl_lembur' => '2024-01-15',
            'jam_mulai' => '18:00',
            'jam_selesai' => '21:00',
            'keterangan_pekerjaan' => 'System maintenance and database optimization',
            'file_surat_lembur' => UploadedFile::fake()->image('overtime_order.jpg')
        ];

        // Submit overtime request
        $response = $this->actingAs($user)->post(route('overtime.store'), $overtimeData);

        // Assert redirect to index with success message
        $response->assertRedirect(route('overtime.index'));
        $response->assertSessionHas('success', 'Overtime request submitted successfully.');

        // Assert overtime record created in database
        $this->assertDatabaseHas('hrd_lembur', [
            'id_karyawan' => $karyawan->id,
            'tgl_lembur' => '2024-01-15',
            'jam_mulai' => '18:00:00',
            'jam_selesai' => '21:00:00',
            'total_jam' => 3.00,
            'keterangan_pekerjaan' => 'System maintenance and database optimization',
            'status_pengupuan' => null
        ]);

        // Assert file was stored
        $overtime = Overtime::where('id_karyawan', $karyawan->id)->first();
        Storage::disk('public')->assertExists($overtime->file_surat_lembur);
    }

    /**
     * Test rejection when overtime duration is less than 1 hour.
     */
    public function test_overtime_submission_rejected_when_duration_less_than_one_hour(): void
    {
        Storage::fake('public');

        $nik = 'EMP' . rand(1000, 9999);
        $user = User::factory()->create(['nik' => $nik]);
        $karyawan = Karyawan::factory()->create(['nik' => $nik]);

        $overtimeData = [
            'tgl_lembur' => '2024-01-15',
            'jam_mulai' => '18:00',
            'jam_selesai' => '18:30', // Only 0.5 hours
            'keterangan_pekerjaan' => 'Quick task',
            'file_surat_lembur' => UploadedFile::fake()->image('overtime_order.jpg')
        ];

        $response = $this->actingAs($user)->post(route('overtime.store'), $overtimeData);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Overtime duration must be at least 1 hour.');

        // Assert no overtime record created
        $this->assertDatabaseMissing('hrd_lembur', [
            'id_karyawan' => $karyawan->id,
            'tgl_lembur' => '2024-01-15'
        ]);
    }

    /**
     * Test rejection when overtime duration exceeds 8 hours.
     */
    public function test_overtime_submission_rejected_when_duration_exceeds_eight_hours(): void
    {
        Storage::fake('public');

        $nik = 'EMP' . rand(1000, 9999);
        $user = User::factory()->create(['nik' => $nik]);
        $karyawan = Karyawan::factory()->create(['nik' => $nik]);

        $overtimeData = [
            'tgl_lembur' => '2024-01-15',
            'jam_mulai' => '08:00',
            'jam_selesai' => '20:00', // 12 hours
            'keterangan_pekerjaan' => 'Extended maintenance',
            'file_surat_lembur' => UploadedFile::fake()->image('overtime_order.jpg')
        ];

        $response = $this->actingAs($user)->post(route('overtime.store'), $overtimeData);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Overtime duration cannot exceed 8 hours.');

        // Assert no overtime record created
        $this->assertDatabaseMissing('hrd_lembur', [
            'id_karyawan' => $karyawan->id,
            'tgl_lembur' => '2024-01-15'
        ]);
    }

    /**
     * Test validation error when end time is before start time.
     */
    public function test_overtime_submission_rejected_when_end_time_before_start_time(): void
    {
        Storage::fake('public');

        $nik = 'EMP' . rand(1000, 9999);
        $user = User::factory()->create(['nik' => $nik]);
        $karyawan = Karyawan::factory()->create(['nik' => $nik]);

        $overtimeData = [
            'tgl_lembur' => '2024-01-15',
            'jam_mulai' => '20:00',
            'jam_selesai' => '18:00', // Before start time
            'keterangan_pekerjaan' => 'Invalid time range',
            'file_surat_lembur' => UploadedFile::fake()->image('overtime_order.jpg')
        ];

        $response = $this->actingAs($user)->post(route('overtime.store'), $overtimeData);

        $response->assertSessionHasErrors('jam_selesai');
    }

    /**
     * Test validation error with invalid file format.
     */
    public function test_overtime_submission_rejected_with_invalid_file_format(): void
    {
        $nik = 'EMP' . rand(1000, 9999);
        $user = User::factory()->create(['nik' => $nik]);
        $karyawan = Karyawan::factory()->create(['nik' => $nik]);

        $overtimeData = [
            'tgl_lembur' => '2024-01-15',
            'jam_mulai' => '18:00',
            'jam_selesai' => '21:00',
            'keterangan_pekerjaan' => 'System maintenance',
            'file_surat_lembur' => UploadedFile::fake()->create('document.pdf', 100) // Invalid format
        ];

        $response = $this->actingAs($user)->post(route('overtime.store'), $overtimeData);

        $response->assertSessionHasErrors('file_surat_lembur');
    }
}
