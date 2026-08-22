--
-- This file is part of Galette Helloasso plugin (https://galette-plugins.github.io/plugin-helloasso).
-- SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
-- SPDX-License-Identifier: GPL-3.0-or-later
--

INSERT INTO galette_helloasso_preferences (nom_pref, val_pref) VALUES ('helloasso_sepa_option', '');

ALTER TABLE galette_helloasso_history
  MODIFY checkout_id varchar(255) COLLATE utf8mb4_unicode_520_ci,
  MODIFY comments varchar(255) COLLATE utf8mb4_unicode_520_ci,
  MODIFY request text COLLATE utf8mb4_unicode_520_ci;

ALTER TABLE galette_helloasso_history
  ADD COLUMN payer_name VARCHAR(255) NOT NULL,
  ADD COLUMN member_id INT(10) NOT NULL,
  ADD COLUMN method VARCHAR(10) NOT NULL,
  ADD COLUMN receipt_url VARCHAR(255) NOT NULL;

UPDATE galette_helloasso_history
SET
  state = CASE
    WHEN state = 0 THEN 3
    ELSE state
  END,
  payer_name = CONCAT(
    UPPER(JSON_UNQUOTE(JSON_EXTRACT(request, '$.data.payer.lastName'))),
    ' ',
    JSON_UNQUOTE(JSON_EXTRACT(request, '$.data.payer.firstName'))
  ),
  member_id = COALESCE(
    CAST(JSON_UNQUOTE(JSON_EXTRACT(request, '$.metadata.member_id')) AS UNSIGNED),
    0
  ),
  method = JSON_UNQUOTE(JSON_EXTRACT(request, '$.data.paymentMeans')),
  receipt_url = JSON_UNQUOTE(JSON_EXTRACT(request, '$.data.paymentReceiptUrl'));
