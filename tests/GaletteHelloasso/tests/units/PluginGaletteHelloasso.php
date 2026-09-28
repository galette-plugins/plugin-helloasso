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
 */
class PluginGaletteHelloasso extends GaletteTestCase
{
    protected int $seed = 20260928091512;

    /**
     * Current tables are not legacy ones
     */
    public function testLegacyDbVersion(): void
    {
        $plugin = $this->container->get(\GaletteHelloasso\PluginGaletteHelloasso::class);
        $this->assertNull($plugin->getLegacyDbVersion());
    }
}
