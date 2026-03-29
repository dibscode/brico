<?php

namespace App\Filament\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class MobileLogin extends Login
{
    protected static string $layout = 'filament.layouts.mobile-login';

    public function getHeading(): string | Htmlable | null
    {
        return null;
    }

    public function getSubheading(): string | Htmlable | null
    {
        return null;
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
            ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->hiddenLabel()
            ->placeholder('Username')
            ->prefixIcon('heroicon-m-user')
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->hiddenLabel()
            ->placeholder('Password')
            ->prefixIcon('heroicon-m-lock-closed')
            ->password()
            ->revealable(true)
            ->autocomplete('current-password')
            ->required();
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        $actions = [];

        if (filament()->hasPasswordReset()) {
            $actions[] = Action::make('requestPasswordReset')
                ->label('Lupa Username/Password?')
                ->link()
                ->url(filament()->getRequestPasswordResetUrl())
                ->extraAttributes([
                    'class' => 'fi-login-forgot-link',
                ]);
        }

        $actions[] = $this->getAuthenticateFormAction();

        return $actions;
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Login')
            ->color('primary')
            ->extraAttributes([
                'class' => 'fi-btn-login w-full !rounded-xl !px-4 !py-3 !font-semibold',
            ]);
    }
}
