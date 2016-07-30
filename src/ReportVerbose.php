<?php

namespace Cheppers\LintReport;

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

            /** @var array $error */
            foreach ($errors as $error) {
                $error += $columns;

                $line = [];
                foreach (array_keys($columns) as $dst) {
                    $src = $this->columnMapping[$dst];
                    switch ($dst) {
                        case 'severity':
                            $line[] = str_pad(strtolower($error[$src]), $report['widths'][$dst], ' ', STR_PAD_RIGHT);
                            break;

                        case 'source':
                            $line[] = str_pad($error[$src], $report['widths'][$dst], ' ', STR_PAD_RIGHT);
                            break;

                        case 'line':
                        case 'column':
                            $line[] = str_pad($error[$src], $report['widths'][$dst], ' ', STR_PAD_LEFT);
                            break;

                        case 'message':
                            $line [] = $error[$src];
                            break;
                    }
                }

                $this->destination->writeln(implode(' | ', $line));
            }

            if ($i !== count($files) - 1) {
                $this->destination->writeln('');
            }

            $i++;
        }

        return $this;
    }
}
