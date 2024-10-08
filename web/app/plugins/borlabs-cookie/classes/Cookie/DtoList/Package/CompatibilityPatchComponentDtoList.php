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

namespace Borlabs\Cookie\DtoList\Package;

use Borlabs\Cookie\Dto\Package\CompatibilityPatchComponentDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<CompatibilityPatchComponentDto>
 */
final class CompatibilityPatchComponentDtoList extends AbstractDtoList
{
    public const DTO_CLASS = CompatibilityPatchComponentDto::class;

    public const UNIQUE_PROPERTY = 'key';

    public function __construct(
        ?array $componentList = null
    ) {
        parent::__construct($componentList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $componentData) {
            $compatibilityPatchComponent = new CompatibilityPatchComponentDto(
                $componentData->key,
                $componentData->downloadUrl,
                $componentData->hash,
            );
            $list[$key] = $compatibilityPatchComponent;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $compatibilityPatchComponent) {
            $list[$key] = CompatibilityPatchComponentDto::prepareForJson($compatibilityPatchComponent);
        }

        return $list;
    }
}
