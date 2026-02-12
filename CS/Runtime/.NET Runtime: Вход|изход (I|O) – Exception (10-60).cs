using System.Text;

namespace System
{
    [Serializable]
    [TypeForwardedFrom("mscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089")]
    public partial class Exception : ISerializable
    {
        private protected const string InnerExceptionPrefix = " ---> ";

        public Exception()
        {
            _HResult = HResults.COR_E_EXCEPTION;
        }

        public Exception(string? message)
            : this()
        {
            _message = message;
        }

        // Creates a new Exception.  All derived classes should
        // provide this constructor.
        // Note: the stack trace is not started until the exception
        // is thrown
        //
        public Exception(string? message, Exception? innerException)
            : this()
        {
            _message = message;
            _innerException = innerException;
        }

        [Obsolete(Obsoletions.LegacyFormatterImplMessage, DiagnosticId = Obsoletions.LegacyFormatterImplDiagId, UrlFormat = Obsoletions.SharedUrlFormat)]
        [EditorBrowsable(EditorBrowsableState.Never)]
        protected Exception(SerializationInfo info, StreamingContext context)
        {
            ArgumentNullException.ThrowIfNull(info);

            _message = info.GetString("Message"); // Do not rename (binary serialization)
            _data = (IDictionary?)(info.GetValueNoThrow("Data", typeof(IDictionary))); // Do not rename (binary serialization)
            _innerException = (Exception?)(info.GetValue("InnerException", typeof(Exception))); // Do not rename (binary serialization)
            _helpURL = info.GetString("HelpURL"); // Do not rename (binary serialization)
            _stackTraceString = info.GetString("StackTraceString"); // Do not rename (binary serialization)
            _remoteStackTraceString = info.GetString("RemoteStackTraceString"); // Do not rename (binary serialization)
            _HResult = info.GetInt32("HResult"); // Do not rename (binary serialization)
            _source = info.GetString("Source"); // Do not rename (binary serialization)

            RestoreRemoteStackTrace(info, context);
        }
