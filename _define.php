<?php

/**
 * This file is part of Galette Helloasso plugin (https://galette-community.github.io/plugin-helloasso).
 * SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

/** @var \Galette\Core\Plugins $this */
$this->register(
    name: 'Galette Helloasso',     //Name
    desc: 'Helloasso integration', //Short description
    author: 'Guillaume AGNIERAY',  //Author
    version: '1.1.0-dev',          //Version
    compver: '1.3.0',              //Galette compatible version
    route: 'helloasso',            //Routing name and translation domain
    date: '2026-08-08',            //Release date
    acls: [                        //Permissions needed
        'helloasso_preferences'        => 'staff',
        'store_helloasso_preferences'  => 'staff',
        'helloasso_history'            => 'staff',
        'filter_helloasso_history'     => 'staff'
    ],
    dbver: 1.10                    //DB version
);
