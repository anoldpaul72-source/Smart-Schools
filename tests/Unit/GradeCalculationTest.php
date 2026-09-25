<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Mark;
use App\Http\Controllers\LeaderController;

class GradeCalculationTest extends TestCase
{
    public function test_mark_model_grading_scale(): void
    {
        // 75-100 = A
        $this->assertEquals(['A', 'Excellent'], Mark::calculateGrade(100));
        $this->assertEquals(['A', 'Excellent'], Mark::calculateGrade(75));

        // 60-74 = B
        $this->assertEquals(['B', 'Very Good'], Mark::calculateGrade(74.9));
        $this->assertEquals(['B', 'Very Good'], Mark::calculateGrade(65));
        $this->assertEquals(['B', 'Very Good'], Mark::calculateGrade(60));

        // 45-59 = C
        $this->assertEquals(['C', 'Good'], Mark::calculateGrade(59.9));
        $this->assertEquals(['C', 'Good'], Mark::calculateGrade(50));
        $this->assertEquals(['C', 'Good'], Mark::calculateGrade(45));

        // 30-44 = D
        $this->assertEquals(['D', 'Pass'], Mark::calculateGrade(44.9));
        $this->assertEquals(['D', 'Pass'], Mark::calculateGrade(35));
        $this->assertEquals(['D', 'Pass'], Mark::calculateGrade(30));

        // 0-29 = F
        $this->assertEquals(['F', 'Fail'], Mark::calculateGrade(29.9));
        $this->assertEquals(['F', 'Fail'], Mark::calculateGrade(15));
        $this->assertEquals(['F', 'Fail'], Mark::calculateGrade(0));
    }

    public function test_leader_controller_grading_and_points(): void
    {
        $controller = new LeaderController();

        // 75-100 = A, Points: 1
        $this->assertEquals('A', $controller->getGradeInfo(100)['G']);
        $this->assertEquals(1, $controller->getGradeInfo(100)['P']);
        $this->assertEquals('A', $controller->getGradeInfo(75)['G']);
        $this->assertEquals(1, $controller->getGradeInfo(75)['P']);

        // 60-74 = B, Points: 2
        $this->assertEquals('B', $controller->getGradeInfo(74.9)['G']);
        $this->assertEquals(2, $controller->getGradeInfo(74.9)['P']);
        $this->assertEquals('B', $controller->getGradeInfo(60)['G']);
        $this->assertEquals(2, $controller->getGradeInfo(60)['P']);

        // 45-59 = C, Points: 3
        $this->assertEquals('C', $controller->getGradeInfo(59.9)['G']);
        $this->assertEquals(3, $controller->getGradeInfo(59.9)['P']);
        $this->assertEquals('C', $controller->getGradeInfo(45)['G']);
        $this->assertEquals(3, $controller->getGradeInfo(45)['P']);

        // 30-44 = D, Points: 4
        $this->assertEquals('D', $controller->getGradeInfo(44.9)['G']);
        $this->assertEquals(4, $controller->getGradeInfo(44.9)['P']);
        $this->assertEquals('D', $controller->getGradeInfo(30)['G']);
        $this->assertEquals(4, $controller->getGradeInfo(30)['P']);

        // 0-29 = F, Points: 5
        $this->assertEquals('F', $controller->getGradeInfo(29.9)['G']);
        $this->assertEquals(5, $controller->getGradeInfo(29.9)['P']);
        $this->assertEquals('F', $controller->getGradeInfo(0)['G']);
        $this->assertEquals(5, $controller->getGradeInfo(0)['P']);
    }

    public function test_a_level_mark_grading_scale(): void
    {
        // 80-100 = A
        $this->assertEquals(['A', 'Excellent'], Mark::calculateGrade(100, true));
        $this->assertEquals(['A', 'Excellent'], Mark::calculateGrade(80, true));

        // 70-79 = B
        $this->assertEquals(['B', 'Very Good'], Mark::calculateGrade(79.9, true));
        $this->assertEquals(['B', 'Very Good'], Mark::calculateGrade(70, true));

        // 60-69 = C
        $this->assertEquals(['C', 'Good'], Mark::calculateGrade(69.9, true));
        $this->assertEquals(['C', 'Good'], Mark::calculateGrade(60, true));

        // 50-59 = D
        $this->assertEquals(['D', 'Satisfactory'], Mark::calculateGrade(59.9, true));
        $this->assertEquals(['D', 'Satisfactory'], Mark::calculateGrade(50, true));

        // 40-49 = E
        $this->assertEquals(['E', 'Pass'], Mark::calculateGrade(49.9, true));
        $this->assertEquals(['E', 'Pass'], Mark::calculateGrade(40, true));

        // 36-40 = S (Subsidiary)
        $this->assertEquals(['S', 'Subsidiary'], Mark::calculateGrade(39.9, true));
        $this->assertEquals(['S', 'Subsidiary'], Mark::calculateGrade(36, true));

        // 0-35 = F
        $this->assertEquals(['F', 'Fail'], Mark::calculateGrade(35.9, true));
        $this->assertEquals(['F', 'Fail'], Mark::calculateGrade(20, true));
        $this->assertEquals(['F', 'Fail'], Mark::calculateGrade(0, true));
    }

    public function test_leader_controller_a_level_grades_and_divisions(): void
    {
        $controller = new LeaderController();

        // Grade info & points (A-Level: A=1, B=2, C=3, D=4, E=5, S=6, F=7)
        $this->assertEquals('A', $controller->getGradeInfo(85, true)['G']);
        $this->assertEquals(1, $controller->getGradeInfo(85, true)['P']);

        $this->assertEquals('B', $controller->getGradeInfo(75, true)['G']);
        $this->assertEquals(2, $controller->getGradeInfo(75, true)['P']);

        $this->assertEquals('C', $controller->getGradeInfo(65, true)['G']);
        $this->assertEquals(3, $controller->getGradeInfo(65, true)['P']);

        $this->assertEquals('D', $controller->getGradeInfo(55, true)['G']);
        $this->assertEquals(4, $controller->getGradeInfo(55, true)['P']);

        $this->assertEquals('E', $controller->getGradeInfo(45, true)['G']);
        $this->assertEquals(5, $controller->getGradeInfo(45, true)['P']);

        $this->assertEquals('S', $controller->getGradeInfo(38, true)['G']);
        $this->assertEquals(6, $controller->getGradeInfo(38, true)['P']);

        $this->assertEquals('F', $controller->getGradeInfo(30, true)['G']);
        $this->assertEquals(7, $controller->getGradeInfo(30, true)['P']);

        // A-Level Division (Best 3 Principal Subjects)
        $this->assertEquals('I', $controller->calculateDivision(3, true));
        $this->assertEquals('I', $controller->calculateDivision(9, true));
        $this->assertEquals('II', $controller->calculateDivision(10, true));
        $this->assertEquals('II', $controller->calculateDivision(12, true));
        $this->assertEquals('III', $controller->calculateDivision(13, true));
        $this->assertEquals('III', $controller->calculateDivision(17, true));
        $this->assertEquals('IV', $controller->calculateDivision(18, true));
        $this->assertEquals('IV', $controller->calculateDivision(19, true));
        $this->assertEquals('0', $controller->calculateDivision(20, true));
        $this->assertEquals('0', $controller->calculateDivision(21, true));
    }
}
