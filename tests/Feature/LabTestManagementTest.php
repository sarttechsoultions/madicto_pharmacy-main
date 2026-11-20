<?php

namespace Tests\Feature;

use App\Models\Lab;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use App\Models\User;
use Tests\TestCase;

class LabTestManagementTest extends TestCase
{
    public function test_api_can_fetch_all_active_labs(): void
    {
        $response = $this->getJson('/api/labs');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'logo',
                        'description',
                        'contact_number',
                        'email',
                        'address',
                        'city',
                        'state',
                        'pincode',
                        'status',
                        'active_tests_count',
                    ]
                ]
            ]);
    }

    public function test_api_can_fetch_all_active_categories(): void
    {
        $response = $this->getJson('/api/lab-test-categories');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'icon',
                        'description',
                        'status',
                        'active_tests_count',
                    ]
                ]
            ]);
    }

    public function test_api_can_fetch_all_active_tests_with_pagination(): void
    {
        $response = $this->getJson('/api/lab-tests');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'test_name',
                        'test_code',
                        'lab_id',
                        'lab_name',
                        'category_id',
                        'category_name',
                        'price',
                        'discount_price',
                        'sample_type',
                        'report_delivery_time',
                        'preparation_instructions',
                        'status',
                    ]
                ],
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'has_more_pages',
                ]
            ]);
    }

    public function test_api_can_search_tests_by_name(): void
    {
        $response = $this->getJson('/api/lab-tests?search=cbc');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertIsArray($data);
        if (count($data) > 0) {
            $this->assertStringContainsStringIgnoringCase('CBC', $data[0]['test_name']);
        }
    }

    public function test_api_can_filter_tests_by_lab(): void
    {
        $lab = Lab::where('status', 'active')->first();
        if ($lab) {
            $response = $this->getJson('/api/lab-tests?lab_id=' . $lab->id);
            $response->assertStatus(200);
            foreach ($response->json('data') as $item) {
                $this->assertEquals($lab->id, $item['lab_id']);
            }
        }
    }

    public function test_api_can_filter_tests_by_category(): void
    {
        $category = LabTestCategory::where('status', 'active')->first();
        if ($category) {
            $response = $this->getJson('/api/lab-tests?category_id=' . $category->id);
            $response->assertStatus(200);
            foreach ($response->json('data') as $item) {
                $this->assertEquals($category->id, $item['category_id']);
            }
        }
    }

    public function test_api_can_fetch_tests_for_specific_lab(): void
    {
        $lab = Lab::where('status', 'active')->first();
        if ($lab) {
            $response = $this->getJson('/api/labs/' . $lab->id . '/tests');
            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'laboratory' => [
                        'id' => $lab->id,
                    ]
                ]);
        }
    }

    public function test_api_can_fetch_single_test_details(): void
    {
        $test = LabTest::where('status', 'active')->first();
        if ($test) {
            $response = $this->getJson('/api/lab-tests/' . $test->id);
            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $test->id,
                        'test_name' => $test->test_name,
                    ]
                ]);
        }
    }

    public function test_api_can_fetch_single_test_pricing(): void
    {
        $test = LabTest::where('status', 'active')->first();
        if ($test) {
            $response = $this->getJson('/api/lab-tests/' . $test->id . '/price');
            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'test_id' => $test->id,
                        'currency' => 'INR',
                    ]
                ]);
        }
    }

    public function test_inactive_tests_are_excluded_from_public_api(): void
    {
        $test = LabTest::create([
            'test_name' => 'Secret Inactive Test ' . uniqid(),
            'lab_id' => Lab::first()->id,
            'category_id' => LabTestCategory::first()->id,
            'price' => 999.00,
            'status' => 'inactive',
        ]);

        $response = $this->getJson('/api/lab-tests?search=' . urlencode($test->test_name));
        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));

        // Direct details fetch should return 404
        $detailsResponse = $this->getJson('/api/lab-tests/' . $test->id);
        $detailsResponse->assertStatus(404);

        $test->delete();
    }

    public function test_admin_lab_dashboard_requires_auth(): void
    {
        $response = $this->get('/lab/dashboard');
        // Unauthenticated sanctum request redirects to login
        $this->assertTrue($response->isRedirection() || $response->status() === 401);
    }
}
