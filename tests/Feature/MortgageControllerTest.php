<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Controllers\API\Mortgage\MortgageController;

class MortgageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testMortgageCalculatorRequests()
    {

        $response = $this->post('/api/calculate', [
            'annual_interest_rate' => 10,
            'loan_term' => 10,
            'loan_amount' => 100000,
            'extra_payment' => 0,
            'save_to_db' => true
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => true,
                'message' => true,
            ]);

    }


    public function test_calculator_response()
    {
        $response = $this->post('/api/calculate', [
            'annual_interest_rate' => 10,
            'loan_term' => 10,
            'loan_amount' => 100000,
            'extra_payment' => 0,
            'save_to_db' => true
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => true,
                'message' => true,
            ]);

    }

    public function test_calculator_invalid_data_interest_rate()
    {
        $response = $this->post('/api/calculate', [
            'annual_interest_rate' => -10,
            'loan_term' => 10,
            'loan_amount' => 100000,
            'extra_payment' => 0,
        ]);

        $response->assertStatus(400);

        $content = json_decode($response->getContent());

        $this->assertEquals("Invalid input", $content->message);

    }

    public function test_calculator_invalid_data_loan_term()
    {
        $response = $this->post('/api/calculate', [
            'annual_interest_rate' => 10,
            'loan_term' => -10,
            'loan_amount' => 100000,
            'extra_payment' => 0,
        ]);

        $response->assertStatus(400);

        $content = json_decode($response->getContent());

        $this->assertEquals("Invalid input", $content->message);

    }

    public function test_calculator_invalid_data_loan_amount()
    {
        $response = $this->post('/api/calculate', [
            'annual_interest_rate' => 10,
            'loan_term' => 10,
            'loan_amount' => -100000,
            'extra_payment' => 0,
        ]);

        $response->assertStatus(400);

        $content = json_decode($response->getContent());

        $this->assertEquals("Invalid input", $content->message);

    }

    public function test_calculator_invalid_data_extra_payment()
    {
        $response = $this->post('/api/calculate', [
            'annual_interest_rate' => 10,
            'loan_term' => 10,
            'loan_amount' => 100000,
            'extra_payment' => -200,
        ]);

        $response->assertStatus(400);

        $content = json_decode($response->getContent());

        $this->assertEquals("Invalid input", $content->message);

    }

    public function testSaveToDB()
    {
        $type = 1;
        $resultArray = [
            'monthly_data' => [
                [
                    'month_number' => 1,
                    'starting_balance' => 100000,
                    'ending_balance' => 99352.54,
                    'monthly_payment' => 1480.79,
                    'principal' => 647.46,
                    "month_interest" => 833.33,
                    "paid_loan" => 0,
                    "extra_repayment" => null
                ],
            ],
            'total_data' => [
                'total_interest' => 17575.86,
                'total_loan_paid' => 64746,
                'total_months' => 120,
            ],
        ];

        $controller = new MortgageController();
        $controller->saveToDB($type, $resultArray);

        $this->assertDatabaseHas('mortgage_schedule', [
            'month_number' => 1,
            'starting_balance' => 100000,
            'ending_balance' => 99352.54,
            'monthly_payment' => 1480.79,
            'principal' => 647.46,
            "interest" => 833.33,
        ]);
        
    }
}
