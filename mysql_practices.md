## ttf mysql query design and syntax practices ##

_always_ follow these if you are working with ttf's mysql queries. if you disagree, post a comment or email the project owner.

  * do not specify `LIMIT 1` if you are already specifying a unique key in a `WHERE` clause.
  * do not use `mysql_free_result()`. they are freed when the script terminates anyway.