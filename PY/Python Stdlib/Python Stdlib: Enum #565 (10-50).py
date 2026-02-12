        'FlagBoundary', 'STRICT', 'CONFORM', 'EJECT', 'KEEP',
        'global_flag_repr', 'global_enum_repr', 'global_str', 'global_enum',
        'EnumCheck', 'CONTINUOUS', 'NAMED_FLAGS', 'UNIQUE',
        'pickle_by_global_name', 'pickle_by_enum_name', 'show_flag_values',
        'bin',
        ]


# Dummy value for Enum and Flag as there are explicit checks for them
# before they have been created.
# This is also why there are checks in EnumType like `if Enum is not None`
Enum = Flag = EJECT = ReprEnum = None

class nonmember(object):
    """
    Protects item from becoming an Enum member during class creation.
    """
    def __init__(self, value):
        self.value = value

class member(object):
    """
    Forces item to become an Enum member during class creation.
    """
    def __init__(self, value):
        self.value = value

def _is_descriptor(obj):
    """
    Returns True if obj is a descriptor, False otherwise.
    """
    return (
            hasattr(obj, '__get__') or
            hasattr(obj, '__set__') or
            hasattr(obj, '__delete__')
            )

def _is_dunder(name):
    """
    Returns True if a __dunder__ name, False otherwise.
    """