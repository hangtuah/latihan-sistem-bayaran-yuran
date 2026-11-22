# 0004 — Store times in UTC, count days in Malaysian time

**Status:** accepted, 2026-11-27

## Context

`config/app.php` uses UTC. Every student and the Bursary are
in Malaysia (UTC+8). The daily report built on `today()`
missed a 7:30 am payment (Modul 6: count=0). A scheduled task
without `timezone()` ran at 8:00 am, not midnight (Modul 4).

## Decision

**The app timezone stays UTC. Days, months and due dates are
computed in Malaysian time, then converted to a UTC range.**

- Start from `now('Asia/Kuala_Lumpur')`; no `today()` or
  `whereDate()` on time columns.
- Every scheduled task that follows local time has
  `->timezone('Asia/Kuala_Lumpur')`.
- Every daily report has a test at 7:30 am Malaysian time
  using `Carbon::setTestNow()`.

## Revisit when

The system has to serve users outside UTC+8.
