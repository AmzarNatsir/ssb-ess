# Design Document: Overtime Module

## Overview

The Overtime Module extends the existing Laravel HRD application to enable employees to submit, track, and manage overtime work requests. The module follows the established patterns from the Leave and Permission modules, integrating with the existing `hrd_lembur` database table and leveraging the application's approval workflow system.

The module provides a two-tab interface (Submission and History) where employees can:
- Submit new overtime requests with date, time range, job description, and supporting documentation
- Edit pending requests before approval
- Cancel pending requests
- View approval history

The system enforces business rules including minimum (1 hour) and maximum (8 hours) overtime duration limits, validates file uploads (jpg, jpeg, png), and automatically calculates overtime duration from start and end times.

## Architecture

### High-Level Architecture

The Overtime Module follows Laravel's MVC architecture pattern, consistent with existing HRD modules:

```
┌─────────────────┐
│  Blade Views    │ ← resources/views/overtime/
│  (UI Layer)     │   - index.blade.php (main interface)
│                 │   - create.blade.php (submission form)
│                 │   - edit.blade.php (edit form)
└────────┬────────┘
         │
         ↓
┌─────────────────┐
│   Controller    │ ← app/Http/Controllers/OvertimeController.php
│   (Logic Layer) │   - Request handling
│                 │   - Validation
│                 │   - Business rules
└────────┬────────┘
         │
         ↓
┌─────────────────┐
│     Model       │ ← app/Models/Overtime.php
│  (Data Layer)   │   - Eloquent ORM
│                 │   - Relationships
└────────┬────────┘
         │
         ↓
┌─────────────────┐
│    Database     │ ← hrd_lembur table
│                 │   - Overtime records
└─────────────────┘
```

### Integration Points

1. **Authentication System**: Uses Laravel's Auth facade to identify the current user and associated employee (karyawan)
2. **File Storage**: Integrates with Laravel's Storage facade for overtime order image uploads
3. **Database**: Uses existing `hrd_lembur` table structure
4. **UI Framework**: Leverages existing Bootstrap and SweetAlert implementations
5. **Approval System**: Integrates with HrdFunction helpers and Approval model for future approval workflow

### Design Patterns

- **Repository Pattern**: Eloquent models serve as repositories for data access
- **MVC Pattern**: Clear separation between views, controllers, and models
- **Service Layer**: Business logic encapsulated in controller methods
- **Form Request Validation**: Laravel validation rules for input sanitization

## Components and Interfaces

### 1. Overtime Model

**File**: `app/Models/Overtime.php`

**Responsibilities**:
- Map to `hrd_lembur` database table
- Define relationships with Karyawan model
- Provide mass-assignable fields
- Handle date casting

**Key Methods**:
- `karyawan()`: BelongsTo relationship to Karyawan model
- Standard Eloquent methods (create, update, delete, find, where)

**Attributes**:
```php
protected $table = 'hrd_lembur';
protected $fillable = [
    'id_karyawan',
    'tgl_lembur',
    'jam_mulai',
    'jam_selesai',
    'total_jam',
    'keterangan_pekerjaan',
    'file_surat_lembur',
    'tgl_pengajuan',
    'status_pengupuan'
];
protected $casts = [
    'tgl_lembur' => 'date',
    'tgl_pengajuan' => 'date'
];
```

### 2. OvertimeController

**File**: `app/Http/Controllers/OvertimeController.php`

**Responsibilities**:
- Handle HTTP requests for overtime operations
- Validate user input
- Enforce business rules
- Manage file uploads
- Return views and JSON responses

**Public Methods**:

| Method | Route | Purpose |
|--------|-------|---------|
| `index()` | GET /overtime | Display submission and history tabs |
| `create()` | GET /overtime/create | Show overtime submission form |
| `store(Request)` | POST /overtime | Create new overtime request |
| `edit($id)` | GET /overtime/{id}/edit | Show edit form for pending request |
| `update(Request, $id)` | PUT /overtime/{id} | Update pending overtime request |
| `destroy($id)` | DELETE /overtime/{id} | Cancel/delete pending request |
| `show($id)` | GET /overtime/{id} | Get overtime request details (JSON) |

**Private Helper Methods**:
- `calculateTotalHours($start_time, $end_time)`: Calculate duration between times
- `validateDuration($total_hours)`: Check if duration is within 1-8 hour range
- `handleFileUpload($file)`: Process and store overtime order image

### 3. Blade Views

