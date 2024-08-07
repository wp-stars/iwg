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

namespace Borlabs\Cookie\DtoList\CloudScan;

use Borlabs\Cookie\Dto\CloudScan\ExternalResourceDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<ExternalResourceDto>
 */
final class ExternalResourceDtoList extends AbstractDtoList
{
    public const DTO_CLASS = ExternalResourceDto::class;

    public function __construct(
        ?array $externalResourceList = null
    ) {
        parent::__construct($externalResourceList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $externalResourceData) {
            $externalResource = new ExternalResourceDto(
                $externalResourceData->hostname,
                $externalResourceData->examples,
                $externalResourceData->packageKey,
            );
            $list[$key] = $externalResource;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $externalResources) {
            $list[$key] = ExternalResourceDto::prepareForJson($externalResources);
        }

        return $list;
    }
}
