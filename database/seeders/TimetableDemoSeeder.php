<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\User;
use App\Models\Subject;
use App\Models\Timetable;
use Illuminate\Support\Facades\Hash;

class TimetableDemoSeeder extends Seeder
{
    public function run()
    {
        $school = School::firstOrCreate(
            ['school_name' => 'Kome Secondary School'],
            ['address' => 'Geita, Tanzania', 'phone' => '+255 700 000 001']
        );

        User::where('username', 'admin')->update(['school_name' => 'Kome Secondary School']);
        User::where('username', 'headmaster1')->update(['school_name' => 'Kome Secondary School']);

        $teachersData = [
            'Nzaria Waziri'   => 'nzaria@kome.ac.tz',
            'Emily'           => 'emily@kome.ac.tz',
            'Bakari'          => 'bakari@kome.ac.tz',
            'Anold Sylvester' => 'anold@kome.ac.tz',
        ];

        $teachers = [];
        foreach ($teachersData as $tName => $tEmail) {
            $username = strtolower(str_replace(' ', '', $tName));
            $teachers[$tName] = User::firstOrCreate(
                ['username' => $username],
                [
                    'name'        => $tName,
                    'email'       => $tEmail,
                    'password'    => Hash::make('password123'),
                    'role'        => 'Teacher',
                    'school_name' => 'Kome Secondary School',
                ]
            );
        }

        $subjectNames = [
            'Biology', 'Business', 'Chemistry', 'Civics', 'Computer Science',
            'English', 'Geograph', 'History', 'Kiswahili', 'Mathematics', 'Physics'
        ];

        $subjects = [];
        foreach ($subjectNames as $sName) {
            $subjects[$sName] = Subject::firstOrCreate(['subject_name' => $sName]);
        }

        // Exact slots for Form 1 from the user's screenshot:
        $slots = [
            'Monday' => [
                1 => ['Biology', 'Nzaria Waziri'],
                2 => ['Business', 'Nzaria Waziri'],
                3 => ['Chemistry', 'Nzaria Waziri'],
                4 => ['Civics', 'Nzaria Waziri'],
                5 => ['Computer Science', 'Nzaria Waziri'],
                6 => ['English', 'Nzaria Waziri'],
                7 => ['Geograph', 'Nzaria Waziri'],
                8 => ['History', 'Nzaria Waziri'],
                9 => ['Kiswahili', 'Nzaria Waziri'],
            ],
            'Tuesday' => [
                1 => ['Mathematics', 'Nzaria Waziri'],
                2 => ['Physics', 'Nzaria Waziri'],
                3 => ['Biology', 'Emily'],
                4 => ['Business', 'Emily'],
                5 => ['Chemistry', 'Emily'],
                6 => ['Civics', 'Emily'],
                7 => ['Computer Science', 'Emily'],
                8 => ['English', 'Emily'],
                9 => ['Geograph', 'Emily'],
            ],
            'Wednesday' => [
                1 => ['History', 'Emily'],
                2 => ['Kiswahili', 'Emily'],
                3 => ['Mathematics', 'Emily'],
                4 => ['Physics', 'Emily'],
                5 => ['Biology', 'Bakari'],
                6 => ['Business', 'Bakari'],
                7 => ['Chemistry', 'Bakari'],
                8 => ['Civics', 'Bakari'],
                9 => ['Computer Science', 'Bakari'],
            ],
            'Thursday' => [
                1 => ['English', 'Bakari'],
                2 => ['Geograph', 'Bakari'],
                3 => ['History', 'Bakari'],
                4 => ['Kiswahili', 'Bakari'],
                5 => ['Mathematics', 'Bakari'],
                6 => ['Physics', 'Bakari'],
                7 => ['Biology', 'Anold Sylvester'],
                8 => ['Business', 'Anold Sylvester'],
                9 => ['Chemistry', 'Anold Sylvester'],
            ],
            'Friday' => [
                1 => ['Civics', 'Anold Sylvester'],
                2 => ['Computer Science', 'Anold Sylvester'],
                3 => ['English', 'Anold Sylvester'],
                4 => ['Geograph', 'Anold Sylvester'],
                5 => ['History', 'Anold Sylvester'],
                6 => ['Kiswahili', 'Anold Sylvester'],
                7 => ['Mathematics', 'Anold Sylvester'],
                8 => ['Physics', 'Anold Sylvester'],
                9 => ['Biology', 'Anold Sylvester'],
            ],
        ];

        Timetable::where('school_name', 'Kome Secondary School')
            ->where('class_name', 'Form 1')
            ->delete();

        foreach ($slots as $day => $periodMap) {
            foreach ($periodMap as $pNum => $pair) {
                [$subName, $teachName] = $pair;
                Timetable::create([
                    'school_name'   => 'Kome Secondary School',
                    'class_name'    => 'Form 1',
                    'day_of_week'   => $day,
                    'period_number' => $pNum,
                    'subject_id'    => $subjects[$subName]->id,
                    'teacher_id'    => $teachers[$teachName]->id,
                ]);
            }
        }
    }
}
