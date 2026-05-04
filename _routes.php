<?php

/**
 * This file is part of Galette Helloasso plugin (https://galette-community.github.io/plugin-helloasso).
 * SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

use Galette\Middleware\Authenticate;
use GaletteHelloasso\Controllers\HelloassoController;

//Constants and classes from plugin
require_once $module['root'] . '/_config.inc.php';

$app->get(
    '/preferences',
    [HelloassoController::class, 'preferences']
)->setName('helloasso_preferences')->add(Authenticate::class);

$app->post(
    '/preferences',
    [HelloassoController::class, 'storePreferences']
)->setName('store_helloasso_preferences')->add(Authenticate::class);

$app->get(
    '/form',
    [HelloassoController::class, 'form']
)->setName('helloasso_form');

$app->post(
    '/form',
    [HelloassoController::class, 'formCheckout']
)->setName('helloasso_formCheckout');

$app->get(
    '/logs[/{option:order|reset|page}/{value}]',
    [HelloassoController::class, 'logs']
)->setName('helloasso_history')->add(Authenticate::class);

//history filtering
$app->post(
    '/history/filter',
    [HelloassoController::class, 'filter']
)->setName('filter_helloasso_history')->add(Authenticate::class);

$app->post(
    '/webhook',
    [HelloassoController::class, 'webhook']
)->setName('helloasso_webhook');

$app->get(
    '/success',
    [HelloassoController::class, 'returnUrl']
)->setName('helloasso_success');

$app->get(
    '/cancel',
    [HelloassoController::class, 'cancelUrl']
)->setName('helloasso_cancel');

$app->get(
    '/error',
    [HelloassoController::class, 'errorUrl']
)->setName('helloasso_error');
