<?php

/**
 * This file is part of Galette Helloasso plugin (https://galette-community.github.io/plugin-helloasso).
 * SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace GaletteHelloasso;

use Analog\Analog;
use Galette\Core\Db;
use Galette\Core\Galette;
use Galette\Core\Login;
use Galette\Core\History;
use Galette\Core\Preferences;
use Galette\Entity\Adherent;
use Galette\Filters\HistoryList;

/**
 * This class stores and serve the Helloasso History.
 *
 * @author Johan Cwiklinski <johan@x-tnd.be>
 * @author Guillaume AGNIERAY <dev@agnieray.net>
 */
class HelloassoHistory extends History
{
    public const string TABLE = 'history';
    public const string PK = 'id_helloasso';

    public const int STATE_NONE = 0;
    public const int STATE_PROCESSED = 1;
    public const int STATE_ERROR = 2;
    public const int STATE_PUBLIC = 3;
    public const int STATE_ALREADYDONE = 4;

    private int $id;

    /**
     * Default constructor.
     *
     * @param Db           $zdb         Database
     * @param Login        $login       Login
     * @param Preferences  $preferences Preferences
     * @param ?HistoryList $filters     Filtering
     */
    public function __construct(Db $zdb, Login $login, Preferences $preferences, ?HistoryList $filters = null)
    {
        $this->with_lists = false;
        parent::__construct($zdb, $login, $preferences, $filters);
    }

    /**
     * Add a new entry
     *
     * @param array<string, mixed>|string $action   the action to log
     * @param string                      $argument the argument
     * @param string                      $query    the query (if relevant)
     *
     * @return bool true if entry was successfully added, false otherwise
     */
    public function add(array|string $action, string $argument = '', string $query = ''): bool
    {
        $request = $action;
        try {
            $values = [
                'history_date'  => date('Y-m-d H:i:s'),
                'checkout_id'   => $request['data']['id'],
                'amount'        => $request['data']['amount'] / 100,
                'comments'      => $request['metadata']['item_name'],
                'request'       => Galette::jsonEncode($request),
                'state'         => self::STATE_NONE
            ];

            $insert = $this->zdb->insert($this->getTableName());
            $insert->values($values);
            $this->zdb->execute($insert);
            $this->id = (int)$this->zdb->driver->getLastGeneratedValue();

            Analog::log(
                'An entry has been added in helloasso history',
                Analog::DEBUG
            );
        } catch (\Exception $e) {
            Analog::log(
                "An error occured trying to add log entry. " . $e->getMessage(),
                Analog::ERROR
            );
            return false;
        }

        return true;
    }

    /**
     * Get table's name
     *
     * @param bool $prefixed Whether table name should be prefixed
     */
    protected function getTableName(bool $prefixed = false): string
    {
        if ($prefixed === true) {
            return PREFIX_DB . HELLOASSO_PREFIX . self::TABLE;
        } else {
            return HELLOASSO_PREFIX . self::TABLE;
        }
    }

    /**
     * Get table's PK
     */
    protected function getPk(): string
    {
        return self::PK;
    }

    /**
     * Gets Helloasso history
     *
     * @return array<int, object>
     */
    public function getHelloassoHistory(): array
    {
        $orig = $this->getHistory();
        $new = [];
        if (count($orig) > 0) {
            foreach ($orig as $o) {
                try {
                    if (Galette::isSerialized($o['request'])) {
                        $oa = unserialize($o['request']);
                    } else {
                        $oa = Galette::jsonDecode($o['request']);
                    }

                    $member_id = $oa['metadata']['member_id'] ?? 0;

                    $o['member_fullname'] = $this->getMemberFullName($member_id);
                    $o['raw_request'] = print_r($oa, true);
                    $o['request'] = $oa;

                    $new[] = $o;
                } catch (\Exception $e) {
                    Analog::log(
                        'Error loading helloasso history entry #' . $o[$this->getPk()]
                        . ' ' . $e->getMessage(),
                        Analog::WARNING
                    );
                }
            }
        }
        return $new;
    }

    /**
     * Gets Member full name
     *
     * @param int $id ID of the member to retrieve
     */
    protected function getMemberFullName(int $id): string
    {
        $fullname = _T('None', 'helloasso');

        $select = $this->zdb->select(Adherent::TABLE);
        $select->columns(['prenom_adh', 'nom_adh']);
        $select->where(['id_adh' => $id]);
        $result = $this->zdb->execute($select);
        $row = $result->current();

        if ($row) {
            $fullname = mb_strtoupper($row['nom_adh']) . ' ' . $row['prenom_adh'];
        }

        return $fullname;
    }

    /**
     * Builds the order clause
     *
     * @return array<int, string> SQL ORDER clause
     */
    protected function buildOrderClause(): array
    {
        $order = [];

        if ($this->filters->orderby == HistoryList::ORDERBY_DATE) {
            $order[] = 'history_date ' . $this->filters->getDirection();
        }

        return $order;
    }

    /**
     * Is payment already processed?
     *
     * @param array<string, mixed> $request Verify sign helloasso parameter
     */
    public function isProcessed(array $request): bool
    {
        $select = $this->zdb->select($this->getTableName());
        $select->where(
            [
                'checkout_id' => $request['data']['id'],
                'state'       => [self::STATE_PROCESSED, self::STATE_PUBLIC]
            ]
        );
        $results = $this->zdb->execute($select);

        return (count($results) > 0);
    }

    /**
     * Set payment state
     *
     * @param int $state State, one of self::STATE_ constants
     */
    public function setState(int $state): bool
    {
        try {
            $update = $this->zdb->update($this->getTableName());
            $update
                ->set(['state' => $state])
                ->where([self::PK => $this->id]);
            $this->zdb->execute($update);
            return true;
        } catch (\Exception $e) {
            Analog::log(
                'An error occurred when updating state field | ' . $e->getMessage(),
                Analog::ERROR
            );
        }
        return false;
    }
}
