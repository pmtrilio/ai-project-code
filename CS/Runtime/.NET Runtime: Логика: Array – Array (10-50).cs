using System.Reflection;
using System.Runtime;
using System.Runtime.CompilerServices;
using System.Runtime.InteropServices;
using Internal.Runtime;

namespace System
{
    [Serializable]
    [TypeForwardedFrom("mscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089")]
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