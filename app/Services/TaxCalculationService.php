<?php

declare(strict_types=1);

namespace App\Services;

class TaxCalculationService
{
    private const PTKP_AMOUNTS = [
        0 => 54_000_000,
        1 => 58_500_000,
        2 => 63_000_000,
        3 => 67_500_000,
    ];

    private const TAX_BRACKETS = [
        ['min' => 0, 'max' => 60_000_000, 'rate' => 0.05],
        ['min' => 60_000_000, 'max' => 250_000_000, 'rate' => 0.15],
        ['min' => 250_000_000, 'max' => 500_000_000, 'rate' => 0.25],
        ['min' => 500_000_000, 'max' => PHP_INT_MAX, 'rate' => 0.30],
    ];

    public function calculateMonthlyTax(float $annualSalary, int $maritalStatus = 0): float
    {
        $annualIncome = $annualSalary;

        $ptkp = self::PTKP_AMOUNTS[$maritalStatus] ?? self::PTKP_AMOUNTS[0];

        $taxableIncome = max(0, $annualIncome - $ptkp);

        $annualTax = $this->calculateProgressiveTax($taxableIncome);

        return $annualTax / 12;
    }

    private function calculateProgressiveTax(float $taxableIncome): float
    {
        $tax = 0;
        $remaining = $taxableIncome;

        foreach (self::TAX_BRACKETS as $bracket) {
            if ($remaining <= 0) {
                break;
            }

            $bracketWidth = $bracket['max'] - $bracket['min'];
            $taxableInBracket = min($remaining, $bracketWidth);
            $tax += $taxableInBracket * $bracket['rate'];
            $remaining -= $taxableInBracket;
        }

        return $tax;
    }

    public function calculatePph21(float $monthlySalary, int $maritalStatus = 0): float
    {
        $annualSalary = $monthlySalary * 12;

        return $this->calculateMonthlyTax($annualSalary, $maritalStatus);
    }
}
