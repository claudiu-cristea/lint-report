<?php

namespace Cheppers\LintReport;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

/**
 * Class ReportVerbose.
 *
 * @package Cheppers\LintReport
 */
class ReportVerbose extends ReportBase
{

    /**
     * {@inheritdoc}
     */
    protected function doIt()
    {
        $columns = [
            'severity' => '?',
            'source' => '?',
            'line' => '?',
            'column' => '?',
            'message' => '?',
        ];
        $i = 0;
        $files = $this->getValue($this->source, $this->getFilesParents());
        foreach ($files as $file_name => $file_report) {
            $errors = $this->getValue($file_report, $this->getErrorsParents());
            if (!$errors) {
                $i++;

                continue;
            }

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
            /** @var array $error */
            foreach ($errors as $error) {
                $error += $columns;
                $row = $columns;
                foreach (array_keys($columns) as $dst) {
                    $src = $this->columnMapping[$dst];
                    switch ($dst) {
                        case 'severity':
                            $row[$dst] = strtolower($error[$src]);
                            break;

                        case 'source':
                        case 'message':
                        case 'line':
                        case 'column':
                            $row[$dst] = $error[$src];
                            break;
                    }
                }

                $table->addRow($row);
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
