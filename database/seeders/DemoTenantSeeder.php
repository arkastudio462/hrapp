<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\PayrollComponent;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Database\Models\Domain as DomainModel;
use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\Facades\Tenancy;

class DemoTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::create([
            'id' => 'demo',
            'data' => json_encode([
                'company_name' => 'PT Teknologi Nusantara',
                'company_email' => 'info@teknologinusantara.co.id',
                'company_phone' => '021-5551234',
                'company_address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'company_website' => 'https://teknologinusantara.co.id',
                'industry' => 'Technology',
                'subscription_started_at' => now()->subMonths(6),
                'subscription_expires_at' => now()->addMonths(6),
                'package' => 'professional',
                'payment_method' => 'manual',
                'logo' => null,
                'leave_config' => [
                    'annual' => 12,
                    'sick' => 9,
                    'maternity' => 90,
                ],
                'work_hours' => [
                    'start' => '09:00',
                    'end' => '18:00',
                    'late_threshold' => 15,
                ],
            ]),
        ]);

        DomainModel::create([
            'domain' => 'demo.hrapp.test',
            'tenant_id' => $tenant->id,
        ]);

        // Create and migrate tenant database
        Artisan::call('tenants:migrate', [
            '--tenants' => ['demo'],
            '--no-interaction' => true,
        ]);

        // Initialize tenancy for the demo tenant
        Tenancy::initialize($tenant);

        $this->seedTenantData($tenant);
    }

    private function seedTenantData(Tenant $tenant): void
    {
        $departments = $this->seedDepartments();
        $positions = $this->seedPositions();
        $employees = $this->seedEmployees($departments, $positions);
        $users = $this->seedUsers($tenant, $employees);
        $this->seedAttendance($employees);
        $this->seedLeaves($employees, $users);
        $this->seedLeaveBalances($employees);
        $this->seedPayroll($employees, $users);
        $this->seedPayrollComponents();
    }

    private function seedDepartments(): array
    {
        $departments = [
            ['name' => 'Information Technology', 'budget' => 500000000],
            ['name' => 'Human Resources', 'budget' => 200000000],
            ['name' => 'Finance', 'budget' => 300000000],
            ['name' => 'Marketing', 'budget' => 350000000],
            ['name' => 'Operations', 'budget' => 400000000],
            ['name' => 'Sales', 'budget' => 250000000],
            ['name' => 'Customer Service', 'budget' => 150000000],
            ['name' => 'Legal', 'budget' => 180000000],
        ];

        $created = [];
        foreach ($departments as $dept) {
            $created[] = Department::create($dept);
        }

        return $created;
    }

    private function seedPositions(): array
    {
        $positions = [
            ['name' => 'Director', 'level' => 10, 'salary_grade' => 'D1'],
            ['name' => 'Senior Manager', 'level' => 9, 'salary_grade' => 'M3'],
            ['name' => 'Manager', 'level' => 8, 'salary_grade' => 'M2'],
            ['name' => 'Assistant Manager', 'level' => 7, 'salary_grade' => 'M1'],
            ['name' => 'Senior Supervisor', 'level' => 6, 'salary_grade' => 'S2'],
            ['name' => 'Supervisor', 'level' => 5, 'salary_grade' => 'S1'],
            ['name' => 'Senior Staff', 'level' => 4, 'salary_grade' => 'SS'],
            ['name' => 'Staff', 'level' => 3, 'salary_grade' => 'ST'],
            ['name' => 'Junior Staff', 'level' => 2, 'salary_grade' => 'JS'],
            ['name' => 'Intern', 'level' => 1, 'salary_grade' => 'IN'],
        ];

        $created = [];
        foreach ($positions as $pos) {
            $created[] = Position::create($pos);
        }

        return $created;
    }

    private function seedEmployees(array $departments, array $positions): array
    {
        $employeesData = [
            // Directors
            ['nik' => 'EMP001', 'name' => 'Budi Santoso', 'gender' => 'male', 'email_personal' => 'budi.santoso@gmail.com', 'phone' => '081234567890', 'birth_date' => '1975-03-15', 'status' => 'permanent', 'join_date' => '2018-01-01'],
            ['nik' => 'EMP002', 'name' => 'Siti Rahayu', 'gender' => 'female', 'email_personal' => 'siti.rahayu@gmail.com', 'phone' => '081234567891', 'birth_date' => '1978-07-22', 'status' => 'permanent', 'join_date' => '2018-06-01'],

            // Managers
            ['nik' => 'EMP003', 'name' => 'Andi Pratama', 'gender' => 'male', 'email_personal' => 'andi.pratama@gmail.com', 'phone' => '081234567892', 'birth_date' => '1985-11-10', 'status' => 'permanent', 'join_date' => '2019-03-15'],
            ['nik' => 'EMP004', 'name' => 'Dewi Lestari', 'gender' => 'female', 'email_personal' => 'dewi.lestari@gmail.com', 'phone' => '081234567893', 'birth_date' => '1987-04-05', 'status' => 'permanent', 'join_date' => '2019-07-01'],
            ['nik' => 'EMP005', 'name' => 'Rizky Ramadan', 'gender' => 'male', 'email_personal' => 'rizky.ramadan@gmail.com', 'phone' => '081234567894', 'birth_date' => '1986-09-18', 'status' => 'permanent', 'join_date' => '2019-09-01'],
            ['nik' => 'EMP006', 'name' => 'Maya Anggraeni', 'gender' => 'female', 'email_personal' => 'maya.anggraeni@gmail.com', 'phone' => '081234567895', 'birth_date' => '1988-02-28', 'status' => 'permanent', 'join_date' => '2020-01-15'],

            // Supervisors
            ['nik' => 'EMP007', 'name' => 'Fajar Nugroho', 'gender' => 'male', 'email_personal' => 'fajar.nugroho@gmail.com', 'phone' => '081234567896', 'birth_date' => '1990-06-12', 'status' => 'permanent', 'join_date' => '2020-06-01'],
            ['nik' => 'EMP008', 'name' => 'Rina Susanti', 'gender' => 'female', 'email_personal' => 'rina.susanti@gmail.com', 'phone' => '081234567897', 'birth_date' => '1991-12-03', 'status' => 'permanent', 'join_date' => '2020-09-01'],
            ['nik' => 'EMP009', 'name' => 'Hendra Kurniawan', 'gender' => 'male', 'email_personal' => 'hendra.kurniawan@gmail.com', 'phone' => '081234567898', 'birth_date' => '1989-08-25', 'status' => 'permanent', 'join_date' => '2021-01-15'],
            ['nik' => 'EMP010', 'name' => 'Putri Wulandari', 'gender' => 'female', 'email_personal' => 'putri.wulandari@gmail.com', 'phone' => '081234567899', 'birth_date' => '1992-03-08', 'status' => 'permanent', 'join_date' => '2021-04-01'],

            // Senior Staff
            ['nik' => 'EMP011', 'name' => 'Dimas Aditya', 'gender' => 'male', 'email_personal' => 'dimas.aditya@gmail.com', 'phone' => '081234567900', 'birth_date' => '1993-07-14', 'status' => 'permanent', 'join_date' => '2021-07-01'],
            ['nik' => 'EMP012', 'name' => 'Anisa Fitri', 'gender' => 'female', 'email_personal' => 'anisa.fitri@gmail.com', 'phone' => '081234567901', 'birth_date' => '1994-01-20', 'status' => 'permanent', 'join_date' => '2021-10-01'],
            ['nik' => 'EMP013', 'name' => 'Reza Pratama', 'gender' => 'male', 'email_personal' => 'reza.pratama@gmail.com', 'phone' => '081234567902', 'birth_date' => '1993-05-30', 'status' => 'permanent', 'join_date' => '2022-01-15'],
            ['nik' => 'EMP014', 'name' => 'Linda Sari', 'gender' => 'female', 'email_personal' => 'linda.sari@gmail.com', 'phone' => '081234567903', 'birth_date' => '1995-11-08', 'status' => 'permanent', 'join_date' => '2022-04-01'],

            // Staff
            ['nik' => 'EMP015', 'name' => 'Yoga Ardianto', 'gender' => 'male', 'email_personal' => 'yoga.ardianto@gmail.com', 'phone' => '081234567904', 'birth_date' => '1996-09-22', 'status' => 'permanent', 'join_date' => '2022-07-01'],
            ['nik' => 'EMP016', 'name' => 'Nina Marlina', 'gender' => 'female', 'email_personal' => 'nina.marlina@gmail.com', 'phone' => '081234567905', 'birth_date' => '1997-02-14', 'status' => 'permanent', 'join_date' => '2022-10-01'],
            ['nik' => 'EMP017', 'name' => 'Bayu Setiawan', 'gender' => 'male', 'email_personal' => 'bayu.setiawan@gmail.com', 'phone' => '081234567906', 'birth_date' => '1996-06-18', 'status' => 'permanent', 'join_date' => '2023-01-15'],
            ['nik' => 'EMP018', 'name' => 'Sari Dewi', 'gender' => 'female', 'email_personal' => 'sari.dewi@gmail.com', 'phone' => '081234567907', 'birth_date' => '1998-08-05', 'status' => 'permanent', 'join_date' => '2023-04-01'],

            // Junior Staff
            ['nik' => 'EMP019', 'name' => 'Ahmad Fauzi', 'gender' => 'male', 'email_personal' => 'ahmad.fauzi@gmail.com', 'phone' => '081234567908', 'birth_date' => '1999-12-10', 'status' => 'probation', 'join_date' => '2024-01-15'],
            ['nik' => 'EMP020', 'name' => 'Ratna Sari', 'gender' => 'female', 'email_personal' => 'ratna.sari@gmail.com', 'phone' => '081234567909', 'birth_date' => '2000-04-25', 'status' => 'probation', 'join_date' => '2024-02-01'],

            // Contract
            ['nik' => 'EMP021', 'name' => 'Tono Sugiarto', 'gender' => 'male', 'email_personal' => 'tono.sugiarto@gmail.com', 'phone' => '081234567910', 'birth_date' => '1995-07-15', 'status' => 'contract', 'join_date' => '2024-03-01'],
            ['nik' => 'EMP022', 'name' => 'Wati Susilawati', 'gender' => 'female', 'email_personal' => 'wati.susilawati@gmail.com', 'phone' => '081234567911', 'birth_date' => '1997-10-20', 'status' => 'contract', 'join_date' => '2024-04-01'],
        ];

        $created = [];
        foreach ($employeesData as $i => $emp) {
            $deptIndex = $i % count($departments);
            $posIndex = min(floor($i / 2), count($positions) - 1);

            $created[] = Employee::create([
                ...$emp,
                'department_id' => $departments[$deptIndex]->id,
                'position_id' => $positions[$posIndex]->id,
                'address' => 'Jl. '.fake()->streetName().' No. '.fake()->numberBetween(1, 100).', '.fake()->city(),
                'is_active' => true,
            ]);
        }

        return $created;
    }

    private function seedUsers(Tenant $tenant, array $employees): array
    {
        $users = [];

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@demo.hrapp.test'],
            [
                'name' => 'Admin Demo',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'tenant_id' => $tenant->id,
                'employee_id' => $employees[0]->id,
                'email_verified_at' => now(),
            ]
        );
        $users['admin'] = $admin;

        // HR
        $hr = User::firstOrCreate(
            ['email' => 'hr@demo.hrapp.test'],
            [
                'name' => 'HR Demo',
                'password' => Hash::make('password'),
                'role' => 'hr',
                'tenant_id' => $tenant->id,
                'employee_id' => $employees[1]->id,
                'email_verified_at' => now(),
            ]
        );
        $users['hr'] = $hr;

        // Employee users
        foreach (array_slice($employees, 2, 5) as $emp) {
            $email = strtolower(str_replace(' ', '.', $emp->name)).'@demo.hrapp.test';
            $users[] = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $emp->name,
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'tenant_id' => $tenant->id,
                    'employee_id' => $emp->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        return $users;
    }

    private function seedAttendance(array $employees): void
    {
        $today = now();
        $methods = ['qr', 'face'];

        for ($daysAgo = 0; $daysAgo < 30; $daysAgo++) {
            $date = $today->copy()->subDays($daysAgo)->toDateString();

            $dayOfWeek = date('N', strtotime($date));
            if ($dayOfWeek >= 6) {
                continue;
            }

            $randomEmployees = collect($employees)->random(min(18, count($employees)));

            foreach ($randomEmployees as $employee) {
                if (rand(1, 100) <= 5) {
                    continue;
                }

                $checkInHour = rand(8, 10);
                $checkInMinute = rand(0, 59);
                $isLate = $checkInHour > 9 || ($checkInHour === 9 && $checkInMinute > 15);

                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'check_in_time' => now()->setTime($checkInHour, $checkInMinute),
                    'check_out_time' => now()->setTime($checkInHour + 9, $checkInMinute + rand(0, 30)),
                    'check_in_method' => $methods[array_rand($methods)],
                    'status' => $isLate ? 'late' : 'present',
                    'notes' => $isLate ? 'Terlambat' : null,
                ]);
            }
        }
    }

    private function seedLeaves(array $employees, array $users): void
    {
        $leaveTypes = ['leave', 'sick', 'permission'];
        $statuses = ['pending', 'approved', 'rejected'];
        $reasons = [
            'Keperluan pribadi',
            'Sakit flu',
            'Kunjungan dokter',
            'Acara keluarga',
            'Istirahat medis',
            'Keperluan urusan',
        ];

        foreach (array_slice($employees, 2, 8) as $employee) {
            $numLeaves = rand(1, 3);

            for ($i = 0; $i < $numLeaves; $i++) {
                $startDate = now()->subDays(rand(1, 60));
                $endDate = $startDate->copy()->addDays(rand(0, 3));
                $status = $statuses[array_rand($statuses)];

                LeaveRequest::create([
                    'employee_id' => $employee->id,
                    'type' => $leaveTypes[array_rand($leaveTypes)],
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'reason' => $reasons[array_rand($reasons)],
                    'status' => $status,
                    'approved_by' => $status !== 'pending' ? $users['hr']->id : null,
                    'approved_at' => $status !== 'pending' ? $startDate->copy()->addDay()->toDateTimeString() : null,
                ]);
            }
        }
    }

    private function seedLeaveBalances(array $employees): void
    {
        $currentYear = now()->year;

        foreach ($employees as $employee) {
            LeaveBalance::create([
                'employee_id' => $employee->id,
                'year' => $currentYear,
                'type' => 'annual',
                'total' => 12,
                'used' => rand(0, 6),
                'remaining' => 12 - rand(0, 6),
            ]);

            LeaveBalance::create([
                'employee_id' => $employee->id,
                'year' => $currentYear,
                'type' => 'sick',
                'total' => 9,
                'used' => rand(0, 3),
                'remaining' => 9 - rand(0, 3),
            ]);

            LeaveBalance::create([
                'employee_id' => $employee->id,
                'year' => $currentYear,
                'type' => 'maternity',
                'total' => 90,
                'used' => 0,
                'remaining' => 90,
            ]);
        }
    }

    private function seedPayroll(array $employees, array $users): void
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $period = PayrollPeriod::create([
            'month' => $currentMonth,
            'year' => $currentYear,
            'status' => 'completed',
            'processed_by' => $users['admin']->id,
            'processed_at' => now()->subDays(5),
        ]);

        $basicSalaries = [
            'D1' => 25000000,
            'M3' => 18000000,
            'M2' => 15000000,
            'M1' => 12000000,
            'S2' => 10000000,
            'S1' => 8500000,
            'SS' => 7500000,
            'ST' => 6000000,
            'JS' => 4500000,
            'IN' => 3000000,
        ];

        foreach ($employees as $employee) {
            $position = $employee->position;
            $grade = $position->salary_grade ?? 'ST';
            $basicSalary = $basicSalaries[$grade] ?? 6000000;

            $transportAllowance = 500000;
            $mealAllowance = rand(15, 25) * 10000;
            $performanceBonus = rand(0, 1) ? rand(500000, 2000000) : 0;
            $totalEarnings = $basicSalary + $transportAllowance + $mealAllowance + $performanceBonus;

            $bpjsHealth = $basicSalary * 0.01;
            $bpjsEmployment = $basicSalary * 0.02;
            $pph21 = $this->calculatePph21($totalEarnings);
            $totalDeductions = $bpjsHealth + $bpjsEmployment + $pph21;

            $netSalary = $totalEarnings - $totalDeductions;

            $payroll = Payroll::create([
                'employee_id' => $employee->id,
                'payroll_period_id' => $period->id,
                'basic_salary' => $basicSalary,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'status' => 'paid',
            ]);

            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'component_code' => 'BASIC',
                'amount' => $basicSalary,
                'type' => 'earning',
                'description' => 'Gaji Pokok',
            ]);

            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'component_code' => 'TRANSPORT',
                'amount' => $transportAllowance,
                'type' => 'earning',
                'description' => 'Tunjangan Transport',
            ]);

            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'component_code' => 'MEAL',
                'amount' => $mealAllowance,
                'type' => 'earning',
                'description' => 'Tunjangan Makan',
            ]);

            if ($performanceBonus > 0) {
                PayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'component_code' => 'BONUS',
                    'amount' => $performanceBonus,
                    'type' => 'earning',
                    'description' => 'Bonus Kinerja',
                ]);
            }

            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'component_code' => 'BPJS_KES',
                'amount' => $bpjsHealth,
                'type' => 'deduction',
                'description' => 'BPJS Kesehatan',
            ]);

            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'component_code' => 'BPJS_JHT',
                'amount' => $bpjsEmployment,
                'type' => 'deduction',
                'description' => 'BPJS Ketenagakerjaan',
            ]);

            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'component_code' => 'PPH21',
                'amount' => $pph21,
                'type' => 'deduction',
                'description' => 'PPh 21',
            ]);
        }
    }

    private function calculatePph21(float $annualSalary): float
    {
        $annual = $annualSalary * 12;
        $ptkp = 54000000;

        $taxableIncome = max(0, $annual - $ptkp);

        $rates = [
            ['min' => 0, 'max' => 60000000, 'rate' => 0.05, 'progressive' => 0],
            ['min' => 60000000, 'max' => 250000000, 'rate' => 0.15, 'progressive' => 3000000],
            ['min' => 250000000, 'max' => 500000000, 'rate' => 0.25, 'progressive' => 31500000],
            ['min' => 500000000, 'max' => PHP_INT_MAX, 'rate' => 0.35, 'progressive' => 94000000],
        ];

        foreach ($rates as $rate) {
            if ($taxableIncome > $rate['min'] && $taxableIncome <= $rate['max']) {
                return ($taxableIncome * $rate['rate'] - $rate['progressive']) / 12;
            }
        }

        return 0;
    }

    private function seedPayrollComponents(): void
    {
        $components = [
            ['code' => 'BASIC', 'name' => 'Gaji Pokok', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'OT', 'name' => 'Lembur', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'BONUS', 'name' => 'Bonus', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'TRANSPORT', 'name' => 'Tunjangan Transport', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 500000],
            ['code' => 'MEAL', 'name' => 'Tunjangan Makan', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 200000],
            ['code' => 'BPJS_KES', 'name' => 'BPJS Kesehatan', 'type' => 'deduction', 'calculation_type' => 'percentage', 'default_value' => 1.0],
            ['code' => 'BPJS_JHT', 'name' => 'BPJS Ketenagakerjaan', 'type' => 'deduction', 'calculation_type' => 'percentage', 'default_value' => 2.0],
            ['code' => 'PPH21', 'name' => 'PPh 21', 'type' => 'deduction', 'calculation_type' => 'formula', 'default_value' => 0],
        ];

        foreach ($components as $component) {
            PayrollComponent::updateOrCreate(
                ['code' => $component['code']],
                $component
            );
        }
    }
}
