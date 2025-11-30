<?php
// scripts/delete_publications_and_files.php
// Usage: php scripts/delete_publications_and_files.php

// This script boots the Laravel app and deletes all Publication records,
// their related publication_files and publication_comments, and removes
// referenced files from storage. It prints counters so you can verify.

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use App\Models\Publication;
use Illuminate\Support\Facades\DB;

echo "Starting deletion of publications and related files...\n";

$pubCount = 0;
$fileRowsDeleted = 0;
$filesDeletedFromDisk = 0;
$commentRowsDeleted = 0;

// Adjust query if you want to target only test publications, e.g. by date or user id.
$publications = Publication::with(['files', 'comments'])->get();

foreach ($publications as $pub) {
    $pubCount++;

    // --- Remove dependent rows in other tables that reference publications ---
    try {
        // Delete notifications that reference this publication directly
        DB::table('notifications')->where('publication_id', $pub->id)->delete();

        // Delete notifications that reference publication comments (we'll collect comment ids below too)
        // Delete known report tables that reference publications
        DB::table('breathalyzer_reports')->where('publication_id', $pub->id)->delete();
        DB::table('road_safety_reports')->where('publication_id', $pub->id)->delete();
        DB::table('injury_observatory_reports')->where('publication_id', $pub->id)->delete();
    } catch (\Exception $e) {
        echo "Error deleting dependent report/notification rows for publication {$pub->id}: " . $e->getMessage() . "\n";
    }

    // Delete files from disk and count file rows
    if ($pub->files && $pub->files->count()) {
        foreach ($pub->files as $f) {
            // Attempt to delete the physical file if a path exists
            if (!empty($f->file_path)) {
                // Storage::delete returns true if deleted or false otherwise
                try {
                    if (Storage::exists($f->file_path)) {
                        if (Storage::delete($f->file_path)) {
                            $filesDeletedFromDisk++;
                            echo "Deleted file from disk: {$f->file_path}\n";
                        } else {
                            echo "Failed to delete file from disk: {$f->file_path}\n";
                        }
                    } else {
                        echo "File not found on disk (skipped): {$f->file_path}\n";
                    }
                } catch (\Exception $e) {
                    echo "Storage delete exception for {$f->file_path}: " . $e->getMessage() . "\n";
                }
            }
            $fileRowsDeleted++;
        }

        // Delete file rows
        try {
            $pub->files()->delete();
        } catch (\Exception $e) {
            echo "Error deleting publication_files rows for publication {$pub->id}: " . $e->getMessage() . "\n";
        }
    }

    // Delete comments
    if ($pub->comments && $pub->comments->count()) {
        $commentIds = $pub->comments->pluck('id')->toArray();
        $commentRowsDeleted += count($commentIds);
        try {
            // Remove notifications that reference these publication comments
            if (!empty($commentIds)) {
                DB::table('notifications')->whereIn('publication_comment_id', $commentIds)->delete();
            }

            $pub->comments()->delete();
        } catch (\Exception $e) {
            echo "Error deleting publication_comments rows for publication {$pub->id}: " . $e->getMessage() . "\n";
        }
    }

    // Finally, remove the publication itself. Use forceDelete() to bypass soft deletes.
    try {
        $pub->forceDelete();
        echo "Deleted publication id={$pub->id}\n";
    } catch (\Exception $e) {
        echo "Error deleting publication id={$pub->id}: " . $e->getMessage() . "\n";
    }
}

echo "\nSummary:\n";
echo "Publications processed: {$pubCount}\n";
echo "Publication file rows deleted: {$fileRowsDeleted}\n";
echo "Physical files deleted from disk: {$filesDeletedFromDisk}\n";
echo "Publication comment rows deleted: {$commentRowsDeleted}\n";

echo "Done.\n";

// Exit with 0 so CI/terminal knows it finished.
exit(0);
