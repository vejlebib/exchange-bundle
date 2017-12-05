# itk-os2display/exchange-bundle
Integration to get calendar data from Exchange Web Service

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

To enable the integration, add the following to your config.yml.

```sh
itk_exchange:
    enabled: true
    host: %itk_exchange_host%
    user: %itk_exchange_user%
    password: %itk_exchange_password%
    version: Exchange2010
    cache_ttl: 1800
```

And add the following to your parameters.yml.

```sh
itk_exchange_host: [HOST]
itk_exchange_user: [USER]
itk_exchange_password: [PASSWORD]
```

Enable the bundle in AppKernel.php, by adding ItkExchangeBundle to $bundles.

```sh
new Itk\ExchangeBundle\ItkExchangeBundle()
```