#### index.blade.php
**File**: `resources/views/overtime/index.blade.php`

**Purpose**: Main interface with two tabs

**Components**:
- Navigation tabs (Submission/History)
- Data tables for each tab
- Action buttons (Edit, Cancel, View Details)
- SweetAlert integration for confirmations

**Data Requirements**:
- `$pendingRequests`: Collection of overtime requests with status_pengupuan = null
- `$history`: Collection of overtime requests with status_pengupuan IN (1, 2)

#### create.blade.php
**File**: `resources/views/overtime/create.blade.php`

**Purpose**: Overtime submission form

**Form Fields**:
- Date picker for overtime date
- Time inputs for start time and end time (HH:MM format)
- Read-only calculated total hours field
- Textarea for job description
- File upload for overtime order (jpg, jpeg, png)
- Submit button with SweetAlert confirmation

#### edit.blade.php
**File**: `resources/views/overtime/edit.blade.php`

**Purpose**: Edit form for pending overtime requests

**Features**:
- Pre-filled form with existing data
- Same validation as create form
- Option to replace uploaded overtime order
- Update button with confirmation

### 4. Routes

**File**: `routes/web.php`

```php
Route::middleware(['auth'])->group(function () {
    Route::resource('overtime', OvertimeController::class);
});
```

### 5. Validation Rules

**Overtime Submission/Update Validation**:
```php
[
    'tgl_lembur' => 'required|date',
    'jam_mulai' => 'required|date_format:H:i',
    'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
    'keterangan_pekerjaan' => 'required|string|max:1000',
    'file_surat_lembur' => 'required|file|mimes:jpg,jpeg,png|max:2048'
]
```

**Business Rule Validation**:
- Total hours >= 1 hour
- Total hours <= 8 hours
- End time > Start time

## Data Models

### Overtime (hrd_lembur table)

**Table**: `hrd_lembur`

**Columns**:

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | INT | NO | Primary key |
| id_karyawan | INT | NO | Foreign key to hrd_karyawan |
| tgl_lembur | DATE | NO | Overtime date |
| jam_mulai | TIME | NO | Start time (HH:MM:SS) |
| jam_selesai | TIME | NO | End time (HH:MM:SS) |
| total_jam | DECIMAL(4,2) | NO | Calculated duration in hours |
| keterangan_pekerjaan | TEXT | NO | Job description |
| file_surat_lembur | VARCHAR(255) | YES | File path to overtime order image |
| tgl_pengajuan | DATE | NO | Submission date |
| status_pengupuan | INT | YES | Approval status (null=pending, 1=approved, 2=rejected) |
| created_at | TIMESTAMP | YES | Record creation timestamp |
| updated_at | TIMESTAMP | YES | Record update timestamp |

**Relationships**:
- `belongsTo` Karyawan (via id_karyawan)

**Indexes**:
- Primary key on `id`
- Foreign key on `id_karyawan`
- Index on `status_pengupuan` for filtering

### Status Values

**status_pengupuan**:
- `null`: Pending approval (displayed in Submission tab)
- `1`: Approved (displayed in History tab)
- `2`: Rejected (displayed in History tab)

### Data Flow

**Create Overtime Request**:
```
User Input → Validation → Calculate Total Hours → Validate Duration → 
Upload File → Create Record (status_pengupuan = null) → Redirect to Index
```

**Edit Overtime Request**:
```
Load Record → Check status_pengupuan = null → Display Form → 
User Input → Validation → Recalculate Total Hours → Validate Duration → 
Update File (if changed) → Update Record → Redirect to Index
```

**Cancel Overtime Request**:
```
Load Record → Check status_pengupuan = null → Confirm → 
Delete File → Delete Record → Redirect to Index
```



## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

After analyzing all acceptance criteria, I identified the following redundancies:
- Properties 2.4, 4.4, and 6.1 all describe the same calculation behavior → Combined into Property 1
- Properties 3.1, 3.2, 3.3, 4.5 all describe duration validation → Combined into Property 2
- Properties 2.6, 7.2, 7.3 all describe file validation → Combined into Property 3
- Properties 4.1 and 5.1 describe button visibility for pending requests → Combined into Property 6
- Properties 4.6 and 5.4 describe button hiding for processed requests → Combined into Property 7
- Properties 8.3, 8.4, 8.5 describe status mapping → Combined into Property 10

### Property 1: Overtime Duration Calculation

