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

namespace Borlabs\Cookie\Localization\ContentBlocker;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ServiceCreateLocalizationStrings** class contains various localized strings.
 */
final class ContentBlockerLocationCreateEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noLocationConfigured' => _x(
                    'No <translation-key id="Location">Location</translation-key> configured.',
                    'Backend / Content Blocker Location / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'addLocation' => _x(
                    'Add Location',
                    'Backend / Content Blocker Location / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'hostname' => _x(
                    'Hostname',
                    'Backend / Content Blocker Location / Label',
                    'borlabs-cookie',
                ),
                'path' => _x(
                    'Path',
                    'Backend / Content Blocker Location / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'locations' => _x(
                    'Locations',
                    'Backend / Content Blocker Location / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'hostname' => _x(
                    '<translation-key id="Hostname">Hostname</translation-key>',
                    'Backend / Content Blocker Location / Table Headline',
                    'borlabs-cookie',
                ),
                'path' => _x(
                    '<translation-key id="Path">Path</translation-key>',
                    'Backend / Content Blocker Location / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineHostnameExplained' => _x(
                    'What is a <translation-key id="Hostname">Hostname</translation-key>?',
                    'Backend / Content Blocker Location / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineLocationsExplained' => _x(
                    'What is the purpose of the <translation-key id="Locations">Locations</translation-key> section?',
                    'Backend / Content Blocker Location / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'hostnameExplained' => _x(
                    'The <translation-key id="Hostname">Hostname</translation-key> is the domain name of the website. For example, if the URL of the website is <strong><em>https://www.example.com</em></strong>, the <translation-key id="Hostname">Hostname</translation-key> is <strong><em>www.example.com</em></strong>.',
                    'Backend / Content Blocker Location / Things to know / Text',
                    'borlabs-cookie',
                ),
                'locationsExplained' => _x(
                    'If a <translation-key id="Hostname">Hostname</translation-key> and <translation-key id="Path">Path</translation-key> configured here are found in an iframe, the iframe is blocked with the settings of this <translation-key id="Content-Blocker">Content Blocker</translation-key>.',
                    'Backend / Content Blocker Location / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
