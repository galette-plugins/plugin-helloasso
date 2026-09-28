--
-- This file is part of Galette Helloasso plugin (https://galette-plugins.github.io/plugin-helloasso).
-- SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
-- SPDX-License-Identifier: GPL-3.0-or-later
--

INSERT INTO galette_helloasso_preferences (nom_pref, val_pref) VALUES ('helloasso_sepa_option', '');

-- without collation, columns get the one of the table
ALTER TABLE galette_helloasso_history
  MODIFY checkout_id varchar(255),
  MODIFY comments varchar(255),
  MODIFY request text;

ALTER TABLE galette_helloasso_history
  ADD COLUMN payer_name VARCHAR(255) NOT NULL,
  ADD COLUMN member_id INT(10) NOT NULL,
  ADD COLUMN method VARCHAR(10) NOT NULL,
  ADD COLUMN receipt_url VARCHAR(255) NOT NULL;

-- 1.0.0 states were 0 (public donation), 1 (processed), 2 (error), 3 (incomplete)
-- and 4 (already done); incomplete payments did not create any contribution
UPDATE galette_helloasso_history
SET
  state = CASE state
    WHEN 0 THEN 3
    WHEN 3 THEN 2
    ELSE state
  END,
  payer_name = COALESCE(CONCAT(
    UPPER(JSON_UNQUOTE(JSON_EXTRACT(request, '$.data.payer.lastName'))),
    ' ',
    JSON_UNQUOTE(JSON_EXTRACT(request, '$.data.payer.firstName'))
  ), ''),
  member_id = COALESCE(
    CAST(JSON_UNQUOTE(JSON_EXTRACT(request, '$.metadata.member_id')) AS UNSIGNED),
    0
  ),
  method = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(request, '$.data.paymentMeans')), ''),
  receipt_url = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(request, '$.data.paymentReceiptUrl')), '');
