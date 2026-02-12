                return new HeaderSegment(HeaderNames.Host);
            case "HTTP_REFERER":
                return new HeaderSegment(HeaderNames.Referer);
            case "HTTP_USER_AGENT":
                return new HeaderSegment(HeaderNames.UserAgent);
            case "HTTP_CONNECTION":
                return new HeaderSegment(HeaderNames.Connection);
            case "HTTP_FORWARDED":
                return new HeaderSegment("Forwarded");
            case "AUTH_TYPE":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "CONN_REMOTE_ADDR":
                return new RemoteAddressSegment();
            case "CONTEXT_PREFIX":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "CONTEXT_DOCUMENT_ROOT":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "IPV6":
                return new IsIPV6Segment();
            case "PATH_INFO":
                throw new NotImplementedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "QUERY_STRING":
                return new QueryStringSegment();
            case "REMOTE_ADDR":
                return new RemoteAddressSegment();
            case "REMOTE_HOST":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "REMOTE_IDENT":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "REMOTE_PORT":
                return new RemotePortSegment();
            case "REMOTE_USER":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "REQUEST_METHOD":
                return new RequestMethodSegment();
            case "SCRIPT_FILENAME":
                return new RequestFileNameSegment();
            case "DOCUMENT_ROOT":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "SCRIPT_GROUP":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "SCRIPT_USER":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "SERVER_ADDR":
                return new LocalAddressSegment();
            case "SERVER_ADMIN":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "SERVER_NAME":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "SERVER_PORT":
                return new LocalPortSegment();
            case "SERVER_PROTOCOL":
                return new ServerProtocolSegment();
            case "SERVER_SOFTWARE":
                throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
            case "TIME_YEAR":
                return new DateTimeSegment(serverVariable);
            case "TIME_MON":
                return new DateTimeSegment(serverVariable);
            case "TIME_DAY":
                return new DateTimeSegment(serverVariable);