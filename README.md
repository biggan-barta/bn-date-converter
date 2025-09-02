# Bangla Date Converter

A WordPress plugin that converts English digits to Bangla digits for dates, times, and meta information.

## Features

- Converts English digits (0-9) to Bangla digits (০-৯)
- Configurable conversion for:
  - Post dates
  - Post times
  - Comment dates and times
  - Archive dates
  - Custom elements using CSS selectors
- Option to exclude specific pages
- Simple and lightweight
- No dependencies
- Clean and modern settings interface

## Installation

1. Download the plugin zip file
2. Go to WordPress Admin > Plugins > Add New > Upload Plugin
3. Upload the zip file and click "Install Now"
4. Activate the plugin

## Configuration

1. Go to WordPress Admin > Settings > Bangla Date
2. Configure the following options:

### Main Settings

- **Enable for Dates**: Convert all post dates to Bangla digits
- **Enable for Times**: Convert all time displays to Bangla digits
- **Enable for Meta**: Convert dates and times in comments to Bangla digits

### Custom Elements

You can convert numbers in specific elements using CSS selectors:

1. Go to "Custom CSS Selectors" field
2. Enter one selector per line, for example:
```
.entry-date
.meta-read
.updated
#post-date
```

### Excluding Pages

To prevent conversion on specific pages:

1. Go to "Excluded Pages (IDs)" field
2. Enter page IDs one per line
3. Save settings

## Examples

Default English numbers: 0123456789  
Converted to Bangla: ০১২৩৪৫৬৭৮৯

## Support

For support or feature requests, please visit [BigganBarta](https://bigganbarta.org)

<<<<<<< HEAD
## Changelog

### Version 1.0.1 (August 31, 2025)
- Fixed settings page saving functionality
- Improved custom CSS selector handling
- Added better error handling and validation
- Removed AJAX dependency for more reliable settings saving
- Enhanced frontend script reliability for custom selectors
- Added initialization check for plugin settings
- Improved documentation and examples

### Version 1.0.0
- Initial release
- Basic digit conversion functionality
- Settings page with toggle options
- Custom CSS selector support
- Page exclusion feature

=======
>>>>>>> 4bf4c815b457a96acf1e3b5b5309d7b0792c2870
## License

GPL v2 or later
