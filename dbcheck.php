<?php
$conn = pg_connect("host=ep-silent-sun-afd0euia-pooler.c-2.us-west-2.aws.neon.tech port=5432 dbname=neondb user=neondb_owner password=TU_PASS sslmode=require");
if ($conn) {
  $r = pg_fetch_row(pg_query($conn, "select current_user, current_database()"));
  echo "OK --> user={$r[0]} db={$r[1]}";
} else {
  echo "ERROR --> " . pg_last_error();
}
