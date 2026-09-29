<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('due_at')->nullable()->after('borrowed_at');
        });

        $days = (int) config('loans.days');

        DB::table('loans')->select('id', 'borrowed_at')->orderBy('id')->chunkById(100, function ($loans) use ($days) {
            foreach ($loans as $loan) {
                DB::table('loans')
                    ->where('id', $loan->id)
                    ->update(['due_at' => Carbon::parse($loan->borrowed_at)->addDays($days)]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('due_at');
        });
    }
};
