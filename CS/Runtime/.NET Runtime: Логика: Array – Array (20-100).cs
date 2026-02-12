    public abstract partial class Array : ICloneable, IList, IStructuralComparable, IStructuralEquatable
    {
        // This is the threshold where Introspective sort switches to Insertion sort.
        // Empirically, 16 seems to speed up most cases without slowing down others, at least for integers.
        // Large value types may benefit from a smaller number.
        internal const int IntrosortSizeThreshold = 16;

        // This ctor exists solely to prevent C# from generating a protected .ctor that violates the surface area.
        private protected Array() { }

        public static ReadOnlyCollection<T> AsReadOnly<T>(T[] array)
        {
            if (array == null)
            {
                ThrowHelper.ThrowArgumentNullException(ExceptionArgument.array);
            }

            return array.Length == 0 ?
                ReadOnlyCollection<T>.Empty :
                new ReadOnlyCollection<T>(array);
        }

        public static void Resize<T>([NotNull] ref T[]? array, int newSize)
        {
            if (newSize < 0)
                ThrowHelper.ThrowArgumentOutOfRangeException(ExceptionArgument.newSize, ExceptionResource.ArgumentOutOfRange_NeedNonNegNum);

            T[]? larray = array; // local copy
            if (larray == null)
            {
                array = new T[newSize];
                return;
            }

            if (larray.Length != newSize)
            {
                // Due to array variance, it's possible that the incoming array is
                // actually of type U[], where U:T; or that an int[] <-> uint[] or
                // similar cast has occurred. In any case, since it's always legal
                // to reinterpret U as T in this scenario (but not necessarily the
                // other way around), we can use SpanHelpers.Memmove here.

                T[] newArray = new T[newSize];
                Buffer.Memmove(
                    ref MemoryMarshal.GetArrayDataReference(newArray),
                    ref MemoryMarshal.GetArrayDataReference(larray),
                    (uint)Math.Min(newSize, larray.Length));
                array = newArray;
            }

            Debug.Assert(array != null);
        }

        [RequiresDynamicCode("The code for an array of the specified type might not be available.")]
        public static unsafe Array CreateInstance(Type elementType, int length)
        {
            ArgumentNullException.ThrowIfNull(elementType);
            ArgumentOutOfRangeException.ThrowIfNegative(length);

            RuntimeType? t = elementType.UnderlyingSystemType as RuntimeType;
            if (t == null)
                ThrowHelper.ThrowArgumentException(ExceptionResource.Arg_MustBeType, ExceptionArgument.elementType);

            return InternalCreate(t, 1, &length, null);
        }

        [UnconditionalSuppressMessage("AotAnalysis", "IL3050:RequiresDynamicCode",
            Justification = "MDArrays of Rank != 1 can be created because they don't implement generic interfaces.")]
        public static unsafe Array CreateInstance(Type elementType, int length1, int length2)
        {
            ArgumentNullException.ThrowIfNull(elementType);
            ArgumentOutOfRangeException.ThrowIfNegative(length1);
            ArgumentOutOfRangeException.ThrowIfNegative(length2);

            RuntimeType? t = elementType.UnderlyingSystemType as RuntimeType;
            if (t == null)
                ThrowHelper.ThrowArgumentException(ExceptionResource.Arg_MustBeType, ExceptionArgument.elementType);

            int* pLengths = stackalloc int[] { length1, length2 };
            return InternalCreate(t, 2, pLengths, null);
        }