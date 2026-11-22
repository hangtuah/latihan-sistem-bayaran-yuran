# 0002 — Authorization goes through policies

**Status:** accepted, 2026-11-27

## Context

The Modul 3 lab API let a student read another student's invoice by
changing the ID in the URL (IDOR, Insecure Direct Object Reference).
The check existed in one controller and was missing in the next. The
Modul 6 `ReceiptController` repeated the same mistake.

## Decision

**Every route that loads a model owned by a student calls the model's
policy.** `InvoicePolicy::view()` allows the owner and the Bursary;
`InvoicePolicy::pay()` allows the owner only.

- Controllers call `Gate::authorize('view', $invoice)`; FormRequests use
  `$this->user()->can(...)` in `authorize()`.
- Payments and receipts reuse `InvoicePolicy` through `$payment->invoice`.
- Bursary-only routes sit behind `can:view-bursary-reports`.
- Every new endpoint has a test for 200, 403 and 401.

## Consequences

One rule for "who may see this". A missing `authorize` is easy to spot
in review because every other controller has one.

## Revisit when

Staff roles beyond student and Bursary need per-faculty access.
