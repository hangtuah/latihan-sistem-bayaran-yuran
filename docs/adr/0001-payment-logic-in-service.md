# 0001 — Payment logic lives in PaymentService, not in controllers

**Status:** accepted, 2026-11-27

## Context

The original `PaymentController` validated input, called the bank, saved
the payment, marked the invoice paid and sent the receipt in one method.
The counter import (`payments:import`) needed the same steps and copied
them. A change to the late penalty had to be made in two places, and the
controller could only be tested through HTTP with every gateway faked.

## Decision

**Every payment goes through `App\Services\PaymentService::pay()`.**
Controllers, commands and jobs call the service; they do not touch
`Payment::create()` or a gateway directly.

- Controllers validate with a FormRequest, call the service, return a response.
- Gateways implement `App\Payments\PaymentGateway` and are chosen by
  `PaymentProcessor`; adding a method means a new class, not a new `if`.
- Data access goes through `InvoiceRepository`.

## Consequences

One place to change payment rules, and unit tests without HTTP. More
files to read for a new developer.

## Revisit when

A second, unrelated payment flow (for example refunds) needs steps the
service cannot share.
