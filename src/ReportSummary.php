<?php

namespace Cheppers\LintReport;

/**
 * Class ReportStats.
 *
 * @package Cheppers\LintReport
 */
class ReportSummary extends ReportBase
{

    /**
     * {@inheritdoc}
     */
    protected function doIt()
    {
        $i = 0;
        $files = $this->getValue($this->source, $this->getFilesParents());
        foreach ($files as $file_name => $file_report) {
            $errors = $this->getValue($file_report, $this->getErrorsParents());
            $report = $this->examineErrors($errors);
            $report['widths'] += [
                'severity' => 0,
                'source' => 0,
                'line' => 0,
                'column' => 0,
                'message' => 0,
            ];

            $this->destination->writeln($this->highlightHeaderBySeverity(
                $report['severity'],
                $this->normalizeFilePath($file_name)
            ));
            foreach ($report['stats'] as $source => $info) {
                $source_padded = str_pad($source, $report['widths']['source'], ' ', STR_PAD_RIGHT);
                $source_decorated = $this->highlightNormalBySeverity($info['severity'], $source_padded);
                $count_padded = str_pad($info['count'], strlen($report['occurrences']), ' ', STR_PAD_LEFT);

                $this->destination->writeln("$source_decorated: $count_padded");
            }

            if ($i !== count($files) - 1) {
                $this->destination->writeln('');
            }

            $i++;
        }

        return $this;
    }
}
