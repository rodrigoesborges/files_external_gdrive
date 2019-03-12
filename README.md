# Files External Google Drive (Nextcloud app)
  Google Drive external storage support for Nextcloud (still in Beta)


# Installation

First of all, you need to enable the `files_external` app.

## From the appstore
- Simply install `files_external_gdrive` from the Nextcloud appstore

## From git
- First, clone the repo `git clone https://github.com/NastuzziSamy/files_external_gdrive.git`
- Execute `make install` in the app directory
- And enable the app with `php occ app:enable files_external_gdrive` in the Nextcloud directory

# Usage
## Google configuration

In order to use the app, you need to create an OAuth2 client via: https://console.developers.google.com/apis/

Complete all needed information, as:
- App name (put "Nextcloud" so you can remember you use it for Nextcloud)
- JS origin urls
- Redirect urls (for OAuth2 callback)


## On Nextcloud

Use your Google credentials to authorize access and connect to your Google account to give access to your Drive.


# TODOs
- [x] Make files and directories:
    - [x] Printable
    - [x] Readable
    - [x] Downloadable
    - [x] Uploadable
    - [x] Editable
    - [x] Renamable
    - [x] With the right mimetype
- [ ] Allow regular user to create its own Google Drive external storage
- [ ] Print better stats
- [ ] Update only on changes
- [ ] Optimize
- [ ] Fix Oauth duplications
- [ ] Unit tests
