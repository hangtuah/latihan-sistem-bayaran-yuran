Invois {{ $invoice->number }} berjumlah RM {{ number_format($invoice->amount, 2) }}
telah lewat 7 hari dari tarikh akhir {{ $invoice->due_date->format('d/m/Y') }}.

Sila jelaskan bayaran melalui aplikasi atau di kaunter Pejabat Bendahari.
Penalti lewat mungkin dikenakan.

Pejabat Bendahari