*For any* valid start time and end time on the same day, the calculated total hours should equal the time difference in hours with decimal precision to two places.

**Validates: Requirements 2.4, 4.4, 6.1, 6.3**

### Property 2: Duration Validation Range

*For any* overtime submission or update, the system should accept the request if and only if the total hours is greater than or equal to 1 hour and less than or equal to 8 hours.

**Validates: Requirements 3.1, 3.2, 3.3, 4.5**

### Property 3: File Format Validation

*For any* file upload attempt, the system should accept the file if and only if the file extension is jpg, jpeg, or png.

**Validates: Requirements 2.6, 7.2, 7.3**

### Property 4: Submission Tab Filtering

*For any* collection of overtime requests, the submission tab should display only those requests where status_pengupuan equals null.

**Validates: Requirements 1.2**

### Property 5: History Tab Filtering

*For any* collection of overtime requests, the history tab should display only those requests where status_pengupuan equals 1 or status_pengupuan equals 2.

**Validates: Requirements 1.3**

### Property 6: Pending Request Action Buttons

*For any* overtime request where status_pengupuan equals null, the system should display both edit and cancel buttons.

**Validates: Requirements 4.1, 5.1**

### Property 7: Processed Request Action Buttons

*For any* overtime request where status_pengupuan equals 1 or 2, the system should hide both edit and cancel buttons.

**Validates: Requirements 4.6, 5.4**

### Property 8: Employee Data Isolation

*For any* authenticated employee, all overtime request queries should return only records where id_karyawan matches the employee's ID.

**Validates: Requirements 1.5**

### Property 9: Submission Status Initialization

*For any* newly created overtime request, the status_pengupuan field should be set to null and the id_karyawan should match the authenticated employee's ID.

**Validates: Requirements 2.8, 2.9, 9.3**

### Property 10: Status Display Mapping

*For any* overtime request, the displayed status text should be "Pending" when status_pengupuan is null, "Approved" when status_pengupuan is 1, and "Rejected" when status_pengupuan is 2.

**Validates: Requirements 8.3, 8.4, 8.5**

### Property 11: Time Range Validation

*For any* time input pair, the system should reject the submission if end time is earlier than or equal to start time.

**Validates: Requirements 6.5**

### Property 12: Request Ordering

*For any* collection of overtime requests displayed in either tab, the requests should be ordered by submission date in descending order.

**Validates: Requirements 1.4**

### Property 13: Cancellation Deletion

*For any* pending overtime request (status_pengupuan = null), when cancelled, the request should be deleted from the database.

**Validates: Requirements 5.3**

### Property 14: Edit Form Pre-population

*For any* overtime request being edited, the edit form should contain all existing field values from the database record.

**Validates: Requirements 4.2**

### Property 15: File Storage Persistence

*For any* valid file upload, the file should be stored in application storage and the file path should be saved in the overtime request record.

**Validates: Requirements 7.4, 7.5**

### Property 16: File Replacement on Edit

*For any* overtime request with an existing file, uploading a new file during edit should replace the old file and update the file path reference.

**Validates: Requirements 7.6**

### Property 17: File Link Display

*For any* overtime request with a non-null file_surat_lembur value, the system should display a link or thumbnail to view the uploaded image.

**Validates: Requirements 8.2**

### Property 18: Time Format Acceptance

*For any* time input, the system should accept values in HH:MM format and reject other formats.

**Validates: Requirements 2.3**


## Error Handling

### Validation Errors

**Input Validation Failures**:
- **Trigger**: Invalid form data (missing fields, wrong formats, invalid ranges)
- **Response**: HTTP 302 redirect back to form with error messages
- **User Feedback**: Laravel's validation error bag displayed in Blade views
- **Example**: "The end time must be after start time"

**Duration Validation Failures**:
- **Trigger**: Total hours < 1 or > 8
- **Response**: Reject submission with error message
- **User Feedback**: "Overtime duration must be between 1 and 8 hours"
- **Recovery**: User adjusts time range and resubmits

**File Upload Validation Failures**:
- **Trigger**: Invalid file type or size
- **Response**: Reject upload with error message
- **User Feedback**: "Only jpg, jpeg, and png files are allowed" or "File size must not exceed 2MB"
- **Recovery**: User selects valid file and resubmits

### Authorization Errors

**Unauthorized Access**:
- **Trigger**: Unauthenticated user attempts to access overtime routes
- **Response**: HTTP 302 redirect to login page
- **User Feedback**: "Please log in to continue"
- **Recovery**: User authenticates and retries

