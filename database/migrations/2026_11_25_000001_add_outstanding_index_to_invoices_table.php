<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // nama: invoices_student_id_paid_at_due_date_index
            $table->index(['student_id', 'paid_at', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('student_id'); // foreign key perlukan index
            $table->dropIndex(['student_id', 'paid_at', 'due_date']);
        });
    }
};
