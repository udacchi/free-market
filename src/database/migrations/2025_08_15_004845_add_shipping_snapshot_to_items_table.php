<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'payment_method'))    $table->string('payment_method')->nullable()->after('buyer_id');
            if (!Schema::hasColumn('items', 'shipping_postal'))   $table->string('shipping_postal', 20)->nullable()->after('payment_method');
            if (!Schema::hasColumn('items', 'shipping_address'))  $table->string('shipping_address')->nullable()->after('shipping_postal');
            if (!Schema::hasColumn('items', 'shipping_building')) $table->string('shipping_building')->nullable()->after('shipping_address');
        });
    }
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'shipping_building')) $table->dropColumn('shipping_building');
            if (Schema::hasColumn('items', 'shipping_address'))  $table->dropColumn('shipping_address');
            if (Schema::hasColumn('items', 'shipping_postal'))   $table->dropColumn('shipping_postal');
            if (Schema::hasColumn('items', 'payment_method'))    $table->dropColumn('payment_method');
        });
    }
};
