<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\User;
use App\Models\WorkOfExtension;
use App\Models\WorkStatus;
use Database\Seeders\OrganizationalUnitSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WorkStatusSeeder;
use Database\Seeders\WorkTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificationDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar seeders necesarios
        $this->seed([
            RoleSeeder::class,
            WorkStatusSeeder::class,
            WorkTypeSeeder::class,
            OrganizationalUnitSeeder::class,
        ]);
    }

    /** @test */
    public function profesor_can_download_own_certified_work_certificate()
    {
        // Crear usuario profesor
        $professor = User::factory()->create();
        $professor->assignRole('profesor');

        // Crear trabajo certificado
        $certifiedStatus = WorkStatus::where('name', 'Certificado')->first();
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $professor->id,
            'current_status_id' => $certifiedStatus->id,
        ]);

        // Crear certificación con archivo
        $certification = Certification::factory()->create([
            'work_of_extension_id' => $work->id,
        ]);

        // Simular archivo de certificado
        $file = UploadedFile::fake()->create('certificado.pdf', 1000);
        $certification->addMedia($file)->toMediaCollection('certificates');

        // Actuar como profesor
        $this->actingAs($professor);

        // Hacer petición de descarga
        $response = $this->get(route('certificates.download', $certification));

        // Verificar respuesta
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename="certificacion-' . $certification->certification_number . '.pdf"');
    }

    /** @test */
    public function profesor_cannot_download_other_professor_certificate()
    {
        // Crear dos profesores
        $professor1 = User::factory()->create();
        $professor1->assignRole('profesor');

        $professor2 = User::factory()->create();
        $professor2->assignRole('profesor');

        // Crear trabajo certificado para profesor1
        $certifiedStatus = WorkStatus::where('name', 'Certificado')->first();
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $professor1->id,
            'current_status_id' => $certifiedStatus->id,
        ]);

        // Crear certificación
        $certification = Certification::factory()->create([
            'work_of_extension_id' => $work->id,
        ]);

        // Actuar como profesor2
        $this->actingAs($professor2);

        // Intentar descargar certificado de profesor1
        $response = $this->get(route('certificates.download', $certification));

        // Debería ser denegado
        $response->assertStatus(403);
    }

    /** @test */
    public function returns_error_when_certificate_file_missing()
    {
        // Crear usuario profesor
        $professor = User::factory()->create();
        $professor->assignRole('profesor');

        // Crear trabajo certificado
        $certifiedStatus = WorkStatus::where('name', 'Certificado')->first();
        $work = WorkOfExtension::factory()->create([
            'primary_responsible_user_id' => $professor->id,
            'current_status_id' => $certifiedStatus->id,
        ]);

        // Crear certificación sin archivo
        $certification = Certification::factory()->create([
            'work_of_extension_id' => $work->id,
        ]);

        // Actuar como profesor
        $this->actingAs($professor);

        // Hacer petición de descarga
        $response = $this->get(route('certificates.download', $certification));

        // Debería redirigir con error
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function certification_has_unique_number()
    {
        $certification1 = Certification::factory()->create();
        $certification2 = Certification::factory()->create();

        $this->assertNotEquals($certification1->certification_number, $certification2->certification_number);
        $this->assertStringStartsWith('VIEX-', $certification1->certification_number);
        $this->assertStringStartsWith('VIEX-', $certification2->certification_number);
    }

    /** @test */
    public function certification_verification_url_is_generated()
    {
        $certification = Certification::factory()->create();

        $this->assertStringContains('certifications/verify/', $certification->verification_url);
    }
}