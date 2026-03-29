<?php
    $livewire ??= null;

    $companyName = config('company.name') ?: config('app.name');
    $headerLogoPath = public_path('logowhite.png');
    $headerLogoUrl = asset('logowhite.png');
    $hasHeaderLogo = file_exists($headerLogoPath);

    $fallbackLogoPath = public_path(config('company.logo', 'logobrico.png'));
    $fallbackLogoUrl = asset(config('company.logo', 'logobrico.png'));
    $hasFallbackLogo = file_exists($fallbackLogoPath);

    $illustrationPath = public_path('ilustrasibrico.png');
    $illustrationUrl = asset('ilustrasibrico.png');
    $hasIllustration = file_exists($illustrationPath);
?>

<?php if (isset($component)) { $__componentOriginale960ae7ad1b1ce9e3596e483505fadc9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale960ae7ad1b1ce9e3596e483505fadc9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.layout.base','data' => ['livewire' => $livewire]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::layout.base'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['livewire' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($livewire)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="brico-login-page">
        <div class="brico-login-shell">
            <div class="brico-login-surface">
                <div class="brico-login-hero" style="background:#1976D2;">
                    <div class="brico-login-hero-logo">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasHeaderLogo): ?>
                            <img src="<?php echo e($headerLogoUrl); ?>" alt="<?php echo e($companyName); ?>" style="height: 32px; max-height: 32px; width: auto;" />
                        <?php elseif($hasFallbackLogo): ?>
                            <img src="<?php echo e($fallbackLogoUrl); ?>" alt="<?php echo e($companyName); ?>" style="height: 32px; max-height: 32px; width: auto;" />
                        <?php else: ?>
                            <p class="brico-login-hero-text"><?php echo e($companyName); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="brico-login-hero-copy">
                        <p class="brico-login-hero-title"><?php echo e($companyName); ?></p>
                        <p class="brico-login-hero-subtitle">Masuk untuk mengelola data koperasi</p>
                    </div>

                    <div class="brico-login-hero-illustration">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasIllustration): ?>
                            <img
                                src="<?php echo e($illustrationUrl); ?>"
                                alt="Ilustrasi"
                                class="brico-login-illustration-img"
                            />
                        <?php else: ?>
                            <svg viewBox="0 0 360 270" class="brico-login-illustration-img" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M44 180c28-60 72-90 136-90 66 0 110 32 136 90" stroke="#60A5FA" stroke-width="18" stroke-linecap="round" opacity="0.55"/>
                                <path d="M78 206c18-36 48-54 102-54 56 0 84 20 102 54" stroke="#3B82F6" stroke-width="18" stroke-linecap="round" opacity="0.35"/>
                                <rect x="150" y="78" width="86" height="128" rx="18" fill="#0EA5E9" opacity="0.18"/>
                                <rect x="162" y="90" width="62" height="104" rx="14" fill="#FFFFFF"/>
                            </svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <svg class="brico-login-hero-arc" viewBox="0 0 1440 140" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                        <path d="M0,40 C360,160 1080,160 1440,40 L1440,140 L0,140 Z" fill="#ffffff"></path>
                    </svg>
                </div>

                <div class="brico-login-content">
                    <div class="brico-login-content-inner">
                        <h1 class="brico-login-desktop-heading">Masuk BRICo</h1>
                        <?php echo e($slot); ?>

                    </div>

                    <p class="brico-login-footer">
                        <?php echo e(now()->year); ?> &middot; <?php echo e($companyName); ?>

                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fi-simple-page {
            padding: 0 !important;
        }

        .fi-simple-main {
            padding: 0 !important;
        }

        .fi-simple-page .fi-section {
            box-shadow: none !important;
            border: 0 !important;
            background: transparent !important;
        }

        .fi-simple-page .fi-section-content {
            padding: 0 !important;
        }

        .fi-simple-page .fi-fo-component-ctn,
        .fi-simple-page .fi-fo-field-wrp {
            margin-top: 0 !important;
        }

        .fi-simple-page .fi-input {
            border-radius: 0.75rem !important;
            padding-top: 0.85rem !important;
            padding-bottom: 0.85rem !important;
        }

        .fi-simple-page .fi-input-wrp {
            border-radius: 0.75rem !important;
        }

        .fi-simple-page .fi-input-wrp-prefix,
        .fi-simple-page .fi-input-wrp-suffix {
            color: rgb(148 163 184) !important;
        }

        .fi-simple-page .fi-ac {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }

        .fi-login-forgot-link {
            display: block !important;
            width: 100% !important;
            text-align: center !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            color: #1976D2 !important;
            text-decoration: none !important;
            padding: 0.25rem 0 !important;
        }

        .fi-btn-login {
            width: 100% !important;
            min-height: 48px !important;
            border-radius: 12px !important;
            background-color: #1976D2 !important;
            color: rgb(255 255 255) !important;
        }

        .fi-btn-login:hover {
            background-color: #1565C0 !important;
        }

        .brico-login-page {
            min-height: 100dvh;
            background: #ffffff;
        }

        .brico-login-shell {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }

        @media (max-width: 767.98px) {
            .brico-login-shell {
                max-width: 100%;
            }
        }

        .brico-login-surface {
            background: #ffffff;
            overflow: hidden;
        }

        .brico-login-hero {
            position: relative;
            padding-top: 34px;
            padding-bottom: 6px;
            /* overflow: hidden; */
        }

        .brico-login-hero-logo {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .brico-login-hero-copy {
            margin-top: 14px;
            padding: 0 20px;
            text-align: center;
            color: #ffffff;
        }

        .brico-login-hero-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.2;
        }

        .brico-login-hero-subtitle {
            margin: 6px 0 0;
            font-size: 13px;
            font-weight: 500;
            opacity: 0.95;
        }

        .brico-login-hero-text {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
        }

        .brico-login-hero-illustration {
            width: 100%;
            height: 250px;
            margin-top: 18px;
            position: relative;
            z-index: 2;
        }

        .brico-login-illustration-img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            object-position: center top;
        }

        .brico-login-hero-arc {
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            width: 100%;
            height: 88px;
            display: block;
            pointer-events: none;
            z-index: 3;
        }

        .brico-login-content {
            padding: 18px 20px 28px;
            background: #ffffff;
        }

        .brico-login-content-inner {
            width: 100%;
        }

        .brico-login-desktop-heading {
            display: none;
        }

        .brico-login-footer {
            margin-top: 18px;
            text-align: center;
            font-size: 12px;
            color: rgb(148 163 184);
        }

        @media (min-width: 768px) {
            .brico-login-page {
                background: #ffffff;
                padding: 0;
                display: flex;
                align-items: stretch;
                justify-content: stretch;
            }

            .brico-login-shell {
                max-width: none;
                margin: 0;
                display: flex;
                align-items: stretch;
            }

            .brico-login-surface {
                display: flex;
                border-radius: 0;
                border: 0;
                background: #ffffff;
                width: 100%;
                min-height: 100vh;
            }

            .brico-login-hero {
                width: 50%;
                padding-top: 42px;
                padding-bottom: 42px;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
            }

            .brico-login-hero-logo {
                justify-content: flex-start;
                padding: 0 34px;
            }

            .brico-login-hero-copy {
                padding: 0 34px;
                text-align: left;
                max-width: 420px;
            }

            .brico-login-hero-title {
                font-size: 22px;
            }

            .brico-login-hero-illustration {
                height: 1px;
                flex: 1;
                margin-top: 22px;
                padding: 0 34px 0 34px;
            }

            .brico-login-illustration-img {
                border-radius: 0;
                object-fit: contain;
                object-position: center;
            }

            .brico-login-hero-arc {
                display: none;
            }

            .brico-login-content {
                width: 50%;
                padding: 0;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .brico-login-content-inner {
                width: 100%;
                max-width: 520px;
                margin: 0 auto;
                padding: 0 56px;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .brico-login-desktop-heading {
                display: block;
                margin: 0 0 18px;
                font-size: 22px;
                font-weight: 700;
                color: rgb(15 23 42);
            }

            .brico-login-footer {
                margin: 0;
                padding: 18px 56px 22px;
                text-align: center;
            }

            .fi-simple-main {
                padding: 0 !important;
            }
        }
    </style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale960ae7ad1b1ce9e3596e483505fadc9)): ?>
<?php $attributes = $__attributesOriginale960ae7ad1b1ce9e3596e483505fadc9; ?>
<?php unset($__attributesOriginale960ae7ad1b1ce9e3596e483505fadc9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale960ae7ad1b1ce9e3596e483505fadc9)): ?>
<?php $component = $__componentOriginale960ae7ad1b1ce9e3596e483505fadc9; ?>
<?php unset($__componentOriginale960ae7ad1b1ce9e3596e483505fadc9); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\koperasibri\resources\views/filament/layouts/mobile-login.blade.php ENDPATH**/ ?>