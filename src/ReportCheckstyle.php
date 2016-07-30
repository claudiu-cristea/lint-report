<?php

namespace Cheppers\LintReport;

/**
 * Class ReportCheckstyle.
 *
 * @package Cheppers\LintReport
 */
class ReportCheckstyle extends ReportBase
{

    /**
     * {@inheritdoc}
     */
    protected function doIt()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = true;
        $e_checkstyle = $dom->createElement('checkstyle');
        $e_checkstyle->setAttribute('version', '2.6.1');
        $dom->appendChild($e_checkstyle);

        foreach ($this->getValue($this->source, $this->getFilesParents()) as $file_name => $file_report) {
            $errors = $this->getValue($file_report, $this->getErrorsParents());
            $e_file = $dom->createElement('file');
            $e_file->setAttribute('name', $this->normalizeFilePath($file_name));
            $e_checkstyle->appendChild($e_file);

            foreach ($errors as $error) {
                $e_error = $dom->createElement('error');
                $e_file->appendChild($e_error);

                foreach ($this->columnMapping as $dst => $src) {
                    if (isset($error[$src])) {
                        if ($dst === 'severity') {
                            $error[$src] = strtolower($error[$src]);
                        }

                        $e_error->setAttribute($dst, $error[$src]);
                    }
                }
            }
        }
        $this->destination->write($dom->saveXML());

        return $this;
    }
}
