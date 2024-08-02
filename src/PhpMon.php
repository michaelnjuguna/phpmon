<?php

namespace MichaelNjuguna\PhpMon;

use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;

class PhpMon
{
    private $filename;
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
    public function run()
    {
        $filesystem = new Filesystem();
        if (!!$filesystem->exists($this->filename) === false) {
            echo "Error: File {$this->filename} does not exist.\n";
            exit(1);
        }
        $lastModifiedTime = filemtime($this->filename);
        $process = $this->startProcess();
        while (true) {
            clearstatcache();
            $currentModifiedTime = filemtime($this->filename);
            if ($currentModifiedTime > $lastModifiedTime) {
                $lastModifiedTime = $currentModifiedTime;

                $process->stop();
                $process = $this->startProcess();
                echo "Server restarted at " . date('Y-m-d H:i:s') . "\n";
            }
            // Sleep for a short period to reduce CPU usage
            usleep(500000);
        }
    }
    public function startProcess()
    {
        $process = new Process(['php', $this->filename]);
        $process->start();
        return $process;
    }
}