# Login Debugging Checklist

## Frontend Setup ✓

### What Changed:
1. **Auth Store** (`src/stores/auth.ts`)
   - Added comprehensive console logging for all auth actions
   - Multiple token extraction locations (data.token, data.access_token, token, access_token)
   - Proper error handling and display

2. **Axios** (`src/api/axios.ts`)
   - Added request/response logging
   - Detailed error logging
   - Token management with logging

3. **Login Page** (`src/pages/Login.vue`)
   - Updated test credentials to: admin@gmail.com / 12345678
   - Added debug mode (Ctrl+Shift+D to toggle)
   - Better error display

## Testing Steps

### Step 1: Open Browser DevTools
1. Open http://localhost:5176/
2. Open DevTools (F12)
3. Go to **Console** tab
4. You should see logs like:
   ```
   [Axios] Configured with baseURL: http://127.0.0.1:8002/api
   [Auth] Restoring auth from localStorage: ✗ No token
   ```

### Step 2: Try Login
1. Email: `admin@gmail.com`
2. Password: `12345678`
3. Click "Sign In"

### Step 3: Monitor Console
Watch for these logs:

**Success Flow:**
```
[Auth] Login attempt with email: admin@gmail.com
[Auth] Sending login request to /api/auth/login
[Axios Request] POST /auth/login with data
[Axios Response] status: 200 with data: {...}
[Auth] ✓ Token found at data.token
[Auth] ✓ Token stored: [token_preview]
[Auth] ✓ Token persisted to localStorage and axios
[LoginPage] Login successful, user role: admin
[LoginPage] Redirecting to: /admin
```

**Error Flow (if fails):**
```
[Axios Response Error] status: 401 data: {...}
[Auth] Login failed: Invalid email or password
[Auth] Final error: Invalid email or password
```

### Step 4: Check Network Tab
1. Go to **Network** tab in DevTools
2. Look for `POST auth/login` request
3. Check:
   - **Request Headers:**
     - `Accept: application/json`
     - `X-Requested-With: XMLHttpRequest`
   - **Request Body:** `{"email":"admin@gmail.com","password":"12345678"}`
   - **Response Status:** Should be 200
   - **Response Body:** Should have `data.token`

### Step 5: Check localStorage
1. DevTools → **Application** → **Local Storage** → http://localhost:5176
2. Look for key: `auth_token`
3. If login successful, token should be stored here

## Troubleshooting

### Issue: Login fails with "Login failed"

#### Check 1: Backend running?
```bash
# Make sure Laravel backend is running at port 8002
cd /home/mohammad/laravel-projects/RAG/job-backoffice
php artisan serve --host 127.0.0.1 --port 8002
```

#### Check 2: CORS enabled?
Backend should allow requests from http://localhost:5176

#### Check 3: User exists in database?
```bash
cd /home/mohammad/laravel-projects/RAG/job-backoffice
php artisan tinker
>>> User::where('email', 'admin@gmail.com')->first()
```

#### Check 4: Password correct?
```bash
# In tinker:
>>> $user = User::where('email', 'admin@gmail.com')->first()
>>> Hash::check('12345678', $user->password)  // Should return true
```

#### Check 5: Token endpoint exists?
```bash
# Check routes
cd /home/mohammad/laravel-projects/RAG/job-backoffice
php artisan route:list | grep auth
```

Should include:
- `POST /api/auth/login`
- `GET /api/auth/me`

### Issue: Console shows no logs

1. Hard refresh: **Ctrl+Shift+R**
2. Clear cache: DevTools → Settings → Network → Disable cache (while DevTools open)
3. Check if Vue is loaded:
   ```javascript
   // In console:
   console.log(window.__VUE__)  // Should exist
   ```

### Issue: Network shows CORS error

Backend needs CORS middleware. Check `config/cors.php` in Laravel:
```php
'allowed_origins' => ['*'],  // or specific frontend URL
```

## Debug Mode

### Keyboard Shortcut: Ctrl+Shift+D
- Toggles debug panel on login page
- Shows API URL, token status, user role
- Auto-dismisses after 10 seconds

### Console Commands (in DevTools)
```javascript
// Check auth store state
window.__VUE__ // Access Vue instance

// Get auth token
localStorage.getItem('auth_token')

// Check axios defaults
// Would need to expose in window for direct access
```

## Expected Results

### Successful Login
- ✓ Page shows loading spinner briefly
- ✓ Token appears in Console: `[Auth] ✓ Token stored: ...`
- ✓ Redirects to /admin dashboard
- ✓ User name appears in navbar
- ✓ Token persisted in localStorage
- ✓ Page refresh maintains login

### Failed Login (wrong credentials)
- ✓ Shows "Invalid email or password"
- ✓ Form stays visible for retry
- ✓ No token stored

### Failed Login (user not found)
- ✓ Shows "Invalid email or password" (same message for security)
- ✓ Console shows: `status: 401`

## Next Actions

1. **Start dev server** (if not running):
   ```bash
   cd /home/mohammad/laravel-projects/RAG/frontend
   npm run dev
   ```

2. **Start backend** (if not running):
   ```bash
   cd /home/mohammad/laravel-projects/RAG/job-backoffice
   php artisan serve --host 127.0.0.1 --port 8002
   ```

3. **Open frontend**: http://localhost:5176/

4. **Try login** with: admin@gmail.com / 12345678

5. **Check console logs** for detailed debugging

6. **Share console output** if issues persist

## Logs to Look For

```
✓ Success:
[Auth] ✓ Token found at data.token
[Auth] ✓ Token persisted to localStorage and axios
[LoginPage] Login successful, user role: admin

✗ Failure:
[Auth] ✗ No token found in response
[Axios] 401 Unauthorized
[Auth] Invalid email or password
```
