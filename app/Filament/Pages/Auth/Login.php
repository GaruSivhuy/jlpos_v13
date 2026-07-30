<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    /**
     * Point this at your own Blade view if you copied/customized it.
     * Remove this property entirely if you just want to keep the
     * default Filament layout and only tweak the form fields below.
     */
    protected string $view = 'filament.pages.auth.login';

    /**
     * Customize the form schema (fields shown on the login page).
     * This overrides the parent's default: email, password, remember me.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                // $this->getRememberFormComponent(),
            ]);
    }

    /**
     * Example: customize the email field (label, placeholder, autofocus, etc.)
     */
    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->label(__("auth.email"))
            ->autofocus();
    }

    /**
     * Example: customize the password field.
     */
    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->label(__("auth.passwords"));
    }

    /**
     * Example: customize the "remember me" checkbox.
     */
    protected function getRememberFormComponent(): Component
    {
        return parent::getRememberFormComponent()
            ->label('Keep me signed in');
    }

    public function getHeading(): string | Htmlable | null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return __('filament-panels::auth/pages/login.multi_factor.heading');
        }

        return __("auth.login_title");
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__("auth.login"))
            ->submit('authenticate');
    }
}