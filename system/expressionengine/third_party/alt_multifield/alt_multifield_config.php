<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

if (! defined('ALT_MULTIFIELD_VER'))
{
define('ALT_MULTIFIELD_NAME', 'ALT MultiField');
define('ALT_MULTIFIELD_VER', '1.0.9');
define('ALT_MULTIFIELD_DESC', 'A Fieldtype to create small groupings of fields.');
}

$config['name'] = ALT_MULTIFIELD_NAME;
$config['version'] = ALT_MULTIFIELD_VER;
$config['description'] = ALT_MULTIFIELD_DESC;
$config['docs_url'] = '';