# Changelogs
## v0.4.0
- Stabilize the application: no more crashes, no more infinite loadings
- Consequence: nothing works with subfolders contents
- Bugs fixed:
    - Guzzle conflicted with the News app one: https://github.com/NastuzziSamy/files_external_gdrive/issues/27
    - Deleting a file created a whole crash

## v0.3.0
- Add Changelog file
- Refractor JS namespaces to follow stable 15
- Add guideline
- Supports for Nextcloud 15 and 16
- Normal user can use the app if allowed by an admin
- Bugs fixed:
    - Random OAuth2 errors: trim all inputs to avoid bad copy/paste from Google Developer Console
    - The "Grant access" button worked randomly: https://github.com/NastuzziSamy/files_external_gdrive/issues/28
    - Cannot create gdrive from command line: https://github.com/NastuzziSamy/files_external_gdrive/issues/40

## v0.2.8
- Bug fixed:
    - Rename create an error https://github.com/NastuzziSamy/files_external_gdrive/issues/8

## v0.2.7
- Bug fixed:
    - In certain php version, some functions returned errors

## v0.2.6
- Bugs fixed:
    - Correct Oauth2 grant bug https://github.com/NastuzziSamy/files_external_gdrive/issues/4
    - Storage was all time "temporarily not available" https://github.com/NastuzziSamy/files_external_gdrive/issues/5
    - Building path did not work correctly with root

## v0.2.5
- Bug fixed:
    - Add vendor directory in the nextcloud app https://github.com/NastuzziSamy/files_external_gdrive/issues/1

## v0.2.4
- Add installation procedure
- Improve the appinfo.xml file

## v0.2.3
- Bugs fixed:
    - It was impossible to install the app from the appstore (Makefile missing..) https://github.com/NastuzziSamy/files_external_gdrive/issues/1

## v0.2.2
- Bugs fixed:
    - Files were all time exported (and sometimes, in an incompatible format) https://github.com/NastuzziSamy/files_external_gdrive/issues/3
    - Files were not downloaded correctly (files streams were badly requested)

## v0.2.1
- Make the app compliant
- Be warn the app is still in beta version until v1.0.0

## v0.2.0
- Add test function
- Force Nextcloud to update all directories contents
- Final Google compatibility
- Google files are automaticaly exported in the good file type
- Files and directories are now deletable
- First publication on Nextcloud appstore

## v0.1.0
- Based on the work of the Google Drive external storage support for ownCloud
- Use of Flysystem fonctionnality
- Switch Google API v2 to v3
- Google files and directories are
    - Printed
    - Readable
    - Downloadable
    - Editable
    - Renamable
