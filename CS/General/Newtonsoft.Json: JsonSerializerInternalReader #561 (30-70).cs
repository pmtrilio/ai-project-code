#if HAVE_DYNAMIC
using System.ComponentModel;
using System.Dynamic;
#endif
using System.Diagnostics;
using System.Globalization;
#if HAVE_BIG_INTEGER
using System.Numerics;
#endif
using System.Reflection;
using System.Runtime.Serialization;
using Newtonsoft.Json.Linq;
using Newtonsoft.Json.Utilities;
using System.Runtime.CompilerServices;
using System.Diagnostics.CodeAnalysis;
#if !HAVE_LINQ
using Newtonsoft.Json.Utilities.LinqBridge;
#else
using System.Linq;
#endif

namespace Newtonsoft.Json.Serialization
{
    internal class JsonSerializerInternalReader : JsonSerializerInternalBase
    {
        internal enum PropertyPresence
        {
            None = 0,
            Null = 1,
            Value = 2
        }

        public JsonSerializerInternalReader(JsonSerializer serializer)
            : base(serializer)
        {
        }

        [RequiresUnreferencedCode(MiscellaneousUtils.TrimWarning)]
        [RequiresDynamicCode(MiscellaneousUtils.AotWarning)]
        public void Populate(JsonReader reader, object target)
        {