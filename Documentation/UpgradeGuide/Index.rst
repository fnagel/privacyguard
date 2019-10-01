.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide:

Upgrade Guide
=============


Update from 2.1.x to 3.0.0
--------------------------

* TYPO3 9.x support
* PHP 7.2 support
* Remove TYPO3 6.x and 7.x support
* Remove PHP < 7.0 support
* Changed PHP namespace to `FelixNagel`
* Remove deprecated `TYPO3_DB` usage
* Switch to PSR-2 CGL

**How to update**
* Use "Clear all caches including PHP opcode cache" and "Dump Autoload Information" in the install tool (if needed for your setup)
* Re-add all tasks in scheduler module


Update from 2.0.x to 2.1.0
--------------------------

* Tested in TYPO3 8.7 LTS
* Removed EXT:comments and EXT:sfpantispam
* Replaced EXT px_phpids with EXT:mkphpids
* Added EXT:femanager


Update from 1.5.0 to 2.0.x
--------------------------

* TYPO3 7.6 support
* Migrate to a Sphinx-based documentation

**How to update**
* Clear cache in Install Tool
* Re-add all tasks in scheduler module
