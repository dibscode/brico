<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CashLedgerExport implements FromView, ShouldAutoSize
{
    /**
     * @param  array{companyName:string,title:string,periodLabel:string,openingBalance:float,rows:array<int, array<string,mixed>>,logoPath:?string}  $payload
     */
    public function __construct(private array $payload)
    {
    }

    public function view(): View
    {
        return view('exports.cash-ledger-excel', [
            'companyName' => $this->payload['companyName'],
            'title' => $this->payload['title'],
            'periodLabel' => $this->payload['periodLabel'],
            'openingBalance' => $this->payload['openingBalance'],
            'rows' => $this->payload['rows'],
        ]);
    }
}
