<?php

namespace App\Helpers;

use App\Models\ExitInterviews;
use App\Models\Leave;
use App\Models\NotificationReadHistory;
use App\Models\Overtime;
use App\Models\Permission;
use App\Models\PinjamanKaryawan;
use App\Models\Resign;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProgressNotificationService
{
    public static function getForCurrentUser(): array
    {
        if (!Auth::check()) {
            return [
                'items' => collect(),
                'unreadCount' => 0,
                'readAllUntil' => null,
            ];
        }

        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return [
                'items' => collect(),
                'unreadCount' => 0,
                'readAllUntil' => null,
            ];
        }

        $userId = (int) $user->id;
        $readAllUntil = self::getReadAllUntilForUser($userId);

        $items = collect()
            ->merge(self::leaveItems((int) $karyawan->id, $userId, $readAllUntil))
            ->merge(self::permissionItems((int) $karyawan->id, $userId, $readAllUntil))
            ->merge(self::overtimeItems((int) $karyawan->id, $userId, $readAllUntil))
            ->merge(self::loanItems((int) $karyawan->id, $userId, $readAllUntil))
            ->merge(self::resignItems((int) $karyawan->id, $userId, $readAllUntil))
            ->merge(self::exitInterviewItems((int) $karyawan->id, $userId, $readAllUntil))
            ->filter(function (array $item) {
                return !$item['is_read'];
            })
            ->sortByDesc('updated_at')
            ->take(20)
            ->values();

        $unreadCount = $items->where('is_read', false)->count();

        return [
            'items' => $items,
            'unreadCount' => $unreadCount,
            'readAllUntil' => $readAllUntil,
        ];
    }

    public static function markAllAsReadForCurrentUser(): void
    {
        if (!Auth::check()) {
            return;
        }

        $userId = (int) Auth::id();
        $currentUnreadItems = self::getForCurrentUser()['items'];

        $now = now();

        foreach ($currentUnreadItems as $item) {
            NotificationReadHistory::updateOrCreate(
                [
                    'user_id' => $userId,
                    'item_module' => $item['module'],
                    'item_id' => $item['id'],
                ],
                [
                    'marked_all_at' => $now,
                ]
            );
        }
    }

    public static function markItemAsReadForCurrentUser(string $itemKey): void
    {
        if (!Auth::check() || empty($itemKey)) {
            return;
        }

        [$module, $itemId] = self::parseItemKey($itemKey);
        if (!$module || !$itemId) {
            return;
        }

        $userId = (int) Auth::id();

        NotificationReadHistory::updateOrCreate(
            [
                'user_id' => $userId,
                'item_module' => $module,
                'item_id' => $itemId,
            ],
            [
                'marked_at' => now(),
            ]
        );
    }

    private static function leaveItems(int $karyawanId, int $userId, ?Carbon $readAllUntil)
    {
        return Leave::query()
            ->where('id_karyawan', $karyawanId)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'approval_key', 'updated_at', 'sts_pengajuan'])
            ->map(function ($row) use ($userId, $readAllUntil) {
                return self::buildItem(
                    'leave',
                    (int) $row->id,
                    'Pengajuan Cuti',
                    self::statusLabel((int) ($row->sts_pengajuan ?? 0)),
                    route('leave.index'),
                    $row->updated_at,
                    $userId,
                    $readAllUntil
                );
            });
    }

    private static function permissionItems(int $karyawanId, int $userId, ?Carbon $readAllUntil)
    {
        return Permission::query()
            ->where('id_karyawan', $karyawanId)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'approval_key', 'updated_at', 'sts_pengajuan'])
            ->map(function ($row) use ($userId, $readAllUntil) {
                return self::buildItem(
                    'permission',
                    (int) $row->id,
                    'Pengajuan Izin',
                    self::statusLabel((int) ($row->sts_pengajuan ?? 0)),
                    route('permission.index'),
                    $row->updated_at,
                    $userId,
                    $readAllUntil
                );
            });
    }

    private static function overtimeItems(int $karyawanId, int $userId, ?Carbon $readAllUntil)
    {
        return Overtime::query()
            ->where('id_karyawan', $karyawanId)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'approval_key', 'updated_at', 'status_pengajuan'])
            ->map(function ($row) use ($userId, $readAllUntil) {
                return self::buildItem(
                    'overtime',
                    (int) $row->id,
                    'Pengajuan Lembur',
                    self::statusLabel((int) ($row->status_pengajuan ?? 0)),
                    route('overtime.index'),
                    $row->updated_at,
                    $userId,
                    $readAllUntil
                );
            });
    }

    private static function loanItems(int $karyawanId, int $userId, ?Carbon $readAllUntil)
    {
        return PinjamanKaryawan::query()
            ->where('id_karyawan', $karyawanId)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'approval_key', 'updated_at', 'status_pengajuan'])
            ->map(function ($row) use ($userId, $readAllUntil) {
                return self::buildItem(
                    'pinjaman',
                    (int) $row->id,
                    'Pengajuan Pinjaman Karyawan',
                    self::statusLabel((int) ($row->status_pengajuan ?? 0)),
                    route('pinjaman.index'),
                    $row->updated_at,
                    $userId,
                    $readAllUntil
                );
            });
    }

    private static function resignItems(int $karyawanId, int $userId, ?Carbon $readAllUntil)
    {
        return Resign::query()
            ->where('id_karyawan', $karyawanId)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'approval_key', 'updated_at', 'sts_pengajuan'])
            ->map(function ($row) use ($userId, $readAllUntil) {
                return self::buildItem(
                    'resign',
                    (int) $row->id,
                    'Pengajuan Resign',
                    self::statusLabel((int) ($row->sts_pengajuan ?? 0)),
                    route('resign.index'),
                    $row->updated_at,
                    $userId,
                    $readAllUntil
                );
            });
    }

    private static function exitInterviewItems(int $karyawanId, int $userId, ?Carbon $readAllUntil)
    {
        return ExitInterviews::query()
            ->whereHas('getPengajuan', function ($q) use ($karyawanId) {
                $q->where('id_karyawan', $karyawanId);
            })
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'id_head', 'approval_key', 'updated_at', 'sts_pengajuan'])
            ->map(function ($row) use ($userId, $readAllUntil) {
                return self::buildItem(
                    'exit-interview',
                    (int) $row->id,
                    'Form Exit Interview',
                    self::statusLabel((int) ($row->sts_pengajuan ?? 0)),
                    route('resign.index'),
                    $row->updated_at,
                    $userId,
                    $readAllUntil
                );
            });
    }

    private static function buildItem(
        string $module,
        int $id,
        string $title,
        string $statusLabel,
        string $destinationUrl,
        $updatedAt,
        int $userId,
        ?Carbon $readAllUntil
    ): array {
        $updated = $updatedAt ? Carbon::parse($updatedAt) : now();
        $isRead = self::isItemRead($userId, $module, $id, $updated, $readAllUntil);
        $itemKey = $module . '-' . $id;

        return [
            'key' => $itemKey,
            'id' => $id,
            'module' => $module,
            'title' => $title,
            'status_label' => $statusLabel,
            'url' => $destinationUrl,
            'read_url' => route('notifications.progress.read', [
                'item_key' => $itemKey,
                'redirect_url' => $destinationUrl,
            ]),
            'updated_at' => $updated,
            'updated_at_human' => $updated->locale('id')->diffForHumans(),
            'is_read' => $isRead,
        ];
    }

    private static function isItemRead(int $userId, string $module, int $itemId, Carbon $updated, ?Carbon $readAllUntil): bool
    {
        $readHistory = NotificationReadHistory::where('user_id', $userId)
            ->where('item_module', $module)
            ->where('item_id', $itemId)
            ->first();

        if ($readHistory) {
            if ($readHistory->marked_at) {
                return true;
            }
            if ($readHistory->marked_all_at && $updated->lessThanOrEqualTo($readHistory->marked_all_at)) {
                return true;
            }
        }

        if ($readAllUntil && $updated->lessThanOrEqualTo($readAllUntil)) {
            return true;
        }

        return false;
    }

    private static function getReadAllUntilForUser(int $userId): ?Carbon
    {
        $latestReadAll = NotificationReadHistory::where('user_id', $userId)
            ->whereNotNull('marked_all_at')
            ->orderByDesc('marked_all_at')
            ->first();

        return $latestReadAll ? $latestReadAll->marked_all_at : null;
    }

    private static function parseItemKey(string $itemKey): array
    {
        $parts = explode('-', $itemKey, 2);
        if (count($parts) !== 2) {
            return [null, null];
        }

        return [$parts[0], (int) $parts[1]];
    }

    private static function statusLabel(int $status): string
    {
        return match ($status) {
            1 => 'Pengajuan',
            2 => 'Disetujui',
            3 => 'Ditolak',
            4 => 'Dibatalkan',
            default => 'Diproses',
        };
    }
}
