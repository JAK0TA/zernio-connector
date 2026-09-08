# Typo3 Zernio Connector

Task to get social media post from all platform supported by Zernio.com

## Development

Add local user, uid and gid to .env file.

Run

```Shell
docker compose --env-file ./.env --project-name 'dev' run --rm --remove-orphans tools composer install
```

to setup dependencies from project root.

## Configuration

To use this extension, require it in [Composer](https://getcomposer.org/):

```Shell
composer require jakota/zernio-connector
```

Add you account details to ext settings see ext_conf_template.txt.
