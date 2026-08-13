--
-- This file is part of Galette Helloasso plugin (https://galette-community.github.io/plugin-helloasso).
-- SPDX-FileCopyrightText: Copyright © 2025-2026 The Galette Team
-- SPDX-License-Identifier: GPL-3.0-or-later
--

UPDATE galette_helloasso_history SET state = 3 WHERE state = 0;
INSERT INTO galette_helloasso_preferences (nom_pref, val_pref) VALUES ('helloasso_sepa_option', '');
