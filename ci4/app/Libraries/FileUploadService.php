<?php

namespace App\Libraries;

class FileUploadService
{
    public function uploadFile(string $inputName, string $targetDir = 'uploads/'): ?string
    {
        $file = \Config\Services::request()->getFile($inputName);

        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(WRITEPATH . '../public/' . $targetDir)) {
                mkdir(WRITEPATH . '../public/' . $targetDir, 0777, true);
            }

            $newName = time() . '_' . $file->getRandomName();
            $file->move(WRITEPATH . '../public/' . $targetDir, $newName);
            return $targetDir . $newName;
        }

        return null;
    }

    public function uploadMultipleFiles(array $files, string $targetDir = 'uploads/'): array
    {
        $paths = [];
        foreach ($files as $file) {
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $path = $this->uploadSingleFile($file, $targetDir);
                if ($path) {
                    $paths[] = $path;
                }
            }
        }
        return $paths;
    }

    protected function uploadSingleFile($file, string $targetDir): ?string
    {
        $dir = WRITEPATH . '../public/' . $targetDir;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $newName = time() . '_' . $file->getRandomName();
        $file->move($dir, $newName);
        return $targetDir . $newName;
    }

    public function deleteFile(string $filePath): bool
    {
        $fullPath = WRITEPATH . '../public/' . $filePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
