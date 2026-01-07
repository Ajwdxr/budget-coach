# Budget Coach

**Track every RM. Master your wealth.**

Budget Coach is a personal finance management application designed to help you track expenses, manage accounts, and stay within your monthly budgets. It features a modern, responsive interface and real-time data synchronization.

## Features

-   **Dashboard Overview**: Get a bird's-eye view of your financial health with total balance, monthly spending, and budget status.
-   **Expense Tracking**: Easily log expenses with details like amount, category, merchant, and notes.
-   **Smart Accounts**: Manage multiple accounts (Bank, Cash, E-Wallets). Balances automatically update as you add or remove expenses.
-   **Budget Management**: Set monthly spending limits for different categories to keep your finances on track.
-   **Category Management**: Customize expense categories to suit your spending habits.
-   **Secure Authentication**: specific user data isolation using Row Level Security (RLS). Support for Google and GitHub login.

## Tech Stack

-   **Frontend**: PHP, HTML5, CSS3, JavaScript (Vanilla)
-   **Backend**: PHP (Page structure), Supabase (Database & Auth)
-   **Database**: PostgreSQL
-   **Icons**: Lucide Icons

## Database Schema

The application uses a PostgreSQL database with the following key tables:

-   `expenses`: Stores individual transaction records.
-   `accounts`: Manages user accounts (e.g., Maybank, Cash). Includes a trigger (`update_account_balance`) to automatically adjust balances when expenses are logged.
-   `budgets`: Stores monthly limits per category.
-   `categories`: User-defined expense categories.

All tables are protected by Row Level Security (RLS) policies to ensure users can only access their own data.

## Setup Instructions

1.  **Clone the repository** to your web server directory (e.g., `htdocs` for XAMPP).
2.  **Database Setup**:
    -   Create a new project in [Supabase](https://supabase.com).
    -   Run the contents of `schema.sql` in the Supabase SQL Editor to set up tables, RLS policies, and triggers.
3.  **Configuration**:
    -   Ensure your Supabase URL and Anon Key are correctly configured in your JavaScript files (check `js/app.js` or `includes/header.php` for where these are initialized).
4.  **Run**:
    -   Start your local PHP server (e.g., via XAMPP or `php -S localhost:8000`).
    -   Navigate to `index.php` to log in.

## Project Structure

-   `index.php`: Landing and login page.
-   `dashboard.php`: Main user dashboard with charts and summaries.
-   `expenses.php`: Add, view, and filter expenses.
-   `accounts.php`: Manage financial accounts.
-   `budgets.php`: Set and monitor budgets.
-   `categories.php`: Manage expense categories.
-   `schema.sql`: SQL commands for database initialization.
-   `includes/`: Shared UI components (header, footer).
-   `css/` & `js/`: Stylesheets and application logic.
