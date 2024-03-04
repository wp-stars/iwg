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

namespace Borlabs\Cookie\System\Installer\IabTcf;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Repository\IabTcf\VendorRepository;

final class VendorUpgrade
{
    private VendorInstall $vendorInstall;

    private WpDb $wpdb;

    public function __construct(
        VendorInstall $vendorInstall,
        WpDb $wpdb
    ) {
        $this->vendorInstall = $vendorInstall;
        $this->wpdb = $wpdb;
    }

    public function upgrade(string $prefix = ''): bool
    {
        $tableName = $prefix . VendorRepository::TABLE;

        return true;
    }
}