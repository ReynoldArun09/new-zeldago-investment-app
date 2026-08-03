# Application Sitemap Analysis

This document provides a comprehensive sitemap of the application based on the routing structure and role definitions. The platform serves three primary roles: **Admin**, **Investor** (Normal User), and **Agent**.

---

## 1. Public Pages (Unauthenticated)
These pages are accessible to anyone visiting the domain.

- **Login Page** (`/` or `/login`)
  - Shared authentication entry point for Users (Investors & Agents).
- **Admin Login** (`/admin/login`)
  - Dedicated authentication page for administrators.

---

## 2. Administrator Role
The administrative module is highly comprehensive, handling user management, financial approvals, system settings, and reporting.

- **Dashboard** (`/admin/dashboard`)
- **User Management**
  - All Users (`/admin/users`)
  - All Investors (`/admin/users/investors`)
  - All Agents (`/admin/users/agents`)
  - Add User (`/admin/users/create`)
  - User Details & Impersonation (`/admin/users/{username}`)
- **Financial Operations**
  - **ROI Management**
    - ROI Overview (`/admin/roi`)
    - Pending ROI (`/admin/roi/pending`)
    - Processing ROI (`/admin/roi/processing`)
  - **Investments**
    - All Investments (`/admin/investments`)
    - Investment Details (`/admin/investments/details/{id}`)
  - **Withdrawals**
    - All Withdrawals (`/admin/withdrawals`)
    - Pending Withdrawals (`/admin/withdrawals/pending`)
  - **Commission Logs** (`/admin/commission-log`)
- **Verification Management**
  - KYC Approvals (`/admin/verification/kyc`)
  - Nominee Approvals (`/admin/verification/nominee`)
  - Bank Detail Approvals (`/admin/verification/bank`)
- **Reports**
  - Investment Reports (`/admin/reports/investment`)
  - ROI Reports (`/admin/reports/roi`)
  - Commissions Reports (`/admin/reports/commissions`)
  - Withdrawals Reports (`/admin/reports/withdrawals`)
- **Support System**
  - Support Tickets (`/admin/support`)
  - Ticket Details/Reply (`/admin/support/{id}`)
- **System Settings**
  - Settings Unlock Form (`/admin/settings/unlock`) - *Security checkpoint*
  - General Settings (`/admin/settings/admin`)
  - Logo & Favicon (`/admin/settings/admin/logo`)
  - SEO Settings (`/admin/settings/admin/seo`)
  - Theme Configuration (`/admin/settings/admin/theme`)
  - Currency Settings (`/admin/settings/admin/currency`)
  - ROI Configuration (`/admin/settings/admin/roi`)
  - Commission Configuration (`/admin/settings/admin/commission`)
- **Admin Profile & Utility**
  - Profile Settings (`/admin/profile`)
  - Password Settings (`/admin/password`)
  - Notifications (`/admin/notifications`)
  - Notification History (`/admin/notifications/history`)

---

## 3. Investor & Agent Roles
Both **Investors** and **Agents** share the primary user application interface (`/` routes after login). Access to specific network and commission capabilities varies based on their account type.

- **Dashboard** (`/dashboard`)
  - Includes statement downloads (`/statements/download`)
- **Investments**
  - Active Investments (`/investments/active`)
  - Closed Investments (`/investments/closed`)
  - Create/Add Investment (`/investments/create`)
- **Network / Team** *(More prominent for Agents)*
  - My Referrals (`/network/referrals`)
  - Genealogy Tree (`/network/genealogy`)
  - Add Investor Modal (`/network/add-investor`)
  - Network Investor Details (`/network/investor/{id}/investments`)
- **Finance**
  - Commission Transactions (`/finance/transactions/commissions`)
  - ROI Transactions (`/finance/transactions/roi`)
  - Withdrawals (`/finance/withdrawals`)
  - Transfer Funds (`/finance/transfer`)
- **Settings & Profile**
  - Profile Information (`/settings/profile`)
  - Password Management (`/settings/password`)
- **Verifications**
  - KYC Details (`/verification/kyc`)
  - Nominee Details (`/verification/nominee`)
  - Bank Details (`/verification/bank`)
- **Utilities**
  - Notifications (`/notifications`)
  - Support Tickets (`/support`)
  - Create Ticket (`/support/create`)
  - Ticket Thread (`/support/{id}`)

---

## Logical Groupings & Notes

> **Shared User Interface**
> The application uses a unified frontend for both `Investors` (Normal Users) and `Agents`. The backend routes differentiate features logically. For instance, an Agent might rely heavily on the **Network** module to track their downlines, while an Investor might focus solely on the **Investments** and **ROI** modules.

> **Impersonation Feature**
> The system has a hidden utility route for administrators: `POST /admin/users/{username}/impersonate`. This allows an Admin to instantly access the Investor/Agent dashboard exactly as that user sees it.

> **Protected Settings**
> The Admin Settings section requires a secondary "Unlock" authentication step (`/admin/settings/unlock`) before administrators can modify critical parameters like Commission rules, ROI percentages, and Theme configurations.
