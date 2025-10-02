<?php

namespace Lakshithasankalpa\AssessmentReports;

require_once __DIR__ . '/../src/ReportGenerator.php';

echo "Please enter the following\n\n";
$studentId = readline("Student ID: ");
echo "\n";
$reportType = (int) readline("Report to generate (1 for Diagnostic, 2 for Progress, 3 for Feedback): ");
echo "\n";
$generator = new ReportGenerator();

switch ($reportType) {
    case 1:
        echo $generator->generateDiagnostic($studentId) . "\n";
        break;
    case 2:
        echo $generator->generateProgress($studentId) . "\n";
        break;
    case 3:
        echo $generator->generateFeedback($studentId) . "\n";
        break;
    default:
        echo "Invalid report type.\n";
}
