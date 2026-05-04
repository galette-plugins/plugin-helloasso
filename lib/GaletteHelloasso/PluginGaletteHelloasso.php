<?php

/**
 * This file is part of Galette Helloasso plugin (https://galette-community.github.io/plugin-helloasso).
 * SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace GaletteHelloasso;

use DI\Attribute\Inject;
use Galette\Core\Db;
use Galette\Core\Login;
use Galette\Core\Plugins\DashboardProviderInterface;
use Galette\Core\Plugins\MenuProviderInterface;
use Galette\Core\Preferences;
use Galette\Core\GalettePlugin;

/**
 * Galette HelloAsso plugin
 *
 * @author Johan Cwiklinski <johan@x-tnd.be>
 * @author Guillaume AGNIERAY <dev@agnieray.net>
 */

class PluginGaletteHelloasso extends GalettePlugin implements MenuProviderInterface, DashboardProviderInterface
{
    #[Inject]
    private readonly Db $zdb; //@phpstan-ignore-line injected from DI

    /**
     * Get plugins menus
     *
     * @return array<string, string|array<string,mixed>>
     */
    public function getMenus(): array
    {
        /**
         * @var Login $login
         */
        global $login;
        $content = [
            'title' => _T("Helloasso", "helloasso"),
            'icon' => 'helloasso'
        ];
        $content['items'] = [];

        if ($login->isAdmin() || $login->isStaff()) {
            $content['items'] = [
                [
                    'label' => _T("Helloasso History", "helloasso"),
                    'route' => [
                        'name' => 'helloasso_history'
                    ]
                ],
                [
                    'label' => _T("Settings"),
                    'route' => [
                        'name' => 'helloasso_preferences'
                    ]
                ]
            ];
        }

        $menus['plugin_helloasso'] = $content;
        return $menus;
    }

    /**
     * Get plugins public menus
     *
     * @return array<int, string|array<string,mixed>>
     */
    public function getPublicMenus(): array
    {
        return [
            [
                'label' => _T("Payment form", "helloasso"),
                'route' => [
                    'name' => 'helloasso_form'
                ],
                'icon' => 'helloasso'
            ]
        ];
    }

    /**
     * Get plugins dashboards
     *
     * @return array<int, string|array<string,mixed>>
     */
    public function getDashboards(): array
    {
        /** @var Login $login */
        global $login;
        /** @var Preferences $preferences */
        global $preferences;
        $contents = [];

        if ($preferences->showPublicPage($login, 'pref_publicpages_visibility_generic')) {
            $contents[] = [
                'label' => _T("Payment form", "helloasso"),
                'route' => [
                    'name' => 'helloasso_form'
                ],
                'icon' => 'helloasso'
            ];
        }
        return $contents;
    }

    /**
     * Get current logged-in user plugins dashboards
     *
     * @return array<int, string|array<string,mixed>>
     */
    public function getMyDashboards(): array
    {
        return [];
    }

    /**
     * Is the plugin fully installed (including database, extra configuration, etc.)?
     */
    public function isInstalled(): bool
    {
        return
            $this->zdb->tableExists(HELLOASSO_PREFIX . Helloasso::TABLE)
            && $this->zdb->tableExists(HELLOASSO_PREFIX . Helloasso::TABLE_TOKENS)
            && $this->zdb->tableExists(HELLOASSO_PREFIX . HelloassoHistory::TABLE)
        ;
    }
}
