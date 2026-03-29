<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use App\Models\Loan;
use App\Services\CashTransactionService;
use App\Services\LoanScheduleService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ViewLoan extends ViewRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve')
                ->requiresConfirmation()
                ->visible(fn () => Auth::user()?->isAdmin() && $this->record->status === Loan::STATUS_PENDING)
                ->action(function (LoanScheduleService $scheduleService, CashTransactionService $cashService): void {
                    /** @var Loan $loan */
                    $loan = $this->record;

                    DB::transaction(function () use ($loan, $scheduleService, $cashService): void {
                        $now = now();

                        $loan->approved_at = $now;
                        $loan->approved_by = Auth::id();
                        $loan->status = Loan::STATUS_ACTIVE;

                        if (! $loan->disbursed_at) {
                            $loan->disbursed_at = $now;
                            $loan->disbursed_by = Auth::id();
                        }

                        $loan->save();

                        if (! $loan->installments()->exists()) {
                            $scheduleService->generateInstallments($loan);
                        }

                        if ($loan->disbursed_at && $loan->disbursed_by) {
                            $memberName = $loan->member()->value('name');
                            $loanLabel = $loan->loan_code ?: ('#' . $loan->id);
                            $description = $memberName
                                ? "Pencairan pinjaman {$loanLabel} ({$memberName})"
                                : "Pencairan pinjaman {$loanLabel}";

                            $cashService->recordExpense(
                                categoryName: 'Pencairan Pinjaman',
                                amount: (float) $loan->principal,
                                occurredAt: $loan->disbursed_at,
                                description: $description,
                                referenceType: Loan::class,
                                referenceId: $loan->id,
                                createdBy: $loan->disbursed_by,
                            );
                        }
                    });

                    Notification::make()->success()->title('Pinjaman disetujui & dicairkan')->send();

                    $this->refreshFormData(['status', 'approved_at', 'disbursed_at']);

                    $this->dispatch('refreshRelationManagers');

                    $this->redirect(LoanResource::getUrl('view', ['record' => $loan]));
                }),

            Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->visible(fn () => Auth::user()?->isAdmin() && $this->record->status === Loan::STATUS_PENDING)
                ->form([
                    Textarea::make('rejection_note')
                        ->label('Catatan Ditolak')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    /** @var Loan $loan */
                    $loan = $this->record;

                    $loan->rejected_at = now();
                    $loan->rejected_by = Auth::id();
                    $loan->rejection_note = $data['rejection_note'] ?? null;
                    $loan->status = Loan::STATUS_REJECTED;
                    $loan->save();

                    Notification::make()->success()->title('Pinjaman ditolak')->send();

                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
