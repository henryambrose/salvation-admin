<?php

return [
    App\Providers\EventServiceProvider::class,
    Modules\Members\Providers\AppServiceProvider::class,
    Modules\Members\Providers\AuthServiceProvider::class,
    Modules\Members\Providers\ModuleServiceProvider::class,
    Modules\Fund\Providers\ModuleServiceProvider::class,
    Modules\Graveyard\Providers\ModuleServiceProvider::class,
];
