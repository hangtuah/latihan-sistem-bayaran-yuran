<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// Data besar untuk Modul 2 (N+1, index, cache) dan Modul 4 (eksport, queue).
// 20,000 pelajar x 10 invois = 200,000 invois. Tarikh akhir dari 900 hari lalu
// hingga 100 hari akan datang, dikira dari hari seeder dijalankan.
//
//   php artisan migrate:fresh --seed
//   php artisan db:seed --class=LargeFeeSeeder      (kira-kira 10 - 30 saat)
//
// Guna INSERT ... SELECT terus dalam MySQL / MariaDB supaya pantas;
// 200,000 panggilan Invoice::create() akan mengambil masa beberapa minit.
class LargeFeeSeeder extends Seeder
{
    private const STUDENTS = 20000;

    public function run(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            throw new RuntimeException('LargeFeeSeeder perlukan MySQL atau MariaDB.');
        }

        // 0 - 99,999 tanpa rekursi: lima digit dicantum
        $numbers = "(SELECT a.d + b.d * 10 + c.d * 100 + e.d * 1000 + f.d * 10000 AS n
            FROM {$this->digits()} a, {$this->digits()} b, {$this->digits()} c,
                 {$this->digits()} e, {$this->digits()} f)";

        $this->command?->info('Pelajar...');
        DB::statement("
            INSERT INTO students (programme_id, matric, name, email, created_at, updated_at)
            SELECT 1 + n % 3,
                   CONCAT('A', 20 + n % 5, 'CS', LPAD(n, 5, '0')),
                   CONCAT('Pelajar ', n),
                   CONCAT('pelajar', n, '@graduate.utm.my'),
                   NOW(), NOW()
            FROM {$numbers} nums
            WHERE n BETWEEN 1 AND ".self::STUDENTS);

        $this->command?->info('Invois...');
        DB::statement("
            INSERT INTO invoices (student_id, number, type, amount, due_date, paid_at, created_at, updated_at)
            SELECT s.id,
                   CONCAT('TMP', s.id, '-', k.d),
                   ELT(1 + k.d % 3, 'semester', 'hostel', 'library_fine'),
                   ELT(1 + k.d % 3, 1100 + (s.id % 4) * 50, 480, 15),
                   DATE_ADD(CURDATE() - INTERVAL 900 DAY, INTERVAL k.d * 100 + s.id % 100 DAY),
                   NULL,
                   NOW(), NOW()
            FROM students s, {$this->digits()} k
            WHERE s.matric LIKE 'A2_CS_____'");

        // kira-kira 60% invois yang sudah lepas tarikh akhir telah dibayar, 3 hari lebih awal
        DB::statement("
            UPDATE invoices
            SET paid_at = DATE_SUB(due_date, INTERVAL 3 DAY)
            WHERE number LIKE 'TMP%' AND due_date < CURDATE() AND ((student_id DIV 100) * 7 + id * 3) % 10 < 6");

        DB::statement("UPDATE invoices SET number = CONCAT('INV', LPAD(id, 7, '0')) WHERE number LIKE 'TMP%'");

        $this->command?->info('Bayaran...');
        DB::statement("
            INSERT INTO payments (invoice_id, student_id, method, amount, reference, paid_at, created_at, updated_at)
            SELECT i.id, i.student_id,
                   ELT(1 + i.id % 4, 'fpx', 'card', 'ewallet', 'counter'),
                   i.amount,
                   CONCAT('REF', LPAD(i.id, 7, '0')),
                   DATE_ADD(i.paid_at, INTERVAL (i.id % 600) MINUTE),
                   NOW(), NOW()
            FROM invoices i
            LEFT JOIN payments p ON p.invoice_id = i.id
            WHERE i.paid_at IS NOT NULL AND p.id IS NULL");

        $this->command?->info(sprintf(
            'Siap: %s pelajar, %s invois, %s bayaran.',
            number_format(DB::table('students')->count()),
            number_format(DB::table('invoices')->count()),
            number_format(DB::table('payments')->count()),
        ));
    }

    private function digits(): string
    {
        return '(SELECT 0 d UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9)';
    }
}
