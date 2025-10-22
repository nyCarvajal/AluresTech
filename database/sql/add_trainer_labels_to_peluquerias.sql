-- SQL script to add customizable stylist role label columns to the peluquerias table.
-- Execute the landlord statement against the primary database and the tenant
-- statement on each tenant schema. Both statements are idempotent on MySQL 8.0+
-- thanks to the IF NOT EXISTS clause.

-- Primary database (landlord)
ALTER TABLE `peluquerias`
    ADD COLUMN IF NOT EXISTS `trainer_label_singular` VARCHAR(255) NULL AFTER `msj_finalizado`,
    ADD COLUMN IF NOT EXISTS `trainer_label_plural` VARCHAR(255) NULL AFTER `trainer_label_singular`;

-- Tenant databases
ALTER TABLE `peluquerias`
    ADD COLUMN IF NOT EXISTS `trainer_label_singular` VARCHAR(255) NULL AFTER `msj_finalizado`,
    ADD COLUMN IF NOT EXISTS `trainer_label_plural` VARCHAR(255) NULL AFTER `trainer_label_singular`;
