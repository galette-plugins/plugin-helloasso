<?php

/**
 * This file is part of Galette Helloasso plugin (https://galette-plugins.github.io/plugin-helloasso).
 * SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace GaletteHelloasso\tests\units;

use Galette\Tests\GaletteTestCase;
use GaletteHelloasso\Filters\HelloassoHistoryList;

/**
 * Helloasso history tests
 *
 * @author Johan Cwiklinski <johan@x-tnd.be>
 */
class HelloassoHistory extends GaletteTestCase
{
    protected int $seed = 20260928081005;

    /**
     * Add an history entry with a serialized request
     *
     * @param string $request Stored request
     */
    private function addSerializedEntry(string $request): void
    {
        $insert = $this->zdb->insert(HELLOASSO_PREFIX . \GaletteHelloasso\HelloassoHistory::TABLE);
        $insert->values([
            'history_date' => date('Y-m-d H:i:s'),
            'checkout_id' => '1',
            'amount' => 10,
            'comments' => 'donation in money',
            'request' => $request,
            'state' => \GaletteHelloasso\HelloassoHistory::STATE_PUBLIC,
            'payer_name' => 'DOE Jane',
            'member_id' => 0,
            'method' => 'Card',
            'receipt_url' => ''
        ]);
        $this->zdb->execute($insert);
    }

    /**
     * Serialized entries are read as plain data
     */
    public function testSerializedEntries(): void
    {
        $this->addSerializedEntry(serialize(['item_id' => '5', 'item_name' => 'donation in money']));
        $this->addSerializedEntry(serialize(new \ArrayObject(['item_id' => '5'])));

        $history = new \GaletteHelloasso\HelloassoHistory($this->zdb, $this->login, $this->preferences);
        $history->setFilters(new HelloassoHistoryList());
        $entries = $history->getHelloassoHistory();

        $this->assertCount(2, $entries);
        $requests = array_column($entries, 'request');
        $this->assertContains(['item_id' => '5', 'item_name' => 'donation in money'], $requests);
        foreach ($requests as $request) {
            $this->assertNotInstanceOf(\ArrayObject::class, $request);
        }
    }
}
