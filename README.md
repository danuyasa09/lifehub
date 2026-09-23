<div align="center">
  <div style="background-color: #4F46E5; padding: 20px; border-radius: 16px; display: inline-block; margin-bottom: 20px;">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M13 10V3L4 14h7v7l9-11h-7z"></path>
    </svg>
  </div>
  <h1>LifeHub</h1>
  <p><strong>Your Ultimate Productivity and Life Management System</strong></p>
</div>

---

LifeHub is a comprehensive, meticulously designed web application that centralizes your day-to-day productivity. Built with modern aesthetics (inspired by Notion and Linear), it provides a seamless and calming environment to manage your tasks, habits, projects, finances, and even mental wellness.

## ✨ Features

- **Dashboard**: A bird's-eye view of your day. See your urgent tasks, today's habits, and quick actions.
- **Smart Task Management**: Organize tasks into categories, set priorities, track progress, and manage sub-tasks seamlessly.
- **Project Kanban Boards**: Visualize your work with drag-and-drop Kanban boards. Upload files, manage task statuses, and track project timelines.
- **Habit Tracking**: Build better routines. Track daily, weekly, or monthly habits and monitor your streaks visually.
- **Finance & Budgeting**: Log your income and expenses. Keep an eye on your budgets and analyze your financial flow with intuitive charts.
- **Journal & Mood Tracking**: Reflect on your day. Log entries using a rich Markdown editor and track your daily moods.
- **AI Assistant**: A built-in intelligent chat interface to help you brainstorm, summarize notes, or plan your day.
- **Global Smart Search (Cmd+K)**: Instantly jump to any task, note, or project without leaving your keyboard.
- **Analytics**: Beautiful Chart.js visualizations covering your productivity, spending habits, and mood trends.
- **PWA Ready**: Installable as a Progressive Web App on mobile and desktop for offline caching and native app feel.

## 🛠 Tech Stack

- **Framework**: [Laravel 12](https://laravel.com)
- **Frontend**: Blade Components, [Tailwind CSS v4](https://tailwindcss.com) (for styling), and [Alpine.js](https://alpinejs.dev) (for lightweight reactivity).
- **Database**: MySQL / MariaDB (managed via Eloquent ORM).
- **Icons**: Heroicons.
- **Charts**: Chart.js.

## 🚀 Installation & Setup

Follow these instructions to get LifeHub running on your local machine.

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL / MariaDB

### 1. Clone the repository
```bash
git clone https://github.com/yourusername/lifehub.git
cd lifehub
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
Copy the example environment file and generate a unique app key:
```bash
cp .env.example .env
php artisan key:generate
```
Open the `.env` file and configure your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lifehub
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Migration & Seeding
Migrate the database and seed it with dummy data to test the UI out of the box:
```bash
php artisan migrate --seed
```
*Note: The seeder populates the database with tasks, projects, habits, finances, AI chat history, and notifications for `test@example.com` with password `password`.*

### 5. Build Assets & Run
Compile the frontend assets and start the local development server:
```bash
npm run build
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 📱 Mobile Responsiveness & PWA

LifeHub has been heavily optimized for mobile devices. The sidebar collapses into a convenient hamburger menu, and all complex views (like Kanban boards and data tables) gracefully degrade or enable horizontal scrolling on smaller screens. 

Additionally, LifeHub includes a `manifest.json` and a Service Worker (`sw.js`). You can "Add to Home Screen" on iOS/Android or install it as a standalone app via Chrome on Desktop.

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! 
Feel free to check the [issues page](https://github.com/yourusername/lifehub/issues).

---
*Designed with ❤️ for maximum productivity and minimal friction.*
