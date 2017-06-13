# WP Hotelier
[![npm](https://img.shields.io/npm/v/npm.svg)]()
[![XO code style](https://img.shields.io/badge/code_style-XO-5ed9c7.svg)](https://github.com/sindresorhus/xo)

Welcome to the [WP Hotelier](https://wphotelier.com) GitHub repository. The documentation for the [WP Hotelier plugin](https://wphotelier.com) can be found on [WPHotelier.com](https://wphotelier.com), here you can browse the source of the project, find and discuss open issues.

## Installation ##

For detailed setup instructions, visit the official [WP Hotelier Documentation](http://docs.wphotelier.com) website.

1. Clone the GitHub repository: `https://github.com/hotelier/hotelier.git`
2. Or download it directly as a ZIP file: `https://github.com/hotelier/hotelier/archive/master.zip`

Like any other WordPress plugin, place it in `/wp-content/plugins/`.

## NPM usage

This repository comes with a ready to use `package.json` file that allows you to run and watch some powerful tasks. You can compile your Sass files, minimize your scripts, preview your changes and so on.

The first thing you need to do is install the npm dependencies. So, with the terminal cd into the **hotelier** folder and run `npm install`.

To make your life easier, the project uses a `.npmrc` file (not included in this repo) to pass the project configuration values. So, create a `.npmrc` file in the root of the **hotelier** folder and adjust the following settings:

```bash
HOTELIER_URL='http://path-to-your-wordpress-installation'
HOTELIER_SSHPORT='22'
HOTELIER_SYNCDEST='username@hostname:path'
```

Settings in detail:

* `HOTELIER_URL`: proxy URL to view your site; more info [here](https://browsersync.io/docs/options#option-proxy)
* `HOTELIER_SSHPORT`: SSH port
* `HOTELIER_SYNCDEST`: rsync destination; for example `username@hostname:/var/www/html/wordpress/wp-content/plugins/hotelier`

## Tasks included

There are five tasks you can run during the development of WP Hotelier. And four of them (`build`, `build-sync`, `build-server` and `build-sync-server`) watch for changes.

### build

This task compiles Sass files, optimizes the CSS, lints and minimizes the Javascript files and generates the `pot` file of WP Hotelier. Just run this command in the terminal:

```bash
npm run build
```

### build-sync

Same as the build task plus the possibility to sync the **hotelier** folder with another folder in a different server or VM. Useful to sync a local **hotelier** folder with the folder in `wp-content/plugins/hotelier` (in another server or VM). You need to specify a correct destination and SSH port in the `.npmrc` file: `HOTELIER_SYNCDEST` and `HOTELIER_SSHPORT`.

Run this command in the terminal:

```bash
npm run build-sync
```

### build-server

Same as the build task plus the possibility to sync your WP installation across multiple devices with Browsersync. You need to specify a correct proxy URL in the `.npmrc` file: `HOTELIER_URL`.

Run this command in the terminal:

```bash
npm run build-server
```

### build-sync-server

All the previous three tasks together.

Run this command in the terminal:

```bash
npm run build-sync-server
```

### dist

Run this command to create a zip file containing the production files:

```bash
npm run dist
```

## Scope of this repository ##

This repository is not suitable for support. But for issues related to WP Hotelier (core plugin only). Please don't submit support requests, use the official support channels for that:

* The [WP Hotelier support portal](https://wphotelier.com/support/) for customers who have purchased extensions or themes.
* [Support forum on wp.org](https://wordpress.org/support/plugin/wphotelier) if you don't have a valid license key.

Support requests or issues related to extensions or themes will be closed immediately.
