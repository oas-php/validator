<?php declare(strict_types=1);

namespace OAS\Runner\Report\Formatter;

use OAS\Runner\TestRunReport;
use OAS\Runner\Report\Formatter;

class JsonFormatter implements Formatter
{
    public function format(TestRunReport $report): string
    {
        $arrayFormatter = new ArrayFormatter();

        return json_encode($arrayFormatter->format($report), flags: JSON_PRETTY_PRINT);
    }
}