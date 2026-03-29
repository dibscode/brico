<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DibscodeInfoWidget extends Widget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.dibscode-info-widget';
}
