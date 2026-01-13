# Budget Coach

**Track every RM. Master your wealth.**

Budget Coach is a personal finance management application designed to help you track expenses, manage accounts, and stay within your monthly budgets. It features a modern, responsive interface with smooth animations, real-time data synchronization, and export capabilities.

## Features

### Core Functionality
-   **Dashboard Overview**: Get a bird's-eye view of your financial health with total balance, monthly spending, and budget status.
-   **Expense Tracking**: Easily log expenses with details like amount, category, merchant, and notes.
-   **Smart Accounts**: Manage multiple accounts (Bank, Cash, E-Wallets). Balances automatically update as you add or remove expenses.
-   **Budget Management**: Set monthly spending limits for different categories to keep your finances on track.
-   **Category Management**: Customize expense categories to suit your spending habits.
-   **Secure Authentication**: User data isolation using Row Level Security (RLS). Support for Google and GitHub login.

### Dashboard Insights
-   **Monthly/Yearly Charts**: Toggle between monthly and yearly views to analyze spending patterns by category.
-   **Top 5 Merchants**: See where you spend the most with a ranked list of your top merchants.
-   **Recent Expenses**: Quick view of your latest transactions directly on the dashboard.
-   **Overspend Alerts**: Visual warnings (red indicators) when you exceed your budget limits.

### Export & Reporting
-   **PDF Reports**: Generate beautifully formatted monthly expense reports with summary statistics, branded with the Budget Coach logo.
-   **CSV Export**: Export expenses for any selected month to a CSV file for use in spreadsheets or other tools.

### User Experience
-   **Toast Notifications**: Modern, non-intrusive notifications for success, error, and info messages instead of native alerts.
-   **Skeleton Loaders**: Smooth loading states that show content placeholders while data is being fetched.
-   **Button Animations**: Subtle scaling effects on button clicks for improved tactile feedback.
-   **Interactive Tables**: Hover effects on table rows with action icons that become more visible on hover.
-   **Floating Action Button**: Quick-add expenses from any page using the FAB button.

## Tech Stack

-   **Frontend**: PHP, HTML5, CSS3, JavaScript (Vanilla)
-   **Backend**: PHP (Page structure), Supabase (Database & Auth)
-   **Database**: PostgreSQL
-   **Icons**: Lucide Icons
-   **PDF Generation**: html2pdf.js (CDN)
-   **Charts**: Chart.js

## Database Schema

The application uses a PostgreSQL database with the following key tables:

-   `expenses`: Stores individual transaction records.
-   `accounts`: Manages user accounts (e.g., Maybank, Cash). Includes a trigger (`update_account_balance`) to automatically adjust balances when expenses are logged.
-   `budgets`: Stores monthly limits per category.
-   `categories`: User-defined expense categories.

All tables are protected by Row Level Security (RLS) policies to ensure users can only access their own data. Deleting a category will also cascade to delete associated budget entries.

## Setup Instructions

1.  **Clone the repository** to your web server directory (e.g., `htdocs` for XAMPP).
2.  **Database Setup**:
    -   Create a new project in [Supabase](https://supabase.com).
    -   Run the contents of `schema.sql` in the Supabase SQL Editor to set up tables, RLS policies, and triggers.
3.  **Configuration**:
    -   Ensure your Supabase URL and Anon Key are correctly configured in `js/app.js`.
4.  **Run**:
    -   Start your local PHP server (e.g., via XAMPP or `php -S localhost:8000`).
    -   Navigate to `index.php` to log in.

## Project Structure

```
budget/
├── index.php           # Landing and login page
├── dashboard.php       # Main dashboard with charts, summaries, and top merchants
├── expenses.php        # Add, view, filter, and export expenses
├── accounts.php        # Manage financial accounts
├── budgets.php         # Set and monitor budgets with overspend alerts
├── categories.php      # Manage expense categories
├── schema.sql          # SQL commands for database initialization
├── favicon.svg         # Application favicon
├── includes/
│   ├── header.php      # Shared header with navigation
│   └── footer.php      # Shared footer
├── css/
│   └── styles.css      # Application styles with animations
└── js/
    └── app.js          # Core application logic
```

## Screenshots

*Coming soon*

## License

This project is for personal use.
