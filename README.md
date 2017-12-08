# itk-os2display/exchange-bundle
Integration to get calendar data from Exchange Web Service.

## Installation
Add the git repository to "repositories" in `composer.json`.

```
"repositories": {
    "itk-os2display/exchange-bundle": {
      "type": "vcs",
      "url": "https://github.com/itk-os2display/exchange-bundle"
    },
    ...
}
```

Require the bundle with composer.

```sh
composer require itk-os2display/exchange-bundle
```

To enable the integration, add the following to your `config.yml`.

```sh
itk_exchange:
    enabled: true
    host: %itk_exchange_host%
    user: %itk_exchange_user%
    password: %itk_exchange_password%
    version: Exchange2010
    cache_ttl: 1800
```

Change this to match your setup.

And add the following to your `parameters.yml`.

```sh
itk_exchange_host: [HOST]
itk_exchange_user: [USER]
itk_exchange_password: [PASSWORD]
```

Enable the bundle in `AppKernel.php`, by adding ItkExchangeBundle to $bundles.

```sh
new Itk\ExchangeBundle\ItkExchangeBundle()
```

## What the bundle does
The bundle reacts to `os2display:core:cron` events by looking for slides with
slide_type `calendar`. Each slide should have an array of resources in the 
`slide.options.resources` field. Each resource should have a `mail` field.

The bundle will gather calendar events from the host's EWS for each resource,
sort them and insert into the `slide.external_data` field.

It is possible to set `slide.options.interest_interval` to how many days into the
future the process should gather events for.

## Slide tool
The bundle injects the slide tool itk-email-list-tool that can be used to set
the resources.

## Service account
The bundle uses an EWS user (service account) that should be set in 
`parameters.yml` (together with password) to access calendars in the Exchange. 
To gain access other accounts, the accounts should "share" their calendars with
the service account.
Otherwise, the service account will not be able to view the calendar events for
the given resource.

## Caching
The bundle caches previous results to avoid spamming the Exchange each time the
cron process runs. The cache time to live (cache_ttl) can be adjusted for how
long a result (in seconds) should be cached.
