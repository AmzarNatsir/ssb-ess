# Design Document: Employee Login Authentication

## Overview

This design implements a session-based authentication system for employee self-service access using Laravel 12.0's built-in authentication framework. The system authenticates employees using their nik (employee ID) and password credentials against the existing users table.

The implementation leverages Laravel's native authentication components:
- **Auth facade** for credential verification and session management
- **Session guard** for maintaining authenticated state
- **Eloquent User model** for database interaction
- **Middleware** for route protection

The design integrates with existing infrastructure without requiring database schema changes or frontend template modifications. The login.blade.php template will be adapted to use nik instead of email, and form submissions will be processed by a new AuthController.

### Key Design Decisions

1. **Use Laravel's built-in authentication**: Rather than implementing custom authentication logic, we leverage Laravel's battle-tested Auth system for security and maintainability
2. **Minimal database changes**: The existing users table schema remains unchanged; we'll use the email field to store nik values or add a nik column if needed
3. **Session-based authentication**: Using Laravel's session guard (already configured) for stateful authentication appropriate for web applications
4. **Middleware-based protection**: Leveraging Laravel's auth middleware for route protection rather than manual checks

## Architecture

### Component Overview

```
┌─────────────────┐
│  Login Form     │
│ (login.blade.php)│
└────────┬────────┘
         │ POST /login
         ▼
┌─────────────────┐
│ AuthController  │
│  - login()      │
│  - logout()     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Auth Facade    │
│  - attempt()    │
│  - logout()     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Session Guard  │
│  - authenticate │
│  - maintain     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  User Model     │
│  (Eloquent)     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  users table    │
└─────────────────┘
```

### Authentication Flow

1. **Login Request**: Employee submits nik and password via login form
2. **Validation**: AuthController validates input fields (required checks)
3. **Credential Verification**: Auth::attempt() verifies credentials against users table
4. **Session Creation**: On success, Laravel creates authenticated session and regenerates session ID
5. **Redirect**: Employee redirected to dashboard or intended URL
6. **Route Protection**: Middleware checks authentication status on protected routes

### Logout Flow

1. **Logout Request**: Employee clicks logout
2. **Session Invalidation**: Auth::logout() invalidates session
3. **Session Regeneration**: New session token generated to prevent fixation attacks
4. **Redirect**: Employee redirected to login page

## Components and Interfaces

### AuthController

**Responsibility**: Handle HTTP authentication requests and coordinate with Auth system

**Methods**:

```php
class AuthController extends Controller
{
    /**
     * Display the login form
     * Redirects to dashboard if already authenticated
     */
    public function showLoginForm(): View|RedirectResponse
    
    /**
     * Process login attempt
     * Validates credentials and creates authenticated session
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    
    /**
     * Process logout
     * Invalidates session and redirects to login
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
}
```

**Validation Rules**:
- `nik`: required|string
- `password`: required|string
- `remember`: optional|boolean

**Error Responses**:
- Validation errors: Return to login form with field-specific error messages
- Authentication failure: Return to login form with "These credentials do not match our records" message

### User Model

**Responsibility**: Represent employee records and define authentication behavior

The existing User model will be used with minimal modifications:

```php
class User extends Authenticatable
{
    protected $fillable = ['name', 'nik', 'password'];
    
    protected $hidden = ['password', 'remember_token'];
    
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
```

**Authentication Identifier**: The model will use `nik` as the authentication identifier instead of `email`. This requires configuring the authentication system to use the nik field.

### Middleware Configuration

**auth Middleware**: Laravel's built-in Authenticate middleware
- Protects routes requiring authentication
- Redirects unauthenticated users to login page
- Stores intended URL for post-login redirect

**guest Middleware**: Laravel's built-in RedirectIfAuthenticated middleware
- Prevents authenticated users from accessing login/register pages
- Redirects authenticated users to dashboard

### Route Definitions

```php
// Guest routes (unauthenticated only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Protected application routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // All other protected routes...
});
```

## Data Models

### users Table Schema

The existing schema will be used:

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,  -- Will store nik or add separate nik column
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,      -- Bcrypt hashed
    remember_token VARCHAR(100) NULL,    -- For "Remember Me" functionality
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Note**: If the email field is actively used for email purposes, we'll need to add a `nik` column:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('nik')->unique()->after('email');
});
```

### sessions Table Schema

Laravel's session table (already exists):

```sql
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX(user_id),
    INDEX(last_activity)
);
```

### Authentication Configuration

The auth.php config will use these settings:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],
```

