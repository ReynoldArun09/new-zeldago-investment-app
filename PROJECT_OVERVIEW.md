# Project Overview

## 1. Introduction
The Info Referral App is a robust, Laravel-based Multi-Level Marketing (MLM) and Investment Management System. It allows users to register, invest funds, earn daily/periodic Return on Investment (ROI), build a referral network (downline) to earn commissions, and withdraw their earnings. The application also provides an administrative backend to oversee users, verify accounts (KYC/Nominee), approve/reject investments and withdrawals, and configure dynamic application settings.

## 2. Architecture & Main Components
The application is built on **Laravel 11.x (PHP 8.3+)** utilizing the standard MVC (Model-View-Controller) architecture.

### Key Components:
- **Authentication & Authorization**: The application uses separate guards for Admins (`admin`) and Users (`web`).
- **Admin Panel**: Manages system health, user actions, and configurations.
- **User Panel**: A dedicated dashboard for investors and agents to track their financial performance and network growth.
- **Database**: Relational database (SQLite configured by default, easily portable to MySQL/PostgreSQL) managed through Laravel Eloquent ORM.

## 3. Core Workflows and Business Logic

### A. Investments & ROI
- **Investment Flow**: Users submit manual investment requests by entering an amount, transaction ID, and uploading a payment proof. The investment is marked as `pending`.
- **Approval**: Admins review and approve the investment. Upon approval, the status changes to `ACTIVE` and the system calculates the `next_roi_date` based on globally configured cycle days.
- **ROI Processing**: The system tracks ROI logs. Based on the investment cycle, ROI amounts are credited to the users' balances (either automatically via background jobs or manually processed by admins).
- **Closing**: Users can request to close an `ACTIVE` investment. Admins approve this request to stop further ROI generation.

### B. Network & Commissions (MLM Logic)
- **Referral System**: Users can invite others using a unique referral code.
- **Genealogy**: The system tracks up-line and down-line relationships, forming an MLM tree.
- **Commission Distribution**: When a user invests or generates ROI, their upline (sponsors/agents) earns commissions based on a multi-tier percentage configured by the admin (Commission Settings).

### C. Financials & Withdrawals
- **Wallets/Balances**: User balances are derived from commissions and ROI, minus any processed withdrawals. 
- **Withdrawal Flow**: Agents or Users can request withdrawals to Cash, UPI, or Bank Transfer. The requested amount is immediately deducted from the `wallet_balance`.
- **Admin Processing**: Admins approve (uploading proof of payment) or reject (refunding the amount to the user's wallet) these requests.

### D. User Verification & Support
- **KYC & Nominee**: Users must upload identification and nominee details for compliance. Admins manually review and approve/reject these submissions.
- **Support Tickets**: An internal ticketing system exists for users to resolve issues directly with the admin team.

## 4. Key Technologies & Libraries
- **Backend Framework**: Laravel 11 (PHP 8.3+)
- **Frontend Assets**: Blade Templating Engine, Vite (for asset bundling), JavaScript, Tailwind CSS (assumed based on modern Laravel ecosystem).
- **Database**: SQLite (via `.env` configuration, easily switchable to MySQL/PostgreSQL).
- **Notifications**: Laravel's Notification System (Mail, Database/UI notifications).
- **File Storage**: Local Disk (Storage facade) and `public_path` moving for uploads.

## 5. Assumptions & Inferred Behavior
- **Job/Cron Scheduling**: It is inferred that a scheduled task or cron job periodically processes ROI for active investments using the `next_roi_date`.
- **Commission Triggers**: The exact point at which MLM commissions are dispersed is likely tied to either the investment approval event or ROI generation event.
- **Multi-Guard Setup**: Since `Admin` models and `auth:admin` middleware exist, it is assumed `config/auth.php` is heavily customized to support multi-auth.
