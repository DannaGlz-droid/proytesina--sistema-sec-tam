<?php
// scripts/delete_users_except_allowed.php
// Usage: php scripts/delete_users_except_allowed.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

$allowed = [
    'admin_local_test',
    'coordinador_local_test',
    'operador_local_test',
    'invitado_local_test',
    'jojo20',
];

echo "Deleting users except: " . implode(', ', $allowed) . "\n";

// Collect user ids that are NOT allowed
$ids = DB::table('users')->whereNotIn('username', $allowed)->pluck('id')->toArray();

if (empty($ids)) {
    echo "No users to delete.\n";
    exit(0);
}

$summary = [
    'users_found' => count($ids),
    'users_deleted' => 0,
    'notifications_deleted' => 0,
    'comments_deleted' => 0,
    'comment_reads_deleted' => 0,
    'publications_deleted' => 0,
    'report_rows_deleted' => 0,
];

foreach ($ids as $uid) {
    echo "Processing user id={$uid}...\n";
    try {
        // Notifications where user is recipient or sender
        if (Schema::hasTable('notifications')) {
            if (Schema::hasColumn('notifications', 'recipient_user_id')) {
                $n1 = DB::table('notifications')->where('recipient_user_id', $uid)->delete();
                $summary['notifications_deleted'] += $n1;
            }
            if (Schema::hasColumn('notifications', 'sender_user_id')) {
                $n2 = DB::table('notifications')->where('sender_user_id', $uid)->delete();
                $summary['notifications_deleted'] += $n2;
            }
        }

        // Comment reads
        if (Schema::hasTable('comment_reads') && Schema::hasColumn('comment_reads', 'user_id')) {
            $ncr = DB::table('comment_reads')->where('user_id', $uid)->delete();
            $summary['comment_reads_deleted'] += $ncr;
        }

        // Publication comments by user
        if (Schema::hasTable('publication_comments') && Schema::hasColumn('publication_comments', 'user_id')) {
            $nc = DB::table('publication_comments')->where('user_id', $uid)->count();
            if ($nc > 0) {
                $delc = DB::table('publication_comments')->where('user_id', $uid)->delete();
                $summary['comments_deleted'] += $delc;
            }
        }

        // Publications authored by user: delete related publication_files and publications
        $pubIds = [];
        if (Schema::hasTable('publications') && Schema::hasColumn('publications', 'user_id')) {
            $pubIds = DB::table('publications')->where('user_id', $uid)->pluck('id')->toArray();
        }
        if (!empty($pubIds)) {
            // publication_files
            $pf = DB::table('publication_files')->whereIn('publication_id', $pubIds)->get();
            foreach ($pf as $row) {
                if (!empty($row->file_path)) {
                    try {
                        if (Storage::exists($row->file_path)) {
                            Storage::delete($row->file_path);
                            echo "Deleted file: {$row->file_path}\n";
                        }
                    } catch (\Exception $e) {
                        echo "Storage delete error for {$row->file_path}: " . $e->getMessage() . "\n";
                    }
                }
            }
            $delPf = DB::table('publication_files')->whereIn('publication_id', $pubIds)->delete();
            $summary['report_rows_deleted'] += $delPf;

            // delete publications
            $delP = DB::table('publications')->whereIn('id', $pubIds)->delete();
            $summary['publications_deleted'] += $delP;
        }

        // Delete report rows that might reference this user directly
        // Delete report rows that might reference this user directly (if column exists)
        $reportTables = [
            'breathalyzer_reports',
            'road_safety_reports',
            'injury_observatory_reports',
        ];
        foreach ($reportTables as $rt) {
            if (Schema::hasTable($rt) && Schema::hasColumn($rt, 'user_id')) {
                $r = DB::table($rt)->where('user_id', $uid)->delete();
                $summary['report_rows_deleted'] += $r;
            }
        }

        // Finally delete user
        $del = DB::table('users')->where('id', $uid)->delete();
        if ($del) {
            $summary['users_deleted']++;
            echo "Deleted user id={$uid}\n";
        } else {
            echo "Failed to delete user id={$uid}\n";
        }

    } catch (\Exception $e) {
        echo "Error processing user id={$uid}: " . $e->getMessage() . "\n";
    }
}

echo "\nSummary:\n";
foreach ($summary as $k => $v) {
    echo str_pad($k, 25) . ": " . $v . "\n";
}

echo "Done.\n";
exit(0);
