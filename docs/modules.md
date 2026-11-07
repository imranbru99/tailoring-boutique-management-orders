# 30 Business Modules Catalog & Directory
### Complete Module Matrix for Tailoring and Boutique Management OS

Author: **Imran Ahmed** ([https://imrandev.bd](https://imrandev.bd) | *me@imrandev.bd*)

---

## Complete 30-Module Registry Matrix

| # | Module Key | Name | Category | Core? | Dependencies | Description |
|---|---|---|---|---|---|---|
| 1 | `dashboard` | Dashboard & Overview | core | Yes | *None* | Real-time executive metrics, order analytics, and financial cashflow widgets. |
| 2 | `customers` | Customer Management | core | Yes | *None* | Customer directory, contact preferences, loyalty points, and purchase history. |
| 3 | `measurements` | Body Measurement Engine | core | Yes | `customers` | Anatomical measurement templates, multi-profile snapshots, and posture tracking. |
| 4 | `designs` | Garment & Design Catalog | operations | No | *None* | Visual design portfolios, necklines, lapels, cuffs, and embroidery catalogs. |
| 5 | `fabrics` | Fabric & Material Rolls | operations | No | *None* | Fabric types, roll batch tracking, shrinkage rates, and remnant management. |
| 6 | `orders` | Tailoring Order Management | core | Yes | `customers`, `measurements` | 18-stage order lifecycle, line items, priority, and due-date tracking. |
| 7 | `production` | Production & Job Cards | production | No | `orders` | Workshop routing, cutting tickets, stitching assignments, and labor timers. |
| 8 | `employees` | Staff & Tailor Management | hr | No | *None* | Master tailors, cutters, finishers, piece-rate wages, and skill matrix. |
| 9 | `appointments` | Appointment Calendar | operations | No | `customers` | Client measurement visits, trial fittings, and designer consultations. |
| 10 | `quality_control` | Quality Inspection (QC) | production | No | `production` | Multi-point inspection checklists, seam inspections, and defect logs. |
| 11 | `alterations` | Alterations & Repairs | operations | No | `orders` | Garment alteration ticketing, fitting adjustments, and repair charges. |
| 12 | `deliveries` | Dispatch & Delivery | operations | No | `orders` | Delivery chits, home dispatch, courier tracking, and customer signatures. |
| 13 | `pos` | Point of Sale (POS) | sales | No | `orders`, `billing` | Touchscreen cashier counter, barcode scanning, split tender, and register drawer. |
| 14 | `inventory` | Inventory & Haberdashery | operations | No | `suppliers` | Thread, buttons, zippers, linings, interlinings, and reorder warnings. |
| 15 | `suppliers` | Supplier Management | operations | No | *None* | Textile mills, haberdashery vendors, purchase orders, and payment terms. |
| 16 | `billing` | Invoicing & Billing | core | Yes | `orders` | Multi-currency invoices, tax breakdowns, discount codes, and payment receipts. |
| 17 | `accounting` | Accounting & Expenses | finance | No | `billing` | Studio expenses, utility bills, rent, cashbook, and P&L financial statements. |
| 18 | `membership` | Memberships & Loyalty | marketing | No | `customers` | VIP tiers, reward points, bridal packages, and member-only discounts. |
| 19 | `marketing` | Marketing & Campaigns | marketing | No | `customers` | SMS reminders, festive campaigns, seasonal coupons, and promotional broadcasts. |
| 20 | `attendance` | Biometric Attendance Hub | hr | No | `employees` | Hardware biometric sync, work shifts, late arrival thresholds, and overtime. |
| 21 | `reports` | Business Intelligence & Reports | operations | No | *None* | Production yield, fabric wastage analysis, tailor efficiency, and revenue trends. |
| 22 | `customer_portal` | Client Self-Service Portal | operations | No | `customers`, `orders` | Customer login, digital measurement sheets, order timeline, and invoice history. |
| 23 | `staff_portal` | Tailor Workshop Portal | production | No | `employees`, `production` | Tailor job queue, measurement review, completion timestamps, and piece-rate stats. |
| 24 | `settings` | System & Branch Settings | core | Yes | *None* | Branch configuration, currency, measurement units (inches/cm), and tax policies. |
| 25 | `multi_branch` | Multi-Branch Centralization | system | No | *None* | Branch inventory transfers, multi-location staff scheduling, and branch consolidation. |
| 26 | `ai_assistant` | AI Fashion & Pattern Assistant | ai | No | *None* | AI catalog writer, style advisor, fabric care recommendations, and prompt playground. |
| 27 | `saas_subscriptions` | SaaS Tier Billing & Limits | system | No | *None* | Tenant subscription plans, monthly quota limits, and billing cycle management. |
| 28 | `api_integrations` | Developer REST API & Webhooks | system | No | *None* | Laravel Sanctum API tokens, POS terminal webhooks, and third-party ERP feeds. |
| 29 | `import_export` | Bulk Import & Data Migration | system | No | *None* | CSV/Excel customer book imports, fabric inventory exports, and backup tools. |
| 30 | `audit_security` | Audit Trail & Security Logs | system | No | *None* | Change logs for sensitive measurement updates, financial edits, and access tracking. |

---

## Module Activation Example

```php
use App\Core\Modules\ModuleManager;

$manager = app(ModuleManager::class);

// 1. Enable module for a boutique tenant
$result = $manager->enable('alterations', $tenantId);
if (!$result['success']) {
    // Shows user-friendly message if dependencies are missing
    echo $result['error'];
}

// 2. Check status
if ($manager->isEnabled('alterations', $tenantId)) {
    // Show Alterations navigation in Filament
}

// 3. Deactivate module (historical records are safely kept intact)
$result = $manager->disable('alterations', $tenantId);
```
