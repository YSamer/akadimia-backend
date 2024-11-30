<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BatchGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create batches
        $batch = Batch::create([
            'name' => 'المرحلة الأولى',
            'submission_date' => '2024-12-05',
            'start_date' => '2024-12-15',
            'max_number' => 100,
            'gender' => 'male',
        ]);

        Group::create([
            'batch_id' => $batch->id,
            'name' => 'المجموعة الأولى',
            'image' => 'groups/group1.jpg',
        ]);

        Group::create([
            'batch_id' => $batch->id,
            'name' => 'المجموعة الثانية',
            'image' => 'groups/group2.jpg',
        ]);



    }
}
