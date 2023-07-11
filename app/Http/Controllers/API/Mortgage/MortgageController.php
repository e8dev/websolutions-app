<?php

namespace App\Http\Controllers\API\Mortgage;

use App\Http\Controllers\API\BaseController;
use Request;
use App\Models\MortgageScheduleExtraPayments;
use App\Models\MortgageSchedule;

class MortgageController extends BaseController
{
    public function mortgageCalculatorRequests()
    {
        $input = Request::only(['annual_interest_rate', 'loan_term', 'loan_amount', 'extra_payment', 'save_to_db']);
        $validationRules = [
            'annual_interest_rate' => 'required|numeric|min:0',
            'loan_term' => 'required|numeric|min:1',
            'loan_amount' => 'required|numeric|min:1',
            'extra_payment' => 'required|numeric|min:0',
            'save_to_db' => 'required'
        ];

        $validator = \Validator::make($input, $validationRules);
        if ($validator->fails()) {
            return $this->sendError('Invalid input', $validator->errors(), 400);
        }

        $use_extra_payment = $input['extra_payment'] > 0;
        $result = $this->mortgageCalculatorLogic($input, $use_extra_payment);

        return $this->sendResponse($result, 'Successfully calculated');
    }

    public function mortgageCalculatorLogic($input, $use_extra_payment)
    {
        $monthly_interest_rate = ($input['annual_interest_rate'] / 12) / 100;
        $loan_term_in_months = $input['loan_term'] * 12;

        $current_loan = $input['loan_amount'];
        $total_interest = 0;
        $total_loan_paid = 0;

        $monthly_payment = $current_loan * $monthly_interest_rate / (1 - (1 + $monthly_interest_rate) ** (-1 * $loan_term_in_months));

        $result_array = [];
        $monthly_data = [];

        for ($i = 1; $current_loan > 0; $i++) {
            $month_interest = $current_loan * $monthly_interest_rate;
            $principal = ($monthly_payment - $month_interest) + ($use_extra_payment ? $input['extra_payment'] : 0);

            if ($current_loan - $principal > 0) {
                $principal = ($monthly_payment - $month_interest) + ($use_extra_payment ? $input['extra_payment'] : 0);
            } else {
                $principal = $current_loan;
                $monthly_payment = $principal + $month_interest;
            }

            $start_balance = $current_loan;
            $current_loan = $start_balance - $principal;

            $total_interest += $month_interest;
            $total_loan_paid += $principal;

            $monthly_data[] = [
                "month_number" => $i,
                "month_interest" => $month_interest,
                "principal" => $principal,
                "monthly_payment" => $monthly_payment,
                "starting_balance" => $start_balance,
                "ending_balance" => $current_loan,
                "paid_loan" => $total_loan_paid,
                "extra_repayment" => $use_extra_payment ? $input['extra_payment'] : null,
            ];
        }

        $result_array["monthly_data"] = $monthly_data;
        $result_array["total_data"] = [
            "total_interest" => $total_interest,
            "total_loan_paid" => $total_loan_paid,
            "total_months" => count($monthly_data),
        ];

        if ($input['save_to_db'] === true) {
            $this->saveToDB($use_extra_payment ? 2 : 1, $result_array);
        }

        return $result_array;
    }

    public function saveToDB($type, $result_array)
    {
        $model = $type == 1 ? MortgageSchedule::class : MortgageScheduleExtraPayments::class;
        $model::truncate();

        foreach ($result_array["monthly_data"] as $monthly_data) {
            $data = [
                "month_number" => $monthly_data["month_number"],
                "starting_balance" => $monthly_data["starting_balance"],
                "ending_balance" => $monthly_data["ending_balance"],
                "monthly_payment" => $monthly_data["monthly_payment"],
                "principal" => $monthly_data["principal"],
                "interest" => $monthly_data["month_interest"],
                
            ];
            if($type==2){
                $data["extra_repayment"] = $monthly_data["extra_repayment"];
                $data["remaining_loan_term"] = $result_array["total_data"]["total_months"] - $monthly_data["month_number"];
            }
            $model::create($data);
        }
    }
}
