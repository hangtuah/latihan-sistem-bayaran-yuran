# 0005 — Money is DECIMAL in the database and a string in the API

**Status:** accepted, 2026-11-27

## Context

A float sum of fees drifts (`0.1 + 0.2` is not `0.3`), and an API that
returned `1100` for one invoice and `1100.5` for another broke the
mobile app's formatting. The late penalty on RM920 at 2% is RM18.40,
which must not become 18.399999.

## Decision

**Amounts are `decimal(10, 2)` columns, cast `decimal:2`, and leave the
API as strings such as `"1100.00"`.**

- Every amount column on `invoices` and `payments` uses `decimal(10, 2)`.
- Models cast amounts with `decimal:2`; tests assert `'1100.00'`.
- Calculations (`LatePenaltyCalculator`, `EarlyPaymentDiscount`) round to
  two decimals before the result is saved or returned.
- Totals are summed in SQL (`sum('amount')`), not in PHP floats.

## Consequences

Exact sen in every report. Code that does arithmetic must remember to
round; tests cover each calculator.

## Revisit when

The system handles currencies other than ringgit.
