# Changes made in this review

## Bugs fixed

1. **Events created via Admin panel never showed on the public /schedule page.**
   `Event` model was missing `wedding_id` in `$fillable`, so every event added
   through the admin form silently saved with `wedding_id = null`. This was
   already broken in your live database (all 7 events had `wedding_id = null`)
   — fixed the model and repaired the existing rows.

2. **Same bug on Information Items** (`InformationItem` model missing
   `wedding_id` in `$fillable`). Not yet triggered in your data, but any new
   item added through the admin panel would have silently disappeared from
   the public `/information` page. Fixed.

3. **Contributions page was hardcoded, not connected to your Payment
   Settings.** The public `/support` page had the M-Pesa number and bank
   details typed directly into the template, completely ignoring the
   `PaymentSetting` records in the database. There was also no admin page to
   edit them. Fixed by:
   - Rewiring the page to render whatever's in `PaymentSetting` (method,
     phone/paybill, bank details, instructions, active toggle).
   - Adding a new **Payment Info** page in the admin sidebar
     (Admin → Payment Info) so you can update your M-Pesa number / bank
     account any time without touching code.

4. **Broken portability:** `.env` had a hardcoded Windows XAMPP path for the
   SQLite database (`C:/xampp/htdocs/...`). Removed it so the app works out
   of the box on any machine/host.

## Daraja / M-Pesa API removed

Since payments are recorded manually (guest submits reference, couple
verifies), the unused M-Pesa Daraja (STK Push) scaffolding was removed:
- `MPESA_*` env variables deleted from `.env`.
- Unused STK-push columns (`merchant_request_id`, `checkout_request_id`,
  `mpesa_receipt_number`, `stk_initiated_at`, `stk_completed_at`) dropped
  from the `contributions` table via a new migration (already applied to
  the included database).

Nothing else relied on these — there was no Daraja service class or
callback route implemented, so this is a clean removal.

## Not changed (flagged, not fixed)

- Deleting a gallery photo, story photo, or hero image from the admin panel
  removes the database row but does not delete the underlying file from
  `storage/app/public`. Harmless for now, but will leave orphaned files
  over time. Say the word if you'd like this tidied up too.
