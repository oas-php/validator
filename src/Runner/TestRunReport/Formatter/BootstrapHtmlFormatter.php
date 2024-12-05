<?php declare(strict_types=1);

namespace OAS\Runner\Report\Formatter;

use OAS\Runner\TestRunReport;
use OAS\Runner\Report\Stats;
use OAS\Runner\Report\Formatter;

class BootstrapHtmlFormatter implements Formatter
{
    public function format(TestRunReport $report): string
    {
        return $this->render(
            $this->formatStats(
                $report->getStats()
            ),
            ''
        );
    }

    private function render(string $stats, string $results): string
    {
        return <<<HTML
            <html>
                <head>
                    <title>TestRunReport</title>
                </head>
                <body>
                    $stats
                    <table>
                        <thead>
                            <th>schema</th>
                            <th>test</th>
                            <th>valid</th>
                            <th>result</th>
                            <th>duraton</th>
                        </thead>
                        $results
                    </table>
                </body>
            </html>
        HTML;
    }

    private function formatStats(Stats $stats): string
    {
        return <<<HTML
            <div>
                <p>{$stats->passedTestsCount}/{$stats->testsCount}</p>
            </div>
        HTML;
    }
}