To use `nik` instead of `email` for authentication, we'll override the `username()` method in AuthController:

```php
public function username(): string
{
    return 'nik';
}
```


## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

After analyzing all acceptance criteria, I identified the following redundancies:
- Properties 3.3 and 4.2 both test unauthenticated access to protected routes - combined into one property
- Properties 2.2 and 7.6 both test remember_token field usage - covered by remember me properties
- Property 1.2 (session creation) is implied by 1.1 (successful authentication) since Laravel's Auth::attempt() creates the session

The following properties provide comprehensive, non-redundant coverage:

### Property 1: Valid credentials authenticate successfully

*For any* employee record in the users table with a valid nik and password, when those credentials are submitted to the authentication system, the employee should be successfully authenticated and Auth::check() should return true.

**Validates: Requirements 1.1, 1.2**

### Property 2: Invalid credentials are rejected

*For any* credential combination where either the nik does not exist in the users table or the password does not match the stored hash, the authentication attempt should fail and Auth::check() should return false.

**Validates: Requirements 1.3**

### Property 3: Authentication failure returns error message

*For any* failed authentication attempt, the response should contain an error message indicating the credentials were invalid.

**Validates: Requirements 1.4**

### Property 4: Empty nik validation

*For any* login request where the nik field is empty or contains only whitespace, the validation should fail and return a field-specific error for the nik field.

**Validates: Requirements 1.5**

### Property 5: Empty password validation

*For any* login request where the password field is empty or contains only whitespace, the validation should fail and return a field-specific error for the password field.

**Validates: Requirements 1.6**

### Property 6: Validation errors are field-specific

*For any* login request that fails validation, the response should contain error messages associated with the specific fields that failed validation.

**Validates: Requirements 1.7, 7.3**

### Property 7: Remember me creates persistent token

*For any* successful authentication where the remember parameter is true, a remember_token should be generated and stored in the user's database record.

**Validates: Requirements 2.1, 2.2**

### Property 8: Remember token enables automatic authentication

*For any* user with a valid remember_token in the database, when a request is made with that token in the cookie, the user should be automatically authenticated without providing credentials.

**Validates: Requirements 2.3**

### Property 9: Successful login redirects to dashboard

*For any* successful authentication attempt where no intended URL is stored, the response should be a redirect to the dashboard route.

**Validates: Requirements 3.1**

### Property 10: Authenticated users redirected from login page

*For any* request to the login page by an already authenticated user, the response should be a redirect to the dashboard route.

**Validates: Requirements 3.2**

### Property 11: Unauthenticated access redirects to login

*For any* request to a protected route by an unauthenticated user, the response should be a redirect to the login page and the intended URL should be stored in the session.

**Validates: Requirements 3.3, 4.2, 4.3**

### Property 12: Post-authentication redirect to intended URL

*For any* authentication that occurs after an unauthenticated user attempted to access a protected route, the post-login redirect should go to the originally intended URL rather than the default dashboard.

**Validates: Requirements 4.4**

### Property 13: Logout invalidates session

*For any* authenticated user who initiates logout, after the logout completes, Auth::check() should return false and the session should not contain user authentication data.

**Validates: Requirements 5.1, 5.4**

### Property 14: Logout regenerates session token

*For any* logout operation, the session ID after logout should be different from the session ID before logout.

**Validates: Requirements 5.2**

### Property 15: Logout redirects to login page

*For any* logout request, the response should be a redirect to the login page.

**Validates: Requirements 5.3**

### Property 16: Login regenerates session ID

*For any* successful authentication, the session ID after authentication should be different from the session ID before authentication to prevent session fixation attacks.

**Validates: Requirements 6.1**

### Property 17: CSRF protection on login

*For any* POST request to the login endpoint without a valid CSRF token, the request should be rejected with a 419 status code.

**Validates: Requirements 6.4**

## Error Handling

### Validation Errors

The system handles validation errors using Laravel's built-in validation:

```php
$request->validate([
    'nik' => 'required|string',
    'password' => 'required|string',
    'remember' => 'boolean',
]);
```

