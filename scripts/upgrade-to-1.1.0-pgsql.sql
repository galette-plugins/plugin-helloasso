--
-- This file is part of Galette Helloasso plugin (https://galette-plugins.github.io/plugin-helloasso).
-- SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
-- SPDX-License-Identifier: GPL-3.0-or-later
--

INSERT INTO galette_helloasso_preferences (nom_pref, val_pref) VALUES ('helloasso_sepa_option', '');

-- Keep time of payments, and do not store amounts as floating point numbers
ALTER TABLE galette_helloasso_history
  ALTER COLUMN history_date TYPE timestamp,
  ALTER COLUMN amount TYPE numeric(15,2),
  ADD COLUMN payer_name character varying(255) DEFAULT '' NOT NULL,
  ADD COLUMN member_id integer DEFAULT 0 NOT NULL,
  ADD COLUMN method character varying(10) DEFAULT '' NOT NULL,
  ADD COLUMN receipt_url character varying(255) DEFAULT '' NOT NULL;
ALTER TABLE galette_helloasso_history
  ALTER COLUMN payer_name DROP DEFAULT,
  ALTER COLUMN member_id DROP DEFAULT,
  ALTER COLUMN method DROP DEFAULT,
  ALTER COLUMN receipt_url DROP DEFAULT;

-- 1.0.0 states were 0 (public donation), 1 (processed), 2 (error), 3 (incomplete)
-- and 4 (already done); incomplete payments did not create any contribution
UPDATE galette_helloasso_history
SET
  state = CASE state
    WHEN 0 THEN 3
    WHEN 3 THEN 2
    ELSE state
  END,
  payer_name = TRIM(CONCAT(
    UPPER(request::json->'data'->'payer'->>'lastName'),
    ' ',
    request::json->'data'->'payer'->>'firstName'
  )),
  member_id = COALESCE((request::json->'metadata'->>'member_id')::integer, 0),
  method = COALESCE(request::json->'data'->>'paymentMeans', ''),
  receipt_url = COALESCE(request::json->'data'->>'paymentReceiptUrl', '');
