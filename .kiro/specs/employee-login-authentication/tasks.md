# Implementation Plan: Employee Login Authentication

## Overview

This implementation plan creates a session-based authentication system for employee self-service access using Laravel 12.0's built-in authentication framework. The system authenticates employees using their nik (employee ID) and password credentials. The implementation leverages Laravel's Auth facade, session guard, Eloquent User model, and middleware for route protection.

## Tasks

- [x] 1. Set up authentication infrastructure
  - Create AuthController with showLoginForm, login, and logout methods
  - Configure routes for login (GET/POST) and logout (POST)
  - Apply guest middleware to login routes and auth middleware to logout route
  - _Requirements: 1.1, 1.2, 5.1, 7.2_

- [-] 2. Implement login functionality
  - [x] 2.1 Implement login validation and authentication logic
    - Add validation rules for nik (required|string) and password (required|string)
    - Implement Auth::attempt() with nik as username field
    - Handle authentication success with session regeneration and redirect to dashboard
    - Handle authentication failure with error message return
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 3.1, 6.1_

  - [x] 2.2 Write property test for valid credentials authentication
    - **Property 1: Valid credentials authenticate successfully**
    - **Validates: Requirements 1.1, 1.2**

  - [ ] 2.3 Write property test for invalid credentials rejection
    - **Property 2: Invalid credentials are rejected**
    - **Validates: Requirements 1.3**

  - [ ] 2.4 Write property test for authentication failure error messages
    - **Property 3: Authentication failure returns error message**
    - **Validates: Requirements 1.4**

  - [ ] 2.5 Write property test for empty nik validation
    - **Property 4: Empty nik validation**
    - **Validates: Requirements 1.5**

  - [ ] 2.6 Write property test for empty password validation
    - **Property 5: Empty password validation**
    - **Validates: Requirements 1.6**

  - [ ] 2.7 Write property test for field-specific validation errors
    - **Property 6: Validation errors are field-specific**
    - **Validates: Requirements 1.7, 7.3**

  - [ ] 2.8 Write unit tests for login functionality
    - Test login with valid credentials (specific example)
    - Test login with invalid nik (specific example)
    - Test login with invalid password (specific example)
    - Test login with empty nik field
    - Test login with empty password field
    - Test CSRF protection on login endpoint
    - _Requirements: 1.1, 1.3, 1.4, 1.5, 1.6, 6.4_

- [ ] 3. Implement remember me functionality
  - [x] 3.1 Add remember me checkbox handling in login method
    - Accept optional remember parameter from request
    - Pass remember parameter to Auth::attempt() method
    - Ensure remember_token is generated and stored on successful authentication
    - _Requirements: 2.1, 2.2, 2.3_

  - [ ] 3.2 Write property test for remember token creation
    - **Property 7: Remember me creates persistent token**
    - **Validates: Requirements 2.1, 2.2**

  - [ ] 3.3 Write property test for remember token authentication
    - **Property 8: Remember token enables automatic authentication**
    - **Validates: Requirements 2.3**

  - [ ] 3.4 Write unit tests for remember me functionality
    - Test login with remember me checked creates token
    - Test login without remember me does not create token
    - Test automatic authentication with valid remember token
    - _Requirements: 2.1, 2.2, 2.3_

