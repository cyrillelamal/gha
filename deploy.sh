HOST="$1"

if [ "$HOST" ]; then
  echo "Deploy using host $HOST"

  docker-compose down -v
  docker-compose up -d
  docker-compose exec wordpress bash /utils/wait-for-it.sh --timeout=30 db:3306

  DUMP="tmp-dump-$(date +%s).sql"

  SQL=$(docker-compose exec wordpress php /utils/deploy.php "$HOST" | tr -d '\r')
  echo "$SQL" >"./dumps/$DUMP"
  docker-compose exec -e DUMP="$DUMP" db sh -c 'mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /dumps/"$DUMP"'
  rm "./dumps/$DUMP"

  echo "$HOST is ready"
else
  echo 'You must provide a target host, e.g. "sh deploy.sh http://gha.net"'
fi
