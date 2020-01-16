[![Build Status](https://travis-ci.org/LotusJeff/sitemap.svg?branch=master)](https://travis-ci.org/LotusJeff/sitemap.svg?branch=master)

# SEO Sitemap Extension for phpBB 3.1
# This is an abandon project. You are welcome to fork it.
This extension adds xml sitemaps to your phpBB forum. This extension allows you to exclude forums by name or size. It additionally has the ability to add link in the footer for the sitemap.

## Requirements
* phpBB 3.1.0 or higher

Note: This extension is in development. Installation is only recommended for testing purposes and is not supported on live boards.

## Installation

#### Download Method
- [Download the latest release](https://github.com/LotusJeff/sitemap) and unzip it.
- Unzip the downloaded files and copy it to the `ext` directory of your phpBB board. The directory structure should be **phpBB3/ext/lotusjeff/sitemap**
- Navigate in the ACP to `Customise -> Manage extensions`.
- Look for Sitemap under the Disabled Extensions list, and click `Enable` link.

#### Git Clone Method

```
cd phpBB3  (base forum install)
git clone https://github.com/LotusJeff/sitemap.git ext/lotusjeff/sitemap/
```

## Activate
- Go to ACP -> tab Customise -> Manage extensions -> enable Sitemap

## Configure

- Goto ACP -> Extensions -> Sitemap

## Update

#### Download Installation Used

- Go to ACP -> tab Customise -> Manage extensions -> disable Sitemap
- Delete files in ext/lotusjeff/sitemap
- Download new files. Unzip and copy files to phpBB3/ext/lotusjeff/sitemap
- Go to ACP -> tab Customise -> Manage extensions -> enable Sitemap

#### Git Clone Installation Used

- Go to ACP -> tab Customise -> Manage extensions -> disable Sitemap

```
cd phpBB3/ext/lotusjeff/sitemap
git pull
```

- Go to ACP -> tab Customise -> Manage extensions -> enable Sitemap

## Uninstallation
- Navigate in the ACP to `Customise -> Manage extensions`.
- Click the `Disable` link for Sitemap.
- To permanently uninstall, click `Delete Data`, then delete the `sitemap` folder from `phpBB3/ext/lotusjeff/`.

## Problems
- Check the file structure where you installed the code. It must be in:
```
       <phpBB root folder>/ext/lotusjeff/sitemap
```

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

© 2015 - Jeff Cocking (LotusJeff)
