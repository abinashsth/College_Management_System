<?php

namespace Tests\Feature\UI;

use App\Models\User;
use App\Models\Student;
use App\Models\Faculty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UIRefinementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles and permissions
        $adminRole = Role::create(['name' => 'super-admin']);
        $studentRole = Role::create(['name' => 'student']);
        
        // Create users with roles
        $this->admin = User::factory()->create(['name' => 'Admin', 'email' => 'admin@test.com']);
        $this->admin->assignRole('super-admin');
        
        $this->student = User::factory()->create(['name' => 'Student', 'email' => 'student@test.com']);
        $this->student->assignRole('student');
    }

    /**
     * Test responsive dashboard layout.
     *
     * @return void
     */
    public function test_dashboard_responsive_layout(): void
    {
        // Admin view
        $response = $this->actingAs($this->admin)->get('/dashboard');
        
        $response->assertStatus(200);
        
        // Check for responsive layout elements
        $response->assertSee('flex');  // Tailwind flexbox classes
        $response->assertSee('md:');   // Medium screen breakpoint
        $response->assertSee('lg:');   // Large screen breakpoint
        
        // Check if sidebar is present
        $response->assertSee('sidebar-item');
        
        // Additional responsive checks for other user roles
        $response = $this->actingAs($this->student)->get('/dashboard');
        $response->assertStatus(200);
    }

    /**
     * Test form validation feedback is displayed properly.
     *
     * @return void
     */
    public function test_form_validation_feedback(): void
    {
        // Try to create a faculty with missing required fields
        $response = $this->actingAs($this->admin)
            ->post('/faculties', [
                // Intentionally missing name and code
                'description' => 'Testing validation feedback'
            ]);
        
        $response->assertSessionHasErrors(['name', 'code']);
        
        // Follow the redirect and check if error messages are displayed
        $redirectResponse = $this->actingAs($this->admin)->get($response->headers->get('Location'));
        $redirectResponse->assertSee('The name field is required');
        $redirectResponse->assertSee('The code field is required');
    }

    /**
     * Test data tables and pagination.
     *
     * @return void
     */
    public function test_data_tables_and_pagination(): void
    {
        // Create test data
        for ($i = 0; $i < 30; $i++) {
            Faculty::create([
                'name' => "Test Faculty $i",
                'code' => "TF$i"
            ]);
        }
        
        // Test pagination on faculties page
        $response = $this->actingAs($this->admin)->get('/faculties');
        
        $response->assertStatus(200);
        
        // Check for pagination components
        $response->assertSee('pagination');
        
        // Test response with specific page parameter
        $response = $this->actingAs($this->admin)->get('/faculties?page=2');
        $response->assertStatus(200);
        
        // Confirm page 2 has appropriate content
        $response->assertDontSee('Test Faculty 1'); // Should be on page 1
    }

    /**
     * Test mobile-friendly UI elements.
     *
     * @return void
     */
    public function test_mobile_friendly_ui(): void
    {
        // Admin views student list
        $response = $this->actingAs($this->admin)->get('/students');
        
        $response->assertStatus(200);
        
        // Check for responsive table elements or mobile-friendly alternatives
        $response->assertSee('md:table');  // Tailwind responsive table
        
        // Check for hamburger menu or mobile navigation toggle
        $response->assertSee('hidden md:block'); // Elements hidden on mobile
        $response->assertSee('block md:hidden'); // Elements shown only on mobile
    }

    /**
     * Test accessibility improvements.
     *
     * @return void
     */
    public function test_accessibility_improvements(): void
    {
        // Check login page for accessibility features
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        
        // Check for accessibility attributes
        $response->assertSee('aria-label');
        $response->assertSee('role=');
        
        // Check for proper form labels
        $response->assertSee('<label');
        
        // Check dashboard for accessibility features
        $response = $this->actingAs($this->admin)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('aria-');  // ARIA attributes
    }

    /**
     * Test new UI components added during refinement phase.
     *
     * @return void
     */
    public function test_refined_ui_components(): void
    {
        // Test student detail page UI improvements
        $student = Student::factory()->create();
        
        $response = $this->actingAs($this->admin)->get("/students/{$student->id}");
        
        $response->assertStatus(200);
        
        // Check for UI components like tabs, cards, etc.
        $response->assertSee('card');
        
        // Test error pages for improved UI
        $response = $this->actingAs($this->admin)->get('/non-existent-page');
        $response->assertStatus(404);
        
        // Check for specific refined UI elements for error pages
        // Note: Specific assertion will depend on your error page implementation
    }

    /**
     * Test page load performance.
     *
     * @return void
     */
    public function test_page_load_performance(): void
    {
        // Create a large dataset to test performance
        for ($i = 0; $i < 20; $i++) {
            Student::factory()->create();
        }
        
        // Measure time to load the students page
        $startTime = microtime(true);
        $response = $this->actingAs($this->admin)->get('/students');
        $endTime = microtime(true);
        
        $response->assertStatus(200);
        
        // Basic performance check (rough approximation)
        $loadTime = $endTime - $startTime;
        
        // Check if page loads within reasonable time (adjust threshold as needed)
        $this->assertLessThan(
            2.0, // seconds - this is an arbitrary threshold
            $loadTime,
            "Page should load within reasonable time"
        );
    }
} 