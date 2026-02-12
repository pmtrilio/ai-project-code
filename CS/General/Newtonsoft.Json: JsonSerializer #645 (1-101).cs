#region License
// Copyright (c) 2007 James Newton-King
//
// Permission is hereby granted, free of charge, to any person
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
using System.Collections;
using System.Collections.Generic;
using System.Diagnostics;
using System.Globalization;
using System.IO;
using System.Runtime.Serialization.Formatters;
using Newtonsoft.Json.Converters;
using Newtonsoft.Json.Serialization;
using Newtonsoft.Json.Utilities;
using System.Runtime.Serialization;
using ErrorEventArgs = Newtonsoft.Json.Serialization.ErrorEventArgs;
using System.Runtime.CompilerServices;
using System.Diagnostics.CodeAnalysis;

namespace Newtonsoft.Json
{
    /// <summary>
    /// Serializes and deserializes objects into and from the JSON format.
    /// The <see cref="JsonSerializer"/> enables you to control how objects are encoded into JSON.
    /// </summary>
    [RequiresUnreferencedCode(MiscellaneousUtils.TrimWarning)]
    [RequiresDynamicCode(MiscellaneousUtils.AotWarning)]
    public class JsonSerializer
    {
        internal TypeNameHandling _typeNameHandling;
        internal TypeNameAssemblyFormatHandling _typeNameAssemblyFormatHandling;
        internal PreserveReferencesHandling _preserveReferencesHandling;
        internal ReferenceLoopHandling _referenceLoopHandling;
        internal MissingMemberHandling _missingMemberHandling;
        internal ObjectCreationHandling _objectCreationHandling;
        internal NullValueHandling _nullValueHandling;
        internal DefaultValueHandling _defaultValueHandling;
        internal ConstructorHandling _constructorHandling;
        internal MetadataPropertyHandling _metadataPropertyHandling;
        internal JsonConverterCollection? _converters;
        internal IContractResolver _contractResolver;
        internal ITraceWriter? _traceWriter;
        internal IEqualityComparer? _equalityComparer;
        internal ISerializationBinder _serializationBinder;
        internal StreamingContext _context;
        private IReferenceResolver? _referenceResolver;

        private Formatting? _formatting;
        private DateFormatHandling? _dateFormatHandling;
        private DateTimeZoneHandling? _dateTimeZoneHandling;
        private DateParseHandling? _dateParseHandling;
        private FloatFormatHandling? _floatFormatHandling;
        private FloatParseHandling? _floatParseHandling;
        private StringEscapeHandling? _stringEscapeHandling;
        private CultureInfo _culture;
        private int? _maxDepth;
        private bool _maxDepthSet;
        private bool? _checkAdditionalContent;
        private string? _dateFormatString;
        private bool _dateFormatStringSet;

        /// <summary>
        /// Occurs when the <see cref="JsonSerializer"/> errors during serialization and deserialization.
        /// </summary>
        public virtual event EventHandler<ErrorEventArgs>? Error;

        /// <summary>
        /// Gets or sets the <see cref="IReferenceResolver"/> used by the serializer when resolving references.
        /// </summary>
        public virtual IReferenceResolver? ReferenceResolver
        {
            get => GetReferenceResolver();
            set
            {
                if (value == null)
                {
                    throw new ArgumentNullException(nameof(value), "Reference resolver cannot be null.");
                }

                _referenceResolver = value;