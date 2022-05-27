user='exampleuser'
pass='examplepass'
db='exampledb'

if [ -z "$1" ]
    then
        echo "You must provide a new domain"
        exit 1
fi

sql="UPDATE wp_options SET option_value = '$1' WHERE option_name = 'siteurl'"
cmd="docker-compose exec db -u$user -p$pass $db -e \"$sql\""
eval $cmd


sql="UPDATE wp_posts SET post_content = REPLACE(post_content, 'localhost:8080', '$1')"
cmd="docker-compose exec db -u$user -p$pass $db -e \"$sql\""
eval $cmd
