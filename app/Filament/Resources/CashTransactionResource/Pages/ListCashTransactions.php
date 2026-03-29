<?php

namespace App\Filament\Resources\CashTransactionResource\Pages;

use App\Filament\Resources\CashTransactionResource;
use App\Exports\CashLedgerExport;
use App\Services\CashLedgerReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ListCashTransactions extends ListRecords
{
    protected static string $resource = CashTransactionResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (static::getResource()::canCreate()) {
            $actions[] = CreateAction::make()->modal();
        }

        $actions[] = Action::make('export')
            ->label('Export')
            ->form([
                Select::make('format')
                    ->label('Format')
                    ->options([
                        'pdf' => 'PDF',
                        'excel' => 'Excel',
                    ])
                    ->required(),
                Select::make('period')
                    ->label('Periode')
                    ->options([
                        'daily' => 'Harian',
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                        'yearly' => 'Tahunan',
                    ])
                    ->required()
                    ->reactive(),
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->default(now())
                    ->required(fn (callable $get): bool => $get('period') === 'daily')
                    ->visible(fn (callable $get): bool => $get('period') === 'daily'),
                DatePicker::make('week_start')
                    ->label('Mulai Minggu')
                    ->helperText('Rentang 7 hari dari tanggal ini.')
                    ->default(now())
                    ->required(fn (callable $get): bool => $get('period') === 'weekly')
                    ->visible(fn (callable $get): bool => $get('period') === 'weekly'),
                Select::make('month')
                    ->label('Bulan')
                    ->options([
                        '1' => 'Januari',
                        '2' => 'Februari',
                        '3' => 'Maret',
                        '4' => 'April',
                        '5' => 'Mei',
                        '6' => 'Juni',
                        '7' => 'Juli',
                        '8' => 'Agustus',
                        '9' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ])
                    ->default((string) now()->month)
                    ->required(fn (callable $get): bool => $get('period') === 'monthly')
                    ->visible(fn (callable $get): bool => $get('period') === 'monthly'),
                Select::make('year')
                    ->label('Tahun')
                    ->options(function () {
                        $current = (int) now()->year;
                        $years = [];
                        for ($y = $current; $y >= ($current - 10); $y--) {
                            $years[(string) $y] = (string) $y;
                        }

                        return $years;
                    })
                    ->default((string) now()->year)
                    ->required(fn (callable $get): bool => in_array($get('period'), ['monthly', 'yearly'], true))
                    ->visible(fn (callable $get): bool => in_array($get('period'), ['monthly', 'yearly'], true)),
            ])
            ->action(function (array $data) {
                /** @var CashLedgerReportService $service */
                $service = app(CashLedgerReportService::class);

                $period = $data['period'];

                [$start, $end] = match ($period) {
                    'daily' => [
                        Carbon::parse($data['date'])->startOfDay(),
                        Carbon::parse($data['date'])->endOfDay(),
                    ],
                    'weekly' => [
                        Carbon::parse($data['week_start'])->startOfDay(),
                        Carbon::parse($data['week_start'])->addDays(6)->endOfDay(),
                    ],
                    'monthly' => [
                        Carbon::createFromDate((int) $data['year'], (int) $data['month'], 1)->startOfMonth(),
                        Carbon::createFromDate((int) $data['year'], (int) $data['month'], 1)->endOfMonth(),
                    ],
                    'yearly' => [
                        Carbon::createFromDate((int) $data['year'], 1, 1)->startOfYear(),
                        Carbon::createFromDate((int) $data['year'], 1, 1)->endOfYear(),
                    ],
                    default => [now()->startOfDay(), now()->endOfDay()],
                };

                $companyName = (string) config('company.name');
                $logoRelative = ltrim((string) config('company.logo'), "\\/\t\n\r\0\x0B");
                $logoPath = public_path($logoRelative);
                $logoPath = is_file($logoPath) ? $logoPath : null;

                $logoFileUri = null;
                if ($logoPath) {
                    $normalized = str_replace('\\', '/', $logoPath);
                    $logoFileUri = 'file:///' . ltrim($normalized, '/');
                }

                $logoDataUri = null;
                if ($logoPath) {
                    $mime = (string) (@mime_content_type($logoPath) ?: '');

                    $supportedMimes = [
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                        'image/gif',
                        'image/svg+xml',
                    ];

                    if ($mime !== '' && ! in_array($mime, $supportedMimes, true)) {
                        Notification::make()
                            ->title('Logo tidak didukung untuk PDF')
                            ->body("Logo terdeteksi sebagai {$mime}. Ubah logo menjadi PNG atau JPG agar tampil di PDF.")
                            ->warning()
                            ->send();
                    } else {
                        $contents = @file_get_contents($logoPath);
                        if ($contents !== false && $contents !== '') {
                            $mime = $mime !== '' ? $mime : 'image/png';
                            $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode($contents);
                        }
                    }
                }

                $title = 'Laporan Buku Besar Kas';
                $periodLabel = $service->makePeriodLabel($start, $end, $period);

                $report = $service->build($start, $end);

                $payload = [
                    'companyName' => $companyName,
                    'logoPath' => $logoPath,
                    'logoDataUri' => $logoDataUri,
                    'logoFileUri' => $logoFileUri,
                    'title' => $title,
                    'periodLabel' => $periodLabel,
                    'openingBalance' => $report['openingBalance'],
                    'rows' => $report['rows'],
                ];

                $fileSuffix = match ($period) {
                    'daily' => $start->format('Y-m-d'),
                    'weekly' => $start->format('Y-m-d') . '_to_' . $end->format('Y-m-d'),
                    'monthly' => $start->format('Y-m'),
                    'yearly' => $start->format('Y'),
                    default => now()->format('Y-m-d_His'),
                };

                if (($data['format'] ?? 'pdf') === 'excel') {
                    return Excel::download(
                        new CashLedgerExport($payload),
                        "buku-besar-kas_{$fileSuffix}.xlsx",
                    );
                }

                $pdf = Pdf::loadView('reports.cash-ledger-pdf', $payload);
                $fileName = "buku-besar-kas_{$fileSuffix}.pdf";

                return response()->streamDownload(
                    function () use ($pdf): void {
                        echo $pdf->output();
                    },
                    $fileName,
                    ['Content-Type' => 'application/pdf'],
                );
            });

        return $actions;
    }
}
