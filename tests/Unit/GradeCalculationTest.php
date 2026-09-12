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
}
