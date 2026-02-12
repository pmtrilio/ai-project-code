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
using System.Collections.Generic;
#if HAVE_INOTIFY_COLLECTION_CHANGED
using System.Collections.ObjectModel;
using System.Collections.Specialized;
#endif
using System.ComponentModel;
#if HAVE_DYNAMIC
using System.Dynamic;
using System.Linq.Expressions;
#endif
using System.IO;
using Newtonsoft.Json.Utilities;
using System.Globalization;
using System.Runtime.CompilerServices;
using System.Diagnostics.CodeAnalysis;
#if !HAVE_LINQ
using Newtonsoft.Json.Utilities.LinqBridge;
#else
using System.Linq;
#endif

namespace Newtonsoft.Json.Linq
{
    /// <summary>
    /// Represents a JSON object.
    /// </summary>
    /// <example>
    ///   <code lang="cs" source="..\Src\Newtonsoft.Json.Tests\Documentation\LinqToJsonTests.cs" region="LinqToJsonCreateParse" title="Parsing a JSON Object from Text" />
    /// </example>
    public partial class JObject : JContainer, IDictionary<string, JToken?>, INotifyPropertyChanged
#if HAVE_COMPONENT_MODEL
        , ICustomTypeDescriptor
#endif
#if HAVE_INOTIFY_PROPERTY_CHANGING
        , INotifyPropertyChanging
#endif
    {
        private readonly JPropertyKeyedCollection _properties = new JPropertyKeyedCollection();

        /// <summary>
        /// Gets the container's children tokens.
        /// </summary>
        /// <value>The container's children tokens.</value>
        protected override IList<JToken> ChildrenTokens => _properties;

        /// <summary>
        /// Occurs when a property value changes.
        /// </summary>
        public event PropertyChangedEventHandler? PropertyChanged;

#if HAVE_INOTIFY_PROPERTY_CHANGING
        /// <summary>
        /// Occurs when a property value is changing.
        /// </summary>
        public event PropertyChangingEventHandler? PropertyChanging;
#endif

        /// <summary>
        /// Initializes a new instance of the <see cref="JObject"/> class.
        /// </summary>
        public JObject()
        {
        }

        /// <summary>
        /// Initializes a new instance of the <see cref="JObject"/> class from another <see cref="JObject"/> object.
        /// </summary>
        /// <param name="other">A <see cref="JObject"/> object to copy from.</param>
        public JObject(JObject other)
            : base(other, settings: null)
        {
        }

        internal JObject(JObject other, JsonCloneSettings? settings)
            : base(other, settings)
        {
        }

        /// <summary>
        /// Initializes a new instance of the <see cref="JObject"/> class with the specified content.
        /// </summary>
        /// <param name="content">The contents of the object.</param>
        public JObject(params object[] content)
            : this((object)content)
        {
        }

        /// <summary>
        /// Initializes a new instance of the <see cref="JObject"/> class with the specified content.
        /// </summary>
        /// <param name="content">The contents of the object.</param>
        public JObject(object content)
        {
            Add(content);
        }

        internal override bool DeepEquals(JToken node)
        {
            if (!(node is JObject t))
            {
                return false;
            }

            return _properties.Compare(t._properties);