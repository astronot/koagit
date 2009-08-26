<?php
///////////////////////////////////////////////////////////////////////////////
// VDaemon PHP Library version 2.3.0
// Copyright (C) 2002-2004 Alexander Orlov and Andrei Stepanuga
//
// VDaemon configuration file
//
///////////////////////////////////////////////////////////////////////////////

// defines VDaemon's behavior in case of POST request.
if (!defined('VDAEMON_POST_SECURITY'))
{
    define('VDAEMON_POST_SECURITY', true);
}

// path to VDaemon installation folder from your web site root
if (!defined('PATH_TO_VDAEMON_JS'))
{
    define('PATH_TO_VDAEMON_JS', '/clm/includes/vdaemon/vdaemon.js');
}

// VDaemon array delimiter
if (!defined('VDAEMON_DELIMITER'))
{
    define('VDAEMON_DELIMITER', '~');
}

?>