- [ ] 4. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [-] 5. Implement logout functionality
  - [x] 5.1 Implement logout method in AuthController
    - Call Auth::logout() to invalidate session
    - Regenerate session token using $request->session()->regenerateToken()
    - Redirect to login page
    - _Requirements: 5.1, 5.2, 5.3, 5.4_

  - [ ] 5.2 Write property test for logout session invalidation
    - **Property 13: Logout invalidates session**
    - **Validates: Requirements 5.1, 5.4**

  - [ ] 5.3 Write property test for logout session regeneration
    - **Property 14: Logout regenerates session token**
    - **Validates: Requirements 5.2**

  - [ ] 5.4 Write property test for logout redirect
    - **Property 15: Logout redirects to login page**
    - **Validates: Requirements 5.3**

  - [ ] 5.5 Write unit tests for logout functionality
    - Test logout invalidates session
    - Test logout redirects to login page
    - Test logout regenerates session token
    - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ] 6. Implement route protection and redirects
  - [ ] 6.1 Configure authentication middleware on protected routes
    - Apply auth middleware to dashboard and all protected routes
    - Configure guest middleware on login routes
    - Set up redirect paths in middleware configuration
    - _Requirements: 3.2, 3.3, 4.1, 4.2_

  - [ ] 6.2 Implement showLoginForm method with authenticated user redirect
    - Check if user is already authenticated
    - Redirect authenticated users to dashboard
    - Render login.blade.php for unauthenticated users
    - _Requirements: 3.2, 7.2_

  - [ ] 6.3 Implement intended URL redirect logic in login method
    - Use redirect()->intended() for post-login redirect
    - Default to dashboard route if no intended URL stored
    - _Requirements: 3.1, 4.3, 4.4_

  - [ ] 6.4 Write property test for successful login redirect
    - **Property 9: Successful login redirects to dashboard**
    - **Validates: Requirements 3.1**

  - [ ] 6.5 Write property test for authenticated user redirect from login
    - **Property 10: Authenticated users redirected from login page**
    - **Validates: Requirements 3.2**

  - [ ] 6.6 Write property test for unauthenticated access redirect
    - **Property 11: Unauthenticated access redirects to login**
    - **Validates: Requirements 3.3, 4.2, 4.3**

  - [ ] 6.7 Write property test for intended URL redirect
    - **Property 12: Post-authentication redirect to intended URL**
    - **Validates: Requirements 4.4**

  - [ ] 6.8 Write unit tests for route protection
    - Test auth middleware protects dashboard route
    - Test guest middleware redirects authenticated users from login
    - Test intended URL is stored when accessing protected route
    - Test redirect to intended URL after authentication
    - _Requirements: 3.2, 3.3, 4.2, 4.3, 4.4_

- [ ] 7. Implement session security measures
  - [ ] 7.1 Add session regeneration on successful login
    - Call $request->session()->regenerate() after Auth::attempt() succeeds
    - Ensure session ID changes to prevent fixation attacks
    - _Requirements: 6.1_

  - [ ] 7.2 Write property test for login session regeneration
    - **Property 16: Login regenerates session ID**
    - **Validates: Requirements 6.1**

  - [ ] 7.3 Write property test for CSRF protection
    - **Property 17: CSRF protection on login**
    - **Validates: Requirements 6.4**

  - [ ] 7.4 Write unit tests for session security
    - Test session ID changes after successful login
    - Test CSRF token validation on login POST
    - _Requirements: 6.1, 6.4_

- [-] 8. Configure User model for nik authentication
  - [x] 8.1 Update User model configuration
    - Add nik to fillable array if not already present
    - Ensure password field uses hashed casting
    - Ensure remember_token field is in hidden array
    - Verify model extends Authenticatable
    - _Requirements: 7.1, 7.5, 7.6_

  - [x] 8.2 Override username method in AuthController
    - Add username() method returning 'nik' to use nik field for authentication
    - Ensure Auth::attempt() uses nik instead of email
    - _Requirements: 1.1, 7.1_

- [-] 9. Adapt login view for nik field
  - [x] 9.1 Update login.blade.php template
    - Change email input field to nik input field
    - Update field labels and placeholders
    - Ensure CSRF token is included in form
    - Ensure remember me checkbox is present
    - Display validation errors for nik and password fields
    - _Requirements: 1.5, 1.6, 1.7, 2.1, 6.4, 7.2, 7.3_

- [ ] 10. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 11. Integration testing and validation
  - [ ] 11.1 Write integration tests for complete authentication flows
    - Test full login to dashboard flow
    - Test intended URL redirect flow (access protected route → login → redirect)
    - Test remember me flow (login with remember → logout → auto-login)
    - Test logout flow (login → logout → verify session cleared)
    - _Requirements: All requirements_

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties across random inputs
- Unit tests validate specific examples and edge cases
- The implementation uses Laravel 12.0's built-in authentication system
- No database schema changes required - uses existing users table
- Session configuration uses existing config/session.php settings
