<?php

use App\Enum\CardStatusEnum;
use App\Models\CardApplication;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //todo it is not fully correct but  i have spent to much time for something needed only  one time
        Schema::table('card_application_checking', function (Blueprint $table) {
            $table->unsignedTinyInteger('card_application_staff_id')->comment('null if it is the student otherwise staff')->nullable()->change();
            $table->enum('status', CardStatusEnum::values()->toArray())->default(CardStatusEnum::TEMPORARY_SAVED);
        });
        DB::table('card_application_checking')/*->whereNotNull('card_application_staff_id')*/ ->
        update(['status' => DB::table('card_applications')->select('status')
                ->whereColumn('card_applications.id', 'card_application_id')]
        );
        // if the update happened from cardStaff it was incomplete not TEMPORARY_SAVED
        DB::table('card_application_checking')/*->whereNotNull('card_application_staff_id')*/ ->
        where('status', CardStatusEnum::TEMPORARY_SAVED->value)->
        update(['status' => CardStatusEnum::INCOMPLETE->value]
        );
        $maxId = (int)DB::table('card_application_checking')->max('id');
        // Define the name of the existing tabl

        DB::table('card_application_checking')->insertUsing(
            ['card_application_staff_id', 'card_application_id', 'comment', 'created_at', 'status'],
            //the creation from student except if never updated.
            DB::table('card_applications')->leftJoin(
                'has_card_applicant_comments',
                'card_applications.id', '=', 'card_application_id'
            )->whereColumn('card_applications.created_at', '<', 'card_applications.updated_at')->
            select(columns: [DB::raw('null as card_application_staff_id'), 'card_applications.id', 'comment', 'card_applications.created_at',
                DB::raw('\'' . CardStatusEnum::SUBMITTED->value . '\' as `status`')])->
            union(
            //the last knowing changing from the student (included if it is the first time)
                DB::table('card_applications')->leftJoin(
                    'has_card_applicant_comments',
                    'card_applications.id', '=', 'card_application_id'
                )->whereIn('status', [CardStatusEnum::TEMPORARY_SAVED, CardStatusEnum::SUBMITTED]
                )->select([DB::raw('null as card_application_staff_id'), 'card_applications.id', 'comment', 'card_applications.updated_at', 'status'])
            )->union(
                DB::table('card_application_checking')->select('card_application_staff_id', 'card_application_id', 'comment', 'created_at', 'status')

            )->orderBy('created_at')

        );


        Schema::table('card_applications', function (Blueprint $table) {
            $table->dropColumn([
                'status'
            ]);
        });
//        DB::table('card_application_checking')->orderBy('created_at')->reorder();
        DB::table('card_application_checking')->where('id', '<=', $maxId)->delete();
        Schema::dropIfExists('has_card_applicant_comments');
        Schema::rename('card_application_checking', 'card_application_update');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

//        DB::statement("
//    INSERT INTO table1 (foreign_key, column1, column2, ...)
//    SELECT id AS foreign_key, value1 AS column1, value2 AS column2, ...
//    FROM table2
//);

        Schema::rename('card_application_update', 'card_application_checking');


        Schema::create('has_card_applicant_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CardApplication::class);
            $table->string('comment');
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('card_application_id')->references('id')->on('card_applications')->cascadeOnDelete()->cascadeOnUpdate();
        });
        Schema::table('card_applications', function (Blueprint $table) {
            $table->enum('status', CardStatusEnum::values()->toArray())->default(CardStatusEnum::SUBMITTED);
        });
        DB::table('has_card_applicant_comments')->insertUsing(
            ['card_application_id', 'comment', 'created_at'],
            DB::table('card_application_checking')->select(['card_application_id', 'comment', 'created_at'])->whereNull('card_application_staff_id')->whereNotNull('comment')
        );
        DB::table('card_applications')
            ->update(['status' => DB::table('card_application_checking')->select('status')
                ->whereColumn('card_application_id', 'card_applications.id')
                ->latest('created_at')->limit(1)
            ]);
        DB::table('card_application_checking')->whereNull('card_application_staff_id')->delete();
        Schema::table('card_application_checking', function (Blueprint $table) {
            $table->dropColumn([
                'status'
            ]);
            $table->unsignedTinyInteger('card_application_staff_id')->change();

        });
    }
};
