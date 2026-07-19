<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class BackupEngine extends BaseService
{
    public function name(): string
    {
        return 'backup';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Create a full backup (DB dump + media files) into a ZIP.
     */
    public function create(string $type = 'full'): Backup
    {
        $backup = Backup::create([
            'filename'   => '',
            'path'       => '',
            'size'       => 0,
            'type'       => $type,
            'status'     => 'running',
            'created_by' => auth()->id(),
        ]);

        try {
            $tmpDir = storage_path('backups/tmp_'.$backup->id);
            File::ensureDirectoryExists($tmpDir);

            $sqlFile = $tmpDir.'/database.sql';
            $this->dumpDatabase($sqlFile);

            $zipName = 'backup_'.now()->format('Ymd_His').'_'.$backup->id.'.zip';
            $zipPath = storage_path('backups/'.$zipName);

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
                $zip->addFile($sqlFile, 'database.sql');

                if ($type === 'full') {
                    $this->addDirectoryToZip($zip, storage_path('app/public/uploads'), 'uploads');
                }

                $zip->close();
            }

            File::deleteDirectory($tmpDir);

            $backup->update([
                'filename'     => $zipName,
                'path'         => $zipPath,
                'size'         => File::size($zipPath),
                'status'       => 'completed',
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $backup->update([
                'status' => 'failed',
                'notes'  => $e->getMessage(),
            ]);
        }

        return $backup->fresh();
    }

    /**
     * Pure-PHP SQL dump without mysqldump.
     */
    public function dumpDatabase(string $outputPath): void
    {
        $db = DB::connection()->getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        $key = 'Tables_in_'.$db;

        $sql = "-- CosmicLib Backup\n-- Generated: ".now()."\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$key;

            $create = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "\n-- Table: {$tableName}\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $create[0]->{'Create Table'}.";\n\n";

            $rows = DB::table($tableName)->get();
            if ($rows->isEmpty()) {
                continue;
            }

            $columns = array_keys((array) $rows->first());
            $escapedCols = array_map(fn ($c) => "`{$c}`", $columns);

            foreach ($rows->chunk(100) as $chunk) {
                $values = [];
                foreach ($chunk as $row) {
                    $rowVals = [];
                    foreach ($columns as $col) {
                        $val = $row->$col;
                        if ($val === null) {
                            $rowVals[] = 'NULL';
                        } elseif (is_numeric($val)) {
                            $rowVals[] = $val;
                        } else {
                            $rowVals[] = "'".addslashes((string) $val)."'";
                        }
                    }
                    $values[] = '('.implode(',', $rowVals).')';
                }
                $sql .= "INSERT INTO `{$tableName}` (".implode(',', $escapedCols).") VALUES\n";
                $sql .= implode(",\n", $values).";\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        File::put($outputPath, $sql);
    }

    /**
     * Restore from a backup ZIP file.
     */
    public function restore(string $zipPath): bool
    {
        $tmpDir = storage_path('backups/restore_'.Str::random(8));
        File::ensureDirectoryExists($tmpDir);

        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('Cannot open backup file.');
        }
        $zip->extractTo($tmpDir);
        $zip->close();

        $sqlFile = $tmpDir.'/database.sql';
        if (! File::exists($sqlFile)) {
            throw new \RuntimeException('Invalid backup: database.sql not found.');
        }

        $sql = File::get($sqlFile);
        DB::unprepared($sql);

        // Restore uploads
        $uploadsDir = $tmpDir.'/uploads';
        if (File::isDirectory($uploadsDir)) {
            File::copyDirectory($uploadsDir, storage_path('app/public/uploads'));
        }

        File::deleteDirectory($tmpDir);

        return true;
    }

    /**
     * Prune old backups, keeping only the N latest.
     */
    public function prune(int $keep = 5): int
    {
        $backups = Backup::completed()->orderBy('created_at', 'desc')->get();
        $prune = $backups->slice($keep);

        foreach ($prune as $backup) {
            if (File::exists($backup->path)) {
                File::delete($backup->path);
            }
            $backup->delete();
        }

        return $prune->count();
    }

    private function addDirectoryToZip(ZipArchive $zip, string $dir, string $relative): void
    {
        if (! File::isDirectory($dir)) {
            return;
        }

        $files = File::allFiles($dir);
        foreach ($files as $file) {
            $localPath = $relative.'/'.$file->getRelativePathname();
            $zip->addFile($file->getRealPath(), $localPath);
        }
    }
}