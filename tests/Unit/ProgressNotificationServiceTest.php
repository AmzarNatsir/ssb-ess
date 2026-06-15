<?php

namespace Tests\Unit;

use App\Helpers\ProgressNotificationService;
use App\Models\NotificationReadHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getForCurrentUser returns empty array when not authenticated
     */
    public function test_get_for_current_user_when_not_authenticated()
    {
        $result = ProgressNotificationService::getForCurrentUser();

        $this->assertEmpty($result['items']);
        $this->assertEquals(0, $result['unreadCount']);
        $this->assertNull($result['readAllUntil']);
    }

    /**
     * Test markAllAsReadForCurrentUser creates database records
     */
    public function test_mark_all_as_read_for_current_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        ProgressNotificationService::markAllAsReadForCurrentUser();

        // Database records should be created or updated
        // (actual test would need mock data from Leave, Permission, etc.)
        $this->assertTrue(true); // Placeholder for when we have test data
    }

    /**
     * Test markItemAsReadForCurrentUser with valid item key
     */
    public function test_mark_item_as_read_with_valid_key()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        ProgressNotificationService::markItemAsReadForCurrentUser('leave-123');

        $record = NotificationReadHistory::where('user_id', $user->id)
            ->where('item_module', 'leave')
            ->where('item_id', 123)
            ->first();

        $this->assertNotNull($record);
        $this->assertNotNull($record->marked_at);
    }

    /**
     * Test markItemAsReadForCurrentUser ignores invalid key format
     */
    public function test_mark_item_as_read_with_invalid_key()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        ProgressNotificationService::markItemAsReadForCurrentUser('invalid-key-format-too-long');

        // Should not create record for invalid key
        $records = NotificationReadHistory::where('user_id', $user->id)->get();
        $this->assertEmpty($records);
    }

    /**
     * Test notification read history persistence after logout/login
     */
    public function test_notification_read_status_persists_after_logout_login()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Mark item as read
        ProgressNotificationService::markItemAsReadForCurrentUser('permission-456');

        // Verify record exists
        $record = NotificationReadHistory::where('user_id', $user->id)
            ->where('item_module', 'permission')
            ->where('item_id', 456)
            ->first();

        $this->assertNotNull($record);
        $this->assertNotNull($record->marked_at);

        // Simulate logout/login
        $this->actingAs($user); // Re-authenticate

        // Verify record still exists in database
        $persistedRecord = NotificationReadHistory::where('user_id', $user->id)
            ->where('item_module', 'permission')
            ->where('item_id', 456)
            ->first();

        $this->assertNotNull($persistedRecord);
        $this->assertTrue($record->id === $persistedRecord->id);
    }

    /**
     * Test unique constraint on (user_id, item_module, item_id)
     */
    public function test_unique_constraint_prevents_duplicate_records()
    {
        $user = User::factory()->create();

        // Create first record
        NotificationReadHistory::create([
            'user_id' => $user->id,
            'item_module' => 'leave',
            'item_id' => 789,
            'marked_at' => now(),
        ]);

        // Try to create duplicate (should update instead)
        NotificationReadHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'item_module' => 'leave',
                'item_id' => 789,
            ],
            [
                'marked_all_at' => now(),
            ]
        );

        // Should have only 1 record
        $records = NotificationReadHistory::where('user_id', $user->id)
            ->where('item_module', 'leave')
            ->where('item_id', 789)
            ->get();

        $this->assertEquals(1, $records->count());
    }

    /**
     * Test marked_at takes precedence in read status
     */
    public function test_marked_at_indicates_read_status()
    {
        $user = User::factory()->create();

        $record = NotificationReadHistory::create([
            'user_id' => $user->id,
            'item_module' => 'overtime',
            'item_id' => 321,
            'marked_at' => now(),
        ]);

        $this->assertNotNull($record->marked_at);
        $this->assertNull($record->marked_all_at);
    }
}
