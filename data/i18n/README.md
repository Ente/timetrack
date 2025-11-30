# Custom Language files

This feature can be used, by placing your language files into this directory.

The structure of the custom language files should follow the same structure as the default `loadLanguage()` method:

`/data/i18n/custom/{page_identifier}/snippets_{language_code}.json`

The workflow will be the following:

- Locale requested has default language file?
  - Yes: load default language file
  - No: Locale requested has custom language file?
    - Yes: load custom language file
    - No: load default language file (English)