<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class FileSummaryService
{
    public function generateSummary(string $directory): string
    {
        $outputFile = base_path('file_summary.txt');
        $files = File::allFiles($directory);

        $content = '';

        foreach ($files as $file) {
            // $relativePath = str_replace(base_path() . '/', '', $file->getRealPath());
            $relativePath = $directory . '\\' . $file->getFilename();
            $fileContent = File::get($file);

            $content .= "$relativePath:\n";
            $content .= "```\n$fileContent```\n\n";
        }

        File::put($outputFile, $content);

        return $outputFile;
    }
}
