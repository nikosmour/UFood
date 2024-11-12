<?php

use App\Enum\MealPlanPeriodEnum;
use App\Models\PurchaseCoupon;
use App\Models\TransferCoupon;
use App\Models\UsageCoupon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createCouponTransactionsView();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS coupon_transactions');
    }


    /**
     * Get the meal columns for "using" transactions.
     */
    private function getMealColumnsUsing(Illuminate\Support\Collection $meals): string
    {
        return collect($meals)->map(function ($meal) {
            return "CASE WHEN period = '$meal' THEN -1 ELSE 0 END as $meal";
        })->join(', ');
    }

    /**
     * Get the meal columns for "sending" transactions.
     */
    private function getMealColumnsSending(Illuminate\Support\Collection $meals): string
    {
        return collect($meals)->map(function ($meal) {
            return "CAST($meal AS SIGNED) * -1 as $meal";
        })->join(', ');
    }

    /**
     * Get the sending transactions query.
     */
    private function getSendingTransactions(string $mealColumnsSending): Builder
    {
        return TransferCoupon::select([
            'id',
            'created_at',
            DB::raw('sender_id as academic_id'),
            DB::raw('"sending" as transaction'),
            DB::raw('receiver_id as other_person_id'),
            DB::raw('0 as money'),
            DB::raw($mealColumnsSending)
        ]);
    }

    /**
     * Get the receiving transactions query.
     */
    private function getReceivingTransactions(Illuminate\Support\Collection $meals): Builder
    {
        return TransferCoupon::select([
            'id',
            'created_at',
            DB::raw('receiver_id as academic_id'),
            DB::raw('"receiving" as transaction'),
            DB::raw('sender_id as other_person_id'),
            DB::raw('0 as money'),
            ...$meals
        ]);
    }

    /**
     * Get the buying transactions query.
     */
    private function getBuyingTransactions(Illuminate\Support\Collection $meals): Builder
    {
        return PurchaseCoupon::select([
            'id',
            'created_at',
            'academic_id',
            DB::raw('"buying" as transaction'),
            DB::raw('0 as other_person_id'),
            DB::raw('money/100 as money'),
            ...$meals
        ]);
    }

    /**
     * Get the using transactions query.
     */
    private function getUsingTransactions(string $mealColumnsUsing): Builder
    {
        return UsageCoupon::select([
            'id',
            'created_at',
            'academic_id',
            DB::raw('"using" as transaction'),
            DB::raw('0 as other_person_id'),
            DB::raw('0 as money'),
            DB::raw($mealColumnsUsing)
        ]);
    }

    /**
     * Combine all the transactions.
     */
    private function combineTransactions($sending, $receiving, $buying, $using)
    {
        return $sending
            ->union($receiving)
            ->union($buying)
            ->union($using)
            ->orderByDesc('created_at');
    }

    /**
     * Create the coupon_transactions view.
     */
    private function createCouponTransactionsView(): void
    {
        $meals = MealPlanPeriodEnum::names();

        $mealColumnsUsing = $this->getMealColumnsUsing($meals);
        $mealColumnsSending = $this->getMealColumnsSending($meals);

        $sending = $this->getSendingTransactions($mealColumnsSending);
        $receiving = $this->getReceivingTransactions($meals);
        $buying = $this->getBuyingTransactions($meals);
        $using = $this->getUsingTransactions($mealColumnsUsing);

        $transactions = $this->combineTransactions($sending, $receiving, $buying, $using);

        DB::statement("
            CREATE VIEW coupon_transactions AS " . $transactions->toSql());
    }
};
