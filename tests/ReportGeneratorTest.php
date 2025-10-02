<?php

namespace Lakshithasankalpa\AssessmentReports;

use PHPUnit\Framework\TestCase;

// require_once __DIR__ . '/../src/ReportGenerator.php';

class ReportGeneratorTest extends TestCase
{
    private $generator;

    protected function setUp(): void
    {
        $this->generator = new ReportGenerator();
    }

    // ////// Helpers //////////////

    private function isCorrectFromGenerator($response, $questionId)
    {
        foreach ($this->generator->getQuestions() as $q) {
            if ($q['id'] === $questionId) {
                return $response === $q['config']['key'];
            }
        }
        return false;
    }

    // //////// Cases ////////////////

    public function testGetStudentName()
    {
        $this->assertEquals('Tony Stark', $this->generator->getStudentName('student1'));
    }

    public function testIsCorrect()
    {
        $this->assertTrue($this->isCorrectFromGenerator('option3', 'numeracy1'));
        $this->assertFalse($this->isCorrectFromGenerator('option1', 'numeracy1'));
    }

    public function testGenerateDiagnostic()
    {
        $output = $this->generator->generateDiagnostic('student1');
        $this->assertStringContainsString('Tony Stark', $output);
        $this->assertStringContainsString('15 questions right out of 16', $output);
        $this->assertStringContainsString('Number and Algebra: 5 out of 5', $output);
    }

    public function testGenerateProgress()
    {
        $output = $this->generator->generateProgress('student1');
        $this->assertStringContainsString('3 times in total', $output);
        $this->assertStringContainsString('9 more correct', $output);
    }

    public function testGenerateFeedback()
    {
        $output = $this->generator->generateFeedback('student1');
        $this->assertStringContainsString("What is the 'median'", $output);
        $this->assertStringContainsString('Your answer: A with value 7', $output);
    }
}
