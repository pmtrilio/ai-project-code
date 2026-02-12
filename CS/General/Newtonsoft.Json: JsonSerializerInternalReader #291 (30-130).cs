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
            ValidationUtils.ArgumentNotNull(target, nameof(target));

            Type objectType = target.GetType();

            JsonContract contract = Serializer._contractResolver.ResolveContract(objectType);

            if (!reader.MoveToContent())
            {
                throw JsonSerializationException.Create(reader, "No JSON content found.");
            }

            if (reader.TokenType == JsonToken.StartArray)
            {
                if (contract.ContractType == JsonContractType.Array)
                {
                    JsonArrayContract arrayContract = (JsonArrayContract)contract;

                    PopulateList((arrayContract.ShouldCreateWrapper) ? arrayContract.CreateWrapper(target) : (IList)target, reader, arrayContract, null, null);
                }
                else
                {
                    throw JsonSerializationException.Create(reader, "Cannot populate JSON array onto type '{0}'.".FormatWith(CultureInfo.InvariantCulture, objectType));
                }
            }
            else if (reader.TokenType == JsonToken.StartObject)
            {
                reader.ReadAndAssert();

                string? id = null;
                if (Serializer.MetadataPropertyHandling != MetadataPropertyHandling.Ignore
                    && reader.TokenType == JsonToken.PropertyName
                    && string.Equals(reader.Value!.ToString(), JsonTypeReflector.IdPropertyName, StringComparison.Ordinal))
                {
                    reader.ReadAndAssert();
                    id = reader.Value?.ToString();
                    reader.ReadAndAssert();
                }

                if (contract.ContractType == JsonContractType.Dictionary)
                {
                    JsonDictionaryContract dictionaryContract = (JsonDictionaryContract)contract;
                    PopulateDictionary((dictionaryContract.ShouldCreateWrapper) ? dictionaryContract.CreateWrapper(target) : (IDictionary)target, reader, dictionaryContract, null, id);
                }
                else if (contract.ContractType == JsonContractType.Object)
                {
                    PopulateObject(target, reader, (JsonObjectContract)contract, null, id);
                }
                else
                {
                    throw JsonSerializationException.Create(reader, "Cannot populate JSON object onto type '{0}'.".FormatWith(CultureInfo.InvariantCulture, objectType));
                }
            }
            else
            {
                throw JsonSerializationException.Create(reader, "Unexpected initial token '{0}' when populating object. Expected JSON object or array.".FormatWith(CultureInfo.InvariantCulture, reader.TokenType));
            }
        }

        private JsonContract? GetContractSafe(Type? type)
        {