<?php

// Migrate the database to another domain.
// Useage: php utils/migrate.php -o="localhost:8080" -n="www.domain.new"

main(getopt('n:o::'));

function main(array $options): never {
    $old = $options['o'] ?? DatabaseContainer::OLD_DOMAIN;
    $new = @$options['n'] or throw new RuntimeException('You must provide a new domain using the "-o" option.');

    $db = new DatabaseContainer();
    $db->updateWpOptionsTable($new);
    $db->updateWpPostsTable($old, $new);

    exit(0);
}

class DatabaseContainer
{
    public const OLD_DOMAIN = 'localhost:8080';

    /**
     * @param string $name Docker Compose container name
     * @param string $user Database user
     * @param string $password Database password
     * @param string $db Docker Database name
     */
    public function __construct(
        public readonly string $name = 'db',
        public readonly string $user = 'exampleuser',
        public readonly string $password = 'examplepass',
        public readonly string $db = 'exampledb',
    ) {
    }

    public function updateWpOptionsTable(string $newDomain): string
    {
        $sql = <<<SQL
UPDATE wp_options
SET option_value = '$newDomain'
WHERE option_name = 'siteurl'
SQL;

        return $this->executeQuery($sql);
    }

    public function updateWpPostsTable(string $oldDomain, string $newDomain): string
    {
        $sql = <<<SQL
UPDATE wp_posts 
SET post_content = REPLACE(post_content, '$oldDomain', '$newDomain')
SQL;

        return $this->executeQuery($sql);
    }

    public function executeQuery(string $sql): string
    {
        $cmd = "docker-compose exec {$this->name} -u{$this->user} -p{$this->password} {$this->db} -e \"$sql\"";

        return shell_exec($cmd);
    }
}
