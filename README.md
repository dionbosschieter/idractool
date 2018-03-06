# Todo
- [ ] everything should retry x amounts based on global retry setting
- [x] create symfony command skeleton tool
- [x] PasswordProvider, by file, class or readline
- [x] Zend_Http_Client -> file_get_contents with http stream wrapper
- [x] write down php requirements
- [x] update idrac firmwares via http download
- [ ] cancel running jobs command

# Usage
```
[0] me@ubu0 ~/code/idractool(master) $ bin/idractool upgrade-firmware -a server01
Possible Firmware Upgrades
============

Info [Idrac\FirmwareChecker] Checking if (3.15.17.15 > 3.15.17.15)
Info [Idrac\FirmwareChecker] Checking if (18.0.17 > 18.3.6)
Info [Idrac\FirmwareChecker] Checking if (18.0.17 > 18.3.6)
Info [Idrac\FirmwareChecker] Checking if (1.3.7 > 1.2.11)
Info [Idrac\FirmwareChecker] Firmware found for [componentId=159] BIOS FOLDER04818210M/1/BIOS_XRN29_WN64_1.3.7.EXE  XRN29 LW64
Info [Idrac\FirmwareChecker] Checking if (25.5.4.0006 > 25.5.4.0006)
Info [Idrac\FirmwareChecker] Checking if (DL53 > DL53)
Info [Idrac\FirmwareChecker] Checking if (DL53 > DL53)
Info [Idrac\FirmwareChecker] Checking if (4.23 > 4.23)
Info [Idrac\FirmwareInstallScheduler] Scheduling update installation for BIOS

```

# Requirements
php 5.4+, phar extension, crypto
