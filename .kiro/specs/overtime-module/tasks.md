# Implementation Plan: Overtime Module

## Overview

This implementation plan creates an overtime management module for the Laravel HRD application. The module follows established patterns from the Leave and Permission modules, enabling employees to submit, edit, cancel, and track overtime requests. Implementation includes creating the Overtime model, OvertimeController with full CRUD operations, Blade views for the interface, and comprehensive validation for business rules (1-8 hour duration limits, file upload validation).

## Tasks

- [x] 1. Create Overtime model and configure database mapping
  - Create `app/Models/Overtime.php` with Eloquent model
  - Map to `hrd_lembur` table with correct primary key
  - Define fillable fields: id_karyawan, tgl_lembur, jam_mulai, jam_selesai, total_jam, keterangan_pekerjaan, file_surat_lembur, tgl_pengajuan, status_pengupuan
  - Add date casting for tgl_lembur and tgl_pengajuan
  - Define belongsTo relationship to Karyawan model
  - _Requirements: 9.1, 9.2_

- [ ]* 1.1 Write property test for Overtime model relationships
  - **Property 8: Employee Data Isolation**
  - **Validates: Requirements 1.5**

- [ ] 2. Create OvertimeController with index and display methods
  - [x] 2.1 Create `app/Http/Controllers/OvertimeController.php`
    - Implement index() method to retrieve pending and history overtime requests
    - Filter pending requests where status_pengupuan = null
    - Filter history where status_pengupuan IN (1, 2)
    - Order both collections by tgl_pengajuan descending
    - Ensure queries filter by authenticated employee's id_karyawan
    - Return view with pendingRequests and history data
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

  - [ ]* 2.2 Write property tests for filtering and ordering
    - **Property 4: Submission Tab Filtering**
    - **Validates: Requirements 1.2**
    - **Property 5: History Tab Filtering**
    - **Validates: Requirements 1.3**
    - **Property 12: Request Ordering**
    - **Validates: Requirements 1.4**

  - [x] 2.3 Implement show() method for overtime request details
    - Accept overtime request ID parameter
    - Query overtime with id_karyawan check for authorization
    - Use firstOrFail() to return 404 if not found or unauthorized
    - Return JSON response with formatted overtime data
    - Map status_pengupuan to display text (null="Pending", 1="Approved", 2="Rejected")
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

  - [ ]* 2.4 Write property test for status display mapping
    - **Property 10: Status Display Mapping**
    - **Validates: Requirements 8.3, 8.4, 8.5**

- [ ] 3. Implement overtime submission functionality
  - [x] 3.1 Implement create() method in OvertimeController
    - Check authenticated user has associated karyawan record
    - Return error if karyawan not found
    - Return create view
    - _Requirements: 2.1_

  - [-] 3.2 Implement store() method with validation and business rules
    - Validate tgl_lembur as required date
    - Validate jam_mulai as required time in H:i format
    - Validate jam_selesai as required time in H:i format and after jam_mulai
    - Validate keterangan_pekerjaan as required string max 1000 characters
    - Validate file_surat_lembur as required file, mimes:jpg,jpeg,png, max:2048KB
    - Calculate total_jam from jam_mulai and jam_selesai
    - Validate total_jam >= 1 and <= 8 hours
    - Handle file upload to storage and get file path
    - Create Overtime record with status_pengupuan = null
    - Set tgl_pengajuan to current date
    - Associate with authenticated employee's id_karyawan
    - Redirect to overtime.index with success message
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9, 3.1, 3.2, 3.3, 3.4, 6.1, 6.2, 6.3, 6.4, 6.5, 7.1, 7.2, 7.3, 7.4, 7.5_

  - [ ]* 3.3 Write property tests for duration calculation and validation
    - **Property 1: Overtime Duration Calculation**
    - **Validates: Requirements 2.4, 4.4, 6.1, 6.3**
    - **Property 2: Duration Validation Range**
    - **Validates: Requirements 3.1, 3.2, 3.3, 4.5**
    - **Property 11: Time Range Validation**
    - **Validates: Requirements 6.5**

  - [ ]* 3.4 Write property test for file format validation
    - **Property 3: File Format Validation**
    - **Validates: Requirements 2.6, 7.2, 7.3**

  - [ ]* 3.5 Write property test for submission initialization
    - **Property 9: Submission Status Initialization**
    - **Validates: Requirements 2.8, 2.9, 9.3**

  - [ ]* 3.6 Write unit tests for store() method
    - Test successful overtime submission with valid data
    - Test rejection when total_jam < 1 hour
    - Test rejection when total_jam > 8 hours
    - Test rejection with invalid file format
    - Test error when karyawan not found

