    """

    response_redirect_class = HttpResponsePermanentRedirect

    def process_request(self, request):
        """
        Check for denied User-Agents and rewrite the URL based on
        settings.APPEND_SLASH and settings.PREPEND_WWW
        """

        # Check for denied User-Agents
        user_agent = request.META.get("HTTP_USER_AGENT")
        if user_agent is not None:
            for user_agent_regex in settings.DISALLOWED_USER_AGENTS:
                if user_agent_regex.search(user_agent):
                    raise PermissionDenied("Forbidden user agent")

        # Check for a redirect based on settings.PREPEND_WWW
        host = request.get_host()

        if settings.PREPEND_WWW and host and not host.startswith("www."):
            # Check if we also need to append a slash so we can do it all
            # with a single redirect. (This check may be somewhat expensive,
            # so we only do it if we already know we're sending a redirect,
            # or in process_response if we get a 404.)
            if self.should_redirect_with_slash(request):
                path = self.get_full_path_with_slash(request)
            else:
                path = request.get_full_path()

            return self.response_redirect_class(f"{request.scheme}://www.{host}{path}")

    def should_redirect_with_slash(self, request):
        """
        Return True if settings.APPEND_SLASH is True and appending a slash to
        the request path turns an invalid path into a valid one.
        """
        if settings.APPEND_SLASH and not request.path_info.endswith("/"):
            urlconf = getattr(request, "urlconf", None)
            if not is_valid_path(request.path_info, urlconf):
                match = is_valid_path("%s/" % request.path_info, urlconf)