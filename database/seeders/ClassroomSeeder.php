<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use Illuminate\Database\Seeder;

/**
 * Seeds a realistic set of classrooms for the active academic year.
 * Idempotent via firstOrCreate keyed on (academic_year_id, room_name).
 *
 * Run: php artisan db:seed --class=ClassroomSeeder
 */
class ClassroomSeeder extends Seeder
{
    private const ROOMS = [
        // Building A — Regular classrooms
        ['room_name' => 'Room 101', 'building' => 'Building A', 'capacity' => 45],
        ['room_name' => 'Room 102', 'building' => 'Building A', 'capacity' => 45],
        ['room_name' => 'Room 103', 'building' => 'Building A', 'capacity' => 45],
        ['room_name' => 'Room 104', 'building' => 'Building A', 'capacity' => 40],
        ['room_name' => 'Room 105', 'building' => 'Building A', 'capacity' => 40],

        // Building B — Regular classrooms
        ['room_name' => 'Room 201', 'building' => 'Building B', 'capacity' => 45],
        ['room_name' => 'Room 202', 'building' => 'Building B', 'capacity' => 45],
        ['room_name' => 'Room 203', 'building' => 'Building B', 'capacity' => 40],
        ['room_name' => 'Room 204', 'building' => 'Building B', 'capacity' => 40],
        ['room_name' => 'Room 205', 'building' => 'Building B', 'capacity' => 40],

        // Building C — Regular classrooms
        ['room_name' => 'Room 301', 'building' => 'Building C', 'capacity' => 50],
        ['room_name' => 'Room 302', 'building' => 'Building C', 'capacity' => 50],
        ['room_name' => 'Room 303', 'building' => 'Building C', 'capacity' => 45],

        // Specialized rooms
        ['room_name' => 'Science Lab 1',   'building' => 'Science Wing',  'capacity' => 35],
        ['room_name' => 'Science Lab 2',   'building' => 'Science Wing',  'capacity' => 35],
        ['room_name' => 'Computer Lab 1',  'building' => 'ICT Building',  'capacity' => 40],
        ['room_name' => 'Computer Lab 2',  'building' => 'ICT Building',  'capacity' => 40],
        ['room_name' => 'Home Economics',  'building' => 'TLE Building',  'capacity' => 30],
        ['room_name' => 'Workshop',        'building' => 'TLE Building',  'capacity' => 30],
        ['room_name' => 'AVR',             'building' => 'Main Building', 'capacity' => 150],
        ['room_name' => 'Library',         'building' => 'Main Building', 'capacity' => 60],
        ['room_name' => 'Gymnasium',       'building' => 'Sports Complex', 'capacity' => 200],
    ];

    public function run(): void
    {
        $ay = AcademicYear::where('status', 'active')->first()
            ?? AcademicYear::orderByDesc('start_date')->first();

        if (! $ay) {
            $this->command->warn('No academic year found. Run SectionSeeder first or create an academic year.');
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach (self::ROOMS as $room) {
            $classroom = Classroom::firstOrCreate(
                ['academic_year_id' => $ay->id, 'room_name' => $room['room_name']],
                ['building' => $room['building'], 'capacity' => $room['capacity'], 'status' => 'active']
            );
            $classroom->wasRecentlyCreated ? $created++ : $skipped++;
        }

        $this->command->info("ClassroomSeeder done: {$created} created, {$skipped} already existed (year: {$ay->year_label}).");
    }
}
