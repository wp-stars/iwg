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

namespace Borlabs\Cookie\Dto\Telemetry;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\Telemetry\ContentBlockerDtoList;
use Borlabs\Cookie\DtoList\Telemetry\PluginDtoList;
use Borlabs\Cookie\DtoList\Telemetry\ServiceDtoList;
use Borlabs\Cookie\DtoList\Telemetry\ThemeDtoList;

class TelemetryDto extends AbstractDto
{
    public ContentBlockerDtoList $borlabsCookieContentBlockers;

    public ServiceDtoList $borlabsCookieServices;

    public SettingsDto $borlabsCookieSettings;

    public PluginDtoList $plugins;

    public ThemeDtoList $themes;
}