- [ ] 4. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 5. Implement overtime edit functionality
  - [ ] 5.1 Implement edit() method in OvertimeController
    - Accept overtime request ID parameter
    - Query overtime with id_karyawan check for authorization
    - Use firstOrFail() to return 404 if not found or unauthorized
    - Check status_pengupuan = null, redirect with error if not pending
    - Return edit view with overtime data pre-filled
    - _Requirements: 4.1, 4.2_

  - [ ]* 5.2 Write property test for edit form pre-population
    - **Property 14: Edit Form Pre-population**
    - **Validates: Requirements 4.2**

  - [ ] 5.3 Implement update() method with validation
    - Accept overtime request ID parameter
    - Query overtime with id_karyawan check for authorization
    - Check status_pengupuan = null, reject if not pending
    - Validate all fields same as store() method
    - Recalculate total_jam from updated jam_mulai and jam_selesai
    - Validate updated total_jam >= 1 and <= 8 hours
    - Handle file upload if new file provided (replace old file)
    - Update Overtime record
    - Redirect to overtime.index with success message
    - _Requirements: 4.3, 4.4, 4.5, 4.6, 7.6_

  - [ ]* 5.4 Write property test for file replacement on edit
    - **Property 16: File Replacement on Edit**
    - **Validates: Requirements 7.6**

  - [ ]* 5.5 Write unit tests for edit and update methods
    - Test successful update of pending overtime
    - Test rejection when editing non-pending overtime
    - Test unauthorized edit attempt (different employee)
    - Test file replacement during edit

- [ ] 6. Implement overtime cancellation functionality
  - [ ] 6.1 Implement destroy() method in OvertimeController
    - Accept overtime request ID parameter
    - Query overtime with id_karyawan check for authorization
    - Use firstOrFail() to return 404 if not found or unauthorized
    - Check status_pengupuan = null, redirect with error if not pending
    - Delete associated file from storage if exists
    - Delete Overtime record from database
    - Redirect to overtime.index with success message
    - _Requirements: 5.1, 5.2, 5.3, 5.4_

  - [ ]* 6.2 Write property test for cancellation deletion
    - **Property 13: Cancellation Deletion**
    - **Validates: Requirements 5.3**

  - [ ]* 6.3 Write unit tests for destroy method
    - Test successful cancellation of pending overtime
    - Test rejection when cancelling non-pending overtime
    - Test unauthorized cancellation attempt
    - Test file deletion on cancellation

- [ ] 7. Create helper methods for business logic
  - [ ] 7.1 Add private calculateTotalHours() method to OvertimeController
    - Accept start_time and end_time as parameters (H:i format)
    - Parse times using Carbon
    - Calculate difference in hours with decimal precision
    - Return total hours rounded to 2 decimal places
    - _Requirements: 6.1, 6.3_

  - [ ] 7.2 Add private validateDuration() method to OvertimeController
    - Accept total_hours parameter
    - Return true if total_hours >= 1 and <= 8
    - Return false otherwise
    - _Requirements: 3.1, 3.2, 3.3_

  - [ ] 7.3 Add private handleFileUpload() method to OvertimeController
    - Accept UploadedFile parameter
    - Store file in 'overtime_orders' directory using Storage facade
    - Return stored file path
    - _Requirements: 7.4, 7.5_

  - [ ]* 7.4 Write property test for file storage persistence
    - **Property 15: File Storage Persistence**
    - **Validates: Requirements 7.4, 7.5**

- [ ] 8. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 9. Create Blade view for overtime index page
  - [ ] 9.1 Create `resources/views/overtime/index.blade.php`
    - Extend application layout (consistent with leave/permission views)
    - Create two-tab interface using Bootstrap tabs
    - Tab 1: "Submission" for pending requests
    - Tab 2: "History" for approved/rejected requests
    - _Requirements: 1.1, 10.1, 10.2, 10.3, 10.4_

  - [ ] 9.2 Implement Submission tab content
    - Display data table with columns: Date, Start Time, End Time, Total Hours, Status, Actions
    - Loop through $pendingRequests collection
    - Display Edit and Cancel buttons for each row
    - Add SweetAlert confirmation for cancel action
    - Show "No pending requests" message if collection empty
    - _Requirements: 1.2, 4.1, 5.1, 5.2_

  - [ ]* 9.3 Write property tests for button visibility
    - **Property 6: Pending Request Action Buttons**
    - **Validates: Requirements 4.1, 5.1**

  - [ ] 9.3 Implement History tab content
    - Display data table with columns: Date, Start Time, End Time, Total Hours, Status
    - Loop through $history collection
    - Display status badge (Approved=green, Rejected=red)
    - Show "No history records" message if collection empty
    - Do not display Edit or Cancel buttons
    - _Requirements: 1.3, 4.6, 5.4_

  - [ ]* 9.4 Write property test for processed request buttons
    - **Property 7: Processed Request Action Buttons**
    - **Validates: Requirements 4.6, 5.4**

  - [ ] 9.5 Add modal for viewing overtime details
    - Create Bootstrap modal triggered by clicking row or view icon
    - Display all overtime fields including job description
    - Show overtime order image thumbnail with link to full image
    - Display formatted dates and times
    - _Requirements: 8.1, 8.2_

  - [ ]* 9.6 Write property test for file link display
    - **Property 17: File Link Display**
    - **Validates: Requirements 8.2**