**Error Response Format**:
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "nik": ["The nik field is required."],
        "password": ["The password field is required."]
    }
}
```

For web requests, errors are flashed to the
r the nik or password was incorrect, preventing user enumeration attacks.

### Session Errors

Session-related errors are handled by Laravel's session middleware:
- **Session expired**: User redirected to login page
- **Session invalid**: New session created automatically
- **Session storage failure**: Logged to Laravel's error log, user sees generic error page

### CSRF Token Errors

Missing or invalid CSRF tokens result in a 419 (Page Expired) response:
- User redirected back to login page
- Error message: "Page expired, please try again"
- Fresh CSRF token generated for retry

### Database Connection Errors

If the database is unavailable during authentication:
- Laravel throws `Illuminate\Database\QueryException`
- Caught by global exception handler
- User sees 500 error page
- Error logged for administrator review

### Rate Limiting

To prevent brute force attacks, implement rate limiting on login attempts:

```php
use Illuminate\Support\Facades\RateLimiter;

// In AuthController::login()
$throttleKey = Str::lower($request->input('nik')).'|'.$request->ip();

if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
    $seconds = RateLimiter::availableIn($throttleKey);
    return back()->withErrors([
        'nik' => "Too many login attempts. Please try again in {$seconds} seconds.",
    ]);
}

RateLimiter::hit($throttleKey, 60); // 5 attempts per minute
```

## Testing Strategy

### Dual Testing Approach

This feature requires both unit tests and property-based tests for comprehensive coverage:

**Unit Tests**: Focus on specific examples, edge cases, and integration points
- Specific valid/invalid credential examples
- Middleware integration
- View rendering
- Redirect behavior with specific URLs
- CSRF token validation

**Property-Based Tests**: Verify universal properties across all inputs
- Authentication behavior with randomly generated credentials
- Validation with various empty/whitespace inputs
- Session management across random user states
- Remember me functionality with random tokens

### Property-Based Testing Configuration

**Library**: Use [Pest PHP with Faker](https://pestphp.com/) or [PHPUnit with faker](https://github.com/fzaninotto/Faker) for property-based testing in PHP.

**Configuration**:
- Minimum 100 iterations per property test
- Each test tagged with feature name and property reference
- Tag format: `@test Feature: employee-login-authentication, Property {number}: {property_text}`

**Example Property Test Structure**:

```php
/**
 * @test
 * Feature: employee-login-authentication, Property 1: Valid credentials authenticate successfully
 */
public function test_valid_credentials_authenticate_successfully()
{
    for ($i = 0; $i < 100; $i++) {
        // Generate random valid user
        $user = User::factory()->create([
            'nik' => fake()->unique()->numerify('EMP####'),
            'password' => 'password123',
        ]);
        
        // Attempt authentication
        $response = $this->post('/login', [
            'nik' => $user->nik,
            'password' => 'password123',
        ]);
        
        // Assert authenticated
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
        
        // Cleanup
        Auth::logout();
        $user->delete();
    }
}
```

### Unit Test Coverage

**AuthController Tests**:
- `test_login_form_displays_for_guests()`: Verify login page renders
- `test_login_form_redirects_authenticated_users()`: Verify guest middleware
- `test_login_with_valid_credentials()`: Specific example of successful login
- `test_login_with_invalid_nik()`: Specific example of wrong nik
- `test_login_with_invalid_password()`: Specific example of wrong password
- `test_login_with_empty_nik()`: Validation error for empty nik
- `test_login_with_empty_password()`: Validation error for empty password
- `test_login_with_remember_me()`: Remember token created
- `test_login_without_remember_me()`: No remember token
- `test_logout_invalidates_session()`: Session cleared after logout
- `test_logout_redirects_to_login()`: Redirect behavior
- `test_csrf_protection_on_login()`: CSRF validation

**Middleware Tests**:
- `test_auth_middleware_protects_routes()`: Protected routes require auth
- `test_auth_middleware_stores_intended_url()`: Intended URL stored
- `test_guest_middleware_redirects_authenticated()`: Guest routes redirect authenticated users

**Integration Tests**:
- `test_full_authentication_flow()`: Complete login to dashboard flow
- `test_intended_url_redirect_flow()`: Access protected route → login → redirect to intended
- `test_remember_me_flow()`: Login with remember → logout → auto-login

### Test Database

Use Laravel's RefreshDatabase trait to ensure clean state:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    
    // Tests...
}
```

### Continuous Integration

All tests should run on every commit:
- Unit tests: Fast feedback on specific scenarios
- Property tests: Comprehensive coverage across input space
- Both must pass for merge approval
