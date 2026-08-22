--
-- This file is part of Galette Helloasso plugin (https://galette-plugins.github.io/plugin-helloasso).
-- SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
-- SPDX-License-Identifier: GPL-3.0-or-later
--

INSERT INTO galette_helloasso_preferences (nom_pref, val_pref) VALUES ('helloasso_sepa_option', '');

ALTER TABLE galette_helloasso_history
  ADD COLUMN payer_name character varying(255) NOT NULL,
  ADD COLUMN member_id integer NOT NULL,
  ADD COLUMN method character varying(10) NOT NULL,
  ADD COLUMN receipt_url character varying(255) NOT NULL;

UPDATE galette_helloasso_history
SET
  state = CASE
    WHEN state = 0 THEN 3
    ELSE state
  END,
  payer_name = CONCAT(
    UPPER((request->'data'->'payer'->>'lastName')),
    ' ',
    (request->'data'->'payer'->>'firstName')
  ),
  member_id = COALESCE(
    (request->'metadata'->>'member_id')::int,
    0
  ),
  method = request->'data'->>'paymentMeans',
  receipt_url = request->'data'->>'paymentReceiptUrl';
