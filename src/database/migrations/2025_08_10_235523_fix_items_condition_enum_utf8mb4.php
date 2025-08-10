<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixItemsConditionEnumUtf8mb4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // このマイグレーションの接続をUTF8MB4に固定
        DB::statement("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

        // 念のためテーブル全体もUTF8MB4へ
        DB::statement("
            ALTER TABLE `items`
              CONVERT TO CHARACTER SET utf8mb4
              COLLATE utf8mb4_unicode_ci
        ");

        // ENUMをUTF8MB4で“再定義”
        DB::statement("
            ALTER TABLE `items`
              MODIFY `condition`
              ENUM('良好','目立った傷や汚れなし','やや傷や汚れあり','状態が悪い')
              CHARACTER SET utf8mb4
              COLLATE utf8mb4_unicode_ci
              NOT NULL
              DEFAULT '良好'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
}
