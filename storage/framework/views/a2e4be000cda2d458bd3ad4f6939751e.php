<?php
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
?>

<nav class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 md:hidden">
    <div class="mx-auto grid max-w-md grid-cols-5 px-2 pb-[max(env(safe-area-inset-bottom),0px)] pt-2">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <a href="<?php echo e($item['url']); ?>"
               class="group flex flex-col items-center justify-center gap-1.5 rounded-xl px-2 py-2 text-[11px] font-medium transition"
            >
                <i class="<?php echo e($item['icon']); ?> text-2xl leading-none transition <?php echo e($item['active'] ? 'text-blue-800' : 'text-slate-500 group-hover:text-slate-900'); ?>" aria-hidden="true"></i>
                <span class="transition <?php echo e($item['active'] ? 'text-blue-800' : 'text-slate-600 group-hover:text-slate-900'); ?>">
                    <?php echo e($item['label']); ?>

                </span>
            </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</nav>

<style>
    @media (max-width: 767.98px) {
        .fi-main {
            padding-bottom: 5.5rem;
        }
    }
</style>
<?php /**PATH C:\laragon\www\koperasibri\resources\views/filament/components/mobile-bottom-nav.blade.php ENDPATH**/ ?>