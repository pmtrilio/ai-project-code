# Conditions for adding methods.  The boxes indicate what action the
# dataclass decorator takes.  For all of these tables, when I talk
# about init=, repr=, eq=, order=, unsafe_hash=, or frozen=, I'm
# referring to the arguments to the @dataclass decorator.  When
# checking if a dunder method already exists, I mean check for an
# entry in the class's __dict__.  I never check to see if an attribute
# is defined in a base class.

# Key:
# +=========+=========================================+
# + Value   | Meaning                                 |
# +=========+=========================================+
# | <blank> | No action: no method is added.          |
# +---------+-----------------------------------------+
# | add     | Generated method is added.              |
# +---------+-----------------------------------------+
# | raise   | TypeError is raised.                    |
# +---------+-----------------------------------------+
# | None    | Attribute is set to None.               |
# +=========+=========================================+

# __init__
#
#   +--- init= parameter
#   |
#   v     |       |       |
#         |  no   |  yes  |  <--- class has __init__ in __dict__?
# +=======+=======+=======+
# | False |       |       |
# +-------+-------+-------+
# | True  | add   |       |  <- the default
# +=======+=======+=======+

# __repr__
#
#    +--- repr= parameter
#    |
#    v    |       |       |
#         |  no   |  yes  |  <--- class has __repr__ in __dict__?
# +=======+=======+=======+
# | False |       |       |
# +-------+-------+-------+
# | True  | add   |       |  <- the default
# +=======+=======+=======+


# __setattr__
# __delattr__
#
#    +--- frozen= parameter
#    |
#    v    |       |       |
#         |  no   |  yes  |  <--- class has __setattr__ or __delattr__ in __dict__?
# +=======+=======+=======+
# | False |       |       |  <- the default
# +-------+-------+-------+
# | True  | add   | raise |
# +=======+=======+=======+
# Raise because not adding these methods would break the "frozen-ness"
# of the class.

# __eq__
#
#    +--- eq= parameter
#    |
#    v    |       |       |
#         |  no   |  yes  |  <--- class has __eq__ in __dict__?
# +=======+=======+=======+
# | False |       |       |
# +-------+-------+-------+
# | True  | add   |       |  <- the default
# +=======+=======+=======+

# __lt__
# __le__
# __gt__
# __ge__
#
#    +--- order= parameter
#    |
#    v    |       |       |
#         |  no   |  yes  |  <--- class has any comparison method in __dict__?
# +=======+=======+=======+
# | False |       |       |  <- the default
# +-------+-------+-------+
# | True  | add   | raise |
# +=======+=======+=======+
# Raise because to allow this case would interfere with using
# functools.total_ordering.

# __hash__

#    +------------------- unsafe_hash= parameter
#    |       +----------- eq= parameter
#    |       |       +--- frozen= parameter
#    |       |       |
#    v       v       v    |        |        |
#                         |   no   |  yes   |  <--- class has explicitly defined __hash__
# +=======+=======+=======+========+========+
# | False | False | False |        |        | No __eq__, use the base class __hash__
# +-------+-------+-------+--------+--------+