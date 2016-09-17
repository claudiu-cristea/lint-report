<?php

namespace Cheppers\LintReport\Reporter;

use Cheppers\LintReport\ReportWrapperInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

/**
 * Class VerboseReporter.
 *
 * @package Cheppers\LintReport\Reporter
 */
class VerboseReporter extends BaseReporter
{
    /**
     * {@inheritdoc}
     */
    protected function doIt()
    {
        $reportWrapper = $this->getReportWrapper();
        $reportWrapper->setReport($this->source);
        if ($reportWrapper->highestSeverity() === ReportWrapperInterface::SEVERITY_OK) {
            return $this;
        }

        $i = 0;
        foreach ($reportWrapper->yieldFiles() as $fileWrapper) {
            $highestSeverity = $fileWrapper->highestSeverity();
            if ($highestSeverity === ReportWrapperInterface::SEVERITY_OK) {
                $i++;

                continue;
            }

            $this->destination->writeln($this->highlightHeaderBySeverity(
                $highestSeverity,
                $this->normalizeFilePath($fileWrapper->filePath())
            ));

            $table = new Table($this->destination);
            $table->setHeaders([
                'Severity',
                'Source',
                'Line',
                'Column',
                'Message',
            ]);
            $tableStyleAlignRight = new TableStyle();
            $tableStyleAlignRight->setPadType(STR_PAD_LEFT);
            $table
                ->setColumnStyle(2, $tableStyleAlignRight)
                ->setColumnStyle(3, $tableStyleAlignRight);
            foreach ($fileWrapper->yieldFailures() as $failureWrapper) {
                $table->addRow([
                    $failureWrapper->severity(),
                    $failureWrapper->source(),
                    $failureWrapper->line(),
                    $failureWrapper->column(),
                    $failureWrapper->message(),
                ]);
            }

            $table->render();

            if ($i !== $reportWrapper->countFiles() - 1) {
                $this->destination->writeln('');
            }

            $i++;
        }

        return $this;
    }
}
