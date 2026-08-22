<?php

/**
 * This file is part of Galette Helloasso plugin (https://galette-plugins.github.io/plugin-helloasso).
 * SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

namespace GaletteHelloasso\tests\units;

use Galette\Tests\GaletteTestCase;

/**
 * Helloasso tests
 *
 * @author Johan Cwiklinski <johan@x-tnd.be>
 * @author Guillaume AGNIERAY <dev@agnieray.net>
 */
class Helloasso extends GaletteTestCase
{
    protected int $seed = 20250723111237;

    /**
     * Cleanup after each test method
     */
    public function tearDown(): void
    {
        $delete = $this->zdb->delete(HELLOASSO_PREFIX . \GaletteHelloasso\Helloasso::TABLE);
        $this->zdb->execute($delete);
        parent::tearDown();
    }

    /**
     * Test empty
     */
    public function testEmpty(): void
    {
        $helloasso = new \GaletteHelloasso\Helloasso($this->zdb, $this->preferences);
        $this->assertFalse($helloasso->getTestMode());
        $this->assertSame('', $helloasso->getOrganizationSlug());
        $this->assertSame('', $helloasso->getClientId());
        $this->assertSame('', $helloasso->getClientSecret());

        $amounts = $helloasso->getAmounts($this->login);
        $this->assertCount(0, $amounts);
        $this->assertCount(7, $helloasso->getAllAmounts());
        $this->assertTrue($helloasso->areAmountsLoaded());
        $this->assertTrue($helloasso->isLoaded());
    }
}
