<?php
// scripts/delete_remaining_publications.php
// Usage: php scripts/delete_remaining_publications.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Publication;

echo "Deleting remaining publications and their related notifications/comments/files...\n";

$pubIds = DB::table('publications')->pluck('id')->toArray();
if (empty($pubIds)) {
    echo "No publications found. Nothing to delete.\n";
    exit(0);
}

$totalPubs = count($pubIds);
$deletedPubs = 0;
$deletedFileRows = 0;
$deletedPhysicalFiles = 0;
$deletedCommentRows = 0;
$deletedNotifications = 0;

foreach ($pubIds as $id) {
    echo "Processing publication id={$id}...\n";

    try {
        // Delete notifications referencing the publication directly
        $n = DB::table('notifications')->where('publication_id', $id)->delete();
        $deletedNotifications += $n;

        // Get comment ids for this publication
        $commentIds = DB::table('publication_comments')->where('publication_id', $id)->pluck('id')->toArray();
        if (!empty($commentIds)) {
            // Delete notifications referencing these comments
            $n2 = DB::table('notifications')->whereIn('publication_comment_id', $commentIds)->delete();
            $deletedNotifications += $n2;

            // Delete comments
            $n3 = DB::table('publication_comments')->where('publication_id', $id)->delete();
            $deletedCommentRows += $n3;
        }

        // Delete publication_files rows and physical files
        $files = DB::table('publication_files')->where('publication_id', $id)->get();
        foreach ($files as $f) {
            if (!empty($f->file_path)) {
                try {
                    if (Storage::exists($f->file_path)) {
                        if (Storage::delete($f->file_path)) {
                            $deletedPhysicalFiles++;
                            echo "Deleted file: {$f->file_path}\n";
                        }
                    }
                } catch (\Exception $e) {
                    echo "Storage delete error for {$f->file_path}: " . $e->getMessage() . "\n";
                }
            }
            $deletedFileRows++;
        }
        DB::table('publication_files')->where('publication_id', $id)->delete();

        // Also delete rows in report tables that may reference this publication
        DB::table('breathalyzer_reports')->where('publication_id', $id)->delete();
        DB::table('road_safety_reports')->where('publication_id', $id)->delete();
        DB::table('injury_observatory_reports')->where('publication_id', $id)->delete();

        // Finally delete the publication
        $pub = Publication::withTrashed()->find($id);
        if ($pub) {
            $pub->forceDelete();
            $deletedPubs++;
            echo "Deleted publication id={$id}\n";
        } else {
            echo "Publication id={$id} not found when attempting delete.\n";
        }

    } catch (\Exception $e) {
        echo "Error processing publication id={$id}: " . $e->getMessage() . "\n";
    }
}

echo "\nSummary:\n";
echo "Publications found: {$totalPubs}\n";
echo "Publications deleted: {$deletedPubs}\n";
echo "Publication file rows removed: {$deletedFileRows}\n";
echo "Physical files removed: {$deletedPhysicalFiles}\n";
echo "Publication comment rows removed: {$deletedCommentRows}\n";
echo "Notifications removed: {$deletedNotifications}\n";

echo "Done.\n";
exit(0);
