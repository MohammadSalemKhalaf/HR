# RAG Frontend (Phase 1 — Admin + Auth Integration)

Vue 3 frontend for the RAG Employee Management System. Fully integrated with Laravel backend using token-based API authentication.

## Features

- ✅ Production-ready authentication with role-based access control
- ✅ Session persistence (tokens stored in localStorage)
- ✅ Role-based redirects after login (admin/company/manager/employee/job_seeker)
- ✅ Global error handling and validation mapping
- ✅ Protected routes with proper guards
- ✅ Tailwind CSS for responsive design
- ✅ Vue 3 Composition API with Pinia state management
- ✅ Admin dashboard scaffold

## Quick Start

### Prerequisites

- Node.js 16+ and npm
- Laravel backend running at `http://127.0.0.1:8002`

### Installation

```bash
cd frontend
npm install
```

### Configure Backend URL

Edit `.env` to point to your Laravel backend:

```env
VITE_API_URL=http://127.0.0.1:8002
```

### Run Development Server

```bash
npm run dev
```

The app will be available at `http://localhost:5173` (or the next available port shown in terminal).

### Build for Production

```bash
npm run build
```

## Architecture

### Authentication Flow

1. User enters credentials on `/login`
2. Frontend calls `POST /api/auth/login` (existing backend API)
3. Backend returns `{ token, user }`
4. Token is stored in `localStorage` and set as `Authorization: Bearer {token}` header
5. User is redirected based on their role:
   - `admin` → `/admin`
   - `company` / `company_owner` → `/company`
   - `manager` → `/manager`
   - `employee` → `/employee`
   - `job_seeker` → `/jobs`

### Session Persistence

- Tokens are automatically restored from `localStorage` on page refresh
- Route guards verify authentication before allowing access
- 401 responses automatically redirect to login and clear token

### File Structure

```
src/
  api/
    axios.ts           # Configured axios instance with interceptors
  stores/
    auth.ts            # Pinia store for user state and auth actions
  pages/
    Login.vue          # Login page with form validation
  modules/
    admin/
      pages/           # Admin pages (Dashboard, CRUD lists)
      components/      # Admin-specific components (Layout, Navbar, Sidebar)
      router/          # Admin route definitions
      types/           # TypeScript interfaces
  router/
    index.ts           # Main router with global guards
  layouts/
    AuthLayout.vue     # Layout wrapper for auth pages
  components/
    base/              # Reusable UI components
```

## API Integration

### Available Endpoints (Backend)

All authenticated requests include `Authorization: Bearer {token}` header:

- `POST /api/auth/login` — Authenticate user
- `POST /api/auth/logout` — Logout user
- `GET /api/auth/me` — Get current user profile
- `GET /api/admin/companies` — List companies (admin only)
- `POST /api/admin/companies` — Create company
- `GET /api/admin/departments` — List departments
- `GET /api/admin/employees` — List employees
- `GET /api/admin/dashboard-stats` — Dashboard statistics

### Error Handling

- **401 Unauthorized**: Token invalid or expired → Redirect to login
- **422 Validation Error**: Validation failed → Display field errors in form
- **500 Server Error**: Generic error handling

## Testing with Real Users

Use existing users from the database:

```
Email: admin@example.com
Password: password
```

Or create test users via backend.

## Development Notes

### Adding New Pages

1. Create page component in `src/modules/admin/pages/`
2. Add route to `src/modules/admin/router/index.ts`
3. Mark route with `meta: { requiresAuth: true, role: 'admin' }`
4. Use `useAuthStore()` to access user data and permissions

### Creating API Services

```typescript
import api from '@/api/axios'

export async function fetchCompanies() {
  const { data } = await api.get('/admin/companies')
  return data
}
```

### Styling with Tailwind

All components use Tailwind CSS. Ensure `tailwind.config.cjs` is configured correctly.

## Troubleshooting

### CORS Errors

Ensure backend is running and accessible at `VITE_API_URL` in `.env`. Laravel backend should have CORS middleware enabled.

### 404 on API Calls

Double-check that `VITE_API_URL` matches your backend host and port. Default is `http://127.0.0.1:8002`.

### Token Not Persisting

Check browser localStorage. If tokens aren't persisting:
1. Verify `localStorage` is not disabled
2. Check that `setAuthToken()` is called after successful login
3. Verify token is stored before redirect

## Next Steps

- [ ] Implement full CRUD pages for Companies, Departments, Employees
- [ ] Add Pinia stores for data caching
- [ ] Add server-side pagination and filtering
- [ ] Implement remaining role modules (company, manager, employee, job_seeker)
- [ ] Add E2E tests with Cypress
- [ ] Deploy to production

## License

MIT

