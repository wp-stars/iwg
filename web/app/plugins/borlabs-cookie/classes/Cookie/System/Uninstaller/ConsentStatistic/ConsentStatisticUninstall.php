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

namespace Borlabs\Cookie\System\Uninstaller\ConsentStatistic;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Repository\ConsentStatistic\ConsentStatisticByDayRepository;
use Borlabs\Cookie\Repository\ConsentStatistic\ConsentStatisticByHourRepository;

final class ConsentStatisticUninstall
{
    private WpDb $wpdb;

    public function __construct(
        WpDb $wpdb
    ) {
        $this->wpdb = $wpdb;
    }

    public function uninstall(string $prefix = ''): bool
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }

        return
            $this->wpdb->query('DROP TABLE IF EXISTS ' . $prefix . ConsentStatisticByDayRepository::TABLE)
            && $this->wpdb->query('DROP TABLE IF EXISTS ' . $prefix . ConsentStatisticByHourRepository::TABLE);
    }
}