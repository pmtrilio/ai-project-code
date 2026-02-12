    if test -n "$ncolors" && test "$ncolors" -ge 8; then
        BOLD="$(tput bold)"
        YELLOW="$(tput setaf 3)"
        GREEN="$(tput setaf 2)"
        NC="$(tput sgr0)"
    fi
fi

# Function that prints the available commands...
function display_help {
    echo "Laravel Sail"
    echo
    echo "${YELLOW}Usage:${NC}" >&2
    echo "  sail COMMAND [options] [arguments]"
    echo
    echo "Unknown commands are passed to the docker-compose binary."
    echo
    echo "${YELLOW}docker-compose Commands:${NC}"
    echo "  ${GREEN}sail up${NC}        Start the application"
    echo "  ${GREEN}sail up -d${NC}     Start the application in the background"
    echo "  ${GREEN}sail stop${NC}      Stop the application"
    echo "  ${GREEN}sail restart${NC}   Restart the application"
    echo "  ${GREEN}sail ps${NC}        Display the status of all containers"
    echo
    echo "${YELLOW}Artisan Commands:${NC}"
    echo "  ${GREEN}sail artisan ...${NC}          Run an Artisan command"
    echo "  ${GREEN}sail artisan queue:work${NC}"
    echo
    echo "${YELLOW}PHP Commands:${NC}"
    echo "  ${GREEN}sail php ...${NC}   Run a snippet of PHP code"
    echo "  ${GREEN}sail php -v${NC}"
    echo
    echo "${YELLOW}Composer Commands:${NC}"
    echo "  ${GREEN}sail composer ...${NC}                       Run a Composer command"
    echo "  ${GREEN}sail composer require laravel/sanctum${NC}"
    echo
    echo "${YELLOW}Node Commands:${NC}"
    echo "  ${GREEN}sail node ...${NC}         Run a Node command"
    echo "  ${GREEN}sail node --version${NC}"
    echo
    echo "${YELLOW}NPM Commands:${NC}"
    echo "  ${GREEN}sail npm ...${NC}        Run a npm command"
    echo "  ${GREEN}sail npx${NC}            Run a npx command"
    echo "  ${GREEN}sail npm run prod${NC}"
    echo