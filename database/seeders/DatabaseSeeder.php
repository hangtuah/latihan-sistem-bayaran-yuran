<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Programme;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

// Data kecil untuk demo dan lab. Semua kata laluan: rahsia123
//
//   aisyah@graduate.utm.my   pelajar A23CS0012
//   badrul@graduate.utm.my   pelajar A23CS0057
//   siti@utm.my              Puan Siti, Pejabat Bendahari (bukan pelajar)
//
// Invois 7 milik Badrul (asrama RM480, lewat), invois 6 milik Aisyah.
// Data besar untuk Modul 2: php artisan db:seed --class=LargeFeeSeeder
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $sc = Programme::create(['code' => 'SECJH', 'name' => 'Sarjana Muda Sains Komputer (Kejuruteraan Perisian)']);
        Programme::create(['code' => 'SECRH', 'name' => 'Sarjana Muda Sains Komputer (Rangkaian & Keselamatan)']);
        Programme::create(['code' => 'SECVH', 'name' => 'Sarjana Muda Sains Komputer (Grafik & Multimedia)']);

        $aisyah = $this->student($sc, 'A23CS0012', 'Aisyah Binti Ahmad', 'aisyah@graduate.utm.my');
        $badrul = $this->student($sc, 'A23CS0057', 'Badrul Hisham', 'badrul@graduate.utm.my');

        User::create(['name' => 'Puan Siti', 'email' => 'siti@utm.my', 'password' => 'rahsia123']);

        // id 1 - 8, dalam susunan ini
        $rows = [
            [$aisyah, 'semester',     1100, '2026-09-30', '2026-09-20 02:15:00'],
            [$badrul, 'semester',     1100, '2026-09-30', '2026-09-28 06:40:00'],
            [$aisyah, 'hostel',        480, '2026-10-15', '2026-10-10 01:05:00'],
            [$badrul, 'library_fine',   15, '2026-10-20', null],
            [$aisyah, 'library_fine',   15, '2025-01-07', null],
            [$aisyah, 'semester',     1250, '2026-12-15', null],
            [$badrul, 'hostel',        480, '2026-11-17', null],
            [$aisyah, 'hostel',        480, '2026-11-17', null],
        ];

        foreach ($rows as $i => [$student, $type, $amount, $due, $paidAt]) {
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'number' => sprintf('INV%07d', $i + 1),
                'type' => $type,
                'amount' => $amount,
                'due_date' => $due,
                'paid_at' => $paidAt,
            ]);

            if ($paidAt) {
                $invoice->payments()->create([
                    'student_id' => $student->id,
                    'method' => 'fpx',
                    'amount' => $amount,
                    'reference' => sprintf('FPX%06d', 100000 + $invoice->id),
                    'paid_at' => $paidAt,
                ]);
            }
        }
    }

    private function student(Programme $programme, string $matric, string $name, string $email): Student
    {
        $student = Student::create([
            'programme_id' => $programme->id, 'matric' => $matric, 'name' => $name, 'email' => $email,
        ]);

        $user = User::create(['name' => $name, 'email' => $email, 'password' => 'rahsia123']);
        $user->forceFill(['student_id' => $student->id])->save();

        return $student;
    }
}
