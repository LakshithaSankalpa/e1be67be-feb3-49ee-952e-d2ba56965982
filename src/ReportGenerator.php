<?php

namespace Lakshithasankalpa\AssessmentReports;

use DateTime;

class ReportGenerator
{
    private $students;
    private $assessments;
    private $questions;
    private $responses;

    public function __construct()
    {
        $this->students = json_decode(file_get_contents('data/students.json'), true);
        $this->assessments = json_decode(file_get_contents('data/assessments.json'), true);
        $this->questions = json_decode(file_get_contents('data/questions.json'), true);
        $this->responses = json_decode(file_get_contents('data/student-responses.json'), true);
    }

    public function getStudentName($studentId)
    {
        foreach ($this->students as $student) {
            if ($student['id'] === $studentId) {
                return $student['firstName'] . ' ' . $student['lastName'];
            }
        }
        return 'Unknown Student';
    }

    public function getCompletedResponses($studentId)
    {
        $completed = [];
        foreach ($this->responses as $resp) {
            if (isset($resp['student']['id']) && $resp['student']['id'] === $studentId && isset($resp['completed'])) {
                $completed[] = $resp;
            }
        }
        usort($completed, function ($a, $b) {
            return strtotime($a['completed']) - strtotime($b['completed']);
        });
        return $completed;
    }

    public function formatDate($dateStr)
    {
        $dt = DateTime::createFromFormat('d/m/Y H:i:s', $dateStr);
        if ($dt) {
            return $dt->format('jS F Y g:i A');
        }
        return $dateStr;
    }

    private function isCorrect($response, $questionId)
    {
        foreach ($this->questions as $q) {
            if ($q['id'] === $questionId) {
                return $response === $q['config']['key'];
            }
        }
        return false;
    }

    private function getStrandCounts($responsesArray, $strand)
    {
        $total = 0;
        $correct = 0;
        foreach ($responsesArray as $resp) {
            foreach ($resp['responses'] as $r) {
                foreach ($this->questions as $q) {
                    if ($q['id'] === $r['questionId'] && $q['strand'] === $strand) {
                        $total++;
                        if ($this->isCorrect($r['response'], $r['questionId'])) {
                            $correct++;
                        }
                    }
                }
            }
        }
        return [$correct, $total];
    }

    private function getAssessmentName($assId)
    {
        foreach ($this->assessments as $ass) {
            if ($ass['id'] === $assId) {
                return $ass['name'];
            }
        }
        return 'Unknown';
    }

    private function getQuestion($qId)
    {
        foreach ($this->questions as $q) {
            if ($q['id'] === $qId) {
                return $q;
            }
        }
        return null;
    }

    private function getOption($question, $optId)
    {
        foreach ($question['config']['options'] as $opt) {
            if ($opt['id'] === $optId) {
                return $opt;
            }
        }
        return ['label' => 'Unknown', 'value' => 'Unknown'];
    }

    public function generateDiagnostic($studentId)
    {
        $completions = $this->getCompletedResponses($studentId);
        if (empty($completions)) {
            return "No completed assessments found.";
        }
        $latest = end($completions);
        $name = $this->getStudentName($studentId);
        $assessment = $this->getAssessmentName($latest['assessmentId']);
        $date = $this->formatDate($latest['completed']);
        $rawScore = $latest['results']['rawScore'];
        $totalQuestions = count($latest['responses']);

        $output = "$name recently completed $assessment assessment on $date\n";
        $output .= "He got $rawScore questions right out of $totalQuestions. Details by strand given below:\n\n";

        $strands = ['Number and Algebra', 'Measurement and Geometry', 'Statistics and Probability'];
        foreach ($strands as $strand) {
            [$correct, $total] = $this->getStrandCounts([$latest], $strand);
            $output .= "$strand: $correct out of $total correct\n";
        }

        return $output;
    }

    public function generateProgress($studentId)
    {
        $completions = $this->getCompletedResponses($studentId);
        if (empty($completions)) {
            return "No completed assessments found.";
        }
        $name = $this->getStudentName($studentId);
        $assessment = $this->getAssessmentName($completions[0]['assessmentId']);

        $output = "$name has completed $assessment assessment " . count($completions) . " times in total. Date and raw score given below:\n\n";
        foreach ($completions as $comp) {
            $date = $this->formatDate($comp['completed']);
            $score = $comp['results']['rawScore'];
            $total = count($comp['responses']);
            $output .= "Date: $date, Raw Score: $score out of $total\n";
        }

        $oldestScore = $completions[0]['results']['rawScore'];
        $recentScore = end($completions)['results']['rawScore'];
        $improvement = $recentScore - $oldestScore;
        $output .= "\n$name got $improvement more correct in the recent completed assessment than the oldest";

        return $output;
    }

    public function generateFeedback($studentId)
    {
        $completions = $this->getCompletedResponses($studentId);
        if (empty($completions)) {
            return "No completed assessments found.";
        }
        $latest = end($completions);
        $name = $this->getStudentName($studentId);
        $assessment = $this->getAssessmentName($latest['assessmentId']);
        $date = $this->formatDate($latest['completed']);
        $rawScore = $latest['results']['rawScore'];
        $totalQuestions = count($latest['responses']);

        $output = "$name recently completed $assessment assessment on $date\n";
        $output .= "He got $rawScore questions right out of $totalQuestions. Feedback for wrong answers given below\n\n";

        foreach ($latest['responses'] as $resp) {
            $questionId = $resp['questionId'];
            $response = $resp['response'];
            if (!$this->isCorrect($response, $questionId)) {
                $q = $this->getQuestion($questionId);
                $yourOpt = $this->getOption($q, $response);
                $rightOpt = $this->getOption($q, $q['config']['key']);
                $hint = $q['config']['hint'];
                $stem = $q['stem'];

                $output .= "Question: $stem\n";
                $output .= "Your answer: {$yourOpt['label']} with value {$yourOpt['value']}\n";
                $output .= "Right answer: {$rightOpt['label']} with value {$rightOpt['value']}\n";
                $output .= "Hint: $hint\n\n";
            }
        }

        return $output;
    }

    public function getQuestions()
    {
        return $this->questions;
    }
}
