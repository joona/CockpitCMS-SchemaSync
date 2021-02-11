# SchemaSync

Sync your Collection and Singleton schemas from JSON files, located on cloud.

## Prequisities

[CloudStorage](https://github.com/agentejo/CloudStorage) addon is required, since it adds required libraries.

## Configuration

Add `schemas` FileStorage namespace configuration directly to `cloudstorage` configs.

```
cloudstorage:
  schemas:
    type: s3
    key: xxxKeyxxx
    secret: xxxSecretxxx
    region: eu-central-1
    bucket: mybucket

    # optional
    endpoint: https://eu-central-1.amazonaws.com
    prefix: subfolder-name
    url: https://s3.eu-central-1.amazonaws.com
```

# Usage

1. Build your Collection and Singleton definitions in JSON -format
2. Upload definitions to configured S3 bucket, using subdirectories `collections` and `singleton` for particular types.
3. Call the API `/api/schema-sync/sync` on Cockpit instance to download and update the definitions.

## Examples

Structure of `cockpit-schemas` S3 bucket via `aws-cli`.
```
$ aws s3 ls s3://cockpit-schemas.foo.bar/
                           PRE collections/
                           PRE singleton/

$ aws s3 ls s3://cockpit-schemas.foo.bar/collections/
2021-02-11 19:48:30      10237 Collections.json
2021-02-11 19:48:30       7766 Pages.json
2021-02-11 19:48:30       9157 Sections.json

$ aws s3 ls s3://cockpit-schemas.foo.bar/singleton/  
2021-02-11 19:48:30        790 Site.json
```

Call the API to sync:
```
$ curl https://my-cockpit.foo.bar/api/schema-sync/sync\?token=$COCKPIT_APITOKEN | jq '.'
{
  "synced": [
    "collections:Collections",
    "collections:Pages",
    "collections:Sections",
    "singleton:Site"
  ]
}
```


