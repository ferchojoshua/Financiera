<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SimulatorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('simulator.index');
    }

    /**
     * Realizar simulación de préstamo
     */
    public function simulate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'term' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:daily,weekly,biweekly,monthly',
            'loan_type' => 'required|in:personal,pyme',
        ]);

        $amount = $request->amount;
        $term = $request->term;
        $interestRate = $request->interest_rate / 100; // Convertir a decimal
        $paymentFrequency = $request->payment_frequency;
        $loanType = $request->loan_type;
        
        // Ajustar la tasa de interés según el tipo de préstamo
        if ($loanType == 'pyme') {
            // Las PYMEs pueden tener una tasa preferencial más baja
            $interestRate = $interestRate * 0.9; // 10% de descuento en la tasa
        }
        
        // Calcular número de pagos según frecuencia
        $paymentsPerYear = $this->getPaymentsPerYear($paymentFrequency);
        $totalPayments = ceil($term * $paymentsPerYear / 12);
        
        // Calcular tasa por período
        $ratePerPeriod = $interestRate / $paymentsPerYear;
        
        // Calcular cuota usando fórmula de amortización
        $payment = $amount * $ratePerPeriod * pow(1 + $ratePerPeriod, $totalPayments) / (pow(1 + $ratePerPeriod, $totalPayments) - 1);
        
        // Generar tabla de amortización
        $schedule = $this->generateAmortizationSchedule($amount, $ratePerPeriod, $totalPayments, $payment);
        
        $summary = [
            'amount' => $amount,
            'term' => $term,
            'interest_rate' => $interestRate * 100,
            'payment_frequency' => $paymentFrequency,
            'loan_type' => $loanType,
            'payment' => $payment,
            'total_payments' => $totalPayments,
            'total_interest' => $schedule[$totalPayments - 1]['accumulated_interest'],
            'total_amount' => $amount + $schedule[$totalPayments - 1]['accumulated_interest'],
        ];
        
        return view('simulator.result', compact('summary', 'schedule'));
    }
    
    /**
     * Obtener número de pagos por año según frecuencia
     */
    private function getPaymentsPerYear($frequency)
    {
        switch ($frequency) {
            case 'daily':
                return 365;
            case 'weekly':
                return 52;
            case 'biweekly':
                return 26;
            case 'monthly':
            default:
                return 12;
        }
    }
    
    /**
     * Generar tabla de amortización
     */
    private function generateAmortizationSchedule($principal, $rate, $periods, $payment)
    {
        $schedule = [];
        $balance = $principal;
        $accumulatedInterest = 0;
        
        for ($i = 0; $i < $periods; $i++) {
            $interest = $balance * $rate;
            $accumulatedInterest += $interest;
            $principal_payment = $payment - $interest;
            $balance -= $principal_payment;
            
            if ($balance < 0) {
                $balance = 0;
            }
            
            $schedule[] = [
                'period' => $i + 1,
                'payment' => $payment,
                'principal' => $principal_payment,
                'interest' => $interest,
                'balance' => $balance,
                'accumulated_interest' => $accumulatedInterest
            ];
        }
        
        return $schedule;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
