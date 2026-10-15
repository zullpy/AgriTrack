<?php

namespace Tests\Feature;

use App\Models\StepNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StepNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_catatan_index_page(): void
    {
        $response = $this->get('/steps/catatan');

        $response->assertStatus(200);
        $response->assertSee('Catatan Penting Budidaya');
    }

    public function test_can_store_new_catatan(): void
    {
        $response = $this->post('/steps/catatan', [
            'judul' => 'Perhatikan Keasaman Air Tangki',
            'catatan' => 'Pastikan pH air berada di kisaran 6.0 hingga 6.5 sebelum melarutkan fungisida.',
        ]);

        $response->assertRedirect('/steps/catatan');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('step_notes', [
            'judul' => 'Perhatikan Keasaman Air Tangki',
            'catatan' => 'Pastikan pH air berada di kisaran 6.0 hingga 6.5 sebelum melarutkan fungisida.',
        ]);
    }

    public function test_validation_when_storing_catatan(): void
    {
        $response = $this->post('/steps/catatan', [
            'judul' => '',
            'catatan' => '',
        ]);

        $response->assertSessionHasErrors(['judul', 'catatan']);
    }

    public function test_can_update_catatan(): void
    {
        $note = StepNote::create([
            'judul' => 'Judul Awal',
            'catatan' => 'Isi catatan awal',
        ]);

        $response = $this->put("/steps/catatan/{$note->id}", [
            'judul' => 'Judul Diperbarui',
            'catatan' => 'Isi catatan diperbarui dengan instruksi lengkap.',
        ]);

        $response->assertRedirect('/steps/catatan');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('step_notes', [
            'id' => $note->id,
            'judul' => 'Judul Diperbarui',
            'catatan' => 'Isi catatan diperbarui dengan instruksi lengkap.',
        ]);
    }

    public function test_can_destroy_catatan(): void
    {
        $note = StepNote::create([
            'judul' => 'Catatan Untuk Dihapus',
            'catatan' => 'Catatan sementara.',
        ]);

        $response = $this->delete("/steps/catatan/{$note->id}");

        $response->assertRedirect('/steps/catatan');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('step_notes', [
            'id' => $note->id,
        ]);
    }
}