- [ ] 10. Create Blade view for overtime submission form
  - [ ] 10.1 Create `resources/views/overtime/create.blade.php`
    - Extend application layout
    - Create form with POST method to overtime.store route
    - Add CSRF token field
    - _Requirements: 2.1, 10.1, 10.2_

  - [ ] 10.2 Add form fields for overtime submission
    - Date picker input for tgl_lembur (required)
    - Time input for jam_mulai in HH:MM format (required)
    - Time input for jam_selesai in HH:MM format (required)
    - Read-only text input for total_jam (auto-calculated)
    - Textarea for keterangan_pekerjaan (required, max 1000 chars)
    - File upload input for file_surat_lembur (required, accept jpg/jpeg/png)
    - Display validation errors using Laravel error bag
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6_

  - [ ] 10.3 Add JavaScript for automatic duration calculation
    - Listen to change events on jam_mulai and jam_selesai inputs
    - Calculate time difference when both fields have values
    - Update total_jam field with calculated hours (2 decimal places)
    - Display error message if end time <= start time
    - _Requirements: 6.1, 6.3, 6.4, 6.5_

  - [ ]* 10.4 Write property test for time format acceptance
    - **Property 18: Time Format Acceptance**
    - **Validates: Requirements 2.3**

  - [ ] 10.5 Add submit button with SweetAlert confirmation
    - Create submit button
    - Attach SweetAlert confirmation dialog on click
    - Submit form only after user confirms
    - _Requirements: 2.7_

- [ ] 11. Create Blade view for overtime edit form
  - [ ] 11.1 Create `resources/views/overtime/edit.blade.php`
    - Extend application layout
    - Create form with PUT method to overtime.update route
    - Add CSRF token and method spoofing fields
    - Pre-fill all form fields with $overtime data
    - _Requirements: 4.2, 10.1, 10.2_

  - [ ] 11.2 Reuse form fields from create view
    - Include same fields as create form
    - Pre-populate tgl_lembur, jam_mulai, jam_selesai, keterangan_pekerjaan
    - Display current overtime order filename or thumbnail
    - Make file upload optional (only if replacing)
    - Include same JavaScript for duration calculation
    - _Requirements: 4.3, 7.6_

  - [ ] 11.3 Add update button with confirmation
    - Create update button
    - Attach SweetAlert confirmation dialog
    - Submit form after confirmation
    - _Requirements: 4.3_

- [ ] 12. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 13. Register routes for overtime module
  - [ ] 13.1 Add overtime routes to `routes/web.php`
    - Wrap routes in auth middleware group
    - Register resource route: Route::resource('overtime', OvertimeController::class)
    - This creates routes for index, create, store, show, edit, update, destroy
    - _Requirements: 10.1_

  - [ ]* 13.2 Write unit tests for route protection
    - Test unauthenticated access redirects to login
    - Test authenticated access succeeds
    - Test authorization checks in controller methods

- [ ] 14. Add navigation menu item for overtime module
  - [ ] 14.1 Update main navigation layout
    - Add "Overtime" menu item to sidebar or top navigation
    - Link to overtime.index route
    - Use icon consistent with Leave and Permission menu items
    - Position near other HRD module menu items
    - _Requirements: 10.4_

- [ ] 15. Integrate with existing HRD constants and helpers
  - [ ] 15.1 Add overtime status constants to HrdConstants helper
    - Define STATUS_LEMBUR array in `app/Helpers/HrdConstants.php`
    - Map null => 'Pending', 1 => 'Approved', 2 => 'Rejected'
    - Use constants in views and controller for status display
    - _Requirements: 8.3, 8.4, 8.5_

  - [ ] 15.2 Add file upload helper if needed
    - Check if HrdFunction helper has file upload utilities
    - If not, create helper method for consistent file handling
    - Ensure file naming convention matches existing modules
    - _Requirements: 7.4, 7.5_

- [ ] 16. Final integration and testing
  - [ ] 16.1 Test complete overtime workflow end-to-end
    - Submit new overtime request
    - Verify appears in Submission tab
    - Edit pending request
    - Cancel pending request
    - Verify all validation rules work correctly
    - _Requirements: All_

  - [ ]* 16.2 Write integration tests for complete workflows
    - Test full submission workflow
    - Test full edit workflow
    - Test full cancellation workflow
    - Test error handling for all edge cases

  - [ ] 16.3 Verify UI consistency with existing modules
    - Check styling matches Leave and Permission modules
    - Verify responsive design works on mobile
    - Test SweetAlert confirmations
    - Validate error message display
    - _Requirements: 10.2, 10.4, 10.5_

- [ ] 17. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional testing tasks and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation at key milestones
- Property tests validate universal correctness properties across random inputs
- Unit tests validate specific examples, edge cases, and integration points
- The implementation follows Laravel best practices and existing HRD module patterns
- File uploads use Laravel's Storage facade for consistent file management
- All database operations use Eloquent ORM for type safety and relationship management
