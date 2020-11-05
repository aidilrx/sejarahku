<?php

/**
 * Alert user using javascript
 * @param string $text_to_alert Text to alert user
 * @return string A block of javascript needed to alert user
 */
function alert_user($text_to_alert)
{
    $alert_script = "<script>alert('$text_to_alert')</script>";
    return $alert_script;
}

/**
 * Redirect user to specific path using javascript
 * @param string $redirect_path Redirect path
 * @return string A block of javascript code needed to redirect user to the $redirect_path
 */
function redirect_to($redirect_path)
{
    $redirect_script = "<script>window.location.href = '$redirect_path'</script>";
    return $redirect_script;
}

/**
 * Return to previous page of the code
 * @return string A block of javascript code needed to bring user back to previous page
 */
function return_to_prev()
{
    $prev_script = "<script>window.history.back();</script>";
    return $prev_script;
}