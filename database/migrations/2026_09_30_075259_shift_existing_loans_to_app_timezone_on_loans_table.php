<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->shiftExistingLoans(1);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->shiftExistingLoans(-1);
    }

    private function shiftExistingLoans(int $direction): void
    {
        $maxId = DB::table('loans')->max('id');

        if ($maxId === null) {
            return;
        }

        $hours = (int) round(Carbon::now()->utcOffset() / 60) * $direction;

        if ($hours === 0) {
            return;
        }

        DB::table('loans')->where('id', '<=', $maxId)->update([
            'borrowed_at' => DB::raw('DATE_ADD(borrowed_at, INTERVAL '.$hours.' HOUR)'),
            'due_at' => DB::raw('DATE_ADD(due_at, INTERVAL '.$hours.' HOUR)'),
            'returned_at' => DB::raw('DATE_ADD(returned_at, INTERVAL '.$hours.' HOUR)'),
        ]);
    }
};
