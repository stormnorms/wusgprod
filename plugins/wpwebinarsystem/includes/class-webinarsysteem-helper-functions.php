<?php

class WebinarSysteemHelperFunctions extends WebinarSysteem {

    public static function isMediaElementJSPlayer($source) {
	return !in_array($source, array('youtubelive', 'vimeo', 'image', 'iframe'));
    }

}
