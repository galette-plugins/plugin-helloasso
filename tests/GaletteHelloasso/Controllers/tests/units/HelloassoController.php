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
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
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
     * Requests sent to the (fake) HelloAsso API
     *
     * @var array<int, array<string, mixed>>
     */
    private array $api_calls = [];

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
     * Answer HelloAsso API calls with given responses, in order
     *
     * Plugin settings must be set before: they are loaded here.
     *
     * @param array<int, array<string, mixed>> $responses JSON responses
     */
    private function fakeApi(array $responses): void
    {
        $this->api_calls = [];
        $stack = HandlerStack::create(new MockHandler(array_map(
            fn(array $body) => new Response(200, ['Content-Type' => 'application/json'], (string)json_encode($body)),
            $responses
        )));
        $stack->push(Middleware::history($this->api_calls));
        $this->container->set(
            Helloasso::class,
            new Helloasso($this->zdb, $this->preferences, new Client(['handler' => $stack]))
        );
    }

    /**
     * Token response of the API
     *
     * @return array<string, mixed>
     */
    private function getTokenResponse(): array
    {
        return ['access_token' => 'access-for-tests', 'expires_in' => 1800, 'refresh_token' => 'refresh-for-tests'];
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

    /**
     * Amount must be a number, at least the one of the payment reason
     */
    public function testCheckoutChecksAmount(): void
    {
        $this->configure();
        $this->setTypeAmount(5, 10);
        $this->getMemberOne();
        $this->logMember($this->dataAdherentOne());

        foreach (['', 'abc', '12abc', ['12']] as $amount) {
            $this->expectCheckoutRefused(
                $this->postCheckout(['item_id' => '5', 'amount' => $amount]),
                _T("Please enter an amount.", "helloasso")
            );
        }
        foreach (['9.99', '-20', '0'] as $amount) {
            $this->expectCheckoutRefused(
                $this->postCheckout(['item_id' => '5', 'amount' => $amount]),
                _T(
                    "The amount you've entered is lower than the minimum amount for the selected option. Please choose another option or change the amount.",
                    "helloasso"
                )
            );
        }

        //decimal comma is accepted: the checkout is requested, HelloAsso cannot be reached
        $test_response = $this->postCheckout(['item_id' => '5', 'amount' => '12,50']);
        $this->assertSame(301, $test_response->getStatusCode());
        $this->expectFlashData(['error_detected' => [_T('An error occurred redirecting to the checkout form.', 'helloasso')]]);
        $this->expectLogEntry(\Analog\Analog::ERROR, 'Error while connecting to Helloasso');
        $this->expectLogEntry(\Analog\Analog::ERROR, 'Cannot create Helloasso checkout');
        $this->expectNoLogEntry();
    }

    /**
     * Get the return page of a checkout
     *
     * @param array<string, string> $query Query parameters
     */
    private function getReturnPage(array $query): ResponseInterface
    {
        $request = $this->createRequest('helloasso_success', [], 'GET', 'text/html', $query);
        return $this->app->handle($request);
    }

    /**
     * Return page only displays checkouts started from the current session
     */
    public function testReturnPageOnlyShowsOwnCheckouts(): void
    {
        $this->configure();

        foreach ([['checkoutIntentId' => '42'], ['orderId' => '42'], ['code' => 'succeeded']] as $query) {
            $test_response = $this->getReturnPage($query);
            $this->assertSame(403, $test_response->getStatusCode());
            $this->expectLogEntry(\Analog\Analog::WARNING, 'has not been started from this session');
            //HelloAsso has not been called
            $this->expectNoLogEntry();
        }

        //a checkout started from this session is looked for on HelloAsso
        $this->session->helloasso_checkouts = ['41', '42'];
        $test_response = $this->getReturnPage(['checkoutIntentId' => '42']);
        $this->assertSame(403, $test_response->getStatusCode());
        $this->expectLogEntry(\Analog\Analog::ERROR, 'Error while connecting to Helloasso');
        $this->expectLogEntry(\Analog\Analog::WARNING, 'payment details could not be retrieved');
        $this->expectNoLogEntry();
    }

    /**
     * Get a plugin preference, as stored
     *
     * @param string $name Preference name
     */
    private function getHelloassoPref(string $name): string
    {
        $select = $this->zdb->select(HELLOASSO_PREFIX . Helloasso::TABLE);
        $select->where(['nom_pref' => $name]);
        return $this->zdb->execute($select)->current()->val_pref;
    }

    /**
     * Post preferences
     *
     * @param array<string, string> $data Posted data
     */
    private function postPreferences(array $data): void
    {
        $request = $this->createRequest('store_helloasso_preferences', [], 'POST')->withParsedBody(
            $data + ['helloasso_organization_slug' => 'galette-tests', 'helloasso_client_id' => 'client-for-tests']
        );
        $test_response = $this->app->handle($request);
        $this->assertSame(301, $test_response->getStatusCode());
        $this->expectFlashData(['success_detected' => [_T('Helloasso settings have been saved.', 'helloasso')]]);
    }

    /**
     * Client secret is never sent back to the browser, and kept when left empty
     */
    public function testPreferencesSecret(): void
    {
        $this->configure();
        $this->logSuperAdmin();

        $test_response = $this->app->handle($this->createRequest('helloasso_preferences'));
        $this->assertSame(200, $test_response->getStatusCode());
        //organization cannot be retrieved from HelloAsso
        $this->expectLogEntry(\Analog\Analog::ERROR, 'Error while connecting to Helloasso');
        $this->expectLogEntry(\Analog\Analog::ERROR, 'Exception when calling OrganisationApi');
        $this->expectNoLogEntry();
        $body = (string)$test_response->getBody();
        $this->assertStringContainsString('client-for-tests', $body);
        $this->assertStringNotContainsString('secret-for-tests', $body);
        $this->assertMatchesRegularExpression('/<input\s+type="password"\s+name="helloasso_client_secret"/', $body);

        //empty secret keeps the stored one
        $this->postPreferences(['helloasso_client_secret' => ' ']);
        $this->assertSame('secret-for-tests', $this->getHelloassoPref('helloasso_client_secret'));

        $this->postPreferences(['helloasso_client_secret' => 'new-secret']);
        $this->assertSame('new-secret', $this->getHelloassoPref('helloasso_client_secret'));
        $this->expectNoLogEntry();
    }

    /**
     * A checkout is created on HelloAsso, and its details displayed on return
     */
    public function testCheckout(): void
    {
        $this->configure();
        $this->setTypeAmount(5, 10);
        $member = $this->getMemberOne();
        $this->logMember($this->dataAdherentOne());
        $this->fakeApi([
            $this->getTokenResponse(),
            ['id' => 1234, 'redirectUrl' => 'https://www.helloasso-sandbox.com/checkout/1234'],
            [
                'id' => 1234,
                'metadata' => ['item_id' => 5, 'item_name' => 'donation in money', 'member_id' => $member->id],
                'order' => [
                    'amount' => ['total' => 1250],
                    'date' => '2026-09-28T10:15:00+02:00',
                    'payments' => [['paymentMeans' => 'Card']]
                ]
            ]
        ]);

        $test_response = $this->postCheckout(['item_id' => '5', 'amount' => '12,50']);
        $this->assertSame(301, $test_response->getStatusCode());
        $this->assertSame(['https://www.helloasso-sandbox.com/checkout/1234'], $test_response->getHeader('Location'));
        $this->expectNoLogEntry();
        $this->assertSame(['1234'], $this->session->helloasso_checkouts);

        $this->assertCount(2, $this->api_calls);
        $checkout = json_decode((string)$this->api_calls[1]['request']->getBody(), true);
        $this->assertSame(1250, $checkout['totalAmount']);
        $this->assertSame(['member_id' => $member->id, 'item_id' => 5, 'item_name' => 'donation in money'], $checkout['metadata']);

        //tokens are kept for next calls
        $select = $this->zdb->select(HELLOASSO_PREFIX . Helloasso::TABLE_TOKENS);
        $select->where(['type' => 'access_token']);
        $this->assertSame('access-for-tests', $this->zdb->execute($select)->current()->value);

        $test_response = $this->getReturnPage(['checkoutIntentId' => '1234', 'code' => 'succeeded']);
        $this->assertSame(200, $test_response->getStatusCode());
        $this->expectNoLogEntry();
        $body = (string)$test_response->getBody();
        $this->assertStringContainsString('donation in money', $body);
        $this->assertStringContainsString('Card', $body);
        $this->assertCount(3, $this->api_calls);
    }

    /**
     * Return page refuses to display unexpected checkout details
     */
    public function testReturnPageWithUnexpectedDetails(): void
    {
        $this->configure();
        $this->fakeApi([$this->getTokenResponse(), ['id' => 1234]]);
        $this->session->helloasso_checkouts = ['1234'];

        $test_response = $this->getReturnPage(['checkoutIntentId' => '1234']);
        $this->assertSame(403, $test_response->getStatusCode());
        $this->expectLogEntry(\Analog\Analog::WARNING, 'payment details could not be retrieved');
        $this->expectNoLogEntry();
    }

    /**
     * Settings ask HelloAsso about the organization only once
     */
    public function testPreferencesOrganization(): void
    {
        $this->configure();
        $this->fakeApi([
            $this->getTokenResponse(),
            ['name' => 'Galette tests organization', 'type' => 'Association1901', 'category' => 'Other']
        ]);
        $this->logSuperAdmin();

        $test_response = $this->app->handle($this->createRequest('helloasso_preferences'));
        $this->assertSame(200, $test_response->getStatusCode());
        $this->expectNoLogEntry();
        $body = (string)$test_response->getBody();
        $this->assertStringContainsString('Galette tests organization', $body);
        $this->assertStringContainsString('Association1901', $body);
        $this->assertCount(2, $this->api_calls);
    }

    /**
     * Checkout amount is rounded to the cent, not truncated
     */
    public function testCheckoutRoundsAmount(): void
    {
        $this->configure();
        $this->setTypeAmount(5, 10);
        $this->getMemberOne();
        $this->logMember($this->dataAdherentOne());
        $this->fakeApi([
            $this->getTokenResponse(),
            ['id' => 1234, 'redirectUrl' => 'https://www.helloasso-sandbox.com/checkout/1234']
        ]);

        $this->assertSame(301, $this->postCheckout(['item_id' => '5', 'amount' => '19.99'])->getStatusCode());
        $this->expectNoLogEntry();
        $checkout = json_decode((string)$this->api_calls[1]['request']->getBody(), true);
        $this->assertSame(1999, $checkout['totalAmount']);
        $this->assertSame(1999, $checkout['initialAmount']);
    }
}
