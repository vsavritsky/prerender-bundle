# Vsavritsky/PrerenderBundle

## Installation

Install the module by typing (or add it to your `composer.json` file):

```sh
$ php composer.phar require "vsavritsky/prerender-bundle" "^0.1"
```

Register the bundle in bundles.php:

Vsavritsky\PrerenderBundle\VsavritskyPrerenderBundle::class => ['all' => true],

Enable the bundle's configuration in `app/config/packages/vsavritsky_prerender.yaml`:

# app/config/packages/vsavritsky_prerender.yaml
vsavritsky_prerender: ~


## Documentation

### How it works

1. Check to make sure we should show a prerendered page
	1. Check if the request is from a crawler (agent string)
	2. Check to make sure we aren't requesting a resource (js, css, etc...)
	3. (optional) Check to make sure the url is in the whitelist
	4. (optional) Check to make sure the url isn't in the blacklist
2. Make a `GET` request to the [prerender service](https://github.com/collectiveip/prerender) (PhantomJS server) for
the page's prerendered HTML
3. Return that HTML to the crawler

### Customization

This bundle comes with a sane default, extracted from
[prerender-node middleware](https://github.com/collectiveip/prerender-node), but you can easily customize it:

    #app/config/packages/vsavritsky_prerender.yaml
    vsavritsky_prerender:
        ....


#### Prerender URL

By default, YuccaPrerenderBundle uses the Prerender.io service deployed at `http://prerender.herokuapp.com`. However, you
may want to [deploy it on your own server](https://github.com/collectiveip/prerender#deploying-your-own). To that
extent, you can customize YuccaPrerenderBundle to use your server using the following configuration:

    #app/config/packages/vsavritsky_prerender.yaml
    vsavritsky_prerender:
        backend_url: http://localhost:3000

With this config, here is how YuccaPrerender will proxy the "https://google.com" request:

`GET` http://localhost:3000/https://google.com

#### Crawler user-agents

YuccaPrerender decides to pre-render based on the User-Agent string to check if a request comes from a bot or not. By
default, those user agents are registered: 'baiduspider', 'facebookexternalhit',
'twitterbot'.
Googlebot, Yahoo, and Bingbot should not be in this list because we support _escaped_fragment_ instead of
checking user agent for those crawlers. Your site must have to understand the '#!' ajax url notation.

You can add other User-Agent string to evaluate using this sample configuration:

    #app/config/packages/vsavritsky_prerender.yaml
    vsavritsky_prerender:
        crawler_user_agents: ['yandex', 'msnbot']

#### Ignored extensions

YuccaPrerender is configured by default to ignore all the requests for resources with those extensions:
`.js`,
`.css`,
`.less`,
`.png`,
`.jpg`,
`.jpeg`,
`.gif`,
`.pdf`,
`.doc`,
`.txt`,
`.zip`,
`.mp3`,
`.rar`,
`.exe`,
`.wmv`,
`.doc`,
`.avi`,
`.ppt`,
`.mpg`,
`.mpeg`,
`.tif`,
`.wav`,
`.mov`,
`.psd`,
`.ai`,
`.xls`,
`.mp4`,
`.m4a`,
`.swf`,
`.dat`,
`.dmg`,
`.iso`,
`.flv`,
`.m4v`,
`.torrent`
. Those are never pre-rendered.

You can add your own extensions using this sample configuration:

#app/config/packages/vsavritsky_prerender.yaml
vsavritsky_prerender:
    ignored_extensions: ['.less', '.pdf']

#### Whitelist

Whitelist a single url path or multiple url paths. Compares using regex, so be specific when possible. If a whitelist
is supplied, only url's containing a whitelist path will be prerendered.

Here is a sample configuration that *only* pre-render URLs that contains "/users/":


    #app/config/packages/vsavritsky_prerender.yaml
    vsavritsky_prerender:
        whitelist_urls: ['/users/*']

> Note: remember to specify URL here and not Symfony2 route names.

#### Blacklist

Blacklist a single url path or multiple url paths. Compares using regex, so be specific when possible. If a blacklist
is supplied, all url's will be pre-rendered except ones containing a blacklist part. Please note that if the referer
is part of the blacklist, it won't be pre-rendered too.

Here is a sample configuration that prerender all URLs *excepting* the ones that contains "/users/":

#app/config/packages/vsavritsky_prerender.yaml
vsavritsky_prerender:
    blacklist_urls: ['/users/*']

> Note: remember to specify URL here and not Symfony22 route names.

### Testing

If you want to make sure your pages are rendering correctly:

1. Open the Developer Tools in Chrome (Cmd + Atl + J)
2. Click the Settings gear in the bottom right corner.
3. Click "Overrides" on the left side of the settings panel.
4. Check the "User Agent" checkbox.
5. Choose "Other..." from the User Agent dropdown.
6. Type googlebot into the input box.
7. Refresh the page (make sure to keep the developer tools open).
