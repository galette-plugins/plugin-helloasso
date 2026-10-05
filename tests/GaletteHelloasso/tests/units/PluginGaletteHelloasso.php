<?php

/**
 * This file is part of Galette Helloasso plugin (https://galette-plugins.github.io/plugin-helloasso).
 * SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace GaletteHelloasso\tests\units;

use Galette\Tests\GaletteTestCase;

/**
 * Helloasso plugin tests
 *
 * @author Johan Cwiklinski <johan@x-tnd.be>
 * @author Guillaume AGNIERAY <dev@agnieray.net>
 */
class PluginGaletteHelloasso extends GaletteTestCase
{
    protected int $seed = 20260928091512;

    /**
     * Cleanup after each test method
     */
    public function tearDown(): void
    {
        $this->login->logout();
        parent::tearDown();
    }

    /**
     * Get menu items routes
     *
     * @return array<string>
     */
    private function getMenuRoutes(): array
    {
        $plugin = $this->container->get(\GaletteHelloasso\PluginGaletteHelloasso::class);
        $menus = $plugin->getMenus();
        return array_map(
            fn($item) => $item['route']['name'],
            $menus['plugin_helloasso']['items'] ?? []
        );
    }

    /**
     * Test menus by profile
     */
    public function testGetMenus(): void
    {
        $this->logSuperAdmin();
        $this->assertSame(
            [
                'helloasso_history',
                'helloasso_preferences',
            ],
            $this->getMenuRoutes()
        );
        $this->login->logout();

        $member = $this->getMemberOne();
        $mdata = $this->dataAdherentOne();
        $this->assertTrue($this->login->login($mdata['login_adh'], $mdata['mdp_adh']));
        $this->assertSame($mdata['login_adh'], $member->login);
        $this->assertSame([], $this->getMenuRoutes());
    }

    /**
     * The public form is declared to the core, with a visibility of its own
     */
    public function testPublicPage(): void
    {
        $name = 'pref_helloasso_publicpages_visibility_form';
        $plugin = $this->container->get(\GaletteHelloasso\PluginGaletteHelloasso::class);

        $this->assertSame(['form' => ['routes' => ['helloasso_form']]], $plugin->getPublicPages());
        $this->assertSame('Payment form', $plugin->getPublicPageLabel('helloasso_form'));
        $this->assertTrue(\Galette\Core\PreferencesSchema::isPublicPage($name));
        $this->assertSame($name, \Galette\Core\PreferencesSchema::getPublicPageRight('helloasso_form'));
        $this->assertSame(
            \Galette\Enums\PublicPageVisibility::Inherit->value,
            \Galette\Core\PreferencesSchema::get($name)['default']
        );

        //the public menu entry follows it, not the default visibility
        $this->setRawPreference('pref_bool_publicpages', true);
        $this->setRawPreference(
            'pref_publicpages_visibility_generic',
            \Galette\Enums\PublicPageVisibility::Hidden->value
        );
        $this->setRawPreference($name, \Galette\Enums\PublicPageVisibility::Everyone->value);
        $this->assertSame(['helloasso_form'], array_map(
            fn($item) => $item['route']['name'],
            $plugin->getPublicMenuItems()
        ));

        $this->setRawPreference($name, \Galette\Enums\PublicPageVisibility::Inherit->value);
        $this->assertSame([], $plugin->getPublicMenuItems());
    }

    /**
     * Current tables are not legacy ones
     */
    public function testLegacyDbVersion(): void
    {
        $plugin = $this->container->get(\GaletteHelloasso\PluginGaletteHelloasso::class);
        $this->assertNull($plugin->getLegacyDbVersion());
    }
}
