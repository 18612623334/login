<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'account' => 'admin',
            'username' => '总管理员',
            'password' => bcrypt('123456'),
            'status' => 1,
        ];
        $model = new \App\Models\Admin\Admin();
        \DB::table($model->getTable())->insert($data);
    }
}
