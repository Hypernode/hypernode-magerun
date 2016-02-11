Hypernode Magerun Addons
==============

Some additional commands for the excellent N98-magerun Magento command-line tool.

Installation
------------
There are a few options.  You can check out the different options in the [Magerun
docs](http://magerun.net/introducting-the-new-n98-magerun-module-system/).

Here's the easiest:

1. Create ~/.n98-magerun/modules/ if it doesn't already exist.

        mkdir -p ~/.n98-magerun/modules/

2. Clone the magerun-addons repository in there

        cd ~/.n98-magerun/modules/ && git clone git@github.com:peterjaap/hypernode-addons.git

3. It should be installed. To see that it was installed, run magerun without any arguments to see if one of the new commands is in there.

        n98-magerun.phar

Commands
--------

### Find available updates for installed modules ###

Example usage:

        n98-magerun sys:modules:list-updates

        Name                             Current      Latest
        ==============================================================
        Aoe_Scheduler                    0.4.3        1.3.1
        Aoe_TemplateHints                0.3.0        0.4.3
        Hans2103_HideCompare             1.0          1.0.2
        Lesti_Fpc                        1.2.0        1.4.4
        Magestore_Magenotification       0.1.3        0.1.4
        Mirasvit_SearchLandingPage       1.0.0        1.0.1
        Mollie_Mpm                       4.1.5        4.1.9
        TIG_PostNL                       1.3.1        1.7.2
        Yireo_DeleteAnyOrder             0.11.2       0.12.3

See if newer versions exist for your currently installed Magento 1 modules (local & community). I hear you say, Magento Connect already does this? Not really, as Magento Connect only contains Magento-registered modules. As it appears, about 20% of modules-in-the-wild are not registered with Magento.

This tool is a crowdsourced initiative: it will report the latest version of any module as seen in the wild. This does not necessarily mean a newer version is publicly available, just that it exists.

As of Feb 2016, it contains version information from about 500 installations.

### Determine required patches ###

        n98-magerun sys:info:patches

John Knowles maintains an [excellent spreadsheet](https://docs.google.com/spreadsheets/d/1MTbU9Bq130zrrsJwLIB9d8qnGfYZnkm4jBlfNaBF19M/pubhtml?widget=true) which links Magento versions with required patches.

Running this command will show you which patches you need for the current Magento version and which are already installed.

Credits due where credits due
--------

Thanks to [Netz98](http://www.netz98.de) for creating the awesome Swiss army knife for Magento, [magerun](https://github.com/netz98/n98-magerun/).
