# Requirements Document

## Introduction

The Overtime Module enables employees to submit, track, and manage overtime work requests within the Laravel HRD application. The module integrates with the existing HRD system and uses the hrd_lembur database table to store overtime records. Employees can submit overtime requests with date, time, duration, job description, and supporting documentation, while the system enforces business rules for minimum and maximum overtime hours.

## Glossary

- **Overtime_System**: The overtime management module within the Laravel HRD application
- **Employee**: An authenticated user with associated employee (karyawan) record
- **Overtime_Request**: A record in the hrd_lembur table representing an overtime work submission
- **Status_Pengupuan**: The approval status field in hrd_lembur table (null = pending, 1 = approved, 2 = rejected)
- **Submission_Tab**: The interface displaying overtime requests where status_pengupuan is null
- **History_Tab**: The interface displaying overtime requests where status_pengupuan is 1 or 2
- **Total_Hours**: The calculated duration between start time and end time in hours
- **Overtime_Order**: An image file (jpg, jpeg, png) uploaded as supporting documentation
- **Job_Description**: A text description of the work performed during overtime

## Requirements

### Requirement 1: Display Overtime Requests by Status

**User Story:** As an employee, I want to view my overtime requests organized by approval status, so that I can track pending submissions separately from completed requests.

#### Acceptance Criteria

1. THE Overtime_System SHALL display a two-tab interface on the overtime index page
2. THE Submission_Tab SHALL display Overtime_Requests where status_pengupuan equals null
3. THE History_Tab SHALL display Overtime_Requests where status_pengupuan equals 1 OR status_pengupuan equals 2
4. THE Overtime_System SHALL order Overtime_Requests by submission date in descending order within each tab
5. THE Overtime_System SHALL display only Overtime_Requests belonging to the authenticated Employee

### Requirement 2: Submit Overtime Request

**User Story:** As an employee, I want to submit an overtime request with all required details, so that my overtime work can be recorded and approved.

#### Acceptance Criteria

1. THE Overtime_System SHALL provide a form with fields for overtime date, start time, end time, job description, and overtime order upload
2. WHEN the Employee selects an overtime date, THE Overtime_System SHALL accept dates in date picker format
3. WHEN the Employee selects start time and end time, THE Overtime_System SHALL accept times in HH:MM format
4. THE Overtime_System SHALL calculate Total_Hours automatically from start time and end time
5. THE Overtime_System SHALL display Total_Hours as a read-only field
6. WHEN the Employee uploads an Overtime_Order, THE Overtime_System SHALL accept only jpg, jpeg, or png file formats
7. WHEN the Employee clicks the submit button, THE Overtime_System SHALL display a SweetAlert confirmation dialog
8. WHEN the Employee confirms submission, THE Overtime_System SHALL create an Overtime_Request with status_pengupuan set to null
9. THE Overtime_System SHALL associate the Overtime_Request with the authenticated Employee's id_karyawan

### Requirement 3: Validate Overtime Duration

**User Story:** As an employee, I want the system to validate my overtime hours, so that I submit requests within allowed limits.

#### Acceptance Criteria

1. WHEN Total_Hours is less than 1 hour, THE Overtime_System SHALL reject the submission and display an error message
2. WHEN Total_Hours exceeds 8 hours, THE Overtime_System SHALL reject the submission and display an error message
3. WHEN Total_Hours is between 1 and 8 hours inclusive, THE Overtime_System SHALL allow the submission to proceed
4. THE Overtime_System SHALL validate Total_Hours before creating the Overtime_Request record

### Requirement 4: Edit Pending Overtime Request

**User Story:** As an employee, I want to edit my pending overtime requests, so that I can correct errors before approval.

#### Acceptance Criteria

1. WHEN status_pengupuan equals null, THE Overtime_System SHALL display an edit button for the Overtime_Request
2. WHEN the Employee clicks the edit button, THE Overtime_System SHALL display a form pre-filled with existing Overtime_Request data
3. THE Overtime_System SHALL allow modification of overtime date, start time, end time, job description, and overtime order
4. WHEN the Employee saves changes, THE Overtime_System SHALL recalculate Total_Hours from the updated start time and end time
5. THE Overtime_System SHALL validate the updated Total_Hours against minimum and maximum limits
6. WHEN status_pengupuan equals 1 OR status_pengupuan equals 2, THE Overtime_System SHALL hide the edit button

