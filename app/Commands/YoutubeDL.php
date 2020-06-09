<?php

declare(strict_types=1);

namespace App\Commands;

use Illuminate\Support\Str;
use LengthException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class YoutubeDL
{
    protected string $binary;

    public function __construct()
    {
        $this->binary = config('app.youtubedl_cmd');
    }

    public function verifyMediaUrl(string $mediaUrl): bool
    {
        $process = new Process([
            $this->binary,
            $mediaUrl,
            '--dump-json'
        ]);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Download media using the youtube-dl command.
     *
     * @param string $mediaUrl
     * @param string $mediaId
     * @param int $quality
     * @return string
     * @throws ProcessFailedException
     */
    public function downloadMediaUrl(string $mediaUrl, string $mediaId, int $quality): string
    {
        $process = new Process([
            $this->binary,
            $mediaUrl,
            '--restrict-filenames',
            '--output',
            storage_path('media/' . $mediaId . '/%(title)s.%(ext)s'),
            '--no-mtime',
            '--format',
            'bestvideo[height<=' . $quality . ']+bestaudio/best'
        ]);
        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this->getFileLocation($process->getOutput());
    }

    /**
     * Get the final file location from the youtube-dl output.
     *
     * @param string $output
     * @return string
     * @throws LengthException
     */
    private function getFileLocation(string $output): string
    {
        $lines = collect(explode(PHP_EOL, $output))->filter(function (string $line) {
            return Str::startsWith($line, '[ffmpeg] Merging formats into');
        });

        if ($lines->count() !== 1) {
            throw new LengthException();
        }

        $line = $lines->first();

        preg_match('/\"(.*)\"/', $line, $matches);

        return $matches[1];
    }
}
