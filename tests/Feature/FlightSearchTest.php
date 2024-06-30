<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FlightSearchTest extends TestCase
{

    protected function prepareData(array $data)
    {
        return array_merge($data, [
            'origin' => 'airport:MHD',
            'destination' => 'airport:THR',
            'date' => now()->format('Y-m-d')
        ]);
    }

    public function test_zero_adult_one_infant_count(): void
    {
        $response = $this->postJson('/api/flights/search/one-way', $this->prepareData([
            'passengers' => [
                'adults' => 0,
                'infants' => 1
            ]
        ]));
        $response->assertJsonValidationErrorFor('passengers.adults');
        $response->assertStatus(422);
    }
    public function test_zero_adult_one_child_count(): void
    {
        $response = $this->postJson('/api/flights/search/one-way', $this->prepareData([
            'passengers' => [
                'adults' => 0,
                'children' => 1
            ]
        ]));
        $response->assertJsonValidationErrorFor('passengers.adults');
        $response->assertStatus(422);
    }
    // /**
    //  * A basic feature test example.
    //  */
    // public function test_adult_infant_count_1on1_ratio(): void
    // {
    //     $random_adult_count = fake()->numberBetween(1, 6);
    //     $response = $this->postJson('/api/flights/search/one-way', [
    //         'passengers' => [
    //             'adults' => $random_adult_count,
    //             'infants' => $random_adult_count
    //         ]
    //     ]);
    //     $response->assertStatus(200);
    // }
    /**
     * A basic feature test example.
     */
    public function test_adult_infant_count_1on2_ratio(): void
    {
        $random_adult_count = fake()->numberBetween(1, 6);
        $response = $this->postJson('/api/flights/search/one-way', $this->prepareData([
            'passengers' => [
                'adults' => $random_adult_count,
                'infants' => $random_adult_count * 2
            ]
        ]));
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('passengers.infants');
    }
    // public function test_1_adult_1_infant_2_children_should_be_allowed(): void
    // {
    //     $response = $this->postJson('/api/flights/search/one-way', $this->prepareData([
    //         'passengers' => [
    //             'adults' => 1,
    //             'infants' => 1,
    //             'children' => 2
    //         ]
    //     ]));
    //     $response->assertStatus(200);
    // }
    public function test_1_adult_2_infants_2_children_should_not_be_allowed(): void
    {
        $response = $this->postJson('/api/flights/search/one-way', $this->prepareData([
            'passengers' => [
                'adults' => 1,
                'infants' => 2,
                'children' => 2
            ]
        ]));
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('passengers.infants');
    }
    public function test_1_adult_1_infant_3_children_should_not_be_allowed(): void
    {
        $response = $this->postJson('/api/flights/search/one-way', $this->prepareData([
            'passengers' => [
                'adults' => 1,
                'infants' => 1,
                'children' => 3
            ]
        ]));
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('passengers.children');
    }
    public function test_1_adult_2_infants_3_children_should_not_be_allowed(): void
    {
        $response = $this->postJson('/api/flights/search/one-way', $this->prepareData([
            'passengers' => [
                'adults' => 1,
                'infants' => 2,
                'children' => 3
            ]
        ]));
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('passengers.infants');
        $response->assertJsonValidationErrorFor('passengers.children');
    }
}
