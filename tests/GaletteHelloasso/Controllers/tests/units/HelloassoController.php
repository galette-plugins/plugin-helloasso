<?php

/**
 * This file is part of Galette Helloasso plugin (https://galette-plugins.github.io/plugin-helloasso).
 * SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace GaletteHelloasso\Controllers\tests\units;

use Galette\Entity\ContributionsTypes;
use Galette\Tests\GaletteRoutingTestCase;
use GaletteHelloasso\Helloasso;
use Psr\Http\Message\ResponseInterface;

/**
 * Helloasso controller tests
 *
 * @author Johan Cwiklinski <johan@x-tnd.be>
 */
class HelloassoController extends GaletteRoutingTestCase
{
    protected int $seed = 20260928061512;
    protected bool $load_plugins = true;

    /**
     * Set up tests
     */
    public function setUp(): void
    {
        parent::setUp();
        //never reach HelloAsso: any call fails at once
        putenv('HTTPS_PROXY=http://127.0.0.1:1');
    }

    /**
     * Cleanup after each test method
     */
    public function tearDown(): void
    {
        putenv('HTTPS_PROXY');
        $this->login->logout();
        parent::tearDown();
    }

    /**
     * Set a plugin preference
     *
     * @param string $name  Preference name
     * @param string $value Preference value
     */
    private function setHelloassoPref(string $name, string $value): void
    {
        $update = $this->zdb->update(HELLOASSO_PREFIX . Helloasso::TABLE);
        $update->set(['val_pref' => $value])->where(['nom_pref' => $name]);
        $this->zdb->execute($update);
    }

    /**
     * Configure plugin, as an administrator would
     */
    private function configure(): void
    {
        $this->setHelloassoPref('helloasso_organization_slug', 'galette-tests');
        $this->setHelloassoPref('helloasso_client_id', 'client-for-tests');
        $this->setHelloassoPref('helloasso_client_secret', 'secret-for-tests');
    }

    /**
     * Log in given member
     *
     * @param array<string,mixed> $mdata Member data
     */
    private function logMember(array $mdata): void
    {
        $this->assertTrue($this->login->login($mdata['login_adh'], $mdata['mdp_adh']));
    }

    /**
     * Set the amount of a contribution type
     *
     * @param int   $id_type Contribution type ID
     * @param float $amount  Amount
     */
    private function setTypeAmount(int $id_type, float $amount): void
    {
        $update = $this->zdb->update(ContributionsTypes::TABLE);
        $update->set(['amount' => $amount])->where([ContributionsTypes::PK => $id_type]);
        $this->zdb->execute($update);
    }

    /**
     * Post the payment form
     *
     * @param array<string, mixed> $data Posted data
     */
    private function postCheckout(array $data): ResponseInterface
    {
        $request = $this->createRequest('helloasso_formCheckout', [], 'POST')->withParsedBody($data);
        return $this->app->handle($request);
    }

    /**
     * Assert payment form has been refused with given message, before calling HelloAsso
     *
     * @param ResponseInterface $test_response Response
     * @param string            $message       Expected error message
     */
    private function expectCheckoutRefused(ResponseInterface $test_response, string $message): void
    {
        $this->assertSame(301, $test_response->getStatusCode());
        $this->assertSame(
            [$this->routeparser->urlFor('helloasso_form')],
            $test_response->getHeader('Location')
        );
        $this->expectFlashData(['error_detected' => [$message]]);
        //a call to HelloAsso would have logged an error
        $this->expectNoLogEntry();
    }

    /**
     * Only payment reasons proposed to the current user can be paid
     */
    public function testCheckoutRefusesUnproposedReason(): void
    {
        $this->configure();
        //type 1 (annual fee) is proposed, type 7 is inactive by default
        $this->setTypeAmount(1, 20);
        $this->setTypeAmount(7, 20);
        $this->getMemberOne();
        $this->logMember($this->dataAdherentOne());

        $this->expectCheckoutRefused(
            $this->postCheckout(['item_id' => '7', 'amount' => '1']),
            _T("You have to select an option.", "helloasso")
        );
        $this->expectCheckoutRefused(
            $this->postCheckout(['item_id' => '9999', 'amount' => '1']),
            _T("You have to select an option.", "helloasso")
        );
        $this->expectCheckoutRefused(
            $this->postCheckout(['amount' => '20']),
            _T("You have to select an option.", "helloasso")
        );

        //membership fees are not proposed to visitors
        $this->login->logout();
        $this->expectCheckoutRefused(
            $this->postCheckout(['item_id' => '1', 'amount' => '20']),
            _T("You have to select an option.", "helloasso")
        );
    }
}