### Requirement 5: Cancel Pending Overtime Request

**User Story:** As an employee, I want to cancel my pending overtime requests, so that I can withdraw submissions that are no longer needed.

#### Acceptance Criteria

1. WHEN status_pengupuan equals null, THE Overtime_System SHALL display a cancel button for the Overtime_Request
2. WHEN the Employee clicks the cancel button, THE Overtime_System SHALL display a SweetAlert confirmation dialog
3. WHEN the Employee confirms cancellation, THE Overtime_System SHALL delete the Overtime_Request from the hrd_lembur table
4. WHEN status_pengupuan equals 1 OR status_pengupuan equals 2, THE Overtime_System SHALL hide the cancel button

### Requirement 6: Calculate Overtime Duration

**User Story:** As an employee, I want the system to automatically calculate overtime hours, so that I don't need to manually compute the duration.

#### Acceptance Criteria

1. WHEN the Employee enters start time and end time, THE Overtime_System SHALL calculate Total_Hours as the difference in hours
2. THE Overtime_System SHALL support time calculations across the same day
3. THE Overtime_System SHALL display Total_Hours with decimal precision to two places
4. WHEN start time or end time changes, THE Overtime_System SHALL recalculate Total_Hours immediately
5. WHEN end time is earlier than or equal to start time, THE Overtime_System SHALL display an error message

### Requirement 7: Upload Overtime Supporting Document

**User Story:** As an employee, I want to upload an overtime order document, so that I can provide evidence of authorized overtime work.

#### Acceptance Criteria

1. THE Overtime_System SHALL provide a file upload input for Overtime_Order
2. WHEN the Employee selects a file, THE Overtime_System SHALL validate the file extension is jpg, jpeg, or png
3. WHEN the file extension is invalid, THE Overtime_System SHALL reject the upload and display an error message
4. WHEN the file is valid, THE Overtime_System SHALL store the file in the application storage
5. THE Overtime_System SHALL save the file path reference in the Overtime_Request record
6. WHEN editing an Overtime_Request, THE Overtime_System SHALL allow replacing the existing Overtime_Order

### Requirement 8: Display Overtime Request Details

**User Story:** As an employee, I want to view complete details of my overtime requests, so that I can review submitted information.

#### Acceptance Criteria

1. THE Overtime_System SHALL display overtime date, start time, end time, Total_Hours, Job_Description, and approval status for each Overtime_Request
2. WHEN the Overtime_Request has an Overtime_Order, THE Overtime_System SHALL display a link or thumbnail to view the uploaded image
3. WHEN status_pengupuan equals null, THE Overtime_System SHALL display "Pending" as the status
4. WHEN status_pengupuan equals 1, THE Overtime_System SHALL display "Approved" as the status
5. WHEN status_pengupuan equals 2, THE Overtime_System SHALL display "Rejected" as the status

### Requirement 9: Integrate with Existing HRD Database

**User Story:** As a system administrator, I want the overtime module to use the existing hrd_lembur table, so that it integrates seamlessly with the current database schema.

#### Acceptance Criteria

1. THE Overtime_System SHALL store all Overtime_Requests in the hrd_lembur database table
2. THE Overtime_System SHALL use status_pengupuan field to track approval status
3. THE Overtime_System SHALL set status_pengupuan to null for new submissions
4. THE Overtime_System SHALL read status_pengupuan values 1 and 2 to identify approved and rejected requests
5. THE Overtime_System SHALL maintain compatibility with existing hrd_lembur table structure

### Requirement 10: Render Overtime Interface

**User Story:** As an employee, I want to access the overtime module through a dedicated page, so that I can manage my overtime requests in one place.

#### Acceptance Criteria

1. THE Overtime_System SHALL render the overtime interface using resources/views/overtime/index.blade.php
2. THE Overtime_System SHALL use Blade template syntax consistent with existing HRD views
3. THE Overtime_System SHALL include navigation tabs for Submission and History views
4. THE Overtime_System SHALL integrate with the application's existing layout and styling
5. THE Overtime_System SHALL display success and error messages using the application's notification system
