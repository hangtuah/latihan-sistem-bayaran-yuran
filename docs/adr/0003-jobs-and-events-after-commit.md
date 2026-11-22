# 0003 — Jobs and events run only after the transaction commits

**Status:** accepted, 2026-11-27

## Context

A payment saves inside `DB::transaction()`. A queued receipt or finance
sync that starts before commit can read a payment that does not exist
yet, or send a receipt for a payment that was rolled back.

## Decision

**Side effects of a payment are queued and fire after commit.**

- `PaymentReceived` implements `ShouldDispatchAfterCommit`.
- Listeners that send mail or call other systems implement `ShouldQueue`.
- Cache invalidation inside a transaction uses `DB::afterCommit()`.
- Jobs that call outside systems set `$tries`, `$backoff` and `failed()`.
- Slow work (report exports) is a job that returns 202, not a request
  that waits.

## Consequences

A request never waits for mail or the finance system. A queue worker
must run in every environment, including development.

## Revisit when

A side effect must be part of the same transaction (for example a ledger
entry in the same database).
