[![Build Status](https://travis-ci.org/horike37/wp-elasticsearch.svg?branch=master)](https://travis-ci.org/horike37/wp-elasticsearch) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/horike37/wp-elasticsearch/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/horike37/wp-elasticsearch/?branch=master)
# wp-elasticsearch
Welcome to the official repository for WP Elasticsearch WordPress plugin.

## Description
This plugin is that WordPress of standard search using `?s=xxx` to replace WordPress DB with Elasticsearch.

## Installation
- Upload wp-elasticsearch to the /wp-content/plugins/ directory.
- Activate the plugin through the 'Plugins' menu in WordPress.
- Please set up on Settings > WP Elasticsearch.

## Setting
- 1. Require setting to `Elasticsearch Endpoint`,`Port`,`index`,`type` and push `Save Changes`. It is default search target `post title`, `post content`.
- 2. Please push `Post Data sync to Elasticsearch`. So Posts data are sent to Elasticsearch.
<img src="https://raw.githubusercontent.com/horike37/wp-elasticsearch/master/screenshot-1.png" title="screenshot"/>

## Contributors
[s-fujimoto](https://github.com/s-fujimoto), [horike37](https://github.com/horike37/)

## License
You can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