**Employee Data Not Found**:
- **Trigger**: Authenticated user has no associated karyawan record
- **Response**: HTTP 302 redirect with error message
- **User Feedback**: "Employee data not found"
- **Recovery**: Contact system administrator

**Unauthorized Edit/Cancel**:
- **Trigger**: User attempts to edit/cancel another employee's request
- **Response**: HTTP 404 Not Found (via firstOrFail with id_karyawan check)
- **User Feedback**: "Record not found"
- **Recovery**: None (security measure)

### Business Logic Errors

**Edit Non-Pending Request**:
- **Trigger**: User attempts to edit request with status_pengupuan != null
- **Response**: HTTP 302 redirect with error message
- **User Feedback**: "Only pending requests can be edited"
- **Recovery**: User can only view the request

**Cancel Non-Pending Request**:
- **Trigger**: User attempts to cancel request with status_pengupuan != null
- **Response**: HTTP 302 redirect with error message
- **User Feedback**: "Only pending requests can be cancelled"
- **Recovery**: User can only view the request

### System Errors

**Database Connection Failure**:
- **Trigger**: Database unavailable or connection timeout
- **Response**: HTTP 500 Internal Server Error
- **User Feedback**: "An error occurred. Please try again later"
- **Logging**: Exception logged to Laravel log file
- **Recovery**: Retry after database is restored

**File Storage Failure**:
- **Trigger**: Disk full or permission issues
- **Response**: HTTP 500 Internal Server Error
- **User Feedback**: "Failed to upload file. Please try again"
- **Logging**: Exception logged with storage details
- **Recovery**: System administrator resolves storage issue

**Model Not Found**:
- **Trigger**: Request ID doesn't exist or belongs to another employee
- **Response**: HTTP 404 Not Found
- **User Feedback**: "Record not found"
- **Recovery**: User returns to index page

### Error Handling Strategy

1. **Validation Layer**: Use Laravel's Form Request validation for input sanitization
2. **Authorization Layer**: Middleware for authentication, controller checks for ownership
3. **Business Logic Layer**: Explicit status checks before edit/cancel operations
4. **Data Layer**: Use Eloquent's firstOrFail() for automatic 404 responses
5. **Exception Handling**: Try-catch blocks for file operations and database transactions
6. **User Feedback**: Flash messages for redirects, JSON responses for AJAX
7. **Logging**: Log all exceptions with context for debugging

## Testing Strategy

### Dual Testing Approach

The Overtime Module will employ both unit testing and property-based testing to ensure comprehensive coverage:

- **Unit Tests**: Verify specific examples, edge cases, error conditions, and integration points
- **Property Tests**: Verify universal properties across all inputs using randomized test data

Both approaches are complementary and necessary for comprehensive correctness validation.

### Unit Testing

**Focus Areas**:
- Specific examples demonstrating correct behavior
- Edge cases (e.g., exactly 1 hour, exactly 8 hours, midnight times)
- Error conditions (e.g., invalid file types, unauthorized access)
- Integration points (e.g., authentication, file storage, database)

**Example Unit Tests**:

```php
// Specific example: Valid overtime submission
test('employee can submit valid overtime request', function () {
    $user = User::factory()->create();
    $karyawan = Karyawan::factory()->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)->post('/overtime', [
        'tgl_lembur' => '2024-01-15',
        'jam_mulai' => '18:00',
        'jam_selesai' => '21:00',
        'keterangan_pekerjaan' => 'System maintenance',
        'file_surat_lembur' => UploadedFile::fake()->image('order.jpg')
    ]);
    
    $response->assertRedirect('/overtime');
    $this->assertDatabaseHas('hrd_lembur', [
        'id_karyawan' => $karyawan->id,
        'status_pengupuan' => null
    ]);
});

// Edge case: Exactly 1 hour duration
test('system accepts exactly 1 hour overtime', function () {
    // Test implementation
});

// Error condition: Unauthorized edit attempt
test('employee cannot edit another employees overtime', function () {
    // Test implementation
});
```

**Unit Test Coverage**:
- Controller methods (index, create, store, edit, update, destroy, show)
- Model relationships (karyawan)
- File upload handling
- Authentication and authorization
- Validation rules

### Property-Based Testing

**Configuration**:
- **Library**: Pest with Pest Property Plugin (or PHPUnit with Eris)
- **Iterations**: Minimum 100 iterations per property test
- **Tagging**: Each test references its design document property

