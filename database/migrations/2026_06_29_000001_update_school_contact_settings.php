<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            'app_name' => 'Harakat Barya School System',
            'address' => 'د سید جمال الدین افغاني عمومي سړک، کابل',
            'school_address' => 'د سید جمال الدین افغاني عمومي سړک، کابل',
            'school_address_en' => 'Sayed Jamaluddin Afghani Main Road, Kabul',
            'support_phone_1' => '0785 600 566',
            'support_phone_2' => '0771 581 521',
            'support_phone_3' => '0770 000 265',
            'support_phone_4' => '0786 133 330',
        ];

        foreach ($settings as $key => $value) {
            $existing = DB::table('app_settings')->where('key', $key)->first();

            if ($existing) {
                DB::table('app_settings')
                    ->where('key', $key)
                    ->update([
                        'tab' => 'app',
                        'value' => $value,
                        'updated_at' => now(),
                    ]);

                continue;
            }

            DB::table('app_settings')->insert([
                'id' => (string) Str::uuid(),
                'tab' => 'app',
                'key' => $key,
                'default' => null,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('app_settings')->whereIn('key', [
            'app_name',
            'address',
            'school_address',
            'school_address_en',
            'support_phone_1',
            'support_phone_2',
            'support_phone_3',
            'support_phone_4',
        ])->delete();
    }
};
