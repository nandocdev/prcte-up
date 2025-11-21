<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sdg_goals', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('work_of_extensions', function (Blueprint $table) {
            $table->string('campus_name', 150)->nullable()->after('organizational_unit_id');
            $table->string('faculty_name', 150)->nullable()->after('campus_name');
            $table->string('department_name', 150)->nullable()->after('faculty_name');
            $table->string('school_name', 150)->nullable()->after('department_name');
            $table->string('responsible_office_phone', 25)->nullable()->after('responsible_phone');
            $table->string('responsible_personal_phone', 25)->nullable()->after('responsible_office_phone');
            $table->string('responsible_email', 150)->nullable()->after('responsible_personal_phone');
            $table->unsignedBigInteger('sdg_goal_id')->nullable()->after('publication_consent');

            $table->foreign('sdg_goal_id', 'woe_sdg_goal_fk')
                ->references('id')
                ->on('sdg_goals');
        });

        Schema::table('publication_details', function (Blueprint $table) {
            $table->text('summary')->nullable()->after('publication_type');
        });

        Schema::table('work_participants', function (Blueprint $table) {
            $table->string('name', 255)->nullable()->after('user_id');
            $table->string('email', 150)->nullable()->after('name');
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('institution', 255)->nullable()->after('phone');
            $table->boolean('is_internal')->default(false)->after('institution');
            $table->boolean('is_primary')->default(false)->after('is_internal');
        });

        DB::table('work_participants')
            ->whereNull('name')
            ->update([
                'name' => DB::raw("COALESCE(external_participant_name, '')"),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_of_extensions', function (Blueprint $table) {
            $table->dropForeign('woe_sdg_goal_fk');
            $table->dropColumn([
                'campus_name',
                'faculty_name',
                'department_name',
                'school_name',
                'responsible_office_phone',
                'responsible_personal_phone',
                'responsible_email',
                'sdg_goal_id',
            ]);
        });

        Schema::table('publication_details', function (Blueprint $table) {
            $table->dropColumn('summary');
        });

        Schema::table('work_participants', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'email',
                'phone',
                'institution',
                'is_internal',
                'is_primary',
            ]);
        });

        Schema::dropIfExists('sdg_goals');
    }
};
