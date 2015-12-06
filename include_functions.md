#summary design and list of common functions
#labels Phase-Design,Phase-Implementation

## design guide for common functions ##

it is nice to put common functions in a place that can be called by all scripts. however, we don't want to put _every_ function there, because many functions will be script specific.

  * if a function is duplicated in two or more scripts, it should be migrated to include\_functions.php.
  * if a function is in include\_functions.php but is only used in one script, it should be migrated to that specific script.

## list of functions in include\_functions.php ##

  * message($label, $title, $body)
  * formatdate($timestamp, $format = "M j, Y, g\:i a")
  * outputbody($input)
  * output($input)
  * showerror()
  * clean($input)
  * generate\_string($length)
  * kill\_guests()
  * kill\_users()
  * kill\_nonadmin()
  * reformat\_caches()