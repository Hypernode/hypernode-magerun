Hypernode Magerun Addons
==============



Some additional commands for the excellent N98-magerun Magento command-line tool.

| Master  |  [![Build Status](https://travis-ci.org/Hypernode/hypernode-magerun.svg?branch=master)](https://travis-ci.org/Hypernode/hypernode-magerun) |
|------------|-------------------------------------------------|
| Staging  | [![Build Status](https://travis-ci.org/Hypernode/hypernode-magerun.svg?branch=staging)](https://travis-ci.org/Hypernode/hypernode-magerun)  |

Installation
------------
There are a few options.  You can check out the different options in the [Magerun
docs](http://magerun.net/introducting-the-new-n98-magerun-module-system/).

Here's the easiest:

1. Create ~/.n98-magerun/modules/ if it doesn't already exist.

        mkdir -p ~/.n98-magerun/modules/
        mkdir -p ~/.n98-magerun2/modules/

2. Clone the hypernode-magerun repository in there

        git clone https://github.com/Hypernode/hypernode-magerun.git ~/.n98-magerun/modules/hypernode-magerun

3. Link the repository for n98-magerun2

        ln -s ~/.n98-magerun/modules/hypernode-magerun ~/.n98-magerun2/modules/hypernode-magerun

4. It should be installed. To see that it was installed, run magerun without any arguments to see if one of the new commands is in there.

        n98-magerun.phar

Commands
--------

### Get a (system).log analyses of the most frequent lines ###

	n98-magerun hypernode:log-analyses
	
Quickly reference the most common lines in the log file ordered by frequency.

### Generate a boilerplate for Nginx http.magerunmaps ###

	n98-magerun hypernode:maps-generate
	
Outputs or saves a http.magerunmaps boilerplate containing your store setup for Nginx. Refer to the [Hypernode Nginx documentation.](https://support.hypernode.com/knowledgebase/how-to-use-nginx/)
	
### Varnish config ###

	n98-magerun hypernode:varnish:config-save
	
Fetches the VCL configuration from turpentine and applies it. Make sure [turpentine is installed and configured](https://support.hypernode.com/knowledgebase/varnish-on-hypernode/) correctly.

### Flush all URL's in Varnish cache ###

	n98-magerun hypernode:varnish:flush
	
Flush all URL's that were cached by varnish.

### Performance reporting ###

	n98-magerun hypernode:performance
	
By default this command loads Magento's sitemap collection from which you can choose what sitemaps you want to crawl. If the store URL does not match the URL's in the sitemap you will be prompted several options (compare, replace, continue). For instance the old and new URL can be compared in a performance report. Additionally a sitemap can be loaded by specifying a path or URL. 

### Checking for weak admin credentials ###

    n98-magerun hypernode:crack:admin-passwords -r best64 vendors

Check your site for weak admin credentials by attempting to brute force the password with popular password / variations.

### Checking for weak admin credentials ###

    n98-magerun hypernode:crack:api-keys -r best64 vendors

This command words exactly the same as the `hypernode:crack:admin-passwords` except it attempts to crack the api_key of SOAP / XML-RPC users. All arguments are the same, check the commands `--help` for details.

Packaging
--------

For development/testing (build package of your feature branch):
```
gbp buildpackage --git-pbuilder --git-dist=precise --git-arch=amd64
```

Building a .deb for release:
```
./build.sh
```

Then if everything is alright, upload the new version to your repository with something like [dput](http://manpages.ubuntu.com/manpages/precise/man1/dput.1.html)

n98-magerun2 compatibility
-----------------------

Currently, only the command `hypernode:perfomance` is partially compatible with n98-magerun2. 

Please contribute to make more commands available for n98-magerun2!

Credits due where credits due
--------

Thanks to [Netz98](http://www.netz98.de) for creating the awesome Swiss army knife for Magento, [magerun](https://github.com/netz98/n98-magerun/).
