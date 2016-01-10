# Server Density report tool
These scripts allow to generate HTML reports from Server Density metrics.

## Setup
1. Install [SD API PHP wrapper](https://github.com/serverdensity/sd-php-wrapper)
2. Place these two script within the same directory
3. Export metrics to JSON like format:
`SDTOKEN="your_sd_api_token" php export_json.php your_server_name`
4. Generate HTML report:
`php report_json.php > your_server_name.html`

