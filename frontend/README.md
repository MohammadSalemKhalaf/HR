# RAG Frontend (Phase 1 — Admin scaffold)

This folder contains the Vue 3 frontend scaffold for Phase 1 (Admin area). It is intentionally minimal and uses Vite + Vue 3 + Pinia.

Quick start

- cd frontend
- npm install
- npm run dev

Notes

- The app uses Laravel session cookie auth; axios is configured with `withCredentials: true`.
- Vite dev server proxies `/api` to `http://127.0.0.1:8000` by default. Adjust `vite.config.ts` if your backend runs on a different host/port.
- Place admin pages under `src/modules/admin/pages` and register routes in `src/router`.
