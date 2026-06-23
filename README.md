# PRITECH Issue Tracker

A small Laravel issue tracker with projects, kanban boards, tags, comments, team assignment, and activity logs.

**Stack:** Laravel 13 · Blade · SQLite · Vite (vanilla JS)

---

## Requirements

- **PHP 8.3+** with extensions: `sqlite3`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- **Composer**
- **Node.js 18+** and **npm**

---

## Quick start

```bash
git clone <your-repo-url>
cd issue_tracker

composer install
cp .env.example .env    # Windows: copy .env.example .env
php artisan key:generate

# Create the SQLite database file
type nul > database\database.sqlite   # Windows (PowerShell)
# touch database/database.sqlite    # macOS / Linux

php artisan migrate:fresh --seed

npm install
npm run build

php artisan serve
```

Open **http://127.0.0.1:8000** in your browser.

> **Tip:** `composer run setup` installs dependencies and runs migrations, but it does **not** seed demo data. Always run `php artisan migrate:fresh --seed` after setup to load the sample projects.

---

## Development (optional)

For live CSS/JS reload while working on the app, run two terminals:

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Or use the all-in-one dev script (server + Vite + logs):

```bash
composer run dev
```

---

## Demo logins

After seeding, use these accounts:

| Role | Email | Password | Owns |
|------|-------|----------|------|
| Admin | `admin@example.com` | `admin` | **Sprig Billing** (5 issues) |
| Alex | `alex@example.com` | `password` | **Pulse Metrics** (4 issues) |

Jordan and Sam exist as team members you can assign to issues.

**Authorization demo:** Sign in as Admin and open **Pulse Metrics** — you can view the board but not edit the project, create issues, or drag cards. Sign in as Alex to get full access to Pulse Metrics; Sprig Billing is view-only for Alex.

---

## What to try

1. **Projects** — two SaaS-themed boards with real issue data (no lorem ipsum).
2. **Kanban** — drag cards between To Do / In Progress / Completed (owner only).
3. **Issue detail** — tags, members, comments, and activity timeline.
4. **Search** — on a project’s issue list, filter and debounced text search.
5. **Policies** — only project owners can edit/delete projects and their issues.

---

## Tests

```bash
php artisan test
```

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| Page has no styling | Run `npm install` then `npm run build` |
| `could not find driver` | Enable the PHP SQLite extension |
| Login fails after clone | Run `php artisan migrate:fresh --seed` |
| Port 8000 in use | `php artisan serve --port=8001` |

---

Built for PRITECH by Lisa Ramizi.