**Property Test Implementation**:

Each correctness property will be implemented as a property-based test with the following structure:

```php
// Feature: overtime-module, Property 1: Overtime Duration Calculation
test('calculated total hours equals time difference for any valid times', function () {
    // Generate random start and end times (same day)
    $startHour = rand(0, 22);
    $startMinute = rand(0, 59);
    $endHour = rand($startHour, 23);
    $endMinute = rand(0, 59);
    
    if ($endHour == $startHour) {
        $endMinute = rand($startMinute + 1, 59);
    }
    
    $startTime = sprintf('%02d:%02d', $startHour, $startMinute);
    $endTime = sprintf('%02d:%02d', $endHour, $endMinute);
    
    $controller = new OvertimeController();
    $totalHours = $controller->calculateTotalHours($startTime, $endTime);
    
    $expectedHours = ($endHour * 60 + $endMinute - $startHour * 60 - $startMinute) / 60;
    
    expect($totalHours)->toBe(round($expectedHours, 2));
})->repeat(100);
```

**Property Test Coverage**:

| Property | Test Description | Generators |
|----------|------------------|------------|
| Property 1 | Duration calculation accuracy | Random time pairs |
| Property 2 | Duration validation range | Random hour values (0-24) |
| Property 3 | File format validation | Random file extensions |
| Property 4 | Submission tab filtering | Random status values |
| Property 5 | History tab filtering | Random status values |
| Property 6 | Pending button visibility | Random status values |
| Property 7 | Processed button hiding | Random status values |
| Property 8 | Employee data isolation | Random employee IDs |
| Property 9 | Submission initialization | Random submission data |
| Property 10 | Status display mapping | Random status values |
| Property 11 | Time range validation | Random time pairs |
| Property 12 | Request ordering | Random submission dates |
| Property 13 | Cancellation deletion | Random pending requests |
| Property 14 | Edit form pre-population | Random overtime data |
| Property 15 | File storage persistence | Ra
eration

**Authentication Integration**:
- Test middleware protection on routes
- Test employee data retrieval from authenticated user
- Test authorization checks in controllers

### Test Data Generators

**For Property-Based Tests**:

```php
// Time generator (HH:MM format)
function generateTime(): string {
    return sprintf('%02d:%02d', rand(0, 23), rand(0, 59));
}

// Valid time pair generator (end > start)
function generateValidTimePair(): array {
    $startHour = rand(0, 22);
    $endHour = rand($startHour + 1, 23);
    return [
        'start' => sprintf('%02d:%02d', $startHour, rand(0, 59)),
        'end' => sprintf('%02d:%02d', $endHour, rand(0, 59))
    ];
}

// Duration generator (1-8 hours)
function generateValidDuration(): float {
    return round(rand(100, 800) / 100, 2);
}

// Status generator
function generateStatus(): ?int {
    return [null, 1, 2][rand(0, 2)];
}

// File extension generator
function generateFileExtension(): string {
    $extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc'];
    return $extensions[rand(0, count($extensions) - 1)];
}
```

### Test Execution

**Running Tests**:
```bash
# Run all tests
php artisan test

# Run only unit tests
php artisan test --testsuite=Unit

# Run only feature tests
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

**Continuous Integration**:
- Tests run automatically on every commit
- Property tests run with 100 iterations in CI
- Coverage threshold: minimum 80% code coverage

### Test Organization

```
tests/
├── Unit/
│   ├── Models/
│   │   └── OvertimeTest.php
│   └── Helpers/
│       └── OvertimeCalculationTest.php
├── Feature/
│   ├── Overtime/
│   │   ├── OvertimeSubmissionTest.php
│   │   ├── OvertimeEditTest.php
│   │   ├── OvertimeCancelTest.php
│   │   └── OvertimeDisplayTest.php
│   └── Properties/
│       ├── OvertimeCalculationPropertyTest.php
│       ├── OvertimeValidationPropertyTest.php
│       ├── OvertimeFilteringPropertyTest.php
│       └── OvertimeAuthorizationPropertyTest.php
└── Generators/
    └── OvertimeGenerators.php
```

### Success Criteria

Tests are considered successful when:
1. All unit tests pass with specific examples and edge cases
2. All property tests pass with 100 iterations each
3. Code coverage exceeds 80%
4. No security vulnerabilities in authorization tests
5. All integration points function correctly
6. Error handling behaves as specified
