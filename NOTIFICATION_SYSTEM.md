# Sistem Notifikasi Progress - Dokumentasi Teknis

## Overview
Sistem notifikasi progress tracking pengajuan karyawan telah diubah dari **session-based** menjadi **database-based** untuk memastikan status "read" bersifat persistent dan tetap tersimpan setelah logout/login ulang.

## Arsitektur

### Database Structure
```
notification_read_history table:
├─ id (primary key)
├─ user_id (FK ke users table)
├─ item_module (string: leave, permission, overtime, pinjaman, resign, exit-interview)
├─ item_id (ID dari item di modul terkait)
├─ marked_at (datetime: ketika item individual di-mark as read)
├─ marked_all_at (datetime: ketika "mark all as read" dilakukan)
├─ created_at
└─ updated_at

Unique constraint: (user_id, item_module, item_id)
Indices: user_id, (user_id, marked_all_at)
```

### Alur Sistem

#### 1. Load Notifikasi (`getForCurrentUser()`)
```
1. Get current user & karyawan
2. Fetch dari 6 modul:
   - Leave (hrd_cuti) - sts_pengajuan
   - Permission (hrd_izin) - sts_pengajuan
   - Overtime (hrd_lembur) - status_pengajuan
   - PinjamanKaryawan (hrd_pinjaman_karyawan) - status_pengajuan
   - Resign (hrd_resign) - sts_pengajuan
   - ExitInterviews (hrd_form_exit_interviews) - sts_pengajuan

3. Cek status "read" di database:
   - Query notification_read_history untuk setiap item
   - Item dianggap "read" jika:
     a) Ada record dengan marked_at (individually marked)
     b) Ada record dengan marked_all_at && item.updated_at <= marked_all_at
   
4. Filter hanya unread items
5. Sort by updated_at DESC, limit 20
6. Return array dengan metadata
```

#### 2. Mark All as Read
```
1. Get semua unread items untuk user
2. Insert/Update ke notification_read_history:
   - Set marked_all_at = now()
   - Untuk setiap item yang unread
3. Next load, item dengan updated_at sebelum marked_all_at dianggap read
```

#### 3. Mark Individual Item as Read
```
1. Parse item_key (format: "module-id")
2. Insert/Update ke notification_read_history:
   - Set marked_at = now()
   - Untuk item yang dipilih
3. Item akan langsung dianggap read
```

## Models

### NotificationReadHistory Model
```php
class NotificationReadHistory extends Model
{
    protected $table = 'notification_read_history';
    
    protected $fillable = [
        'user_id',
        'item_module',
        'item_id',
        'marked_at',
        'marked_all_at',
    ];
    
    protected $casts = [
        'marked_at' => 'datetime',
        'marked_all_at' => 'datetime',
    ];
}
```

## Services

### ProgressNotificationService
**Location:** `app/Helpers/ProgressNotificationService.php`

**Public Methods:**
- `getForCurrentUser(): array` - Get unread notifications untuk current user
- `markAllAsReadForCurrentUser(): void` - Mark all unread items as read
- `markItemAsReadForCurrentUser(string $itemKey): void` - Mark single item as read

**Private Methods:**
- `getReadAllUntilForUser(int $userId): ?Carbon` - Get latest "mark all at" timestamp
- `isItemRead(int $userId, string $module, int $itemId, Carbon $updated, ?Carbon $readAllUntil): bool` - Check if item is read
- `parseItemKey(string $itemKey): array` - Parse item key format
- Item fetchers: `leaveItems()`, `permissionItems()`, `overtimeItems()`, `loanItems()`, `resignItems()`, `exitInterviewItems()`

## Controller Integration

### ProgressNotificationController
**Location:** `app/Http/Controllers/ProgressNotificationController.php`

**Actions:**
- `markAllRead(Request $request)` - Handle "Mark all as read" button (POST)
  - Calls: `ProgressNotificationService::markAllAsReadForCurrentUser()`
  - Redirect: back with success message
  
