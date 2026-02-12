whitespace_re = re.compile("\s+")

def _alias(attr):
    """Alias one attribute name to another for backward compatibility"""
    @property
    def alias(self):
        return getattr(self, attr)

    @alias.setter
    def alias(self):
        return setattr(self, attr)
    return alias


class NamespacedAttribute(unicode):

    def __new__(cls, prefix, name, namespace=None):
        if name is None:
            obj = unicode.__new__(cls, prefix)
        elif prefix is None:
            # Not really namespaced.
            obj = unicode.__new__(cls, name)
        else:
            obj = unicode.__new__(cls, prefix + ":" + name)
        obj.prefix = prefix
        obj.name = name
        obj.namespace = namespace
        return obj

class AttributeValueWithCharsetSubstitution(unicode):
    """A stand-in object for a character encoding specified in HTML."""

class CharsetMetaAttributeValue(AttributeValueWithCharsetSubstitution):
    """A generic stand-in for the value of a meta tag's 'charset' attribute.

    When Beautiful Soup parses the markup '<meta charset="utf8">', the
    value of the 'charset' attribute will be one of these objects.
    """

    def __new__(cls, original_value):
        obj = unicode.__new__(cls, original_value)
        obj.original_value = original_value
        return obj

    def encode(self, encoding):
        return encoding


class ContentMetaAttributeValue(AttributeValueWithCharsetSubstitution):
    """A generic stand-in for the value of a meta tag's 'content' attribute.

    When Beautiful Soup parses the markup:
     <meta http-equiv="content-type" content="text/html; charset=utf8">

    The value of the 'content' attribute will be one of these objects.
    """

    CHARSET_RE = re.compile("((^|;)\s*charset=)([^;]*)", re.M)

    def __new__(cls, original_value):
        match = cls.CHARSET_RE.search(original_value)
        if match is None:
            # No substitution necessary.
            return unicode.__new__(unicode, original_value)

        obj = unicode.__new__(cls, original_value)
        obj.original_value = original_value
        return obj

    def encode(self, encoding):
        def rewrite(match):
            return match.group(1) + encoding
        return self.CHARSET_RE.sub(rewrite, self.original_value)

class HTMLAwareEntitySubstitution(EntitySubstitution):

    """Entity substitution rules that are aware of some HTML quirks.

    Specifically, the contents of <script> and <style> tags should not
    undergo entity substitution.

    Incoming NavigableString objects are checked to see if they're the
    direct children of a <script> or <style> tag.
    """

    cdata_containing_tags = set(["script", "style"])

    preformatted_tags = set(["pre"])

    @classmethod
    def _substitute_if_appropriate(cls, ns, f):
        if (isinstance(ns, NavigableString)
            and ns.parent is not None
            and ns.parent.name in cls.cdata_containing_tags):
            # Do nothing.
            return ns
        # Substitute.
        return f(ns)

    @classmethod
    def substitute_html(cls, ns):