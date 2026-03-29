@php
	use Filament\Pages\Dashboard;
	use App\Filament\Resources\MemberResource;
	use App\Filament\Resources\SavingResource;
	use App\Filament\Resources\LoanResource;
	use App\Filament\Resources\CashTransactionResource;

	if (! filament()->auth()->check()) {
		return;
	}

	$panelId = filament()->getCurrentOrDefaultPanel()?->getId() ?? 'admin';

	$items = [
		[
			'label' => 'Beranda',
			'icon' => 'bi bi-house-door-fill',
			'url' => Dashboard::getUrl(panel: $panelId, isAbsolute: false),
			'active' => request()->is('admin') || request()->routeIs('filament.' . $panelId . '.pages.dashboard'),
		],
		[
			'label' => 'Anggota',
			'icon' => 'bi bi-people-fill',
			'url' => MemberResource::getUrl('index', panel: $panelId, isAbsolute: false),
			'active' => request()->is('admin/members*'),
		],
		[
			'label' => 'Simpanan',
			'icon' => 'bi bi-piggy-bank-fill',
			'url' => SavingResource::getUrl('index', panel: $panelId, isAbsolute: false),
			'active' => request()->is('admin/savings*'),
		],
		[
			'label' => 'Pinjaman',
			'icon' => 'bi bi-cash-stack',
			'url' => LoanResource::getUrl('index', panel: $panelId, isAbsolute: false),
			'active' => request()->is('admin/loans*'),
		],
		[
			'label' => 'Kas',
			'icon' => 'bi bi-wallet2',
			'url' => CashTransactionResource::getUrl('index', panel: $panelId, isAbsolute: false),
			'active' => request()->is('admin/cash-transactions*') || request()->is('admin/cash-categories*'),
		],
	];
@endphp

<nav class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 md:hidden">
	<div class="mx-auto grid max-w-md grid-cols-5 px-2 pb-[max(env(safe-area-inset-bottom),0px)] pt-2">
		@foreach ($items as $item)
			<a href="{{ $item['url'] }}"
			   class="group flex flex-col items-center justify-center gap-1.5 rounded-xl px-2 py-2 text-[11px] font-medium transition"
			>
				<i class="{{ $item['icon'] }} text-2xl leading-none transition {{ $item['active'] ? 'text-blue-800' : 'text-slate-500 group-hover:text-slate-900' }}" aria-hidden="true"></i>
				<span class="transition {{ $item['active'] ? 'text-blue-800' : 'text-slate-600 group-hover:text-slate-900' }}">
					{{ $item['label'] }}
				</span>
			</a>
		@endforeach
	</div>
</nav>

<style>
	@media (max-width: 767.98px) {
		.fi-main {
			padding-bottom: 5.5rem;
		}
	}
</style>