- `markRead(Request $request)` - Handle individual item click (GET)
  - Parameters: `item_key` (required), `redirect_url` (optional)
  - Calls: `ProgressNotificationService::markItemAsReadForCurrentUser()`
  - Redirect: to provided redirect_url or home

## View Integration

### Header Layout
**Location:** `resources/views/layout/partials/header.blade.php`

**Data Binding:**
```php
// AppServiceProvider.php
View::composer('layout.partials.header', function ($view) {
    $view->with('progressNotifications', 
        ProgressNotificationService::getForCurrentUser()
    );
});
```

**Notification Dropdown:**
- Shows badge with unread count
- Lists unread items (max 20)
- "Mark all as read" button (disabled if 0 unread)
- Each item is a clickable link to detail page

## Modules Tracked

| Module | Database Table | Status Field | Controller | Route |
|--------|----------------|--------------|-----------|-------|
| Cuti | hrd_cuti | sts_pengajuan | LeaveController | leave.index |
| Izin | hrd_izin | sts_pengajuan | PermissionController | permission.index |
| Lembur | hrd_lembur | status_pengajuan | OvertimeController | overtime.index |
| Pinjaman | hrd_pinjaman_karyawan | status_pengajuan | PinjamanController | pinjaman.index |
| Resign | hrd_resign | sts_pengajuan | ResignController | resign.index |
| Exit Interview | hrd_form_exit_interviews | sts_pengajuan | ResignController | resign.index |

## Status Values
```
1 = Pengajuan (baru diajukan)
2 = Disetujui (approved)
3 = Ditolak (rejected)
4 = Dibatalkan (cancelled)
Default = Diproses
```

## Migration

**File:** `database/migrations/2026_06_15_000001_create_notification_read_history_table.php`

**Changes:**
- Created `notification_read_history` table
- Unique constraint on (user_id, item_module, item_id)
- Indices untuk query optimization

## Testing

### Manual Testing Checklist
1. ✓ User login
2. ✓ Verify notifikasi muncul di header dropdown
3. ✓ Click "Mark all as read" button
4. ✓ Verify badge count becomes 0
5. ✓ Verify items tidak tampil di dropdown
6. ✓ User logout
7. ✓ User login kembali
8. ✓ Verify notifikasi tetap tidak tampil (data persist)
9. ✓ Create/update item di salah satu modul
10. ✓ Verify notifikasi muncul lagi
11. ✓ Click individual item
12. ✓ Verify redirect ke detail page
13. ✓ Verify item tidak tampil lagi setelah di-mark

### Database Query Testing
```sql
-- Check read history for user
SELECT * FROM notification_read_history 
WHERE user_id = {user_id} 
ORDER BY created_at DESC;

-- Check marked_all_at timestamps
SELECT DISTINCT marked_all_at 
FROM notification_read_history 
WHERE user_id = {user_id} 
AND marked_all_at IS NOT NULL;
```

## Backward Compatibility

**Session Constants Removed:**
- `SESSION_KEY_PREFIX` (previously: 'progress_notif_read_until_user_')
- `SESSION_READ_ITEMS_KEY_PREFIX` (previously: 'progress_notif_read_items_user_')

**Old Session Methods Removed:**
- `sessionKey()`
- `sessionReadItemsKey()`
- `readItemKeys()`

All read status now comes from database exclusively.

## Performance Considerations

1. **Query Optimization:**
   - Each module query limited to 20 items (per modul)
   - Indices on (user_id, marked_all_at) untuk bulk read check
   - Unique constraint prevents duplicate records

2. **Caching:**
   - No explicit caching (can be added if needed)
   - Database queries run on every page load (fast due to indices)

3. **Database Size:**
   - Grows with user × notification interactions
   - Cleanup policy: can archive old records after 90 days if needed

## Future Enhancements

1. Add cleanup job untuk archive old notification history
2. Add admin dashboard untuk view notification statistics
3. Add bulk archive untuk user
4. Add unmark as read feature
5. Add notification preferences per module
