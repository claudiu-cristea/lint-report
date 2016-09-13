<?php

namespace Cheppers\LintReport;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

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

            $table = new Table($this->destination);
            $table->setHeaders([
                'Source',
                'Occurrences',
            ]);
            $tableStyleAlignRight = new TableStyle();
            $tableStyleAlignRight->setPadType(STR_PAD_LEFT);
            $table->setColumnStyle(1, $tableStyleAlignRight);
            foreach ($report['stats'] as $source => $info) {
                $table->addRow([
                    $this->highlightNormalBySeverity($info['severity'], $source),
                    $info['count'],
                ]);
            }

            $table->render();

            if ($i !== count($files) - 1) {
                $this->destination->writeln('');
            }

            $i++;
        }

        return $this;
    }
}
