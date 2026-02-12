            $name = preg_replace_callback('/[a-zA-Z_\x7f-\xff][\\\\a-zA-Z0-9_\x7f-\xff]*+@anonymous\x00.*?\.php(?:0x?|:[0-9]++\$)?[0-9a-fA-F]++/', static fn ($m) => class_exists($m[0], false) ? (get_parent_class($m[0]) ?: key(class_implements($m[0])) ?: 'class').'@anonymous' : $m[0], $name);
        }

        throw new BadRequestException(\sprintf('Callable "%s()" is not allowed as a controller. Did you miss tagging it with "#[AsController]" or registering its type with "%s::allowControllers()"?', $name, self::class));
    }
}
