<?php

namespace Tests\Feature\Faculty;

use App\Models\Faculty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FacultyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'manage faculty']);
        
        // Create admin role with permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo('manage faculty');
        
        // Create and authenticate as admin
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** @test */
    public function admin_can_view_faculty_index()
    {
        $this->actingAs($this->admin)
            ->get(route('faculties.index'))
            ->assertStatus(200)
            ->assertViewIs('faculties.index');
    }

    /** @test */
    public function admin_can_create_faculty()
    {
        Storage::fake('public');

        $this->actingAs($this->admin)
            ->get(route('faculties.create'))
            ->assertStatus(200)
            ->assertViewIs('faculties.create');
        
        $data = [
            'name' => $this->faker->company,
            'code' => 'FC' . $this->faker->numberBetween(100, 999),
            'description' => $this->faker->paragraph,
            'contact_email' => $this->faker->email,
            'contact_phone' => $this->faker->phoneNumber,
            'website' => $this->faker->url,
            'address' => $this->faker->address,
            'established_date' => $this->faker->date,
            'status' => 1,
            'logo' => UploadedFile::fake()->image('faculty_logo.jpg'),
        ];
        
        $response = $this->post(route('faculties.store'), $data);
        
        $response->assertRedirect(route('faculties.index'));
        $this->assertDatabaseHas('academic_structures', [
            'name' => $data['name'],
            'code' => $data['code'],
            'type' => 'faculty',
        ]);
        Storage::disk('public')->assertExists('faculty_logos/' . Faculty::latest()->first()->logo);
    }

    /** @test */
    public function admin_can_view_faculty_details()
    {
        $faculty = Faculty::create([
            'name' => $this->faker->company,
            'code' => 'FC' . $this->faker->numberBetween(100, 999),
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'type' => 'faculty',
            'status' => 1,
        ]);
        
        $this->actingAs($this->admin)
            ->get(route('faculties.show', $faculty))
            ->assertStatus(200)
            ->assertViewIs('faculties.show')
            ->assertViewHas('faculty', $faculty);
    }

    /** @test */
    public function admin_can_update_faculty()
    {
        Storage::fake('public');
        
        $faculty = Faculty::create([
            'name' => $this->faker->company,
            'code' => 'FC' . $this->faker->numberBetween(100, 999),
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'type' => 'faculty',
            'status' => 1,
        ]);
        
        $this->actingAs($this->admin)
            ->get(route('faculties.edit', $faculty))
            ->assertStatus(200)
            ->assertViewIs('faculties.edit')
            ->assertViewHas('faculty', $faculty);
        
        $data = [
            'name' => 'Updated Faculty Name',
            'code' => 'UPD123',
            'description' => 'Updated description',
            'status' => 0,
            'logo' => UploadedFile::fake()->image('updated_logo.jpg'),
        ];
        
        $response = $this->put(route('faculties.update', $faculty), $data);
        
        $response->assertRedirect(route('faculties.index'));
        $this->assertDatabaseHas('academic_structures', [
            'id' => $faculty->id,
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);
    }

    /** @test */
    public function admin_can_delete_faculty()
    {
        $faculty = Faculty::create([
            'name' => $this->faker->company,
            'code' => 'FC' . $this->faker->numberBetween(100, 999),
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'type' => 'faculty',
            'status' => 1,
        ]);
        
        $this->actingAs($this->admin)
            ->delete(route('faculties.destroy', $faculty))
            ->assertRedirect(route('faculties.index'));
        
        $this->assertDatabaseMissing('academic_structures', [
            'id' => $faculty->id,
        ]);
    }

    /** @test */
    public function guests_cannot_access_faculty_pages()
    {
        $faculty = Faculty::create([
            'name' => $this->faker->company,
            'code' => 'FC' . $this->faker->numberBetween(100, 999),
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'type' => 'faculty',
            'status' => 1,
        ]);
        
        $this->get(route('faculties.index'))->assertRedirect(route('login'));
        $this->get(route('faculties.create'))->assertRedirect(route('login'));
        $this->get(route('faculties.show', $faculty))->assertRedirect(route('login'));
        $this->get(route('faculties.edit', $faculty))->assertRedirect(route('login'));
        $this->post(route('faculties.store'), [])->assertRedirect(route('login'));
        $this->put(route('faculties.update', $faculty), [])->assertRedirect(route('login'));
        $this->delete(route('faculties.destroy', $faculty))->assertRedirect(route('login'));
    }

    /** @test */
    public function faculty_requires_valid_data()
    {
        $this->actingAs($this->admin);
        
        // Test validation failures
        $response = $this->post(route('faculties.store'), []);
        
        $response->assertSessionHasErrors(['name', 'code']);
        
        // Test duplicate code validation
        $faculty = Faculty::create([
            'name' => 'First Faculty',
            'code' => 'FC123',
            'slug' => 'first-faculty',
            'type' => 'faculty',
        ]);
        
        $response = $this->post(route('faculties.store'), [
            'name' => 'Second Faculty',
            'code' => 'FC123', // Duplicate code
        ]);
        
        $response->assertSessionHasErrors('code');
    }

    /** @test */
    public function admin_can_view_faculty_dashboard()
    {
        $faculty = Faculty::create([
            'name' => $this->faker->company,
            'code' => 'FC' . $this->faker->numberBetween(100, 999),
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'type' => 'faculty',
            'status' => 1,
        ]);
        
        $this->actingAs($this->admin)
            ->get(route('faculties.dashboard', $faculty))
            ->assertStatus(200)
            ->assertViewIs('faculties.dashboard')
            ->assertViewHas('faculty', $faculty);
    }
} 