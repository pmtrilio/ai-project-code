// obtaining a copy of this software and associated documentation
// files (the "Software"), to deal in the Software without
// restriction, including without limitation the rights to use,
// copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the
// Software is furnished to do so, subject to the following
// conditions:
//
// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
// OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
// HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
// OTHER DEALINGS IN THE SOFTWARE.
#endregion

using System;
using System.IO;
using System.Globalization;
#if HAVE_BIG_INTEGER
using System.Numerics;
#endif
using Newtonsoft.Json.Linq;
using Newtonsoft.Json.Utilities;
using System.Xml;
using Newtonsoft.Json.Converters;
using Newtonsoft.Json.Serialization;
using System.Text;
using System.Diagnostics;
using System.Runtime.CompilerServices;
using System.Diagnostics.CodeAnalysis;
#if HAVE_XLINQ
using System.Xml.Linq;
#endif

namespace Newtonsoft.Json
{
    /// <summary>
    /// Provides methods for converting between .NET types and JSON types.
    /// </summary>
    /// <example>
    ///   <code lang="cs" source="..\Src\Newtonsoft.Json.Tests\Documentation\SerializationTests.cs" region="SerializeObject" title="Serializing and Deserializing JSON with JsonConvert" />
    /// </example>
    public static class JsonConvert
    {
        /// <summary>
        /// Gets or sets a function that creates default <see cref="JsonSerializerSettings"/>.
        /// Default settings are automatically used by serialization methods on <see cref="JsonConvert"/>,
        /// and <see cref="JToken.ToObject{T}()"/> and <see cref="JToken.FromObject(object)"/> on <see cref="JToken"/>.
        /// To serialize without using any default settings create a <see cref="JsonSerializer"/> with
        /// <see cref="JsonSerializer.Create()"/>.
        /// </summary>
        public static Func<JsonSerializerSettings>? DefaultSettings { get; set; }

        /// <summary>
        /// Represents JavaScript's boolean value <c>true</c> as a string. This field is read-only.
        /// </summary>
        public static readonly string True = "true";

        /// <summary>
        /// Represents JavaScript's boolean value <c>false</c> as a string. This field is read-only.
        /// </summary>
        public static readonly string False = "false";

        /// <summary>
        /// Represents JavaScript's <c>null</c> as a string. This field is read-only.
        /// </summary>
        public static readonly string Null = "null";

        /// <summary>
        /// Represents JavaScript's <c>undefined</c> as a string. This field is read-only.
        /// </summary>
        public static readonly string Undefined = "undefined";

        /// <summary>
        /// Represents JavaScript's positive infinity as a string. This field is read-only.