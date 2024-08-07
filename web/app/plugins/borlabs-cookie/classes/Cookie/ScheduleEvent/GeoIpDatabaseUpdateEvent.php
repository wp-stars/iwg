<?php
/*
 *  Copyright (c) 2024 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\ScheduleEvent;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\System\GeoIp\GeoIp;
use Exception;

final class GeoIpDatabaseUpdateEvent implements ScheduleEventInterface
{
    public const EVENT_NAME = 'GeoIpDatabaseUpdate';

    private GeoIp $geoIp;

    private WpFunction $wpFunction;

    public function __construct(
        GeoIp $geoIp,
        WpFunction $wpFunction
    ) {
        $this->geoIp = $geoIp;
        $this->wpFunction = $wpFunction;
    }

    public function deregister(): void
    {
        $this->wpFunction->wpClearScheduledHook(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME);
    }

    public function register(): void
    {
        $this->wpFunction->addAction(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME, [$this, 'run']);

        if (!$this->wpFunction->wpNextScheduled(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME)) {
            $this->wpFunction->wpScheduleEvent(
                time(),
                'daily',
                ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME,
            );
        }
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->geoIp->downloadGeoIpDatabase();
    }
}
