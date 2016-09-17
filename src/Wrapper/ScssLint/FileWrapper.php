<?php

namespace Cheppers\LintReport\Wrapper\ScssLint;

use Cheppers\LintReport\FileWrapperInterface;
use Cheppers\LintReport\ReportWrapperInterface;

/**
 * Class FileWrapper.
 *
 * @package Cheppers\LintReport\Wrapper\ScssLint
 */
class FileWrapper implements FileWrapperInterface
{
    /**
     * @var array
     */
    protected $file = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $file)
    {
        $this->file = $file + [
            'filePath' => '',
            'errors' => 0,
            'warnings' => 0,
            'stats' => [],
            'failures' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function filePath()
    {
        return $this->file['filePath'];
    }

    /**
     * {@inheritdoc}
     */
    public function numOfErrors()
    {
        return $this->file['errors'];
    }

    /**
     * {@inheritdoc}
     */
    public function numOfWarnings()
    {
        return $this->file['warnings'];
    }

    /**
     * {@inheritdoc}
     */
    public function yieldFailures()
    {
        foreach ($this->file['failures'] as $failure) {
            yield new FailureWrapper($failure);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stats()
    {
        if (!$this->file['stats']) {
            $this->file['stats'] = [
                'severity' => 0,
                'has' => [
                    ReportWrapperInterface::SEVERITY_OK => false,
                    ReportWrapperInterface::SEVERITY_WARNING => false,
                    ReportWrapperInterface::SEVERITY_ERROR => false,
                ],
                'source' => [],
            ];
            foreach ($this->file['failures'] as $failure) {
                if ($this->file['stats']['severity'] < $failure['severity']) {
                    $this->file['stats']['severity'] = $failure['severity'];
                }

                $this->file['stats']['has'][$failure['severity']] = true;

                $this->file['stats']['source'] += [
                    $failure['linter'] => [
                        'severity' => $failure['severity'],
                        'count' => 0,
                    ],
                ];
                $this->file['stats']['source'][$failure['linter']]['count']++;
            }
        }

        return $this->file['stats'];
    }


    /**
     * @return string
     */
    public function highestSeverity()
    {
        if ($this->numOfErrors()) {
            return ReportWrapperInterface::SEVERITY_ERROR;
        }

        if ($this->numOfWarnings()) {
            return ReportWrapperInterface::SEVERITY_WARNING;
        }

        return ReportWrapperInterface::SEVERITY_OK;
    }
}
