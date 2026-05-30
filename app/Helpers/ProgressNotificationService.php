<?php

namespace App\Helpers;

use App\Models\ExitInterviews;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\Permission;
use App\Models\PinjamanKaryawan;
use App\Models\Resign;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProgressNotificationService
{
    private const SESSION_KEY_PREFIX = 'progress_notif_read_until_user_';
    private const SESSION_READ_ITEMS_KEY_PREFIX = 'progress_notif_read_items_user_';

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
        $readItemKeys = self::readItemKeys($userId);
        $readAllUntilRaw = session(self::sessionKey($userId));
        $readAllUntil = $readAllUntilRaw ? Carbon::parse($readAllUntilRaw) : null;

        $items = collect()
            ->merge(self::leaveItems((int) $karyawan->id, $readAllUntil))
            ->merge(self::permissionItems((int) $karyawan->id, $readAllUntil))
            ->merge(self::overtimeItems((int) $karyawan->id, $readAllUntil))
            ->merge(self::loanItems((int) $karyawan->id, $readAllUntil))
            ->merge(self::resignItems((int) $karyawan->id, $readAllUntil))
            ->merge(self::exitInterviewItems((int) $karyawan->id, $readAllUntil))
            ->map(function (array $item) use ($readItemKeys) {
                $item['is_read'] = $item['is_read'] || in_array($item['key'], $readItemKeys, true);
                return $item;
            })
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
        $currentUnreadKeys = self::getForCurrentUser()['items']->pluck('key')->all();
        $existingReadKeys = self::readItemKeys($userId);

        session([
            self::sessionReadItemsKey($userId) => array_values(array_unique(array_merge($existingReadKeys, $currentUnreadKeys))),
            self::sessionKey($userId) => now()->toDateTimeString(),
        ]);
    }

    public static function markItemAsReadForCurrentUser(string $itemKey): void
    {
        if (!Auth::check() || empty($itemKey)) {
            return;
        }

        $userId = (int) Auth::id();
        $readKeys = self::readItemKeys($userId);
        if (!in_array($itemKey, $readKeys, true)) {
            $readKeys[] = $itemKey;
        }

        session([self::sessionReadItemsKey($userId) => array_values(array_unique($readKeys))]);
    }

    private static function leaveItems(int $karyawanId, ?Carbon $readAllUntil)
    {
        return Leave::query()
            ->where('id_karyawan', $karyawanId)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'approval_key', 'updated_at', 'sts_pengajuan'])
            ->map(function ($row) use ($readAllUntil) {
                return self::buildItem(
                    'leave',
                    (int) $row->id,
                    'Pengajuan Cuti',
                    self::statusLabel((int) ($row->sts_pengajuan ?? 0)),
                    route('leave.index'),
                    $row->updated_at,
                    $readAllUntil
                );
            });
    }

    private static function permissionItems(int $karyawanId, ?Carbon $readAllUntil)
    {
        return Permission::query()
            ->where('id_karyawan', $karyawanId)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'approval_key', 'updated_at', 'sts_pengajuan'])
            ->map(function ($row) use ($readAllUntil) {
                return self::buildItem(
                    'permission',
                    (int) $row->id,
                    'Pengajuan Izin',
                    self::statusLabel((int) ($row->sts_pengajuan ?? 0)),
                    route('permission.index'),
                    $row->updated_at,
                    $readAllUntil
                );
            });
    }

    private static function overtimeItems(int $karyawanId, ?Carbon $readAllUntil)
    {
        return Overtime::query()
            ->where('id_karyawan', $karyawanId)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'approval_key', 'updated_at', 'status_pengajuan'])
            ->map(function ($row) use ($readAllUntil) {
                return self::buildItem(
                    'overtime',
                    (int) $row->id,
                    'Pengajuan Lembur',
                    self::statusLabel((int) ($row->status_pengajuan ?? 0)),
                    route('overtime.index'),
                    $row->updated_at,
                    $readAllUntil
                );
            });
    }

    private static function loanItems(int $karyawanId, ?Carbon $readAllUntil)
    {
        return PinjamanKaryawan::query()
            ->where('id_karyawan', $karyawanId)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'approval_key', 'updated_at', 'status_pengajuan'])
            ->map(function ($row) use ($readAllUntil) {
                return self::buildItem(
                    'pinjaman',
                    (int) $row->id,
                    'Pengajuan Pinjaman Karyawan',
                    self::statusLabel((int) ($row->status_pengajuan ?? 0)),
                    route('pinjaman.index'),
                    $row->updated_at,
                    $readAllUntil
                );
            });
    }

    private static function resignItems(int $karyawanId, ?Carbon $readAllUntil)
    {
        return Resign::query()
            ->where('id_karyawan', $karyawanId)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'approval_key', 'updated_at', 'sts_pengajuan'])
            ->map(function ($row) use ($readAllUntil) {
                return self::buildItem(
                    'resign',
                    (int) $row->id,
                    'Pengajuan Resign',
                    self::statusLabel((int) ($row->sts_pengajuan ?? 0)),
                    route('resign.index'),
                    $row->updated_at,
                    $readAllUntil
                );
            });
    }

    private static function exitInterviewItems(int $karyawanId, ?Carbon $readAllUntil)
    {
        return ExitInterviews::query()
            ->whereHas('getPengajuan', function ($q) use ($karyawanId) {
                $q->where('id_karyawan', $karyawanId);
            })
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'id_head', 'approval_key', 'updated_at', 'sts_pengajuan'])
            ->map(function ($row) use ($readAllUntil) {
                return self::buildItem(
                    'exit-interview',
                    (int) $row->id,
                    'Form Exit Interview',
                    self::statusLabel((int) ($row->sts_pengajuan ?? 0)),
                    route('resign.index'),
                    $row->updated_at,
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
        ?Carbon $readAllUntil
    ): array {
        $updated = $updatedAt ? Carbon::parse($updatedAt) : now();
        $isRead = $readAllUntil ? $updated->lessThanOrEqualTo($readAllUntil) : false;
        $itemKey = $module . '-' . $id;

        return [
            'key' => $itemKey,
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

    private static function sessionKey(int $userId): string
    {
        return self::SESSION_KEY_PREFIX . $userId;
    }

    private static function sessionReadItemsKey(int $userId): string
    {
        return self::SESSION_READ_ITEMS_KEY_PREFIX . $userId;
    }

    private static function readItemKeys(int $userId): array
    {
        $keys = session(self::sessionReadItemsKey($userId), []);
        return is_array($keys) ? $keys : [];
    }

}
