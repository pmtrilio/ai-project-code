
if [ "$mariadb" -gt 1 ]; then
    mariadb -e "CREATE DATABASE IF NOT EXISTS \`$DB\` DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci"
elif [ "$mysql" -gt 1 ]; then
    mysql -e "CREATE DATABASE IF NOT EXISTS \`$DB\` DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci"
else
    # Skip Creating database
    echo "We didn't find MariaDB (\$mariadb) or MySQL (\$mysql), skipping \`$DB\` creation"
fi
