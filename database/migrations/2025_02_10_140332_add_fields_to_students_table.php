<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('name');
            $table->string('nationality')->nullable()->after('gender');
            $table->string('state')->nullable()->after('nationality');
            $table->string('lga')->nullable()->after('state');
            $table->string('blood_group')->nullable()->after('lga');
            $table->string('telephone')->nullable()->after('contact_number');
            $table->string('passport_photo')->nullable()->after('telephone');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'nationality',
                'state',
                'lga',
                'blood_group',
                'telephone',
                'passport_photo'
            ]);
        });
    }
};
