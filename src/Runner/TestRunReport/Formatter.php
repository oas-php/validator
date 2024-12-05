<?php declare(strict_types=1);

namespace OAS\Runner\Report;

use OAS\Runner\TestRunReport;

interface Formatter
{
    public function format(TestRunReport $report): string;
}